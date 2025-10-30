@extends('layout.master')

@section('content')
@php
  $serviceTypeMap = [
    'installation'       => 'Instalação',
    'maintenance'        => 'Manutenção',
    'cleaning'           => 'Limpeza',
    'parts_replacement'  => 'Troca de Peças',
    'preventive'         => 'Manutenção',
    'corrective'         => 'Manutenção',
    'install'            => 'Instalação',
    'other'              => 'Manutenção',
  ];

  $problemCategoryMap = [
    'energia_alimentacao'    => 'Energia / Alimentação',
    'mecanico_transporte'    => 'Mecânico / Transporte',
    'impressao_cabeca'       => 'Impressão / Cabeça',
    'calibracao_alinhamento' => 'Calibração / Alinhamento',
    'firmware_software'      => 'Firmware / Software',
    'rede_conectividade'     => 'Rede / Conectividade',
    'sensores_leituras'      => 'Sensores / Leituras',
    'limpeza_preventiva'     => 'Limpeza / Preventiva',
    'acabamento'             => 'Acabamento',
    'outros'                 => 'Outros',
  ];

  $laborCost  = round(($order->labor_hours ?? 0) * ($order->technician_hour_cost_snapshot ?? 0), 2);
  $travelCost = round(($order->travel_km ?? 0)   * ($order->travel_cost_per_km_snapshot ?? 0), 2);
  $totalCost  = round(($order->parts_cost ?? 0) + $laborCost + $travelCost, 2);

  $status = $order->status;
  $badge = match($status) {
    'opened'      => 'secondary',
    'in_progress' => 'primary',
    'paused'      => 'warning',
    'closed'      => 'success',
    'canceled'    => 'danger',
    default       => 'light'
  };
@endphp

<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('service-orders.index') }}">Ordens de Serviço</a></li>
    <li class="breadcrumb-item active" aria-current="page">OS #{{ $order->id }}</li>
  </ol>
