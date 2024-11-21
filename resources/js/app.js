import './bootstrap.js';
import '../css/app.css'; 

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const toggleSidebar = document.getElementById('toggle-sidebar');
    const logoFull = document.getElementById('logo-full');
    const logoMini = document.getElementById('logo-mini');
    const navbar = document.getElementById('navbar');
    const admincontent = document.getElementById('admincontent'); // Add content area

    // Toggle Sidebar
    toggleSidebar?.addEventListener('click', () => {
        const isMinimized = sidebar.getAttribute('data-minimized') === 'true';
        sidebar.setAttribute('data-minimized', !isMinimized);

        // Adjust sidebar classes
        sidebar.classList.toggle('w-64', !isMinimized);
        sidebar.classList.toggle('w-16', isMinimized);

        logoFull?.classList.toggle('hidden', isMinimized);
        logoMini?.classList.toggle('hidden', !isMinimized);

        document.querySelectorAll('.menu-label').forEach(label => {
            label.classList.toggle('hidden', isMinimized);
        });

        // Adjust navbar and content area
        adjustLayout(isMinimized);
    });

    const adjustLayout = (isMinimized) => {
        navbar.style.marginLeft = isMinimized ? '4rem' : '16rem';
        admincontent.style.marginLeft = isMinimized ? '4rem' : '16rem';
        admincontent.style.width = isMinimized ? 'calc(100% - 4rem)' : 'calc(100% - 16rem)';
    };

    // Dropdown Toggle
    document.querySelectorAll('[aria-controls]').forEach(button => {
        button.addEventListener('click', () => {
            const dropdownId = button.getAttribute('aria-controls');
            const dropdown = document.getElementById(dropdownId);
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                button.setAttribute('aria-expanded', dropdown.classList.contains('hidden') ? 'false' : 'true');
            }
        });
    });
});
