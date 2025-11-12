<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Details - {{ $payment->merchant_reference }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .payment-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .payment-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 30px;
        }
        .payment-body {
            background: white;
            padding: 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .amount-highlight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
        }
        .timeline-item {
            padding: 15px 0;
            border-left: 3px solid #dee2e6;
            padding-left: 25px;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 20px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #dee2e6;
        }
        .timeline-item.active::before {
            background: #28a745;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <div class="payment-container">
            <!-- Back Button -->
            <div class="mb-4 no-print">
                <a href="{{ route('tenant.payments') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Payments
                </a>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Payment Card -->
            <div class="card border-0 shadow-lg">
                <!-- Header -->
                <div class="payment-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-receipt me-3"></i>Payment Receipt
                            </h2>
                            <h5 class="mb-0 opacity-75">{{ $payment->merchant_reference }}</h5>
                        </div>
                        <div class="col-md-4 text-end">
                            @php
                                $statusClass = match($payment->status) {
                                    'succeeded' => 'success',
                                    'pending', 'initiated' => 'warning',
                                    'failed', 'cancelled' => 'danger',
                                    'refunded' => 'info',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="status-badge bg-{{ $statusClass }} bg-opacity-25 text-white">
                                <i class="fas fa-{{ match($payment->status) {
                                    'succeeded' => 'check-circle',
                                    'pending', 'initiated' => 'clock',
                                    'failed', 'cancelled' => 'times-circle',
                                    'refunded' => 'undo',
                                    default => 'question-circle'
                                } }} me-2"></i>
                                {{ ucfirst($payment->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="payment-body">
                    <!-- Amount Section -->
                    <div class="amount-highlight mb-4">
                        <h6 class="mb-2 opacity-75">PAYMENT AMOUNT</h6>
                        <h1 class="mb-3 fw-bold">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</h1>
                        @if($payment->fees_amount > 0)
                            <div class="row">
                                <div class="col-6">
                                    <small class="opacity-75">Transaction Fee</small>
                                    <h6 class="mb-0">{{ $payment->currency }} {{ number_format($payment->fees_amount, 2) }}</h6>
                                </div>
                                <div class="col-6">
                                    <small class="opacity-75">Net Amount</small>
                                    <h6 class="mb-0">{{ $payment->currency }} {{ number_format($payment->net_amount, 2) }}</h6>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Details -->
                    <div class="info-box">
                        <h6 class="text-muted mb-3"><i class="fas fa-info-circle me-2"></i>PAYMENT INFORMATION</h6>

                        <div class="info-row">
                            <strong>Transaction Reference:</strong>
                            <span>{{ $payment->merchant_reference }}</span>
                        </div>

                        @if($payment->gateway_transaction_id)
                            <div class="info-row">
                                <strong>Gateway Transaction ID:</strong>
                                <span>{{ $payment->gateway_transaction_id }}</span>
                            </div>
                        @endif

                        <div class="info-row">
                            <strong>Payment Method:</strong>
                            <span>
                                @switch($payment->payment_method)
                                    @case('mpesa')
                                        <i class="fas fa-mobile-alt text-success me-1"></i>M-Pesa
                                        @break
                                    @case('card')
                                        <i class="fas fa-credit-card text-primary me-1"></i>Credit/Debit Card
                                        @break
                                    @case('bank_transfer')
                                        <i class="fas fa-university text-info me-1"></i>Bank Transfer
                                        @break
                                    @default
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                @endswitch
                            </span>
                        </div>

                        @if($payment->gateway)
                            <div class="info-row">
                                <strong>Payment Gateway:</strong>
                                <span>{{ ucfirst($payment->gateway) }}</span>
                            </div>
                        @endif

                        <div class="info-row">
                            <strong>Payment Status:</strong>
                            <span>
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Related Invoice -->
                    @if($payment->invoice)
                        <div class="info-box">
                            <h6 class="text-muted mb-3"><i class="fas fa-file-invoice me-2"></i>RELATED INVOICE</h6>

                            <div class="info-row">
                                <strong>Invoice Reference:</strong>
                                <span>{{ $payment->invoice->reference }}</span>
                            </div>

                            <div class="info-row">
                                <strong>Invoice Amount:</strong>
                                <span>{{ $payment->invoice->currency }} {{ number_format($payment->invoice->amount, 2) }}</span>
                            </div>

                            <div class="info-row">
                                <strong>Invoice Status:</strong>
                                <span>
                                    <span class="badge bg-{{ $payment->invoice->status === 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payment->invoice->status) }}
                                    </span>
                                </span>
                            </div>

                            <div class="text-end mt-3">
                                <a href="{{ route('tenant.invoice.details', $payment->invoice->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>View Invoice
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Date Information -->
                    <div class="info-box">
                        <h6 class="text-muted mb-3"><i class="fas fa-calendar me-2"></i>DATE INFORMATION</h6>

                        <div class="info-row">
                            <strong>Initiated On:</strong>
                            <span>{{ $payment->created_at->format('F d, Y \a\t h:i A') }}</span>
                        </div>

                        @if($payment->paid_at)
                            <div class="info-row">
                                <strong>Completed On:</strong>
                                <span>{{ $payment->paid_at->format('F d, Y \a\t h:i A') }}</span>
                            </div>

                            <div class="info-row">
                                <strong>Processing Time:</strong>
                                <span>{{ $payment->created_at->diffForHumans($payment->paid_at, true) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Additional Notes -->
                    @if($payment->notes)
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-sticky-note me-2"></i>Additional Notes</h6>
                            <p class="mb-0">{{ $payment->notes }}</p>
                        </div>
                    @endif

                    <!-- Timeline -->
                    <div class="mt-4">
                        <h6 class="text-muted mb-3"><i class="fas fa-stream me-2"></i>TRANSACTION TIMELINE</h6>

                        <div class="timeline-item active">
                            <strong>Payment Initiated</strong>
                            <p class="text-muted mb-0 small">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        @if(in_array($payment->status, ['initiated', 'pending', 'succeeded']))
                            <div class="timeline-item {{ in_array($payment->status, ['pending', 'succeeded']) ? 'active' : '' }}">
                                <strong>Payment Processing</strong>
                                <p class="text-muted mb-0 small">
                                    {{ in_array($payment->status, ['pending', 'succeeded']) ? 'In progress' : 'Awaiting' }}
                                </p>
                            </div>
                        @endif

                        @if($payment->paid_at)
                            <div class="timeline-item active">
                                <strong>Payment Completed</strong>
                                <p class="text-muted mb-0 small">{{ $payment->paid_at->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif

                        @if(in_array($payment->status, ['failed', 'cancelled']))
                            <div class="timeline-item" style="border-color: #dc3545;">
                                <strong class="text-danger">Payment {{ ucfirst($payment->status) }}</strong>
                                <p class="text-muted mb-0 small">{{ $payment->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif

                        @if($payment->status === 'refunded')
                            <div class="timeline-item" style="border-color: #17a2b8;">
                                <strong class="text-info">Payment Refunded</strong>
                                <p class="text-muted mb-0 small">{{ $payment->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center mt-5 no-print">
                        @if($payment->status === 'succeeded')
                            <a href="{{ route('tenant.payment.receipt', $payment->id) }}"
                               class="btn btn-success btn-lg me-2"
                               target="_blank">
                                <i class="fas fa-download me-2"></i>Download Receipt
                            </a>
                        @endif
                        <button onclick="window.print()" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>

            <!-- Support Info -->
            <div class="alert alert-light border mt-4 no-print">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x text-primary me-3"></i>
                    <div>
                        <h6 class="mb-1">Need Help?</h6>
                        <p class="mb-0 small text-muted">
                            If you have any questions about this payment, please contact support with your transaction reference:
                            <strong>{{ $payment->merchant_reference }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
