@extends('layout.master')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ordens de Serviço</li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">

        <div class="d-flex align-items-center justify-content-between mb-3">
          <h4 class="card-title mb-0">Ordens de Serviço</h4>
          <a href="{{ route('service-orders.create') }}" class="btn btn-primary">
            <i data-feather="file-plus" class="me-1"></i> Nova OS
          </a>
        </div>

        {{-- Filtros --}}
        <form method="GET" action="{{ route('service-orders.index') }}" class="row g-2 mb-3">
          <div class="col-md-4">
            <input type="text" class="form-control" name="q" value="{{ request('q') }}"
                   placeholder="Buscar por cliente, e-mail, telefone ou técnico">
          </div>
          <div class="col-md-3">
            <select name="status" class="form-select">
              <option value="">— Status —</option>
              @foreach($statusOptions as $opt)
                <option value="{{ $opt }}" {{ request('status') === $opt ? 'selected' : '' }}>
                  {{ ucfirst(str_replace('_',' ', $opt)) }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="De">
          </div>
          <div class="col-md-2">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="Até">
          </div>
          <div class="col-md-1 d-grid">
            <button class="btn btn-outline-secondary" type="submit">Filtrar</button>
          </div>
          @if(request()->hasAny(['q','status','from','to']) && request()->filled(['q']) || request()->filled(['status','from','to']))
            <div class="col-12 mt-1">
              <a href="{{ route('service-orders.index') }}" class="btn btn-link p-0">Limpar filtros</a>
            </div>
          @endif
        </form>

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th style="width:80px;">OS #</th>
                <th>Cliente</th>
                <th style="width:140px;">Status</th>
                <th style="width:170px;">Aberta em</th>
                <th style="width:170px;">Fechada em</th>
                <th style="width:180px;">Técnico</th>
                <th style="width:120px;" class="text-end">Valor (R$)</th>
                <th style="width:200px;" class="text-end">Ações</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
                <tr>
                  <td class="fw-semibold">#{{ $order->id }}</td>

                  <td>
                    <div class="fw-semibold">{{ $order->customer_name_snapshot }}</div>
                    <div class="small text-muted">
                      {{ $order->customer_phone_snapshot ?: '—' }} • {{ $order->customer_email_snapshot ?: '—' }}
                    </div>
                  </td>

                  <td>
                    @php
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
                    <span class="badge bg-{{ $badge }}">
                      {{ ucfirst(str_replace('_',' ', $status)) }}
                    </span>
                  </td>

                  <td>{{ optional($order->opened_at)->format('d/m/Y H:i') ?: '—' }}</td>
                  <td>{{ optional($order->closed_at)->format('d/m/Y H:i') ?: '—' }}</td>

                  <td>{{ $order->technician_name ?: '—' }}</td>

                  <td class="text-end">{{ number_format($order->total_amount, 2, ',', '.') }}</td>

                  <td class="text-end">
                    <a href="{{ route('service-orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                      <i data-feather="eye" class="me-1"></i> Ver
                    </a>
                    <a href="{{ route('service-orders.edit', $order) }}" class="btn btn-sm btn-primary">
                      <i data-feather="edit-2" class="me-1"></i> Editar
                    </a>
                    @if($order->status !== 'closed')
                      <form action="{{ route('service-orders.close', $order) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Encerrar esta OS?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">
                          <i data-feather="check-circle" class="me-1"></i> Fechar
                        </button>
                      </form>
                    @endif
                    <form action="{{ route('service-orders.destroy', $order) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Remover esta OS? Essa ação não pode ser desfeita.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i data-feather="trash-2" class="me-1"></i> Excluir
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center text-muted py-4">
                    Nenhuma ordem de serviço encontrada.
                    <a href="{{ route('service-orders.create') }}">Abrir nova OS</a>.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Paginação --}}
        <div class="mt-3">
          {{ $orders->links() }}
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
