import './bootstrap.js';
import '../css/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggleSidebar = document.getElementById('toggle-sidebar');
    const logoFull = document.getElementById('logo-full');
    const logoMini = document.getElementById('logo-mini');
    const navbar = document.getElementById('navbar');
    const admincontent = document.getElementById('admincontent'); // Konten utama
    const footer = document.getElementById("main-footer"); // Footer utama

    // Fungsi untuk menyesuaikan layout berdasarkan status sidebar
    const adjustLayout = (isMinimized) => {
        const sidebarWidth = isMinimized ? '4rem' : '16rem'; // Lebar sidebar
        navbar.style.marginLeft = sidebarWidth;
        admincontent.style.marginLeft = sidebarWidth;
        admincontent.style.width = `calc(100% - ${sidebarWidth})`;
        footer.style.marginLeft = sidebarWidth;
    };

    // Toggle Sidebar
    toggleSidebar?.addEventListener('click', () => {
        const isMinimized = sidebar.getAttribute('data-minimized') === 'true';
        sidebar.setAttribute('data-minimized', !isMinimized);

        // Ubah kelas sidebar
        sidebar.classList.toggle('w-64', !isMinimized);
        sidebar.classList.toggle('w-16', isMinimized);

        // Ubah visibilitas logo
        logoFull?.classList.toggle('hidden', isMinimized);
        logoMini?.classList.toggle('hidden', !isMinimized);

        // Sembunyikan/Perlihatkan label menu
        document.querySelectorAll('.menu-label').forEach(label => {
            label.classList.toggle('hidden', isMinimized);
        });

        // Sesuaikan layout
        adjustLayout(isMinimized);
    });

    // Dropdown Toggle
    document.querySelectorAll('[aria-controls]').forEach(button => {
        button.addEventListener('click', () => {
            const dropdownId = button.getAttribute('aria-controls');
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                button.setAttribute('aria-expanded', !dropdown.classList.contains('hidden'));
            }
        });
    });

    // Inisialisasi layout saat halaman dimuat
    const isMinimized = sidebar.getAttribute('data-minimized') === 'true';
    adjustLayout(isMinimized);
});
