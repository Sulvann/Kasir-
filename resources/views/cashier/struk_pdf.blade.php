<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk Pembayaran #{{ $transaction->id }}</title>
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
    <div class="container">
        <div class="header">
            <div class="shop-name">Warung Bakso Panjang Rezeki</div>
            <div>Struk Pembayaran</div>
        </div>

        <div class="info-row">
            <div class="info-label">No. Transaksi</div>
            <div class="info-value">#{{ $transaction->id }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kasir</div>
            <div class="info-value">{{ $transaction->user->name ?? 'Kasir' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Waktu</div>
            <div class="info-value">{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
        </div>

        <div class="divider"></div>

        <table>
            @foreach($transaction->items as $item)
                <tr>
                    <td colspan="2">{{ $item->product->name }}</td>
                </tr>
                <tr>
                    <td>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        <div class="info-row total-row">
            <div class="info-label">Total</div>
            <div class="info-value">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
        </div>

        @if($transaction->payment_method == 'cash')
            <div class="info-row">
                <div class="info-label">Tunai</div>
                <div class="info-value">Rp {{ number_format($transaction->cash_amount, 0, ',', '.') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kembali</div>
                <div class="info-value">Rp
                    {{ number_format($transaction->cash_amount - $transaction->total_amount, 0, ',', '.') }}</div>
            </div>
        @else
            <div class="info-row">
                <div class="info-label">Metode</div>
                <div class="info-value">NON-TUNAI (QRIS)</div>
            </div>
        @endif

        <div class="footer">
            <p>Terima Kasih Banyak</p>
            <br><br>
            <p>( TTD Pemilik Warung )</p>
        </div>
    </div>
</body>

</html>