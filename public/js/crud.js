/**
 * crud.js
 * Handle all pop-up modals and dynamic form updates
 */

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if(modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Sedikit delay agar transisi CSS berjalan (trigger reflow)
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            
            // Animasi konten di dalamnya (membesar & muncul)
            const content = modal.firstElementChild;
            if(content) {
                content.classList.remove('scale-90', 'opacity-0', '-translate-y-10');
                content.classList.add('scale-100', 'opacity-100', 'translate-y-0');
            }
        }, 10);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if(modal) {
        // Animasi menghilang (reverse)
        modal.classList.remove('opacity-100');
        modal.classList.add('opacity-0');
        
        const content = modal.firstElementChild;
        if(content) {
            content.classList.remove('scale-100', 'opacity-100', 'translate-y-0');
            content.classList.add('scale-90', 'opacity-0', '-translate-y-10');
        }

        // Sembunyikan setelah animasi selesai (300ms)
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
}

// ==========================================
// MASTER DATA & TRANSAKSI GLOBAL CRUD SCRIPTS
// ==========================================

// Global Edit Data (Generik)
function editData(id, baseUrl, dataMap) {
    const form = document.getElementById('crudForm');
    if(!form) return;
    
    // Set Action URL
    form.action = baseUrl + "/" + id;
    
    // Set Method to PUT
    const methodInput = document.getElementById('formMethod');
    if(methodInput) methodInput.value = "PUT";
    
    // Populate Data
    for (const key in dataMap) {
        const input = document.getElementById(key);
        if (input) {
            input.value = dataMap[key];
        }
    }
    
    // Change Button Text
    const btn = document.getElementById('btnSubmit');
    if(btn) btn.innerText = "UPDATE";
}

// Global Reset Form (Generik)
function resetForm(baseUrl, fieldsToClear) {
    const form = document.getElementById('crudForm');
    if(!form) return;
    
    form.action = baseUrl;
    
    const methodInput = document.getElementById('formMethod');
    if(methodInput) methodInput.value = "POST";
    
    fieldsToClear.forEach(id => {
        const input = document.getElementById(id);
        if(input) input.value = '';
    });
    
    const btn = document.getElementById('btnSubmit');
    if(btn) btn.innerText = "SIMPAN";
}

// ==========================================
// SPECIFIC SCRIPTS
// ==========================================

// Specific for Request Order
function approveOrder(id, baseUrl) {
    const form = document.getElementById('approveForm');
    if(form) form.action = baseUrl + "/approve/" + id;
}

function rejectOrder(id, baseUrl) {
    const form = document.getElementById('rejectForm');
    if(form) form.action = baseUrl + "/reject/" + id;
}

function updateApproveQty(input) {
    // Memastikan qty_approve tidak lebih dari qty_request
    let max = parseInt(input.getAttribute('max'));
    let val = parseInt(input.value);
    if(val > max) {
        input.value = max;
    }
    if(val < 1) {
        input.value = 1;
    }
}


/* Global Delete Confirmation */
function confirmDelete(url) {
    const deleteForm = document.getElementById('globalDeleteForm');
    if (deleteForm) {
        deleteForm.action = url;
        openModal('deleteModal');
    }
}

/* Global PDF Download for Print Preview Modals */
function downloadPDF(filename = 'Laporan.pdf') {
    const element = document.getElementById('documentPreviewArea');
    if (!element) return;
    
    // Simpan style lama container agar pdf tidak kepotong (clipping)
    const container = element.parentElement;
    const oldOverflow = container.style.overflow;
    const oldMaxHeight = container.style.maxHeight;
    const oldHeight = container.style.height;

    // Bebaskan tinggi kontainer agar html2canvas bisa render full height
    container.style.overflow = 'visible';
    container.style.maxHeight = 'none';
    container.style.height = 'auto';

    // Set ukuran format ke A4 agar PDF terbagi otomatis ke beberapa halaman jika panjang
    const opt = {
        margin:       [10, 10, 10, 10],
        filename:     filename,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true, logging: false, backgroundColor: '#0b0b2a', scrollY: 0 },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(() => {
        // Kembalikan style
        container.style.overflow = oldOverflow;
        container.style.maxHeight = oldMaxHeight;
        container.style.height = oldHeight;
    });
}

