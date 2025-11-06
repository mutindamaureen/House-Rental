<!DOCTYPE html>
<html>
<head>
    @include('admin.css')
</head>
<body>
@include('admin.header')

<div class="d-flex align-items-stretch">
    @include('admin.sidebar')

    <!-- Sidebar Navigation end-->
    <div class="page-content">
        <div class="page-header">
            <div class="container-fluid">
                <h2 class="mb-4">Add New Landlord</h2>

                <!-- Landlord Form -->
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

                    <form action="{{ url('upload_landlord') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="user_id" class="form-label">Select User <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="">-- Select a User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Select an existing user to assign as landlord</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="national_id" class="form-label">National ID</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="national_id"
                                    id="national_id"
                                    placeholder="Enter national ID number"
                                    value="{{ old('national_id') }}"
                                >
                                <small class="form-text text-muted">Optional: Landlord's national identification number</small>
                            </div>

                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="company_name"
                                    id="company_name"
                                    placeholder="Enter company name (if applicable)"
                                    value="{{ old('company_name') }}"
                                >
                                <small class="form-text text-muted">Optional: Company or business name</small>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Save Landlord</button>
                            <a href="{{ url(path: 'view_landlord') }}" class="btn btn-secondary">Cancel</a>
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
