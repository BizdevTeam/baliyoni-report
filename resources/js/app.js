import AOS from 'aos';
import 'aos/dist/aos.css';  // Mengimpor file CSS AOS

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

        // Adjust sidebar classes
        sidebar.classList.toggle('w-64', !isMinimized);
        sidebar.classList.toggle('w-16', isMinimized);

        // Ubah visibilitas logo
        logoFull?.classList.toggle('hidden', isMinimized);
        logoMini?.classList.toggle('hidden', !isMinimized);

        // Sembunyikan/Perlihatkan label menu
        document.querySelectorAll('.menu-label').forEach(label => {
            label.classList.toggle('hidden', isMinimized);
        });
        document.querySelectorAll('.fa-chevron-down').forEach(icon => {
            icon.classList.toggle('hidden', isMinimized);
        });

        // Tutup semua dropdown jika sidebar diminimalkan
        if (isMinimized) {
            document.querySelectorAll('[aria-controls]').forEach(button => {
                const dropdownId = button.getAttribute('aria-controls');
                const dropdown = document.getElementById(dropdownId);

                if (dropdown) {
                    dropdown.classList.add('hidden'); // Tutup dropdown
                    button.setAttribute('aria-expanded', 'false'); // Update atribut aria

                    // Reset ikon indikator
                    const icon = button.querySelector('.fa-chevron-down');
                    if (icon) {
                        icon.classList.remove('rotate-180');
                    }
                }
            });
        }

        // Sesuaikan layout
        adjustLayout(isMinimized);
    });

    // Dropdown Toggle
    document.querySelectorAll('[aria-controls]').forEach(button => {
        button.addEventListener('click', () => {
            const dropdownId = button.getAttribute('aria-controls');
            const dropdown = document.getElementById(dropdownId);

            if (dropdown) {
                const isHidden = dropdown.classList.contains('hidden');

                // Tutup semua dropdown yang terbuka
                document.querySelectorAll('[aria-controls]').forEach(otherButton => {
                    const otherDropdownId = otherButton.getAttribute('aria-controls');
                    const otherDropdown = document.getElementById(otherDropdownId);
                    if (otherDropdown && otherDropdownId !== dropdownId) {
                        otherDropdown.classList.add('hidden');
                        otherButton.setAttribute('aria-expanded', 'false');

                        // Reset indikator
                        const otherIcon = otherButton.querySelector('.fa-chevron-down');
                        if (otherIcon) {
                            otherIcon.classList.remove('rotate-180');
                        }
                    }
                });

                // Toggle dropdown yang dipilih
                dropdown.classList.toggle('hidden', !isHidden);
                button.setAttribute('aria-expanded', isHidden ? 'true' : 'false');

                // Rotate indikator (optional)
                const icon = button.querySelector('.fa-chevron-down');
                if (icon) {
                    icon.classList.toggle('rotate-180', isHidden);
                }
            }
        });
    });

    // Inisialisasi layout saat halaman dimuat
    const isMinimized = sidebar.getAttribute('data-minimized') === 'true';
    adjustLayout(isMinimized);
    

    // Inisialisasi layout saat halaman dimuat


});
AOS.init();

