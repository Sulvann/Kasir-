<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RealKasir</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #0f172a;
            /* Slate 900 */
            color: white;
            padding: 1.5rem 1rem;
            /* Padding horizontal ditambah sedikit */
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .brand {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: white;
            /* Brand tetap di tengah */
            text-align: center;
            padding: 0 0.75rem;
            line-height: 1.4;
        }

        .brand span {
            color: #3b82f6;
            /* Blue 500 */
        }

        .nav-menu {
            list-style: none;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            /* Reset Bootstrap padding/margin */
            padding-left: 0 !important;
            margin-left: 0 !important;
            margin-bottom: 0 !important;
        }

        .nav-item {
            width: 100%;
            padding-left: 0;
            margin-left: 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.75rem;
            /* Padding: atas/bawah 0.75rem, kiri 0.5rem, kanan 1rem */
            padding: 0.75rem 1rem 0.75rem 0.5rem;
            margin-left: 0;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            width: 100%;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link svg {
            width: 22px;
            height: 22px;
            min-width: 22px;
            opacity: 0.8;
            /* Memastikan ikon selalu di tengah secara vertikal */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-link:hover svg,
        .nav-link.active svg {
            opacity: 1;
            filter: drop-shadow(0 0 5px rgba(59, 130, 246, 0.5));
        }

        .user-profile {
            margin-top: auto;
            /* Memastikan profil selalu di bawah */
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            /* PERBAIKAN: Layout profil dibuat menyamping (row) agar lebih hemat tempat vertikal */
            flex-direction: row;
            align-items: center;
            gap: 0.75rem;
            text-align: left;
            padding-bottom: 0.5rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            min-width: 40px;
            background: #3b82f6;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            color: white;
            font-size: 1.1rem;
        }

        .user-info {
            overflow: hidden;
            /* Mencegah teks panjang merusak layout */
        }

        .user-info h4 {
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info p {
            font-size: 0.75rem;
            color: #94a3b8;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-logout {
            margin-top: 0.5rem;
            width: 100%;
            padding: 0.75rem;
            background: rgba(220, 38, 38, 0.1);
            color: #fca5a5;
            border: 1px solid rgba(220, 38, 38, 0.2);
            /* Tambah border tipis */
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-logout:hover {
            background: rgba(220, 38, 38, 0.2);
            color: #fca5a5;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            /* Sesuaikan dengan lebar sidebar */
            flex: 1;
            padding: 2rem 2.5rem;
            /* Padding atas dikurangi sedikit agar konten naik */
            width: calc(100% - 250px);
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
        }

        /* Loading Overlay */
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f1f5f9;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #0f172a;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive Fix */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
            }

            /* Anda mungkin butuh tombol toggle untuk mobile di masa depan */
        }
    </style>
    @yield('styles')
</head>

<body>
    <div id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <aside class="sidebar">
        <div class="brand">
            Warung Bakso<br><span>Panjang Rezeki</span>
        </div>
        <ul class="nav-menu">

            <li class="nav-item">
                <a href="/admin/dashboard" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                        </path>
                    </svg>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="/admin/account" class="nav-link {{ request()->is('admin/account*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Akun
                </a>
            </li>

            <li class="nav-item">
                <a href="/admin/users" class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    Kelola Kasir
                </a>
            </li>

            <li class="nav-item">
                <a href="/admin/categories" class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Kategori
                </a>
            </li>

            <li class="nav-item">
                <a href="/admin/products" class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Produk
                </a>
            </li>

            <li class="nav-item">
                <a href="/admin/incomes" class="nav-link {{ request()->is('admin/incomes*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Pemasukan
                </a>
            </li>

            <li class="nav-item">
                <a href="/admin/expenses" class="nav-link {{ request()->is('admin/expenses*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                    </svg>
                    Pengeluaran
                </a>
            </li>

            <li class="nav-item">
                <a href="/admin/reports" class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Laporan Keuangan
                </a>
            </li>
        </ul>

        <div class="user-profile">
            <div class="user-avatar" id="userAvatar">A</div>
            <div class="user-info">
                <h4 id="userName">{{ auth()->user()->name }}</h4>
                <p id="userEmail">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <button class="btn-logout" onclick="logout()">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </button>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

    <script>
        // Auth Check handled by server middleware
        document.getElementById('loadingOverlay').style.display = 'none';

        // Avatar Init
        const userName = "{{ auth()->user()->name }}";
        document.getElementById('userAvatar').textContent = userName.charAt(0).toUpperCase();

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    }
                }).finally(() => {
                    window.location.href = '/login';
                });
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>

</html>