<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->merchant_reference }}</title>
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
        .receipt-wrapper {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border: 2px solid #28a745;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .receipt-stamp {
            display: inline-block;
            border: 3px solid #28a745;
            color: #28a745;
            padding: 10px 30px;
            border-radius: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            transform: rotate(-5deg);
            margin: 20px 0;
        }
        .info-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .amount-box {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin: 30px 0;
        }
        .receipt-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
        }
        .signature-box {
            text-align: center;
            padding-top: 60px;
            border-top: 2px solid #333;
            width: 200px;
        }
        @media print {
            body {
                background: white;
            }
            .receipt-wrapper {
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
    <div class="receipt-wrapper">
        <!-- Print Button -->
        <div class="text-end mb-3 no-print">
            <button onclick="window.print()" class="btn btn-success">
                <i class="fas fa-print me-2"></i>Print Receipt
            </button>
        </div>

        <!-- Receipt Header -->
        <div class="receipt-header">
            <h1 style="color: #28a745; margin-bottom: 10px;">PAYMENT RECEIPT</h1>
            <div class="receipt-stamp">PAID</div>
            <p class="mb-0"><strong>Receipt No:</strong> {{ $payment->merchant_reference }}</p>
        </div>

        <!-- Company Info -->
        <div class="text-center mb-4">
            <h4 class="mb-2">Property Management System</h4>
            <p class="mb-0">
                123 Property Street, Nairobi, Kenya<br>
                Phone: +254 700 000 000 | Email: info@property.com<br>
                <small class="text-muted">PIN: A000000000A</small>
            </p>
        </div>

        <!-- Payment Amount -->
        <div class="amount-box">
            <h6 class="mb-2 opacity-75">AMOUNT PAID</h6>
            <h1 class="mb-0 fw-bold" style="font-size: 3rem;">
                {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
            </h1>
            @if($payment->fees_amount > 0)
                <hr class="border-white opacity-50 my-3">
                <div class="row">
                    <div class="col-6">
                        <small class="opacity-75">Processing Fee</small>
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
        <div class="info-section">
            <h6 class="mb-3"><strong>PAYMENT DETAILS</strong></h6>

            <div class="info-row">
                <strong>Received From:</strong>
                <span>{{ $payment->tenant->user->name }}</span>
            </div>

            <div class="info-row">
                <strong>Email:</strong>
                <span>{{ $payment->tenant->user->email }}</span>
            </div>

            <div class="info-row">
                <strong>Phone:</strong>
                <span>{{ $payment->tenant->user->phone ?? 'N/A' }}</span>
            </div>

            <div class="info-row">
                <strong>Payment Date:</strong>
                <span>{{ $payment->paid_at->format('F d, Y \a\t h:i A') }}</span>
            </div>

            <div class="info-row">
                <strong>Payment Method:</strong>
                <span>
                    @switch($payment->payment_method)
                        @case('mpesa')
                            M-Pesa
                            @break
                        @case('card')
                            Credit/Debit Card
                            @break
                        @case('bank_transfer')
                            Bank Transfer
                            @break
                        @default
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                    @endswitch
                </span>
            </div>

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

            @if($payment->gateway)
                <div class="info-row">
                    <strong>Payment Gateway:</strong>
                    <span>{{ ucfirst($payment->gateway) }}</span>
                </div>
            @endif
        </div>

        <!-- Invoice Details -->
        @if($payment->invoice)
            <div class="info-section">
                <h6 class="mb-3"><strong>INVOICE DETAILS</strong></h6>

                <div class="info-row">
                    <strong>Invoice Reference:</strong>
                    <span>{{ $payment->invoice->reference }}</span>
                </div>

                <div class="info-row">
                    <strong>Description:</strong>
                    <span>{{ $payment->invoice->description }}</span>
                </div>

                <div class="info-row">
                    <strong>Property:</strong>
                    <span>{{ $payment->invoice->house->title ?? 'N/A' }}</span>
                </div>

                <div class="info-row">
                    <strong>Location:</strong>
                    <span>{{ $payment->invoice->house->location ?? 'N/A' }}</span>
                </div>

                <div class="info-row">
                    <strong>Invoice Amount:</strong>
                    <span>{{ $payment->invoice->currency }} {{ number_format($payment->invoice->amount, 2) }}</span>
                </div>

                <div class="info-row">
                    <strong>Amount Paid:</strong>
                    <span class="text-success fw-bold">
                        {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                    </span>
                </div>

                @if($payment->invoice->amount > $payment->invoice->paid_amount)
                    <div class="info-row">
                        <strong>Balance Due:</strong>
                        <span class="text-danger fw-bold">
                            {{ $payment->invoice->currency }} {{ number_format($payment->invoice->amount - $payment->invoice->paid_amount, 2) }}
                        </span>
                    </div>
                @else
                    <div class="info-row">
                        <strong>Status:</strong>
                        <span class="text-success fw-bold">FULLY PAID</span>
                    </div>
                @endif
            </div>
        @endif

        <!-- Additional Notes -->
        @if($payment->notes)
            <div class="alert alert-info">
                <h6 class="alert-heading"><strong>Additional Notes</strong></h6>
                <p class="mb-0">{{ $payment->notes }}</p>
            </div>
        @endif

        <!-- Payment Confirmation -->
        <div class="alert alert-success text-center mt-4">
            <h5 class="alert-heading mb-2">
                <i class="fas fa-check-circle me-2"></i>Payment Confirmed
            </h5>
            <p class="mb-0">
                This is to certify that the above amount has been received in full.<br>
                <small>This receipt is valid without signature (computer generated).</small>
            </p>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <strong>Received By</strong><br>
                <small class="text-muted">Property Management</small>
            </div>
            <div class="signature-box">
                <strong>Date</strong><br>
                <small class="text-muted">{{ $payment->paid_at->format('F d, Y') }}</small>
            </div>
        </div>

        <!-- Footer -->
        <div class="receipt-footer">
            <p class="mb-1"><strong>Thank you for your payment!</strong></p>
            <p class="mb-2">Keep this receipt for your records.</p>
            <hr class="my-3">
            <p class="mb-0 small">
                For inquiries, please contact us at info@property.com or +254 700 000 000<br>
                Receipt generated on {{ now()->format('F d, Y \a\t h:i A') }}
            </p>
        </div>

        <!-- Verification Notice -->
        <div class="text-center mt-4 p-3 bg-light rounded">
            <p class="mb-0 small">
                <i class="fas fa-shield-alt text-success me-2"></i>
                <strong>Verification Code:</strong> {{ strtoupper(substr(md5($payment->id . $payment->merchant_reference), 0, 8)) }}<br>
                <small class="text-muted">This code can be used to verify the authenticity of this receipt.</small>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Auto-print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
