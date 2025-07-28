@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container-fluid vh-100">
    <div class="row h-100">
        <!-- Left Side - Branding -->
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-primary">
            <div class="text-center text-white">
                <div class="mb-4">
                    <i class="fas fa-graduation-cap fa-5x mb-3"></i>
                    <h1 class="fw-bold">Training Management System</h1>
                    <p class="lead">Manage employee training programs efficiently</p>
                </div>
                <div class="row text-center">
                    <div class="col-4">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <p class="small">Employee Management</p>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
                        <p class="small">Training Programs</p>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                        <p class="small">Progress Tracking</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="w-100" style="max-width: 400px;">
                <div class="text-center mb-4">
                    <div class="d-lg-none mb-4">
                        <i class="fas fa-graduation-cap fa-3x text-primary"></i>
                        <h2 class="fw-bold text-primary">Training System</h2>
                    </div>
                    <h3 class="fw-bold mb-2">Welcome Back!</h3>
                    <p class="text-muted">Please sign in to your account</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus
                                   placeholder="Enter your email">
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required
                                   placeholder="Enter your password">
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Sign In
                        </button>
                    </div>
                </form>

                <!-- Demo Accounts -->
                <div class="mt-4">
                    <hr>
                    <p class="text-center text-muted small mb-2">Demo Accounts:</p>
                    <div class="row">
                        <div class="col-6">
                            <div class="card border-primary">
                                <div class="card-body p-2 text-center">
                                    <small class="fw-bold text-primary">Administrator</small><br>
                                    <small class="text-muted">admin@training.com</small><br>
                                    <small class="text-muted">password123</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-success">
                                <div class="card-body p-2 text-center">
                                    <small class="fw-bold text-success">HR Manager</small><br>
                                    <small class="text-muted">hr@training.com</small><br>
                                    <small class="text-muted">password123</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Back to Home -->
                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
