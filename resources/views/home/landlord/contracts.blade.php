{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Contracts - Landlord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contract-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border-left: 4px solid #0d6efd;
        }
        .contract-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-contract text-primary me-2"></i>My Contracts</h2>
            <a href="{{ route('landlord.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
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
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-file-signature fa-2x text-primary mb-2"></i>
                        <h3 class="mb-0">{{ $contracts->where('status', 'signed')->count() }}</h3>
                        <small class="text-muted">Active Contracts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-hourglass-half fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0">{{ $contracts->where('status', 'pending')->count() }}</h3>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-ban fa-2x text-danger mb-2"></i>
                        <h3 class="mb-0">{{ $contracts->where('status', 'terminated')->count() }}</h3>
                        <small class="text-muted">Terminated</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-file-contract fa-2x text-info mb-2"></i>
                        <h3 class="mb-0">{{ $contracts->total() }}</h3>
                        <small class="text-muted">Total Contracts</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contracts List -->
        @if($contracts->count() > 0)
            <div class="row">
                @foreach($contracts as $contract)
                    <div class="col-lg-6 mb-4">
                        <div class="card contract-card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">
                                            <i class="fas fa-home text-primary me-2"></i>{{ $contract->house->title }}
                                        </h5>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $contract->house->location }}
                                        </p>
                                    </div>
                                    @if($contract->status === 'signed')
                                        <span class="badge bg-success status-badge">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                    @elseif($contract->status === 'pending')
                                        <span class="badge bg-warning status-badge">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @elseif($contract->status === 'terminated')
                                        <span class="badge bg-danger status-badge">
                                            <i class="fas fa-ban me-1"></i>Terminated
                                        </span>
                                    @else
                                        <span class="badge bg-secondary status-badge">{{ ucfirst($contract->status) }}</span>
                                    @endif
                                </div>

                                <hr>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tenant</small>
                                        <strong>{{ $contract->tenant->name }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Monthly Rent</small>
                                        <strong class="text-success">KSh {{ number_format($contract->house->price, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Start Date</small>
                                        <strong>{{ \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">End Date</small>
                                        <strong>{{ \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') }}</strong>
                                    </div>
                                </div>

                                @if($contract->termination_status)
                                    <div class="alert alert-warning py-2 mb-3">
                                        <small>
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Termination Status: <strong>{{ ucfirst($contract->termination_status) }}</strong>
                                        </small>
                                    </div>
                                @endif

                                <div class="d-flex gap-2">
                                    <a href="{{ route('landlord.contract.details', $contract->id) }}"
                                       class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>

                                    @if($contract->status === 'signed' && !$contract->termination_status)
                                        <button type="button"
                                                class="btn btn-danger btn-sm"
                                                onclick="confirmTermination({{ $contract->id }})">
                                            <i class="fas fa-times-circle me-1"></i>Terminate
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $contracts->links() }}
            </div>
        @else
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <h5>No Contracts Found</h5>
                <p class="mb-0">You don't have any contracts yet.</p>
            </div>
        @endif
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmTermination(contractId) {
            if (confirm('Are you sure you want to request termination for this contract? This will notify the tenant and require both parties to sign.')) {
                // Create and submit form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/landlord/contracts/${contractId}/request-termination`;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;

                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Contracts - Landlord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contract-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border-left: 4px solid #0d6efd;
        }
        .contract-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-contract text-primary me-2"></i>My Contracts</h2>
            <a href="{{ route('landlord.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
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
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-file-signature fa-2x text-primary mb-2"></i>
                        <h3 class="mb-0">{{ $contracts->where('status', 'signed')->count() }}</h3>
                        <small class="text-muted">Active Contracts</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-hourglass-half fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0">{{ $contracts->where('status', 'pending')->count() }}</h3>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-ban fa-2x text-danger mb-2"></i>
                        <h3 class="mb-0">{{ $contracts->where('status', 'terminated')->count() }}</h3>
                        <small class="text-muted">Terminated</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-file-contract fa-2x text-info mb-2"></i>
                        <h3 class="mb-0">{{ $contracts->total() }}</h3>
                        <small class="text-muted">Total Contracts</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contracts List -->
        @if($contracts->count() > 0)
            <div class="row">
                @foreach($contracts as $contract)
                    <div class="col-lg-6 mb-4">
                        <div class="card contract-card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title mb-1">
                                            <i class="fas fa-home text-primary me-2"></i>{{ $contract->house->title }}
                                        </h5>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $contract->house->location }}
                                        </p>
                                    </div>
                                    @if($contract->status === 'signed')
                                        <span class="badge bg-success status-badge">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                    @elseif($contract->status === 'pending')
                                        <span class="badge bg-warning status-badge">
                                            <i class="fas fa-clock me-1"></i>Pending
                                        </span>
                                    @elseif($contract->status === 'terminated')
                                        <span class="badge bg-danger status-badge">
                                            <i class="fas fa-ban me-1"></i>Terminated
                                        </span>
                                    @else
                                        <span class="badge bg-secondary status-badge">{{ ucfirst($contract->status) }}</span>
                                    @endif
                                </div>

                                <hr>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tenant</small>
                                        <strong>{{ $contract->tenant->name }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Monthly Rent</small>
                                        <strong class="text-success">KSh {{ number_format($contract->house->price, 2) }}</strong>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Start Date</small>
                                        <strong>{{ \Carbon\Carbon::parse($contract->start_date)->format('M d, Y') }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">End Date</small>
                                        <strong>{{ \Carbon\Carbon::parse($contract->end_date)->format('M d, Y') }}</strong>
                                    </div>
                                </div>

                                @if($contract->termination_status)
                                    <div class="alert alert-warning py-2 mb-3">
                                        <small>
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Termination Status: <strong>{{ ucfirst($contract->termination_status) }}</strong>
                                        </small>
                                    </div>
                                @endif

                                <div class="d-flex gap-2">
                                    <a href="{{ route('landlord.contract.details', $contract->id) }}"
                                       class="btn btn-primary btn-sm flex-grow-1">
                                        <i class="fas fa-eye me-1"></i>View Details
                                    </a>

                                    @if($contract->status === 'signed' && !$contract->termination_status)
                                        <button type="button"
                                                class="btn btn-danger btn-sm"
                                                onclick="confirmTermination('{{ route('landlord.contract.request-termination', $contract->id) }}')">
                                            <i class="fas fa-times-circle me-1"></i>Terminate
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $contracts->links() }}
            </div>
        @else
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <h5>No Contracts Found</h5>
                <p class="mb-0">You don't have any contracts yet.</p>
            </div>
        @endif
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmTermination(actionUrl) {
            if (confirm('Are you sure you want to request termination for this contract? This will notify the tenant and require both parties to sign.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = actionUrl;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;

                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
