<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Contracts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contract-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .contract-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }
        .badge-signed {
            background-color: #28a745;
            color: #fff;
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-contract me-2"></i>My Contracts</h2>
            <a href="{{ url('/tenant/dashboard') }}" class="btn btn-secondary">
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

        @if($contracts->count() > 0)
            <div class="row">
                @foreach($contracts as $contract)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card contract-card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-home me-2"></i>{{ $contract->house->title }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2">
                                    <strong><i class="fas fa-map-marker-alt me-2 text-danger"></i>Location:</strong><br>
                                    {{ $contract->house->location }}
                                </p>
                                <p class="mb-2">
                                    <strong><i class="fas fa-user-tie me-2 text-info"></i>Landlord:</strong><br>
                                    {{ $contract->landlord->name }}
                                </p>
                                <p class="mb-2">
                                    <strong><i class="fas fa-calendar me-2 text-warning"></i>Created:</strong><br>
                                    {{ $contract->created_at->format('M d, Y') }}
                                </p>
                                <p class="mb-0">
                                    <strong><i class="fas fa-info-circle me-2"></i>Status:</strong><br>
                                    @if($contract->status === 'signed')
                                        <span class="badge badge-signed">
                                            <i class="fas fa-check-circle me-1"></i>Signed
                                        </span>
                                    @else
                                        <span class="badge badge-pending">
                                            <i class="fas fa-clock me-1"></i>Pending Signature
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="card-footer bg-light">
                                <a href="{{ route('tenant.contract.details', $contract->id) }}" class="btn btn-primary btn-sm w-100 mb-2">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>

                                @if($contract->status === 'signed' && !$contract->termination_status)
                                    <form action="{{ route('tenant.contract.request-termination', $contract->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to request contract termination?')">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm w-100">
                                            <i class="fas fa-ban me-1"></i>Request Termination
                                        </button>
                                    </form>
                                @elseif(in_array($contract->termination_status, ['pending', 'partial']))
                                    <a href="{{ route('tenant.termination.details', $contract->id) }}" class="btn btn-danger btn-sm w-100">
                                        <i class="fas fa-file-signature me-1"></i>Sign Termination
                                    </a>
                                @endif
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
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h4>No Contracts Found</h4>
                <p>You don't have any contracts yet. Contact your landlord for more information.</p>
            </div>
        @endif
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
