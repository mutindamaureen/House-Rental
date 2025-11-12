<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Create Invoice</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Create New Invoice</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('invoices.store') }}" method="POST" novalidate>
                            @csrf

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
                                            <option value="{{ $tenant->id }}" data-house-id="{{ $tenant->house_id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
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
                                            <option value="{{ $house->id }}" data-price="{{ $house->price }}" {{ old('house_id') == $house->id ? 'selected' : '' }}>
                                                {{ $house->title ?? 'N/A' }}{{ $house->location ? ' - ' . $house->location : '' }}{{ isset($house->price) ? ' (' . number_format($house->price, 2) . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('house_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-8">
                                    <label for="amount" class="form-label fw-bold">Amount <span class="text-danger">*</span></label>
                                    <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0.01" value="{{ old('amount') }}" required>
                                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="currency" class="form-label fw-bold">Currency</label>
                                    <input type="text" name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', 'KES') }}" maxlength="3">
                                    @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description') }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="issued_date" class="form-label fw-bold">Issued Date</label>
                                    <input type="date" name="issued_date" id="issued_date" class="form-control @error('issued_date') is-invalid @enderror" value="{{ old('issued_date', now()->format('Y-m-d')) }}">
                                    @error('issued_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="due_date" class="form-label fw-bold">Due Date <span class="text-danger">*</span></label>
                                    <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date') }}" required>
                                    @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status</label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="unpaid" {{ old('status', 'unpaid') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                        <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary me-3">
                                        <i class="fas fa-arrow-left me-1"></i> Back
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-1"></i> Create Invoice
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

    <script>
    (function () {
        const tenantHouseRouteTemplate = "{{ route('tenants.house', ['tenant_id' => '__ID__']) }}";

        function getTenantHouseUrl(tenantId) {
            return tenantHouseRouteTemplate.replace('__ID__', tenantId);
        }

        const tenantSelect = document.getElementById('tenant_id');
        const houseSelect = document.getElementById('house_id');
        const amountInput = document.getElementById('amount');

        if (tenantSelect) {
            tenantSelect.addEventListener('change', function () {
                const tenantId = this.value;
                if (!tenantId) return;

                fetch(getTenantHouseUrl(tenantId), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.house) {
                        const house = data.house;

                        let option = houseSelect.querySelector('option[value="' + house.id + '"]');
                        if (!option) {
                            option = document.createElement('option');
                            option.value = house.id;
                            option.text = house.title + (house.location ? ' - ' + house.location : '');
                            option.setAttribute('data-price', house.price ?? '');
                            houseSelect.appendChild(option);
                        }

                        houseSelect.value = house.id;
                        if (house.price !== undefined && house.price !== null) {
                            amountInput.value = parseFloat(house.price).toFixed(2);
                        }
                    }
                })
                .catch(err => {
                    console.error('Error fetching tenant house:', err);
                });
            });
        }

        if (houseSelect) {
            houseSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption ? selectedOption.getAttribute('data-price') : null;
                if (price) {
                    amountInput.value = parseFloat(price).toFixed(2);
                }
            });
        }
    })();
    </script>
</body>
</html>
