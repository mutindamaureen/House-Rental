
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

    <li>
      <a href="#maintenanceDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-wrench"></i> Maintenance Requests
      </a>
      <ul id="maintenanceDropdown" class="collapse list-unstyled">
        <li><a href="{{ url('add_maintenancerequest') }}">Add Maintenance Request</a></li>
        <li><a href="{{ url('view_maintenancerequest') }}">View Maintenance Requests</a></li>
      </ul>
    </li>

    <li>
      <a href="#contractDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-doc"></i> Contracts
      </a>
      <ul id="contractDropdown" class="collapse list-unstyled">
        <li><a href="{{ url('add_contract') }}">Add Contract</a></li>
        <li><a href="{{ url('view_contract') }}">View Contracts</a></li>
        <li><a href="{{ url('terminated_contracts') }}">Terminated Contracts</a></li>
      </ul>
    </li>

    <li>
      <a href="#paymentsDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-wrench"></i> Payments
      </a>
      <ul id="paymentsDropdown" class="collapse list-unstyled">
        <li><a href="{{ route('admin.add_payment') }}">Add Payments</a></li>
        <li><a href="{{ route('admin.view_payments') }}">View Payments</a></li>
      </ul>
    </li>
    <li>
      <a href="#invoiceDropdown" aria-expanded="false" data-toggle="collapse">
        <i class="icon-wrench"></i> Invoice
      </a>
      <ul id="invoiceDropdown" class="collapse list-unstyled">
        <li><a href="{{ route('invoices.create') }}">Add Invoice</a></li>
        <li><a href="{{ route('invoices.index') }}">View Invoice</a></li>
      </ul>
    </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/admin/chat') }}">
                                <i class="fa fa-comments"></i>
                                <span>Chat Management</span>
                            </a>
                        </li>

  </ul>
</nav>
