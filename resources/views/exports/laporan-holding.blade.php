<!-- resources/views/exports/rekap-holding.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian (Holding)</title>
</head>
<body>
     <!-- Header Gambar -->
     <div style="position: absolute; top: 0; left: 0; width: 100%; height: auto; z-index: -1;">
        <img src="images/HEADER.png" alt="Header" style="width: 100%; height: auto;" />
    </div>
    
    <!-- Footer -->
    <footer style="position: fixed; bottom: 10px; width: 100%; text-align: center; font-size: 10px;">
        Laporan Marketing | Laporan Rekap Penjualan Perusahaan
    </footer>
    
    <!-- Konten -->
    <div style="gap: 100px; width: 100%; margin-top: 50px;">
        <div style="width: 30%; float: left; padding-right: 20px;">
            <h2 style="font-size: 14px; text-align: center; margin-bottom: 10px;">Tabel Data</h2>
            <table style="border-collapse: collapse; width: 100%; font-size: 10px;" border="1">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #000; padding: 1px;">Bulan</th>
                        <th style="border: 1px solid #000; padding: 2px;">Perusahaan</th>
                        <th style="border: 1px solid #000; padding: 2px;">Total Penjualan</th>
                    </tr>
                </thead>
                <tbody>
            @php $total = 0; @endphp
            @foreach($laporanholdings as $holding)
                @php $total += $holding->nilai; @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($holding->bulan)->translatedFormat('F Y') }}</td>
                    <td>{{ $holding->perusahaan->nama_perusahaan }}</td>
                    <td>{{ $holding->nilai_formatted }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2">Total</td>
                <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div style="width: 65%; text-align:center; margin-left: 20px;">
    <h2 style="font-size: 14px; margin-bottom: 10px;">Grafik Laporan (Holding)</h2>
    <img src="{{ $chartImagePath }}" alt="Grafik Penjualan">
</div>
</div>
</body>
</html>