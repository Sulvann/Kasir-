@extends('layouts.admin')

@section('styles')
    <style>
        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-edit {
            background: #f1f5f9;
            color: #334155;
        }

        .btn-edit:hover {
            background: #e2e8f0;
        }

        .btn-danger {
            background: #fee2e2;
            color: #ef4444;
        }

        .btn-danger:hover {
            background: #fecaca;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Manajemen Kasir</h1>
        <button onclick="openModal()" class="btn-primary"
            style="background: #0f172a; color: white; padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; cursor: pointer;">
            + Tambah Kasir
        </button>
    </div>

    <div class="card"
        style="background: white; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #e2e8f0; text-align: left;">
                    <th style="padding: 1rem;">Nama</th>
                    <th style="padding: 1rem;">Email</th>
                    <th style="padding: 1rem;">Dibuat Pada</th>
                    <th style="padding: 1rem; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <!-- Data loaded via JS -->
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="userModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
        <div style="background: white; padding: 2rem; border-radius: 1rem; width: 400px; max-width: 90%;">
            <h2 id="modalTitle" style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: bold;">Tambah Kasir</h2>
            <form id="userForm">
                <input type="hidden" id="userId">

                <div style="margin-bottom: 1rem;">
                    <label
                        style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 500;">Nama</label>
                    <input type="text" id="name" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label
                        style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 500;">Email</label>
                    <input type="email" id="email" required
                        style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 500;">Kata
                        Sandi</label>
                    <small style="color: #64748b; font-size: 0.75rem;">Biarkan Kosong Jika Tidak Ingin Mengubah Kata
                        Sandi</small>
                    <input type="password" id="password"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 500;">Konfirmasi
                        Kata Sandi</label>
                    <input type="password" id="password_confirmation"
                        style="width: 100%; padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 0.375rem;">
                </div>

                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <button type="button" onclick="closeModal()"
                        style="padding: 0.5rem 1rem; background: #0f172a; color: white; border: none; border-radius: 0.375rem; cursor: pointer;">Batal</button>
                    <button type="submit"
                        style="padding: 0.5rem 1rem; background: #0f172a; color: white; border: none; border-radius: 0.375rem; cursor: pointer;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const API_URL = '/admin/api/users';
        let isEditing = false;

        // Load Users
        async function loadUsers() {
            try {
                const response = await fetch(API_URL);
                const users = await response.json();
                const tbody = document.getElementById('usersTableBody');

                tbody.innerHTML = users.map(user => `
                                                                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                                                                            <td style="padding: 1rem;">
                                                                                                <div style="color: #0f172a; font-weight: 500;">${user.name}</div>
                                                                                            </td>
                                                                                            <td style="padding: 1rem; color: #64748b;">${user.email}</td>
                                                                                            <td style="padding: 1rem; color: #64748b;">${new Date(user.created_at).toLocaleDateString()}</td>
                                                                                            <td style="padding: 1rem; text-align: right;">
                                                                                                <button class="btn btn-edit" onclick="editUser(${user.id}, '${user.name}', '${user.email}')"><svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit</button>
                                                                                                <button class="btn btn-danger" onclick="deleteUser(${user.id})"><svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus</button>
                                                                                            </td>
                                                                                        </tr>
                                                                                    `).join('');
            } catch (error) {
                console.error('Error loading users:', error);
                alert('Failed to load users');
            }
        }

        // Initialize
        loadUsers();

        // Modal Logic
        const modal = document.getElementById('userModal');
        const form = document.getElementById('userForm');

        function openModal() {
            modal.style.display = 'flex';
            modalTitle.textContent = 'Add Cashier';
            form.reset();
            document.getElementById('userId').value = '';
            isEditing = false;
        }

        function closeModal() {
            modal.style.display = 'none';
            form.reset();
        }

        window.editUser = function (id, name, email) {
            modal.style.display = 'flex';
            document.getElementById('modalTitle').textContent = 'Edit Cashier';
            document.getElementById('userId').value = id;
            document.getElementById('name').value = name;
            document.getElementById('email').value = email;
            isEditing = true;
        }

        // Submit Form
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const id = document.getElementById('userId').value;
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            };

            // Remove empty password fields if editing
            if (!formData.password) delete formData.password;
            if (!formData.password_confirmation) delete formData.password_confirmation;

            const url = isEditing ? `${API_URL}/${id}` : API_URL;
            const method = isEditing ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                if (!res.ok) {
                    const data = await res.json();
                    throw new Error(data.message || 'Something went wrong');
                }

                closeModal();
                loadUsers();
                alert(isEditing ? 'User updated successfully' : 'User created successfully');
            } catch (error) {
                alert(error.message);
            }
        });

        // Delete User
        window.deleteUser = async function (id) {
            if (!confirm('Are you sure you want to delete this user?')) return;

            try {
                const res = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!res.ok) throw new Error('Failed to delete');

                loadUsers();
            } catch (error) {
                alert(error.message);
            }
        }
    </script>
@endsection