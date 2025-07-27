// import AOS from 'aos';
// import 'aos/dist/aos.css';  // Mengimpor file CSS AOS

// document.addEventListener('DOMContentLoaded', () => {
//     const sidebar = document.getElementById('sidebar');
//     const toggleSidebar = document.getElementById('toggle-sidebar');
//     const logoFull = document.getElementById('logo-full');
//     const logoMini = document.getElementById('logo-mini');
//     const navbar = document.getElementById('navbar');
//     const admincontent = document.getElementById('admincontent'); // Konten utama
//     const footer = document.getElementById("main-footer"); // Footer utama
  


//     const adjustLayout = (isMinimized) => {
//         const sidebarWidth = isMinimized ? '4rem' : '16rem'; // Lebar sidebar
    
//         if (navbar) navbar.style.marginLeft = sidebarWidth;
//         if (admincontent) {
//             admincontent.style.marginLeft = sidebarWidth;
//             admincontent.style.width = `calc(100% - ${sidebarWidth})`;
//         }
//         if (footer) footer.style.marginLeft = sidebarWidth;
//     };
    
//     // Toggle Sidebar
//     toggleSidebar?.addEventListener('click', () => {
//         const isMinimized = sidebar.getAttribute('data-minimized') === 'true';
//         sidebar.setAttribute('data-minimized', !isMinimized);

//         // Adjust sidebar classes
//         sidebar.classList.toggle('w-64', !isMinimized);
//         sidebar.classList.toggle('w-16', isMinimized);

//         // Ubah visibilitas logo
//         logoFull?.classList.toggle('hidden', isMinimized);
//         logoMini?.classList.toggle('hidden', !isMinimized);

//         // Sembunyikan/Perlihatkan label menu
//         document.querySelectorAll('.menu-label').forEach(label => {
//             label.classList.toggle('hidden', isMinimized);
//         });
//         document.querySelectorAll('.fa-chevron-down').forEach(icon => {
//             icon.classList.toggle('hidden', isMinimized);
//         });

//         // Tutup semua dropdown jika sidebar diminimalkan
//         if (isMinimized) {
//             document.querySelectorAll('[aria-controls]').forEach(button => {
//                 const dropdownId = button.getAttribute('aria-controls');
//                 const dropdown = document.getElementById(dropdownId);

//                 if (dropdown) {
//                     dropdown.classList.add('hidden'); // Tutup dropdown
//                     button.setAttribute('aria-expanded', 'false'); // Update atribut aria

//                     // Reset ikon indikator
//                     const icon = button.querySelector('.fa-chevron-down');
//                     if (icon) {
//                         icon.classList.remove('rotate-180');
//                     }
//                 }
//             });
//         }

//         // Sesuaikan layout
//         adjustLayout(isMinimized);
//     });

//     // Dropdown Toggle
//     document.querySelectorAll('[aria-controls]').forEach(button => {
//         button.addEventListener('click', () => {
//             const dropdownId = button.getAttribute('aria-controls');
//             const dropdown = document.getElementById(dropdownId);

//             if (dropdown) {
//                 const isHidden = dropdown.classList.contains('hidden');

//                 // Tutup semua dropdown yang terbuka
//                 document.querySelectorAll('[aria-controls]').forEach(otherButton => {
//                     const otherDropdownId = otherButton.getAttribute('aria-controls');
//                     const otherDropdown = document.getElementById(otherDropdownId);
//                     if (otherDropdown && otherDropdownId !== dropdownId) {
//                         otherDropdown.classList.add('hidden');
//                         otherButton.setAttribute('aria-expanded', 'false');

//                         // Reset indikator
//                         const otherIcon = otherButton.querySelector('.fa-chevron-down');
//                         if (otherIcon) {
//                             otherIcon.classList.remove('rotate-180');
//                         }
//                     }
//                 });

