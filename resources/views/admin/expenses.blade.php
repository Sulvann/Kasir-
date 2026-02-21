@extends('layouts.admin')

@section('styles')
    <style>
        .split-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            padding: 2rem;
        }

        /* Form Section */
        .form-header {
            margin-bottom: 1.5rem;
        }

        .form-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #0f172a;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 1.5rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }

        .btn-submit {
            background: #0f172a;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            height: 46px;
            /* Match input height */
        }

        .btn-submit:hover {
            background: #1e293b;
        }

        /* Table Section */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 1rem;
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .text-right {
            text-align: right;
        }

        .btn-edit {
            background: #f1f5f9;
            color: #334155;
            border: none;
            cursor: pointer;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-edit:hover {
            background: #e2e8f0;
        }

        .btn-danger {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            cursor: pointer;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .btn-submit {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">Manajemen Pengeluaran</h1>
    </div>

    <div class="split-container">
        <!-- Bagian Atas: Form Input -->
        <div class="card">
            <div class="form-header">
                <h2>Input Pengeluaran Baru</h2>
            </div>
            <form id="expenseForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Deskripsi (Apa yang dibeli)</label>
                        <input type="text" id="description" class="form-input" placeholder="Contoh: Beli Kertas Thermal"
                            required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total Harga (Rupiah)</label>
                        <input type="number" id="amount" class="form-input" placeholder="0" min="1" required>
                    </div>
                    <button type="submit" class="btn-submit">
                        Kirim
                    </button>
                </div>
            </form>
        </div>

        <!-- Bagian Bawah: List Pengeluaran -->
        <div class="card">
            <div class="form-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2>Riwayat Pengeluaran</h2>
                <div style="font-size: 0.9rem; color: #64748b;">
                    Total: <span id="totalExpense" style="color: #0f172a; font-weight: 700;">Rp 0</span>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal & Waktu</th>
                            <th>Deskripsi</th>
                            <th class="text-right">Jumlah</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="expensesTable">
                        <tr>
                            <td colspan="4" style="text-align: center;">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const API_URL = '/admin/api/expenses';
        let isEditing = false;
        let editingId = null;

        // Helper: Format Currency
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        // Helper: Format Date
        const formatDate = (dateString) => {
            const options = {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        // Load Data
        async function loadExpenses() {
            try {
                const res = await fetch(API_URL, {
                    headers: { 'Accept': 'application/json' }
                });
                const responseData = await res.json();
                const expenses = responseData.data;

                renderTable(expenses);
                calculateTotal(expenses);
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('expensesTable').innerHTML =
                    '<tr><td colspan="4" style="text-align: center; color: red;">Gagal memuat data</td></tr>';
            }
        }

        // Render Table
        function renderTable(expenses) {
            const tbody = document.getElementById('expensesTable');

            if (expenses.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">Belum ada pengeluaran dicatat</td></tr>';
                return;
            }

            tbody.innerHTML = expenses.map(item => `
                                <tr>
                                    <td>${formatDate(item.created_at)}</td>
                                    <td style="font-weight: 500;">${item.description}</td>
                                    <td class="text-right" style="font-family: monospace; font-size: 1rem;">${formatRupiah(item.amount)}</td>
                                    <td class="text-right">
                                        <button onclick="editExpense(${item.id}, '${item.description.replace(/'/g, "\\\\'")}', ${item.amount})" class="btn-edit" title="Ubah"><svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit</button>
                                        <button onclick="deleteExpense(${item.id})" class="btn-danger" title="Hapus"><svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg> Hapus</button>
                                    </td>
                                </tr>
                            `).join('');
        }

        // Calculate Total
        function calculateTotal(expenses) {
            const total = expenses.reduce((sum, item) => sum + parseInt(item.amount), 0);
            document.getElementById('totalExpense').textContent = formatRupiah(total);
        }

        // Handle Submit
        document.getElementById('expenseForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const description = document.getElementById('description').value;
            const amount = document.getElementById('amount').value;
            const btn = e.target.querySelector('button');

            // Disable button
            btn.disabled = true;
            btn.textContent = 'Mengirim...';

            try {
                const url = isEditing ? `${API_URL}/${editingId}` : API_URL;
                const method = isEditing ? 'PUT' : 'POST';

                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ description, amount })
                });

                const data = await res.json();

                if (res.ok) {
                    // Reset form
                    document.getElementById('expenseForm').reset();
                    loadExpenses(); // Reload list
                } else {
                    alert(data.message || 'Gagal menyimpan data');
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan jaringan');
            } finally {
                btn.disabled = false;
                btn.textContent = isEditing ? 'Kirim' : 'Kirim';
                isEditing = false;
                editingId = null;
            }
        });

        // Edit Expense
        window.editExpense = function (id, description, amount) {
            document.getElementById('description').value = description;
            document.getElementById('amount').value = amount;
            isEditing = true;
            editingId = id;

            // Ubah tombol submit
            const btn = document.querySelector('.btn-submit');
            btn.textContent = 'Update';

            // Scroll ke form
            document.getElementById('expenseForm').scrollIntoView({ behavior: 'smooth' });
        };

        // Delete Action
        window.deleteExpense = async function (id) {
            if (!confirm('Hapus data pengeluaran ini?')) return;

            try {
                const res = await fetch(`${API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (res.ok) {
                    loadExpenses();
                } else {
                    alert('Gagal menghapus data');
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan');
            }
        }

        // Initial Load
        document.addEventListener('DOMContentLoaded', loadExpenses);
    </script>
@endsection