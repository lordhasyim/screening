<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin - Dashboard</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }

        .admin-login-container {
            width: 100%;
            max-width: 450px;
            padding: 2rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, var(--calm-blue), var(--compassion-purple));
            color: white;
            padding: 2rem;
            text-align: center;
            margin: -2rem -2rem 2rem -2rem;
        }

        .login-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .form-floating {
            margin-bottom: 1rem;
        }

        .form-control:focus {
            border-color: var(--compassion-purple);
            box-shadow: 0 0 0 0.25rem rgba(156, 39, 176, 0.25);
        }

        .btn-admin-login {
            background: linear-gradient(135deg, var(--calm-blue), var(--compassion-purple));
            border: none;
            padding: 12px 2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-admin-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        }

        .remember-check {
            accent-color: var(--compassion-purple);
        }

        .back-to-site {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .back-to-site:hover {
            color: white;
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            border: none;
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>

<body>
    <!-- Floating Background Shapes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="admin-login-container">
        <!-- Login Card -->
        <div class="login-card p-4">
            <!-- Header -->
            <div class="login-header">
                <div class="login-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h3 class="mb-1"> Admin Dashboard</h3>
                <p class="mb-0 opacity-90">Sistem Manajemen Kesehatan Mental</p>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') ?? $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('admin.login') }}" id="loginForm">
                @csrf

                <!-- email Field -->
                <div class="form-floating">
                    <input type="text" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="email" 
                           required 
                           autocomplete="email"
                           autofocus>
                    <label for="email">email</label>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- NIP/Code Field -->
                <div class="form-floating">
                    <input type="text" 
                           class="form-control @error('nip') is-invalid @enderror" 
                           id="nip" 
                           name="nip" 
                           value="{{ old('nip') }}" 
                           placeholder="NIP/Kode" 
                           required 
                           autocomplete="off">
                    <label for="nip">NIP / Kode</label>
                    @error('nip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="form-floating">
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Password" 
                           required 
                           autocomplete="current-password">
                    <label for="password">Password</label>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-3">
                    <input class="form-check-input remember-check" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-muted" for="remember">
                        Ingat saya selama 30 hari
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 mb-3">
                    <button class="btn btn-primary btn-lg btn-admin-login" type="submit" id="loginBtn">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Masuk ke Dashboard
                    </button>
                </div>
            </form>

            <!-- Additional Links -->
            <div class="text-center">
                <small class="text-muted">
                    Hanya untuk admin dan tim skrining yang berwenang
                </small>
            </div>
        </div>

        <!-- Back to Site -->
        <div class="text-center mt-4">
            <a href="{{ route('quiz.index') }}" class="back-to-site">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Halaman Utama
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function() {
            // Form submission handling
            $('#loginForm').on('submit', function(e) {
                const $btn = $('#loginBtn');
                const originalText = $btn.html();
                
                // Show loading state
                $btn.html('<div class="spinner-border spinner-border-sm me-2"></div>Memproses...').prop('disabled', true);
                
                // Re-enable after 10 seconds as fallback
                setTimeout(function() {
                    $btn.html(originalText).prop('disabled', false);
                }, 10000);
            });

            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Add some interactive effects
            $('.form-control').on('focus', function() {
                $(this).parent().addClass('focused');
            }).on('blur', function() {
                $(this).parent().removeClass('focused');
            });
        });
    </script>
</body>
</html>