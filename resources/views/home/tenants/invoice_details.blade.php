<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice Details - {{ $invoice->reference }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .invoice-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 30px;
        }
        .invoice-body {
            background: white;
            padding: 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
        }
        .info-row {
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .amount-box {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
        }
        .payment-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #28a745;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .invoice-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    @include('home.header')

    <div class="container py-5">
        <div class="invoice-container">
            <!-- Back Button -->
            <div class="mb-4 no-print">
                <a href="{{ route('tenant.invoices') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Invoices
                </a>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Invoice Card -->
            <div class="card border-0 shadow-lg">
                <!-- Header -->
                <div class="invoice-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="fas fa-file-invoice me-3"></i>Invoice
                            </h2>
                            <h4 class="mb-0 opacity-75">{{ $invoice->reference }}</h4>
                        </div>
                        <div class="col-md-4 text-end">
                            @php
                                $statusClass = match($invoice->status) {
                                    'paid' => 'success',
                                    'unpaid' => 'warning',
                                    'overdue' => 'danger',
                                    'cancelled' => 'secondary',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="status-badge bg-{{ $statusClass }} bg-opacity-25 text-white">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="invoice-body">
                    <!-- Invoice Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">BILL TO:</h6>
                            <h5 class="mb-2">{{ Auth::user()->name }}</h5>
                            <p class="mb-1"><i class="fas fa-envelope text-muted me-2"></i>{{ Auth::user()->email }}</p>
                            <p class="mb-1"><i class="fas fa-phone text-muted me-2"></i>{{ Auth::user()->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h6 class="text-muted mb-3">PROPERTY:</h6>
                            <h5 class="mb-2">{{ $invoice->house->title ?? 'N/A' }}</h5>
                            <p class="mb-1"><i class="fas fa-map-marker-alt text-muted me-2"></i>{{ $invoice->house->location ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Date Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="row">
                                    <div class="col-6">
                                        <strong class="text-muted">Issue Date:</strong>
                                    </div>
                                    <div class="col-6 text-end">
                                        {{ $invoice->issued_date->format('F d, Y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="row">
                                    <div class="col-6">
                                        <strong class="text-muted">Due Date:</strong>
                                    </div>
                                    <div class="col-6 text-end">
                                        <span class="{{ $invoice->status === 'overdue' ? 'text-danger fw-bold' : '' }}">
                                            {{ $invoice->due_date->format('F d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="amount-box">
                                <h6 class="mb-2 opacity-75">TOTAL AMOUNT</h6>
                                <h1 class="mb-0 fw-bold">{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</h1>
                                @if($invoice->paid_amount > 0)
                                    <hr class="border-white opacity-50 my-3">
                                    <div>
                                        <small class="opacity-75">Paid: {{ $invoice->currency }} {{ number_format($invoice->paid_amount, 2) }}</small><br>
                                        <small class="opacity-75">Balance: {{ $invoice->currency }} {{ number_format($invoice->amount - $invoice->paid_amount, 2) }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">DESCRIPTION:</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0">{{ $invoice->description }}</p>
                        </div>
                    </div>

                    <!-- Payment History -->
                    @if($invoice->payments->isNotEmpty())
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">PAYMENT HISTORY:</h6>
                            @foreach($invoice->payments as $payment)
                                <div class="payment-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Transaction ID</small>
                                            <strong>{{ $payment->gateway_transaction_id ?? $payment->merchant_reference }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Amount</small>
                                            <strong class="text-success">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</strong>
                                        </div>
                                        <div class="col-md-3">
                                            <small class="text-muted d-block">Method</small>
                                            <strong>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</strong>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <small class="text-muted d-block">Date</small>
                                            <strong>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : 'Pending' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="text-center mt-5 no-print">
                        @if($invoice->status === 'unpaid' || $invoice->status === 'overdue')
                            <button type="button"
                                    class="btn btn-success btn-lg me-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#paymentModal">
                                <i class="fas fa-credit-card me-2"></i>Pay Now
                            </button>
                        @endif
                        <a href="{{ route('tenant.invoice.download', $invoice->id) }}"
                           class="btn btn-outline-primary btn-lg me-2">
                            <i class="fas fa-download me-2"></i>Download
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card me-2"></i>Make Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('tenant.payment.initiate', $invoice->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Invoice:</strong> {{ $invoice->reference }}<br>
                            <strong>Amount Due:</strong> {{ $invoice->currency }} {{ number_format($invoice->amount - $invoice->paid_amount, 2) }}
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Payment Method <span class="text-danger">*</span></label>
                            <div class="list-group">
                                <label class="list-group-item">
                                    <input class="form-check-input me-2" type="radio" name="payment_method" value="mpesa" id="mpesa" required>
                                    <i class="fas fa-mobile-alt text-success me-2"></i>M-Pesa
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-2" type="radio" name="payment_method" value="card" id="card">
                                    <i class="fas fa-credit-card text-primary me-2"></i>Credit/Debit Card
                                </label>
                                <label class="list-group-item">
                                    <input class="form-check-input me-2" type="radio" name="payment_method" value="bank_transfer" id="bank">
                                    <i class="fas fa-university text-info me-2"></i>Bank Transfer
                                </label>
                            </div>
                        </div>

                        <div class="mb-3" id="phoneGroup" style="display: none;">
                            <label class="form-label">M-Pesa Phone Number <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="phone_number"
                                   class="form-control"
                                   placeholder="0712345678"
                                   pattern="[0-9]{10}">
                            <small class="text-muted">Enter the number to receive STK push notification</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('home.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            const phoneGroup = document.getElementById('phoneGroup');

            paymentMethods.forEach(method => {
                method.addEventListener('change', function() {
                    phoneGroup.style.display = this.value === 'mpesa' ? 'block' : 'none';
                    if (this.value === 'mpesa') {
                        phoneGroup.querySelector('input').required = true;
                    } else {
                        phoneGroup.querySelector('input').required = false;
                    }
                });
            });
        });
    </script>
</body>
</html>
