{{-- <form id="exportForm" action="{{ route('export-laporan-all-new') }}" method="get">
    <input type="hidden" name="start_month" id="startMonthInput">
    <input type="hidden" name="end_month" id="endMonthInput">
    <button id="openExportModalBtn" type="button"
        class="fixed bottom-80 right-6 w-20 h-20 justify-center rounded-full bg-red-600 font-medium text-white px-4 py-3 hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <mask id="lineMdCloudAltPrintFilledLoop0">
                <g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                    <path stroke-dasharray="64" stroke-dashoffset="64"
                        d="M7 19h11c2.21 0 4 -1.79 4 -4c0 -2.21 -1.79 -4 -4 -4h-1v-1c0 -2.76 -2.24 -5 -5 -5c-2.42 0 -4.44 1.72 -4.9 4h-0.1c-2.76 0 -5 2.24 -5 5c0 2.76 2.24 5 5 5Z">
                        <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
                        <set fill="freeze" attributeName="opacity" begin="0.7s" to="0" />
                    </path>
                    <g fill="#fff" stroke="none" opacity="0">
                        <circle cx="12" cy="10" r="6">
                            <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </circle>
                        <rect width="9" height="8" x="8" y="12" />
                        <rect width="15" height="12" x="1" y="8" rx="6">
                            <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite"
                                values="1;0;1;2;1" />
                        </rect>
                        <rect width="13" height="10" x="10" y="10" rx="5">
                            <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite"
                                values="10;9;10;11;10" />
                        </rect>
                        <set fill="freeze" attributeName="opacity" begin="0.7s" to="1" />
                    </g>
                    <g fill="#000" fill-opacity="0" stroke="none">
                        <circle cx="12" cy="10" r="4">
                            <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </circle>
                        <rect width="9" height="6" x="8" y="12" />
                        <rect width="11" height="8" x="3" y="10" rx="4">
                            <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite"
                                values="3;2;3;4;3" />
                        </rect>
                        <rect width="9" height="6" x="12" y="12" rx="3">
                            <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </rect>
                        <set fill="freeze" attributeName="fill-opacity" begin="0.7s" to="1" />
                        <animate fill="freeze" attributeName="opacity" begin="0.7s" dur="0.5s" values="1;0" />
                    </g>
                    <g stroke="none">
                        <path fill="#fff" d="M6 11h12v0h-12z">
                            <animate fill="freeze" attributeName="d" begin="1.3s" dur="0.22s"
                                values="M6 11h12v0h-12z;M6 11h12v11h-12z" />
                        </path>
                        <path fill="#000" d="M8 13h8v0h-8z">
                            <animate fill="freeze" attributeName="d" begin="1.34s" dur="0.14s"
                                values="M8 13h8v0h-8z;M8 13h8v7h-8z" />
                        </path>
                        <path fill="#fff" fill-opacity="0" d="M9 12h6v1H9zM9 14h6v1H9zM9 16h6v1H9zM9 18h6v1H9z">
                            <animate fill="freeze" attributeName="fill-opacity" begin="1.4s" dur="0.1s" values="0;1" />
                            <animateMotion begin="1.5s" calcMode="linear" dur="1.5s" path="M0 0v2"
                                repeatCount="indefinite" />
                        </path>
                    </g>
                </g>
            </mask>
            <rect width="30" height="30" fill="currentColor" mask="url(#lineMdCloudAltPrintFilledLoop0)" />
        </svg>

    </button>
</form>

<!-- Modal -->
<div id="exportModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg p-6 shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Export PDF</h2>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Start Month (Opsional)</label>
            <input type="month" id="startMonthModal" class="w-full border-gray-300 rounded p-2" />
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">End Month (Opsional)</label>
            <input type="month" id="endMonthModal" class="w-full border-gray-300 rounded p-2" />
        </div>
        <div class="flex justify-end space-x-2">
            <button id="cancelExportBtn" type="button"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Batal</button>
            <button id="confirmExportBtn" type="button"
                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Ekspor PDF</button>
        </div>
    </div>
</div>

<script>
    const exportModal = document.getElementById('exportModal');
    const openExportModalBtn = document.getElementById('openExportModalBtn');
    const cancelExportBtn = document.getElementById('cancelExportBtn');
    const confirmExportBtn = document.getElementById('confirmExportBtn');

    openExportModalBtn.addEventListener('click', () => {
        exportModal.classList.remove('hidden');
    });

    cancelExportBtn.addEventListener('click', () => {
        exportModal.classList.add('hidden');
    });

    confirmExportBtn.addEventListener('click', () => {
        const startMonth = document.getElementById('startMonthModal').value;
        const endMonth = document.getElementById('endMonthModal').value;

        // Isi input hidden di form
        document.getElementById('startMonthInput').value = startMonth;
        document.getElementById('endMonthInput').value = endMonth;

        // Submit form
        document.getElementById('exportForm').submit();
    });
</script> --}}
{{--
<form id="exportForm" action="{{ route('export-laporan-all-new') }}" method="get">
    <input type="hidden" name="start_month" id="startMonthInput">
    <input type="hidden" name="end_month" id="endMonthInput">

    <button id="openExportModalBtn" type="button"
        class="fixed bottom-80 right-6 w-20 h-20 justify-center rounded-full bg-red-600 font-medium text-white px-4 py-3 hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24">
            <mask id="lineMdCloudAltPrintFilledLoop0">
                <g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                    <path stroke-dasharray="64" stroke-dashoffset="64"
                        d="M7 19h11c2.21 0 4 -1.79 4 -4c0 -2.21 -1.79 -4 -4 -4h-1v-1c0 -2.76 -2.24 -5 -5 -5c-2.42 0 -4.44 1.72 -4.9 4h-0.1c-2.76 0 -5 2.24 -5 5c0 2.76 2.24 5 5 5Z">
                        <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
                        <set fill="freeze" attributeName="opacity" begin="0.7s" to="0" />
                    </path>
                    <g fill="#fff" stroke="none" opacity="0">
                        <circle cx="12" cy="10" r="6">
                            <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </circle>
                        <rect width="9" height="8" x="8" y="12" />
                        <rect width="15" height="12" x="1" y="8" rx="6">
                            <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite"
                                values="1;0;1;2;1" />
                        </rect>
                        <rect width="13" height="10" x="10" y="10" rx="5">
                            <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite"
                                values="10;9;10;11;10" />
                        </rect>
                        <set fill="freeze" attributeName="opacity" begin="0.7s" to="1" />
                    </g>
                    <g fill="#000" fill-opacity="0" stroke="none">
                        <circle cx="12" cy="10" r="4">
                            <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </circle>
                        <rect width="9" height="6" x="8" y="12" />
                        <rect width="11" height="8" x="3" y="10" rx="4">
                            <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite"
                                values="3;2;3;4;3" />
                        </rect>
                        <rect width="9" height="6" x="12" y="12" rx="3">
                            <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </rect>
                        <set fill="freeze" attributeName="fill-opacity" begin="0.7s" to="1" />
                        <animate fill="freeze" attributeName="opacity" begin="0.7s" dur="0.5s" values="1;0" />
                    </g>
                    <g stroke="none">
                        <path fill="#fff" d="M6 11h12v0h-12z">
                            <animate fill="freeze" attributeName="d" begin="1.3s" dur="0.22s"
                                values="M6 11h12v0h-12z;M6 11h12v11h-12z" />
                        </path>
                        <path fill="#000" d="M8 13h8v0h-8z">
                            <animate fill="freeze" attributeName="d" begin="1.34s" dur="0.14s"
                                values="M8 13h8v0h-8z;M8 13h8v7h-8z" />
                        </path>
                        <path fill="#fff" fill-opacity="0" d="M9 12h6v1H9zM9 14h6v1H9zM9 16h6v1H9zM9 18h6v1H9z">
                            <animate fill="freeze" attributeName="fill-opacity" begin="1.4s" dur="0.1s" values="0;1" />
                            <animateMotion begin="1.5s" calcMode="linear" dur="1.5s" path="M0 0v2"
                                repeatCount="indefinite" />
                        </path>
                    </g>
                </g>
            </mask>
            <rect width="24" height="24" fill="currentColor" mask="url(#lineMdCloudAltPrintFilledLoop0)" />
        </svg>
    </button>

    <div id="exportModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-lg p-6 shadow-lg w-auto max-w-2xl">
            <h2 class="text-xl font-bold mb-4">Export Laporan ke PDF</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Start Month (Opsional)</label>
                    <input type="month" id="startMonthModal" class="w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">End Month (Opsional)</label>
                    <input type="month" id="endMonthModal" class="w-full border-gray-300 rounded p-2" />
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Pilih Laporan untuk Diekspor</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 border p-3 rounded-md max-h-60 overflow-y-auto">
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="penjualan" class="rounded"> <span>Rekap Penjualan</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="penjualan_perusahaan" class="rounded"> <span>Penjualan Perusahaan</span></label>
                    </div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="paket_admin" class="rounded"> <span>Paket Administrasi</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="status_paket" class="rounded"> <span>Status Paket</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="per_instansi" class="rounded"> <span>Laporan Per Instansi</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="holding" class="rounded"> <span>Laporan Holding</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]" value="stok"
                                class="rounded"> <span>Laporan Stok</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="pembelian_outlet" class="rounded"> <span>Pembelian Outlet</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="negosiasi" class="rounded"> <span>Negosiasi</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="pendapatan_asp" class="rounded"> <span>Pendapatan ASP</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="piutang_asp" class="rounded"> <span>Piutang ASP</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="pengiriman" class="rounded"> <span>Laporan Pengiriman</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="ptbos" class="rounded"> <span>PT BOS</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="ijasa" class="rounded"> <span>IJASA</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="ijasagambar" class="rounded"> <span>IJASA Gambar</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="sakit" class="rounded"> <span>Laporan Sakit</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]" value="cuti"
                                class="rounded"> <span>Laporan Cuti</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]" value="izin"
                                class="rounded"> <span>Laporan Izin</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="terlambat" class="rounded"> <span>Laporan Terlambat</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]" value="khps"
                                class="rounded"> <span>Kas-Hutang-Piutang</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="arus_kas" class="rounded"> <span>Arus Kas</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]" value="spi"
                                class="rounded"> <span>Laporan SPI</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="spiit" class="rounded"> <span>Laporan SPI IT</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="tiktok" class="rounded"> <span>Tiktok</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="instagram" class="rounded"> <span>Instagram</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="bizdev" class="rounded"> <span>Bizdev Gambar</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="bizdev1" class="rounded"> <span>Bizdev Kendala</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="laba_rugi" class="rounded"> <span>Laba Rugi</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="neraca" class="rounded"> <span>Neraca</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="rasio" class="rounded"> <span>Rasio</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]" value="ppn"
                                class="rounded"> <span>PPn</span></label></div>
                    <div><label class="flex items-center space-x-2"><input type="checkbox" name="reports[]"
                                value="taxplanning" class="rounded"> <span>Tax Planning</span></label></div>
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <button id="cancelExportBtn" type="button"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Batal</button>
                <button id="confirmExportBtn" type="submit"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Ekspor PDF</button>
            </div>
        </div>
    </div>
