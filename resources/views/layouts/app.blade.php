<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Employee Training Management') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --danger-color: #dc2626;
            --warning-color: #d97706;
            --info-color: #0891b2;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-color) 0%, #1e40af 100%);
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 0.5rem;
            margin: 0.25rem 0;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .main-content {
            background-color: #f8fafc;
            min-height: 100vh;
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 0.75rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
        }

        .stat-card.success {
            background: linear-gradient(135deg, var(--success-color) 0%, #10b981 100%);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #f59e0b 100%);
        }

        .stat-card.info {
            background: linear-gradient(135deg, var(--info-color) 0%, #06b6d4 100%);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af 0%, var(--primary-color) 100%);
            transform: translateY(-2px);
        }

        .table {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .alert {
            border-radius: 0.75rem;
            border: none;
        }

        .breadcrumb {
            background: transparent;
            margin-bottom: 1.5rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        @auth
            <div class="container-fluid">
                <div class="row">
                    <!-- Sidebar -->
                    <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebarMenu">
                        <div class="position-sticky pt-3">
                            <!-- Logo/Brand -->
                            <div class="text-center mb-4">
                                <h4 class="fw-bold">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    Training System
                                </h4>
                                <small class="text-light">{{ auth()->user()->role->display_name ?? 'User' }}</small>
                            </div>

                            <!-- Navigation -->
                            <ul class="nav flex-column px-2">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>
                                        Dashboard
                                    </a>
                                </li>

                                @if(auth()->user()->role && in_array('users.read', auth()->user()->role->permissions ?? []))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                            <i class="fas fa-users me-2"></i>
                                            Users
                                        </a>
                                    </li>
                                @endif

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                                        <i class="fas fa-user-tag me-2"></i>
                                        Roles
                                    </a>
                                </li>

                                @if(auth()->user()->role && in_array('employees.read', auth()->user()->role->permissions ?? []))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                                            <i class="fas fa-id-badge me-2"></i>
                                            Employees
                                        </a>
                                    </li>
                                @endif

                                @if(auth()->user()->role && in_array('trainings.read', auth()->user()->role->permissions ?? []))
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('trainings.*') ? 'active' : '' }}" href="{{ route('trainings.index') }}">
                                            <i class="fas fa-chalkboard-teacher me-2"></i>
                                            Trainings
                                        </a>
                                    </li>
                                @endif

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('training-enrollments.*') ? 'active' : '' }}" href="{{ route('training-enrollments.index') }}">
                                        <i class="fas fa-clipboard-list me-2"></i>
                                        Enrollments
                                    </a>
                                </li>

                                <li class="nav-item mt-3">
                                    <hr class="text-light">
                                </li>

                                <li class="nav-item">
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="nav-link border-0 bg-transparent text-start w-100">
                                            <i class="fas fa-sign-out-alt me-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </nav>

                    <!-- Main content -->
                    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                        <!-- Top Navigation -->
                        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                            <h1 class="h2">@yield('title', 'Dashboard')</h1>
                            <div class="btn-toolbar mb-2 mb-md-0">
                                <div class="btn-group me-2">
                                    <span class="navbar-text">
                                        <i class="fas fa-user-circle me-2"></i>
                                        {{ auth()->user()->name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Breadcrumb -->
                        @if(isset($breadcrumbs))
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    @foreach($breadcrumbs as $breadcrumb)
                                        @if($loop->last)
                                            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['name'] }}</li>
                                        @else
                                            <li class="breadcrumb-item">
                                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ol>
                            </nav>
                        @endif

                        <!-- Flash Messages -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('warning'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('warning') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Page Content -->
                        @yield('content')
                    </main>
                </div>
            </div>
        @else
            <!-- Guest Layout -->
            @yield('content')
        @endauth
    </div>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: 'Choose...',
                allowClear: true
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });

        // CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
