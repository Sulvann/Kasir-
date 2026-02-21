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
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .product-img {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            background: #f1f5f9;
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
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .form-group {
            margin-bottom: 1.25rem;
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
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Manajemen Produk</h1>
        <button class="btn btn-primary" onclick="openModal()">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Produk
        </button>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody id="productsTable">
                <tr>
                    <td colspan="6" style="text-align: center;">Memuat...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" style="margin-bottom: 1.5rem;">Tambah Produk</h2>
            <form id="productForm">
                <input type="hidden" id="productId">

                <div class="form-group">
                    <label>Nama Produk</label>
                    <input type="text" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select id="category_id" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                    </select>
                </div>

                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label>Harga (Rp)</label>
                        <input type="number" id="price" class="form-control" required>
                    </div>
                    <div>
                        <label>Stok</label>
                        <input type="number" id="stock" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Gambar Produk</label>
                    <input type="file" id="image" class="form-control" accept="image/*">
                    <div id="imagePreview" style="margin-top: 0.5rem; display: none;">
                        <img src="" style="height: 100px; border-radius: 8px;">
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-edit" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let products = [];
        const API_URL = '/admin/api/products';
        const CAT_API_URL = '/admin/api/categories';
        // const TOKEN = localStorage.getItem('token');

        document.addEventListener('DOMContentLoaded', () => {
            loadCategories();
            loadProducts();
        });

        // Helper: Format Currency
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
        }

        async function loadCategories() {
            try {
                const res = await fetch(CAT_API_URL, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                const select = document.getElementById('category_id');
                data.data.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.name;
                    select.appendChild(option);
                });
            } catch (error) { console.error('Error loading categories', error); }
        }

        async function loadProducts() {
            try {
                const res = await fetch(API_URL, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                products = data.data;
                renderTable();
            } catch (error) {
                console.error(error);
                document.getElementById('productsTable').innerHTML = '<tr><td colspan="6" style="text-align: center;">Gagal memuat data</td></tr>';
            }
        }

        function renderTable() {
            const tbody = document.getElementById('productsTable');
            if (products.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Belum ada produk</td></tr>';
                return;
            }

            tbody.innerHTML = products.map(p => {
                const imgSrc = p.image ? `/storage/${p.image}` : 'https://via.placeholder.com/48?text=Img';
                const catName = p.category ? p.category.name : '-';
                return `
                        <tr>
                            <td><img src="${imgSrc}" class="product-img"></td>
                            <td style="font-weight: 500;">${p.name}</td>
                            <td><span style="background: #eff6ff; color: #1e40af; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem;">${catName}</span></td>
                            <td>${formatRupiah(p.price)}</td>
                            <td>${p.stock}</td>
                            <td style="text-align: right;">
                                <button class="btn btn-edit" onclick="editProduct(${p.id})"><svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit</button>
                                <button class="btn btn-danger" onclick="deleteProduct(${p.id})"><svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus</button>
                            </td>
                        </tr>
                        `;
            }).join('');
        }

        const modal = document.getElementById('productModal');
        const form = document.getElementById('productForm');

        function openModal(isEdit = false) {
            modal.classList.add('active');
            if (!isEdit) {
                document.getElementById('modalTitle').textContent = 'Tambah Produk';
                form.reset();
                document.getElementById('productId').value = '';
                document.getElementById('imagePreview').style.display = 'none';
            }
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        function editProduct(id) {
            const p = products.find(i => i.id === id);
            document.getElementById('productId').value = p.id;
            document.getElementById('name').value = p.name;
            document.getElementById('category_id').value = p.category_id;
            document.getElementById('price').value = p.price;
            document.getElementById('stock').value = p.stock;

            document.getElementById('modalTitle').textContent = 'Edit Produk';

            if (p.image) {
                const preview = document.getElementById('imagePreview');
                preview.style.display = 'block';
                preview.querySelector('img').src = `/storage/${p.image}`;
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }

            openModal(true);
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const id = document.getElementById('productId').value;
            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);
            formData.append('category_id', document.getElementById('category_id').value);
            formData.append('price', document.getElementById('price').value);
            formData.append('stock', document.getElementById('stock').value);

            const imageFile = document.getElementById('image').files[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }

            // Method Spoofing for Laravel PUT with FormData
            if (id) {
                formData.append('_method', 'PUT');
            }

            const url = id ? `${API_URL}/${id}` : API_URL;

            try {
                const res = await fetch(url, {
                    method: 'POST', // Always POST for FormData with file (even for PUT)
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();

                if (res.ok) {
                    closeModal();
                    loadProducts();
                    alert(data.message);
                } else {
                    alert(data.message || 'Gagal menyimpan produk');
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan');
            }
        });

        async function deleteProduct(id) {
            if (!confirm('Yakin ingin menghapus produk ini?')) return;

            try {
                const res = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (res.ok) {
                    loadProducts();
                    alert(data.message);
                }
            } catch (error) {
                alert('Gagal menghapus');
            }
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    </script>
@endsection