</form>

<script>
    const exportModal = document.getElementById('exportModal');
    const openExportModalBtn = document.getElementById('openExportModalBtn');
    const cancelExportBtn = document.getElementById('cancelExportBtn');
    const confirmExportBtn = document.getElementById('confirmExportBtn');
    const exportForm = document.getElementById('exportForm');

    openExportModalBtn.addEventListener('click', () => {
        exportModal.classList.remove('hidden');
    });

    cancelExportBtn.addEventListener('click', () => {
        exportModal.classList.add('hidden');
    });

    // Menggunakan event 'submit' pada form untuk memvalidasi dan mengirim data
    exportForm.addEventListener('submit', (event) => {
        // Pindahkan nilai dari modal ke input form yang tersembunyi
        const startMonth = document.getElementById('startMonthModal').value;
        const endMonth = document.getElementById('endMonthModal').value;
        document.getElementById('startMonthInput').value = startMonth;
        document.getElementById('endMonthInput').value = endMonth;

        // Cek apakah ada checkbox yang dipilih
        const checkedReports = document.querySelectorAll('input[name="reports[]"]:checked').length;
        if (checkedReports === 0) {
            alert('Silakan pilih minimal satu laporan untuk diekspor.');
            event.preventDefault(); // Mencegah form untuk submit
            return;
        }

        exportModal.classList.add('hidden');
        // Form akan disubmit secara otomatis setelah event listener ini selesai
    });
