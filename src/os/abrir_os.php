<?php
// abrir_os.php
declare(strict_types=1);
session_start();
require __DIR__ . '/../include/db.php'; // ajuste se o caminho do include for diferente

/* ========= Helpers ========= */
function csrf_token(): string {
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
  return $_SESSION['csrf'];
}
function csrf_check($t): bool {
  return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$t);
}
function only_digits(string $s): string { return preg_replace('/\D+/', '', $s); }

/* Validação matemática do CNPJ (opcional; usamos de forma flexível abaixo) */
function validaCNPJ(string $cnpj): bool {
  $cnpj = only_digits($cnpj);
  if (strlen($cnpj) !== 14 || preg_match('/^(.)\1{13}$/', $cnpj)) return false;
  $calc = function($base, $pesos) {
    $s = 0; foreach ($pesos as $i => $p) { $s += (int)$base[$i] * $p; }
    $r = $s % 11; return $r < 2 ? 0 : 11 - $r;
  };
  $b1 = $calc($cnpj, [5,4,3,2,9,8,7,6,5,4,3,2]);
  if ((int)$cnpj[12] !== $b1) return false;
  $b2 = $calc($cnpj, [6,5,4,3,2,9,8,7,6,5,4,3,2]);
  return (int)$cnpj[13] === $b2;
}

