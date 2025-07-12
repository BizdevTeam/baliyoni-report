<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Laporan Gabungan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th {
            background-color: #f2f2f2;
        }
        .content-html ol { list-style-type: decimal; margin-left: 20px; }
        .content-html ul { list-style-type: disc; margin-left: 20px; }
        .content-html li { margin-bottom: 4px; }

        /* Print specific styles */
        @media print {
            body, html {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .no-print {
                display: none;
            }
            .page-break {
                page-break-before: always;
            }
            .page {
                page-break-inside: avoid;
                width: 100%;
                background: white;
            }
            @page {
                margin: 0;
                size: A4 landscape;
            }
            table {
                page-break-inside: auto;
                width: 100%;
                border-collapse: collapse;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
            table, th, td {
                border: 1px solid black;
            }
            th, td {
                padding: 4px;
                font-size: 10px;
                text-align: center;
                font-family: serif;
            }
            .header-img {
                width: 100%;
            }
        }
    </style>
</head>
<body onload="window.print()">

    {{-- Bagian ini hanya akan terlihat di halaman cetak --}}
    <div id="exportContent">

        @php $isFirstPage = true; @endphp

        @foreach($selectedReports as $reportKey)
            @if(!$isFirstPage)
                <div class="page-break"></div>
            @endif
            @php
                $isFirstPage = false;
                // Asumsikan controller mengirimkan data dalam array asosiatif $data
                $reportData = $data[$reportKey] ?? ['rekap' => [], 'chart' => []];
            @endphp

            <div class="page">
                @switch($reportKey)

                    @case('penjualan')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Rekap Penjualan</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Penjualan (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-start text-[14px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-start text-[14px] font-serif">{{ $item['Total Penjualan'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Rekap Penjualan</p></div>
                        @break

                    @case('penjualan_perusahaan')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Penjualan Perusahaan</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Perusahaan</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Penjualan (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[14px]">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[14px]">{{ $item['Perusahaan'] }}</td>
                                            <td class="border border-black p-1 text-center text-[14px]">{{ $item['Total Penjualan'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Rekap Penjualan Perusahaan</p></div>
                        @break

                    @case('paket_admin')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                             <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Paket Administrasi</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Website</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Paket</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Website'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Total Paket'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-paket w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Paket Administrasi</p></div>
                        @break

                    @case('status_paket')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                             <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Status Paket</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Status</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Paket</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Status'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Total Paket'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-paket w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Status Paket</p></div>
                        @break

                    @case('per_instansi')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Laporan Per Instansi</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Instansi</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Nilai (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Instansi'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Nilai'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Per Instansi</p></div>
                        @break

                    @case('holding')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Laporan Holding</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Perusahaan</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Nilai (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Perusahaan'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Nilai'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Marketing - Laporan Holding</p></div>
                        @break
                    
                    @case('stok')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Laporan Stok</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Stok (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Stok'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Procurements - Laporan Stok</p></div>
                        @break

                    @case('pembelian_outlet')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Pembelian Outlet</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Stok (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Total'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Procurements - Laporan Pembelian Outlet</p></div>
                        @break

                    @case('negosiasi')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Negosiasi</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Stok (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Total'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Procurements - Laporan Negosiasi</p></div>
                        @break

                    @case('pendapatan_asp')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Negosiasi</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Nilai Pendapatan (Rp)</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Pelaksana'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Nilai'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Supports - Laporan Rekap Pendapatan Servis ASP</p></div>
                        @break

                    @case('piutang_asp')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Negosiasi</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Nilai Pendapatan (Rp)</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Pelaksana'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Nilai'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Supports - Laporan Rekap Piutang Servis ASP</p></div>
                        @break

                    @case('pengiriman')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Negosiasi</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Pengiriman (Rp)</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Pelaksana'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Total'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Supports - Laporan Pengiriman</p></div>
                        @break

                        @case('laba_rugi')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-center p-6">
                            <div class="text-center">
                                <h2 class="text-center font-serif mb-4">Laporan Laba Rugi</h2>
                                @forelse ($reportData['rekap'] as $item)
                                    <img src="{{ $item['Gambar'] }}" alt="Laba Rugi Image" class="h-[500px] w-auto mx-auto object-contain border border-gray-300 mb-4">
                                @empty
                                    <p>Tidak ada gambar untuk ditampilkan.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Laba Rugi</p></div>
                        @break

                        @case('neraca')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-center p-6">
                            <div class="text-center">
                                <h2 class="text-center font-serif mb-4">Laporan Neraca</h2>
                                @forelse ($reportData['rekap'] as $item)
                                    <img src="{{ $item['Gambar'] }}" alt="Laba Rugi Image" class="h-[500px] w-auto mx-auto object-contain border border-gray-300 mb-4">
                                @empty
                                    <p>Tidak ada gambar untuk ditampilkan.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Neraca</p></div>
                        @break
                        
                        @case('rasio')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-center p-6">
                            <div class="text-center">
                                <h2 class="text-center font-serif mb-4">Laporan Rasio</h2>
                                @forelse ($reportData['rekap'] as $item)
                                    <img src="{{ $item['Gambar'] }}" alt="Laba Rugi Image" class="h-[500px] w-auto mx-auto object-contain border border-gray-300 mb-4">
                                @empty
                                    <p>Tidak ada gambar untuk ditampilkan.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Rasio</p></div>
                        @break

                        @case('khps')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Kas Hutang Piutang Stok</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Kas</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Hutang</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Piutang</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Kas'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Hutang'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Piutang'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Stok'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Kas Hutang Piutang Stok</p></div>
                        @break

                        @case('arus_kas')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Arus Kas</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Kas Masuk</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Kas Keluar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                        <tr>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Masuk'] }}</td>
                                            <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Keluar'] }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="2" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Arus Kas</p></div>
                        @break

                        @case('ppn')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-center p-6">
                            <div class="text-center">
                                <h2 class="text-center font-serif mb-4">Laporan PPN</h2>
                                @forelse ($reportData['rekap'] as $item)
                                    <img src="{{ $item['Gambar'] }}" alt="Laba Rugi Image" class="h-[500px] w-auto mx-auto object-contain border border-gray-300 mb-4">
                                @empty
                                    <p>Tidak ada gambar untuk ditampilkan.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Accounting - Laporan PPN</p></div>
                        @break

                        @case('taxplanning')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-center p-6">
                            <div class="text-center">
                                <h2 class="text-center font-serif mb-4">Laporan Tax Planning</h2>
                                @forelse ($reportData['rekap'] as $item)
                                    <img src="{{ $item['Gambar'] }}" alt="Laba Rugi Image" class="h-[500px] w-auto mx-auto object-contain border border-gray-300 mb-4">
                                @empty
                                    <p>Tidak ada gambar untuk ditampilkan.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan Accounting - Laporan Tax Planning</p></div>
                        @break

                        {{-- Batas Print Out Done --}}

                        @case('instagram')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-center p-6">
                            <div class="text-center">
                                <h2 class="text-center font-serif mb-4">Laporan Multimedia Instagram</h2>
                                @forelse ($reportData['rekap'] as $item)
                                    <img src="{{ $item['Gambar'] }}" alt="Laba Rugi Image" class="h-[500px] w-auto mx-auto object-contain border border-gray-300 mb-4">
                                @empty
                                    <p>Tidak ada gambar untuk ditampilkan.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan IT - Laporan Multimedia Instagram</p></div>
                        @break

                        @case('tiktok')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-center p-6">
                            <div class="text-center">
                                <h2 class="text-center font-serif mb-4">Laporan Multimedia Tiktok</h2>
                                @forelse ($reportData['rekap'] as $item)
                                    <img src="{{ $item['Gambar'] }}" alt="Laba Rugi Image" class="h-[500px] w-auto mx-auto object-contain border border-gray-300 mb-4">
                                @empty
                                    <p>Tidak ada gambar untuk ditampilkan.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan IT - Laporan Multimedia Tiktok</p></div>
                        @break   

                        @case('bizdev')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-center p-6">
                            <div class="text-center">
                                <h2 class="text-center font-serif mb-4">Laporan Bizdev</h2>
                                @forelse ($reportData['rekap'] as $item)
                                    <img src="{{ $item['Gambar'] }}" alt="Laba Rugi Image" class="h-[500px] w-auto mx-auto object-contain border border-gray-300 mb-4">
                                @empty
                                    <p>Tidak ada gambar untuk ditampilkan.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan IT - Laporan Bizdev</p></div>
                        @break   

=                    @case('bizdev1')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="p-6">
                            <h2 class="text-center font-serif items-center mb-4">Tabel Data Laporan Bizdev</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Kendala</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['rekap'] as $item)
                                    <tr>
                                        <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                        <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $item['Kendala'] !!}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan IT - Laporan Bizdev</p></div>
                        @break

=                    @case('ptbos')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="p-6">
                            <h2 class="text-center font-serif items-center mb-4">Tabel Data PT BOS</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="p-1 text-[10px]">Tanggal</th>
                                        <th class="p-1 text-[10px]">Pekerjaan</th>
                                        <th class="p-1 text-[10px]">Kondisi Bulan Lalu</th>
                                        <th class="p-1 text-[10px]">Kondisi Bulan Ini</th>
                                        <th class="p-1 text-[10px]">Update</th>
                                        <th class="p-1 text-[10px]">Rencana Implementasi</th>
                                        <th class="p-1 text-[10px]">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['rekap'] as $item)
                                    <tr>
                                        <td class="p-1 text-center text-[8px] font-serif">{{ $item['Tanggal'] }}</td>
                                        <td class="p-1 content-html text-[8px] align-top text-justify font-serif">{!! $item['Pekerjaan'] !!}</td>
                                        <td class="p-1 content-html text-[8px] align-top text-justify font-serif">{!! $item['Kondisi Bulan Lalu'] !!}</td>
                                        <td class="p-1 content-html text-[8px] align-top text-justify font-serif">{!! $item['Kondisi Bulan Ini'] !!}</td>
                                        <td class="p-1 content-html text-[8px] align-top text-justify font-serif">{!! $item['Update'] !!}</td>
                                        <td class="p-1 content-html text-[8px] align-top text-justify font-serif">{!! $item['Rencana Implementasi'] !!}</td>
                                        <td class="p-1 content-html text-[8px] align-top text-justify font-serif">{!! $item['Keterangan'] !!}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan HRGA - Laporan PT BOS</p></div>
                        @break

=                    @case('ijasa')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="p-6">
                            <h2 class="text-center font-serif items-center mb-4">Tabel Data iJASA</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Jam</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Permasalahan</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Impact</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Troubleshooting</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Resolve Tanggal</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Resolve Jam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['rekap'] as $item)
                                    <tr>
                                        <td class="border border-black p-1 text-center text-[12px] font-serif">{{ $item['Tanggal'] }}</td>
                                        <td class="border border-black p-1 text-center text-[12px] font-serif">{{ $item['Jam'] }}</td>
                                        <td class="border border-black p-1 content-html text-[12px] align-top text-justify font-serif">{!! $item['Permasalahan'] !!}</td>
                                        <td class="border border-black p-1 content-html text-[12px] align-top text-justify font-serif">{!! $item['Impact'] !!}</td>
                                        <td class="border border-black p-1 content-html text-[12px] align-top text-justify font-serif">{!! $item['Troubleshooting'] !!}</td>
                                        <td class="border border-black p-1 text-center text-[12px] font-serif">{{ $item['Resolve Tanggal'] }}</td>
                                        <td class="border border-black p-1 text-center text-[12px] font-serif">{{ $item['Resolve Jam'] }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan HRGA - Laporan iJASA</p></div>
                        @break

=                    @case('ijasagambar')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-center p-6">
                            <div class="text-center">
                                <h2 class="text-center font-serif mb-4">Laporan iJASA Gambar</h2>
                                @forelse ($reportData['rekap'] as $item)
                                    <img src="{{ $item['Gambar'] }}" alt="iJASA Image" class="h-[500px] w-auto mx-auto object-contain border border-gray-300 mb-4">
                                @empty
                                    <p>Tidak ada gambar untuk ditampilkan.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan HRGA - Laporan iJASA Gambar</p></div>
                        @break

                    @case('sakit')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Sakit</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Sakit (Hari)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                            <tr>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Nama'] }}</td>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Total'] }}</td>
                                            </tr>
                                        @empty
                                        <tr><td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan HRGA - Laporan Sakit</p></div>
                        @break

                    @case('cuti')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Cuti</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Cuti (Hari)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                            <tr>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Nama'] }}</td>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Total'] }}</td>
                                            </tr>
                                        @empty
                                        <tr><td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan HRGA - Laporan Cuti</p></div>
                        @break

                    @case('izin')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Izin</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Izin (Hari)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                            <tr>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Nama'] }}</td>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Total'] }}</td>
                                            </tr>
                                        @empty
                                        <tr><td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan HRGA - Laporan Izin</p></div>
                        @break

                    @case('terlambat')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="flex justify-between p-6">
                            <div class="w-1/2 pr-10">
                                <h2 class="text-center font-serif">Tabel Data Terlambat</h2>
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Pelaksana</th>
                                            <th class="border border-black p-1 text-center text-[10px] font-serif">Total Terlambat (Hari)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reportData['rekap'] as $item)
                                            <tr>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Nama'] }}</td>
                                                <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Total'] }}</td>
                                            </tr>
                                        @empty
                                        <tr><td colspan="3" class="border border-black p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <h2 class="text-center font-serif">Grafik Laporan</h2>
                                <canvas class="chart-export-rp w-[700px] max-h-[500px]" data-chart='@json($reportData["chart"])'></canvas>
                            </div>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan HRGA - Laporan Terlambat</p></div>
                        @break

                    @case('spi')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="p-6">
                            <h2 class="text-center font-serif items-center mb-4">Tabel Data SPI</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Aspek</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Masalah</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Solusi</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Implementasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['rekap'] as $item)
                                    <tr>
                                        <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                        <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $item['Aspek'] !!}</td>
                                        <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $item['Masalah'] !!}</td>
                                        <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $item['Solusi'] !!}</td>
                                        <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $item['Implementasi'] !!}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan SPI - Laporan SPI Operasional</p></div>
                        @break

                    @case('spiit')
                        <div><img src="{{ asset('images/HEADER.png') }}" alt="Header" class="header-img"></div>
                        <div class="p-6">
                            <h2 class="text-center font-serif items-center mb-4">Tabel Data SPI IT</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Tanggal</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Aspek</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Masalah</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Solusi</th>
                                        <th class="border border-black p-1 text-center text-[10px] font-serif">Implementasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['rekap'] as $item)
                                    <tr>
                                        <td class="border border-black p-1 text-center text-[10px] font-serif">{{ $item['Tanggal'] }}</td>
                                        <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $item['Aspek'] !!}</td>
                                        <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $item['Masalah'] !!}</td>
                                        <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $item['Solusi'] !!}</td>
                                        <td class="border border-black p-1 content-html text-[10px] align-top text-justify font-serif">{!! $item['Implementasi'] !!}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="7" class="p-1 text-center text-[10px] font-serif">Tidak ada data.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="border-t text-center pt-4"><p class="text-sm font-serif mt-2">Laporan SPI - Laporan SPI IT</p></div>
                        @break

                @endswitch
            </div>
        @endforeach
    </div>
    <script>
// Skrip untuk merender grafik di halaman cetak
document.addEventListener('DOMContentLoaded', function () {
    // Fungsi untuk grafik dengan format 'Rp'
    document.querySelectorAll('.chart-export-rp').forEach(canvas => {
        const chartData = JSON.parse(canvas.dataset.chart);
        new Chart(canvas.getContext('2d'), {
            type: 'bar', data: chartData,
            options: { responsive: true, maintainAspectRatio: false, animation: false, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value) } } } }
        });
    });

    // Fungsi untuk grafik dengan format 'Paket'
    document.querySelectorAll('.chart-export-paket').forEach(canvas => {
       const chartData = JSON.parse(canvas.dataset.chart);
        new Chart(canvas.getContext('2d'), {
            type: 'bar', data: chartData,
            options: { responsive: true, maintainAspectRatio: false, animation: false, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: value => new Intl.NumberFormat('id-ID').format(value) + ' Paket' } } } }
        });
    });
});
</script>
</body>
</html>
