<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Payments</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">All Payments</h4>
                            <a href="{{ route('admin.add_payment') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Payment
                            </a>
                        </div>

                        <div class="card-body">
                            @if($payments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Reference</th>
                                                <th>Tenant</th>
                                                <th>Invoice</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Gateway</th>
                                                <th>Status</th>
                                                <th>Paid At</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($payments as $payment)
                                                <tr>
                                                    <td>{{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}</td>
                                                    <td>{{ $payment->merchant_reference ?? $payment->gateway_transaction_id ?? '—' }}</td>

                                                    {{-- Tenant Relationship --}}
                                                    <td>
                                                        @if($payment->tenant && $payment->tenant->user)
                                                            {{ $payment->tenant->user->name }} <br>
                                                            <small class="text-muted">{{ $payment->tenant->user->email }}</small>
                                                        @else
                                                            <span class="text-danger">N/A (Tenant Deleted)</span>
                                                        @endif
                                                    </td>

                                                    <td>{{ optional($payment->invoice)->reference ?? '—' }}</td>
                                                    <td>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                                                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                                    <td>{{ $payment->gateway ?? '—' }}</td>

                                                    <td>
                                                        @php
                                                            $statusColors = [
                                                                'succeeded' => 'success',
                                                                'pending' => 'warning',
                                                                'initiated' => 'info',
                                                                'failed' => 'danger',
                                                                'refunded' => 'secondary',
                                                                'cancelled' => 'dark'
                                                            ];
                                                            $color = $statusColors[$payment->status] ?? 'secondary';
                                                        @endphp
                                                        <span class="badge bg-{{ $color }}">
                                                            {{ ucfirst($payment->status) }}
                                                        </span>
                                                    </td>

                                                    <td>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : '—' }}</td>

                                                    <td class="text-end">

                                                        {{-- View Payment --}}
                                                        <a href="{{ route('admin.view_payment', $payment->id) }}"
                                                           class="btn btn-sm btn-info"
                                                           title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        {{-- Delete Payment --}}
                                                        <form action="{{ route('admin.delete_payment', $payment->id) }}"
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Are you sure you want to delete this payment?')"
                                                                    title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>

                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center py-4">
                                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">No payments found</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination --}}
                                <div class="mt-3">
                                    {{ $payments->links() }}
                                </div>

                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No payments recorded yet</h5>
                                    <a href="{{ route('admin.add_payment') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus"></i> Add First Payment
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('admin.js')

    <style>
        .table th {
            font-weight: 600;
            white-space: nowrap;
        }
        .badge {
            padding: 0.35rem 0.65rem;
            font-size: 0.875rem;
        }
    </style>

</body>
</html>
