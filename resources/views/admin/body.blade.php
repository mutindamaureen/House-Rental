
<!DOCTYPE html>
<html>
<head>
    @include('admin.css')
    <style>
        /* Dashboard Color Accents */
        .dashtext-1 { color: #198754; }
        .dashtext-2 { color: #0d6efd; }
        .dashtext-3 { color: #ffc107; }
        .dashtext-4 { color: #6f42c1; }

        .dashbg-1 { background-color: #198754; }
        .dashbg-2 { background-color: #0d6efd; }
        .dashbg-3 { background-color: #ffc107; }
        .dashbg-4 { background-color: #6f42c1; }

        /* Card Enhancements */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: translateY(-3px);
        }
        .number {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .statistic-block {
            background: #fff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .statistic-block:hover {
            transform: translateY(-4px);
        }
        .title strong {
            font-size: 1rem;
            color: #333;
        }
        footer.footer {
            background: #f8f9fa;
            padding: 1rem 0;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
        }
        h2.h5.no-margin-bottom {
            color: #0d6efd;
            font-weight: 600;
        }
    </style>
</head>
<body>
@include('admin.header')

<div class="d-flex align-items-stretch">
    @include('admin.sidebar')

    <div class="page-content">
        <div class="page-header bg-light py-3 mb-4 shadow-sm">
            <div class="container-fluid">
                <h2 class="h5 no-margin-bottom">Dashboard Overview</h2>
            </div>
        </div>

        <!-- Statistics Section -->
        <section class="mb-5">
            <div class="container-fluid">
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="statistic-block text-center">
                            <div class="icon mb-2"><i class="icon-user-1 fs-4 text-success"></i></div>
                            <strong>Total Users</strong>
                            <div class="number dashtext-1 mt-2">{{ $totalUsers }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="statistic-block text-center">
                            <div class="icon mb-2"><i class="icon-home fs-4 text-primary"></i></div>
                            <strong>Total Houses</strong>
                            <div class="number dashtext-2 mt-2">{{ $totalHouses }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="statistic-block text-center">
                            <div class="icon mb-2"><i class="icon-user fs-4 text-warning"></i></div>
                            <strong>Total Tenants</strong>
                            <div class="number dashtext-3 mt-2">{{ $totalTenants }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="statistic-block text-center">
                            <div class="icon mb-2"><i class="icon-user-1 fs-4 text-purple"></i></div>
                            <strong>Total Landlords</strong>
                            <div class="number dashtext-4 mt-2">{{ $totalLandlords }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Financial Cards -->
        <section class="mb-5">
            <div class="container-fluid">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="text-success">Total Monthly Rent</h5>
                                <p class="text-muted mb-1">All Tenants Combined</p>
                                <h3 class="fw-bold dashtext-1">KSh {{ number_format($totalRevenue, 2) }}</h3>
                                <small>{{ $totalTenants }} Active Tenants</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="text-primary">Total Utilities</h5>
                                <p class="text-muted mb-1">Monthly Utilities</p>
                                <h3 class="fw-bold dashtext-2">KSh {{ number_format($totalUtilities, 2) }}</h3>
                                <small>Additional Charges</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="text-warning">Security Deposits</h5>
                                <p class="text-muted mb-1">Total Held</p>
                                <h3 class="fw-bold dashtext-3">KSh {{ number_format($totalDeposits, 2) }}</h3>
                                <small>Refundable Deposits</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Top Landlords -->
        <section class="mb-5">
            <div class="container-fluid">
                <h4 class="mb-3 text-primary fw-semibold">Top Landlords by Property Count</h4>
                <div class="row g-3">
                    @foreach($topLandlords->take(3) as $index => $landlord)
                        <div class="col-lg-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <img src="{{ asset('img/avatar-'.($index+1).'.jpg') }}" alt="Avatar" class="rounded-circle mb-3" width="80" height="80">
                                    <h5 class="fw-bold mb-0">{{ $landlord->user->name ?? 'N/A' }}</h5>
                                    <small class="text-muted">{{ $landlord->user->email ?? 'N/A' }}</small>
                                    <p class="mt-2 mb-0">{{ $landlord->houses_count }} Properties</p>
                                    <p class="text-muted small mb-0">{{ $landlord->user->phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Recent Tenants -->
        <section class="mb-5">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Recent Tenants</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($recentTenants as $tenant)
                                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                    <img src="{{ asset('img/avatar-3.jpg') }}" class="rounded-circle me-3" width="45" height="45">
                                    <div class="flex-fill">
                                        <strong>{{ $tenant->user->name ?? 'N/A' }}</strong>
                                        <p class="mb-0 text-muted">{{ $tenant->house->title ?? 'N/A' }} — KSh {{ number_format($tenant->rent, 2) }}/month</p>
                                        <small class="text-muted">{{ $tenant->created_at->format('M d, Y') }}</small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="mb-5">
            <div class="container-fluid">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="text-primary">Monthly Growth - {{ date('Y') }}</h5>
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="text-primary">Houses by Category</h5>
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer mt-4">
            <div class="container-fluid text-center">
                <p class="no-margin-bottom mb-0">{{ date('Y') }} &copy; Property Management System</p>
            </div>
        </footer>
    </div>
</div>

@include('admin.js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            datasets: [{
                label: 'New Tenants',
                data: @json(array_values($tenantData)),
                borderColor: '#198754',
                backgroundColor: 'rgba(25,135,84,0.2)',
                tension: 0.1
            },{
                label: 'New Houses',
                data: @json(array_values($houseData)),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.2)',
                tension: 0.1
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($housesByCategory)),
            datasets: [{
                data: @json(array_values($housesByCategory)),
                backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#6f42c1','#20c997']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
</script>
</body>
</html>
