<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->reference }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            background: white;
        }
        .invoice-wrapper {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .invoice-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info {
            text-align: right;
        }
        .invoice-details {
            margin: 30px 0;
        }
        .invoice-table {
            width: 100%;
            margin: 30px 0;
        }
        .invoice-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }
        .invoice-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        .total-row {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .grand-total {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
            padding: 15px 0;
            border-top: 3px solid #667eea;
            margin-top: 10px;
        }
        .invoice-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        .status-unpaid {
            background: #fff3cd;
            color: #856404;
        }
        .status-overdue {
            background: #f8d7da;
            color: #721c24;
        }
        @media print {
            body {
                background: white;
            }
            .invoice-wrapper {
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <!-- Print Button -->
        <div class="text-end mb-3 no-print">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>Print Invoice
            </button>
        </div>

        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="mb-0" style="color: #667eea;">INVOICE</h1>
                    <p class="text-muted mb-0">{{ $invoice->reference }}</p>
                </div>
                <div class="col-md-6 company-info">
                    <h4 class="mb-2">Property Management System</h4>
                    <p class="mb-0">
                        123 Property Street<br>
                        Nairobi, Kenya<br>
                        Phone: +254 700 000 000<br>
                        Email: info@property.com
                    </p>
                </div>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="text-end mb-4">
            <span class="status-badge status-{{ $invoice->status }}">
                {{ ucfirst($invoice->status) }}
            </span>
        </div>

        <!-- Bill To & Invoice Details -->
        <div class="row invoice-details">
            <div class="col-md-6">
                <h6 class="text-muted mb-2">BILL TO:</h6>
                <h5 class="mb-2">{{ $invoice->tenant->user->name }}</h5>
                <p class="mb-0">
                    {{ $invoice->tenant->user->email }}<br>
                    {{ $invoice->tenant->user->phone ?? 'N/A' }}<br>
                    {{ $invoice->tenant->user->address ?? '' }}
                </p>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-end"><strong>Invoice Date:</strong></td>
                        <td>{{ $invoice->issued_date->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>Due Date:</strong></td>
                        <td>
                            <span class="{{ $invoice->status === 'overdue' ? 'text-danger fw-bold' : '' }}">
                                {{ $invoice->due_date->format('F d, Y') }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>Property:</strong></td>
                        <td>{{ $invoice->house->title ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-end"><strong>Location:</strong></td>
                        <td>{{ $invoice->house->location ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Invoice Items -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 60%;">Description</th>
                    <th style="width: 20%;" class="text-center">Quantity</th>
                    <th style="width: 20%;" class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $invoice->description }}</strong><br>
                        <small class="text-muted">
                            Property: {{ $invoice->house->title ?? 'N/A' }}<br>
                            Period: {{ $invoice->issued_date->format('F Y') }}
                        </small>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="row justify-content-end">
                <div class="col-md-6">
                    <div class="total-row">
                        <div class="row">
                            <div class="col-6"><strong>Subtotal:</strong></div>
                            <div class="col-6">{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}</div>
                        </div>
                    </div>

                    @if($invoice->paid_amount > 0)
                        <div class="total-row text-success">
                            <div class="row">
                                <div class="col-6"><strong>Paid Amount:</strong></div>
                                <div class="col-6">{{ $invoice->currency }} {{ number_format($invoice->paid_amount, 2) }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="grand-total">
                        <div class="row">
                            <div class="col-6">AMOUNT DUE:</div>
                            <div class="col-6">{{ $invoice->currency }} {{ number_format($invoice->amount - $invoice->paid_amount, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        @if($invoice->status !== 'paid')
            <div class="alert alert-info mt-4">
                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Payment Information</h6>
                <p class="mb-2"><strong>Payment Methods Available:</strong></p>
                <ul class="mb-0">
                    <li>M-Pesa: Send to 0700 000 000</li>
                    <li>Bank Transfer: Account #123456789, ABC Bank</li>
                    <li>Credit/Debit Card: Pay online through tenant portal</li>
                </ul>
                <p class="mt-2 mb-0"><small>Please include invoice reference <strong>{{ $invoice->reference }}</strong> in your payment description.</small></p>
            </div>
        @endif

        <!-- Payment History -->
        @if($invoice->payments && $invoice->payments->isNotEmpty())
            <div class="mt-4">
                <h6 class="mb-3">Payment History</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            <th>Method</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : $payment->created_at->format('M d, Y') }}</td>
                                <td><small>{{ $payment->merchant_reference }}</small></td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                <td class="text-end">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $payment->status === 'succeeded' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Terms and Conditions -->
        <div class="mt-5">
            <h6 class="mb-2">Terms & Conditions</h6>
            <ol class="small text-muted">
                <li>Payment is due within {{ $invoice->due_date->diffInDays($invoice->issued_date) }} days of invoice date.</li>
                <li>Late payments may incur additional charges as per the lease agreement.</li>
                <li>Please ensure payment reference is included in all transactions.</li>
                <li>For any queries, please contact property management.</li>
            </ol>
        </div>

        <!-- Footer -->
        <div class="invoice-footer">
            <p class="mb-1"><strong>Thank you for your payment!</strong></p>
            <p class="mb-0">This is a computer-generated invoice. For any inquiries, please contact us.</p>
            <p class="mt-3 mb-0 small">Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
