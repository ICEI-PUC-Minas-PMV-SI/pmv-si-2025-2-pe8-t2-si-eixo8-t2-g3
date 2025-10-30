<?php
// abrir_os_detalhes.php
declare(strict_types=1);
session_start();
require __DIR__ . '/../include/db.php';

/* ===== Helpers ===== */
function csrf_token2(): string {
  if (empty($_SESSION['csrf2'])) $_SESSION['csrf2'] = bin2hex(random_bytes(16));
  return $_SESSION['csrf2'];
}
function csrf_check2($t): bool {
  return isset($_SESSION['csrf2']) && hash_equals($_SESSION['csrf2'], (string)$t);
}
function only_digits(string $s): string { return preg_replace('/\D+/', '', $s); }
function first_non_empty(array $row, array $keys, $default = '') {
  foreach ($keys as $k) if (!empty($row[$k])) return $row[$k];
  return $default;
}

/* ===== Precisa vir da etapa 1 ===== */
if (empty($_SESSION['abertura_os_cnpj'])) { header('Location: abrir_os.php'); exit; }
$cnpj = only_digits($_SESSION['abertura_os_cnpj']);

$pdo = db();

/* ===== Busca cliente (tabela clients/clientes) ===== */
$tbl = null;
$stmT = $pdo->prepare("
  SELECT TABLE_NAME
  FROM information_schema.tables
  WHERE table_schema = :db AND TABLE_NAME IN ('clients','clientes')
  ORDER BY TABLE_NAME='clients' DESC
  LIMIT 1
");
$stmT->execute([':db' => DB_NAME]);
$rt = $stmT->fetch();
if ($rt && isset($rt['TABLE_NAME'])) {
  $tbl = $rt['TABLE_NAME'];
} else {
  http_response_code(500);
  exit('Tabela de clientes não encontrada.');
}

$st = $pdo->prepare("SELECT * FROM {$tbl}
  WHERE REPLACE(REPLACE(REPLACE(TRIM(cnpj),'.',''),'/',''),'-','') = :cnpj
  LIMIT 1");
$st->execute([':cnpj' => $cnpj]);
$cliente = $st->fetch();
if (!$cliente) {
  $cliente = $_SESSION['abertura_os_cliente'] ?? null;
  if (!$cliente) { header('Location: abrir_os.php'); exit; }
}

/* ===== Dados do cliente ===== */
$cliente_id   = (int) first_non_empty($cliente, ['id','cliente_id'], 0);
$cliente_nome = first_non_empty($cliente, ['name','nome','razao_social','fantasia'], 'Cliente');
$cliente_fone = first_non_empty($cliente, ['phone','telefone'], '');
$cliente_mail = first_non_empty($cliente, ['email'], '');

/* ===== Máquinas (explode por ;) ===== */
$machines_raw  = (string) ($cliente['machines'] ?? '');
$machines_list = array_values(array_filter(array_map('trim', explode(';', $machines_raw)), fn($s)=>$s !== ''));

/* ===== POST: salvar OS ===== */
$errors = [];
$ok_id  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check2($_POST['csrf'] ?? '')) {
    $errors[] = 'Sessão expirada. Atualize a página e tente novamente.';
  } else {
    $sel = $_POST['machines'] ?? [];
    if (!is_array($sel)) $sel = [];
    $sel = array_values(array_intersect($sel, $machines_list)); // sanity check

    $desc = trim((string)($_POST['problem_description'] ?? ''));

    if (count($sel) === 0) $errors[] = 'Selecione ao menos uma máquina.';
    if (mb_strlen($desc) < 5) $errors[] = 'Descreva o problema (mínimo de 5 caracteres).';

    if (!$errors) {
      $machines_sel   = implode(', ', $sel);
      $problem_desc   = "Máquina(s): {$machines_sel}\nDescrição: {$desc}";
      $status         = 'opened';

      // INSERT com colunas *_snapshot e status 'opened'
      $sql = "
        INSERT INTO service_orders
          (customer_id,
           customer_name_snapshot, customer_phone_snapshot, customer_email_snapshot,
           opened_at, status, problem_description,
           created_at, updated_at)
        VALUES
          (:cid, :cname, :cphone, :cemail, NOW(), :status, :pdesc, NOW(), NOW())
      ";
      $ins = $pdo->prepare($sql);
      $ins->execute([
        ':cid'   => $cliente_id,
        ':cname' => $cliente_nome,
        ':cphone'=> $cliente_fone,
        ':cemail'=> $cliente_mail,
        ':status'=> $status,
        ':pdesc' => $problem_desc,
      ]);
      $ok_id = (int)$pdo->lastInsertId();
    }
  }
}