//                 // Toggle dropdown yang dipilih
//                 dropdown.classList.toggle('hidden', !isHidden);
//                 button.setAttribute('aria-expanded', isHidden ? 'true' : 'false');

//                 // Rotate indikator (optional)
//                 const icon = button.querySelector('.fa-chevron-down');
//                 if (icon) {
//                     icon.classList.toggle('rotate-180', isHidden);
//                 }
//             }
//         });
//     });

//     // Inisialisasi layout saat halaman dimuat
//     const isMinimized = sidebar.getAttribute('data-minimized') === 'true';
//     adjustLayout(isMinimized);
    

//     // Inisialisasi layout saat halaman dimuat


// });
// AOS.init();

import AOS from 'aos';
import 'aos/dist/aos.css';

document.addEventListener('DOMContentLoaded', () => {
  const sidebar       = document.getElementById('sidebar');
  const toggleSidebar = document.getElementById('toggle-sidebar');
  const logoFull      = document.getElementById('logo-full');
  const logoMini      = document.getElementById('logo-mini');
  const navbar        = document.getElementById('navbar');
  const admincontent  = document.getElementById('admincontent');
  const footer        = document.getElementById('main-footer');

  // Make sidebar its own fixed scroll zone
  Object.assign(sidebar.style, {
    position:   'fixed',
    top:        '0',
    bottom:     '0',
    overflowY:  'auto',
  });

  const adjustLayout = (isMinimized) => {
    const w = isMinimized ? '4rem' : '16rem';
    [navbar, admincontent, footer].forEach(el => {
      if (!el) return;
      el.style.marginLeft = w;
    });
    if (admincontent) {
      admincontent.style.width = `calc(100% - ${w})`;
    }
  };

  toggleSidebar?.addEventListener('click', () => {
    // read old state, compute new
    const wasMinimized = sidebar.dataset.minimized === 'true';
    const isMinimized  = !wasMinimized;
    sidebar.dataset.minimized = isMinimized;

    // update widths
    sidebar.classList.toggle('w-16', isMinimized);
    sidebar.classList.toggle('w-64', !isMinimized);

    // logos
    logoFull?.classList.toggle('hidden', isMinimized);
    logoMini?.classList.toggle('hidden', !isMinimized);

    // menu labels + chevrons
    document.querySelectorAll('.menu-label, .fa-chevron-down').forEach(el => {
      el.classList.toggle('hidden', isMinimized);
    });

    // if just minimized, force-close any open dropdowns
    if (isMinimized) {
      document.querySelectorAll('[aria-controls]').forEach(btn => {
        const dd = document.getElementById(btn.getAttribute('aria-controls'));
        if (dd) {
          dd.classList.add('hidden');
          btn.setAttribute('aria-expanded', 'false');
          btn.querySelector('.fa-chevron-down')?.classList.remove('rotate-180');
        }
      });
    }

    // apply the new layout
    adjustLayout(isMinimized);
  });

  // dropdown toggles (unchanged)
  document.querySelectorAll('[aria-controls]').forEach(button => {
    button.addEventListener('click', () => {
      const id       = button.getAttribute('aria-controls');
      const dropdown = document.getElementById(id);
      const isHidden = dropdown.classList.contains('hidden');

      // close others
      document.querySelectorAll('[aria-controls]').forEach(other => {
        if (other === button) return;
        const od   = document.getElementById(other.getAttribute('aria-controls'));
        other.setAttribute('aria-expanded', 'false');
        od?.classList.add('hidden');
        other.querySelector('.fa-chevron-down')?.classList.remove('rotate-180');
      });

      // toggle this one
      dropdown.classList.toggle('hidden', !isHidden);
      button.setAttribute('aria-expanded', String(isHidden));
      button.querySelector('.fa-chevron-down')?.classList.toggle('rotate-180', isHidden);
    });
  });

  // on load, apply whatever the initial data-minimized says
  adjustLayout(sidebar.dataset.minimized === 'true');
});

AOS.init();
