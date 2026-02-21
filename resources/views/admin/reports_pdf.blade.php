<!DOCTYPE html>
<html>

<head>
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            background: #f4f4f4;
            padding: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-box {
            margin-top: 30px;
            border: 1px solid #333;
            padding: 10px;
            width: 40%;
            float: right;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .clear {
            clear: both;
        }

        .badge-success {
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Warung Bakso Panjang Rezeki</h1>
        <p>Laporan Keuangan Periode: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <!-- Pemasukan -->
    <div class="section-title">A. Pemasukan (Transaksi)</div>
    @if($incomes->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th>Tanggal</th>
                    <th>Metode</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomes as $index => $income)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $income->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ strtoupper($income->payment_method) }}</td>
                        <td class="text-right">Rp {{ number_format($income->total_amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right"><strong>Total Pemasukan</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($incomes->sum('total_amount'), 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    @else
        <p>Tidak ada data pemasukan pada periode ini.</p>
    @endif

    <!-- Pengeluaran -->
    <div class="section-title">B. Pengeluaran (Operasional)</div>
    @if($expenses->count() > 0)
        <table>
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $index => $expense)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $expense->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $expense->description }}</td>
                        <td class="text-right">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right"><strong>Total Pengeluaran</strong></td>
                    <td class="text-right"><strong>Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        <p>Tidak ada data pengeluaran pada periode ini.</p>
    @endif

    <!-- Ringkasan -->
    <div class="summary-box">
        <table style="border: none; margin: 0;">
            <tr>
                <td style="border: none;">Total Pemasukan</td>
                <td style="border: none;" class="text-right">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none;">Total Pengeluaran</td>
                <td style="border: none;" class="text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-top: 1px solid #333;">
                <td style="border: none; padding-top: 10px;"><strong>LABA BERSIH</strong></td>
                <td style="border: none; padding-top: 10px;" class="text-right"><strong>Rp
                        {{ number_format($netProfit, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

</body>

</html>