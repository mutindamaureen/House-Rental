<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Invoice Details</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Invoice Details</h4>
                                <span class="badge bg-light text-dark fs-6">{{ $invoice->reference ?? '—' }}</span>
                            </div>

                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Tenant Information</h6>
                                        <p class="mb-1"><strong>Name:</strong> {{ optional($invoice->tenant->user)->name ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Email:</strong> {{ optional($invoice->tenant->user)->email ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Phone:</strong> {{ optional($invoice->tenant)->phone ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-muted">House Information</h6>
                                        <p class="mb-1"><strong>Title:</strong> {{ optional($invoice->house)->title ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Location:</strong> {{ optional($invoice->house)->location ?? 'N/A' }}</p>
                                        <p class="mb-1">
                                            <strong>Monthly Rent:</strong>
                                            @if(optional($invoice->house)->price)
                                                {{ $invoice->currency ?? 'KES' }} {{ number_format(optional($invoice->house)->price, 2) }}
                                            @else
                                                —
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <hr>

                                @php
                                    $currency = $invoice->currency ?? 'KES';
                                    $amount = (float) ($invoice->amount ?? 0);
                                    $paid = (float) ($invoice->paid_amount ?? 0);
                                    $balance = max(0, $amount - $paid);
                                    $statusColors = ['paid' => 'success', 'unpaid' => 'warning', 'overdue' => 'danger', 'cancelled' => 'secondary'];
                                    $color = $statusColors[$invoice->status ?? 'unpaid'] ?? 'secondary';
                                @endphp

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Invoice Amount:</strong> <span class="text-primary fs-5">{{ $currency }} {{ number_format($amount, 2) }}</span></p>
                                        <p class="mb-2"><strong>Paid Amount:</strong> <span class="text-success">{{ $currency }} {{ number_format($paid, 2) }}</span></p>
                                        <p class="mb-2"><strong>Balance:</strong> <span class="text-danger">{{ $currency }} {{ number_format($balance, 2) }}</span></p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Status:</strong>
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($invoice->status ?? 'unpaid') }}</span>
                                        </p>

                                        <p class="mb-2"><strong>Issued Date:</strong>
                                            @if(!empty($invoice->issued_date))
                                                {{ \Carbon\Carbon::parse($invoice->issued_date)->format('d M Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </p>

                                        <p class="mb-2"><strong>Due Date:</strong>
                                            @if(!empty($invoice->due_date))
                                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <h6 class="text-muted">Description</h6>
                                    <p>{{ $invoice->description ?? '—' }}</p>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>

                                    @can('update', $invoice)
                                        <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    @endcan

                                    @if((($invoice->status ?? 'unpaid') !== 'paid') && (float) ($invoice->amount ?? 0) > 0)
                                        @can('markPaid', $invoice)
                                            <form action="{{ route('invoices.markPaid', $invoice->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Mark this invoice as paid?')">
                                                    <i class="fas fa-check"></i> Mark as Paid
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Payment History</h5>
                            </div>

                            <div class="card-body">
                                @if($invoice->payments && $invoice->payments->count() > 0)
                                    <div class="list-group">
                                        @foreach($invoice->payments as $payment)
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <strong>{{ $payment->currency ?? $currency }} {{ number_format($payment->amount ?? 0, 2) }}</strong>
                                                        <div class="small text-muted">{{ $payment->payment_method ?? 'N/A' }}</div>
                                                        @if(!empty($payment->reference))
                                                            <div class="small text-muted">Ref: {{ $payment->reference }}</div>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">{{ optional($payment->created_at)->format('d M Y') ?? '—' }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted text-center">No payments recorded</p>
                                @endif
                            </div>

                            @if($invoice->payments && $invoice->payments->count() > 5)
                                <div class="card-footer text-center">
                                    <small class="text-muted">Showing latest {{ $invoice->payments->take(5)->count() }} payments</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div> <!-- row -->
            </div> <!-- container -->
        </div> <!-- page-content -->
    </div> <!-- d-flex -->

    @include('admin.js')
</body>
</html>
