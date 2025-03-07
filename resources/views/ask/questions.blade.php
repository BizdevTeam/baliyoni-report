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
                    <img src="{{ asset("images/question.jpg") }}" alt="Banner Image" class="absolute inset-0 w-full h-full object-cover" />
                        <div class="min-h-[350px] relative z-40 h-full max-w-[1500px] mx-auto flex flex-col justify-center items-center text-center text-white p-6">
                            <h2 class="text-[100px] font-bold">FAQ</h2>
                            <p class="sm:text-lg text-base text-center">Rapat Bulanan Baliyoni</p>
                        </div>
                    </div>

                    <div class="mx-auto bg-white p-8 rounded-2xl shadow-lg mt-8">
                        <h2 class="text-2xl font-semibold text-red-600 text-center">Search by Date</h2>
                    
                        <form action="{{ route('questions.index') }}" method="GET" class="space-y-6">
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
                                @foreach ($questions as $question)
                                <div class="p-4 border rounded-lg shadow-md bg-white w-full mb-6">
                                    <div class="w-full">
                                        <h2 class="text-lg font-bold text-gray-800">Question: {{ $question->question }}</h2>
                                        <p class="text-sm text-gray-600">Asked By: {{ $question->asked_by }} | Asked To: {{ $question->asked_to }}</p>
                                        <p class="text-sm text-gray-600">Created At: {{ $question->created_at->format('l, j F Y H:i:s') }}</p>
                                    </div>
                                    
                                    <!-- Form Edit Pertanyaan -->
                                    <form method="POST" action="{{ route('questions.update', $question->id) }}" id="edit-question-{{ $question->id }}" class="hidden w-full bg-gray-50 shadow-md rounded-lg p-4 mt-4">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="question" class="w-full border rounded p-2 focus:ring focus:ring-green-400" required>{{ $question->question }}</textarea>
                                        <button type="submit" class="mt-2 bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">Simpan</button>
                                    </form>
                                    
                                    <!-- Edit & Delete Buttons -->
                                    <div class="flex justify-end gap-2 mt-2">
                                        <button onclick="toggleForm('edit-question-{{ $question->id }}')" class="text-red-500 py-2 px-4 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                <path fill="currentColor" d="M6 22q-.825 0-1.412-.587T4 20V4q0-.825.588-1.412T6 2h8l6 6v3q-.575.125-1.075.4t-.925.7l-6 5.975V22zm8 0v-3.075l5.525-5.5q.225-.225.5-.325t.55-.1q.3 0 .575.113t.5.337l.925.925q.2.225.313.5t.112.55t-.1.563t-.325.512l-5.5 5.5zm6.575-5.6l.925-.975l-.925-.925l-.95.95zM13 9h5l-5-5l5 5l-5-5z"/>
                                            </svg>
                                        </button>
                                        <form method="POST" action="{{ route('questions.destroy', $question->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 py-2 px-4 rounded" onclick="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                    <path fill="currentColor" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM19 4h-3.5l-1-1h-5l-1 1H5v2h14z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Toggle button to show/hide answers -->
                                    <div class="mt-4">
                                        <button onclick="toggleAnswerSection('answers-{{ $question->id }}', this)" class="text-red-600 font-semibold flex items-center gap-2 hover:underline">
                                            <svg id="icon-{{ $question->id }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                <path fill="currentColor" d="m12 15.4l-6-6L7.4 8l4.6 4.6L16.6 8L18 9.4z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Answers Section -->
                                    <div id="answers-{{ $question->id }}" class="mt-4 hidden">
                                        <form method="POST" action="{{ route('answers.store', $question->id) }}" class="mt-4">
                                            @csrf
                                            <div class="relative flex items-center border rounded p-2">
                                                <input type="text" name="answer" placeholder="Tambahkan Jawaban / Saran / Masukan" class="flex-1 border-none focus:outline-none p-2" required>
                                                <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded ml-2 hover:bg-red-700">Kirim</button>
                                            </div>
                                        </form>
                                        
                                        <!-- Answers List -->
                                        <div class="mt-4">
                                            @foreach ($question->answers as $answer)
                                            <div class="p-4 bg-gray-100 rounded-lg mb-2">
                                                <p class="text-lg text-gray-700">{{ $answer->answer }}</p>
                                                
                                                <!-- Form Edit Jawaban -->
                                                <form method="POST" action="{{ route('answers.update', $answer->id) }}" id="edit-answer-{{ $answer->id }}" class="hidden bg-white shadow-md rounded-lg p-4 mt-4">
                                                    @csrf
                                                    @method('PUT')
                                                    <textarea name="answer" class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:outline-none" required>{{ $answer->answer }}</textarea>
                                                    <div class="flex justify-end gap-2 mt-2">
                                                        <button type="button" class="bg-red-500 text-white py-2 px-4 rounded" onclick="toggleForm('edit-answer-{{ $answer->id }}')">Batal</button>
                                                        <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded">Simpan</button>
                                                    </div>
                                                </form>
                                                
                                                <!-- Edit & Delete Answer Buttons -->
                                                <div class="flex justify-end gap-2 mt-2">
                                                    <button onclick="toggleForm('edit-answer-{{ $answer->id }}')" class="text-red-600 py-2 px-4 rounded">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                            <path fill="currentColor" d="M6 22q-.825 0-1.412-.587T4 20V4q0-.825.588-1.412T6 2h8l6 6v3q-.575.125-1.075.4t-.925.7l-6 5.975V22zm8 0v-3.075l5.525-5.5q.225-.225.5-.325t.55-.1q.3 0 .575.113t.5.337l.925.925q.2.225.313.5t.112.55t-.1.563t-.325.512l-5.5 5.5zm6.575-5.6l.925-.975l-.925-.925l-.95.95zM13 9h5l-5-5l5 5l-5-5z"/>
                                                        </svg>
                                                    </button>
                                                    <form method="POST" action="{{ route('answers.destroy', $answer->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 py-2 px-4 rounded" onclick="return confirm('Apakah Anda yakin ingin menghapus jawaban ini?')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                                <path fill="currentColor" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM19 4h-3.5l-1-1h-5l-1 1H5v2h14z"/>
                                                            </svg> 
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
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
                         {{-- <!-- Floating Action Button -->
                    <button id="toggleQuestion" class="w-[100px] h-[100px] fixed bottom-36 right-8 bg-red-600 text-white p-4 rounded-full shadow-lg hover:bg-red-700 transition-all flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24"><g fill="currentColor" fill-opacity="0" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="64" stroke-dashoffset="64" d="M12 3c4.97 0 9 4.03 9 9c0 4.97 -4.03 9 -9 9c-4.97 0 -9 -4.03 -9 -9c0 -4.97 4.03 -9 9 -9Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0"/></path><path fill="none" stroke-dasharray="16" stroke-dashoffset="16" d="M9 10c0 -1.66 1.34 -3 3 -3c1.66 0 3 1.34 3 3c0 0.98 -0.47 1.85 -1.2 2.4c-0.73 0.55 -1.3 0.6 -1.8 1.6"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.6s" dur="0.2s" values="16;0"/></path><path fill="none" stroke-dasharray="2" stroke-dashoffset="2" d="M12 17v0.01"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.8s" dur="0.2s" values="2;0"/></path><animate fill="freeze" attributeName="fill-opacity" begin="1.1s" dur="0.15s" values="0;0.3"/></g></svg>
                    </button> --}}

                    <!-- Modal Backdrop -->
                    <div id="modal-backdrop" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50"></div>

                    <!-- Modal -->
                    <div id="question-modal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg w-full max-w-2xl z-50">
                        <div class="p-6 border rounded-lg shadow max-h-[90vh] overflow-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold">Tambah Pertanyaan Baru</h2>
                                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <form method="POST" action="{{ route('questions.store') }}" onsubmit="closeModal()">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label for="question" class="block text-sm font-medium">Pertanyaan:</label>
                                        <input type="text" name="question" id="question" class="w-full border rounded p-2" required>
                                    </div>

                                    <!-- Dropdown Options (Perbaikan value yang duplikat) -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="asked_by" class="block text-sm font-medium">Pertanyaan Dari :</label>
                                            <select name="asked_by" id="asked_by" class="w-full border rounded p-2" required>
                                                <option value="Pimpinan">Pimpinan</option>
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
                                            <label for="asked_to" class="block text-sm font-medium"> :</label>
                                            <select name="asked_to" id="asked_to" class="w-full border rounded p-2" required>
                                                <option value="Pimpinan">Pimpinan</option>
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
                                    </div>

                                    <div>
                                        <label for="created_at" class="block text-sm font-medium">Tanggal & Waktu:</label>
                                        <input type="text" name="created_at" id="created_at" class="w-full border rounded p-2 bg-gray-100" readonly>
                                    </div>

                                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition-all">
                                        Tambah Pertanyaan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    </div>
                </div>
            </div>
        </div>

        <script>
            
            const toggleQuestion = document.getElementById('toggleQuestion');
            const questionId = document.getElementById('questionId');

            toggleQuestion.addEventListener('click', () => {
                questionId.classList.toggle('hidden');
            });

            document.addEventListener("DOMContentLoaded", function() {
        var currentDate = new Date();
        var formattedDate = currentDate.toLocaleString('id-ID', {
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric', 
            hour: 'numeric', 
            minute: 'numeric', 
            second: 'numeric'
        });

        // Set the value of the "created_at" field to the current date and time
        document.getElementById("created_at").value = formattedDate;
    });

            function toggleForm(formId) {
                const form = document.getElementById(formId);
                if (form.classList.contains('hidden')) {
                    form.classList.remove('hidden');
                } else {
                    form.classList.add('hidden');
                }
            }

            function toggleAnswerSection(answerId, button) {
            let answerSection = document.getElementById(answerId);
            let icon = button.querySelector("svg path");

            if (answerSection.style.display === "none" || answerSection.style.display === "") {
                answerSection.style.display = "block";
                icon.setAttribute("d", "m12 10.8l-4.6 4.6L6 14l6-6l6 6l-1.4 1.4z");
            } else {
                answerSection.style.display = "none";
                icon.setAttribute("d", "m12 15.4l-6-6L7.4 8l4.6 4.6L16.6 8L18 9.4z");
            }
        }

            function openModal() {
            // Set tanggal dan waktu otomatis
            const now = new Date();
            const options = {
                weekday: 'long'
                , year: 'numeric'
                , month: 'long'
                , day: 'numeric'
                , hour: '2-digit'
                , minute: '2-digit'
            };
            document.getElementById('created_at').value = now.toLocaleDateString('id-ID', options);

            // Tampilkan modal
            document.getElementById('modal-backdrop').classList.remove('hidden');
            document.getElementById('question-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal-backdrop').classList.add('hidden');
            document.getElementById('question-modal').classList.add('hidden');
        }
        document.getElementById('questionId').addEventListener('click',closeModal);


            
        </script>

</body>
