@extends('layout.master')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active" aria-current="page">Cadastrar</li>
  </ol>
</nav>

<div class="row">
  {{-- COLUNA ESQUERDA: FORMULÁRIO (8) --}}
  <div class="col-md-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">

        <h6 class="card-title">Novo Cliente</h6>

        <form class="forms-sample" method="POST" action="{{ route('clients.store') }}" novalidate>
          @csrf

          {{-- Dados básicos --}}
          <div class="mb-3">
            <label class="form-label" for="name">Nome/Razão Social <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name') }}" placeholder="Ex.: Delta Coding Ltda" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label" for="cnpj">CNPJ (apenas números) <span class="text-danger">*</span></label>
            <input type="text" inputmode="numeric" maxlength="14"
                   class="form-control @error('cnpj') is-invalid @enderror"
                   id="cnpj" name="cnpj" value="{{ old('cnpj') }}" placeholder="Ex.: 12345678000199" required>
            @error('cnpj') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label" for="email">E-mail</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email" value="{{ old('email') }}" placeholder="contato@empresa.com.br">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label" for="phone">Telefone (apenas números)</label>
            <input type="text" inputmode="numeric" class="form-control @error('phone') is-invalid @enderror"
                   id="phone" name="phone" value="{{ old('phone') }}" placeholder="27999998888">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <hr class="my-4">

          {{-- Seção de Endereço --}}
          <h6 class="card-title mb-3">Endereço</h6>

          <div class="mb-3">
            <label class="form-label" for="cep">CEP (8 dígitos)</label>
            <div class="input-group">
              <input type="text" inputmode="numeric" maxlength="9"
                     class="form-control @error('cep') is-invalid @enderror"
                     id="cep" name="cep" value="{{ old('cep') }}" placeholder="00000-000">
              <button type="button" id="btnBuscarCep" class="btn btn-outline-primary">
                Buscar CEP
              </button>
            </div>
            @error('cep') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small class="text-muted d-block mt-1">Digite o CEP e clique em “Buscar” para preenchimento automático.</small>
            <div id="cepStatus" class="small text-muted mt-1"></div>
          </div>

          <input type="hidden" id="codigo_ibge" name="codigo_ibge" value="{{ old('codigo_ibge') }}">

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="latitude">Latitude</label>
                <input type="text" readonly class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="longitude">Longitude</label>
                <input type="text" readonly class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <label class="form-label" for="street">Rua/Logradouro</label>
                <input type="text" class="form-control @error('street') is-invalid @enderror"
                       id="street" name="street" value="{{ old('street') }}" placeholder="Preenchido ao buscar CEP">
                @error('street') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label" for="complement">Complemento<span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('complement') is-invalid @enderror"
                       id="complement" name="complement" value="{{ old('complement') }}" placeholder="Sala 402, Apto 101..." required>
                @error('complement') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-5">
              <div class="mb-3">
                <label class="form-label" for="city">Cidade</label>
                {{-- Para edit.blade.php, use: value="{{ old('city', $client->city) }}" --}}
                <input type="text" class="form-control @error('city') is-invalid @enderror"
                       id="city" name="city" value="{{ old('city') }}" placeholder="Preenchido ao buscar CEP">
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label" for="district">Bairro</label>
                {{-- Para edit.blade.php, use: value="{{ old('district', $client->district) }}" --}}
                <input type="text" class="form-control @error('district') is-invalid @enderror"
                       id="district" name="district" value="{{ old('district') }}" placeholder="Preenchido ao buscar CEP">
                @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="col-md-3">
              <div class="mb-3">
                <label class="form-label" for="state">UF</label>
                {{-- Para edit.blade.php, use: value="{{ old('state', $client->state) }}" --}}
                <input type="text" class="form-control @error('state') is-invalid @enderror"
                       id="state" name="state" value="{{ old('state') }}" placeholder="UF" maxlength="2" style="text-transform: uppercase;">
                @error('state') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          <hr class="my-4">

          {{-- Máquinas --}}
          <h6 class="card-title mb-3">Informações Adicionais</h6>
          <div class="mb-3">
            <label class="form-label" for="machines">Máquinas (descrição livre)</label>
            <textarea class="form-control @error('machines') is-invalid @enderror"
                      id="machines" name="machines" rows="4"
                      placeholder="Ex.: Impressora HP LaserJet M402; PC Financeiro (i5/8GB/SSD256); CNC XYZ…">{{ old('machines') }}</textarea>
            @error('machines') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>

      </div>
    </div>
  </div>

  {{-- COLUNA DIREITA: INSTRUÇÕES (4) --}}
