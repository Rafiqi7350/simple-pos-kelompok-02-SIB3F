<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 4px;
            color: #111;
        }

        .periode {
            margin-bottom: 16px;
            color: #555;
        }

        .summary-box {
            margin-bottom: 16px;
        }

        .summary-box p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th, td {
            border: 1px solid #999;
            padding: 6px 10px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <h1>Laporan Penjualan</h1>
    <p class="periode">Periode: {{ $from }} s.d. {{ $to }}</p>

    <div class="summary-box">
        <p><strong>Total Penjualan:</strong> Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
        <p><strong>Jumlah Transaksi:</strong> {{ $jumlahTransaksi }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th width="30%">Tanggal</th>
                <th width="35%">Kasir</th>
                <th width="25%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $item)
                <tr>
                    <td class="text-center">{{ $item->id }}</td>
                    <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>