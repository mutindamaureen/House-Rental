

<!DOCTYPE html>
<html>
<head>
    @include('admin.css')
    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table {
            min-width: 1000px;
            white-space: nowrap;
        }
        .table td, .table th {
            white-space: normal;
            min-width: 100px;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .houses-list {
            max-height: 150px;
            overflow-y: auto;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-top: 5px;
        }
        .house-item {
            padding: 5px;
            margin-bottom: 5px;
            background-color: white;
            border-left: 3px solid #007bff;
            border-radius: 3px;
            font-size: 0.9rem;
        }
        .house-item:last-child {
            margin-bottom: 0;
        }
        .no-houses {
            color: #6c757d;
            font-style: italic;
            font-size: 0.9rem;
        }
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
                        <h3 class="mb-0">Landlord List</h3>
                    </div>

                    <div class="card-body">
                        <div class="mb-3 text-end">
                            <a href="{{ url('add_landlord') }}" class="btn btn-success">
                                <i class="fa fa-plus"></i> Add New Landlord
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">National ID</th>
                                        <th scope="col">Company Name</th>
                                        <th scope="col">Houses</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @forelse ($landlords as $index => $landlord)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $landlord->user->name ?? 'N/A' }}</td>
                                        <td>{{ $landlord->user->email ?? 'N/A' }}</td>
                                        <td>{{ $landlord->user->phone ?? 'N/A' }}</td>
                                        <td>{{ $landlord->user->address ?? 'N/A' }}</td>
                                        <td>{{ $landlord->national_id ?? 'N/A' }}</td>
                                        <td>{{ $landlord->company_name ?? 'N/A' }}</td>
                                        {{-- <td style="text-align: left;">
                                            @if($landlord->houses && $landlord->houses->count() > 0)
                                                <span class="badge badge-info">{{ $landlord->houses->count() }} House(s)</span>
                                                <div class="houses-list">
                                                    @foreach($landlord->houses as $house)
                                                        <div class="house-item">
                                                            <strong>{{ $house->title }}</strong><br>
                                                            <small>📍 {{ $house->location }}</small><br>
                                                            <small>💰 Ksh {{ number_format($house->price) }}</small><br>
                                                            <small>🟢 {{$house->status }}</small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="no-houses">No houses</span>
                                            @endif
                                        </td> --}}
                                        <td style="text-align: left;">
                                            @if($landlord->houses && $landlord->houses->count() > 0)
                                                <span class="badge badge-info">{{ $landlord->houses->count() }} House(s)</span>
                                                <div class="houses-list">
                                                    @foreach($landlord->houses as $house)
                                                        <div class="house-item" style="margin-bottom: 8px;">
                                                            <strong>{{ $house->title }}</strong><br>
                                                            <small>📍 {{ $house->location }}</small><br>
                                                            <small>💰 Ksh {{ number_format($house->price) }}</small><br>

                                                            @php
                                                                $statusEmoji = match(strtolower($house->status)) {
                                                                    'available' => '🟢',
                                                                    'occupied' => '🔴',
                                                                    'under maintenance' => '🟡',
                                                                    default => '⚪'
                                                                };
                                                            @endphp

                                                            <small>{{ $statusEmoji }} {{ ucfirst($house->status) }}</small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="no-houses">No houses</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge badge-success">Active</span>
                                        </td>
                                        <td>
                                            <a class="btn btn-success btn-sm"
                                               href="{{ url('edit_landlord', $landlord->id) }}">
                                                Edit
                                            </a>
                                            <a class="btn btn-danger btn-sm"
                                               onclick="confirmation(event)"
                                               href="{{ url('delete_landlord', $landlord->id) }}">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            No landlords found. <a href="{{ url('add_landlord') }}">Add your first landlord</a>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($landlords->count() > 0)
                        <div class="mt-3">
                            <p class="text-muted">Total Landlords: <strong>{{ $landlords->count() }}</strong></p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript files-->
    @include('admin.js')

    <script type="text/javascript">
        function confirmation(ev) {
            ev.preventDefault();
            var urlToRedirect = ev.currentTarget.getAttribute('href');
            console.log(urlToRedirect);

            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this landlord!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    window.location.href = urlToRedirect;
                }
            });
        }
    </script>
</body>
</html>
