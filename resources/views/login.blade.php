<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RealKasir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: #f1f5f9;
            background-image:
                radial-gradient(at 0% 0%, rgba(15, 23, 42, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(30, 58, 138, 0.05) 0px, transparent 50%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: white;
            padding: 3rem;
            border-radius: 24px;
            width: 100%;
            max-width: 420px;
            box-shadow:
                0 4px 6px -1px rgba(15, 23, 42, 0.05),
                0 10px 15px -3px rgba(15, 23, 42, 0.05),
                0 0 0 1px rgba(15, 23, 42, 0.02);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: left;
            margin-bottom: 2.5rem;
        }

        .login-header h1 {
            color: #0f172a;
            text-align: center;
            /* Slate 900 */
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .login-header p {
            color: #64748b;
            /* Slate 500 */
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #334155;
            /* Slate 700 */
            font-size: 0.875rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            color: #0f172a;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            background: white;
            border-color: #1e3a8a;
            /* Navy Blue */
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: #0f172a;
            /* Navy/Slate 900 */
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.1);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .loading-spinner {
            display: none;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 2px solid white;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 12px;
            font-size: 0.9rem;
            display: none;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            color: #166534;
        }

        /* Decorative Element (Navy Accent) */
        .brand-accent {
            width: 40px;
            height: 4px;
            background: #1e3a8a;
            border-radius: 2px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="brand-accent"></div>
        <div class="login-header">
            <h1>Warung Bakso Panjang Rezeki</h1>
            <p></p>
        </div>

        <div id="alertMessage" class="alert"></div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input type="email" id="email" class="form-control" placeholder="example@gmail.com" required>
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <span class="btn-text">Masuk</span>
                <div class="loading-spinner" id="spinner"></div>
            </button>
        </form>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const btnText = document.querySelector('.btn-text');
        const spinner = document.getElementById('spinner');
        const alertBox = document.getElementById('alertMessage');

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // UI Loading State
            loginBtn.disabled = true;
            btnText.style.display = 'none';
            spinner.style.display = 'block';
            alertBox.style.display = 'none';

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    // Success
                    // localStorage.setItem('token', data.access_token); // No longer needed for session auth
                    // localStorage.setItem('user', JSON.stringify(data.user)); // Optional, depends on frontend needs

                    if (data.user.role === 'admin') {
                        showAlert('Login successful! Redirecting to Dashboard...', 'success');
                        setTimeout(() => {
                            window.location.href = '/admin/dashboard';
                        }, 1000);
                    } else {
                        showAlert('Login successful! Redirecting to Cashier...', 'success');
                        setTimeout(() => {
                            window.location.href = '/cashier';
                        }, 1000);
                    }
                } else {
                    // Error
                    showAlert(data.message || 'Login failed. Please check your credentials.', 'error');
                }
            } catch (error) {
                showAlert('Unable to connect to the server. Please check your internet connection.', 'error');
                console.error('Login error:', error);
            } finally {
                // Reset UI
                loginBtn.disabled = false;
                btnText.style.display = 'block';
                spinner.style.display = 'none';
            }
        });

        function showAlert(message, type) {
            alertBox.textContent = message;
            alertBox.className = `alert alert-${type}`;
            alertBox.style.display = 'flex';
        }
    </script>
</body>

</html>