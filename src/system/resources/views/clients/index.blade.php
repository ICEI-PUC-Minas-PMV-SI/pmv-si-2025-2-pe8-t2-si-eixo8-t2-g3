@extends('layout.master')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Clientes</li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">

        <div class="d-flex align-items-center justify-content-between mb-3">
          <h4 class="card-title mb-0">Clientes</h4>
          <a href="{{ route('clients.create') }}" class="btn btn-primary">
            <i data-feather="user-plus" class="me-1"></i> Novo Cliente
          </a>
        </div>

        {{-- Filtro de busca (opcional) --}}
        <form method="GET" action="{{ route('clients.index') }}" class="row g-2 mb-3">
          <div class="col-md-4">
            <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Buscar por nome, CNPJ, e-mail ou telefone">
          </div>
          <div class="col-md-2">
            <button class="btn btn-outline-secondary w-100" type="submit">Buscar</button>
          </div>
          @if(request()->filled('q'))
            <div class="col-md-2">
              <a href="{{ route('clients.index') }}" class="btn btn-link">Limpar</a>
            </div>
          @endif
        </form>

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th style="width:80px;">ID</th>
                <th>Nome/Razão Social</th>
                <th style="width:160px;">CNPJ</th>
                <th>E-mail</th>
                <th style="width:140px;">Telefone</th>
                <th style="width:260px;">Endereço</th>
                <th style="width:160px;" class="text-end">Ações</th>
              </tr>
            </thead>
            <tbody>
              @forelse($clients as $client)
                <tr>
                  <td>{{ $client->id }}</td>
                  <td class="fw-semibold">{{ $client->name }}</td>
                  <td><code>{{ $client->cnpj }}</code></td>
                  <td>{{ $client->email ?? '—' }}</td>
                  <td>{{ $client->phone ?? '—' }}</td>
                  <td>
                    @php
                      $partes = array_filter([
                        $client->street,
                        $client->district,
                        $client->state,
                        $client->cep
                      ]);
                    @endphp
                    {{ $partes ? implode(' • ', $partes) : '—' }}
                  </td>
                  <td class="text-end">
                    <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-secondary">
                      <i data-feather="eye" class="me-1"></i> Ver
                    </a>
                    <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-primary">
                      <i data-feather="edit-2" class="me-1"></i> Editar
                    </a>
                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Remover este cliente?');">
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
                  <td colspan="7" class="text-center text-muted py-4">
                    Nenhum cliente encontrado.
                    <a href="{{ route('clients.create') }}">Cadastrar agora</a>.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Paginação --}}
        <div class="mt-3">
          {{ $clients->appends(['q' => request('q')])->links() }}
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
