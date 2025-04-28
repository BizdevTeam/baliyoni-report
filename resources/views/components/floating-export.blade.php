<form id="exportForm" action="{{ route('export-laporan-all-new') }}" method="get">
    <input type="hidden" name="start_month" id="startMonthInput">
    <input type="hidden" name="end_month" id="endMonthInput">
    <button id="openExportModalBtn" type="button" class="fixed bottom-80 right-6 w-20 h-20 justify-center rounded-full bg-red-600 font-medium text-white px-4 py-3 hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <mask id="lineMdCloudAltPrintFilledLoop0">
                <g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                    <path stroke-dasharray="64" stroke-dashoffset="64" d="M7 19h11c2.21 0 4 -1.79 4 -4c0 -2.21 -1.79 -4 -4 -4h-1v-1c0 -2.76 -2.24 -5 -5 -5c-2.42 0 -4.44 1.72 -4.9 4h-0.1c-2.76 0 -5 2.24 -5 5c0 2.76 2.24 5 5 5Z">
                        <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
                        <set fill="freeze" attributeName="opacity" begin="0.7s" to="0" />
                    </path>
                    <g fill="#fff" stroke="none" opacity="0">
                        <circle cx="12" cy="10" r="6">
                            <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite" values="12;11;12;13;12" />
                        </circle>
                        <rect width="9" height="8" x="8" y="12" />
                        <rect width="15" height="12" x="1" y="8" rx="6">
                            <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite" values="1;0;1;2;1" />
                        </rect>
                        <rect width="13" height="10" x="10" y="10" rx="5">
                            <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite" values="10;9;10;11;10" />
                        </rect>
                        <set fill="freeze" attributeName="opacity" begin="0.7s" to="1" />
                    </g>
                    <g fill="#000" fill-opacity="0" stroke="none">
                        <circle cx="12" cy="10" r="4">
                            <animate attributeName="cx" begin="0.7s" dur="30s" repeatCount="indefinite" values="12;11;12;13;12" />
                        </circle>
                        <rect width="9" height="6" x="8" y="12" />
                        <rect width="11" height="8" x="3" y="10" rx="4">
                            <animate attributeName="x" begin="0.7s" dur="21s" repeatCount="indefinite" values="3;2;3;4;3" />
                        </rect>
                        <rect width="9" height="6" x="12" y="12" rx="3">
                            <animate attributeName="x" begin="0.7s" dur="17s" repeatCount="indefinite" values="12;11;12;13;12" />
                        </rect>
                        <set fill="freeze" attributeName="fill-opacity" begin="0.7s" to="1" />
                        <animate fill="freeze" attributeName="opacity" begin="0.7s" dur="0.5s" values="1;0" />
                    </g>
                    <g stroke="none">
                        <path fill="#fff" d="M6 11h12v0h-12z">
                            <animate fill="freeze" attributeName="d" begin="1.3s" dur="0.22s" values="M6 11h12v0h-12z;M6 11h12v11h-12z" />
                        </path>
                        <path fill="#000" d="M8 13h8v0h-8z">
                            <animate fill="freeze" attributeName="d" begin="1.34s" dur="0.14s" values="M8 13h8v0h-8z;M8 13h8v7h-8z" />
                        </path>
                        <path fill="#fff" fill-opacity="0" d="M9 12h6v1H9zM9 14h6v1H9zM9 16h6v1H9zM9 18h6v1H9z">
                            <animate fill="freeze" attributeName="fill-opacity" begin="1.4s" dur="0.1s" values="0;1" />
                            <animateMotion begin="1.5s" calcMode="linear" dur="1.5s" path="M0 0v2" repeatCount="indefinite" />
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
            <button id="cancelExportBtn" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Batal</button>
            <button id="confirmExportBtn" type="button" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Ekspor PDF</button>
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
</script>
