@extends('layout.master')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('service-orders.index') }}">Ordens de Serviço</a></li>
    <li class="breadcrumb-item active" aria-current="page">Nova OS</li>
  </ol>
</nav>

<div class="row">
  {{-- COLUNA ESQUERDA: FORMULÁRIO (8) --}}
  <div class="col-md-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">

        <h6 class="card-title">Abrir Ordem de Serviço</h6>

        <form class="forms-sample" method="POST" action="{{ route('service-orders.store') }}" novalidate>
          @csrf

          {{-- Cliente --}}
          <div class="mb-3">
            <label class="form-label" for="customer_id">Cliente <span class="text-danger">*</span></label>
            <select class="form-select @error('customer_id') is-invalid @enderror"
                    id="customer_id" name="customer_id" required>
              <option value="" disabled {{ old('customer_id') ? '' : 'selected' }}>Selecione um cliente</option>
              @foreach($clients as $id => $name)
                <option value="{{ $id }}" {{ (string)$id === (string)old('customer_id') ? 'selected' : '' }}>
                  {{ $name }}
                </option>
              @endforeach
            </select>
            @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small class="text-muted d-block mt-1">
              Os dados do cliente (nome, telefone, e-mail) serão copiados como snapshot no momento da abertura.
            </small>
          </div>

          <div class="row">
            <div class="col-md-6">
              {{-- Abertura --}}
              <div class="mb-3">
                <label class="form-label" for="opened_at">Data de abertura</label>
                <input type="datetime-local"
                       class="form-control @error('opened_at') is-invalid @enderror"
                       id="opened_at" name="opened_at"
                       value="{{ old('opened_at') }}">
                @error('opened_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <small class="text-muted">Se vazio, será usado o horário atual.</small>
              </div>
            </div>
            <div class="col-md-6">
              {{-- Status --}}
              <div class="mb-3">
                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                <select class="form-select @error('status') is-invalid @enderror"
                        id="status" name="status" required>
                  @foreach($statusOptions as $status)
                    <option value="{{ $status }}" {{ old('status', 'opened') === $status ? 'selected' : '' }}>
                      {{ Str::title(str_replace('_',' ', $status)) }}
                    </option>
                  @endforeach
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          {{-- Tipo de serviço --}}
          <div class="mb-3">
            <label class="form-label" for="service_type">Tipo de serviço <span class="text-danger">*</span></label>
            <select class="form-select @error('service_type') is-invalid @enderror"
                    id="service_type" name="service_type" required>
              @php
                $serviceTypes = [
                  'installation'       => 'Instalação',
                  'maintenance'        => 'Manutenção',
                  'cleaning'           => 'Limpeza',
                  'parts_replacement'  => 'Troca de Peças',
                ];
                $serviceTypeValue = old('service_type', 'maintenance');
              @endphp
              @foreach($serviceTypes as $val => $label)
                <option value="{{ $val }}" {{ $serviceTypeValue === $val ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
            @error('service_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Categoria do problema --}}
          <div class="mb-3">
            <label class="form-label" for="problem_category">Categoria do problema</label>
            <select class="form-select @error('problem_category') is-invalid @enderror"
                    id="problem_category" name="problem_category">
              @php
                $problemCats = [
                  'energia_alimentacao'   => 'Energia / Alimentação',
                  'mecanico_transporte'   => 'Mecânico / Transporte',
                  'impressao_cabeca'      => 'Impressão / Cabeça',
                  'calibracao_alinhamento'=> 'Calibração / Alinhamento',
                  'firmware_software'     => 'Firmware / Software',
                  'rede_conectividade'    => 'Rede / Conectividade',
                  'sensores_leituras'     => 'Sensores / Leituras',
                  'limpeza_preventiva'    => 'Limpeza / Preventiva',
                  'acabamento'            => 'Acabamento',
                  'outros'                => 'Outros',
                ];
                $problemCatValue = old('problem_category', 'outros');
              @endphp
              @foreach($problemCats as $val => $label)
                <option value="{{ $val }}" {{ $problemCatValue === $val ? 'selected' : '' }}>
                  {{ $label }}
                </option>
              @endforeach
            </select>
            @error('problem_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>


          {{-- Problema --}}
          <div class="mb-3">
            <label class="form-label" for="problem_description">Descrição do problema</label>
            <textarea class="form-control @error('problem_description') is-invalid @enderror"
                      id="problem_description" name="problem_description" rows="4"
                      placeholder="Relate o problema informado pelo cliente...">{{ old('problem_description') }}</textarea>
            @error('problem_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Serviços executados --}}
          <div class="mb-3">
            <label class="form-label" for="services_done">Serviços executados</label>
            <textarea class="form-control @error('services_done') is-invalid @enderror"
                      id="services_done" name="services_done" rows="4"
                      placeholder="Descreva os procedimentos realizados...">{{ old('services_done') }}</textarea>
            @error('services_done') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Peças / Materiais (lista livre) --}}
          <div class="mb-3">
            <label class="form-label" for="parts_list">Peças / Materiais</label>
            <textarea class="form-control @error('parts_list') is-invalid @enderror"
                      id="parts_list" name="parts_list" rows="3"
                      placeholder="Liste peças e materiais utilizados (quantidade, referência)...">{{ old('parts_list') }}</textarea>
            @error('parts_list') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- Operacional: horas e deslocamento --}}
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label" for="labor_hours">Horas de trabalho (h)</label>
                <input type="number" step="0.01" min="0"
                       class="form-control @error('labor_hours') is-invalid @enderror"
                       id="labor_hours" name="labor_hours"
                       value="{{ old('labor_hours', '0.00') }}">
                @error('labor_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label" for="travel_km">Deslocamento (km, ida+volta)</label>
                <input type="number" step="0.01" min="0"
                       class="form-control @error('travel_km') is-invalid @enderror"
                       id="travel_km" name="travel_km"
                       value="{{ old('travel_km', '0.00') }}">
                @error('travel_km') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label" for="technician_name">Técnico responsável</label>
                <input type="text" class="form-control @error('technician_name') is-invalid @enderror"
                       id="technician_name" name="technician_name"
                       value="{{ old('technician_name') }}" placeholder="Nome do técnico">
                @error('technician_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          {{-- Custos (snapshots) --}}
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label" for="technician_hour_cost_snapshot">Custo/h do técnico (R$)</label>
                <input type="number" step="0.01" min="0"
                       class="form-control @error('technician_hour_cost_snapshot') is-invalid @enderror"
                       id="technician_hour_cost_snapshot" name="technician_hour_cost_snapshot"
                       value="{{ old('technician_hour_cost_snapshot', '0.00') }}">
                @error('technician_hour_cost_snapshot') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label" for="travel_cost_per_km_snapshot">Custo por km (R$)</label>
                <input type="number" step="0.01" min="0"
                       class="form-control @error('travel_cost_per_km_snapshot') is-invalid @enderror"
                       id="travel_cost_per_km_snapshot" name="travel_cost_per_km_snapshot"
                       value="{{ old('travel_cost_per_km_snapshot', '0.00') }}">
                @error('travel_cost_per_km_snapshot') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label" for="parts_cost">Custo das peças (R$)</label>
                <input type="number" step="0.01" min="0"
                       class="form-control @error('parts_cost') is-invalid @enderror"
                       id="parts_cost" name="parts_cost"
                       value="{{ old('parts_cost', '0.00') }}">
                @error('parts_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>
          </div>

          <div class="row">
            {{-- Valor total --}}
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="total_amount">Valor total (R$) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" min="0"
                       class="form-control @error('total_amount') is-invalid @enderror"
                       id="total_amount" name="total_amount"
                       value="{{ old('total_amount', '0.00') }}" required>
                @error('total_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            {{-- FTFR (opcional já na abertura) --}}
            <div class="col-md-6">
              <div class="mb-3 form-check mt-4">
                <input type="checkbox"
                       class="form-check-input @error('resolved_in_first_visit') is-invalid @enderror"
                       id="resolved_in_first_visit" name="resolved_in_first_visit"
                       value="1" {{ old('resolved_in_first_visit') ? 'checked' : '' }}>
                <label class="form-check-label" for="resolved_in_first_visit">
                  Resolvida na 1ª visita (FTFR)
                </label>
                @error('resolved_in_first_visit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <small class="text-muted d-block">Se ainda não sabe, deixe desmarcado e marque ao encerrar.</small>
              </div>
            </div>
          </div>

          {{-- Fechamento opcional --}}
          <div class="mb-3">
            <label class="form-label" for="closed_at">Data de fechamento (opcional)</label>
            <input type="datetime-local"
                   class="form-control @error('closed_at') is-invalid @enderror"
                   id="closed_at" name="closed_at"
                   value="{{ old('closed_at') }}">
            @error('closed_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('service-orders.index') }}" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>

      </div>
    </div>
  </div>

{{-- COLUNA DIREITA: INSTRUÇÕES (4) --}}
<div class="col-md-4 grid-margin stretch-card">
  <div class="card h-100">
    <div class="card-body">
      <h6 class="card-title">Instruções de Preenchimento</h6>

      <div class="mb-3">
        <h6 class="mb-1">1) Cliente & Status</h6>
        <ul class="small text-muted ps-3">
          <li><strong>Cliente</strong> (obrigatório): escolha na lista. Os dados do cliente são gravados como <em>snapshot</em> na OS.</li>
          <li><strong>Status</strong> inicial: <code>opened</code>. Use:
            <ul class="ps-3 mb-0">
              <li><code>in_progress</code>: técnico em atendimento</li>
              <li><code>paused</code>: aguardando peça/cliente</li>
              <li><code>closed</code>: concluída</li>
              <li><code>canceled</code>: cancelada</li>
            </ul>
          </li>
        </ul>
      </div>

      <div class="mb-3">
        <h6 class="mb-1">2) Tipo & Categoria do Problema</h6>
        <ul class="small text-muted ps-3">
          <li><strong>Tipo de serviço</strong>: Instalação, Manutenção, Limpeza, Troca de Peças.</li>
          <li><strong>Categoria</strong>: escolha a que melhor descreve o sintoma.
            <div class="mt-1">
              <span class="badge bg-light text-dark me-1 mb-1">Firmware / Software</span>
              <span class="badge bg-light text-dark me-1 mb-1">Rede / Conectividade</span>
              <span class="badge bg-light text-dark me-1 mb-1">Mecânico / Transporte</span>
              <span class="badge bg-light text-dark me-1 mb-1">Energia / Alimentação</span>
            </div>
          </li>
          <li><em>Exemplo</em>: “Instalação” + “Rede / Conectividade”.</li>
        </ul>
      </div>

      <div class="mb-3">
        <h6 class="mb-1">3) Datas</h6>
        <ul class="small text-muted ps-3">
          <li><strong>Abertura</strong>: pode ficar vazia (usa o horário atual).</li>
          <li><strong>Fechamento</strong>: preencha apenas quando finalizar (deve ser ≥ abertura).</li>
        </ul>
      </div>

      <div class="mb-3">
        <h6 class="mb-1">4) Descrição, Serviços e Peças</h6>
        <ul class="small text-muted ps-3">
          <li><strong>Descrição do problema</strong>: relato do cliente + sintomas.
            <div class="mt-1">Ex.: “Impressora não conecta ao Wi-Fi; LED pisca em vermelho.”</div>
          </li>
          <li><strong>Serviços executados</strong>: procedimentos realizados.
            <div class="mt-1">Ex.: “Atualizado firmware; reconfigurado SSID; teste de impressão OK.”</div>
          </li>
          <li><strong>Peças/Materiais</strong>: liste itens e quantidades.
            <div class="mt-1">Ex.: “Fonte 24V (1 un), Cabo CAT6 (10 m)”.</div>
          </li>
        </ul>
      </div>

      <div class="mb-3">
        <h6 class="mb-1">5) Operacional & Custos</h6>
        <ul class="small text-muted ps-3">
          <li><strong>Horas de trabalho</strong> (formato decimal): use <code>1.50</code> para 1h30.  
              <em>Ex.:</em> 2.00</li>
          <li><strong>Deslocamento (km)</strong>: ida + volta.  
              <em>Ex.:</em> 18.5</li>
          <li><strong>Custo/h do técnico</strong> (R$) e <strong>Custo por km</strong> (R$): valores <em>snapshot</em> do dia.  
              <em>Ex.:</em> 80.00 (hora), 1.90 (km)</li>
          <li><strong>Custo das peças</strong> (R$): soma dos itens usados.  
              <em>Ex.:</em> 120.00</li>
          <li><strong>Cálculo automático do Total</strong> =
            <code>horas × custo_hora + km × custo_km + custo_peças</code>. O campo “Valor total (R$)” é atualizado
            sozinho; revise antes de salvar.
          </li>
          <li><strong>FTFR</strong>: marque <em>Resolvida na 1ª visita</em> somente se a OS foi concluída na primeira ida.</li>
        </ul>
      </div>

      <div class="mb-3">
        <h6 class="mb-1">6) Exemplos completos</h6>
        <ul class="small text-muted ps-3">
          <li><em>Instalação + Rede/Conectividade</em>: horas <code>2.00</code>, km <code>18.5</code>,
              custo/h <code>80.00</code>, custo/km <code>1.90</code>, peças <code>120.00</code> → total = <code>2×80 + 18.5×1.9 + 120 = 301.15</code></li>
          <li><em>Manutenção + Mecânico/Transporte</em>: horas <code>1.50</code>, km <code>6</code>,
              custo/h <code>90.00</code>, custo/km <code>2.10</code>, peças <code>0.00</code> → total = <code>1.5×90 + 6×2.1 = 153.60</code></li>
        </ul>
      </div>

      <div class="mb-3">
        <h6 class="mb-1">7) Erros comuns (evite)</h6>
        <ul class="small text-muted ps-3">
          <li>Usar vírgula em horas (<code>1,5</code>) — use <code>1.5</code>.</li>
          <li>Informar somente “km de ida” — preencha <strong>ida + volta</strong>.</li>
          <li>Deixar “Valor total” diferente do cálculo automático.</li>
          <li>Fechar OS sem preencher o que foi feito.</li>
        </ul>
      </div>

      <hr>
      <p class="small text-muted mb-0">
        Dica: você pode editar a OS depois em <em>Ordens de Serviço &gt; Listar</em>.
      </p>
    </div>
  </div>
</div>
</div>
@push('custom-scripts')
<script>
(function(){
  function val(id){
    const el = document.getElementById(id);
    if(!el) return 0;
    const n = parseFloat((el.value || '').toString().replace(',', '.'));
    return isNaN(n) ? 0 : n;
  }
  function setMoney(id, v){
    const el = document.getElementById(id);
    if(el) el.value = (Math.round(v * 100) / 100).toFixed(2);
  }
  function recalc(){
    const labor  = val('labor_hours') * val('technician_hour_cost_snapshot');
    const travel = val('travel_km')   * val('travel_cost_per_km_snapshot');
    const parts  = val('parts_cost');
    const total  = labor + travel + parts;
    setMoney('total_amount', total);
  }

  ['labor_hours','technician_hour_cost_snapshot','travel_km','travel_cost_per_km_snapshot','parts_cost']
    .forEach(id => {
      const el = document.getElementById(id);
      if(el){
        el.addEventListener('input', recalc);
        el.addEventListener('change', recalc);
      }
    });

  // calcula uma vez ao carregar
  document.addEventListener('DOMContentLoaded', recalc);
})();
</script>
@endpush
@endsection
