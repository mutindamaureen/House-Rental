<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Add Payment</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Add Payment</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.upload_payment') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="tenant_id" class="form-label fw-bold">Tenant</label>
                                    <select name="tenant_id" id="tenant_id" class="form-control">
                                        <option value="">Select Tenant</option>
                                        @foreach ($tenants as $tenant)
                                            <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->user->name ?? 'Unnamed' }} ({{ $tenant->user->email ?? '' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="invoice_id" class="form-label fw-bold">Invoice</label>
                                    <select name="invoice_id" id="invoice_id" class="form-control">
                                        <option value="">Select Invoice (optional)</option>
                                        @foreach ($invoices as $inv)
                                            <option value="{{ $inv->id }}" {{ old('invoice_id') == $inv->id ? 'selected' : '' }}>
                                                {{ $inv->reference ?? $inv->id }} — {{ number_format($inv->amount, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="amount" class="form-label fw-bold">Amount</label>
                                    <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label for="currency" class="form-label fw-bold">Currency</label>
                                    <input type="text" name="currency" id="currency" class="form-control" value="{{ old('currency','KES') }}" maxlength="3" required>
                                </div>

                                <div class="col-md-4">
                                    <label for="payment_method" class="form-label fw-bold">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control" required>
                                        <option value="">Select Method</option>
                                        <option value="card" {{ old('payment_method')=='card' ? 'selected' : '' }}>Card</option>
                                        <option value="mpesa" {{ old('payment_method')=='mpesa' ? 'selected' : '' }}>M-Pesa</option>
                                        <option value="paybill" {{ old('payment_method')=='paybill' ? 'selected' : '' }}>Paybill</option>
                                        <option value="till" {{ old('payment_method')=='till' ? 'selected' : '' }}>Till</option>
                                        <option value="bank_transfer" {{ old('payment_method')=='bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                        <option value="cash" {{ old('payment_method')=='cash' ? 'selected' : '' }}>Cash</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="gateway" class="form-label fw-bold">Gateway</label>
                                    <input type="text" name="gateway" id="gateway" class="form-control" value="{{ old('gateway') }}" placeholder="e.g., daraja, flutterwave">
                                </div>

                                <div class="col-md-6">
                                    <label for="gateway_transaction_id" class="form-label fw-bold">Gateway Transaction ID</label>
                                    <input type="text" name="gateway_transaction_id" id="gateway_transaction_id" class="form-control" value="{{ old('gateway_transaction_id') }}">
                                </div>

                                <div class="col-md-4">
                                    <label for="fees_amount" class="form-label fw-bold">Fees</label>
                                    <input type="number" step="0.01" min="0" name="fees_amount" id="fees_amount" class="form-control" value="{{ old('fees_amount',0) }}">
                                </div>

                                <div class="col-md-4">
                                    <label for="status" class="form-label fw-bold">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="pending" {{ old('status')=='pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="initiated" {{ old('status')=='initiated' ? 'selected' : '' }}>Initiated</option>
                                        <option value="succeeded" {{ old('status')=='succeeded' ? 'selected' : '' }}>Succeeded</option>
                                        <option value="failed" {{ old('status')=='failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="refunded" {{ old('status')=='refunded' ? 'selected' : '' }}>Refunded</option>
                                        <option value="cancelled" {{ old('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="notes" class="form-label fw-bold">Notes</label>
                                    <input type="text" name="notes" id="notes" class="form-control" value="{{ old('notes') }}">
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fa fa-save me-2"></i> Save Payment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.js')
</body>
</html>
