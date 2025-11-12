{{--

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Landlord Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
        }
        .stats-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .stats-card .card-body {
            padding: 25px;
        }
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        .quick-action-btn {
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid #e9ecef;
            background: white;
            text-decoration: none;
            color: #212529;
            display: block;
        }
        .quick-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #667eea;
            color: #667eea;
        }
        .quick-action-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .section-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .table-actions .btn {
            padding: 4px 12px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2"><i class="fas fa-tachometer-alt me-3"></i>Landlord Dashboard</h2>
                    <p class="mb-0 opacity-75">Welcome back, {{ Auth::user()->name }}! Here's your property overview.</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="text-white">
                        <small class="d-block opacity-75">Total Properties</small>
                        <h1 class="mb-0 fw-bold">{{ $houses->count() }}</h1>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Houses</p>
                                <h3 class="mb-0 fw-bold">{{ $houses->count() }}</h3>
                            </div>
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-home"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Active Tenants</p>
                                <h3 class="mb-0 fw-bold">{{ $tenants->where('lease_status', 'active')->count() }}</h3>
                            </div>
                            <div class="stats-icon bg-success bg-opacity-10 text-success">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Pending Requests</p>
                                <h3 class="mb-0 fw-bold">{{ $requests->where('status', 'pending')->count() }}</h3>
                            </div>
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-tools"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Available Houses</p>
                                <h3 class="mb-0 fw-bold">{{ $houses->where('status', 'available')->count() }}</h3>
                            </div>
                            <div class="stats-icon bg-info bg-opacity-10 text-info">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h4 class="mb-4"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h4>
        <div class="row g-3 mb-5">
            <div class="col-md-3">
                <a href="{{ route('landlord.contracts') }}" class="quick-action-btn">
                    <div class="quick-action-icon text-primary">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h6 class="mb-0">View Contracts</h6>
                    <small class="text-muted">Manage tenant contracts</small>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('landlord.termination.contracts') }}" class="quick-action-btn">
                    <div class="quick-action-icon text-danger">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h6 class="mb-0">Terminations</h6>
                    <small class="text-muted">Pending terminations</small>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#maintenance" class="quick-action-btn">
                    <div class="quick-action-icon text-warning">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <h6 class="mb-0">Maintenance</h6>
                    <small class="text-muted">Handle requests</small>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#tenants" class="quick-action-btn">
                    <div class="quick-action-icon text-success">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h6 class="mb-0">Tenants</h6>
                    <small class="text-muted">View all tenants</small>
                </a>
            </div>
        </div>

        <!-- My Houses Section -->
        <div class="card section-card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-home me-2"></i>My Properties</h5>
                <span class="badge bg-white text-primary">{{ $houses->count() }} Total</span>
            </div>
            <div class="card-body">
                @if($houses->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-home fa-4x text-muted mb-3"></i>
                        <h5>No Properties Yet</h5>
                        <p class="text-muted">You haven't registered any properties.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Property Name</th>
                                    <th>Location</th>
                                    <th>Rent (KSh)</th>
                                    <th width="120" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($houses as $index => $house)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $house->title }}</strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $house->location }}
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ number_format($house->price, 2) }}</strong>
                                        </td>
                                        <td class="text-center">
                                            @if($house->status == 'available')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Available
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-user me-1"></i>Occupied
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- My Tenants Section -->
        <div class="card section-card mb-4" id="tenants">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Active Tenants</h5>
                <span class="badge bg-white text-success">{{ $tenants->count() }} Total</span>
            </div>
            <div class="card-body">
                @if($tenants->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h5>No Tenants Yet</h5>
                        <p class="text-muted">You don't have any tenants assigned to your properties.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Tenant Name</th>
                                    <th>Contact</th>
                                    <th>Property</th>
                                    <th>Lease Period</th>
                                    <th>Monthly Rent</th>
                                    <th width="120" class="text-center">Lease Status</th>
                                    <th width="120" class="text-center">Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenants as $index => $tenant)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $tenant->user->name ?? '—' }}</strong>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div><i class="fas fa-envelope text-muted me-1"></i>{{ $tenant->user->email ?? '—' }}</div>
                                                @if($tenant->user->phone ?? false)
                                                    <div><i class="fas fa-phone text-muted me-1"></i>{{ $tenant->user->phone }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $tenant->house->title ?? '—' }}</td>
                                        <td>
                                            <small>
                                                {{ \Carbon\Carbon::parse($tenant->lease_start_date)->format('M d, Y') }}<br>
                                                to {{ \Carbon\Carbon::parse($tenant->lease_end_date)->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <strong class="text-success">KSh {{ number_format($tenant->total_rent, 2) }}</strong>
                                            <div class="small text-muted">
                                                Rent: {{ number_format($tenant->rent, 2) }} +
                                                Utils: {{ number_format($tenant->utilities, 2) }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $leaseStatusClass = match($tenant->lease_status) {
                                                    'active' => 'success',
                                                    'expired' => 'danger',
                                                    'pending' => 'warning',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $leaseStatusClass }}">
                                                {{ ucfirst($tenant->lease_status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $paymentStatusClass = match($tenant->payment_status) {
                                                    'paid' => 'success',
                                                    'pending' => 'warning',
                                                    'overdue' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $paymentStatusClass }}">
                                                {{ ucfirst($tenant->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Maintenance Requests Section -->
        <div class="card section-card mb-4" id="maintenance">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Maintenance Requests</h5>
                <span class="badge bg-dark">{{ $requests->count() }} Total</span>
            </div>
            <div class="card-body">
                @if($requests->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                        <h5>No Maintenance Requests</h5>
                        <p class="text-muted">You have no pending maintenance requests.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Tenant</th>
                                    <th>Property</th>
                                    <th>Subject</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th width="140" class="text-center">Status</th>
                                    <th width="180">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $index => $req)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $req->tenant->name ?? 'Unknown' }}</strong>
                                        </td>
                                        <td>{{ $req->house_name ?? '—' }}</td>
                                        <td><strong>{{ $req->subject }}</strong></td>
                                        <td>
                                            <small>{{ Str::limit($req->description ?? '—', 40) }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $req->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = match($req->status) {
                                                    'pending' => 'warning',
                                                    'in_progress' => 'info',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST"
                                            action="{{ route('landlord.maintenance.update', $req->id) }}"
                                            class="d-inline">
                                                @csrf
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="in_progress" {{ $req->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="completed" {{ $req->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $req->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Landlord Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
        }
        .stats-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .stats-card .card-body {
            padding: 25px;
        }
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        .quick-action-btn {
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            border: 2px solid #e9ecef;
            background: white;
            text-decoration: none;
            color: #212529;
            display: block;
        }
        .quick-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #667eea;
            color: #667eea;
        }
        .quick-action-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .section-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .table-actions .btn {
            padding: 4px 12px;
            font-size: 0.85rem;
        }
        .action-dropdown {
            min-width: 180px;
        }
        .dropdown-item i {
            width: 20px;
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2"><i class="fas fa-tachometer-alt me-3"></i>Landlord Dashboard</h2>
                    <p class="mb-0 opacity-75">Welcome back, {{ Auth::user()->name }}! Here's your property overview.</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="text-white">
                        <small class="d-block opacity-75">Total Properties</small>
                        <h1 class="mb-0 fw-bold">{{ $houses->count() }}</h1>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Houses</p>
                                <h3 class="mb-0 fw-bold">{{ $houses->count() }}</h3>
                            </div>
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-home"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Active Tenants</p>
                                <h3 class="mb-0 fw-bold">{{ $tenants->where('lease_status', 'active')->count() }}</h3>
                            </div>
                            <div class="stats-icon bg-success bg-opacity-10 text-success">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Pending Requests</p>
                                <h3 class="mb-0 fw-bold">{{ $requests->where('status', 'pending')->count() }}</h3>
                            </div>
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-tools"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Available Houses</p>
                                <h3 class="mb-0 fw-bold">{{ $houses->where('status', 'available')->count() }}</h3>
                            </div>
                            <div class="stats-icon bg-info bg-opacity-10 text-info">
                                <i class="fas fa-building"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h4 class="mb-4"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h4>
        <div class="row g-3 mb-5">
            <div class="col-md-3">
                <a href="{{ route('landlord.contracts') }}" class="quick-action-btn">
                    <div class="quick-action-icon text-primary">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h6 class="mb-0">View Contracts</h6>
                    <small class="text-muted">Manage tenant contracts</small>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('landlord.termination.contracts') }}" class="quick-action-btn">
                    <div class="quick-action-icon text-danger">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h6 class="mb-0">Terminations</h6>
                    <small class="text-muted">Pending terminations</small>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#maintenance" class="quick-action-btn">
                    <div class="quick-action-icon text-warning">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <h6 class="mb-0">Maintenance</h6>
                    <small class="text-muted">Handle requests</small>
                </a>
            </div>
            <div class="col-md-3">
                <a href="#tenants" class="quick-action-btn">
                    <div class="quick-action-icon text-success">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h6 class="mb-0">Tenants</h6>
                    <small class="text-muted">View all tenants</small>
                </a>
            </div>
        </div>

        <!-- My Houses Section -->
        <div class="card section-card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-home me-2"></i>My Properties</h5>
                <span class="badge bg-white text-primary">{{ $houses->count() }} Total</span>
            </div>
            <div class="card-body">
                @if($houses->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-home fa-4x text-muted mb-3"></i>
                        <h5>No Properties Yet</h5>
                        <p class="text-muted">You haven't registered any properties.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Property Name</th>
                                    <th>Location</th>
                                    <th>Rent (KSh)</th>
                                    <th width="120" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($houses as $index => $house)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $house->title }}</strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $house->location }}
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ number_format($house->price, 2) }}</strong>
                                        </td>
                                        <td class="text-center">
                                            @if($house->status == 'available')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Available
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-user me-1"></i>Occupied
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- My Tenants Section -->
        <div class="card section-card mb-4" id="tenants">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Active Tenants</h5>
                <span class="badge bg-white text-success">{{ $tenants->count() }} Total</span>
            </div>
            <div class="card-body">
                @if($tenants->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-4x text-muted mb-3"></i>
                        <h5>No Tenants Yet</h5>
                        <p class="text-muted">You don't have any tenants assigned to your properties.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Tenant Name</th>
                                    <th>Contact</th>
                                    <th>Property</th>
                                    <th>Lease Period</th>
                                    <th>Monthly Rent</th>
                                    <th width="120" class="text-center">Lease Status</th>
                                    <th width="120" class="text-center">Payment</th>
                                    <th width="160" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenants as $index => $tenant)
                                    @php
                                        // Check if contract exists for this tenant
                                        $contract = \App\Models\Contract::where('tenant_id', $tenant->user_id)
                                            ->where('landlord_id', Auth::id())
                                            ->whereIn('status', ['pending', 'signed'])
                                            ->first();

                                        $hasActiveContract = $contract && $contract->status === 'signed';
                                        $hasPendingContract = $contract && $contract->status === 'pending';
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $tenant->user->name ?? '—' }}</strong>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <div><i class="fas fa-envelope text-muted me-1"></i>{{ $tenant->user->email ?? '—' }}</div>
                                                @if($tenant->user->phone ?? false)
                                                    <div><i class="fas fa-phone text-muted me-1"></i>{{ $tenant->user->phone }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $tenant->house->title ?? '—' }}</td>
                                        <td>
                                            <small>
                                                {{ \Carbon\Carbon::parse($tenant->lease_start_date)->format('M d, Y') }}<br>
                                                to {{ \Carbon\Carbon::parse($tenant->lease_end_date)->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <strong class="text-success">KSh {{ number_format($tenant->total_rent, 2) }}</strong>
                                            <div class="small text-muted">
                                                Rent: {{ number_format($tenant->rent, 2) }} +
                                                Utils: {{ number_format($tenant->utilities, 2) }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $leaseStatusClass = match($tenant->lease_status) {
                                                    'active' => 'success',
                                                    'expired' => 'danger',
                                                    'pending' => 'warning',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $leaseStatusClass }}">
                                                {{ ucfirst($tenant->lease_status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $paymentStatusClass = match($tenant->payment_status) {
                                                    'paid' => 'success',
                                                    'pending' => 'warning',
                                                    'overdue' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $paymentStatusClass }}">
                                                {{ ucfirst($tenant->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                @if(!$contract)
                                                    <!-- No contract exists - show create button -->
                                                    <a href="{{ route('landlord.contract.create', $tenant->user_id) }}"
                                                       class="btn btn-sm btn-primary"
                                                       title="Create Contract">
                                                        <i class="fas fa-file-contract me-1"></i>Create Contract
                                                    </a>
                                                @elseif($hasPendingContract)
                                                    <!-- Contract pending tenant signature -->
                                                    <button class="btn btn-sm btn-warning" disabled title="Awaiting tenant signature">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </button>
                                                @elseif($hasActiveContract)
                                                    <!-- Active signed contract - show termination option -->
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-success dropdown-toggle"
                                                                type="button"
                                                                id="contractActions{{ $tenant->id }}"
                                                                data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                            <i class="fas fa-check-circle me-1"></i>Active
                                                        </button>
                                                        <ul class="dropdown-menu action-dropdown" aria-labelledby="contractActions{{ $tenant->id }}">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('landlord.contract.details', $contract->id) }}">
                                                                    <i class="fas fa-eye text-info"></i> View Contract
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('landlord.contract.download', $contract->id) }}">
                                                                    <i class="fas fa-download text-primary"></i> Download PDF
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form method="POST"
                                                                {{-- action="{{ route('landlord.termination.request',$contract->id) }}" --}}
                                                                      onsubmit="return confirm('Are you sure you want to initiate contract termination?');">
                                                                    @csrf
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-ban"></i> Request Termination
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Maintenance Requests Section -->
        <div class="card section-card mb-4" id="maintenance">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Maintenance Requests</h5>
                <span class="badge bg-dark">{{ $requests->count() }} Total</span>
            </div>
            <div class="card-body">
                @if($requests->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                        <h5>No Maintenance Requests</h5>
                        <p class="text-muted">You have no pending maintenance requests.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Tenant</th>
                                    <th>Property</th>
                                    <th>Subject</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th width="140" class="text-center">Status</th>
                                    <th width="180">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $index => $req)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $req->tenant->name ?? 'Unknown' }}</strong>
                                        </td>
                                        <td>{{ $req->house_name ?? '—' }}</td>
                                        <td><strong>{{ $req->subject }}</strong></td>
                                        <td>
                                            <small>{{ Str::limit($req->description ?? '—', 40) }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $req->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = match($req->status) {
                                                    'pending' => 'warning',
                                                    'in_progress' => 'info',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST"
                                            action="{{ route('landlord.maintenance.update', $req->id) }}"
                                            class="d-inline">
                                                @csrf
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                    <option value="pending" {{ $req->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="in_progress" {{ $req->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                    <option value="completed" {{ $req->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $req->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
