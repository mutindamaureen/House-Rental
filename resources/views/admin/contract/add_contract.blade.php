<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
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
                                <select name="landlord_id" class="form-control" required>
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
                                <select name="tenant_id" class="form-control" required>
                                    <option value="">-- Select Tenant --</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->name }} ({{ $tenant->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">House <span class="text-danger">*</span></label>
                                <select name="house_id" class="form-control" required>
                                    <option value="">-- Select House --</option>
                                    @foreach($houses as $house)
                                        <option value="{{ $house->id }}" {{ old('house_id') == $house->id ? 'selected' : '' }}>
                                            {{ $house->title }} - {{ $house->location }} (Ksh {{ number_format($house->price) }})
                                        </option>
                                    @endforeach
                                </select>
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
</body>
</html>
