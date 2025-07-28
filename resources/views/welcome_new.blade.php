@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center text-white">
                <!-- Hero Section -->
                <div class="mb-5">
                    <i class="fas fa-graduation-cap fa-5x mb-4"></i>
                    <h1 class="display-4 fw-bold mb-3">Employee Training Management System</h1>
                    <p class="lead mb-4">
                        Streamline your company's training programs with our comprehensive management platform.
                        Track employee progress, manage training schedules, and maintain detailed records.
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg px-4">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Login to System
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Features Grid -->
                <div class="row mb-5">
                    <div class="col-md-4 mb-4">
                        <div class="card bg-transparent border-light h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h5 class="card-title">Employee Management</h5>
                                <p class="card-text">
                                    Manage employee profiles, track their training history, and monitor their development progress.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card bg-transparent border-light h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                                <h5 class="card-title">Training Programs</h5>
                                <p class="card-text">
                                    Create and manage training sessions, upload materials, and assign trainers efficiently.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card bg-transparent border-light h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-3x mb-3"></i>
                                <h5 class="card-title">Progress Tracking</h5>
                                <p class="card-text">
                                    Monitor training completion, generate reports, and analyze performance metrics.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Features -->
                <div class="row">
                    <div class="col-12">
                        <h3 class="mb-4">Key Features</h3>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                                    <span>Role-based access control</span>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                                    <span>PDF material upload & management</span>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                                    <span>Excel import/export functionality</span>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                                    <span>Comprehensive audit trail</span>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                                    <span>Advanced search & filtering</span>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                                    <span>Real-time notifications</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Demo Account Info -->
                @guest
                <div class="mt-5 pt-4 border-top border-light">
                    <h4 class="mb-3">Demo Accounts</h4>
                    <div class="row justify-content-center">
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card bg-white text-dark">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-primary">Administrator</h6>
                                    <p class="card-text small mb-1">Email: admin@training.com</p>
                                    <p class="card-text small">Password: password123</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card bg-white text-dark">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-success">HR Manager</h6>
                                    <p class="card-text small mb-1">Email: hr@training.com</p>
                                    <p class="card-text small">Password: password123</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endguest
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h6>Employee Training Management System</h6>
                <p class="small text-muted">Built with Laravel & Bootstrap</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="small text-muted mb-0">
                    © {{ date('Y') }} Training System. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</footer>
@endsection
