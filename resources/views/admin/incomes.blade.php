@extends('layouts.admin')

@section('styles')
    <style>
        /* Aesthetic Table Styles */
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .aesthetic-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.05);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .card-header-aesthetic {
            padding: 2rem;
            background: white;
            border-bottom: 1px solid #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .aesthetic-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .aesthetic-table th {
            background: #f8fafc;
            color: #0f172a;
            /* Navy */
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1.25rem 1.5rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .aesthetic-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.95rem;
            vertical-align: middle;
            transition: background 0.2s;
        }

        .aesthetic-table tr:last-child td {
            border-bottom: none;
        }

        .aesthetic-table tr:hover td {
            background: #f8fafc;
        }

        /* Column Specifics */
        .col-id {
            font-family: 'Plus Jakarta Sans', monospace;
            font-weight: 600;
            color: #64748b;
        }

        .col-amount {
            font-weight: 700;
            color: #0f172a;
            /* Navy */
            font-size: 1rem;
        }

        /* Badges */
        .badge-aesthetic {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.025em;
        }

        .badge-qris {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
        }

        .badge-cash {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #dcfce7;
        }

        .status-dot {
            height: 6px;
            width: 6px;
            border-radius: 50%;
            background-color: #10b981;
            display: inline-block;
            margin-right: 6px;
        }
    </style>
@endsection

@section('content')
    <div class="page-container">
        <div class="page-header" style="margin-bottom: 2rem;">
            <h1 style="font-size: 1.875rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Riwayat Pemasukan</h1>
            <p style="color: #64748b; font-size: 1rem;">Pantau arus kas masuk dari setiap transaksi penjualan real-time.</p>
        </div>

        <div class="aesthetic-card">
            <div class="table-responsive">
                <table class="aesthetic-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">ID Transaksi</th>
                            <th style="width: 25%;">Waktu Transaksi</th>
                            <th style="width: 25%;">Nominal Masuk</th>
                            <th style="width: 20%;">Metode</th>
                            <th style="width: 15%;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody">
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 4rem; color: #94a3b8;">
                                <div style="margin-bottom: 1rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="animate-spin">
                                        <path d="M21 12a9 9 0 1 1-6.219-8.56"></path>
                                    </svg>
                                </div>
                                Sedang memuat data transaksi...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', loadTransactions);

        async function loadTransactions() {
            try {
                const res = await fetch('/cashier-api/transactions');
                const result = await res.json();

                const formatRupiah = (num) => {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(num);
                };

                const tbody = document.getElementById('transactionsTableBody');

                if (result.status === 'success' && result.data.length > 0) {
                    tbody.innerHTML = result.data.map(trx => {
                        let methodBadge = trx.payment_method === 'qris'
                            ? `<span class="badge-aesthetic badge-qris">QRIS</span>`
                            : `<span class="badge-aesthetic badge-cash">Tunai</span>`;

                        const dateObj = new Date(trx.created_at);
                        const dateStr = dateObj.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                        const timeStr = dateObj.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                        return `
                            <tr>
                                <td class="col-id">#${trx.id}</td>
                                <td>
                                    <div style="font-weight: 600; color: #334155;">${dateStr}</div>
                                    <div style="font-size: 0.8rem; color: #94a3b8;">${timeStr} WIB</div>
                                </td>
                                <td class="col-amount">${formatRupiah(trx.total_amount)}</td>
                                <td>${methodBadge}</td>
                                <td>
                                    <div style="display: flex; align-items: center; font-weight: 600; color: #0f172a; font-size: 0.85rem;">
                                        <span class="status-dot"></span>
                                        Berhasil
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 4rem;">
                                <div style="background: #f1f5f9; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                                </div>
                                <h3 style="color: #0f172a; font-weight: 700; margin-bottom: 0.5rem;">Belum Ada Transaksi</h3>
                                <p style="color: #64748b;">Transaksi penjualan yang berhasil akan muncul di sini.</p>
                            </td>
                        </tr>
                    `;
                }

            } catch (error) {
                console.error('Error loading transactions:', error);
                document.getElementById('transactionsTableBody').innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align:center; color: #ef4444; padding: 2rem; font-weight: 600;">
                            Gagal memuat data transaksi. Silakan refresh halaman.
                        </td>
                    </tr>
                `;
            }
        }
    </script>
@endsection