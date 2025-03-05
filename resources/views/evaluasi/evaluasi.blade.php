<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FAQ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('templates/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <script src="https://cdn.ckeditor.com/ckeditor5/38.1.0/classic/ckeditor.js"></script>
    @vite('resources/css/tailwind.css')
    @vite('resources/css/custom.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-100 hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Sidebar -->
        <x-sidebar class="w-64 h-screen fixed bg-gray-800 text-white z-10" />

        <!-- Navbar -->
        <x-navbar class="fixed top-0 left-64 right-0 h-16 bg-gray-800 text-white shadow z-20 flex items-center px-4" />

        <!-- Main Content -->
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100 duration-300 mt-14">
            <div class="mx-auto bg-white p-6 rounded-lg shadow">
                <div class="p-6">
                    <div class="relative font-sans before:absolute before:w-full before:h-full before:inset-0 before:bg-black before:opacity-50 before:z-10">
                        <img src="https://readymadeui.com/cardImg.webp" alt="Banner Image" class="absolute inset-0 w-full h-full object-cover" />
                        <div class="min-h-[350px] relative z-40 h-full max-w-[1500px] mx-auto flex flex-col justify-center items-center text-center text-white p-6">
                            <h2 class="text-[100px] font-bold">Evaluasi Kinerja</h2>
                            <p class="sm:text-lg text-base text-center">Per Divisi Perusahaan Baliyoni</p>
                        </div>
                    </div>

                    <div class="mx-auto bg-white p-8 rounded-2xl shadow-lg mt-8">
                        <h2 class="text-2xl font-semibold text-red-600 text-center">Search by Date</h2>
                    
                        <form action="{{ route('evaluasi.index') }}" method="GET" class="space-y-6">
                            <div class="flex items-end gap-4">
                                <!-- Start Date -->
                                <div class="flex-1">
                                    <label for="start_date" class="text-gray-700 mb-2 block">Start Date:</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                                           class="w-full p-3 rounded-lg border border-gray-300 focus:ring-4 focus:ring-red-400 focus:outline-none">
                                </div>
                                <!-- End Date -->
                                <div class="flex-1">
                                    <label for="end_date" class="text-gray-700 mb-2 block">End Date:</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                           class="w-full p-3 rounded-lg border border-gray-300 focus:ring-4 focus:ring-red-400 focus:outline-none">
                                </div>
                                <!-- Search Button -->
                                <div>
                                    <button type="submit"
                                            class="bg-red-500 text-white font-semibold py-3 px-6 rounded-lg hover:bg-red-600 transition duration-300">
                                        Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>                    
                    
                    <div class="w-full">
                        <div class="grid grid-cols-1 gap-4 mt-10 w-full">
                            <div id="questionId" class="w-full">
                                @foreach ($evaluasis as $evaluasi)
                                <div class="p-6 border rounded-lg shadow-md bg-white w-full mb-6">
                                    <div class="w-full">
                                        <h2 class="text-lg font-bold text-gray-800">Divisi: {{ $evaluasi->divisi }}</h2>
                                        <p class="text-sm text-gray-600 mb-4">Created At: {{ $evaluasi->created_at->format('l, j F Y H:i:s') }}</p>
                                        
                                        <!-- Toggle button to show/hide answers -->
                                        <div class="mt-4">
                                            <button onclick="toggleAnswerSection('answers-{{ $evaluasi->id }}', this)" class="text-red-600 font-semibold flex items-center gap-2 hover:underline">
                                                <svg id="icon-{{ $evaluasi->id }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                    <path fill="currentColor" d="m12 15.4l-6-6L7.4 8l4.6 4.6L16.6 8L18 9.4z"/>
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Content Section (Hidden by Default) -->
                                        <div id="answers-{{ $evaluasi->id }}" class="hidden mt-4">
                                            <div class="mb-4">
                                                <p class="text-sm font-semibold text-gray-700">Target Realisasi:</p>
                                                <div class="p-4 bg-gray-100 rounded-lg">
                                                    <div class="text-sm text-gray-600 content-html align-top text-justify">{!! $evaluasi->target_realisasi !!}</div>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <p class="text-sm font-semibold text-gray-700">Analisa Penyimpangan:</p>
                                                <div class="p-4 bg-gray-100 rounded-lg">
                                                    <div class="text-sm text-gray-600 content-html align-top text-justify">{!! $evaluasi->analisa_penyimpangan !!}</div>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <p class="text-sm font-semibold text-gray-700">Alternative Solusi:</p>
                                                <div class="p-4 bg-gray-100 rounded-lg">
                                                    <div class="text-sm text-gray-600 content-html align-top text-justify">{!! $evaluasi->alternative_solusi !!}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Edit & Delete Buttons -->
                                    <div class="flex justify-end gap-2 mt-4">
                                        <button onclick="openEditModal('{{ $evaluasi->id }}')" class="text-blue-500 py-2 px-4 rounded bg-blue-100 hover:bg-blue-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                <path fill="currentColor" d="M6 22q-.825 0-1.412-.587T4 20V4q0-.825.588-1.412T6 2h8l6 6v3q-.575.125-1.075.4t-.925.7l-6 5.975V22zm8 0v-3.075l5.525-5.5q.225-.225.5-.325t.55-.1q.3 0 .575.113t.5.337l.925.925q.2.225.313.5t.112.55t-.1.563t-.325.512l-5.5 5.5zm6.575-5.6l.925-.975l-.925-.925l-.95.95zM13 9h5l-5-5l5 5l-5-5z"/>
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('evaluasi.destroy', $evaluasi->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 py-2 px-4 rounded bg-red-100 hover:bg-red-200" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                    <path fill="currentColor" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM19 4h-3.5l-1-1h-5l-1 1H5v2h14z"/>
                                                </svg> 
                                            </button>
                                        </form>
                                    </div>
                                </div>  

                            <!-- Edit Modal -->
                            <div id="edit-modal-{{ $evaluasi->id }}" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-white p-6 rounded-lg shadow-lg w-96 overflow-auto">
                                    <h3 class="text-lg font-bold mb-4">Edit Evaluasi</h3>
                                    <form method="POST" action="{{ route('evaluasi.update', $evaluasi->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label for="divisi" class="block text-sm font-medium">Divisi</label>
                                            <input type="hidden" name="divisi" id="edit-{{ $evaluasi->id }}-divisi-input" value="{{ $evaluasi->divisi }}">
                                            <select name="divisi" id="edit-{{ $evaluasi->id }}-divisi" class="w-full border rounded p-2" required>
                                                <option value="SPI Operasional" {{ $evaluasi->divisi == 'SPI Operasional' ? 'selected' : '' }}>SPI Operasional</option>
                                                <option value="SPI IT" {{ $evaluasi->divisi == 'SPI IT' ? 'selected' : '' }}>SPI IT</option>
                                                <option value="Divisi Marketing" {{ $evaluasi->divisi == 'Divisi Marketing' ? 'selected' : '' }}>Divisi Marketing</option>
                                                <option value="Divisi Procurement" {{ $evaluasi->divisi == 'Divisi Procurement' ? 'selected' : '' }}>Divisi Procurement</option>
                                                <option value="Divisi Support" {{ $evaluasi->divisi == 'Divisi Support' ? 'selected' : '' }}>Divisi Support</option>
                                                <option value="Divisi Accounting" {{ $evaluasi->divisi == 'Divisi Accounting' ? 'selected' : '' }}>Divisi Accounting</option>
                                                <option value="Divisi IT" {{ $evaluasi->divisi == 'Divisi IT' ? 'selected' : '' }}>Divisi IT</option>
                                                <option value="Divisi HRGA" {{ $evaluasi->divisi == 'Divisi HRGA' ? 'selected' : '' }}>Divisi HRGA</option>
                                                <option value="Divisi SPI" {{ $evaluasi->divisi == 'Divisi SPI' ? 'selected' : '' }}>Divisi SPI</option>
                                            </select>
                                        </div>                                        
                                        <div>
                                            <label for="target_realisasi" class="block text-sm font-medium">Target Realisasi</label>
                                            <input type="hidden" name="target_realisasi" id="edit-{{ $evaluasi->id }}-target_realisasi-input" value="{{ $evaluasi->target_realisasi }}">
                                            <div id="edit-{{ $evaluasi->id }}-target_realisasi" class="editor-container"></div>
                                        </div>
                                        <div>
                                            <label for="analisa_penyimpangan" class="block text-sm font-medium">Analisa Penyimpangan</label>
                                            <input type="hidden" name="analisa_penyimpangan" id="edit-{{ $evaluasi->id }}-analisa_penyimpangan-input" value="{{ $evaluasi->analisa_penyimpangan }}">
                                            <div id="edit-{{ $evaluasi->id }}-analisa_penyimpangan" class="editor-container"></div>
                                        </div>
                                        <div>
                                            <label for="alternative_solusi" class="block text-sm font-medium">Alternative Solusi</label>
                                            <input type="hidden" name="alternative_solusi" id="edit-{{ $evaluasi->id }}-alternative_solusi-input" value="{{ $evaluasi->alternative_solusi }}">
                                            <div id="edit-{{ $evaluasi->id }}-alternative_solusi" class="editor-container"></div>
                                        </div>

                                        <div class="flex justify-end gap-2">
                                            <button type="button" onclick="closeEditModal('{{ $evaluasi->id }}')" class="py-2 px-4 bg-gray-200 rounded">Batal</button>
                                            <button type="submit" class="py-2 px-4 bg-blue-500 text-white rounded">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                    <!-- Floating Action Button -->
                    <button onclick="openModal()" class="w-[80px] h-[80px] fixed bottom-8 right-8 bg-red-600 text-white p-4 rounded-full shadow-lg hover:bg-red-700 transition-all flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
  
                    <!-- Modal Backdrop -->
                    <div id="modal-backdrop" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50"></div>

                    <!-- Modal -->
                    <div id="add-modal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg w-full max-w-2xl z-50">
                        <div class="p-6 border rounded-lg shadow max-h-[90vh] overflow-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold">Tambah Evaluasi Kinerja</h2>
                                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('evaluasi.store') }}" onsubmit="closeModal()">
                                @csrf
                                <div class="space-y-4">
                                <!-- Dropdown Options (Perbaikan value yang duplikat) -->
                                <div class="gap-4 space-y">
                                    <div>
                                        <label for="created_at" class="block text-sm font-medium">Periode:</label>
                                        <input type="text" name="created_at" id="created_at" class="w-full border rounded p-2 bg-gray-100" readonly>
                                    </div>
                                    <div>
                                        <label for="divisi" class="block text-sm font-medium">Evaluasi Kinerja Divisi :</label>
                                        <select name="divisi" id="divisi" class="w-full border rounded p-2" required>
                                            <option value="SPI Operasional">SPI Operasional</option>
                                            <option value="SPI IT">SPI IT</option>
                                            <option value="Divisi Marketing">Divisi Marketing</option>
                                            <option value="Divisi Procurement">Divisi Procurement</option>
                                            <option value="Divisi Support">Divisi Support</option>
                                            <option value="Divisi Accounting">Divisi Accounting</option>
                                            <option value="Divisi IT">Divisi IT</option>
                                            <option value="Divisi HRGA">Divisi HRGA</option>
                                            <option value="Divisi SPI">Divisi SPI</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="target_realisasi" class="block text-sm font-medium">Aspek</label>
                                        <input type="hidden" name="target_realisasi" id="target_realisasi-input">
                                        <div id="editor-target_realisasi"></div>
                                    </div>
                                    <div>
                                        <label for="analisa_penyimpangan" class="block text-sm font-medium">Masalah</label>
                                        <input type="hidden" name="analisa_penyimpangan" id="analisa_penyimpangan-input">
                                        <div id="editor-analisa_penyimpangan"></div>
                                    </div>
                                    <div>
                                        <label for="alternative_solusi" class="block text-sm font-medium mb-1">Solusi <span class="text-red-500">*</span></label>
                                        <input type="hidden" name="alternative_solusi" id="alternative_solusi-input" required>
                                        <div id="editor-alternative_solusi"></div>
                                    </div>

                                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition-all">
                                        Tambah
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Styling agar numbered list & bullet list tetap tampil di tabel */
        .content-html ol {
        list-style-type: decimal;
        margin-left: 20px;
        }
    
        .content-html ul {
        list-style-type: disc;
        margin-left: 20px;
        }
    
        .content-html li {
        margin-bottom: 4px;
        }
    </style>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Toggle pertanyaan
    const toggleQuestion = document.getElementById("toggleQuestion");
    const questionId = document.getElementById("questionId");

    if (toggleQuestion && questionId) {
        toggleQuestion.addEventListener("click", () => {
            questionId.classList.toggle("hidden");
        });
    }

    // Mengatur waktu otomatis pada input 'created_at'
    const createdAtInput = document.getElementById("created_at");
    if (createdAtInput) {
        const currentDate = new Date();
        const formattedDate = currentDate.toLocaleString("id-ID", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
        });
        createdAtInput.value = formattedDate;
    }

    // Fungsi untuk menampilkan/menyembunyikan form
    window.toggleForm = function (formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.classList.toggle("hidden");
        }
    };

    // Fungsi untuk toggle jawaban dan ikon
    window.toggleAnswerSection = function (answerId, button) {
        const answerSection = document.getElementById(answerId);
        if (!answerSection || !button) return;

        let icon = button.querySelector("svg path");

        if (answerSection.style.display === "none" || answerSection.style.display === "") {
            answerSection.style.display = "block";
            if (icon) icon.setAttribute("d", "m12 10.8l-4.6 4.6L6 14l6-6l6 6l-1.4 1.4z");
        } else {
            answerSection.style.display = "none";
            if (icon) icon.setAttribute("d", "m12 15.4l-6-6L7.4 8l4.6 4.6L16.6 8L18 9.4z");
        }
    };

    // Fungsi untuk membuka modal dan mengisi tanggal otomatis
    window.openModal = function () {
        const now = new Date();
        const options = {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        };

        const modalBackdrop = document.getElementById("modal-backdrop");
        const modal = document.getElementById("add-modal");

        if (createdAtInput) createdAtInput.value = now.toLocaleDateString("id-ID", options);
        if (modalBackdrop) modalBackdrop.classList.remove("hidden");
        if (modal) modal.classList.remove("hidden");
    };

    // Fungsi untuk menutup modal
    window.closeModal = function () {
        document.getElementById("modal-backdrop")?.classList.add("hidden");
        document.getElementById("add-modal")?.classList.add("hidden");
    };

    // Menutup modal saat klik di luar modal
    if (questionId) {
        questionId.addEventListener("click", closeModal);
    }

    let editors = {}; // Object untuk menyimpan instance CKEditor

    function initCKEditor(elementId, inputId) {
        const editorElement = document.getElementById(elementId);
        const inputElement = document.getElementById(inputId);
        
        if (!editorElement || !inputElement) return;

        // Hancurkan editor jika sudah ada sebelumnya
        if (editors[elementId]) {
            editors[elementId].destroy().then(() => {
                delete editors[elementId];
            });
        }

        ClassicEditor.create(editorElement, {
            toolbar: ["bold", "italic", "|", "bulletedList", "numberedList", "|", "undo", "redo"]
        }).then((editor) => {
            editors[elementId] = editor;
            editor.setData(inputElement.value);
            editor.model.document.on("change:data", () => {
                inputElement.value = editor.getData();
            });
        }).catch((error) => console.error("CKEditor error:", error));
    }

    // Inisialisasi CKEditor di form tambah
    initCKEditor("editor-target_realisasi", "target_realisasi-input");
    initCKEditor("editor-analisa_penyimpangan", "analisa_penyimpangan-input");
    initCKEditor("editor-alternative_solusi", "alternative_solusi-input");

    // Fungsi untuk membuka modal edit dan menginisialisasi CKEditor
    window.openEditModal = function (id) {
        const modal = document.getElementById(`edit-modal-${id}`);
        if (modal) {
            modal.classList.remove("hidden");
            
            // Inisialisasi CKEditor di modal edit
            initCKEditor(`edit-${id}-target_realisasi`, `edit-${id}-target_realisasi-input`);
            initCKEditor(`edit-${id}-analisa_penyimpangan`, `edit-${id}-analisa_penyimpangan-input`);
            initCKEditor(`edit-${id}-alternative_solusi`, `edit-${id}-alternative_solusi-input`);
        }
    };

    // Fungsi untuk menutup modal edit
    window.closeEditModal = function (id) {
        const modal = document.getElementById(`edit-modal-${id}`);
        if (modal) modal.classList.add("hidden");
    };

    console.log("JavaScript Loaded");
    });
    function toggleAnswerSection(answerId, iconId) {
        let answerSection = document.getElementById(answerId);
        let icon = document.getElementById(iconId).querySelector("path");

        if (answerSection) {
            answerSection.classList.toggle("hidden");

            // Mengubah ikon panah
            if (answerSection.classList.contains("hidden")) {
                icon.setAttribute("d", "m12 15.4l-6-6L7.4 8l4.6 4.6L16.6 8L18 9.4z"); // Panah ke bawah
            } else {
                icon.setAttribute("d", "m12 10.8l-4.6 4.6L6 14l6-6l6 6l-1.4 1.4z"); // Panah ke atas
            }
        }
    }


</script>        
</body>
