<!DOCTYPE html>
<html>
<head>
    @include('admin.css')

    <style>
        /* Make the table scrollable without changing colors */
        .table-container {
            max-height: 70vh; /* 70% of viewport height */
            overflow-y: auto;
            overflow-x: auto;
        }

        /* Keep the table header visible while scrolling vertically */
        .table thead th {
            position: sticky;
            top: 0;
            background-color: inherit; /* Keep your current background color */
            z-index: 10;
        }
    </style>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <!-- Sidebar Navigation end-->
        <div class="page-content">
            <div class="container-fluid">

                <div class="container mt-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h2 class="text-center mb-4 text-primary">Tenants</h2>

                            <!-- Scrollable wrapper -->
                            <div class="table-container">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-primary">
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">National ID</th>
                                            <th scope="col">House</th>
                                            <th scope="col">Landlord</th>
                                            <th scope="col">Rent</th>
                                            <th scope="col">Utilities</th>
                                            <th scope="col">Total Rent</th>
                                            <th scope="col">Security Deposit</th>
                                            <th scope="col">Lease Start</th>
                                            <th scope="col">Lease End</th>
                                            <th scope="col">Emergency Contact</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tenants as $tenant)
                                        <tr>
                                            <td>{{ $tenant->user->name ?? 'N/A' }}</td>
                                            <td>{{ $tenant->user->email ?? 'N/A' }}</td>
                                            <td>{{ $tenant->user->phone ?? 'N/A' }}</td>
                                            <td>{{ $tenant->national_id ?? 'N/A' }}</td>
                                            <td>{{ $tenant->house->title ?? 'N/A' }}</td>
                                            <td>{{ $tenant->landlord->name ?? 'N/A' }}</td>
                                            <td>KES {{ number_format($tenant->rent, 2) }}</td>
                                            <td>KES {{ number_format($tenant->utilities ?? 0, 2) }}</td>
                                            <td>KES {{ number_format($tenant->total_rent, 2) }}</td>
                                            <td>KES {{ number_format($tenant->security_deposit ?? 0, 2) }}</td>
                                            <td>{{ $tenant->lease_start_date ? \Carbon\Carbon::parse($tenant->lease_start_date)->format('d M Y') : 'N/A' }}</td>
                                            <td>{{ $tenant->lease_end_date ? \Carbon\Carbon::parse($tenant->lease_end_date)->format('d M Y') : 'N/A' }}</td>
                                            <td>
                                                @if($tenant->emergency_contact_name)
                                                    {{ $tenant->emergency_contact_name }}<br>
                                                    <small class="text-muted">{{ $tenant->emergency_contact_phone }}</small>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-success btn-sm" href="{{ url('edit_tenant', $tenant->id) }}">Edit</a>
                                                <a class="btn btn-danger btn-sm" onclick="confirmation(event)" href="{{ url('delete_tenant', $tenant->id) }}">Delete</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="13" class="text-center text-muted">No tenants found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- End scrollable wrapper -->

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('admin.js')
</body>
</html>