$csrf = htmlspecialchars(csrf_token2(), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="theme-color" content="#061948">
		<meta name="msapplication-navbutton-color" content="#061948">
		<meta name="apple-mobile-web-app-status-bar-style" content="#061948">
		<title>Delta Coding — Detalhes da OS</title>
		<link rel="icon" type="image/png" sizes="56x56" href="../images/fav-icon/icon.png">
		<link rel="stylesheet" type="text/css" href="../css/style.css">
		<link rel="stylesheet" type="text/css" href="../css/responsive.css">
    <style>
      .wrap{max-width:900px;margin:28px auto;padding:0 16px}
      .card{background:#fff;border:1px solid #e6e6e6;border-radius:12px;padding:20px}
      h1{font-size:26px;margin:0 0 6px}
      .muted{color:#6b7280}
      label{display:block;font-weight:600;margin:12px 0 6px}
      textarea{width:100%;min-height:140px;padding:12px;border:1px solid #cfd6e4;border-radius:10px}
      .machines{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px;margin:8px 0}
      .badge{display:inline-block;background:#eef2ff;border:1px solid #c7d2fe;padding:6px 10px;border-radius:10px}
      .actions{margin-top:14px;display:flex;gap:10px;flex-wrap:wrap}
      .btn{background:#0d6efd;color:#fff;border:0;border-radius:10px;padding:10px 16px;cursor:pointer;text-decoration:none}
      .btn-secondary{background:#6b7280}
      .msg-erro{background:#fff2f0;color:#a8071a;border:1px solid #ffccc7;border-radius:10px;padding:10px;margin:12px 0}
      .msg-ok{background:#f6ffed;color:#237804;border:1px solid #b7eb8f;border-radius:10px;padding:10px;margin:12px 0}
    </style>
	</head>

	<body>
		<div class="main-page-wrapper">

			<!-- ===== Topo/Menu originais ===== -->
			<header class="header-one">
				<div class="top-header">
					<div class="container clearfix">
						<div class="logo float-left"><a href="../index.html"><img src="../images/logo/logo.png" alt=""></a></div>
						<div class="address-wrapper float-right">
							<ul>
								<li class="address">
									<i class="icon flaticon-placeholder"></i>
									<h6>Endereço:</h6>
									<p>Rua Fued Mansur Kfuri 170 - Dom Silvério</p>
								</li>
								<li class="address">
									<i class="icon flaticon-multimedia"></i>
									<h6>E-mail:</h6>
									<p>contato@deltacoding.com.br</p>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<div class="theme-menu-wrapper">
					<div class="container">
						<div class="bg-wrapper clearfix">
					   		<div class="menu-wrapper float-left">
					   			<nav id="mega-menu-holder" class="clearfix">
								   <ul class="clearfix">
									    <li class="active"><a href="#">Home</a></li>
									    <li><a href="#">Datadoras</a>
									    	<ul class="dropdown">
									    		<li><a href="about.html">Importadas</a></li>
									    		<li><a href="team.html">Nacionais</a></li>
									       </ul>
									    </li>
									    <li><a href="#">Serviços</a>
									    	<ul class="dropdown">
									        	<li><a href="service.html">Peças</a></li>
									        	<li><a href="service-v2.html">Insumos</a></li>
									       </ul>
									    </li>
									    <li><a href="#">Equipamentos Industriais</a></li>
									    <li><a href="contact.html">Contato</a></li>
								   </ul>
								</nav>
					   		</div>
					   		<div class="right-widget float-right">
					   			<ul>
					   				<li class="social-icon">
					   					<ul>
											<li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
											<li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
											<li><a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
											<li><a href="#"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
										</ul>
					   				</li>
					   				<li class="cart-icon">
					   					<a href="#"><i class="flaticon-tool"></i> <span>2</span></a>
					   				</li>
					   				<li class="search-option">
					   					<div class="dropdown">
					   						<button type="button" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-search" aria-hidden="true"></i></button>
											<form action="#" class="dropdown-menu">
												<input type="text" Placeholder="O que você está buscando">
												<button><i class="fa fa-search"></i></button>
											</form>
					   					</div>
					   				</li>
					   			</ul>
					   		</div>
						</div>
					</div>
				</div>
			</header>

      <!-- ===== Miolo: Detalhes da OS ===== -->
      <section class="section-spacing">
        <div class="container">
          <div class="wrap">
            <h1>Detalhes da Ordem de Serviço</h1>
            <p class="muted">Cliente: <strong><?=htmlspecialchars($cliente_nome)?></strong> • CNPJ: <strong><?=htmlspecialchars($cnpj)?></strong></p>

            <?php if ($errors): ?>
              <div class="msg-erro">
                <?php foreach ($errors as $e) echo '<div>'.htmlspecialchars($e,ENT_QUOTES,'UTF-8').'</div>'; ?>
              </div>
            <?php endif; ?>

            <?php if ($ok_id): ?>
              <div class="msg-ok">
                OS aberta com sucesso! Número da OS: <strong>#<?= (int)$ok_id ?></strong>.
              </div>
              <p><a class="btn" href="abrir_os.php">Abrir outra OS</a></p>
            <?php else: ?>
              <div class="card">
                <form method="post" action="">
                  <input type="hidden" name="csrf" value="<?=$csrf?>">

                  <label>Selecione a(s) máquina(s) *</label>
                  <?php if ($machines_list): ?>
                    <div class="machines">
                      <?php foreach ($machines_list as $m): ?>
                        <label class="badge">
                          <input type="checkbox" name="machines[]" value="<?=htmlspecialchars($m,ENT_QUOTES,'UTF-8')?>"> <?=htmlspecialchars($m)?>
                        </label>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <p class="muted">Nenhuma máquina cadastrada para este cliente.</p>
                  <?php endif; ?>

                  <label>Descrição do problema *</label>
                  <textarea name="problem_description" placeholder="Explique o que está acontecendo, quando começou, mensagens de erro, etc." required><?= isset($_POST['problem_description']) ? htmlspecialchars($_POST['problem_description'],ENT_QUOTES,'UTF-8') : '' ?></textarea>

                  <div class="actions">
                    <button class="btn" type="submit">Enviar OS</button>
                    <a class="btn btn-secondary" href="abrir_os.php">Voltar</a>
                  </div>
                  <p class="muted" style="margin-top:8px">Gravaremos a lista das máquinas selecionadas junto com sua descrição.</p>
                </form>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>

			<!-- ===== Rodapé original ===== -->
      <footer class="theme-footer-one">
        <div class="top-footer">
          <div class="container">
            <div class="row">
              <div class="col-xl-3 col-lg-4 col-sm-6 about-widget">
                <h6 class="title">Sobre a Delta Coding</h6>
                <p>Manutenção, insumos e equipamentos para marcação e datação industrial (TIJ, CIJ, Laser e TTO) com foco em qualidade e custo justo.</p>
                <div class="queries">
                  <i class="flaticon-phone-call"></i> Atendimento:
                  <a href="tel:+553134933910">(31) 3493-3910</a>
                </div>
              </div>

              <div class="col-xl-4 col-lg-3 col-sm-6 footer-recent-post">
                <h6 class="title">Últimos Posts</h6>
                <ul>
                  <li class="clearfix">
                    <img src="../images/blog/1.jpg" alt="" class="float-left">
                    <div class="post float-left">
                      <a href="#">Boas práticas de impressão TIJ na linha</a>
                      <div class="date"><i class="fa fa-calendar-o" aria-hidden="true"></i> Set 15, 2025</div>
                    </div>
                  </li>
                  <li class="clearfix">
                    <img src="../images/blog/2.jpg" alt="" class="float-left">
                    <div class="post float-left">
                      <a href="#">Quando escolher CIJ, Laser ou TTO?</a>
                      <div class="date"><i class="fa fa-calendar-o" aria-hidden="true"></i> Ago 28, 2025</div>
                    </div>
                  </li>
                </ul>
              </div>

              <div class="col-xl-2 col-lg-3 col-sm-6 footer-list">
                <h6 class="title">Soluções</h6>
                <ul>
                  <li><a href="https://www.deltacoding.com.br/insumos-para-datadoras">Insumos</a></li>
                  <li><a href="https://www.deltacoding.com.br/pecasdatadoras?sort_by=lowest_price">Peças</a></li>
                  <li><a href="https://www.deltacoding.com.br/datadoras-importacao?sort_by=best_sellers">Datadoras</a></li>
                  <li><a href="https://www.deltacoding.com.br/equipamentos-industrias?sort_by=best_sellers">Equipamentos</a></li>
                  <li><a href="https://wa.me/5531988335786" target="_blank" rel="noopener">Suporte</a></li>
                </ul>
              </div>

              <div class="col-xl-3 col-lg-2 col-sm-6 footer-newsletter">
                <h6 class="title">Newsletter</h6>
                <form action="#">
                  <input type="text" placeholder="Nome *">
                  <input type="email" placeholder="E-mail *">
                  <button class="theme-button-one">ASSINAR</button>
                </form>
                <small>Receba novidades.</small>
              </div>
            </div>
          </div>
        </div>

        <div class="bottom-footer">
          <div class="container">
            <div class="row">
              <div class="col-md-6 col-12">
                <p>© 2025 Delta Coding e Equipamentos LTDA • CNPJ: 15.808.019/0001-36</p>
              </div>
              <div class="col-md-6 col-12">
                <ul>
                  <li><a href="https://www.deltacoding.com.br/atendimento/rastreio">Rastreio</a></li>
                  <li><a href="https://www.deltacoding.com.br/atendimento/politica-de-cookies">Política de Cookies</a></li>
                  <li><a href="https://wa.me/5531988335786" target="_blank" rel="noopener">Fale Conosco</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </footer>

			<!-- Scroll Top Button -->
			<button class="scroll-top tran3s"><i class="fa fa-angle-up" aria-hidden="true"></i></button>

      <!-- JS originais -->
      <script src="../vendor/jquery.2.2.3.min.js"></script>
      <script src="../vendor/popper.js/popper.min.js"></script>
      <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
      <script src='../vendor/Camera-master/scripts/jquery.mobile.customized.min.js'></script>
      <script src='../vendor/Camera-master/scripts/jquery.easing.1.3.js'></script>
      <script src='../vendor/Camera-master/scripts/camera.min.js'></script>
      <script src="../vendor/menu/src/js/jquery.slimmenu.js"></script>
      <script src="../vendor/WOW-master/dist/wow.min.js"></script>
      <script src="../vendor/owl-carousel/owl.carousel.min.js"></script>
      <script src="../vendor/jquery.appear.js"></script>
      <script src="../vendor/jquery.countTo.js"></script>
      <script src="../vendor/fancybox/dist/jquery.fancybox.min.js"></script>
      <script src="../js/theme.js"></script>
		</div><!-- /.main-page-wrapper -->
	</body>
</html>
