@extends('layout.master')

@section('content')
<nav class="page-breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active" aria-current="page">Visualizar</li>
  </ol>
</nav>

<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Dados do Cliente</h4>
        <p class="text-muted mb-3">Add class <code>.table-striped</code></p>

        <div class="table-responsive">
          <table class="table table-striped">
            <tbody>
              <tr>
                <th style="width: 260px;">ID</th>
                <td>{{ $client->id }}</td>
              </tr>
              <tr>
                <th>Nome/Razão Social</th>
                <td>{{ $client->name ?? '-' }}</td>
              </tr>
              <tr>
                <th>CNPJ</th>
                <td>{{ $client->cnpj ?? '-' }}</td>
              </tr>
              <tr>
                <th>E-mail</th>
                <td>{{ $client->email ?? '-' }}</td>
              </tr>
              <tr>
                <th>Telefone</th>
                <td>{{ $client->phone ?? '-' }}</td>
              </tr>

              <tr>
                <th>Rua/Logradouro</th>
                <td>{{ $client->street ?? '-' }}</td>
              </tr>
              <tr>
                <th>Complemento</th>
                <td>{{ $client->complement ?? '-' }}</td>
              </tr>
              <tr>
                <th>Bairro</th>
                <td>{{ $client->district ?? '-' }}</td>
              </tr>
              <tr>
                <th>UF</th>
                <td>{{ $client->state ?? '-' }}</td>
              </tr>
              <tr>
                <th>CEP</th>
                <td>{{ $client->cep ?? '-' }}</td>
              </tr>

              <tr>
                <th>Máquinas</th>
                <td>
                  @if(!empty($client->machines))
                    <div style="white-space: pre-wrap;">{{ $client->machines }}</div>
                  @else
                    -
                  @endif
                </td>
              </tr>

              <tr>
                <th>Criado em</th>
                <td>{{ optional($client->created_at)->format('d/m/Y H:i') ?? '-' }}</td>
              </tr>
              <tr>
                <th>Atualizado em</th>
                <td>{{ optional($client->updated_at)->format('d/m/Y H:i') ?? '-' }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-3 d-flex gap-2">
          <a href="{{ route('clients.index') }}" class="btn btn-secondary">Voltar</a>
          <a href="{{ route('clients.edit', $client) }}" class="btn btn-primary">Editar</a>

          <form action="{{ route('clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Remover este cliente?');" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Excluir</button>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