<div class="col-md-4 grid-margin stretch-card">
    <div class="card h-100">
      <div class="card-body">
        <h6 class="card-title">Guia de Preenchimento</h6>
        <p class="text-muted small">
            Preencha o formulário ao lado com atenção. Estas dicas vão te ajudar a cadastrar tudo de forma rápida e sem erros.
        </p>
        <hr>
        <ul class="list-unstyled">
            <li class="mb-3">
                <strong>1. Campos Obrigatórios</strong>
                <p class="small text-muted mb-0">
                    Fique de olho no asterisco vermelho (<span class="text-danger">*</span>). Ele indica os campos que são essenciais e não podem ficar em branco.
                </p>
            </li>
            <li class="mb-3">
                <strong>2. Busca de Endereço Mágica</strong>
                <p class="small text-muted mb-0">
                    Para poupar seu tempo, digite o <strong>CEP</strong> (ex: <code>29055-131</code>) e clique em <strong>"Buscar CEP"</strong>. Nosso sistema preencherá a Rua, Bairro, UF e as coordenadas para você!
                </p>
            </li>
            <li class="mb-3">
                <strong>3. Detalhes do Endereço (Complemento)</strong>
                <p class="small text-muted mb-0">
                    O campo <strong>Complemento</strong> é o único que você precisa preencher manualmente. Informe aqui os detalhes que a busca de CEP não encontra.
                    <br><em>Exemplos: <code>Sala 502</code>, <code>Apto 1201</code>, <code>Bloco B</code>, <code>Fundos</code>.</em>
                </p>
            </li>
            <li class="mb-3">
                <strong>4. Apenas Números, por favor!</strong>
                <p class="small text-muted mb-0">
                    Para que o sistema funcione corretamente, digite <strong>apenas os números</strong> nos seguintes campos:
                    <br>• <strong>CNPJ:</strong> <code>12345678000199</code> (não <code>12.345...</code>)
                    <br>• <strong>Telefone:</strong> <code>27999998888</code> (não <code>(27)...</code>)
                </p>
            </li>
            <li>
                <strong>5. Inventário de Máquinas</strong>
                <p class="small text-muted mb-0">
                    Liste aqui os equipamentos importantes do cliente. Isso ajuda a manter um registro técnico organizado.
                    <br><strong>Importante:</strong> Separe cada máquina com um <strong>ponto e vírgula (;)</strong>.
                    <br>
                    <strong class="d-block mt-1">Exemplo de preenchimento:</strong>
                    <code class="d-block p-2 bg-light border rounded mt-1">Impressora HP M402; Servidor Dell T40 (Xeon/16GB); PC Recepção (i5/8GB)</code>
                </p>
            </li>
        </ul>
      </div>
    </div>
  </div>
</div>

{{-- JS CEP → ViaCEP → IBGE → sua API de coords --}}
@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const $ = (s) => document.querySelector(s);

  const cepInput   = $('#cep');
  const btnBuscar  = $('#btnBuscarCep');
  const statusEl   = $('#cepStatus');

  const street     = $('#street');
  const complement = $('#complement');
  const city       = $('#city'); //
  const district   = $('#district'); // bairro
  const state      = $('#state');    // UF

  const ibgeField  = $('#codigo_ibge');
  const latField   = $('#latitude');
  const lngField   = $('#longitude');

  // máscara leve (#####-###)
  cepInput.addEventListener('input', () => {
    let v = cepInput.value.replace(/\D/g, '').slice(0, 8);
    if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
    cepInput.value = v;
  });

  // Enter dispara busca
  cepInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); btnBuscar.click(); }
  });

  btnBuscar.addEventListener('click', async () => {
    statusEl.textContent = '';
    const raw = cepInput.value.trim();
    const onlyDigits = raw.replace(/\D/g, '');
    if (onlyDigits.length !== 8) {
      statusEl.innerHTML = '<span class="text-danger">CEP inválido: use 8 dígitos.</span>';
      return;
    }

    btnBuscar.disabled = true;
    statusEl.textContent = 'Consultando ViaCEP...';

    try {
      // 1) ViaCEP
      const viaUrl = `https://viacep.com.br/ws/${onlyDigits}/json/`;
      const viaResp = await fetch(viaUrl, { cache: 'no-store' });
      if (!viaResp.ok) throw new Error('ViaCEP indisponível');
      const via = await viaResp.json();

      if (via.erro) {
        statusEl.innerHTML = '<span class="text-danger">CEP não encontrado no ViaCEP.</span>';
        return;
      }

      // Preenche endereço (SEM O COMPLEMENTO)
      if (via.logradouro) street.value = via.logradouro;
      // if (via.complemento && !complement.value) complement.value = via.complemento; // <-- LINHA REMOVIDA
      if (via.localidade) city.value = via.localidade;
      if (via.bairro)     district.value = via.bairro;
      if (via.uf)         state.value = String(via.uf).toUpperCase();

      // Normaliza CEP no campo
      cepInput.value = (via.cep || raw || '').replace(/\D/g,'').replace(/(\d{5})(\d{3})/, '$1-$2');

      // 2) IBGE -> sua API coords
      const ibge = via.ibge;
      if (!ibge) {
        statusEl.innerHTML = '<span class="text-warning">IBGE não veio no ViaCEP; sem coordenadas.</span>';
        return;
      }
      ibgeField.value = ibge;

      statusEl.textContent = 'Consultando coordenadas...';
      const apiUrl = `https://rodrigozambon.com.br/sophiapi/public/api/municipios/${ibge}/coords`;
      const apiResp = await fetch(apiUrl, { cache: 'no-store' });
      if (!apiResp.ok) throw new Error('API de municípios retornou erro');
      const coords = await apiResp.json();

      const lat = parseFloat(coords.latitude);
      const lng = parseFloat(coords.longitude);

      if (!isNaN(lat) && !isNaN(lng)) {
        latField.value = lat.toFixed(6);
        lngField.value = lng.toFixed(6);
        statusEl.innerHTML = '<span class="text-success">Endereço e coordenadas preenchidos.</span>';
      } else {
        statusEl.innerHTML = '<span class="text-warning">Coordenadas não retornadas pela API.</span>';
      }
    } catch (e) {
      console.error(e);
      statusEl.innerHTML = '<span class="text-danger">Falha ao consultar CEP/Coordenadas.</span>';
    } finally {
      btnBuscar.disabled = false;
    }
  });
});
</script>
<script>
// Este script garante que o CEP seja enviado sem a máscara para o backend.
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form.forms-sample');
  const cepInput = document.getElementById('cep');

  form.addEventListener('submit', function () {
    if (cepInput && cepInput.value) {
      cepInput.value = cepInput.value.replace(/\D/g, ''); // mantém só dígitos
    }
  });
});
</script>
@endpush
@endsection