<nav class="sidebar">
  <div class="sidebar-header">
    <a href="#" class="sidebar-brand">
      Noble<span>UI</span>
    </a>
    <div class="sidebar-toggler not-active">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
  <div class="sidebar-body">
  <ul class="nav">
    {{-- PRINCIPAL --}}
    <li class="nav-item nav-category">Principal</li>
    <li class="nav-item {{ request()->routeIs('dashboard') || request()->is('/') ? 'active' : '' }}">
      <a href="{{ url('/') }}" class="nav-link">
        <i class="link-icon" data-feather="box"></i>
        <span class="link-title">Dashboard</span>
      </a>
    </li>

    {{-- CLIENTES --}}
    <li class="nav-item nav-category">Clientes</li>
    <li class="nav-item {{ request()->routeIs('clients.create') ? 'active' : '' }}">
      <a href="{{ route('clients.create') }}" class="nav-link">
        <i class="link-icon" data-feather="user-plus"></i>
        <span class="link-title">Cadastrar</span>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('clients.index','clients.show','clients.edit') ? 'active' : '' }}">
      <a href="{{ route('clients.index') }}" class="nav-link">
        <i class="link-icon" data-feather="users"></i>
        <span class="link-title">Listar</span>
      </a>
    </li>

    {{-- ORDENS DE SERVIÇO --}}
    <li class="nav-item nav-category">Ordens de Serviço</li>
    <li class="nav-item {{ request()->routeIs('service-orders.create') ? 'active' : '' }}">
      <a href="{{ route('service-orders.create') }}" class="nav-link">
        <i class="link-icon" data-feather="file-plus"></i>
        <span class="link-title">Cadastrar</span>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('service-orders.index','service-orders.show','service-orders.edit') ? 'active' : '' }}">
      <a href="{{ route('service-orders.index') }}" class="nav-link">
        <i class="link-icon" data-feather="file-text"></i>
        <span class="link-title">Listar</span>
      </a>
    </li>
  </ul>
</div>
</nav>