</nav>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">

        {{-- TOPO --}}
        <div class="container-fluid d-flex justify-content-between align-items-start">
          <div class="col-lg-6 ps-0">
            <a href="{{ url('/') }}" class="noble-ui-logo d-block mt-2">
              {{ config('app.name', 'App') }}<span> • OS</span>
            </a>

            <div class="mt-3 d-flex flex-wrap gap-2 align-items-center">
              <span class="badge bg-{{ $badge }} px-3 py-2">
                {{ ucfirst(str_replace('_',' ', $status)) }}
              </span>
              <span class="badge bg-info px-3 py-2">
                {{ $serviceTypeMap[$order->service_type] ?? ucfirst($order->service_type ?? '—') }}
              </span>
              @if($order->resolved_in_first_visit)
                <span class="badge bg-success px-3 py-2">FTFR: 1ª visita</span>
              @endif
            </div>

            <div class="mt-3">
              <h6 class="text-muted mb-1">Cliente</h6>
              <p class="mb-1 fw-semibold">{{ $order->customer_name_snapshot }}</p>

              {{-- Contatos do snapshot --}}
              <p class="mb-1">
                <span class="d-block">{{ $order->customer_email_snapshot ?: '—' }}</span>
                <span class="d-block">{{ $order->customer_phone_snapshot ?: '—' }}</span>
              </p>

              {{-- Endereço atual do cadastro (clients) --}}
              @php $c = $order->customer; @endphp
              @if($c)
                <div class="small text-muted">
                  {{-- Rua, número/complemento --}}
                  @if($c->street)
                    <div>
                      {{ $c->street }}@if($c->complement), {{ $c->complement }}@endif
                    </div>
                  @endif

                  {{-- Bairro --}}
                  @if($c->district)
                    <div>{{ $c->district }}</div>
                  @endif

                  {{-- Cidade - UF --}}
                  @if($c->city || $c->state)
                    <div>
                      {{ $c->city }}@if($c->state) - {{ $c->state }}@endif
                    </div>
                  @endif

                  {{-- CEP (formatado) --}}
                  @if($c->cep)
                    <div>CEP: {{ preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $c->cep) }}</div>
                  @endif

                  {{-- DISTÂNCIA ESTIMADA ATÉ O LOCAL --}}
                  @if(!empty($routeInfo))
                    <div class="alert alert-info d-flex align-items-center mt-2" role="alert" style="gap:10px;">
                      <i data-feather="navigation" class="icon-sm"></i>
                      <div>
                        <div class="fw-semibold">
                          Distância estimada até o local:
                          <span class="text-dark">
                            {{ number_format($routeInfo['km'], 2, ',', '.') }} km
                          </span>
                          @if(isset($routeInfo['minutes']))
                            • ~ {{ $routeInfo['minutes'] }} min
                          @endif
                        </div>
                        @if(!empty($routeInfo['fallback']))
                          <div class="small text-muted">
                            *Estimativa por distância em linha reta (Haversine).
                          </div>
                        @else
                          <div class="small text-muted">
                            *Estimativa de rota (OpenRouteService).
                          </div>
                        @endif
                      </div>
                    </div>
                  @endif


                  {{-- Link de mapa, se tiver coordenadas --}}
                  @if(!empty($c->latitude) && !empty($c->longitude))
                    <div class="mt-1">
                      <a href="https://www.google.com/maps?q={{ $c->latitude }},{{ $c->longitude }}"
                        target="_blank" rel="noopener" class="link-secondary">
                        Ver no mapa
                      </a>
                    </div>
                  @endif
                </div>
              @endif
            </div>


            @if($order->technician_name)
              <div class="mt-2 small text-muted">
                Técnico responsável: <strong>{{ $order->technician_name }}</strong>
              </div>
            @endif
          </div>

          <div class="col-lg-5 pe-0 text-end">
            <h4 class="fw-bold text-uppercase mt-2 mb-1">Ordem de Serviço</h4>
            <h6 class="mb-3"># OS-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h6>

            <div class="row text-end">
              <div class="col-12">
                <div class="text-muted">Abertura</div>
                <div class="mb-1">{{ optional($order->opened_at)->format('d/m/Y H:i') ?: '—' }}</div>
              </div>
              <div class="col-12">
                <div class="text-muted">Fechamento</div>
                <div class="mb-1">{{ optional($order->closed_at)->format('d/m/Y H:i') ?: '—' }}</div>
              </div>
              <div class="col-12">
                <div class="text-muted">Duração (h)</div>
                <div class="mb-3">{{ number_format($order->duration_hours ?? 0, 0, ',', '.') }}</div>
              </div>
            </div>

            <div class="text-muted">Valor Total</div>
            <h3 class="fw-normal mb-0">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</h3>
          </div>
        </div>

        {{-- INFO TÉCNICA RÁPIDA --}}
        <div class="container-fluid mt-4">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="border rounded p-3 h-100">
                <div class="text-muted">Categoria do problema</div>
                <div class="fw-semibold">
                  {{ $problemCategoryMap[$order->problem_category] ?? ($order->problem_category ?: '—') }}
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="border rounded p-3 h-100">
                <div class="text-muted">Horas de trabalho</div>
                <div class="fw-semibold">
                  {{ number_format($order->labor_hours ?? 0, 2, ',', '.') }} h
                  <span class="text-muted"> x R$ {{ number_format($order->technician_hour_cost_snapshot ?? 0, 2, ',', '.') }}</span>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="border rounded p-3 h-100">
                <div class="text-muted">Deslocamento</div>
                <div class="fw-semibold">
                  {{ number_format($order->travel_km ?? 0, 2, ',', '.') }} km
                  <span class="text-muted"> x R$ {{ number_format($order->travel_cost_per_km_snapshot ?? 0, 2, ',', '.') }}/km</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- DESCRIÇÕES --}}
        <div class="container-fluid mt-4 w-100">
          <div class="row">
            <div class="col-lg-12">
              <div class="border rounded p-3 mb-3">
                <h6 class="mb-2">Descrição do problema</h6>
                <div class="text-muted" style="white-space: pre-wrap;">
                  {{ $order->problem_description ?: '—' }}
                </div>
              </div>

              <div class="border rounded p-3 mb-3">
                <h6 class="mb-2">Serviços executados</h6>
                <div class="text-muted" style="white-space: pre-wrap;">
                  {{ $order->services_done ?: '—' }}
                </div>
              </div>

              <div class="border rounded p-3">
                <h6 class="mb-2">Peças / Materiais</h6>
                <div class="text-muted" style="white-space: pre-wrap;">
                  {{ $order->parts_list ?: '—' }}
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- RESUMO FINANCEIRO (sem margem) --}}
        <div class="container-fluid mt-4 w-100">
          <div class="row">
            <div class="col-md-7 ms-auto">
              <div class="table-responsive">
                <table class="table align-middle">
                  <tbody>
                    <tr>
                      <td>Peças</td>
                      <td class="text-end">R$ {{ number_format($order->parts_cost ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                      <td>Mão de obra
                        <span class="text-muted small d-block">
                          {{ number_format($order->labor_hours ?? 0, 2, ',', '.') }} h ×
                          R$ {{ number_format($order->technician_hour_cost_snapshot ?? 0, 2, ',', '.') }}
                        </span>
                      </td>
                      <td class="text-end">R$ {{ number_format($laborCost, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                      <td>Deslocamento
                        <span class="text-muted small d-block">
                          {{ number_format($order->travel_km ?? 0, 2, ',', '.') }} km ×
                          R$ {{ number_format($order->travel_cost_per_km_snapshot ?? 0, 2, ',', '.') }}/km
                        </span>
                      </td>
                      <td class="text-end">R$ {{ number_format($travelCost, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-light">
                      <td class="fw-semibold">Custo total</td>
                      <td class="fw-semibold text-end">R$ {{ number_format($totalCost, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                      <td>Valor (faturado)</td>
                      <td class="text-end">R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              @if($order->technician_name)
                <div class="small text-muted mt-2 text-end">
                  Técnico responsável: <strong>{{ $order->technician_name }}</strong>
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- AÇÕES --}}
        <div class="container-fluid w-100">
          <a href="{{ route('service-orders.index') }}" class="btn btn-secondary float-end mt-4 ms-2">
            <i data-feather="arrow-left" class="me-2 icon-md"></i>Voltar
          </a>
          <a href="{{ route('service-orders.pdf', $order) }}" class="btn btn-outline-secondary float-end mt-4 ms-2">
            <i data-feather="file-text" class="me-2 icon-md"></i>Gerar PDF
          </a>
          <a href="{{ route('service-orders.edit', $order) }}" class="btn btn-primary float-end mt-4 ms-2">
            <i data-feather="edit-2" class="me-2 icon-md"></i>Editar
          </a>
          @if(Route::has('service-orders.close') && $order->status !== 'closed')
            <form action="{{ route('service-orders.close', $order) }}" method="POST"
                  class="d-inline float-end mt-4 ms-2"
                  onsubmit="return confirm('Encerrar esta OS?');">
              @csrf
              <button type="submit" class="btn btn-success">
                <i data-feather="check-circle" class="me-2 icon-md"></i>Fechar OS
              </button>
            </form>
          @endif
          <button type="button" onclick="window.print();" class="btn btn-outline-primary float-end mt-4">
            <i data-feather="printer" class="me-2 icon-md"></i>Imprimir
          </button>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