</script> --}}

{{-- <form id="exportForm" action="{{ route('export-laporan-all-new') }}" method="get" target="_blank">
    <input type="hidden" name="start_month" id="startMonthInput">
    <input type="hidden" name="end_month" id="endMonthInput">

    <button id="openExportModalBtn" type="button"
        class="fixed bottom-80 right-6 w-20 h-20 justify-center rounded-full bg-red-600 font-medium text-white px-4 py-3 hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24">
            <mask id="lineMdCloudAltPrintFilledLoop0">
                <g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                    <path stroke-dasharray="64" stroke-dashoffset="64"
                        d="M7 19h11c2.21 0 4 -1.79 4 -4c0 -2.21 -1.79 -4 -4 -4h-1v-1c0 -2.76 -2.24 -5 -5 -5c-2.42 0 -4.44 1.72 -4.9 4h-0.1c-2.76 0 -5 2.24 -5 5c0 2.76 2.24 5 5 5Z">
                        <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
                        <set fill="freeze" attributeName="opacity" begin="0.7s" to="0" />
                    </path>
                    <g fill="#fff" stroke="none" opacity="0">
                        <circle cx="12" cy="10" r="6">
                            <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </circle>
                        <rect width="9" height="8" x="8" y="12" />
                        <rect width="15" height="12" x="1" y="8" rx="6">
                            <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite"
                                values="1;0;1;2;1" />
                        </rect>
                        <rect width="13" height="10" x="10" y="10" rx="5">
                            <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite"
                                values="10;9;10;11;10" />
                        </rect>
                        <set fill="freeze" attributeName="opacity" begin="0.7s" to="1" />
                    </g>
                    <g fill="#000" fill-opacity="0" stroke="none">
                        <circle cx="12" cy="10" r="4">
                            <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </circle>
                        <rect width="9" height="6" x="8" y="12" />
                        <rect width="11" height="8" x="3" y="10" rx="4">
                            <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite"
                                values="3;2;3;4;3" />
                        </rect>
                        <rect width="9" height="6" x="12" y="12" rx="3">
                            <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </rect>
                        <set fill="freeze" attributeName="fill-opacity" begin="0.7s" to="1" />
                        <animate fill="freeze" attributeName="opacity" begin="0.7s" dur="0.5s" values="1;0" />
                    </g>
                    <g stroke="none">
                        <path fill="#fff" d="M6 11h12v0h-12z">
                            <animate fill="freeze" attributeName="d" begin="1.3s" dur="0.22s"
                                values="M6 11h12v0h-12z;M6 11h12v11h-12z" />
                        </path>
                        <path fill="#000" d="M8 13h8v0h-8z">
                            <animate fill="freeze" attributeName="d" begin="1.34s" dur="0.14s"
                                values="M8 13h8v0h-8z;M8 13h8v7h-8z" />
                        </path>
                        <path fill="#fff" fill-opacity="0" d="M9 12h6v1H9zM9 14h6v1H9zM9 16h6v1H9zM9 18h6v1H9z">
                            <animate fill="freeze" attributeName="fill-opacity" begin="1.4s" dur="0.1s" values="0;1" />
                            <animateMotion begin="1.5s" calcMode="linear" dur="1.5s" path="M0 0v2"
                                repeatCount="indefinite" />
                        </path>
                    </g>
                </g>
            </mask>
            <rect width="24" height="24" fill="currentColor" mask="url(#lineMdCloudAltPrintFilledLoop0)" />
        </svg>
    </button>

    <div id="exportModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-lg p-6 shadow-lg w-auto max-w-2xl">
            <h2 class="text-xl font-bold mb-4">Export Laporan ke PDF</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Start Month (Opsional)</label>
                    <input type="month" id="startMonthModal" class="w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">End Month (Opsional)</label>
                    <input type="month" id="endMonthModal" class="w-full border-gray-300 rounded p-2" />
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Pilih Laporan untuk Diekspor</label>
                <div class="mb-2 border-b pb-2">
                    <label class="flex items-center space-x-2 font-semibold">
                        <input type="checkbox" id="selectAllReports" class="rounded">
                        <span>Pilih Semua Laporan</span>
                    </label>
                </div>
                <div id="reportsContainer"
                    class="grid grid-cols-2 md:grid-cols-3 gap-2 border p-3 rounded-md max-h-60 overflow-y-auto">
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <button id="cancelExportBtn" type="button"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Ekspor
                    PDF</button>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const exportModal = document.getElementById('exportModal');
    const openExportModalBtn = document.getElementById('openExportModalBtn');
    const cancelExportBtn = document.getElementById('cancelExportBtn');
    const exportForm = document.getElementById('exportForm');
    const selectAllCheckbox = document.getElementById('selectAllReports');
    const reportsContainer = document.getElementById('reportsContainer');

    const reports = [
        { value: 'penjualan', label: 'Rekap Penjualan' }, { value: 'penjualan_perusahaan', label: 'Penjualan Perusahaan' },
        { value: 'paket_admin', label: 'Paket Administrasi' }, { value: 'status_paket', label: 'Status Paket' },
        { value: 'per_instansi', label: 'Laporan Per Instansi' }, { value: 'holding', label: 'Laporan Holding' },
        { value: 'stok', label: 'Laporan Stok' }, { value: 'pembelian_outlet', label: 'Pembelian Outlet' },
        { value: 'negosiasi', label: 'Negosiasi' }, { value: 'pendapatan_asp', label: 'Pendapatan ASP' },
        { value: 'piutang_asp', label: 'Piutang ASP' }, { value: 'pengiriman', label: 'Laporan Pengiriman' },
        { value: 'ptbos', label: 'PT BOS' }, { value: 'ijasa', label: 'IJASA' },
        { value: 'ijasagambar', label: 'IJASA Gambar' }, { value: 'sakit', label: 'Laporan Sakit' },
        { value: 'cuti', label: 'Laporan Cuti' }, { value: 'izin', label: 'Laporan Izin' },
        { value: 'terlambat', label: 'Laporan Terlambat' }, { value: 'khps', label: 'Kas-Hutang-Piutang' },
        { value: 'arus_kas', label: 'Arus Kas' }, { value: 'spi', label: 'Laporan SPI' },
        { value: 'spiit', label: 'Laporan SPI IT' }, { value: 'tiktok', label: 'Tiktok' },
        { value: 'instagram', label: 'Instagram' }, { value: 'bizdev', label: 'Bizdev Gambar' },
        { value: 'bizdev1', label: 'Bizdev Kendala' }, { value: 'laba_rugi', label: 'Laba Rugi' },
        { value: 'neraca', label: 'Neraca' }, { value: 'rasio', label: 'Rasio' },
        { value: 'ppn', label: 'PPn' }, { value: 'taxplanning', label: 'Tax Planning' }
    ];

    // Buat checkbox secara dinamis
    reports.forEach(report => {
        const label = document.createElement('label');
        label.className = 'flex items-center space-x-2';
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'reports[]';
        checkbox.value = report.value;
        checkbox.className = 'rounded report-checkbox';
        const span = document.createElement('span');
        span.textContent = report.label;
        label.appendChild(checkbox);
        label.appendChild(span);
        reportsContainer.appendChild(label);
    });

    const reportCheckboxes = document.querySelectorAll('.report-checkbox');

    openExportModalBtn.addEventListener('click', () => exportModal.classList.remove('hidden'));
    cancelExportBtn.addEventListener('click', () => exportModal.classList.add('hidden'));

    // Logika untuk "Pilih Semua"
    selectAllCheckbox.addEventListener('change', () => {
        reportCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });

    // Logika untuk individual checkbox
    reportCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const allChecked = Array.from(reportCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        });
    });

    exportForm.addEventListener('submit', (event) => {
        document.getElementById('startMonthInput').value = document.getElementById('startMonthModal').value;
        document.getElementById('endMonthInput').value = document.getElementById('endMonthModal').value;

        const checkedReportsCount = document.querySelectorAll('input[name="reports[]"]:checked').length;
        if (checkedReportsCount === 0) {
            alert('Silakan pilih minimal satu laporan untuk diekspor.');
            event.preventDefault(); // Batalkan submit
            return;
        }

        exportModal.classList.add('hidden');
    });
});
</script> --}}
<form id="exportForm" action="{{ route('export-laporan-all-new') }}" method="get" target="_blank">
    <input type="hidden" name="start_month" id="startMonthInput">
    <input type="hidden" name="end_month" id="endMonthInput">

    <button id="openExportModalBtn" type="button"
        class="fixed bottom-80 right-6 w-20 h-20 justify-center rounded-full bg-red-600 font-medium text-white px-4 py-3 hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24">
            <mask id="lineMdCloudAltPrintFilledLoop0">
                <g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                    <path stroke-dasharray="64" stroke-dashoffset="64"
                        d="M7 19h11c2.21 0 4 -1.79 4 -4c0 -2.21 -1.79 -4 -4 -4h-1v-1c0 -2.76 -2.24 -5 -5 -5c-2.42 0 -4.44 1.72 -4.9 4h-0.1c-2.76 0 -5 2.24 -5 5c0 2.76 2.24 5 5 5Z">
                        <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
                        <set fill="freeze" attributeName="opacity" begin="0.7s" to="0" />
                    </path>
                    <g fill="#fff" stroke="none" opacity="0">
                        <circle cx="12" cy="10" r="6">
                            <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </circle>
                        <rect width="9" height="8" x="8" y="12" />
                        <rect width="15" height="12" x="1" y="8" rx="6">
                            <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite"
                                values="1;0;1;2;1" />
                        </rect>
                        <rect width="13" height="10" x="10" y="10" rx="5">
                            <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite"
                                values="10;9;10;11;10" />
                        </rect>
                        <set fill="freeze" attributeName="opacity" begin="0.7s" to="1" />
                    </g>
                    <g fill="#000" fill-opacity="0" stroke="none">
                        <circle cx="12" cy="10" r="4">
                            <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </circle>
                        <rect width="9" height="6" x="8" y="12" />
                        <rect width="11" height="8" x="3" y="10" rx="4">
                            <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite"
                                values="3;2;3;4;3" />
                        </rect>
                        <rect width="9" height="6" x="12" y="12" rx="3">
                            <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite"
                                values="12;11;12;13;12" />
                        </rect>
                        <set fill="freeze" attributeName="fill-opacity" begin="0.7s" to="1" />
                        <animate fill="freeze" attributeName="opacity" begin="0.7s" dur="0.5s" values="1;0" />
                    </g>
                    <g stroke="none">
                        <path fill="#fff" d="M6 11h12v0h-12z">
                            <animate fill="freeze" attributeName="d" begin="1.3s" dur="0.22s"
                                values="M6 11h12v0h-12z;M6 11h12v11h-12z" />
                        </path>
                        <path fill="#000" d="M8 13h8v0h-8z">
                            <animate fill="freeze" attributeName="d" begin="1.34s" dur="0.14s"
                                values="M8 13h8v0h-8z;M8 13h8v7h-8z" />
                        </path>
                        <path fill="#fff" fill-opacity="0" d="M9 12h6v1H9zM9 14h6v1H9zM9 16h6v1H9zM9 18h6v1H9z">
                            <animate fill="freeze" attributeName="fill-opacity" begin="1.4s" dur="0.1s" values="0;1" />
                            <animateMotion begin="1.5s" calcMode="linear" dur="1.5s" path="M0 0v2"
                                repeatCount="indefinite" />
                        </path>
                    </g>
                </g>
            </mask>
            <rect width="24" height="24" fill="currentColor" mask="url(#lineMdCloudAltPrintFilledLoop0)" />
        </svg>
    </button>

    <div id="exportModal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-lg p-6 shadow-lg w-auto max-w-2xl">
            <h2 class="text-xl font-bold mb-4">Export Laporan ke PDF</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Start Month (Opsional)</label>
                    <input type="month" id="startMonthModal" class="w-full border-gray-300 rounded p-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">End Month (Opsional)</label>
                    <input type="month" id="endMonthModal" class="w-full border-gray-300 rounded p-2" />
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Pilih Laporan untuk Diekspor</label>
                <div class="mb-2 border-b pb-2">
                    <label class="flex items-center space-x-2 font-semibold">
                        <input type="checkbox" id="selectAllReports" class="rounded">
                        <span>Pilih Semua Laporan</span>
                    </label>
                </div>
                <div id="reportsContainer"
                    class="grid grid-cols-2 md:grid-cols-3 gap-2 border p-3 rounded-md max-h-60 overflow-y-auto">
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <button id="cancelExportBtn" type="button"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Ekspor
                    PDF</button>
            </div>
        </div>
    </div>
