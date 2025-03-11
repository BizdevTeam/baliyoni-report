<!-- Floating Button untuk Date -->
<div class="fixed bottom-56 right-6 z-50">
  <button id="floatingDateButton" class="justify-center rounded-full w-20 h-20 bg-red-600 font-medium text-white px-4 py-3 hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
      <rect width="14" height="0" x="5" y="5" fill="currentColor">
        <animate fill="freeze" attributeName="height" begin="0.6s" dur="0.2s" values="0;3" />
      </rect>
      <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
        <path stroke-dasharray="64" stroke-dashoffset="64" d="M12 4h7c0.55 0 1 0.45 1 1v14c0 0.55 -0.45 1 -1 1h-14c-0.55 0 -1 -0.45 -1 -1v-14c0 -0.55 0.45 -1 1 -1Z">
          <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0" />
        </path>
        <path stroke-dasharray="4" stroke-dashoffset="4" d="M7 4v-2M17 4v-2">
          <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.6s" dur="0.2s" values="4;0" />
        </path>
        <path stroke-dasharray="12" stroke-dashoffset="12" d="M7 11h10">
          <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.8s" dur="0.2s" values="12;0" />
        </path>
        <path stroke-dasharray="8" stroke-dashoffset="8" d="M7 15h7">
          <animate fill="freeze" attributeName="stroke-dashoffset" begin="1s" dur="0.2s" values="8;0" />
        </path>
      </g>
    </svg>
  </button>
</div>

<!-- Date Modal -->
<div id="dateModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-lg p-6 w-auto relative flex flex-col">

    <h2 class="text-2xl font-semibold text-red-600 text-center mb-4">Search by Month</h2>
    <!-- Close Button di Pojok Kanan Atas -->
    <button id="closeDateModal" class="absolute top-2 right-2 text-gray-700 hover:text-gray-900 ml-10">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>

    <div class="flex justify-between items-center">
      <form id="monthFilterForm" method="GET" action="#" class="flex items-center gap-2">
        <div class="flex items-center border border-gray-700 rounded-lg p-2">
          <input type="month" id="startMonth" name="start_month" placeholder="Start Month" value="{{ request('start_month') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
        </div>
        <span class="text-gray-700">to</span>
        <div class="flex items-center border border-gray-700 rounded-lg p-2">
          <input type="month" id="endMonth" name="end_month" placeholder="End Month" value="{{ request('end_month') }}" class="flex-1 border-none focus:outline-none text-gray-700 placeholder-gray-400" />
        </div>
        <button type="submit" class="flex-1 bg-gradient-to-r h from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 text-white px-3 py-2.5 rounded-md shadow-md hover:shadow-lg transition duration-300 ease-in-out transform hover:scale-102 flex items-center gap-2 text-sm" aria-label="Search Month Range">
          Search
        </button>
      </form>
    </div>
  </div>
</div>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    const floatingDateButton = document.getElementById('floatingDateButton');
    const dateModal = document.getElementById('dateModal');
    const closeDateModal = document.getElementById('closeDateModal');

    // Tampilkan modal saat tombol floating diklik
    floatingDateButton.addEventListener('click', function () {
      dateModal.classList.remove('hidden');
    });

    // Sembunyikan modal saat tombol close diklik
    closeDateModal.addEventListener('click', function () {
      dateModal.classList.add('hidden');
    });

    // Sembunyikan modal saat klik di luar modal
    document.addEventListener('click', function (event) {
      if (event.target === dateModal) {
        dateModal.classList.add('hidden');
      }
    });
  });
</script>
