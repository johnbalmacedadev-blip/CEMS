<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Car Empire Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 50%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Red accent lines */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #dc3545 0%, #c82333 50%, #dc3545 100%);
            z-index: 1;
        }
        
        body::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #dc3545 0%, #c82333 50%, #dc3545 100%);
            z-index: 1;
        }
        
        .login-container {
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5),
                        0 0 0 2px #dc3545,
                        inset 0 0 0 1px rgba(220, 53, 69, 0.3);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 2;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .logo-container {
            margin-bottom: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .logo-container img {
            max-width: 100%;
            height: auto;
            max-height: 120px;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(220, 53, 69, 0.3));
        }
        
        .login-header h1 {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .login-header p {
            color: #e0e0e0;
            margin: 0;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }
        
        .form-floating {
            margin-bottom: 1.25rem;
        }
        
        .form-floating > .form-control {
            background-color: rgba(45, 45, 45, 0.8);
            border: 2px solid rgba(220, 53, 69, 0.5);
            border-radius: 8px;
            color: #ffffff;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-floating > .form-control:focus {
            background-color: rgba(45, 45, 45, 1);
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25),
                        0 0 10px rgba(220, 53, 69, 0.3);
            color: #ffffff;
        }
        
        .form-floating > label {
            color: #b0b0b0;
            background-color: transparent;
        }
        
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #dc3545;
        }
        
        /* Password toggle button */
        #togglePassword {
            color: #b0b0b0 !important;
            text-decoration: none;
            padding: 0;
            margin: 0;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        #togglePassword:hover {
            color: #dc3545 !important;
        }
        
        #togglePassword:focus {
            outline: none;
            box-shadow: none;
        }
        
        .form-floating > .form-control:not(:placeholder-shown) ~ #togglePassword {
            top: 50%;
            transform: translateY(-50%);
            margin-top: 0;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            border-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.5);
            color: #ffffff;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .form-check-input {
            background-color: rgba(45, 45, 45, 0.8);
            border: 2px solid rgba(220, 53, 69, 0.5);
            width: 1.25rem;
            height: 1.25rem;
            cursor: pointer;
        }
        
        .form-check-input:checked {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        .form-check-input:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
        
        .form-check-label {
            color: #e0e0e0;
            cursor: pointer;
            margin-left: 0.5rem;
        }
        
        .form-check {
            margin-bottom: 1.5rem;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .forgot-password a {
            color: #dc3545;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: #ff6b7a;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 8px;
            border: 2px solid rgba(220, 53, 69, 0.5);
            background-color: rgba(220, 53, 69, 0.1);
            color: #ff6b7a;
            margin-bottom: 1.5rem;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.15);
            border-color: #dc3545;
        }
        
        .invalid-feedback {
            color: #ff6b7a;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
        }
        
        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
        
        /* Decorative red lines */
        .login-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: 20px;
            right: 20px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #dc3545, transparent);
            border-radius: 15px 15px 0 0;
        }
        
        .login-container::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 20px;
            right: 20px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #dc3545, transparent);
            border-radius: 0 0 15px 15px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .login-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .logo-container img {
                max-height: 100px;
            }
        }
    </style>
</head>
<body>
    @include('partials.preloader')
    <div class="login-container">
        <div class="login-header">
            <div class="logo-container">
                <img src="{{ asset('images/CAREMPIRE_LOGO.png') }}" alt="CAR EMPIRE Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div style="display: none; color: #dc3545; font-size: 2rem; font-weight: bold;">
                    CAR<span style="color: #ffffff; text-shadow: 2px 2px 0px #dc3545;">EMPIRE</span>
                </div>
            </div>
            <h1>Car Empire</h1>
            <p>Management System</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-floating">
                <input type="text" class="form-control @error('login') is-invalid @enderror" 
                       id="login" name="login" placeholder="Username or Email Address" 
                       value="{{ old('login') }}" required autocomplete="username" autofocus>
                <label for="login">
                    <i class="fas fa-user me-2"></i>Username or Email
                </label>
                @error('login')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-floating position-relative">
                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" name="password" placeholder="Password" 
                       required autocomplete="current-password">
                <button type="button" class="btn btn-link text-white position-absolute end-0 top-0 h-100 d-flex align-items-center pe-3" 
                        id="togglePassword" style="z-index: 10; border: none; background: transparent; outline: none;">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
                <label for="password">
                    <i class="fas fa-lock me-2"></i>Password
                </label>
                @error('password')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" 
                       id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">
                    Remember Me
                </label>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>

            <div class="forgot-password">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        Forgot Your Password?
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Show/Hide Password Toggle and CSRF Token Refresh -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (togglePassword && passwordInput && eyeIcon) {
                togglePassword.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Toggle password visibility
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    }
                });
            }
            
            // Refresh CSRF token every 4 minutes to prevent expiration (before 5 minute default)
            let csrfTokenRefreshInterval = setInterval(function() {
                fetch('/login', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-cache'
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newToken = doc.querySelector('meta[name="csrf-token"]');
                    if (newToken) {
                        const metaToken = document.querySelector('meta[name="csrf-token"]');
                        if (metaToken) {
                            metaToken.setAttribute('content', newToken.getAttribute('content'));
                        }
                        const csrfInput = document.querySelector('input[name="_token"]');
                        if (csrfInput) {
                            csrfInput.value = newToken.getAttribute('content');
                        }
                    }
                })
                .catch(error => {
                    console.log('CSRF token refresh failed:', error);
                });
            }, 240000); // Refresh every 4 minutes (240000ms)
        });
    </script>
</body>
</html>
