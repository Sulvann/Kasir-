@extends('layouts.cashier')

@section('styles')
    <style>
        /* Reset Layout Default */
        .main-content {
            padding: 0 !important;
            height: 100vh;
            overflow: hidden;
            background: #f1f5f9;
        }

        .container-split {
            display: flex;
            height: 100vh;
        }

        /* --- LEFT PANEL: PREVIEW STRUK --- */
        .panel-preview {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e2e8f0;
            padding: 2rem;
            overflow-y: auto;
        }

        .paper {
            background: white;
            width: 380px;
            /* Standard thermal paper width approx */
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            font-family: 'Courier New', Courier, monospace;
            /* Monospace for receipt look */
            color: #000;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .shop-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .receipt-info {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 1rem 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .total-section {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 1.1rem;
            margin-top: 1rem;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
        }

        .sign-area {
            margin-top: 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4rem;
        }

        /* --- RIGHT PANEL: ACTIONS --- */
        .panel-actions {
            flex: 1;
            background: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2rem;
            border-left: 1px solid #cbd5e1;
        }

        .action-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 2rem;
        }

        .action-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-print {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: 0.2s;
        }

        .btn-print:hover {
            background: #1e293b;
        }

        .btn-whatsapp {
            background: #25D366;
            /* WA Color */
            color: white;
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: 0.2s;
        }

        .btn-whatsapp:hover {
            background: #128C7E;
        }

        .btn-back {
            background: white;
            border: 1px solid #cbd5e1;
            color: #64748b;
            padding: 1rem;
            width: 100%;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-back:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .wa-input {
            width: 100%;
            padding: 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        /* PRINT STYLES */
        @media print {
            .panel-actions {
                display: none !important;
            }

            .panel-preview {
                background: white;
                padding: 0;
                overflow: visible;
                display: block;
            }

            .main-content {
                height: auto;
                overflow: visible;
            }

            .paper {
                box-shadow: none;
                width: 100%;
                padding: 0;
            }

            @page {
                margin: 0;
            }

            body {
                margin: 1.6cm;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-split">
        <!-- LEFT: PREVIEW STRUK -->
        <div class="panel-preview">
            <div class="paper">
                <div class="receipt-header">
                    <div class="shop-name">Warung Bakso Panjang Rezeki</div>
                </div>

                <div class="receipt-info">
                    <span>Nama Kasir</span>
                    <span>{{ $transaction->user->name ?? 'Kasir' }}</span>
                </div>
                <div class="receipt-info">
                    <span>Waktu & Tanggal</span>
                    <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="divider"></div>

                <div style="margin-bottom: 1rem;">
                    <!-- Headers for items -->
                    <div
                        style="display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 0.5rem; font-size: 0.9rem;">
                        <span>List Makanan</span>
                        <span>Total Harga</span>
                    </div>

                    @foreach($transaction->items as $item)
                        <div class="item-row">
                            <span>{{ $item->product->name }} <span
                                    style="font-size: 0.8em; color: #555;">x{{ $item->quantity }}</span></span>
                            <span>{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="divider"></div>

                <div class="total-section">
                    <span>Metode Pembayaran</span>
                    <span>{{ $transaction->payment_method == 'cash' ? 'Tunai' : 'QRIS' }}</span>
                </div>

                <div class="total-section" style="margin-top: 0.5rem; font-size: 1.3rem;">
                    <span>Total</span>
                    <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                </div>
                @if($transaction->payment_method == 'cash')
                    <div class="item-row" style="margin-top: 0.5rem; justify-content: space-between;">
                        <span>Tunai</span>
                        <span>Rp {{ number_format($transaction->cash_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="item-row" style="justify-content: space-between;">
                        <span>Kembali</span>
                        <span>Rp {{ number_format($transaction->cash_amount - $transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="receipt-footer">
                    <p style="font-weight: bold; margin-bottom: 2rem;">Terima Kasih Banyak</p>

                    <div class="sign-area">
                        <div>(Stempel Warung Bakso Panjang Rezeki)</div>
                        <div>(TTD Pemilik Warung)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: ACTIONS -->
        <div class="panel-actions">
            <div style="text-align: center; margin-bottom: 1rem;">
                <h1 style="font-size: 1.8rem; font-weight: 800; color: #0f172a;">Transaksi Berhasil!</h1>
                <p style="color: #64748b;">Silakan cetak struk atau kirim ke pelanggan.</p>
            </div>

            <div class="action-card">
                <div class="action-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak Struk
                </div>
                <button class="btn-print" onclick="window.print()">
                    Cetak Sekarang
                </button>
            </div>

            <div class="action-card">
                <div class="action-title">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                        </path>
                    </svg>
                    Kirim WhatsApp
                </div>
                <input type="text" id="waNumber" class="wa-input" placeholder="Nomor WhatsApp (08xxx)" value="">
                <button class="btn-whatsapp" onclick="sendWhatsapp()" id="btnWa">
                    Kirim via WhatsApp
                </button>
            </div>

            <button class="btn-back" onclick="window.location.href='/cashier'">
                Kembali ke Menu Kasir
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        async function sendWhatsapp() {
            const phone = document.getElementById('waNumber').value;
            const btn = document.getElementById('btnWa');

            if (!phone) return alert('Masukkan nomor WhatsApp');

            btn.disabled = true;
            btn.textContent = 'Mengirim...';

            try {
                const res = await fetch('/cashier/send-whatsapp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        phone: phone,
                        transaction_id: {{ $transaction->id }}
                    })
                });

                const data = await res.json();

                if (res.ok) {
                    alert('Berhasil dikirim!');
                    document.getElementById('waNumber').value = '';
                } else {
                    alert(data.message || 'Gagal mengirim');
                }
            } catch (error) {
                console.error(error);
                alert('Terjadi kesalahan');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Kirim via WhatsApp';
            }
        }
    </script>
@endsection