/* ========= POST handler ========= */
$errors = [];
$success = false;
$cliente = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) {
    $errors[] = 'Sessão expirada. Atualize a página e tente novamente.';
  } else {
    $cnpj = only_digits($_POST['cnpj'] ?? '');
    if (strlen($cnpj) !== 14) {
      $errors[] = 'Informe exatamente 14 dígitos no CNPJ (somente números).';
    } else {
      try {
        $pdo = db();

        // 0) Descobre se a tabela é "clientes" ou "clients"
        $tbl = null;
        $qTbl = $pdo->prepare("
          SELECT TABLE_NAME
          FROM information_schema.tables
          WHERE table_schema = :db AND TABLE_NAME IN ('clientes','clients')
          LIMIT 1
        ");
        $qTbl->execute([':db' => DB_NAME]);
        $rowTbl = $qTbl->fetch();
        if ($rowTbl && isset($rowTbl['TABLE_NAME'])) {
          $tbl = $rowTbl['TABLE_NAME'];
        } else {
          throw new RuntimeException('Tabela de clientes não encontrada (clientes/clients).');
        }

        // 1) Tentativa direta (coluna cnpj CHAR(14) sem máscara)
        $st = $pdo->prepare("SELECT * FROM {$tbl} WHERE TRIM(cnpj) = :cnpj LIMIT 1");
        $st->execute([':cnpj' => $cnpj]);
        $cliente = $st->fetch();

        // 2) Fallback com normalização (caso haja máscara no banco)
        if (!$cliente) {
          $st = $pdo->prepare("
            SELECT * FROM {$tbl}
            WHERE REPLACE(REPLACE(REPLACE(TRIM(cnpj),'.',''),'/',''),'-','') = :cnpj
            LIMIT 1
          ");
          $st->execute([':cnpj' => $cnpj]);
          $cliente = $st->fetch();
        }

        if ($cliente) {
          $_SESSION['abertura_os_cnpj']    = $cnpj;
          $_SESSION['abertura_os_cliente'] = $cliente;
          $success = true;
          // header('Location: abrir_os_detalhes.php'); exit; // se quiser redirecionar direto
        } else {
          $errors[] = 'CNPJ não localizado em nossa base.';
        }
      } catch (Throwable $e) {
        // Log silencioso no servidor para debug
        error_log('abrir_os.php erro: '.$e->getMessage());
        $errors[] = 'Erro temporário ao consultar. Tente novamente em instantes.';
      }
    }
  }
}
$csrf = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
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
		<title>Delta Coding — Abrir OS (CNPJ)</title>
		<link rel="icon" type="image/png" sizes="56x56" href="../images/fav-icon/icon.png">
		<link rel="stylesheet" type="text/css" href="../css/style.css">
		<link rel="stylesheet" type="text/css" href="../css/responsive.css">
		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
			<script src="vendor/html5shiv.js"></script>
			<script src="vendor/respond.js"></script>
		<![endif]-->
    <style>
      .os-card{max-width:720px;margin:32px auto;background:#fff;border:0px solid #e6e6e6;border-radius:12px;padding:22px}
      .os-card h1{font-size:26px;margin:0 0 4px}
      .os-sub{color:#6b7280;margin:0 0 14px}
      .os-instrucoes{background:#f7f9fc;border:1px dashed #cfd6e4;border-radius:10px;padding:14px;margin:16px 0}
      .os-instrucoes h3{font-size:16px;margin:0 0 8px}
      .os-instrucoes ul{margin:0 0 0 18px}
      .os-form label{display:block;font-weight:600;margin:12px 0 6px}
      .os-form input{width:100%;padding:12px;border:1px solid #cfd6e4;border-radius:10px;font-size:16px}
      .os-actions{margin-top:14px;display:flex;gap:10px;align-items:center;flex-wrap:wrap}
      .btn-primary{background:#0d6efd;color:#fff;border:0;border-radius:10px;padding:10px 16px;cursor:pointer;text-decoration:none;display:inline-block}
      .msg-erro{background:#fff2f0;color:#a8071a;border:1px solid #ffccc7;border-radius:10px;padding:10px;margin:8px 0}
      .msg-ok{background:#f6ffed;color:#237804;border:1px solid #b7eb8f;border-radius:10px;padding:10px;margin:8px 0}
      .preview{background:#fafafa;border:1px solid #eee;border-radius:10px;padding:10px;margin-top:10px}
      .muted{color:#6b7280}
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

			<!-- ===== Seção central: OS Etapa 1 (CNPJ) ===== -->
      <section class="section-spacing">
        <div class="container ">
          <div class="os-card">
            <h1>Abertura de Ordem de Serviço</h1>
            <p class="os-sub">Etapa 1 de 2 — Informe o CNPJ para localizar seu cadastro.</p>

            <?php if ($errors): ?>
              <div class="msg-erro">
                <?php foreach ($errors as $e) { echo '<div>'.htmlspecialchars($e, ENT_QUOTES, 'UTF-8').'</div>'; } ?>
              </div>
            <?php elseif ($success): ?>
              <div class="msg-ok">Cliente localizado com sucesso. Clique em <strong>Prosseguir</strong> para continuar.</div>
              <?php if (is_array($cliente)): ?>
                <div class="preview">
                  <strong>Resumo do cadastro:</strong>
                  <ul style="margin:8px 0 0 18px">
                    <?php
                      foreach (['name','email','phone','street','complement','district','state','cep'] as $k) {
                        if (!empty($cliente[$k])) {
                          echo '<li><b>'.htmlspecialchars(ucwords(str_replace('_',' ',$k))).':</b> '
                               .htmlspecialchars((string)$cliente[$k]).'</li>';
                        }
                      }
                    ?>
                  </ul>
                </div>
              <?php endif; ?>
            <?php endif; ?>

            <div class="os-instrucoes">
              <h3>Instruções para abrir a OS</h3>
              <ul>
                <li>Digite o <strong>CNPJ</strong> (somente números, 14 dígitos) para localizar seu cadastro.</li>
                <li>Na etapa 2, confirme seus dados, informe a <strong>máquina/equipamento</strong> e <strong>descreva o problema</strong>.</li>
                <li>Ao finalizar, geramos um <strong>protocolo</strong> para acompanhamento.</li>
                <li>Se o CNPJ não estiver cadastrado, <a href="https://wa.me/5531988335786" target="_blank" rel="noopener">fale conosco</a> para cadastro inicial.</li>
              </ul>
            </div>

            <form class="os-form" method="post" action="">
              <input type="hidden" name="csrf" value="<?=$csrf?>">
              <label for="cnpj">CNPJ (somente números) *</label>
              <input
                id="cnpj"
                name="cnpj"
                inputmode="numeric"
                pattern="\d{14}"
                maxlength="14"
                minlength="14"
                placeholder="Ex.: 12654789000101"
                required
                value="<?= isset($_POST['cnpj']) ? htmlspecialchars(only_digits($_POST['cnpj']), ENT_QUOTES, 'UTF-8') : '' ?>"
                oninput="this.value=this.value.replace(/\D/g,'').slice(0,14)"
              >
              <div class="os-actions">
                <button class="btn-primary" type="submit">Validar CNPJ</button>
                <?php if ($success): ?>
                  <a class="btn-primary" href="abrir_os_detalhes.php">Prosseguir »</a>
                <?php endif; ?>
                <span class="muted">Campos com * são obrigatórios.</span>
              </div>
            </form>
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

			<button class="scroll-top tran3s"><i class="fa fa-angle-up" aria-hidden="true"></i></button>

		<!-- JS originais do tema -->
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
		</div> <!-- /.main-page-wrapper -->
	</body>
</html>
