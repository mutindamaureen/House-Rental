<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contract Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contract-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .detail-card {
            border-left: 4px solid #667eea;
        }
        .signature-box {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            background: #f8f9fa;
            text-align: center;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .info-row {
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 1.1rem;
            color: #212529;
            font-weight: 600;
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-contract text-primary me-2"></i>Contract Details</h2>
            <a href="{{ route('landlord.contracts') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Contracts
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

        <!-- Contract Header -->
        <div class="contract-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3><i class="fas fa-home me-2"></i>{{ $contract->house->title }}</h3>
                    <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ $contract->house->location }}</p>
                    <p class="mb-0"><i class="fas fa-money-bill-wave me-2"></i><strong>Monthly Rent:</strong> KSh {{ number_format($contract->house->price, 2) }}</p>
                </div>
                <div class="col-md-4 text-end">
                    @if($contract->status === 'signed')
                        <span class="badge bg-success" style="font-size: 1.2rem; padding: 10px 20px;">
                            <i class="fas fa-check-circle me-1"></i>Active
                        </span>
                    @elseif($contract->status === 'pending')
                        <span class="badge bg-warning text-dark" style="font-size: 1.2rem; padding: 10px 20px;">
                            <i class="fas fa-clock me-1"></i>Pending
                        </span>
                    @elseif($contract->status === 'terminated')
                        <span class="badge bg-danger" style="font-size: 1.2rem; padding: 10px 20px;">
                            <i class="fas fa-ban me-1"></i>Terminated
                        </span>
                    @else
                        <span class="badge bg-secondary" style="font-size: 1.2rem; padding: 10px 20px;">
                            {{ ucfirst($contract->status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-5">
                <!-- Contract Information -->
                <div class="card detail-card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Contract Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">Contract Start Date</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($contract->start_date)->format('F d, Y') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Contract End Date</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($contract->end_date)->format('F d, Y') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Contract Duration</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($contract->start_date)->diffInMonths(\Carbon\Carbon::parse($contract->end_date)) }} months</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Contract Status</div>
                            <div class="info-value text-capitalize">{{ str_replace('_', ' ', $contract->status) }}</div>
                        </div>
                        @if($contract->termination_status)
                            <div class="info-row">
                                <div class="info-label">Termination Status</div>
                                <div class="info-value text-danger">{{ ucfirst($contract->termination_status) }}</div>
                            </div>
                        @endif
                        @if($contract->created_at)
                            <div class="info-row">
                                <div class="info-label">Contract Created</div>
                                <div class="info-value">{{ \Carbon\Carbon::parse($contract->created_at)->format('F d, Y') }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tenant Information -->
                <div class="card detail-card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Tenant Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">{{ $contract->tenant->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">
                                <a href="mailto:{{ $contract->tenant->email }}" class="text-decoration-none">
                                    {{ $contract->tenant->email }}
                                </a>
                            </div>
                        </div>
                        @if($contract->tenant->phone)
                            <div class="info-row">
                                <div class="info-label">Phone Number</div>
                                <div class="info-value">
                                    <a href="tel:{{ $contract->tenant->phone }}" class="text-decoration-none">
                                        {{ $contract->tenant->phone }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('landlord.contract.download', $contract->id) }}"
                           class="btn btn-outline-primary w-100 mb-2" target="_blank">
                            <i class="fas fa-download me-1"></i>Download Contract PDF
                        </a>

                        @if($contract->status === 'signed' && !$contract->termination_status)
                            <button type="button"
                                    class="btn btn-danger w-100"
                                    onclick="confirmTermination()">
                                <i class="fas fa-times-circle me-1"></i>Request Contract Termination
                            </button>

                            <form id="terminationForm"
                                  action="{{ route('landlord.contract.request-termination', $contract->id) }}"
                                  method="POST"
                                  style="display: none;">
                                @csrf
                            </form>
                        @endif

                        @if($contract->termination_status && $contract->termination_status !== 'completed')
                            <a href="{{ route('landlord.termination.details', $contract->id) }}"
                               class="btn btn-warning w-100 mt-2">
                                <i class="fas fa-exclamation-triangle me-1"></i>View Termination Progress
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Signatures -->
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-file-signature me-2"></i>Contract Signatures</h5>
                    </div>
                    <div class="card-body">
                        @if($contract->status === 'signed')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="signature-box">
                                        <h6 class="mb-3">Your Signature (Landlord)</h6>
                                        @if($contract->landlord_signature)
                                            <img src="{{ asset('signatures/' . $contract->landlord_signature) }}"
                                                 alt="Landlord Signature"
                                                 class="img-fluid mb-2"
                                                 style="max-height: 100px; border: 1px solid #ccc; padding: 5px;">
                                            <p class="text-success small mb-0">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Signed on {{ \Carbon\Carbon::parse($contract->landlord_signed_at)->format('M d, Y') }}
                                            </p>
                                        @else
                                            <i class="fas fa-signature fa-3x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No signature available</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="signature-box">
                                        <h6 class="mb-3">Tenant Signature</h6>
                                        @if($contract->tenant_signature)
                                            <img src="{{ asset('signatures/' . $contract->tenant_signature) }}"
                                                 alt="Tenant Signature"
                                                 class="img-fluid mb-2"
                                                 style="max-height: 100px; border: 1px solid #ccc; padding: 5px;">
                                            <p class="text-success small mb-0">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Signed on {{ \Carbon\Carbon::parse($contract->tenant_signed_at)->format('M d, Y') }}
                                            </p>
                                        @else
                                            <i class="fas fa-signature fa-3x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No signature available</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Contract Fully Executed</strong><br>
                                Both parties have signed this contract. It is now legally binding.
                            </div>
                        @elseif($contract->status === 'pending')
                            <div class="text-center py-5">
                                <i class="fas fa-hourglass-half fa-4x text-warning mb-3"></i>
                                <h5>Awaiting Signatures</h5>
                                <p class="text-muted">This contract is pending signature from the tenant.</p>
                            </div>
                        @elseif($contract->status === 'terminated')
                            <div class="text-center py-5">
                                <i class="fas fa-ban fa-4x text-danger mb-3"></i>
                                <h5>Contract Terminated</h5>
                                <p class="text-muted">This contract was terminated on {{ \Carbon\Carbon::parse($contract->terminated_at)->format('F d, Y') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Termination Signatures (if applicable) -->
                @if($contract->termination_status === 'completed')
                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-file-signature me-2"></i>Termination Signatures</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="signature-box">
                                        <h6 class="mb-3">Your Termination Signature</h6>
                                        @if($contract->landlord_termination_signature)
                                            <img src="{{ asset('signatures/' . $contract->landlord_termination_signature) }}"
                                                 alt="Landlord Termination Signature"
                                                 class="img-fluid mb-2"
                                                 style="max-height: 100px; border: 1px solid #ccc; padding: 5px;">
                                            <p class="text-success small mb-0">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Signed on {{ \Carbon\Carbon::parse($contract->landlord_signed_termination_at)->format('M d, Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="signature-box">
                                        <h6 class="mb-3">Tenant Termination Signature</h6>
                                        @if($contract->tenant_termination_signature)
                                            <img src="{{ asset('signatures/' . $contract->tenant_termination_signature) }}"
                                                 alt="Tenant Termination Signature"
                                                 class="img-fluid mb-2"
                                                 style="max-height: 100px; border: 1px solid #ccc; padding: 5px;">
                                            <p class="text-success small mb-0">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Signed on {{ \Carbon\Carbon::parse($contract->tenant_signed_termination_at)->format('M d, Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Termination Agreement Completed</strong><br>
                                Both parties have signed the termination agreement. The contract is now officially terminated.
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmTermination() {
            if (confirm('Are you sure you want to request termination for this contract?\n\nThis will:\n• Notify the tenant immediately\n• Require both parties to sign the termination agreement\n• Begin the contract termination process\n\nThis action cannot be undone.')) {
                document.getElementById('terminationForm').submit();
            }
        }
    </script>
</body>
</html>
