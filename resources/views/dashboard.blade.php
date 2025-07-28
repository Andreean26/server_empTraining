@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-uppercase fw-bold mb-1">Total Employees</div>
                            <div class="h5 mb-0 fw-bold">{{ number_format($stats['total_employees']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-uppercase fw-bold mb-1">Total Trainings</div>
                            <div class="h5 mb-0 fw-bold">{{ number_format($stats['total_trainings']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-uppercase fw-bold mb-1">Active Trainings</div>
                            <div class="h5 mb-0 fw-bold">{{ number_format($stats['active_trainings']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-uppercase fw-bold mb-1">Completed</div>
                            <div class="h5 mb-0 fw-bold">{{ number_format($stats['completed_trainings']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-medal fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Trainings -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Trainings
                    </h5>
                    @if(auth()->user()->role && in_array('trainings.create', auth()->user()->role->permissions ?? []))
                        <a href="{{ route('trainings.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Add Training
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($recentTrainings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Training</th>
                                        <th>Trainer</th>
                                        <th>Date</th>
                                        <th>Enrollments</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTrainings as $training)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $training->title }}</div>
                                                <small class="text-muted">{{ Str::limit($training->description, 50) }}</small>
                                            </td>
                                            <td>{{ $training->trainer_name }}</td>
                                            <td>
                                                <div>{{ $training->training_date->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $training->start_time }} - {{ $training->end_time }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $training->enrollments->count() }}/{{ $training->max_participants }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($training->training_date->isPast())
                                                    <span class="badge bg-secondary">Completed</span>
                                                @elseif($training->training_date->isToday())
                                                    <span class="badge bg-warning">Today</span>
                                                @else
                                                    <span class="badge bg-success">Upcoming</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No trainings found.</p>
                            @if(auth()->user()->role && in_array('trainings.create', auth()->user()->role->permissions ?? []))
                                <a href="{{ route('trainings.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Create First Training
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Trainings -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Upcoming Trainings
                    </h5>
                </div>
                <div class="card-body">
                    @if($upcomingTrainings->count() > 0)
                        @foreach($upcomingTrainings as $training)
                            <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'border-bottom pb-3' : '' }}">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <small class="fw-bold">{{ $training->training_date->format('d') }}</small>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ Str::limit($training->title, 30) }}</div>
                                    <small class="text-muted">
                                        {{ $training->training_date->format('M d, Y') }} • 
                                        {{ $training->enrollments->count() }} enrolled
                                    </small>
                                </div>
                            </div>
                        @endforeach
                        <div class="text-center">
                            <a href="{{ route('trainings.index') }}" class="btn btn-outline-primary btn-sm">
                                View All Trainings
                            </a>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-calendar fa-2x text-muted mb-2"></i>
                            <p class="text-muted small">No upcoming trainings</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Training by Department Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Training Enrollments by Department
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Completion Trend -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Monthly Completion Trend
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="completionChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->role && in_array('employees.create', auth()->user()->role->permissions ?? []))
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('employees.create') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                    Add Employee
                                </a>
                            </div>
                        @endif

                        @if(auth()->user()->role && in_array('trainings.create', auth()->user()->role->permissions ?? []))
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('trainings.create') }}" class="btn btn-outline-success w-100">
                                    <i class="fas fa-chalkboard-teacher fa-2x mb-2 d-block"></i>
                                    Create Training
                                </a>
                            </div>
                        @endif

                        <div class="col-md-3 mb-3">
                            <a href="{{ route('training-enrollments.index') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-clipboard-list fa-2x mb-2 d-block"></i>
                                View Enrollments
                            </a>
                        </div>

                        @if(auth()->user()->role && in_array('export.data', auth()->user()->role->permissions ?? []))
                            <div class="col-md-3 mb-3">
                                <div class="dropdown">
                                    <button class="btn btn-outline-warning w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-download fa-2x mb-2 d-block"></i>
                                        Export Data
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('export.employees') }}">Export Employees</a></li>
                                        <li><a class="dropdown-item" href="{{ route('export.trainings') }}">Export Trainings</a></li>
                                        <li><a class="dropdown-item" href="{{ route('export.enrollments') }}">Export Enrollments</a></li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Department Chart
    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    const departmentData = @json($trainingsByDepartment);
    
    new Chart(departmentCtx, {
        type: 'doughnut',
        data: {
            labels: departmentData.map(item => item.department),
            datasets: [{
                data: departmentData.map(item => item.total),
                backgroundColor: [
                    'rgba(37, 99, 235, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)'
                ],
                borderWidth: 2,
                borderColor: '#fff'
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

    // Monthly Completion Chart
    const completionCtx = document.getElementById('completionChart').getContext('2d');
    const completionData = @json($monthlyCompletions);
    
    new Chart(completionCtx, {
        type: 'line',
        data: {
            labels: completionData.map(item => `${item.year}-${String(item.month).padStart(2, '0')}`),
            datasets: [{
                label: 'Completions',
                data: completionData.map(item => item.total),
                borderColor: 'rgba(37, 99, 235, 1)',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
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
</script>
@endpush
