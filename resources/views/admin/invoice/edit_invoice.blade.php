<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Edit Invoice</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Edit Invoice - {{ $invoice->reference ?? '—' }}</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="tenant_id" class="form-label fw-bold">Tenant <span class="text-danger">*</span></label>
                                    <select name="tenant_id" id="tenant_id" class="form-control @error('tenant_id') is-invalid @enderror" required>
                                        <option value="">Select Tenant</option>
                                        @foreach($tenants as $tenant)
                                            <option value="{{ $tenant->id }}" {{ (old('tenant_id', $invoice->tenant_id) == $tenant->id) ? 'selected' : '' }}>
                                                {{ optional($tenant->user)->name ?? 'N/A' }}{{ optional($tenant->user)->email ? ' - ' . optional($tenant->user)->email : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tenant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="house_id" class="form-label fw-bold">House <span class="text-danger">*</span></label>
                                    <select name="house_id" id="house_id" class="form-control @error('house_id') is-invalid @enderror" required>
                                        <option value="">Select House</option>
                                        @foreach($houses as $house)
                                            <option value="{{ $house->id }}" {{ (old('house_id', $invoice->house_id) == $house->id) ? 'selected' : '' }}>
                                                {{ $house->title ?? 'N/A' }}{{ $house->location ? ' - ' . $house->location : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('house_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-8">
                                    <label for="amount" class="form-label fw-bold">Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" value="{{ old('amount', $invoice->amount) }}" required>
                                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="currency" class="form-label fw-bold">Currency</label>
                                    <input type="text" name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $invoice->currency ?? 'KES') }}" maxlength="3">
                                    @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $invoice->description) }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="issued_date" class="form-label fw-bold">Issued Date</label>
                                    <input type="date" name="issued_date" id="issued_date" class="form-control @error('issued_date') is-invalid @enderror" value="{{ old('issued_date', optional($invoice->issued_date)->format('Y-m-d')) }}">
                                    @error('issued_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="due_date" class="form-label fw-bold">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}" required>
                                    @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        @php $st = old('status', $invoice->status ?? 'unpaid'); @endphp
                                        <option value="unpaid" {{ $st == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        <option value="paid" {{ $st == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="overdue" {{ $st == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                        <option value="cancelled" {{ $st == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="paid_amount" class="form-label fw-bold">Paid Amount</label>
                                    <input type="number" name="paid_amount" id="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" step="0.01" value="{{ old('paid_amount', $invoice->paid_amount) }}">
                                    @error('paid_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="form-text text-muted">Leave blank or 0 if none.</small>
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary me-3">
                                        <i class="fas fa-arrow-left me-1"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-1"></i> Update Invoice
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div> <!-- card-body -->
                </div> <!-- card -->
            </div> <!-- container -->
        </div> <!-- page-content -->
    </div> <!-- d-flex -->

    @include('admin.js')
</body>
</html>
