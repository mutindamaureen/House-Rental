<!DOCTYPE html>
<html>
<head>
    @include('admin.css')
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Edit Maintenance Request</h3>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ url('update_maintenancerequest', $maintenance->id) }}" method="post">
                            @csrf

                            <div class="row g-3">
                                <!-- Tenant -->
                                <div class="col-md-6">
                                    <label for="tenant_id" class="form-label fw-bold">
                                        Select Tenant <span class="text-danger">*</span>
                                    </label>
                                    <select name="tenant_id" id="tenant_id" class="form-control" required>
                                        <option value="">-- Select a Tenant --</option>
                                        @foreach($tenants as $tenant)
                                            <option value="{{ $tenant->id }}"
                                                {{ $maintenance->tenant_id == $tenant->id ? 'selected' : '' }}>
                                                {{ $tenant->name }} ({{ $tenant->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Landlord -->
                                <div class="col-md-6">
                                    <label for="landlord_id" class="form-label fw-bold">
                                        Select Landlord
                                    </label>
                                    <select name="landlord_id" id="landlord_id" class="form-control">
                                        <option value="">-- Select a Landlord --</option>
                                        @foreach($landlords as $landlord)
                                            <option value="{{ $landlord->id }}"
                                                {{ $maintenance->landlord_id == $landlord->id ? 'selected' : '' }}>
                                                {{ $landlord->name }} ({{ $landlord->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- House -->
                                <div class="col-md-6">
                                    <label for="house_name" class="form-label fw-bold">
                                        House Name <span class="text-danger">*</span>
                                    </label>
                                    <select name="house_name" id="house_name" class="form-control" required>
                                        <option value="">-- Select a House --</option>
                                        @foreach($houses as $house)
                                            <option value="{{ $house->title }}"
                                                {{ $maintenance->house_name == $house->title ? 'selected' : '' }}>
                                                {{ $house->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Subject -->
                                <div class="col-md-6">
                                    <label for="subject" class="form-label fw-bold">
                                        Subject <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="subject"
                                        name="subject"
                                        value="{{ $maintenance->subject }}"
                                        required
                                        placeholder="Enter maintenance subject">
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <label for="description" class="form-label fw-bold">
                                        Description <span class="text-danger">*</span>
                                    </label>
                                    <textarea
                                        name="description"
                                        id="description"
                                        class="form-control"
                                        rows="4"
                                        required
                                        placeholder="Describe the maintenance issue...">{{ $maintenance->description }}</textarea>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="pending" {{ $maintenance->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $maintenance->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $maintenance->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $maintenance->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>

                                <!-- Created At (Read only) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Created At</label>
                                    <input type="text" class="form-control" value="{{ $maintenance->created_at->format('Y-m-d H:i') }}" readonly>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fa fa-edit me-2"></i> Update Maintenance
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
</body>
</html>
