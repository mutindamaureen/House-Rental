{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Contract</title>
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
                        <h3 class="mb-0">Edit Contract #{{ $contract->id }}</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ url('/update_contract', $contract->id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-bold">Landlord <span class="text-danger">*</span></label>
                                <select name="landlord_id" class="form-control" required>
                                    <option value="">Select Landlord</option>
                                    @foreach($landlords as $landlord)
                                        <option value="{{ $landlord->id }}" {{ $contract->landlord_id == $landlord->id ? 'selected' : '' }}>
                                            {{ $landlord->name }} - {{ $landlord->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('landlord_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tenant <span class="text-danger">*</span></label>
                                <select name="tenant_id" class="form-control" required>
                                    <option value="">Select Tenant</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ $contract->tenant_id == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->name }} - {{ $tenant->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">House <span class="text-danger">*</span></label>
                                <select name="house_id" class="form-control" required>
                                    <option value="">Select House</option>
                                    @foreach($houses as $house)
                                        <option value="{{ $house->id }}" {{ $contract->house_id == $house->id ? 'selected' : '' }}>
                                            {{ $house->title }} - {{ $house->location }} (Ksh {{ number_format($house->price) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('house_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Contract PDF</label>
                                <input type="file" name="contract_pdf" class="form-control" accept=".pdf">
                                <small class="text-muted d-block mt-1">Leave empty to keep current file. Upload a new PDF to replace (max 10MB).</small>

                                @if($contract->contract_pdf)
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <strong>Current File:</strong> {{ $contract->contract_pdf }}
                                        <a href="{{ url('download_contract', $contract->id) }}" class="btn btn-success btn-sm ms-2">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                @endif
                                @error('contract_pdf')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" {{ $contract->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="signed" {{ $contract->status == 'signed' ? 'selected' : '' }}>Signed</option>
                                </select>
                            </div>

                            @if($contract->signed_at)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Signed Date</label>
                                <input type="text" class="form-control" value="{{ $contract->signed_at->format('M d, Y H:i') }}" readonly>
                            </div>
                            @endif

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-4 me-2">
                                    <i class="fa fa-save me-2"></i> Update Contract
                                </button>
                                <a href="{{ url('/view_contract') }}" class="btn btn-secondary px-4">
                                    <i class="fa fa-times me-2"></i> Cancel
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
</html> --}}



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Contract</title>
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
                        <h3 class="mb-0">Edit Contract #{{ $contract->id }}</h3>
                    </div>

                    <div class="card-body">
                        <!-- Termination Status Alert -->
                        @if($contract->termination_status)
                        <div class="alert alert-warning mb-4">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Termination Status:</strong> {{ ucfirst($contract->termination_status) }}
                            {{-- @if($contract->termination_initiated_at)
                                <br><small>Initiated on: {{ $contract->termination_initiated_at->format('M d, Y H:i') }}</small>
                            @endif --}}
                            @if($contract->termination_initiated_at)
                                <br><small>Initiated on: {{ \Carbon\Carbon::parse($contract->termination_initiated_at)->format('M d, Y H:i') }}</small>
                            @endif
                        </div>
                        @endif

                        <form action="{{ url('/update_contract', $contract->id) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-bold">Landlord <span class="text-danger">*</span></label>
                                <select name="landlord_id" class="form-control" required>
                                    <option value="">Select Landlord</option>
                                    @foreach($landlords as $landlord)
                                        <option value="{{ $landlord->id }}" {{ $contract->landlord_id == $landlord->id ? 'selected' : '' }}>
                                            {{ $landlord->name }} - {{ $landlord->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('landlord_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tenant <span class="text-danger">*</span></label>
                                <select name="tenant_id" class="form-control" required>
                                    <option value="">Select Tenant</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ $contract->tenant_id == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->name }} - {{ $tenant->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tenant_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">House <span class="text-danger">*</span></label>
                                <select name="house_id" class="form-control" required>
                                    <option value="">Select House</option>
                                    @foreach($houses as $house)
                                        <option value="{{ $house->id }}" {{ $contract->house_id == $house->id ? 'selected' : '' }}>
                                            {{ $house->title }} - {{ $house->location }} (Ksh {{ number_format($house->price) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('house_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Contract PDF</label>
                                <input type="file" name="contract_pdf" class="form-control" accept=".pdf">
                                <small class="text-muted d-block mt-1">Leave empty to keep current file. Upload a new PDF to replace (max 10MB).</small>

                                @if($contract->contract_pdf)
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <strong>Current File:</strong> {{ $contract->contract_pdf }}
                                        <a href="{{ url('download_contract', $contract->id) }}" class="btn btn-success btn-sm ms-2">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                @endif
                                @error('contract_pdf')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" {{ $contract->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="signed" {{ $contract->status == 'signed' ? 'selected' : '' }}>Signed</option>
                                </select>
                            </div>

                            @if($contract->signed_at)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Signed Date</label>
                                <input type="text" class="form-control" value="{{ $contract->signed_at->format('M d, Y H:i') }}" readonly>
                            </div>
                            @endif

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-4 me-2">
                                    <i class="fa fa-save me-2"></i> Update Contract
                                </button>
                                <a href="{{ url('/view_contract') }}" class="btn btn-secondary px-4 me-2">
                                    <i class="fa fa-times me-2"></i> Cancel
                                </a>

                                <!-- Terminate Contract Button -->
                                @if($contract->status == 'signed' && !$contract->termination_status)
                                <button type="button" class="btn btn-danger px-4" onclick="confirmTermination()">
                                    <i class="fa fa-ban me-2"></i> Terminate Contract
                                </button>
                                @endif

                                @if($contract->termination_status && $contract->termination_status != 'completed')
                                <a href="{{ url('/contract/termination', $contract->id) }}" class="btn btn-warning px-4">
                                    <i class="fa fa-eye me-2"></i> View Termination
                                </a>
                                @endif
                            </div>
                        </form>

                        <!-- Hidden form for termination -->
                        <form id="terminateForm" action="{{ url('/contract/initiate-termination', $contract->id) }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('admin.js')

    <script>
        function confirmTermination() {
            if (confirm('Are you sure you want to initiate termination for this contract? Both landlord and tenant will be notified and required to sign.')) {
                document.getElementById('terminateForm').submit();
            }
        }
    </script>
</body>
</html>
