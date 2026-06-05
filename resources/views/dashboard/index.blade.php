@extends('layouts.app')

@section('title', 'Dashboard - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New
                    </button>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                Welcome back, <strong>{{ $user->name }}</strong>! You are logged in as {{ ucfirst($user->role) }}.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Vehicles
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalVehicles }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-car fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Available Vehicles
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $availableVehicles }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Released Vehicles
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $releasedVehicles }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Reserved Vehicles
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $reservedVehicles }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Total Purchase Value
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalPurchaseValue, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-secondary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                        Total Expenses
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalExpenses, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Active Employees
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeEmployees }} / {{ $totalEmployees }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-users fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Active Sales Agents
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeSalesAgents }} / {{ $totalSalesAgents }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Charts Section -->
            <div class="row mb-4">
                <!-- Monthly Vehicle Additions Chart -->
                <div class="col-xl-8 col-lg-7 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-line me-2"></i>Monthly Vehicle Additions
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyVehiclesChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Status Distribution Pie Chart -->
                <div class="col-xl-4 col-lg-5 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie me-2"></i>Vehicle Status Distribution
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="statusPieChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Charts Row -->
            <div class="row mb-4">
                <!-- Top Makes Bar Chart -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-bar me-2"></i>Top 5 Vehicle Makes
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="topMakesChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Monthly Expenses Chart -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-area me-2"></i>Monthly Expenses Trend
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyExpensesChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Recent Vehicles</h6>
                        </div>
                        <div class="card-body">
                            @if($recentVehicles->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Vehicle</th>
                                                <th>Plate Number</th>
                                                <th>Purchase Price</th>
                                                <th>Status</th>
                                                <th>Added</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentVehicles as $vehicle)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($vehicle->primaryImage)
                                                            <img src="{{ $vehicle->primaryImage->thumbnail_url }}" alt="Vehicle" class="me-2" style="width: 40px; height: 30px; object-fit: cover; border-radius: 4px;">
                                                        @else
                                                            <div class="me-2 d-flex align-items-center justify-content-center bg-light" style="width: 40px; height: 30px; border-radius: 4px;">
                                                                <i class="fas fa-car text-muted" style="font-size: 12px;"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <strong>{{ $vehicle->full_name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $vehicle->year }} • {{ $vehicle->colour }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-secondary">{{ $vehicle->plate_number }}</span></td>
                                                <td>{{ $vehicle->formatted_purchase_price }}</td>
                                                <td>
                                                    @if($vehicle->status === 'Available')
                                                        <span class="badge bg-success">{{ $vehicle->status }}</span>
                                                    @elseif($vehicle->status === 'Sold')
                                                        <span class="badge bg-primary">{{ $vehicle->status }}</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ $vehicle->status }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $vehicle->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary btn-sm">
                                        View Unit Report
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-car fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No vehicles added yet</h5>
                                    <p class="text-muted">Start by adding your first vehicle to the inventory.</p>
                                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Add New Vehicle
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add New Vehicle
                                </a>
                                <button class="btn btn-success" type="button">
                                    <i class="fas fa-user-plus me-2"></i>Add Customer
                                </button>
                                <button class="btn btn-info" type="button">
                                    <i class="fas fa-file-invoice me-2"></i>Create Sale
                                </button>
                                <button class="btn btn-warning" type="button">
                                    <i class="fas fa-tools me-2"></i>Schedule Service
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">System Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Database</span>
                                    <span class="badge bg-success">Online</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Server</span>
                                    <span class="badge bg-success">Running</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Last Backup</span>
                                    <span class="text-muted">2 hours ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.border-left-secondary {
    border-left: 0.25rem solid #858796 !important;
}
.text-xs {
    font-size: 0.7rem;
}
.font-weight-bold {
    font-weight: 700 !important;
}
.text-uppercase {
    text-transform: uppercase !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}

/* Modal backdrop with 28% opacity */
.modal-backdrop.show {
    opacity: 1 !important;
    background-color: rgb(0 0 0 / 28%) !important;
}

</style>

@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Vehicle Additions Line Chart
    const monthlyCtx = document.getElementById('monthlyVehiclesChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyLabels),
                datasets: [{
                    label: 'Vehicles Added',
                    data: @json($monthlyData),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Vehicle Status Distribution Pie Chart
    const statusCtx = document.getElementById('statusPieChart');
    if (statusCtx) {
        const statusData = @json($statusDistribution);
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed + ' vehicles';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    // Top Makes Bar Chart
    const makesCtx = document.getElementById('topMakesChart');
    if (makesCtx) {
        const topMakes = @json($topMakes);
        new Chart(makesCtx, {
            type: 'bar',
            data: {
                labels: topMakes.map(make => make.name),
                datasets: [{
                    label: 'Number of Vehicles',
                    data: topMakes.map(make => make.count),
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' vehicles';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Monthly Expenses Line Chart
    const expensesCtx = document.getElementById('monthlyExpensesChart');
    if (expensesCtx) {
        new Chart(expensesCtx, {
            type: 'line',
            data: {
                labels: @json($expenseLabels),
                datasets: [{
                    label: 'Expenses (₱)',
                    data: @json($monthlyExpenses),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.parsed.y.toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString('en-US');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
