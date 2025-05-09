<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Export Rekap Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div id="exportContent">
        <!-- Tabel Data untuk ekspor PDF -->
        <table id="rekapTable" class="dataTable">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Total Penjualan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rekappenjualans as $rekap)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($rekap->tanggal)->format('F Y') }}</td>
                    <td>{{ 'Rp ' . number_format($rekap->total_penjualan, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Grafik untuk ekspor PDF -->
        <div id="chartContainer" style="margin-top: 20px; height: 300px;">
            <canvas id="rekapChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data untuk grafik
            const chartData = @json($chartData);
            
            // Inisialisasi grafik untuk ekspor PDF
            const rekapChart = document.getElementById('rekapChart').getContext('2d');
            const exportChart = new Chart(rekapChart, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Total Penjualan',
                        data: chartData.datasets[0].data,
                        backgroundColor: chartData.datasets[0].backgroundColor,
                        borderColor: chartData.datasets[0].backgroundColor.map(color => color.replace('0.7', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Grafik Rekap Penjualan'
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>