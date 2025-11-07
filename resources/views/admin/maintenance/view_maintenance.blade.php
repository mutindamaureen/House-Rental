<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Maintenance Requests</title>
    <style>
        .description-short {
            display: inline;
        }
        .description-full {
            display: none;
        }
        .more-link {
            color: #007bff;
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.875rem;
            margin-left: 5px;
        }
        .more-link:hover {
            color: #0056b3;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table {
            min-width: 1200px;
            white-space: nowrap;
        }
        .table td, .table th {
            white-space: normal;
            min-width: 100px;
        }
        .status-badge {
            font-weight: 600;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-in_progress { background-color: #cce5ff; color: #004085; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container-fluid">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Maintenance Requests</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th scope="col">Tenant</th>
                                        <th scope="col">Landlord</th>
                                        <th scope="col">House</th>
                                        <th scope="col">Subject</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $item->tenant->name ?? 'N/A' }}</td>
                                            <td>{{ $item->landlord->name ?? 'N/A' }}</td>
                                            <td>{{ $item->house_name }}</td>
                                            <td>{{ $item->subject }}</td>
                                            <td>
                                                <span class="description-short" id="short-{{ $item->id }}">
                                                    {{ Str::limit($item->description, 20, '') }}
                                                </span>
                                                <span class="description-full" id="full-{{ $item->id }}">
                                                    {{ $item->description }}
                                                </span>
                                                @if(strlen($item->description) > 20)
                                                    <span class="more-link" onclick="toggleDescription({{ $item->id }})" id="toggle-{{ $item->id }}">...more</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="status-badge status-{{ $item->status }}">
                                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $item->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ url('edit_maintenancerequest', $item->id) }}" class="btn btn-success btn-sm">Edit</a>
                                                <a href="{{ url('delete_maintenancerequest', $item->id) }}" onclick="confirmation(event)" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $data->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.js')

    <script>
        function toggleDescription(id) {
            const shortText = document.getElementById('short-' + id);
            const fullText = document.getElementById('full-' + id);
            const toggleLink = document.getElementById('toggle-' + id);

            if (fullText.style.display === 'none') {
                fullText.style.display = 'inline';
                shortText.style.display = 'none';
                toggleLink.textContent = ' show less';
            } else {
                fullText.style.display = 'none';
                shortText.style.display = 'inline';
                toggleLink.textContent = '...more';
            }
        }
    </script>
</body>
</html>
