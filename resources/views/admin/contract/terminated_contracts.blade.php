<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>View Contracts</title>
</head>
<body>
@include('admin.header')

<div class="d-flex align-items-stretch">
    @include('admin.sidebar')

    <div class="page-content">
        <div class="page-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Contracts Management</h2>
                    <a href="{{ url('/add_contract') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-1"></i> Add New Contract
                    </a>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        {{-- <div class="table-responsive"> --}}
                        <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">

                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Landlord</th>
                                        <th>Tenant</th>
                                        <th>House</th>
                                        <th>Status</th>
                                        <th>Termination</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $contract)
                                        <tr>
                                            <td>{{ $contract->id }}</td>
                                            <td>
                                                {{ $contract->landlord->name }}<br>
                                                <small class="text-muted">{{ $contract->landlord->email }}</small>
                                            </td>
                                            <td>
                                                {{ $contract->tenant->name }}<br>
                                                <small class="text-muted">{{ $contract->tenant->email }}</small>
                                            </td>
                                            <td>
                                                {{ $contract->house->title }}<br>
                                                <small class="text-muted">{{ $contract->house->location }}</small>
                                            </td>
                                            <td>
                                                @if($contract->status === 'signed')
                                                    <span class="badge bg-success">Signed</span>
                                                @elseif($contract->status === 'terminated')
                                                    <span class="badge bg-danger">Terminated</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($contract->termination_status === 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($contract->termination_status === 'partial')
                                                    <span class="badge bg-info">Partial</span>
                                                @elseif($contract->termination_status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $contract->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ url('/view_contract_details/' . $contract->id) }}"
                                                   class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                <a href="{{ url('/download_contract/' . $contract->id) }}"
                                                   class="btn btn-sm btn-secondary" title="Download">
                                                    <i class="fa fa-download"></i>
                                                </a>

                                                @if($contract->status === 'signed' && !$contract->termination_status)
                                                    <form action="{{ url('/contract/initiate-termination/' . $contract->id) }}"
                                                          method="POST"
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Are you sure you want to initiate contract termination?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning" title="Initiate Termination">
                                                            <i class="fa fa-ban"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if(in_array($contract->termination_status, ['pending', 'partial', 'completed']))
                                                    <a href="{{ url('/contract/termination/' . $contract->id) }}"
                                                       class="btn btn-sm btn-warning" title="View Termination">
                                                        <i class="fa fa-file-signature"></i>
                                                    </a>
                                                @endif

                                                @if($contract->status !== 'terminated')
                                                    <a href="{{ url('/edit_contract/' . $contract->id) }}"
                                                       class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                @endif

                                                <a href="{{ url('/delete_contract/' . $contract->id) }}"
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Are you sure you want to delete this contract?')"
                                                   title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No contracts found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $data->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.js')
</body>
</html>