/* Cetak menggunakan window baru agar tampilan identik dengan PDF */
function printPage() {
    const element = document.getElementById('documentPreviewArea');
    if (!element) return;

    // Kumpulkan semua CSS dari halaman saat ini
    let styles = '';
    document.querySelectorAll('link[rel="stylesheet"], style').forEach(node => {
        styles += node.outerHTML;
    });

    // Buat konten HTML untuk window print
    const printContent = `
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan</title>
    ${styles}
    <style>
        /* === RESET WARNA KE PUTIH/HITAM UNTUK HASIL CETAK YANG JELAS === */
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        html, body {
            margin: 0;
            padding: 0;
            background: #ffffff !important;
            color: #111111 !important;
            font-family: 'Inclusive Sans', sans-serif;
        }

        /* ---- Override semua warna teks menjadi hitam ---- */
        #documentPreviewArea,
        #documentPreviewArea * {
            color: #111111 !important;
            border-color: #cccccc !important;
            border-radius: 0 !important;
        }

        /* ---- Background putih untuk semua container ---- */
        #documentPreviewArea,
        #documentPreviewArea .glass-panel,
        #documentPreviewArea [class*="bg-"],
        #documentPreviewArea [class*="backdrop-"] {
            background: #ffffff !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            box-shadow: none !important;
            border: 1px solid #e0e0e0 !important;
        }

        /* ---- Header dokumen: beri border bawah yang jelas ---- */
        #documentPreviewArea > div:first-child {
            border-bottom: 2px solid #111111 !important;
            padding-bottom: 12px !important;
            margin-bottom: 16px !important;
        }

        /* ---- Tabel: border hitam yang jelas ---- */
        table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin-top: 12px !important;
        }
        th, td {
            border: 1px solid #333333 !important;
            padding: 8px 12px !important;
            color: #111111 !important;
            background: #ffffff !important;
            font-size: 10pt !important;
            text-align: left !important;
        }
        th {
            font-weight: bold !important;
            background: #f0f0f0 !important;
        }
        tfoot td, tfoot th {
            background: #e8e8e8 !important;
            font-weight: bold !important;
        }

        /* ---- Badge/pill selisih: beri border untuk visibility ---- */
        span[class*="rounded-full"] {
            border: 1px solid #333333 !important;
            background: transparent !important;
            padding: 2px 8px !important;
            font-weight: bold !important;
        }

        /* ---- Highlight box (Total Pendapatan, dll) ---- */
        #documentPreviewArea div[class*="rounded"] {
            background: #f5f5f5 !important;
            border: 1px solid #cccccc !important;
        }

        /* ---- Judul utama dokumen ---- */
        #documentPreviewArea h1 {
            font-size: 22pt !important;
            font-weight: 900 !important;
            letter-spacing: 0.1em;
        }

        /* ---- Bebaskan overflow agar tidak terpotong ---- */
        .overflow-y-auto, .overflow-x-auto, .overflow-hidden {
            overflow: visible !important;
            max-height: none !important;
            height: auto !important;
        }

        @media print {
            html, body { background: #ffffff !important; }
            * { overflow: visible !important; }
            @page { margin: 1.5cm; size: A4 portrait; }
        }
    </style>
</head>
<body>
    ${element.outerHTML}
    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); window.close(); }, 400);
        };
    <\/script>
</body>
</html>`;

    const printWindow = window.open('', '_blank', 'width=900,height=700');
    if (!printWindow) {
        alert('Popup diblokir! Izinkan popup untuk halaman ini lalu coba lagi.');
        return;
    }
    printWindow.document.write(printContent);
    printWindow.document.close();
}
