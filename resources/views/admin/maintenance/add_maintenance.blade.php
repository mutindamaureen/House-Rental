{{-- <!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Add Maintenance Request</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Add Maintenance Request</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ url('upload_maintenancerequest') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <!-- Tenant -->
                                <div class="col-md-6">
                                    <label for="tenant_id" class="form-label fw-bold">Tenant</label>
                                    <select name="tenant_id" id="tenant_id" class="form-control" required>
                                        <option value="">Select Tenant</option>
                                        @foreach ($tenants as $tenant)
                                            <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->name }} ({{ $tenant->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Landlord -->
                                <div class="col-md-6">
                                    <label for="landlord_id" class="form-label fw-bold">Landlord</label>
                                    <select name="landlord_id" id="landlord_id" class="form-control">
                                        <option value="">Select Landlord (optional)</option>
                                        @foreach ($landlords as $landlord)
                                            <option value="{{ $landlord->id }}" {{ old('landlord_id') == $landlord->id ? 'selected' : '' }}>
                                                {{ $landlord->name }} ({{ $landlord->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- House -->
                                <div class="col-md-6">
                                    <label for="house_name" class="form-label fw-bold">House</label>
                                    <select name="house_name" id="house_name" class="form-control" required>
                                        <option value="">Select House</option>
                                        @foreach ($houses as $house)
                                            <option value="{{ $house->title }}" {{ old('house_name') == $house->title ? 'selected' : '' }}>
                                                {{ $house->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Subject -->
                                <div class="col-md-6">
                                    <label for="subject" class="form-label fw-bold">Subject</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="subject"
                                        name="subject"
                                        placeholder="Enter maintenance subject"
                                        value="{{ old('subject') }}"
                                        required
                                    >
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <label for="description" class="form-label fw-bold">Description</label>
                                    <textarea
                                        class="form-control"
                                        id="description"
                                        name="description"
                                        rows="4"
                                        placeholder="Describe the issue"
                                        required
                                    >{{ old('description') }}</textarea>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <!-- Submit -->
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fa fa-wrench me-2"></i> Add Maintenance Request
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
</html> --}}

<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Add Maintenance Request</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Add Maintenance Request</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ url('upload_maintenancerequest') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <!-- Tenant -->
                                <div class="col-md-6">
                                    <label for="tenant_id" class="form-label fw-bold">Tenant <span class="text-danger">*</span></label>
                                    <select name="tenant_id" id="tenant_id" class="form-control" required>
                                        <option value="">Select Tenant</option>
                                        @foreach ($tenants as $tenant)
                                            <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->name }} ({{ $tenant->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select tenant first to auto-fill house and landlord</small>
                                </div>

                                <!-- House Name (Auto-populated) -->
                                <div class="col-md-6">
                                    <label for="house_name" class="form-label fw-bold">House <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        name="house_name"
                                        id="house_name"
                                        class="form-control"
                                        readonly
                                        required
                                        placeholder="Will auto-fill when tenant is selected"
                                        value="{{ old('house_name') }}"
                                        style="background-color: #f8f9fa;"
                                    >
                                    <small class="text-muted">Auto-filled from tenant selection</small>
                                </div>

                                <!-- Landlord (Auto-populated) -->
                                <div class="col-md-6">
                                    <label for="landlord_id" class="form-label fw-bold">Landlord</label>
                                    <select name="landlord_id" id="landlord_id" class="form-control" style="background-color: #f8f9fa;">
                                        <option value="">Select Landlord (optional)</option>
                                        @foreach ($landlords as $landlord)
                                            <option value="{{ $landlord->id }}" {{ old('landlord_id') == $landlord->id ? 'selected' : '' }}>
                                                {{ $landlord->name }} ({{ $landlord->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Auto-filled from tenant selection</small>
                                </div>

                                <!-- Subject -->
                                <div class="col-md-6">
                                    <label for="subject" class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="subject"
                                        name="subject"
                                        placeholder="e.g., Leaking Pipe, Broken Door"
                                        value="{{ old('subject') }}"
                                        required
                                    >
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <label for="description" class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                                    <textarea
                                        class="form-control"
                                        id="description"
                                        name="description"
                                        rows="4"
                                        placeholder="Describe the maintenance issue in detail"
                                        required
                                    >{{ old('description') }}</textarea>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <!-- Submit -->
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fa fa-wrench me-2"></i> Add Maintenance Request
                                    </button>
                                    <a href="{{ url('view_maintenancerequest') }}" class="btn btn-secondary px-5 ms-2">
                                        <i class="fa fa-times me-2"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.js')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tenantSelect = document.getElementById('tenant_id');
        const houseInput = document.getElementById('house_name');
        const landlordSelect = document.getElementById('landlord_id');

        // Tenant data from controller
        const tenantData = @json($tenantData ?? []);

        tenantSelect.addEventListener('change', function() {
            const tenantId = this.value;

            if (!tenantId) {
                houseInput.value = '';
                landlordSelect.value = '';
                return;
            }

            // Use the data passed from controller
            if (tenantData[tenantId]) {
                houseInput.value = tenantData[tenantId].house_name || '';
                landlordSelect.value = tenantData[tenantId].landlord_id || '';

                // Show success feedback for both fields
                houseInput.classList.add('is-valid');
                landlordSelect.classList.add('is-valid');
                setTimeout(() => {
                    houseInput.classList.remove('is-valid');
                    landlordSelect.classList.remove('is-valid');
                }, 2000);
            } else {
                // Fallback to AJAX if data not available
                fetch(`/admin/tenant-details/${tenantId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            houseInput.value = data.data.house_name || '';
                            landlordSelect.value = data.data.landlord_id || '';

                            // Show success feedback for both fields
                            houseInput.classList.add('is-valid');
                            landlordSelect.classList.add('is-valid');
                            setTimeout(() => {
                                houseInput.classList.remove('is-valid');
                                landlordSelect.classList.remove('is-valid');
                            }, 2000);
                        } else {
                            alert('Could not fetch tenant details');
                            houseInput.value = '';
                            landlordSelect.value = '';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while fetching tenant details');
                        houseInput.value = '';
                        landlordSelect.value = '';
                    });
            }
        });
    });
    </script>
</body>
</html>
