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
                        <h3 class="mb-0">Update Landlord</h3>
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

                        <form action="{{ url('update_landlord', $landlord->id) }}" method="post">
                            @csrf

                            <div class="row g-3">
                                <!-- User Selection -->
                                <div class="col-12">
                                    <label for="user_id" class="form-label fw-bold">
                                        Select User <span class="text-danger">*</span>
                                    </label>
                                    <select name="user_id" id="user_id" class="form-control" required>
                                        <option value="">-- Select a User --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ $landlord->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Select the user associated with this landlord</small>
                                </div>

                                <!-- Current User Information (Read-only display) -->
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <strong>Current Landlord Information:</strong><br>
                                        <strong>Name:</strong> {{ $landlord->user->name ?? 'N/A' }}<br>
                                        <strong>Email:</strong> {{ $landlord->user->email ?? 'N/A' }}<br>
                                        <strong>Phone:</strong> {{ $landlord->user->phone ?? 'N/A' }}<br>
                                        <strong>Address:</strong> {{ $landlord->user->address ?? 'N/A' }}
                                    </div>
                                </div>

                                <!-- National ID -->
                                <div class="col-md-6">
                                    <label for="national_id" class="form-label fw-bold">National ID</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="national_id"
                                        name="national_id"
                                        value="{{ $landlord->national_id }}"
                                        placeholder="Enter national ID number"
                                    >
                                    <small class="form-text text-muted">Optional: Landlord's national identification number</small>
                                </div>

                                <!-- Company Name -->
                                <div class="col-md-6">
                                    <label for="company_name" class="form-label fw-bold">Company Name</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="company_name"
                                        name="company_name"
                                        value="{{ $landlord->company_name }}"
                                        placeholder="Enter company name (if applicable)"
                                    >
                                    <small class="form-text text-muted">Optional: Company or business name</small>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fa fa-edit me-2"></i> Update Landlord
                                    </button>
                                    <a href="{{ url('view_landlord') }}" class="btn btn-secondary px-5 ms-2">
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

    <!-- JavaScript files-->
    @include('admin.js')
</body>
</html>
