<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 120px;
            position: relative;
        }
        .header-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: auto;
            z-index: -1;
        }
        .header-image {
            width: 100%;
            height: auto;
        }
        .content-wrapper {
            width: 100%;
            margin-top: 30px;
            margin-bottom: 40px;
        }
        .flex-container {
            width: 100%;
            gap: 100px;
        }
        .table-section {
            width: 30%;
            float: left;
            padding-right: 20px;
        }
        .chart-section {
            width: 65%;
            float: left;
            margin-left: 20px;
            text-align: center;
        }
        .section-title {
            font-size: 14px;
            text-align: center;
            margin-bottom: 10px;
            font-weight: normal;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            border: 1px solid #000;
        }
        .data-table th {
            background-color: #f2f2f2;
            border: 1px solid #000;
            padding: 1px;
        }
        .data-table td {
            border: 1px solid #000;
            padding: 2px;
        }
        .chart-image {
            width: 100%;
            height: auto;
        }
        .footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            font-size: 10px;
        }
        /* Clear float after flex container */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <!-- Header Gambar -->
    <div class="header-container">
        <img src="images/HEADER.png" alt="Header" class="header-image">
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="flex-container clearfix">
            <!-- Table Section -->
            <div class="table-section">
                <h2 class="section-title">Tabel Data</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Total Penjualan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach($rekappenjualans as $penjualan)
                        @php $total += $penjualan->total_penjualan; @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($penjualan->bulan)->translatedFormat('F Y') }}</td>
                            <td>Rp {{ number_format($penjualan->total_penjualan, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Chart Section -->
            <div class="chart-section">
                <h2 class="section-title">Grafik Laporan Penjualan</h2>
                <img src="{{ $chartImagePath }}" alt="Grafik Penjualan" class="chart-image">
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Laporan Marketing | Laporan Rekap Penjualan
    </div>
</body>
</html>