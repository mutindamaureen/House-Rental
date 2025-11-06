<header class="header sticky-top bg-white shadow-sm">
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid d-flex align-items-center justify-content-between">

      <!-- Left Section: Logo -->
      <div class="navbar-header d-flex align-items-center">
        <a href="index.html" class="navbar-brand d-flex align-items-center">
          <div class="brand-text brand-big text-uppercase">
            <strong class="text-primary">Dark</strong><strong>Admin</strong>
          </div>
          <div class="brand-text brand-sm ms-2">
            <strong class="text-primary">D</strong><strong>A</strong>
          </div>
        </a>
        <button class="btn btn-sm btn-outline-secondary ms-3 sidebar-toggle">
          <i class="fa fa-long-arrow-left"></i>
        </button>
      </div>

      <!-- Center Section: Search -->
      <form id="searchForm" class="d-flex" action="{{ url('search_house') }}" method="get">
        @csrf
        <input
          class="form-control me-2"
          type="search"
          name="search"
          placeholder="What are you searching for..."
          aria-label="Search">
        <button class="btn btn-outline-primary" type="submit">Search</button>
      </form>

      <!-- Right Section: Logout Button -->
      <div class="logout">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-danger">
            <i class="fa fa-sign-out-alt me-1"></i> Logout
          </button>
        </form>
      </div>

    </div>
  </nav>
</header>
