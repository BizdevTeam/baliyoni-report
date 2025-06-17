<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Tax Planning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @vite('resources/css/tailwind.css')
    @vite('resources/css/custom.css')
    @vite('resources/js/app.js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 hold-transition sidebar-mini layout-fixed">
  <div class="container mx-auto p-6">
  <h1 class="text-3xl font-bold mb-6">Tax Planning Overview</h1>

  {{-- Filter Form --}}
  <form method="GET" action="{{ route('taxplanning.index') }}" class="flex flex-wrap gap-4 mb-8">
    <input
      type="text"
      name="search"
      value="{{ request('search') }}"
      placeholder="Search perusahaan…"
      class="border rounded px-3 py-2 flex-1"
    />
    <select name="per_page" class="border rounded px-3 py-2">
      @foreach([12,25,50,100] as $n)
        <option value="{{ $n }}" {{ request('per_page',12)==$n ? 'selected' : '' }}>
          {{ $n }} / page
        </option>
      @endforeach
    </select>
    <button
      type="submit"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
    >
      Apply
    </button>
  </form>
  
{{-- 
  <div class="mb-10">
    <canvas id="taxChart" class="max-h-96 w-full"></canvas>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
        document.addEventListener("DOMContentLoaded", function () {
        const chartSelect = document.getElementById("chartSelect");
        const chartpContainer = document.getElementById("chartpContainer");
        const chartpptotalContainer = document.getElementById("chartpptotalContainer");

        chartSelect.addEventListener("change", function () {
            const selectedChart = this.value;

            // Tampilkan chart yang dipilih dan sembunyikan yang lainnya
            chartpContainer.classList.toggle("hidden", selectedChart !== "chartpp");
            chartpptotalContainer.classList.toggle("hidden", selectedChart !== "chartpptotal");
        });
    });

    const chartData = @json($chartData);
    const ctx = document.getElementById('taxChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: chartData,
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script> --}}
  {{-- Chart --}}
<div class="mb-10">
    <h3 class="text-xl font-semibold mb-4">Grafik Total Tax Planning dan Total Penjualan per Perusahaan</h3>
    <canvas id="taxChart" class="max-h-96 w-full"></canvas>
</div>

{{-- Kontrol Paginasi Chart --}}
<div class="flex justify-center mt-4">
    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        {{-- Tombol Sebelumnya --}}
        @if ($chartPage > 1)
            <a href="{{ request()->fullUrlWithQuery(['chart_page' => $chartPage - 1, 'chart_per_page' => $chartPerPage]) }}"
               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Sebelumnya</span>
                <!-- Heroicon name: solid/chevron-left -->
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        @endif

        {{-- Tautan Halaman --}}
        @for ($i = 1; $i <= $totalPages; $i++)
            <a href="{{ request()->fullUrlWithQuery(['chart_page' => $i, 'chart_per_page' => $chartPerPage]) }}"
               class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium
                      @if ($i == $chartPage) bg-indigo-50 border-indigo-500 text-indigo-600 @else text-gray-700 hover:bg-gray-50 @endif">
                {{ $i }}
            </a>
        @endfor

        {{-- Tombol Selanjutnya --}}
        @if ($chartPage < $totalPages)
            <a href="{{ request()->fullUrlWithQuery(['chart_page' => $chartPage + 1, 'chart_per_page' => $chartPerPage]) }}"
               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Selanjutnya</span>
                <!-- Heroicon name: solid/chevron-right -->
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        @endif
    </nav>
</div>

{{-- Dropdown untuk mengubah jumlah perusahaan per halaman --}}
<div class="flex justify-center mt-4">
    <label for="chart_per_page_select" class="block text-sm font-medium text-gray-700 mr-2">Perusahaan per halaman:</label>
    <select id="chart_per_page_select" name="chart_per_page"
            onchange="window.location.href = '{{ request()->fullUrlWithQuery(['chart_page' => 1]) }}' + '&chart_per_page=' + this.value"
            class="mt-1 block w-20 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
        @foreach ([5, 10, 15, 20] as $option)
            <option value="{{ $option }}" @if ($chartPerPage == $option) selected @endif>{{ $option }}</option>
        @endforeach
    </select>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chartData = @json($chartData);
        const ctx = document.getElementById('taxChart').getContext('2d'); // Pastikan ID ini cocok dengan <canvas>

        new Chart(ctx, {
            type: 'bar', // Tipe chart batang cocok untuk perbandingan antar perusahaan
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Grafik Total Tax Planning dan Total Penjualan per Perusahaan' // Sesuaikan judul
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Nama Perusahaan' // Label sumbu X sekarang adalah nama perusahaan
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nilai'
                        }
                    }
                }
            }
        });
    });
</script>

  <div class="container mx-auto px-4 py-6">
  <!-- Title -->
  <h1 class="text-2xl font-bold mb-6">Tax Planning Report</h1>

  <!-- Filter form -->
  <form method="GET" action="{{ route('taxplanning.index') }}" class="flex flex-wrap gap-4 mb-8">
    <div>
      <label class="block text-sm font-medium text-gray-700">Per Page</label>
      <select
        name="per_page"
        onchange="this.form.submit()"
        class="mt-1 block w-full rounded border border-gray-300 bg-white px-3 py-2">
        @foreach([5,10,12,25,50,100] as $size)
          <option value="{{ $size }}" @if($perPage == $size) selected @endif>
            {{ $size }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="flex-1 min-w-[200px]">
      <label class="block text-sm font-medium text-gray-700">Search</label>
      <input
        type="text"
        name="search"
        value="{{ $search }}"
        placeholder="Nama Perusahaan…"
        class="mt-1 block w-full rounded border border-gray-300 px-3 py-2"
      />
    </div>

    <div class="flex items-end">
      <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
        Filter
      </button>
    </div>
  </form>

  <!-- Data Table -->
  <div class="overflow-x-auto">
    <table class="min-w-full table-auto bg-white">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-semibold">#</th>
          <th class="px-4 py-2 text-left text-sm font-semibold">Perusahaan</th>
          <th class="px-4 py-2 text-left text-sm font-semibold">Tanggal</th>
          <th class="px-4 py-2 text-right text-sm font-semibold">Tax Planning</th>
          <th class="px-4 py-2 text-right text-sm font-semibold">Total Penjualan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rekappenjualans as $item)
          <tr class="@if($loop->even) bg-gray-50 @endif">
            <td class="border px-4 py-2">{{ $loop->iteration + ($rekappenjualans->currentPage()-1)*$rekappenjualans->perPage() }}</td>
            <td class="border px-4 py-2">{{ $item->nama_perusahaan }}</td>
            <td class="border px-4 py-2">
              {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
            </td>
            <td class="border px-4 py-2 text-right">
              {{ number_format($item->tax_planning, 0, ',', '.') }}
            </td>
            <td class="border px-4 py-2 text-right">
              {{ number_format($item->total_penjualan, 0, ',', '.') }}
            </td> 
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="mt-6">
    {{ $rekappenjualans->withQueryString()->links() }}
  </div>
</div>

@push('scripts')
  <!-- Chart.js from CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const ctx = document.getElementById('taxChart');
      new Chart(ctx, {
        type: 'bar',
        data: @json($chartData),
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: (v) => v.toLocaleString()
              }
            }
          },
          plugins: {
            legend: {
              position: 'top'
            },
            tooltip: {
              callbacks: {
                label: (ctx) => ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString()
              }
            }
          }
        }
      });
    });
  </script>
</html>
