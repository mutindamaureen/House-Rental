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
        <h2 class="mb-4">My Tenant Dashboard</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">My House Details</div>
            <div class="card-body">
                <h5>{{ $tenant->house->title }}</h5>
                {{-- <p>{{ $tenant->house->description }}</p> --}}
                <p><strong>Location:</strong> {{ $tenant->house->location }}</p>
                <p><strong>Monthly Rent:</strong> KES {{ number_format($tenant->rent, 2) }}</p>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-success text-white">💰 Rent & Lease Details</div>
            <div class="card-body">
                <p><strong>Lease Start:</strong> {{ $tenant->lease_start_date ?? 'N/A' }}</p>
                <p><strong>Lease End:</strong> {{ $tenant->lease_end_date ?? 'N/A' }}</p>

                @if(!is_null($daysRemaining))
                    @if($daysRemaining > 0)
                        <p><strong>Days Remaining:</strong> {{ $daysRemaining }} days</p>
                    @elseif($daysRemaining == 0)
                        <p class="text-warning">Lease ends today!</p>
                    @else
                        <p class="text-danger">Lease expired {{ abs($daysRemaining) }} days ago.</p>
                    @endif
                @endif

                <p><strong>Utilities:</strong> KES {{ number_format($tenant->utilities, 2) }}</p>
                <p><strong>Security Deposit:</strong> KES {{ number_format($tenant->security_deposit, 2) }}</p>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-warning text-dark">🧰 Maintenance Requests</div>
            <div class="card-body">
                <form method="POST" action="{{ route('tenant.requestMaintenance') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="issue" class="form-label">Issue</label>
                        <input type="text" name="subject" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optional)</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <button class="btn btn-primary">Submit Request</button>
                </form>

                <hr>
                <h5>My Previous Requests</h5>
                @if($requests->isEmpty())
                    <p>No maintenance requests yet.</p>
                @else
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Issue</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $req)
                                <tr>
                                    <td>{{ $req->subject }}</td>
                                    <td>{{ $req->description ?? '—' }}</td>
                                    <td><span class="badge bg-{{ $req->status == 'Pending' ? 'warning' : 'success' }}">{{ $req->status }}</span></td>
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
