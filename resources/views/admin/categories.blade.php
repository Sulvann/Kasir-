@extends('layouts.admin')

@section('styles')
    <style>
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

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

        .btn-primary {
            background: #0f172a;
            color: white;
        }

        .btn-primary:hover {
            background: #1e293b;
        }

        .btn-danger {
            background: #fee2e2;
            color: #ef4444;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        .btn-edit {
            background: #f1f5f9;
            color: #334155;
        }

        .btn-edit:hover {
            background: #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 1rem 1.5rem;
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 1rem 1.5rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            position: relative;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #334155;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.95rem;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Kategori</h1>
        <button class="btn btn-primary" onclick="openModal()">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Kategori
        </button>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="categoriesTable">
                <tr>
                    <td colspan="2" style="text-align: center;">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" style="margin-bottom: 1.5rem;">Tambah Kategori</h2>
            <form id="categoryForm">
                <input type="hidden" id="categoryId">
                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" id="categoryName" class="form-control" required placeholder="e.g. Makanan">
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-edit" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let categories = [];
        const API_URL = '/admin/api/categories';
        // const TOKEN = localStorage.getItem('token'); // Not used

        document.addEventListener('DOMContentLoaded', loadCategories);

        async function loadCategories() {
            try {
                const res = await fetch(API_URL, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                categories = data.data;
                renderTable();
            } catch (error) {
                console.error(error);
                alert('Failed to load categories');
            }
        }

        function renderTable() {
            const tbody = document.getElementById('categoriesTable');
            if (categories.length === 0) {
                tbody.innerHTML = '<tr><td colspan="2" style="text-align: center;">No categories found</td></tr>';
                return;
            }

            tbody.innerHTML = categories.map(cat => `
                                        <tr>
                                            <td style="font-weight: 500;">${cat.name}</td>
                                            <td style="text-align: right;">
                                                <button class="btn btn-edit" onclick="editCategory(${cat.id})"><svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit</button>
                                                <button class="btn btn-danger" onclick="deleteCategory(${cat.id})"><svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus</button>
                                            </td>
                                        </tr>
                                    `).join('');
        }

        // Modal Logic
        const modal = document.getElementById('categoryModal');
        const form = document.getElementById('categoryForm');
        const title = document.getElementById('modalTitle');
        const idInput = document.getElementById('categoryId');
        const nameInput = document.getElementById('categoryName');

        function openModal(isEdit = false) {
            modal.classList.add('active');
            if (!isEdit) {
                title.textContent = 'Add Category';
                form.reset();
                idInput.value = '';
            }
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        function editCategory(id) {
            const cat = categories.find(c => c.id === id);
            idInput.value = cat.id;
            nameInput.value = cat.name;
            title.textContent = 'Edit Category';
            openModal(true);
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = idInput.value;
            const name = nameInput.value;
            const method = id ? 'PUT' : 'POST';
            const url = id ? `${API_URL}/${id}` : API_URL;

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ name })
                });

                if (res.ok) {
                    closeModal();
                    loadCategories();
                } else {
                    const data = await res.json();
                    alert(data.message || 'Error saving category');
                }
            } catch (error) {
                console.error(error);
                alert('Something went wrong');
            }
        });

        async function deleteCategory(id) {
            if (!confirm('Are you sure?')) return;

            try {
                const res = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) loadCategories();
            } catch (error) {
                alert('Failed to delete');
            }
        }

        // Close modal on outside click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    </script>
@endsection