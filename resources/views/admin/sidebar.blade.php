{{--
<!-- Sidebar Navigation-->


<nav id="sidebar">
  <!-- Sidebar Header-->
  <div class="sidebar-header d-flex align-items-center">
    <div class="avatar">
      <img src="{{ asset('admincss/img/avatar-6.jpg') }}" alt="..." class="img-fluid rounded-circle">
    </div>
    <div class="title">
      <h1 class="h5">Mark Stephen</h1>
      <p>Web Designer</p>
    </div>
  </div>

  <!-- Sidebar Navigation Menus -->
  <span class="heading">Main</span>
  <ul class="list-unstyled">
    <li class="active">
      <a href="{{ url('admin/dashboard') }}">
        <i class="icon-home"></i> Home
      </a>
    </li>

    <li>
      <a href="{{ url('view_category') }}">
        <i class="icon-grid"></i> Category
      </a>
    </li>

    <li>
      <a href="#houseDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-windows"></i> Houses
      </a>
      <ul id="houseDropdown" class="collapse list-unstyled">
        <li><a href="{{ url('add_house') }}">Add House</a></li>
        <li><a href="{{ url('view_house') }}">View House</a></li>
      </ul>
    </li>

    <li>
      <a href="#userDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-user"></i> Users
      </a>
      <ul id="userDropdown" class="collapse list-unstyled">
        <li><a href="{{ url('add_user') }}">Add User</a></li>
        <li><a href="{{ url('view_user') }}">View Users</a></li>
      </ul>
    </li>

    <li>
      <a href="#tenantDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-people"></i> Tenants
      </a>
      <ul id="tenantDropdown" class="collapse list-unstyled">
        <li><a href="{{ url('add_tenant') }}">Add Tenant</a></li>
        <li><a href="{{ url('view_tenant') }}">View Tenants</a></li>
      </ul>
    </li>

    <li>
      <a href="#landlordDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-briefcase"></i> Landlords
      </a>
      <ul id="landlordDropdown" class="collapse list-unstyled">
        <li><a href="{{ url('add_landlord') }}">Add Landlord</a></li>
        <li><a href="{{ url('view_landlord') }}">View Landlords</a></li>
      </ul>
    </li>
  </ul>
</nav>
 --}}


 <!-- Sidebar Navigation-->
<nav id="sidebar">
  <!-- Sidebar Header-->
  <div class="sidebar-header d-flex align-items-center">
    <div class="avatar">
      <img src="{{ asset('admincss/img/avatar-6.jpg') }}" alt="..." class="img-fluid rounded-circle">
    </div>
    <div class="title">
      <h1 class="h5">{{ Auth::user()->name }}</h1>
      <p>{{ Auth::user()->usertype ?? 'admin' }}</p>
    </div>
  </div>

  <!-- Sidebar Navigation Menus -->
  <span class="heading">Main</span>
  <ul class="list-unstyled">
    <li class="active">
      <a href="{{ url('admin/dashboard') }}">
        <i class="icon-home"></i> Home
      </a>
    </li>

    <li>
      <a href="{{ url('view_category') }}">
        <i class="icon-grid"></i> Category
      </a>
    </li>

    <li>
      <a href="#houseDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-windows"></i> Houses
      </a>
      <ul id="houseDropdown" class="collapse list-unstyled">
        <li><a href="{{ url('add_house') }}">Add House</a></li>
        <li><a href="{{ url('view_house') }}">View House</a></li>
      </ul>
    </li>

    <li>
      <a href="#userDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-user"></i> Users
      </a>
      <ul id="userDropdown" class="collapse list-unstyled">
        <li><a href="{{ url('add_user') }}">Add User</a></li>
        <li><a href="{{ url('view_user') }}">View Users</a></li>
      </ul>
    </li>

    <li>
      <a href="#tenantDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-people"></i> Tenants
      </a>
      <ul id="tenantDropdown" class="collapse list-unstyled">
        <li><a href="{{ url('add_tenant') }}">Add Tenant</a></li>
        <li><a href="{{ url('view_tenant') }}">View Tenants</a></li>
      </ul>
    </li>

    <li>
      <a href="#landlordDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-briefcase"></i> Landlords
      </a>
      <ul id="landlordDropdown" class="collapse list-unstyled">
        <li><a href="{{ url('add_landlord') }}">Add Landlord</a></li>
        <li><a href="{{ url('view_landlord') }}">View Landlords</a></li>
      </ul>
    </li>
  </ul>
</nav>
