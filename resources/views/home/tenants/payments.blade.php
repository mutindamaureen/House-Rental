<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Payments - Tenant Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
        .payment-card {
            border: none;
            border-radius: 12px;
            margin-bottom: 15px;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid #28a745;
        }
        .payment-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .payment-card.pending {
            border-left-color: #ffc107;
        }
        .payment-card.failed {
            border-left-color: #dc3545;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .payment-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
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
                    <h2 class="mb-2"><i class="fas fa-wallet me-3"></i>Payment History</h2>
                    <p class="mb-0 opacity-75">Track all your rental payments</p>
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
                                <p class="text-muted mb-1">Pending</p>
                                <h3 class="mb-0 text-warning fw-bold">KSh {{ number_format($pendingPayments, 2) }}</h3>
                            </div>
                            <div class="text-warning" style="font-size: 2.5rem;">
                                <i class="fas fa-clock"></i>
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
                                <p class="text-muted mb-1">Last Payment</p>
                                @if($lastPayment)
                                    <h5 class="mb-0 fw-bold">{{ $lastPayment->paid_at->format('M d, Y') }}</h5>
                                    <small class="text-muted">{{ $lastPayment->paid_at->diffForHumans() }}</small>
                                @else
                                    <h5 class="mb-0 text-muted">No payments yet</h5>
                                @endif
                            </div>
                            <div class="text-info" style="font-size: 2.5rem;">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments List -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Transaction History</h5>
            </div>
            <div class="card-body p-4">
                @if($payments->isEmpty())
                    <div class="text-center py-5">
                        <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                        <h5>No Payment History</h5>
                        <p class="text-muted">You haven't made any payments yet.</p>
                        <a href="{{ route('tenant.invoices') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-file-invoice me-2"></i>View Invoices
                        </a>
                    </div>
                @else
                    @foreach($payments as $payment)
                        @php
                            $statusClass = match($payment->status) {
                                'succeeded' => 'success',
                                'pending', 'initiated' => 'warning',
                                'failed', 'cancelled' => 'danger',
                                'refunded' => 'info',
                                default => 'secondary'
                            };
                            $iconClass = match($payment->status) {
                                'succeeded' => 'check-circle',
                                'pending', 'initiated' => 'clock',
                                'failed', 'cancelled' => 'times-circle',
                                'refunded' => 'undo',
                                default => 'question-circle'
                            };
                            $cardClass = match($payment->status) {
                                'pending', 'initiated' => 'pending',
                                'failed', 'cancelled' => 'failed',
                                default => ''
                            };
                        @endphp
                        <div class="payment-card {{ $cardClass }}">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-1 text-center">
                                        <div class="payment-icon bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }}">
                                            <i class="fas fa-{{ $iconClass }}"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="mb-1">
                                            @if($payment->invoice)
                                                Invoice: {{ $payment->invoice->reference }}
                                            @else
                                                Manual Payment
                                            @endif
                                        </h6>
                                        <p class="text-muted mb-0 small">
                                            <i class="fas fa-hashtag me-1"></i>{{ $payment->merchant_reference }}
                                        </p>
                                        @if($payment->gateway_transaction_id)
                                            <p class="text-muted mb-0 small">
                                                <i class="fas fa-barcode me-1"></i>{{ $payment->gateway_transaction_id }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">Amount</small>
                                        <h5 class="mb-0 fw-bold text-{{ $statusClass }}">
                                            {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                                        </h5>
                                        @if($payment->fees_amount > 0)
                                            <small class="text-muted">Fees: {{ $payment->currency }} {{ number_format($payment->fees_amount, 2) }}</small>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">Method</small>
                                        <strong>
                                            @switch($payment->payment_method)
                                                @case('mpesa')
                                                    <i class="fas fa-mobile-alt text-success me-1"></i>M-Pesa
                                                    @break
                                                @case('card')
                                                    <i class="fas fa-credit-card text-primary me-1"></i>Card
                                                    @break
                                                @case('bank_transfer')
                                                    <i class="fas fa-university text-info me-1"></i>Bank
                                                    @break
                                                @default
                                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                            @endswitch
                                        </strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small class="text-muted d-block">Date</small>
                                        <strong>
                                            @if($payment->paid_at)
                                                {{ $payment->paid_at->format('M d, Y') }}
                                                <br><small class="text-muted">{{ $payment->paid_at->format('h:i A') }}</small>
                                            @else
                                                {{ $payment->created_at->format('M d, Y') }}
                                                <br><small class="text-muted">Initiated</small>
                                            @endif
                                        </strong>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <div class="mb-2">
                                            <span class="status-badge bg-{{ $statusClass }} text-white">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('tenant.payment.details', $payment->id) }}"
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->status === 'succeeded')
                                                <a href="{{ route('tenant.payment.receipt', $payment->id) }}"
                                                   class="btn btn-outline-success"
                                                   target="_blank">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($payment->notes)
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <div class="alert alert-info mb-0 py-2">
                                                <small><strong>Note:</strong> {{ $payment->notes }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Summary Card -->
        @if(!$payments->isEmpty())
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Payment Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center py-3">
                            <h6 class="text-muted mb-2">Total Transactions</h6>
                            <h3 class="mb-0 fw-bold">{{ $payments->total() }}</h3>
                        </div>
                        <div class="col-md-3 text-center py-3 border-start">
                            <h6 class="text-muted mb-2">Successful</h6>
                            <h3 class="mb-0 fw-bold text-success">
                                {{ $payments->where('status', 'succeeded')->count() }}
                            </h3>
                        </div>
                        <div class="col-md-3 text-center py-3 border-start">
                            <h6 class="text-muted mb-2">Pending</h6>
                            <h3 class="mb-0 fw-bold text-warning">
                                {{ $payments->whereIn('status', ['pending', 'initiated'])->count() }}
                            </h3>
                        </div>
                        <div class="col-md-3 text-center py-3 border-start">
                            <h6 class="text-muted mb-2">Failed</h6>
                            <h3 class="mb-0 fw-bold text-danger">
                                {{ $payments->whereIn('status', ['failed', 'cancelled'])->count() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
