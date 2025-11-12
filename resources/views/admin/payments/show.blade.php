<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Payment Details</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Payment Details</h3>
                    <a href="{{ route('admin.view_payments') }}" class="btn btn-outline-secondary">Back to list</a>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <strong>Reference</strong>
                                <div>{{ $payment->merchant_reference ?? $payment->gateway_transaction_id ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Amount</strong>
                                <div>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Tenant</strong>
                                <div>{{ optional($payment->tenant->user)->name ?? 'N/A' }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Invoice</strong>
                                <div>{{ optional($payment->invoice)->reference ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Method</strong>
                                <div>{{ ucfirst($payment->payment_method) }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Gateway</strong>
                                <div>{{ $payment->gateway ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Status</strong>
                                <div>{{ ucfirst($payment->status) }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Paid at</strong>
                                <div>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Fees</strong>
                                <div>{{ number_format($payment->fees_amount ?? 0, 2) }}</div>
                            </div>

                            <div class="col-md-6">
                                <strong>Net Amount</strong>
                                <div>{{ number_format($payment->net_amount ?? ($payment->amount - ($payment->fees_amount ?? 0)), 2) }}</div>
                            </div>

                            <div class="col-12">
                                <strong>Notes</strong>
                                <div class="border p-2">{{ $payment->notes ?? '—' }}</div>
                            </div>

                            <div class="col-12 mt-3">
                                <strong>Raw Request Payload</strong>
                                <pre class="small bg-light p-2">{{ json_encode($payment->request_payload ?? [], JSON_PRETTY_PRINT) }}</pre>
                            </div>

                            <div class="col-12 mt-1">
                                <strong>Raw Response Payload</strong>
                                <pre class="small bg-light p-2">{{ json_encode($payment->response_payload ?? [], JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>

                        <div class="mt-4">
                            <form action="{{ route('admin.delete_payment', $payment->id) }}" method="POST" onsubmit="return confirm('Delete this payment?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">Delete Payment</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.js')
</body>
</html>
