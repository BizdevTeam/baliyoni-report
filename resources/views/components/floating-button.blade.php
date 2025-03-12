<!-- Floating Button untuk Grid -->
<div class="fixed bottom-32 right-6 z-50">
  <button id="floatingGridButton" class="justify-center rounded-full w-20 h-20 bg-red-600 font-medium text-white px-4 py-3 hover:shadow-xl transition duration-300 ease-in-out transform hover:scale-105 flex items-center gap-2">
    <svg class="justify-center items-center animated transition-colors duration-300" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
      <path class="fill-current text-white" d="M3 21h8v-8H3m2 2h4v4H5m-2-8h8V3H3m2 2h4v4H5m8-6v8h8V3m-2 6h-4V5h4m-1 11h3v2h-3v3h-2v-3h-3v-2h3v-3h2Z" />
    </svg>
  </button>
</div>

<!-- Modal untuk Grid Options -->
<div id="gridModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-lg p-6 w-[300px] relative flex flex-col">
    
    <!-- Header Modal dengan Flex -->
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold text-gray-800">Pilih Tampilan Grid</h2>
      <button id="closeGridModal" class="text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Tombol Dropdown -->
    <div class="relative">
      <button id="dropdownButton" class="w-full text-left px-4 py-2 border rounded-md bg-white text-gray-700 hover:bg-gray-100 transition">
        Pilih Tampilan Grid
      </button>

      <!-- Dropdown Menu -->
      <div id="dropdownMenu" class="absolute left-0 mt-2 w-full bg-white rounded-md shadow-lg hidden border border-gray-100">
        <ul class="space-y-1">
          <li>
            <a href="#" onclick="setGrid(1)" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                <path fill="currentColor" d="M4 4h16v16H4zm2 4v10h12V8z"/>
              </svg>
              <span>Mode Presentasi</span>
            </a>
          </li>
          <li>
            <a href="#" onclick="setGrid(2)" class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                <path fill="currentColor" d="M5 11q-.402 0-.701-.299T4 10V5q0-.402.299-.701T5 4h5q.402 0 .701.299T11 5v5q0 .402-.299.701T10 11zm0 9q-.402 0-.701-.299T4 19v-5q0-.402.299-.701T5 13h5q.402 0 .701.299T11 14v5q0 .402-.299.701T10 20zm9-9q-.402 0-.701-.299T13 10V5q0-.402.299-.701T14 4h5q.402 0 .701.299T20 5v5q0 .402-.299.701T19 11zm0 9q-.402 0-.701-.299T13 19v-5q0-.402.299-.701T14 13h5q.402 0 .701.299T20 14v5q0 .402-.299.701T19 20zM5 10h5V5H5zm9 0h5V5h-5zm0 9h5v-5h-5zm-9 0h5v-5H5zm5-9"/>
              </svg>
              <span>Mode Medium</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const floatingGridButton = document.getElementById('floatingGridButton');
    const gridModal = document.getElementById('gridModal');
    const closeGridModal = document.getElementById('closeGridModal');
    const dropdownButton = document.getElementById('dropdownButton');
    const dropdownMenu = document.getElementById('dropdownMenu');

    // Tampilkan modal saat tombol floating button diklik
    floatingGridButton.addEventListener('click', function () {
        gridModal.classList.remove('hidden');
    });

    // Sembunyikan modal saat tombol close diklik
    closeGridModal.addEventListener('click', function () {
        gridModal.classList.add('hidden');
    });

    // Sembunyikan modal saat klik di luar modal (hanya jika modal terbuka)
    document.addEventListener('click', function (event) {
        if (event.target === gridModal) {
            gridModal.classList.add('hidden');
        }
    });

    // Toggle dropdown menu saat tombol diklik
    dropdownButton.addEventListener('click', function (event) {
        event.stopPropagation(); // Hindari penutupan dropdown saat klik
        dropdownMenu.classList.toggle('hidden');
    });

    // Sembunyikan dropdown jika klik di luar dropdown
    document.addEventListener('click', function (event) {
        if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });

    // Load grid preferences saat halaman dimuat
    loadGridPreferences();
});

function loadGridPreferences() {
    // Load grid preferences dari localStorage
    const savedColumns = localStorage.getItem('gridColumns');
    if (savedColumns) {
        setGrid(parseInt(savedColumns), false);
    }
}

// Fungsi untuk mengubah tampilan grid
function setGrid(columns, refresh = true) {
    // Simpan preferensi grid ke localStorage
    localStorage.setItem('gridColumns', columns);

    // Refresh halaman untuk menerapkan perubahan
    if (refresh) {
        setTimeout(() => {
            location.reload();
        }, 200); // Beri sedikit jeda sebelum refresh
        return;
    }

    const gridContainer = document.getElementById('gridContainer'); // Ganti dengan ID grid yang sesuai
    if (gridContainer) {
        // Hapus semua kelas grid yang mungkin ada
        gridContainer.className = '';
        // Tambahkan kelas grid baru
        gridContainer.classList.add('grid', 'gap-6', `grid-cols-${columns}`);
    }

    // Sembunyikan modal & dropdown setelah memilih opsi
    document.getElementById('gridModal').classList.add('hidden');
    document.getElementById('dropdownMenu').classList.add('hidden');
}

</script>

<style>
  svg {
      transition: transform 0.3s ease-in-out;
  }
  
  svg:hover {
      transform: scale(1.2);
  }

  @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
  }

  .animated {
      animation: pulse 1.5s infinite;
  }
</style>