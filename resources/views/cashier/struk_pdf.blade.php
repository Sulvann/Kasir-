<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran #{{ $transaction->transaction_id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 100%;
            padding: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .shop-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .info-label {
            display: table-cell;
            text-align: left;
        }

        .info-value {
            display: table-cell;
            text-align: right;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
        }

        .total-row {
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>

<body>
    {{-- Wrapper utama struk PDF --}}
    <div class="container">
        {{-- Header identitas toko dan judul struk --}}
        <div class="header">
            <div class="shop-name" style="line-height: 1.2;">
                <div style="color: #000; font-weight: bold;">Warung Bakso</div>
                <div style="color: #FF0000; font-weight: 900;">Panjang Rezeki</div>
            </div>
            {{-- Judul dokumen struk --}}
            <div>Struk Pembayaran</div>
        </div>

        {{-- Nomor transaksi struk --}}
        <div class="info-row">
            <div class="info-label">No. Transaksi</div>
            <div class="info-value">#{{ $transaction->transaction_id }}</div>
        </div>

        {{-- Nama kasir yang memproses transaksi --}}
        <div class="info-row">
            <div class="info-label">Kasir</div>
            <div class="info-value">{{ $transaction->user->name ?? 'Kasir' }}</div>
        </div>

        {{-- Waktu transaksi dibuat --}}
        <div class="info-row">
            <div class="info-label">Waktu</div>
            <div class="info-value">{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
        </div>

        {{-- Garis pemisah sebelum daftar item --}}
        <div class="divider"></div>

        {{-- Daftar item pesanan pada struk --}}
        <table>
            @foreach($transaction->items as $item)
                <tr>
                    <td colspan="2" style="font-weight: bold;">{{ $item->product->name }}</td>
                </tr>
                @if($item->note)
                    <tr>
                        <td colspan="2" style="font-size: 10px; font-style: italic; color: #333; padding-left: 8px;">
                            @php
                                $notes = explode(' | ', $item->note);
                            @endphp
                            @foreach($notes as $n)
                                <div>- {{ $n }}</div>
                            @endforeach
                        </td>
                    </tr>
                @endif
                <tr>
                    <td style="padding-bottom: 8px;">{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}
                    </td>
                    <td class="text-right" style="padding-bottom: 8px;">
                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </table>

        {{-- Garis pemisah sebelum ringkasan pembayaran --}}
        <div class="divider"></div>

        {{-- Metode pembayaran transaksi --}}
        <div class="info-row">
            <div class="info-label">Metode Pembayaran</div>
            <div class="info-value">{{ $transaction->payment_method == 'cash' ? 'Tunai (Cash)' : 'Non-Tunai (QRIS)' }}
            </div>
        </div>

        {{-- Total pembayaran transaksi --}}
        <div class="info-row total-row">
            <div class="info-label">Total</div>
            <div class="info-value">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
        </div>

        @if($transaction->payment_method == 'cash')
            {{-- Detail pembayaran tunai --}}
            <div class="info-row">
                <div class="info-label">Tunai</div>
                <div class="info-value">Rp {{ number_format($transaction->cash_amount, 0, ',', '.') }}</div>
            </div>
            {{-- Nominal kembalian transaksi tunai --}}
            <div class="info-row">
                <div class="info-label">Kembali</div>
                <div class="info-value">Rp
                    {{ number_format($transaction->cash_amount - $transaction->total_amount, 0, ',', '.') }}
                </div>
            </div>
        @else
            {{-- Detail pembayaran non-tunai --}}
            <div class="info-row">
                <div class="info-label">Metode</div>
                <div class="info-value">NON-TUNAI (QRIS)</div>
            </div>
        @endif

        {{-- Garis pemisah sebelum footer --}}
        <div class="divider"></div>

        {{-- Footer ucapan terima kasih --}}
        <div class="footer">
            <p>Terima Kasih Banyak</p>
        </div>
    </div>
</body>

</html>
