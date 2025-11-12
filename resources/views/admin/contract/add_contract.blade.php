<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add New Contract</title>
</head>
<body>
@include('admin.header')

<div class="d-flex align-items-stretch">
    @include('admin.sidebar')

    <div class="page-content">
        <div class="page-header">
            <div class="container-fluid">
                <h2 class="mb-4">Add New Contract</h2>

                <!-- Card Form Container -->
                <div class="card shadow-sm p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('/upload_contract') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Landlord <span class="text-danger">*</span></label>
                                <select name="landlord_id" id="landlord_id" class="form-control" required>
                                    <option value="">-- Select Landlord --</option>
                                    @foreach($landlords as $landlord)
                                        <option value="{{ $landlord->id }}" {{ old('landlord_id') == $landlord->id ? 'selected' : '' }}>
                                            {{ $landlord->name }} ({{ $landlord->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('landlord_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tenant <span class="text-danger">*</span></label>
                                <select name="tenant_id" id="tenant_id" class="form-control" required disabled>
                                    <option value="">-- Select Landlord First --</option>
                                </select>
                                <small class="text-muted d-block mt-1">Select a landlord to view available tenants</small>
                                @error('tenant_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">House <span class="text-danger">*</span></label>
                                <select name="house_id" id="house_id" class="form-control" required disabled>
                                    <option value="">-- Select Tenant First --</option>
                                </select>
                                <small class="text-muted d-block mt-1">House will auto-populate when tenant is selected</small>
                                @error('house_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Contract PDF <span class="text-danger">*</span></label>
                                <input type="file" name="contract_pdf" class="form-control" accept=".pdf" required>
                                <small class="text-muted">Upload a valid PDF document (max 10MB)</small>
                                @error('contract_pdf')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-control">
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="signed" {{ old('status') == 'signed' ? 'selected' : '' }}>Signed</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-1"></i> Save Contract
                            </button>
                            <a href="{{ url('/view_contract') }}" class="btn btn-secondary">
                                <i class="fa fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // When landlord is selected, fetch tenants
    $('#landlord_id').on('change', function() {
        const landlordId = $(this).val();
        const tenantSelect = $('#tenant_id');
        const houseSelect = $('#house_id');

        // Reset tenant and house dropdowns
        tenantSelect.html('<option value="">-- Loading Tenants... --</option>').prop('disabled', true);
        houseSelect.html('<option value="">-- Select Tenant First --</option>').prop('disabled', true);

        if (landlordId) {
            // Fetch tenants for selected landlord
            $.ajax({
                url: `{{ url('/get_tenants_by_landlord') }}/${landlordId}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Tenants Response:', response); // Debug
                    tenantSelect.html('<option value="">-- Select Tenant --</option>');

                    if (response && response.length > 0) {
                        $.each(response, function(index, tenant) {
                            tenantSelect.append(
                                `<option value="${tenant.id}">${tenant.name} (${tenant.email})</option>`
                            );
                        });
                        tenantSelect.prop('disabled', false);
                    } else {
                        tenantSelect.html('<option value="">-- No Tenants Found for this Landlord --</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching tenants:', error);
                    console.error('Response:', xhr.responseText); // Debug
                    tenantSelect.html('<option value="">-- Error Loading Tenants --</option>');
                    alert('Failed to load tenants. Please check console for details.');
                }
            });
        } else {
            tenantSelect.html('<option value="">-- Select Landlord First --</option>');
        }
    });

    // When tenant is selected, fetch and auto-populate house
    $('#tenant_id').on('change', function() {
        const tenantId = $(this).val();
        const houseSelect = $('#house_id');

        houseSelect.html('<option value="">-- Loading House... --</option>').prop('disabled', true);

        if (tenantId) {
            // Fetch house for selected tenant
            $.ajax({
                url: `{{ url('/get_house_by_tenant') }}/${tenantId}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('House Response:', response); // Debug
                    if (response.house) {
                        const house = response.house;
                        const formattedPrice = new Intl.NumberFormat().format(house.price);
                        houseSelect.html(
                            `<option value="${house.id}" selected>${house.title} - ${house.location} (Ksh ${formattedPrice})</option>`
                        );
                        houseSelect.prop('disabled', false);
                    } else {
                        houseSelect.html('<option value="">-- No House Assigned --</option>');
                        alert('This tenant does not have a house assigned yet.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching house:', error);
                    console.error('Response:', xhr.responseText); // Debug
                    houseSelect.html('<option value="">-- Error Loading House --</option>');
                    alert('Failed to load house information. Please check console for details.');
                }
            });
        } else {
            houseSelect.html('<option value="">-- Select Tenant First --</option>');
        }
    });
});
</script>

</body>
</html>