</form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Sembunyikan konten utama pada halaman awal, hanya untuk dicetak
    const exportContent = document.getElementById('exportContent');
    const urlParams = new URLSearchParams(window.location.search);
    // Hanya tampilkan konten jika ada parameter 'reports[]', yang menandakan ini adalah halaman pratinjau cetak
    if (exportContent && !urlParams.has('reports[]')) {
        exportContent.style.display = 'none';
    }

    const exportModal = document.getElementById('exportModal');
    const openExportModalBtn = document.getElementById('openExportModalBtn');
    const cancelExportBtn = document.getElementById('cancelExportBtn');
    const exportForm = document.getElementById('exportForm');
    const selectAllCheckbox = document.getElementById('selectAllReports');
    const reportsContainer = document.getElementById('reportsContainer');

    const reports = [
        { value: 'penjualan', label: 'Rekap Penjualan' }, 
        { value: 'penjualan_perusahaan', label: 'Penjualan Perusahaan' },
        { value: 'paket_admin', label: 'Paket Administrasi' }, 
        { value: 'status_paket', label: 'Status Paket' },
        { value: 'per_instansi', label: 'Laporan Per Instansi' }, 
        { value: 'holding', label: 'Laporan Holding' },
        { value: 'stok', label: 'Laporan Stok' }, 
        { value: 'pembelian_outlet', label: 'Pembelian Outlet' },
        { value: 'negosiasi', label: 'Negosiasi' },
        { value: 'pendapatan_asp', label: 'Pendapatan ASP' },
        { value: 'piutang_asp', label: 'Piutang ASP' }, 
        { value: 'pengiriman', label: 'Laporan Pengiriman' },
        { value: 'laba_rugi', label: 'Laba Rugi' },
        { value: 'neraca', label: 'Neraca' }, 
        { value: 'rasio', label: 'Rasio' },
        { value: 'khps', label: 'Kas-Hutang-Piutang' },
        { value: 'arus_kas', label: 'Arus Kas' }, 
        { value: 'ppn', label: 'PPn' }, 
        { value: 'taxplanning', label: 'Tax Planning' },
        { value: 'instagram', label: 'Instagram' },
        { value: 'tiktok', label: 'Tiktok' },
        { value: 'bizdev', label: 'Bizdev Gambar' },
        { value: 'bizdev1', label: 'Bizdev Kendala' }, 
        { value: 'ptbos', label: 'PT BOS' },
        { value: 'ijasa', label: 'IJASA' },
        { value: 'ijasagambar', label: 'IJASA Gambar' }, 
        { value: 'sakit', label: 'Laporan Sakit' },
        { value: 'cuti', label: 'Laporan Cuti' }, 
        { value: 'izin', label: 'Laporan Izin' },
        { value: 'terlambat', label: 'Laporan Terlambat' }, 
        { value: 'spi', label: 'Laporan SPI' },
        { value: 'spiit', label: 'Laporan SPI IT' },         
    ];

    // Buat checkbox secara dinamis
    reports.forEach(report => {
        const label = document.createElement('label');
        label.className = 'flex items-center space-x-2';
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'reports[]';
        checkbox.value = report.value;
        checkbox.className = 'rounded report-checkbox';
        const span = document.createElement('span');
        span.textContent = report.label;
        label.appendChild(checkbox);
        label.appendChild(span);
        reportsContainer.appendChild(label);
    });

    const reportCheckboxes = document.querySelectorAll('.report-checkbox');

    openExportModalBtn.addEventListener('click', () => exportModal.classList.remove('hidden'));
    cancelExportBtn.addEventListener('click', () => exportModal.classList.add('hidden'));

    // Logika untuk "Pilih Semua"
    selectAllCheckbox.addEventListener('change', () => {
        reportCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });

    // Logika untuk individual checkbox
    reportCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const allChecked = Array.from(reportCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        });
    });

    exportForm.addEventListener('submit', (event) => {
        document.getElementById('startMonthInput').value = document.getElementById('startMonthModal').value;
        document.getElementById('endMonthInput').value = document.getElementById('endMonthModal').value;

        const checkedReportsCount = document.querySelectorAll('input[name="reports[]"]:checked').length;
        if (checkedReportsCount === 0) {
            alert('Silakan pilih minimal satu laporan untuk diekspor.');
            event.preventDefault(); // Batalkan submit
            return;
        }

        exportModal.classList.add('hidden');
    });
});
</script>


