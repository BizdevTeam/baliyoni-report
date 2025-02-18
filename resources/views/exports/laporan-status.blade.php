<!-- resources/views/exports/rekap-statuspaket.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Status Paket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 16px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .chart-container {
            width: 100%;
            text-align: center;
            margin-top: 30px;
        }
        .chart-container img {
            max-width: 100%;
            height: auto;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Laporan Status Paket</div>
        <div class="subtitle">Periode: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Status</th>
                <th>Nilai Paket</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($statuspakets as $index => $statuspaket)
                @php $total += $statuspaket->total_paket; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($statuspaket->bulan)->translatedFormat('F Y') }}</td>
                    <td>{{ $statuspaket->status }}</td>
                    <td>{{ $statuspaket->total_paket_formatted }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td>{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="chart-container">
        <img src="{{ $chartImagePath }}" alt="Grafik Status Paket">
    </div>
</body>
</html>