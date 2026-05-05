<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - WebGIS Wisata</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            --bg-body: #0f172a;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-body);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* Animated Background Shapes */
        .bg-shape {
            position: absolute;
            z-index: 0;
            filter: blur(80px);
            opacity: 0.4;
            border-radius: 50%;
        }
        .shape-1 { width: 400px; height: 400px; background: #4f46e5; top: -100px; left: -100px; }
        .shape-2 { width: 300px; height: 300px; background: #6366f1; bottom: -50px; right: -50px; }

        .login-card {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header i {
            font-size: 3rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .login-header h2 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .input-group-text {
            border-radius: 0.75rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login {
            background: var(--primary-gradient);
            border: none;
            border-radius: 0.75rem;
            padding: 0.875rem;
            font-weight: 600;
            color: #fff;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.4);
            color: #fff;
        }

        .alert {
            border-radius: 0.75rem;
            font-size: 0.9rem;
            border: none;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-geo-fill"></i>
            <h2>Admin Login</h2>
            <p>Silakan masuk untuk mengelola WebGIS Wisata</p>
        </div>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <div><?= session()->getFlashdata('error') ?></div>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('admin/loginProcess') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="Masukkan email" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="password" name="password" placeholder="Masukkan password" required>
                    <span class="input-group-text border-start-0 text-muted" id="togglePassword">
                        <i class="bi bi-eye-slash" id="eyeIcon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                Sign In <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="mt-4 text-center small text-muted">
            &copy; <?= date('Y') ?> WebGIS Wisata. All rights reserved.
        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
                this.classList.add('text-primary');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
                this.classList.remove('text-primary');
            }
        });
    </script>
</body>
</html>
