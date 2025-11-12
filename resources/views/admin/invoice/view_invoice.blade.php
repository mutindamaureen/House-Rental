<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Invoice Management</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Invoice Management</h2>
                    <div>
                        <a href="{{ route('invoices.create') }}" class="btn btn-primary me-2">
                            <i class="fas fa-plus me-1"></i> Add Invoice
                        </a>

                        <form action="{{ route('invoices.generateMonthly') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Generate invoices for all tenants for this month?')">
                                <i class="fas fa-calendar-check me-1"></i> Generate Monthly Invoices
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Reference</th>
                                        <th>Tenant</th>
                                        <th>House</th>
                                        <th>Amount</th>
                                        <th>Issued Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $invoice)
                                    <tr>
                                        <td><strong>{{ $invoice->reference }}</strong></td>

                                        <td>
                                            {{ optional($invoice->tenant->user)->name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ optional($invoice->tenant->user)->email ?? '' }}</small>
                                        </td>

                                        <td>
                                            {{ $invoice->house->title ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $invoice->house->location ?? '' }}</small>
                                        </td>

                                        <td>
                                            <strong>{{ $invoice->currency ?? 'KES' }} {{ number_format($invoice->amount ?? 0, 2) }}</strong><br>
                                            @if(!empty($invoice->paid_amount) && $invoice->paid_amount > 0)
                                                <small class="text-success">Paid: {{ number_format($invoice->paid_amount, 2) }}</small>
                                            @endif
                                        </td>

                                        <td>
                                            @if(!empty($invoice->issued_date))
                                                {{ \Carbon\Carbon::parse($invoice->issued_date)->format('d M Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if(!empty($invoice->due_date))
                                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        <td>
                                            @php
                                                $statusColors = [
                                                    'paid' => 'success',
                                                    'unpaid' => 'warning',
                                                    'overdue' => 'danger',
                                                    'cancelled' => 'secondary'
                                                ];
                                                $color = $statusColors[$invoice->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($invoice->status ?? 'unpaid') }}</span>
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-info">View Details</a>

                                                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                                @if(($invoice->status ?? 'unpaid') !== 'paid')
                                                    <form action="{{ route('invoices.markPaid', $invoice->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">Mark as Paid</button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>

                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <p class="text-muted mb-0">No invoices found</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 d-flex justify-content-end">
                            {{ $invoices->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- page-content -->
    </div> <!-- d-flex -->

    @include('admin.js')
</body>
</html>
