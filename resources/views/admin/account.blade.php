@extends('layouts.admin')

@section('styles')
    <style>
        /* --- Local Variables to match Admin Theme --- */
        :root {
            --c-navy-primary: #0f172a;
            --c-navy-light: #1e293b;
            --c-gray-text: #64748b;
            --c-border-subtle: #e2e8f0;
            --radius-card: 16px;
        }

        /* Standard Page Header */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--c-navy-primary);
        }

        .page-subtitle {
            color: var(--c-gray-text);
            font-size: 0.95rem;
        }

        /* Profile Card */
        .card-profile {
            border: 1px solid var(--c-border-subtle);
            border-radius: var(--radius-card);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
        }

        .profile-header-bg {
            height: 140px;
            background: linear-gradient(135deg, var(--c-navy-primary) 0%, #334155 100%);
            position: relative;
        }

        /* Decorative Pattern */
        .profile-header-bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: radial-gradient(circle at 2px 2px, rgba(255, 255, 255, 0.1) 1px, transparent 0);
            background-size: 20px 20px;
            opacity: 0.4;
        }

        .view-content-wrapper {
            padding: 0 2rem 2.5rem 2rem;
            text-align: center;
            margin-top: -70px;
            /* Overlap avatar */
            position: relative;
            z-index: 2;
        }

        /* Avatar */
        .avatar-wrapper {
            position: relative;
            width: 140px;
            height: 140px;
            margin: 0 auto 1rem auto;
        }

        .profile-img-lg {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        /* Info */
        .user-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--c-navy-primary);
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }

        .user-email {
            color: var(--c-gray-text);
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .user-role-badge {
            background: #eff6ff;
            color: var(--c-navy-primary);
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        /* Stats Grid inside Card */
        .stats-row {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin: 2rem 0;
            padding-top: 2rem;
            border-top: 1px dashed var(--c-border-subtle);
        }

        .stats-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--c-gray-text);
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .stats-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--c-navy-primary);
        }

        /* Interactive Buttons */
        .btn-navy {
            background: var(--c-navy-primary);
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-navy:hover {
            background: var(--c-navy-light);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
        }

        /* --- Modal Styling --- */
        .modal-content {
            border: none;
            border-radius: 20px;
        }

        .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--c-border-subtle);
            padding: 1.5rem;
            border-radius: 20px 20px 0 0;
        }

        .modal-title {
            font-weight: 700;
            color: var(--c-navy-primary);
        }

        .modal-body {
            padding: 2rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--c-navy-primary);
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 10px;
            border: 1px solid var(--c-border-subtle);
        }

        .form-control:focus {
            border-color: var(--c-navy-primary);
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
        }

        /* Edit Avatar in Modal */
        .edit-avatar-wrapper {
            width: 100px;
            height: 100px;
            position: relative;
            margin: 0 auto 1rem;
        }

        .btn-camera {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 32px;
            height: 32px;
            background: var(--c-navy-primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-camera:hover {
            transform: scale(1.1);
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title"></h1>
            <p class="page-subtitle"></p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">

            <!-- Profile Card -->
            <div class="card card-profile">
                <div class="profile-header-bg"></div>

                <div class="view-content-wrapper">
                    <div class="avatar-wrapper">
                        <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0f172a&color=fff&bold=true' }}"
                            alt="Avatar" class="profile-img-lg">
                    </div>

                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-email">{{ auth()->user()->email }}</div>

                    <span class="user-role-badge">
                        <i class="fas fa-shield-alt me-1"></i> Administrator
                    </span>

                    <div class="stats-row">
                        <div>
                            <div class="stats-label">Status Akun</div>
                            <div class="stats-value text-success">
                                <i class="fas fa-check-circle me-1"></i> Aktif
                            </div>
                        </div>
                        <div style="border-left: 1px solid var(--c-border-subtle);"></div>
                        <div>
                            <div class="stats-label">Bergabung</div>
                            <div class="stats-value">
                                {{ auth()->user()->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-pen me-2"></i> Edit Profil
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Edit Profil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateProfileForm" enctype="multipart/form-data">
                        @csrf

                        <div class="text-center mb-4">
                            <div class="edit-avatar-wrapper">
                                <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=0f172a&color=fff' }}"
                                    id="previewAvatar" class="profile-img-lg">
                                <label for="avatarInput" class="btn-camera">
                                    <i class="fas fa-camera fa-xs"></i>
                                </label>
                                <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*">
                            </div>
                            <small class="text-muted" id="fileName">Klik ikon kamera untuk mengganti foto</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Email</label>
                            <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}"
                                required>
                        </div>

                        <div class="my-4 position-relative text-center">
                            <hr>
                            <span class="bg-white px-3 text-muted small position-absolute"
                                style="top: -10px; left: 50%; transform: translateX(-50%); font-weight: 500;">
                                Ganti Password (Opsional)
                            </span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter"
                                autocomplete="new-password">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Ulangi password baru" autocomplete="new-password">
                        </div>

                        <div class=" d-grid">
                            <button type="submit" class="btn btn-navy" id="btnSave">
                                <span class="spinner-border spinner-border-sm d-none me-2" id="loadingSpinner"></span>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Avatar Preview
        document.getElementById('avatarInput').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('previewAvatar').src = e.target.result;
                }
                reader.readAsDataURL(file);
                document.getElementById('fileName').textContent = file.name;
            }
        });

        // Handle Submit
        document.getElementById('updateProfileForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = document.getElementById('btnSave');
            const spinner = document.getElementById('loadingSpinner');
            const formData = new FormData(this);

            btn.disabled = true;
            spinner.classList.remove('d-none');

            try {
                const res = await fetch("{{ route('admin.profile.update') }}", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 422) {
                        let errorMsg = '';
                        Object.values(data.errors).forEach(err => errorMsg += `• ${err[0]}<br>`);
                        throw new Error(errorMsg);
                    }
                    throw new Error(data.message || 'Terjadi kesalahan.');
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Profil berhasil diperbarui',
                    timer: 1500,
                    showConfirmButton: false,
                    confirmButtonColor: '#0f172a'
                });

                window.location.reload();

            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    html: err.message,
                    confirmButtonColor: '#0f172a'
                });
                btn.disabled = false;
                spinner.classList.add('d-none');
            }
        });
    </script>
@endsection