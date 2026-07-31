<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login MyCampus</title>
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/mycampus-theme.css') }}" rel="stylesheet">
    <style>
        body.bg-gradient-primary {
            background: linear-gradient(135deg, #0a0a1a 0%, #0d1117 25%, #0a1628 50%, #0d1117 75%, #0a0a1a 100%);
            background-size: 300% 300%;
            animation: gradientShift 12s ease infinite;
            min-height: 100vh;
            position: relative;
        }

        body.bg-gradient-primary::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(rgba(0, 240, 255, 0.03) 1px, transparent 1px),
                radial-gradient(rgba(168, 85, 247, 0.02) 1px, transparent 1px);
            background-size: 32px 32px, 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3) translateY(10px); }
            50% { opacity: 1; transform: scale(1.1) translateY(-2px); }
            70% { transform: scale(0.95) translateY(1px); }
            100% { transform: scale(1) translateY(0); }
        }

        @keyframes cardSlideUp {
            from { opacity: 0; transform: translateY(30px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes inputFocusPulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 240, 255, 0.2); }
            70% { box-shadow: 0 0 0 6px rgba(0, 240, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 240, 255, 0); }
        }

        @keyframes logoGlow {
            0%, 100% { filter: drop-shadow(0 0 8px rgba(0, 240, 255, 0.5)); }
            50% { filter: drop-shadow(0 0 18px rgba(0, 240, 255, 0.8)); }
        }

        .login-icon {
            animation: bounceIn 0.7s cubic-bezier(.4, 0, .2, 1) 0.2s both;
            display: inline-block;
        }

        .login-icon svg {
            width: 56px;
            height: 56px;
            animation: logoGlow 3s ease-in-out infinite;
        }

        .login-card-glass {
            background: rgba(22, 27, 34, 0.90);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 240, 255, 0.1);
            border-radius: 16px;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(0, 240, 255, 0.05) inset;
            animation: cardSlideUp 0.6s cubic-bezier(.4, 0, .2, 1) 0.1s both;
            overflow: hidden;
            position: relative;
        }

        .login-card-glass::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #00f0ff, #a855f7, #00f0ff);
            border-radius: 16px 16px 0 0;
        }

        .login-title {
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--mycampus-text);
        }

        .login-subtitle {
            color: var(--mycampus-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .login-form .form-group label {
            font-weight: 600;
            font-size: 0.82rem;
            color: var(--mycampus-muted);
            letter-spacing: 0.02em;
            margin-bottom: 0.3rem;
        }

        .login-form .form-control {
            border: 1.5px solid #30363d;
            border-radius: 10px;
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
            transition: all 0.25s cubic-bezier(.4, 0, .2, 1);
            background: #0d1117;
            color: var(--mycampus-text);
        }

        .login-form .form-control::placeholder {
            color: #484f58;
        }

        .login-form .form-control:hover {
            border-color: #484f58;
        }

        .login-form .form-control:focus {
            border-color: #00f0ff;
            box-shadow: 0 0 0 3px rgba(0, 240, 255, 0.12), 0 0 12px rgba(0, 240, 255, 0.06);
            background: #0d1117;
            animation: inputFocusPulse 0.6s ease;
        }

        .login-btn {
            background: linear-gradient(135deg, #00f0ff 0%, #0891b2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.02em;
            color: #0a0a1a;
            transition: all 0.3s cubic-bezier(.4, 0, .2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0, 240, 255, 0.25);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(0, 240, 255, 0.35);
            background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 2px 8px rgba(0, 240, 255, 0.2);
        }

        .remember-label {
            font-size: 0.82rem;
            color: var(--mycampus-muted);
            font-weight: 500;
        }

        .custom-control-label::before {
            border-radius: 4px;
            border: 1.5px solid #484f58;
            background-color: #0d1117;
            transition: all 0.2s ease;
        }

        .custom-control-input:checked ~ .custom-control-label::before {
            background: #00f0ff;
            border-color: #00f0ff;
        }

        .login-divider {
            border: none;
            height: 1px;
            background: linear-gradient(90deg, transparent, #30363d, transparent);
            margin: 1.2rem 0;
        }

        .login-footer {
            color: #484f58;
            font-size: 0.78rem;
        }

        .login-footer i {
            color: var(--mycampus-primary);
        }

        .text-primary {
            color: var(--mycampus-primary) !important;
        }
    </style>
</head>
<body class="bg-gradient-primary">
<div class="container position-relative" style="z-index: 1;">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-xl-4 col-lg-5 col-md-6">
            <div class="card o-hidden border-0 login-card-glass">
                <div class="card-body p-0">
                    <div class="p-5">
                        <div class="text-center mb-4">
                            <div class="mb-3 login-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" fill="none">
                                    <defs>
                                        <linearGradient id="neonGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                                            <stop offset="0%" stop-color="#00f0ff"/>
                                            <stop offset="100%" stop-color="#a855f7"/>
                                        </linearGradient>
                                    </defs>
                                    <!-- Cap top (diamond) -->
                                    <polygon points="32,8 56,22 32,30 8,22" fill="url(#neonGrad)" opacity="0.9"/>
                                    <!-- Cap tassel line -->
                                    <line x1="48" y1="16" x2="54" y2="28" stroke="#00f0ff" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
                                    <circle cx="54" cy="29" r="2" fill="#00f0ff" opacity="0.8"/>
                                    <!-- Cap body -->
                                    <polygon points="18,24 32,32 46,24 46,28 32,36 18,28" fill="url(#neonGrad)" opacity="0.7"/>
                                    <!-- Book shape -->
                                    <rect x="20" y="36" width="24" height="16" rx="2" fill="none" stroke="url(#neonGrad)" stroke-width="1.5" opacity="0.6"/>
                                    <line x1="32" y1="36" x2="32" y2="52" stroke="url(#neonGrad)" stroke-width="1" opacity="0.4"/>
                                    <!-- Circuit lines -->
                                    <line x1="8" y1="22" x2="4" y2="22" stroke="#00f0ff" stroke-width="0.8" opacity="0.4"/>
                                    <line x1="4" y1="22" x2="4" y2="30" stroke="#00f0ff" stroke-width="0.8" opacity="0.4"/>
                                    <circle cx="4" cy="30" r="1" fill="#00f0ff" opacity="0.5"/>
                                    <line x1="56" y1="22" x2="60" y2="22" stroke="#a855f7" stroke-width="0.8" opacity="0.4"/>
                                    <line x1="60" y1="22" x2="60" y2="14" stroke="#a855f7" stroke-width="0.8" opacity="0.4"/>
                                    <circle cx="60" cy="14" r="1" fill="#a855f7" opacity="0.5"/>
                                    <!-- Corner nodes -->
                                    <circle cx="8" cy="22" r="1.5" fill="#00f0ff" opacity="0.6"/>
                                    <circle cx="56" cy="22" r="1.5" fill="#a855f7" opacity="0.6"/>
                                </svg>
                            </div>
                            <h1 class="h3 login-title mb-1">Login MyCampus</h1>
                            <p class="login-subtitle mb-0">Sistem Informasi Prestasi Mahasiswa</p>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('login.process') }}" method="POST" class="login-form" autocomplete="off">
                            @csrf
                            <div class="form-group">
                                <label><i class="fas fa-envelope fa-sm mr-1"></i> Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Masukkan email" autocomplete="off" required autofocus>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-lock fa-sm mr-1"></i> Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox small">
                                    <input type="checkbox" name="remember" class="custom-control-input" id="remember">
                                    <label class="custom-control-label remember-label" for="remember">Ingat saya</label>
                                </div>
                            </div>
                            <button type="submit" class="btn login-btn btn-block">
                                <i class="fas fa-sign-in-alt mr-1"></i> Login
                            </button>
                        </form>

                        <hr class="login-divider">

                        <div class="text-center">
                            <small class="login-footer">
                                <i class="fas fa-shield-alt mr-1"></i> MyCampus &copy; {{ date('Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
