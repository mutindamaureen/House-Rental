<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tenant Dashboard</title>
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
            height: 100%;
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
        .property-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 12px;
            padding: 25px;
        }
        .lease-warning {
            border-left: 4px solid #ffc107;
            background: #fff3cd;
        }
        .lease-danger {
            border-left: 4px solid #dc3545;
            background: #f8d7da;
        }
        .lease-info {
            border-left: 4px solid #0dcaf0;
            background: #cff4fc;
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
                    <h2 class="mb-2"><i class="fas fa-tachometer-alt me-3"></i>Tenant Dashboard</h2>
                    <p class="mb-0 opacity-75">Welcome back, {{ Auth::user()->name }}! Here's your rental overview.</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="text-white">
                        <small class="d-block opacity-75">Lease Status</small>
                        <h3 class="mb-0 fw-bold text-uppercase">{{ ucfirst($tenant->lease_status ?? 'Active') }}</h3>
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

        @if(session('whatsapp_url'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <h5 class="alert-heading">
                    <i class="fab fa-whatsapp me-2"></i>Request Submitted!
                </h5>
                <p>Your maintenance request has been saved. You can also notify your landlord directly via WhatsApp:</p>
                <a href="{{ session('whatsapp_url') }}" target="_blank" class="btn btn-success">
                    <i class="fab fa-whatsapp me-1"></i>Send to Landlord via WhatsApp
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Lease Status Alert -->
        @if(!is_null($daysRemaining))
            @if($daysRemaining > 0 && $daysRemaining <= 30)
                <div class="alert lease-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Lease Expiring Soon!</strong> Your lease expires in <strong>{{ $daysRemaining }} days</strong> on {{ \Carbon\Carbon::parse($tenant->lease_end_date)->format('F d, Y') }}.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif($daysRemaining == 0)
                <div class="alert lease-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Lease Ends Today!</strong> Please contact your landlord regarding lease renewal.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif($daysRemaining < 0)
                <div class="alert lease-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Lease Expired!</strong> Your lease expired {{ abs($daysRemaining) }} days ago. Please contact your landlord immediately.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endif

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card stats-card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Monthly Rent</p>
                                <h4 class="mb-0 fw-bold">KSh {{ number_format($tenant->rent, 0) }}</h4>
                            </div>
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-money-bill-wave"></i>
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
                                <p class="text-muted mb-1 small">Total Paid</p>
                                <h4 class="mb-0 fw-bold">KSh {{ number_format($totalPaid ?? 0, 0) }}</h4>
                            </div>
                            <div class="stats-icon bg-success bg-opacity-10 text-success">
                                <i class="fas fa-check-circle"></i>
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
                                <p class="text-muted mb-1 small">Pending Invoices</p>
                                <h4 class="mb-0 fw-bold">{{ $pendingInvoices ?? 0 }}</h4>
                            </div>
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-file-invoice"></i>
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
                                <p class="text-muted mb-1 small">Overdue</p>
                                <h4 class="mb-0 fw-bold">{{ $overdueInvoices ?? 0 }}</h4>
                            </div>
                            <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h4 class="mb-4"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h4>
        <div class="row g-3 mb-5">
            <div class="col-md-2">
                <a href="{{ route('tenant.invoices') }}" class="quick-action-btn">
                    <div class="quick-action-icon text-primary">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h6 class="mb-0">Invoices</h6>
                    <small class="text-muted">View bills</small>
                </a>
            </div>
            <div class="col-md-2">
                <a href="{{ route('tenant.payments') }}" class="quick-action-btn">
                    <div class="quick-action-icon text-success">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h6 class="mb-0">Payments</h6>
                    <small class="text-muted">Payment history</small>
                </a>
            </div>
            <div class="col-md-2">
                <a href="{{ route('tenant.contracts') }}" class="quick-action-btn">
                    <div class="quick-action-icon text-info">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h6 class="mb-0">Contracts</h6>
                    <small class="text-muted">View & sign</small>
                </a>
            </div>
            <div class="col-md-2">
                <a href="{{ route('tenant.termination.contracts') }}" class="quick-action-btn">
                    <div class="quick-action-icon text-danger">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h6 class="mb-0">Terminations</h6>
                    <small class="text-muted">End contract</small>
                </a>
            </div>
            <div class="col-md-2">
                <a href="#maintenance" class="quick-action-btn">
                    <div class="quick-action-icon text-warning">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <h6 class="mb-0">Maintenance</h6>
                    <small class="text-muted">Submit request</small>
                </a>
            </div>
            <div class="col-md-2">
                <a href="#property" class="quick-action-btn">
                    <div class="quick-action-icon text-secondary">
                        <i class="fas fa-home"></i>
                    </div>
                    <h6 class="mb-0">My Property</h6>
                    <small class="text-muted">View details</small>
                </a>
            </div>
        </div>

        <!-- Property Details -->
        <div class="card section-card mb-4" id="property">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-home me-2"></i>My Rental Property</h5>
                <span class="badge bg-white text-primary">{{ ucfirst($tenant->house->status) }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="mb-3">{{ $tenant->house->title }}</h4>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Location</small>
                                    <strong><i class="fas fa-map-marker-alt text-danger me-2"></i>{{ $tenant->house->location }}</strong>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Lease Start Date</small>
                                    <strong><i class="fas fa-calendar-alt text-primary me-2"></i>{{ \Carbon\Carbon::parse($tenant->lease_start_date)->format('F d, Y') }}</strong>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Lease End Date</small>
                                    <strong><i class="fas fa-calendar-times text-danger me-2"></i>{{ \Carbon\Carbon::parse($tenant->lease_end_date)->format('F d, Y') }}</strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <small class="text-muted d-block">Days Remaining</small>
                                    @if($daysRemaining > 0)
                                        <h4 class="text-success mb-0">{{ $daysRemaining }} days</h4>
                                    @elseif($daysRemaining == 0)
                                        <h4 class="text-warning mb-0">Ends Today</h4>
                                    @else
                                        <h4 class="text-danger mb-0">Expired</h4>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted d-block">Payment Status</small>
                                    @php
                                        $paymentStatusClass = match($tenant->payment_status ?? 'pending') {
                                            'paid' => 'success',
                                            'pending' => 'warning',
                                            'overdue' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $paymentStatusClass }} fs-6">
                                        {{ ucfirst($tenant->payment_status ?? 'Pending') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="property-card text-center">
                            <div class="mb-3">
                                <i class="fas fa-home fa-3x"></i>
                            </div>
                            <h5 class="mb-2">Monthly Breakdown</h5>
                            <div class="mb-2">
                                <small class="opacity-75">Base Rent</small>
                                <h4 class="mb-0">KSh {{ number_format($tenant->rent, 2) }}</h4>
                            </div>
                            <div class="mb-2">
                                <small class="opacity-75">Utilities</small>
                                <h5 class="mb-0">KSh {{ number_format($tenant->utilities, 2) }}</h5>
                            </div>
                            <hr class="border-white opacity-50 my-3">
                            <div>
                                <small class="opacity-75">Total Due</small>
                                <h3 class="mb-0 fw-bold">KSh {{ number_format($tenant->rent + $tenant->utilities, 2) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Requests Section -->
        <div class="card section-card mb-4" id="maintenance">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Maintenance Requests</h5>
                <span class="badge bg-dark">{{ $requests->count() }} Total</span>
            </div>
            <div class="card-body">
                <!-- Submit New Request Form -->
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary bg-opacity-10">
                        <h6 class="mb-0 text-primary"><i class="fas fa-plus-circle me-2"></i>Submit New Request</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('tenant.requestMaintenance') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="subject" class="form-label fw-semibold">
                                        Issue/Subject <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="subject"
                                           id="subject"
                                           class="form-control @error('subject') is-invalid @enderror"
                                           value="{{ old('subject') }}"
                                           placeholder="e.g., Leaking faucet in kitchen"
                                           required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label fw-semibold">
                                        Description <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="description"
                                              id="description"
                                              class="form-control @error('description') is-invalid @enderror"
                                              rows="4"
                                              placeholder="Please provide detailed information about the maintenance issue..."
                                              required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Request
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Previous Requests -->
                <h6 class="mb-3"><i class="fas fa-history me-2"></i>My Previous Requests</h6>

                @if($requests->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                        <h5>No Maintenance Requests</h5>
                        <p class="text-muted">You haven't submitted any maintenance requests yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Issue</th>
                                    <th>Description</th>
                                    <th width="140" class="text-center">Status</th>
                                    <th width="160">Date Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $index => $req)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $req->subject }}</strong></td>
                                        <td>{{ Str::limit($req->description ?? '—', 60) }}</td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = match($req->status) {
                                                    'pending' => 'warning',
                                                    'in_progress' => 'info',
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                    default => 'secondary'
                                                };
                                                $statusIcon = match($req->status) {
                                                    'pending' => 'clock',
                                                    'in_progress' => 'spinner',
                                                    'completed' => 'check-circle',
                                                    'cancelled' => 'times-circle',
                                                    default => 'question-circle'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">
                                                <i class="fas fa-{{ $statusIcon }} me-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $req->created_at->format('M d, Y') }}<br>{{ $req->created_at->format('h:i A') }}</small>
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
