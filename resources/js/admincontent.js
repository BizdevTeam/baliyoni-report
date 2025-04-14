// Function to toggle the export modal visibility
function toggleExportModal() {
    const modal = document.getElementById('exportModal');
    modal.classList.toggle('hidden');
}

// Initialize export functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get export button and modal elements
    const exportFloatingButton = document.getElementById('exportFloatingButton');
    const cancelExportBtn = document.getElementById('cancelExportBtn');
    const confirmExportBtn = document.getElementById('confirmExportBtn');
    const exportModal = document.getElementById('exportModal');

    // Show modal when export button is clicked
    if (exportFloatingButton) {
        exportFloatingButton.addEventListener('click', toggleExportModal);
    }

    // Hide modal when cancel button is clicked
    if (cancelExportBtn) {
        cancelExportBtn.addEventListener('click', toggleExportModal);
    }

    // Export PDFs when confirm button is clicked
    if (confirmExportBtn) {
        confirmExportBtn.addEventListener('click', function() {
            // First hide the modal
            toggleExportModal();
            // Show loading indicator
            Swal.fire({
                title: 'Mengekspor PDF...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Get current page data
            const currentUrl = window.location.pathname;
            
            // Determine which export function to call based on current URL
            if (currentUrl.includes('rekap-penjualan-perusahaan')) {
                exportRekapPenjualanPerusahaan();
            } else if (currentUrl.includes('rekap-penjualan')) {
                exportRekapPenjualan();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Ekspor Gagal',
                    text: 'Halaman ini tidak mendukung ekspor PDF',
                });
            }
        });
    }
});

// Function to export Rekap Penjualan PDF
function exportRekapPenjualan() {
    // Get the table data
    const table = document.querySelector('.dataTable');
    if (!table) {
        Swal.fire({
            icon: 'error',
            title: 'Ekspor Gagal',
            text: 'Data tabel tidak ditemukan',
        });
        return;
    }
    
    // Generate table HTML for PDF
    let tableHTML = '';
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const date = row.querySelector('td:nth-child(1)').textContent.trim();
        const total = row.querySelector('td:nth-child(2)').textContent.trim();
        tableHTML += `<tr>
            <td style='border: 1px solid #000; padding: 2px;'>${date}</td>
            <td style='border: 1px solid #000; padding: 2px;'>${total}</td>
        </tr>`;
    });
    
    // Get chart as base64 image
    const chartContainer = document.getElementById('chartContainer');
    if (!chartContainer) {
        Swal.fire({
            icon: 'error',
            title: 'Ekspor Gagal',
            text: 'Data grafik tidak ditemukan',
        });
        return;
    }
    
    // Use html2canvas to convert the chart to an image
    html2canvas(chartContainer).then(canvas => {
        const chartBase64 = canvas.toDataURL('image/png');
        
        // Send data to server for PDF generation
        axios.post('/exports/export-rekap-penjualan-pdf', {
            table: tableHTML,
            chart: chartBase64
        })
        .then(response => {
            // Create a blob from the PDF data
            const blob = new Blob([response.data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);
            
            // Create a link to download the PDF
            const a = document.createElement('a');
            a.href = url;
            a.download = 'laporan_rekap_penjualan.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            
            Swal.fire({
                icon: 'success',
                title: 'Ekspor Berhasil',
                text: 'PDF berhasil diunduh',
            });
        })
        .catch(error => {
            console.error('Error exporting PDF:', error);
            Swal.fire({
                icon: 'error',
                title: 'Ekspor Gagal',
                text: 'Terjadi kesalahan saat mengekspor PDF',
            });
        });
    });
}

// Function to export Rekap Penjualan Perusahaan PDF
function exportRekapPenjualanPerusahaan() {
    // Get the table data
    const table = document.querySelector('.dataTable');
    if (!table) {
        Swal.fire({
            icon: 'error',
            title: 'Ekspor Gagal',
            text: 'Data tabel tidak ditemukan',
        });
        return;
    }
    
    // Generate table HTML for PDF
    let tableHTML = '';
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const date = row.querySelector('td:nth-child(1)').textContent.trim();
        const company = row.querySelector('td:nth-child(2)').textContent.trim();
        const total = row.querySelector('td:nth-child(3)').textContent.trim();
        tableHTML += `<tr>
            <td style='border: 1px solid #000; padding: 2px;'>${date}</td>
            <td style='border: 1px solid #000; padding: 2px;'>${company}</td>
            <td style='border: 1px solid #000; padding: 2px;'>${total}</td>
        </tr>`;
    });
    
    // Get chart as base64 image
    const chartContainer = document.getElementById('chartContainer');
    if (!chartContainer) {
        Swal.fire({
            icon: 'error',
            title: 'Ekspor Gagal',
            text: 'Data grafik tidak ditemukan',
        });
        return;
    }
    
    // Use html2canvas to convert the chart to an image
    html2canvas(chartContainer).then(canvas => {
        const chartBase64 = canvas.toDataURL('image/png');
        
        // Send data to server for PDF generation
        axios.post('/exports/export-rekap-penjualan-perusahaan-pdf', {
            table: tableHTML,
            chart: chartBase64
        })
        .then(response => {
            // Create a blob from the PDF data
            const blob = new Blob([response.data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);
            
            // Create a link to download the PDF
            const a = document.createElement('a');
            a.href = url;
            a.download = 'laporan_rekap_penjualan_perusahaan.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            
            Swal.fire({
                icon: 'success',
                title: 'Ekspor Berhasil',
                text: 'PDF berhasil diunduh',
            });
        })
        .catch(error => {
            console.error('Error exporting PDF:', error);
            Swal.fire({
                icon: 'error',
                title: 'Ekspor Gagal',
                text: 'Terjadi kesalahan saat mengekspor PDF',
            });
        });
    });
}