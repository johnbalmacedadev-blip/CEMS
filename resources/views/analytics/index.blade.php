@extends('layouts.app')

@section('title', 'Analytics - Car Empire Management System')

@section('content')
    <div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-chart-line me-2"></i>Analytics & Reports
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Print
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportCharts()">
                            <i class="fas fa-download me-1"></i>Export
        </button>
                    </div>
                </div>
            </div>

            <!-- Key Metrics Summary -->
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
                                    <div class="text-xs text-muted mt-1">All time inventory</div>
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
                                        Total Revenue
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalRevenue, 2) }}</div>
                                    <div class="text-xs text-muted mt-1">From released vehicles</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                        Total Expenses
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalExpenses, 2) }}</div>
                                    <div class="text-xs text-muted mt-1">All expense transactions</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
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
                                        Net Profit
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalProfit, 2) }}</div>
                                    <div class="text-xs text-muted mt-1">Revenue - Purchase - Expenses</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Analytics Section -->
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <h4 class="text-primary">
                        <i class="fas fa-car me-2"></i>Vehicle Analytics
                    </h4>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Monthly Vehicle Additions (12 months) -->
                <div class="col-xl-8 col-lg-7 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-line me-2"></i>Monthly Vehicle Additions (Last 12 Months)
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyVehiclesChart" style="height: 350px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Status Distribution -->
                <div class="col-xl-4 col-lg-5 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie me-2"></i>Vehicle Status Distribution
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="statusPieChart" style="height: 350px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Yearly Vehicle Trends -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-area me-2"></i>Yearly Vehicle Additions (Last 5 Years)
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="yearlyVehiclesChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Top 10 Vehicle Makes -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-bar me-2"></i>Top 10 Vehicle Makes
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="topMakesChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Top 10 Vehicle Models -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-bar me-2"></i>Top 10 Vehicle Models
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="topModelsChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Vehicles by Transmission Type -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie me-2"></i>Vehicles by Transmission Type
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="transmissionChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Vehicles by Fuel Type -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie me-2"></i>Vehicles by Fuel Type
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="fuelTypeChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Vehicles by Manufacturing Year -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-bar me-2"></i>Vehicles by Manufacturing Year
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="vehiclesByYearChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Analytics Section -->
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <h4 class="text-primary">
                        <i class="fas fa-money-bill-wave me-2"></i>Financial Analytics
                    </h4>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Monthly Expenses Trend -->
                <div class="col-xl-8 col-lg-7 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-line me-2"></i>Monthly Expenses Trend (Last 12 Months)
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyExpensesChart" style="height: 350px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue -->
                <div class="col-xl-4 col-lg-5 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-area me-2"></i>Monthly Revenue (Last 12 Months)
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyRevenueChart" style="height: 350px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Expense Breakdown by Payment Tag -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie me-2"></i>Expense Breakdown by Category
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="expenseByTagChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-calculator me-2"></i>Financial Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td class="font-weight-bold">Total Purchase Value</td>
                                            <td class="text-right">₱{{ number_format($totalPurchaseValue, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Average Purchase Price</td>
                                            <td class="text-right">₱{{ number_format($averagePurchasePrice, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Total Revenue</td>
                                            <td class="text-right text-success">₱{{ number_format($totalRevenue, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="font-weight-bold">Total Expenses</td>
                                            <td class="text-right text-danger">₱{{ number_format($totalExpenses, 2) }}</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td class="font-weight-bold">Net Profit</td>
                                            <td class="text-right font-weight-bold">₱{{ number_format($totalProfit, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personnel Analytics Section -->
            <div class="row mb-4">
                <div class="col-12 mb-3">
                    <h4 class="text-primary">
                        <i class="fas fa-users me-2"></i>Personnel Analytics
                    </h4>
    </div>
            </div>

            <div class="row mb-4">
                <!-- Employees by Contract Type -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-pie me-2"></i>Employees by Contract Type
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="employeeContractChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Personnel Summary -->
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-users me-2"></i>Personnel Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card border-left-primary">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Employees
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalEmployees }}</div>
                                            <div class="text-xs text-muted mt-1">
                                                Active: {{ $activeEmployees }} | Inactive: {{ $inactiveEmployees }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card border-left-info">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Sales Agents
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSalesAgents }}</div>
                                            <div class="text-xs text-muted mt-1">
                                                Active: {{ $activeSalesAgents }} | Inactive: {{ $inactiveSalesAgents }}
                                            </div>
                                        </div>
                                    </div>
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
@media print {
    .btn-toolbar, .btn-group {
        display: none !important;
    }
}
</style>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Vehicle Additions Line Chart (12 months)
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
                                return context.label + ': ' + context.parsed + ' vehicles';
                            }
                        }
                    }
                }
            }
        });
    }

    // Yearly Vehicle Additions Bar Chart
    const yearlyCtx = document.getElementById('yearlyVehiclesChart');
    if (yearlyCtx) {
        new Chart(yearlyCtx, {
            type: 'bar',
            data: {
                labels: @json($yearlyLabels),
                datasets: [{
                    label: 'Vehicles Added',
                    data: @json($yearlyData),
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

    // Top 10 Makes Bar Chart
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
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Top 10 Models Bar Chart
    const modelsCtx = document.getElementById('topModelsChart');
    if (modelsCtx) {
        const topModels = @json($topModels);
        new Chart(modelsCtx, {
            type: 'bar',
            data: {
                labels: topModels.map(model => model.name),
                datasets: [{
                    label: 'Number of Vehicles',
                    data: topModels.map(model => model.count),
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    // Transmission Type Pie Chart
    const transmissionCtx = document.getElementById('transmissionChart');
    if (transmissionCtx) {
        const transmissionData = @json($transmissionData);
        new Chart(transmissionCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(transmissionData),
                datasets: [{
                    data: Object.values(transmissionData),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Fuel Type Pie Chart
    const fuelCtx = document.getElementById('fuelTypeChart');
    if (fuelCtx) {
        const fuelData = @json($fuelTypeData);
        new Chart(fuelCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(fuelData),
                datasets: [{
                    data: Object.values(fuelData),
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Vehicles by Year Bar Chart
    const yearCtx = document.getElementById('vehiclesByYearChart');
    if (yearCtx) {
        const yearData = @json($vehiclesByYear);
        new Chart(yearCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(yearData),
                datasets: [{
                    label: 'Vehicles',
                    data: Object.values(yearData),
                    backgroundColor: 'rgba(153, 102, 255, 0.8)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
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

    // Monthly Revenue Line Chart
    const revenueCtx = document.getElementById('monthlyRevenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueLabels),
                datasets: [{
                    label: 'Revenue (₱)',
                    data: @json($monthlyRevenue),
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

    // Expense by Tag Pie Chart
    const expenseTagCtx = document.getElementById('expenseByTagChart');
    if (expenseTagCtx) {
        const expenseByTag = @json($expenseByTag);
        new Chart(expenseTagCtx, {
            type: 'pie',
            data: {
                labels: expenseByTag.map(item => item.name),
                datasets: [{
                    data: expenseByTag.map(item => item.total),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ₱' + context.parsed.toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                }
            }
        });
    }

    // Employee Contract Type Pie Chart
    const employeeContractCtx = document.getElementById('employeeContractChart');
    if (employeeContractCtx) {
        const contractData = @json($employeeContractTypes);
        new Chart(employeeContractCtx, {
            type: 'pie',
            data: {
                labels: Object.keys(contractData),
                datasets: [{
                    data: Object.values(contractData),
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});

function exportCharts() {
    alert('Export functionality will be implemented soon.');
}
</script>
@endsection
