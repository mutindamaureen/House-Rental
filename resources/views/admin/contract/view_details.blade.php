{{--
<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contract Details</title>

    <style>
        /* Page layout */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 950px;
            margin: 0 auto;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            background-color: #fff;
            padding: 30px;
        }

        /* Detail rows */
        .detail-row {
            display: flex;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            width: 220px;
            font-weight: 600;
            color: #495057;
        }

        .detail-value {
            flex: 1;
            color: #212529;
        }

        .detail-value strong {
            color: #000;
        }

        /* Status badges */
        .status-badge {
            padding: 6px 14px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .status-signed {
            background-color: #28a745;
            color: #fff;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-terminated {
            background-color: #dc3545;
            color: #fff;
        }

        .status-termination-pending {
            background-color: #ff9800;
            color: #fff;
        }

        /* Termination alert */
        .termination-alert {
            background-color: #fff3cd;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .termination-alert strong {
            color: #856404;
        }

        /* Signature and document preview */
        .signature-preview {
            max-width: 280px;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 6px;
            background-color: #f8f9fa;
            margin-top: 10px;
        }

        .fa-file-pdf {
            font-size: 1.2rem;
            margin-right: 5px;
        }

        /* Action buttons section */
        .action-buttons {
            margin-top: 35px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
        }

        .action-buttons .btn {
            margin: 5px;
            border-radius: 6px;
        }

        /* Back button alignment on smaller screens */
        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                width: 100%;
                margin-bottom: 8px;
            }
            .action-buttons {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3>Contract Details - #{{ $contract->id }}</h3>
                    </div>

                    <div class="card-body">
                        <!-- Termination Status Alert -->
                        @if($contract->termination_status)
                        <div class="termination-alert">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Termination Status:</strong> {{ ucfirst($contract->termination_status) }}
                            @if($contract->termination_initiated_at)
                                <br><small>Initiated on: {{ $contract->termination_initiated_at->format('F d, Y H:i') }}</small>
                                <br><small>Initiated by: {{ ucfirst($contract->termination_initiated_by ?? 'N/A') }}</small>
                            @endif

                            @if($contract->termination_status == 'pending' || $contract->termination_status == 'partial')
                            <div class="mt-2">
                                <small>
                                    @if($contract->landlord_signed_termination_at)
                                        ✅ Landlord signed on {{ $contract->landlord_signed_termination_at->format('M d, Y H:i') }}<br>
                                    @else
                                        ⏳ Waiting for landlord signature<br>
                                    @endif

                                    @if($contract->tenant_signed_termination_at)
                                        ✅ Tenant signed on {{ $contract->tenant_signed_termination_at->format('M d, Y H:i') }}
                                    @else
                                        ⏳ Waiting for tenant signature
                                    @endif
                                </small>
                            </div>
                            @endif

                            @if($contract->termination_status == 'completed')
                            <div class="mt-2">
                                <small class="text-success">
                                    ✅ Contract termination completed
                                </small>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="detail-row">
                            <div class="detail-label">Contract ID:</div>
                            <div class="detail-value">#{{ $contract->id }}</div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Landlord:</div>
                            <div class="detail-value">
                                <strong>{{ $contract->landlord->name ?? 'N/A' }}</strong><br>
                                📧 {{ $contract->landlord->email ?? 'N/A' }}<br>
                                ☎️ {{ $contract->landlord->phone ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Tenant:</div>
                            <div class="detail-value">
                                <strong>{{ $contract->tenant->name ?? 'N/A' }}</strong><br>
                                📧 {{ $contract->tenant->email ?? 'N/A' }}<br>
                                ☎️ {{ $contract->tenant->phone ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">House:</div>
                            <div class="detail-value">
                                <strong>{{ $contract->house->title ?? 'N/A' }}</strong><br>
                                📍 {{ $contract->house->location ?? 'N/A' }}<br>
                                💰 Ksh {{ number_format($contract->house->price ?? 0) }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">
                                @if($contract->status == 'signed')
                                    <span class="status-badge status-signed">Signed</span>
                                @else
                                    <span class="status-badge status-pending">Pending</span>
                                @endif

                                @if($contract->termination_status)
                                    <br>
                                    @if($contract->termination_status == 'completed')
                                        <span class="status-badge status-terminated mt-1">Terminated</span>
                                    @else
                                        <span class="status-badge status-termination-pending mt-1">
                                            {{ ucfirst($contract->termination_status) }} Termination
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Created Date:</div>
                            <div class="detail-value">{{ $contract->created_at->format('F d, Y H:i') }}</div>
                        </div>

                        @if($contract->signed_at)
                        <div class="detail-row">
                            <div class="detail-label">Signed Date:</div>
                            <div class="detail-value">{{ $contract->signed_at->format('F d, Y H:i') }}</div>
                        </div>
                        @endif

                        <div class="detail-row">
                            <div class="detail-label">Contract Document:</div>
                            <div class="detail-value">
                                @if($contract->contract_pdf)
                                    <i class="fa fa-file-pdf text-danger"></i> {{ $contract->contract_pdf }}
                                @else
                                    <span class="text-muted">No document uploaded</span>
                                @endif
                            </div>
                        </div>

                        @if($contract->tenant_signature)
                        <div class="detail-row">
                            <div class="detail-label">Tenant Signature:</div>
                            <div class="detail-value">
                                <img src="{{ asset('signatures/' . $contract->tenant_signature) }}"
                                     alt="Signature" class="signature-preview">
                            </div>
                        </div>
                        @endif

                        @if($contract->landlord_termination_signature)
                        <div class="detail-row">
                            <div class="detail-label">Landlord Termination Signature:</div>
                            <div class="detail-value">
                                <img src="{{ asset('signatures/' . $contract->landlord_termination_signature) }}"
                                     alt="Landlord Termination Signature" class="signature-preview">
                            </div>
                        </div>
                        @endif

                        @if($contract->tenant_termination_signature)
                        <div class="detail-row">
                            <div class="detail-label">Tenant Termination Signature:</div>
                            <div class="detail-value">
                                <img src="{{ asset('signatures/' . $contract->tenant_termination_signature) }}"
                                     alt="Tenant Termination Signature" class="signature-preview">
                            </div>
                        </div>
                        @endif

                        <div class="action-buttons">
                            <a href="{{ url('download_contract', $contract->id) }}" class="btn btn-success">
                                <i class="fa fa-download"></i> Download PDF
                            </a>
                            <a href="{{ url('edit_contract', $contract->id) }}" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit Contract
                            </a>

                            <!-- Terminate Contract Button -->
                            @if($contract->status == 'signed' && !$contract->termination_status)
                            <button type="button" class="btn btn-danger" onclick="confirmTermination()">
                                <i class="fa fa-ban"></i> Terminate Contract
                            </button>
                            @endif

                            <!-- View Termination Details -->
                            @if($contract->termination_status && $contract->termination_status != 'completed')
                            <a href="{{ url('contract/termination', $contract->id) }}" class="btn btn-warning">
                                <i class="fa fa-hourglass-half"></i> View Termination Details
                            </a>
                            @endif

                            <!-- Cancel Termination -->
                            @if($contract->termination_status && $contract->termination_status != 'completed')
                            <button type="button" class="btn btn-secondary" onclick="confirmCancelTermination()">
                                <i class="fa fa-undo"></i> Cancel Termination
                            </button>
                            @endif

                            <a href="{{ url('/view_contract') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>

                            @if(!$contract->termination_status || $contract->termination_status != 'completed')
                            <a href="{{ url('delete_contract', $contract->id) }}"
                               class="btn btn-danger"
                               onclick="return confirm('Are you sure you want to delete this contract?')">
                                <i class="fa fa-trash"></i> Delete Contract
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden forms for POST requests -->
    <form id="terminateForm" action="{{ url('/contract/initiate-termination', $contract->id) }}" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="cancelTerminationForm" action="{{ url('/contract/cancel-termination', $contract->id) }}" method="POST" style="display: none;">
        @csrf
    </form>

    @include('admin.js')

    <script>
        function confirmTermination() {
            if (confirm('Are you sure you want to initiate termination for this contract?\n\nBoth landlord and tenant will be notified and required to sign the termination agreement.')) {
                document.getElementById('terminateForm').submit();
            }
        }

        function confirmCancelTermination() {
            if (confirm('Are you sure you want to cancel the termination process?\n\nThis will revert the contract back to signed status.')) {
                document.getElementById('cancelTerminationForm').submit();
            }
        }
    </script>
</body>
</html> --}}


<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contract Details</title>

    <style>
        /* Page layout */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 950px;
            margin: 0 auto;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            background-color: #fff;
            padding: 30px;
        }

        /* Detail rows */
        .detail-row {
            display: flex;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            width: 220px;
            font-weight: 600;
            color: #495057;
        }

        .detail-value {
            flex: 1;
            color: #212529;
        }

        .detail-value strong {
            color: #000;
        }

        /* Status badges */
        .status-badge {
            padding: 6px 14px;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .status-signed {
            background-color: #28a745;
            color: #fff;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-terminated {
            background-color: #dc3545;
            color: #fff;
        }

        .status-termination-pending {
            background-color: #ff9800;
            color: #fff;
        }

        /* Termination alert */
        .termination-alert {
            background-color: #fff3cd;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .termination-alert strong {
            color: #856404;
        }

        /* Signature and document preview */
        .signature-preview {
            max-width: 280px;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 6px;
            background-color: #f8f9fa;
            margin-top: 10px;
        }

        .fa-file-pdf {
            font-size: 1.2rem;
            margin-right: 5px;
        }

        /* Action buttons section */
        .action-buttons {
            margin-top: 35px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            text-align: center;
        }

        .action-buttons .btn {
            margin: 5px;
            border-radius: 6px;
        }

        /* Back button alignment on smaller screens */
        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                width: 100%;
                margin-bottom: 8px;
            }
            .action-buttons {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3>Contract Details - #{{ $contract->id }}</h3>
                    </div>

                    <div class="card-body">
                        <!-- Termination Status Alert -->
                        @if($contract->termination_status)
                        <div class="termination-alert">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Termination Status:</strong> {{ ucfirst($contract->termination_status) }}
                            @if($contract->termination_initiated_at)
                                <br><small>Initiated on: {{ is_string($contract->termination_initiated_at) ? \Carbon\Carbon::parse($contract->termination_initiated_at)->format('F d, Y H:i') : $contract->termination_initiated_at->format('F d, Y H:i') }}</small>
                                <br><small>Initiated by: {{ ucfirst($contract->termination_initiated_by ?? 'N/A') }}</small>
                            @endif

                            @if($contract->termination_status == 'pending' || $contract->termination_status == 'partial')
                            <div class="mt-2">
                                <small>
                                    @if($contract->landlord_signed_termination_at)
                                        ✅ Landlord signed on {{ is_string($contract->landlord_signed_termination_at) ? \Carbon\Carbon::parse($contract->landlord_signed_termination_at)->format('M d, Y H:i') : $contract->landlord_signed_termination_at->format('M d, Y H:i') }}<br>
                                    @else
                                        ⏳ Waiting for landlord signature<br>
                                    @endif

                                    @if($contract->tenant_signed_termination_at)
                                        ✅ Tenant signed on {{ is_string($contract->tenant_signed_termination_at) ? \Carbon\Carbon::parse($contract->tenant_signed_termination_at)->format('M d, Y H:i') : $contract->tenant_signed_termination_at->format('M d, Y H:i') }}
                                    @else
                                        ⏳ Waiting for tenant signature
                                    @endif
                                </small>
                            </div>
                            @endif

                            @if($contract->termination_status == 'completed')
                            <div class="mt-2">
                                <small class="text-success">
                                    ✅ Contract termination completed
                                </small>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="detail-row">
                            <div class="detail-label">Contract ID:</div>
                            <div class="detail-value">#{{ $contract->id }}</div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Landlord:</div>
                            <div class="detail-value">
                                <strong>{{ $contract->landlord->name ?? 'N/A' }}</strong><br>
                                📧 {{ $contract->landlord->email ?? 'N/A' }}<br>
                                ☎️ {{ $contract->landlord->phone ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Tenant:</div>
                            <div class="detail-value">
                                <strong>{{ $contract->tenant->name ?? 'N/A' }}</strong><br>
                                📧 {{ $contract->tenant->email ?? 'N/A' }}<br>
                                ☎️ {{ $contract->tenant->phone ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">House:</div>
                            <div class="detail-value">
                                <strong>{{ $contract->house->title ?? 'N/A' }}</strong><br>
                                📍 {{ $contract->house->location ?? 'N/A' }}<br>
                                💰 Ksh {{ number_format($contract->house->price ?? 0) }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">
                                @if($contract->status == 'signed')
                                    <span class="status-badge status-signed">Signed</span>
                                @else
                                    <span class="status-badge status-pending">Pending</span>
                                @endif

                                @if($contract->termination_status)
                                    <br>
                                    @if($contract->termination_status == 'completed')
                                        <span class="status-badge status-terminated mt-1">Terminated</span>
                                    @else
                                        <span class="status-badge status-termination-pending mt-1">
                                            {{ ucfirst($contract->termination_status) }} Termination
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Created Date:</div>
                            <div class="detail-value">{{ $contract->created_at->format('F d, Y H:i') }}</div>
                        </div>

                        @if($contract->signed_at)
                        <div class="detail-row">
                            <div class="detail-label">Signed Date:</div>
                            <div class="detail-value">{{ is_string($contract->signed_at) ? \Carbon\Carbon::parse($contract->signed_at)->format('F d, Y H:i') : $contract->signed_at->format('F d, Y H:i') }}</div>
                        </div>
                        @endif

                        <div class="detail-row">
                            <div class="detail-label">Contract Document:</div>
                            <div class="detail-value">
                                @if($contract->contract_pdf)
                                    <i class="fa fa-file-pdf text-danger"></i> {{ $contract->contract_pdf }}
                                @else
                                    <span class="text-muted">No document uploaded</span>
                                @endif
                            </div>
                        </div>

                        @if($contract->tenant_signature)
                        <div class="detail-row">
                            <div class="detail-label">Tenant Signature:</div>
                            <div class="detail-value">
                                <img src="{{ asset('signatures/' . $contract->tenant_signature) }}"
                                     alt="Signature" class="signature-preview">
                            </div>
                        </div>
                        @endif

                        @if($contract->landlord_termination_signature)
                        <div class="detail-row">
                            <div class="detail-label">Landlord Termination Signature:</div>
                            <div class="detail-value">
                                <img src="{{ asset('signatures/' . $contract->landlord_termination_signature) }}"
                                     alt="Landlord Termination Signature" class="signature-preview">
                            </div>
                        </div>
                        @endif

                        @if($contract->tenant_termination_signature)
                        <div class="detail-row">
                            <div class="detail-label">Tenant Termination Signature:</div>
                            <div class="detail-value">
                                <img src="{{ asset('signatures/' . $contract->tenant_termination_signature) }}"
                                     alt="Tenant Termination Signature" class="signature-preview">
                            </div>
                        </div>
                        @endif

                        <div class="action-buttons">
                            <a href="{{ url('download_contract', $contract->id) }}" class="btn btn-success">
                                <i class="fa fa-download"></i> Download PDF
                            </a>
                            <a href="{{ url('edit_contract', $contract->id) }}" class="btn btn-warning">
                                <i class="fa fa-edit"></i> Edit Contract
                            </a>

                            <!-- Terminate Contract Button -->
                            @if($contract->status == 'signed' && !$contract->termination_status)
                            <button type="button" class="btn btn-danger" onclick="confirmTermination()">
                                <i class="fa fa-ban"></i> Terminate Contract
                            </button>
                            @endif

                            <!-- View Termination Details -->
                            @if($contract->termination_status && $contract->termination_status != 'completed')
                            <a href="{{ url('contract/termination', $contract->id) }}" class="btn btn-warning">
                                <i class="fa fa-hourglass-half"></i> View Termination Details
                            </a>
                            @endif

                            <!-- Cancel Termination -->
                            @if($contract->termination_status && $contract->termination_status != 'completed')
                            <button type="button" class="btn btn-secondary" onclick="confirmCancelTermination()">
                                <i class="fa fa-undo"></i> Cancel Termination
                            </button>
                            @endif

                            <a href="{{ url('/view_contract') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>

                            @if(!$contract->termination_status || $contract->termination_status != 'completed')
                            <a href="{{ url('delete_contract', $contract->id) }}"
                               class="btn btn-danger"
                               onclick="return confirm('Are you sure you want to delete this contract?')">
                                <i class="fa fa-trash"></i> Delete Contract
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden forms for POST requests -->
    <form id="terminateForm" action="{{ url('/contract/initiate-termination', $contract->id) }}" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="cancelTerminationForm" action="{{ url('/contract/cancel-termination', $contract->id) }}" method="POST" style="display: none;">
        @csrf
    </form>

    @include('admin.js')

    <script>
        function confirmTermination() {
            if (confirm('Are you sure you want to initiate termination for this contract?\n\nBoth landlord and tenant will be notified and required to sign the termination agreement.')) {
                document.getElementById('terminateForm').submit();
            }
        }

        function confirmCancelTermination() {
            if (confirm('Are you sure you want to cancel the termination process?\n\nThis will revert the contract back to signed status.')) {
                document.getElementById('cancelTerminationForm').submit();
            }
        }
    </script>
</body>
</html>
