<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Contracts</title>

    <style>
        /* Container and layout styling */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 15px 0;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 600;
        }

        .btn {
            border-radius: 6px;
        }

        /* Table styling */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            min-width: 1100px;
            white-space: nowrap;
        }

        .table th, .table td {
            vertical-align: middle !important;
            text-align: center;
        }

        .table th {
            background-color: #e9ecef;
        }

        /* Badges for contract status */
        .badge {
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-signed {
            background-color: #28a745;
            color: white;
        }

        /* Action buttons */
        .action-buttons a {
            margin: 0 3px;
            padding: 6px 10px;
        }

        /* Empty state */
        .no-data {
            color: #6c757d;
            font-style: italic;
            text-align: center;
        }

        /* Pagination styling */
        .pagination {
            justify-content: center;
        }

        .pagination .page-link {
            color: #007bff;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>

<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3>Contract Management</h3>
                    </div>

                    <div class="card-body">
                        <div class="mb-3 text-end">
                            <a href="{{ url('/add_contract') }}" class="btn btn-success">
                                <i class="fa fa-plus"></i> Add New Contract
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th>ID</th>
                                        <th>Landlord</th>
                                        <th>Tenant</th>
                                        <th>House</th>
                                        <th>Status</th>
                                        <th>Signed Date</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($data as $contract)
                                    <tr>
                                        <td>{{ $contract->id }}</td>

                                        <td>
                                            {{ $contract->landlord->name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $contract->landlord->email ?? '' }}</small>
                                        </td>

                                        <td>
                                            {{ $contract->tenant->name ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $contract->tenant->email ?? '' }}</small>
                                        </td>

                                        <td>
                                            {{ $contract->house->title ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $contract->house->location ?? '' }}</small>
                                        </td>

                                        <td>
                                            @if($contract->status == 'signed')
                                                <span class="badge badge-signed">Signed</span>
                                            @else
                                                <span class="badge badge-pending">Pending</span>
                                            @endif
                                        </td>

                                        <td>{{ $contract->signed_at ? $contract->signed_at->format('M d, Y') : 'Not signed' }}</td>
                                        <td>{{ $contract->created_at->format('M d, Y') }}</td>

                                        <td class="action-buttons">
                                            <a href="{{ url('view_contract_details', $contract->id) }}" class="btn btn-info btn-sm" title="View Details">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ url('download_contract', $contract->id) }}" class="btn btn-success btn-sm" title="Download PDF">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <a href="{{ url('edit_contract', $contract->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a class="btn btn-danger btn-sm" onclick="confirmation(event)"
                                            href="{{ url('delete_contract', $contract->id) }}"><i class="fa fa-trash"></i></a>

                                            {{-- <a href="{{ url('delete_contract', $contract->id) }}" class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this contract?')" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </a> --}}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="no-data py-4">No contracts found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $data->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.js')
</body>
</html>
