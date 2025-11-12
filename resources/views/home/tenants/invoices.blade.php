<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Invoices - Tenant Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .stats-card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .stats-card:hover {
            transform: translateY(-3px);
        }
        .invoice-card {
            border: none;
            border-radius: 12px;
            margin-bottom: 15px;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .invoice-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btn-action {
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        .invoice-amount {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .overdue-alert {
            border-left: 4px solid #dc3545;
            background: #f8d7da;
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2"><i class="fas fa-file-invoice-dollar me-3"></i>My Invoices</h2>
                    <p class="mb-0 opacity-75">View and manage your rental invoices</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('tenant.dashboard') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
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

        <!-- Statistics -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Due</p>
                                <h3 class="mb-0 text-warning fw-bold">KSh {{ number_format($totalDue, 2) }}</h3>
                            </div>
                            <div class="text-warning" style="font-size: 2.5rem;">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Paid</p>
                                <h3 class="mb-0 text-success fw-bold">KSh {{ number_format($totalPaid, 2) }}</h3>
                            </div>
                            <div class="text-success" style="font-size: 2.5rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Overdue</p>
                                <h3 class="mb-0 text-danger fw-bold">{{ $overdueCount }}</h3>
                            </div>
                            <div class="text-danger" style="font-size: 2.5rem;">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Invoice History</h5>
            </div>
            <div class="card-body p-4">
                @if($invoices->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-file-invoice fa-4x text-muted mb-3"></i>
                        <h5>No Invoices Found</h5>
                        <p class="text-muted">You don't have any invoices yet.</p>
                    </div>
                @else
                    @foreach($invoices as $invoice)
                        <div class="invoice-card {{ $invoice->status === 'overdue' ? 'overdue-alert' : '' }}">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <div class="text-center">
                                            <div class="mb-2">
                                                @php
                                                    $iconClass = match($invoice->status) {
                                                        'paid' => 'text-success',
                                                        'unpaid' => 'text-warning',
                                                        'overdue' => 'text-danger',
                                                        'cancelled' => 'text-secondary',
                                                        default => 'text-muted'
                                                    };
                                                @endphp
                                                <i class="fas fa-file-invoice fa-3x {{ $iconClass }}"></i>
                                            </div>
                                            <small class="text-muted">{{ $invoice->reference }}</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="mb-1">{{ $invoice->house->title ?? 'N/A' }}</h6>
                                        <p class="text-muted mb-1 small">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $invoice->house->location ?? 'N/A' }}
                                        </p>
                                        <p class="text-muted mb-0 small">{{ Str::limit($invoice->description, 40) }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">Issued Date</small>
                                        <strong>{{ $invoice->issued_date->format('M d, Y') }}</strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">Due Date</small>
                                        <strong class="{{ $invoice->status === 'overdue' ? 'text-danger' : '' }}">
                                            {{ $invoice->due_date->format('M d, Y') }}
                                        </strong>
                                        @if($invoice->status === 'overdue')
                                            <small class="text-danger d-block">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                {{ $invoice->due_date->diffForHumans() }}
                                            </small>
                                        @endif
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Amount</small>
                                            <h4 class="mb-0 fw-bold">{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</h4>
                                        </div>
                                        <div class="mb-2">
                                            @php
                                                $statusClass = match($invoice->status) {
                                                    'paid' => 'success',
                                                    'unpaid' => 'warning',
                                                    'overdue' => 'danger',
                                                    'cancelled' => 'secondary',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="status-badge bg-{{ $statusClass }} text-white">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('tenant.invoice.details', $invoice->id) }}"
                                               class="btn btn-outline-primary btn-action">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            @if($invoice->status === 'unpaid' || $invoice->status === 'overdue')
                                                <button type="button"
                                                        class="btn btn-success btn-action"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#paymentModal{{ $invoice->id }}">
                                                    <i class="fas fa-credit-card me-1"></i>Pay Now
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Modal -->
                        <div class="modal fade" id="paymentModal{{ $invoice->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            <i class="fas fa-credit-card me-2"></i>Payment Options
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('tenant.payment.initiate', $invoice->id) }}">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-info">
                                                <strong>Invoice:</strong> {{ $invoice->reference }}<br>
                                                <strong>Amount:</strong> {{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Select Payment Method</label>
                                                <div class="list-group">
                                                    <label class="list-group-item">
                                                        <input class="form-check-input me-2" type="radio" name="payment_method" value="mpesa" required>
                                                        <i class="fas fa-mobile-alt text-success me-2"></i>M-Pesa
                                                    </label>
                                                    <label class="list-group-item">
                                                        <input class="form-check-input me-2" type="radio" name="payment_method" value="card">
                                                        <i class="fas fa-credit-card text-primary me-2"></i>Credit/Debit Card
                                                    </label>
                                                    <label class="list-group-item">
                                                        <input class="form-check-input me-2" type="radio" name="payment_method" value="bank_transfer">
                                                        <i class="fas fa-university text-info me-2"></i>Bank Transfer
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="mb-3" id="phoneGroup{{ $invoice->id }}" style="display: none;">
                                                <label class="form-label">M-Pesa Phone Number</label>
                                                <input type="text"
                                                       name="phone_number"
                                                       class="form-control"
                                                       placeholder="0712345678">
                                                <small class="text-muted">Enter the number to receive STK push</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check me-2"></i>Proceed to Pay
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const modal{{ $invoice->id }} = document.getElementById('paymentModal{{ $invoice->id }}');
                                const phoneGroup{{ $invoice->id }} = document.getElementById('phoneGroup{{ $invoice->id }}');

                                modal{{ $invoice->id }}.querySelectorAll('input[name="payment_method"]').forEach(radio => {
                                    radio.addEventListener('change', function() {
                                        phoneGroup{{ $invoice->id }}.style.display = this.value === 'mpesa' ? 'block' : 'none';
                                    });
                                });
                            });
                        </script>
                    @endforeach

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
