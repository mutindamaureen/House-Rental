<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Contract Termination Details</title>
    <style>
        .signature-box {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
            text-align: center;
        }
        .status-badge-large {
            font-size: 1.2rem;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
@include('admin.header')

<div class="d-flex align-items-stretch">
    @include('admin.sidebar')

    <div class="page-content">
        <div class="page-header">
            <div class="container-fluid">
                <h2 class="mb-4">Contract Termination Details</h2>

                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0"><i class="fa fa-ban me-2"></i>Termination Agreement</h4>
                    </div>
                    <div class="card-body">
                        <!-- Contract Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Contract Information</h5>
                                <p><strong>House:</strong> {{ $contract->house->title }}</p>
                                <p><strong>Location:</strong> {{ $contract->house->location }}</p>
                                <p><strong>Tenant:</strong> {{ $contract->tenant->name }}</p>
                                <p><strong>Landlord:</strong> {{ $contract->landlord->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5>Termination Status</h5>
                                <p>
                                    @if($contract->termination_status === 'pending')
                                        <span class="badge bg-warning status-badge-large">Pending Signatures</span>
                                    @elseif($contract->termination_status === 'partial')
                                        <span class="badge bg-info status-badge-large">Partially Signed</span>
                                    @else
                                        <span class="badge bg-success status-badge-large">Completed</span>
                                    @endif
                                </p>
                                <p><strong>Initiated By:</strong> {{ ucfirst($contract->termination_initiated_by) }}</p>
                                <p><strong>Initiated At:</strong> {{ \Carbon\Carbon::parse($contract->termination_initiated_at)->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        <hr>

                        <!-- Signatures -->
                        <h5 class="mb-3">Termination Signatures</h5>
                        <div class="row">
                            <!-- Landlord Signature -->
                            <div class="col-md-6 mb-3">
                                <div class="signature-box">
                                    <h6>Landlord Signature</h6>
                                    @if($contract->landlord_termination_signature)
                                        <img src="{{ asset('signatures/' . $contract->landlord_termination_signature) }}"
                                             alt="Landlord Signature"
                                             style="max-height: 100px; border: 1px solid #ccc; padding: 5px;">
                                        <p class="text-success mt-2">
                                            <i class="fa fa-check-circle"></i> Signed on {{ \Carbon\Carbon::parse($contract->landlord_signed_termination_at)->format('M d, Y') }}
                                        </p>
                                    @else
                                        <p class="text-muted"><i class="fa fa-clock"></i> Awaiting Signature</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Tenant Signature -->
                            <div class="col-md-6 mb-3">
                                <div class="signature-box">
                                    <h6>Tenant Signature</h6>
                                    @if($contract->tenant_termination_signature)
                                        <img src="{{ asset('signatures/' . $contract->tenant_termination_signature) }}"
                                             alt="Tenant Signature"
                                             style="max-height: 100px; border: 1px solid #ccc; padding: 5px;">
                                        <p class="text-success mt-2">
                                            <i class="fa fa-check-circle"></i> Signed on {{ \Carbon\Carbon::parse($contract->tenant_signed_termination_at)->format('M d, Y') }}
                                        </p>
                                    @else
                                        <p class="text-muted"><i class="fa fa-clock"></i> Awaiting Signature</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($contract->termination_status === 'completed')
                            <div class="alert alert-success mt-3">
                                <i class="fa fa-check-circle me-2"></i>
                                <strong>Termination Completed!</strong> This contract was terminated on {{ \Carbon\Carbon::parse($contract->terminated_at)->format('M d, Y h:i A') }}
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="mt-4">
                            <a href="{{ url('/view_contract') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Back to Contracts
                            </a>

                            @if($contract->termination_status !== 'completed')
                                <form action="{{ url('/contract/cancel-termination/' . $contract->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this termination?')">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fa fa-times me-1"></i> Cancel Termination
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.js')
</body>
</html>
