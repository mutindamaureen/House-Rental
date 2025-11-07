<!DOCTYPE html>
<html>

<head>
    @include('home.css')
</head>

<body>
  <div class="hero_area">
    <!-- header section strats -->
    @include('home.header')
    <!-- end header section -->


        <div class="container py-4">
            <h2 class="mb-4">🏡 Landlord Dashboard</h2>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">My Houses</div>
                <div class="card-body">
                    @if($houses->isEmpty())
                        <p>No houses registered yet.</p>
                    @else
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>House Title</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($houses as $index => $house)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $house->title }}</td>
                                        <td>{{ $house->location }}</td>
                                        <td>
                                            <span class="badge bg-{{ $house->status == 'available' ? 'success' : 'warning' }}">
                                                {{ ucfirst($house->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">👥 My Tenants</div>
                <div class="card-body">
                    @if($tenants->isEmpty())
                        <p>No tenants assigned to your houses.</p>
                    @else
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>House</th>
                                    <th>Rent</th>
                                    <th>Lease Period</th>
                                    <th>Utilities</th>
                                    <th>Security Deposit</th>
                                    <th>Lease Status</th>
                                    <th>Payment Status</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenants as $tenant)
                                    <tr>
                                        <td>{{ $tenant->user->name  }}</td>
                                        <td>{{ $tenant->user->email  }}</td>
                                        <td>{{ $tenant->user->phone  }}</td>
                                        <td>{{ $tenant->house->title ?? '—' }}</td>
                                        <td>KES {{ number_format($tenant->rent, 2) }}</td>
                                        <td>
                                            {{ $tenant->lease_start_date ?? 'N/A' }} -
                                            {{ $tenant->lease_end_date ?? 'N/A' }}
                                        </td>
                                        <td>KES {{ number_format($tenant->utilities, 2) }}</td>
                                        <td>KES {{ number_format($tenant->security_deposit, 2) }}</td>
                                        <td>{{ $tenant->lease_status }}</td>
                                        <td>{{ $tenant->payment_status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-warning text-dark">🧰 Maintenance Requests</div>
                <div class="card-body">
                    @if($requests->isEmpty())
                        <p>No maintenance requests yet.</p>
                    @else
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Tenant</th>
                                    <th>House</th>
                                    <th>Issue</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Date Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $req)
                                    <tr>
                                        <td>{{ $req->tenant->name ?? 'Unknown Tenant' }}</td>
                                        <td>{{ $req->house->title ?? 'Unknown House' }}</td>
                                        <td>{{ $req->issue }}</td>
                                        <td>{{ $req->description ?? '—' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $req->status == 'Pending' ? 'warning' : 'success' }}">
                                                {{ $req->status }}
                                            </span>
                                        </td>
                                        <td>{{ $req->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>


  <!-- info section -->

  @include('home.footer')
</body>

</html>
