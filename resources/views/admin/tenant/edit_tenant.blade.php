
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
                <h2 class="mb-4">Update Tenant</h2>

                <!-- Tenant Form -->
                <div class="card shadow-sm p-4">
                    <form action="{{ url('update_tenant', $tenants->id) }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">Tenant (User)</label>
                                <select name="user_id" id="user_id" class="form-control" required>
                                    <option value="{{ $tenants->user_id }}" selected>{{ $tenants->user->name ?? 'Current User' }} ({{ $tenants->user->email ?? 'N/A' }})</option>
                                    @foreach($users as $user)
                                        @if($user->id != $tenants->user_id)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="house_id" class="form-label">House</label>
                                <select name="house_id" id="house_id" class="form-control" required>
                                    <option value="{{ $tenants->house_id }}" selected>{{ $tenants->house->title ?? 'Current House' }} - {{ $tenants->house->location ?? '' }}</option>
                                    @foreach($houses as $house)
                                        @if($house->id != $tenants->house_id)
                                            <option value="{{ $house->id }}">{{ $house->title }} - {{ $house->location }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="landlord_id" class="form-label">Landlord</label>
                                <select name="landlord_id" id="landlord_id" class="form-control">
                                    <option value="">-- Select Landlord --</option>
                                    @foreach($landlords as $landlord)
                                        <option value="{{ $landlord->id }}" {{ $tenants->landlord_id == $landlord->id ? 'selected' : '' }}>
                                            {{ $landlord->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="national_id" class="form-label">National ID</label>
                                <input type="text" class="form-control" value="{{ $tenants->national_id }}" name="national_id" placeholder="Enter tenant ID number">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="rent" class="form-label">Rent (Ksh)</label>
                                <input type="number" step="0.01" class="form-control" value="{{ $tenants->rent }}" name="rent" placeholder="e.g. 15000" required>
                            </div>

                            <div class="col-md-4">
                                <label for="utilities" class="form-label">Utilities (Ksh)</label>
                                <input type="number" step="0.01" class="form-control" value="{{ $tenants->utilities }}" name="utilities" placeholder="e.g. 1000">
                            </div>

                            <div class="col-md-4">
                                <label for="security_deposit" class="form-label">Security Deposit (Ksh)</label>
                                <input type="number" step="0.01" class="form-control" value="{{ $tenants->security_deposit }}" name="security_deposit" placeholder="e.g. 5000">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="lease_start_date" class="form-label">Lease Start Date</label>
                                <input type="date" class="form-control" value="{{ $tenants->lease_start_date }}" name="lease_start_date">
                            </div>

                            <div class="col-md-6">
                                <label for="lease_end_date" class="form-label">Lease End Date</label>
                                <input type="date" class="form-control" value="{{ $tenants->lease_end_date }}" name="lease_end_date">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                <input type="text" class="form-control" value="{{ $tenants->emergency_contact_name }}" name="emergency_contact_name" placeholder="Contact name">
                            </div>

                            <div class="col-md-6">
                                <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                <input type="text" class="form-control" value="{{ $tenants->emergency_contact_phone }}" name="emergency_contact_phone" placeholder="e.g. 0712345678">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Update Tenant</button>
                            <a href="{{ url('view_tenant') }}" class="btn btn-secondary">Cancel</a>
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
