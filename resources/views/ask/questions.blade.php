<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Q&A</title>
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
        <div id="admincontent" class="content-wrapper ml-64 p-4 bg-gray-100 duration-300">
            <div class="mx-auto bg-white p-6 rounded-lg shadow">
                <div class="p-6">
                    <div class="relative font-sans before:absolute before:w-full before:h-full before:inset-0 before:bg-black before:opacity-50 before:z-10">
                        <img src="https://readymadeui.com/cardImg.webp" alt="Banner Image" class="absolute inset-0 w-full h-full object-cover" />
                        <div class="min-h-[350px] relative z-50 h-full max-w-6xl mx-auto flex flex-col justify-center items-center text-center text-white p-6">
                            <h2 class="sm:text-4xl text-2xl font-bold">Sesi Tanya Jawab</h2>
                            <p class="sm:text-lg text-base text-center">Rapat Bulanan Baliyoni</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-[40px]">
                        <!-- List Questions -->

                        <div>
                            @foreach ($questions as $question)
                            <div class="p-4 border rounded mb-4 shadow">
                                <div class="flex flex-wrap">
                                    <div class="block w-full">
                                        <h2 class="block text-lg font-bold text-gray-800">Question: {{ $question->question }}</h2>
                                        <p class="block text-sm text-gray-600">Asked By: {{ $question->asked_by }} | Asked To: {{ $question->asked_to }}</p>
                                        <p class="block text-sm text-gray-600">Created At: {{ $question->created_at->format('l, j F Y H:i:s') }}</p>
                                    </div>

                                    <!-- Form Edit Pertanyaan -->
                                    <form method="POST" action="{{ route('questions.update', $question->id) }}" id="edit-question-{{ $question->id }}" class="hidden w-full bg-white shadow-md rounded-lg p-4 mt-4 mb-4">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="question" class="w-full border rounded p-2" required>{{ $question->question }}</textarea>
                                        <button type="submit" class="mt-2 bg-green-600 text-white py-1 px-4 rounded">Simpan</button>
                                    </form>

                                    <!-- Edit Question Button -->
                                    <div class="flex justify-end w-full gap-2">
                                        <button onclick="toggleForm('edit-question-{{ $question->id }}')" class="flex items-center gap-2 bg-blue-600 text-white py-2 px-4 rounded">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                <path fill="currentColor" d="M6 22q-.825 0-1.412-.587T4 20V4q0-.825.588-1.412T6 2h8l6 6v3q-.575.125-1.075.4t-.925.7l-6 5.975V22zm8 0v-3.075l5.525-5.5q.225-.225.5-.325t.55-.1q.3 0 .575.113t.5.337l.925.925q.2.225.313.5t.112.55t-.1.563t-.325.512l-5.5 5.5zm6.575-5.6l.925-.975l-.925-.925l-.95.95zM13 9h5l-5-5l5 5l-5-5z"/>
                                            </svg>
                                            <span class="hidden sm:inline">Edit Jawaban</span>                                        </button>
                                        <form method="POST" action="{{ route('questions.destroy', $question->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center gap-2 bg-red-600 text-white py-2 px-4  rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                    <path fill="currentColor" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM19 4h-3.5l-1-1h-5l-1 1H5v2h14z"/>
                                                </svg>
                                                <span class="hidden sm:inline">Hapus Pertanyaan</span>
                                        </form>
                                    </div>
                                </div>

                                <!-- Toggle button to show/hide answers -->
                                <div class="flex justify-start items-center mt-4">
                                    <button onclick="toggleAnswerSection('answers-{{ $question->id }}')" class=" flex text-red-600 content-end end-0 text-end font-semibold hover:underline">
                                        Tampilkan Jawaban                                    
                                    </button>
                                </div>

                                <!-- Answers Section (hidden by default) -->
                                <div id="answers-{{ $question->id }}" class="mt-8 hidden">
                                    <form method="POST" action="{{ route('answers.store', $question->id) }}" class="mt-8">
                                        @csrf
                                        <div class="relative flex items-center border rounded p-2">
                                            <input type="text" name="answer" placeholder="Tambahkan Jawaban..." class="flex-1 border-none focus:outline-none p-2" required>
                                            <button type="submit" class=" flex items-center justify-center bg-red-600 text-white py-2 px-4 rounded ml-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                        <path stroke-dasharray="64" stroke-dashoffset="64" d="M13 3l6 6v12h-14v-18h8">
                                                            <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0"/>
                                                        </path>
                                                        <path stroke-dasharray="14" stroke-dashoffset="14" stroke-width="1" d="M12.5 3v5.5h6.5">
                                                            <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.2s" values="14;0"/>
                                                        </path>
                                                        <path stroke-dasharray="8" stroke-dashoffset="8" d="M9 14h6">
                                                            <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.9s" dur="0.2s" values="8;0"/>
                                                        </path>
                                                        <path stroke-dasharray="8" stroke-dashoffset="8" d="M12 11v6">
                                                            <animate fill="freeze" attributeName="stroke-dashoffset" begin="1.1s" dur="0.2s" values="8;0"/>
                                                        </path>
                                                    </g>
                                                </svg>
                                                Tambah Pertanyaan
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Answers -->
                                    <div class="mt-8">
                                        @foreach ($question->answers as $answer)
                                        <div class="flex flex-wrap  p-2 bg-gray-100 rounded mb-2">
                                            <div class="block w-full">
                                                <p class="block text-sm text-gray-700"><strong>Jawaban:</strong> {{ $answer->answer }}</p>
                                            </div>
                                            <!-- Edit Answer Button -->

                                            <!-- Form Edit Jawaban -->
                                            <form method="POST" action="{{ route('answers.update', $answer->id) }}" id="edit-answer-{{ $answer->id }}" class="hidden bg-white shadow-md rounded-lg p-4 w-full mt-4 mb-4">
                                                @csrf
                                                @method('PUT')

                                                <label for="answer" class="block text-sm font-medium text-gray-700 mb-2">Edit Answer</label>

                                                <textarea name="answer" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:outline-none" required>{{ $answer->answer }}</textarea>

                                                <div class="flex justify-end gap-2 mt-4">
                                                    <button type="button" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-all duration-300" onclick="document.getElementById('edit-answer-{{ $answer->id }}').classList.add('hidden');">
                                                        Cancel
                                                    </button>

                                                    <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-all duration-300">
                                                        Save
                                                    </button>
                                                </div>
                                            </form>

                                            <div class="flex justify-end w-full gap-2">
                                                <!-- Edit Button -->
                                                <button 
                                                    onclick="toggleForm('edit-answer-{{ $answer->id }}')" 
                                                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors duration-200 py-2 px-4 "
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                        <path fill="currentColor" d="M6 22q-.825 0-1.412-.587T4 20V4q0-.825.588-1.412T6 2h8l6 6v3q-.575.125-1.075.4t-.925.7l-6 5.975V22zm8 0v-3.075l5.525-5.5q.225-.225.5-.325t.55-.1q.3 0 .575.113t.5.337l.925.925q.2.225.313.5t.112.55t-.1.563t-.325.512l-5.5 5.5zm6.575-5.6l.925-.975l-.925-.925l-.95.95zM13 9h5l-5-5l5 5l-5-5z"/>
                                                    </svg>
                                                    <span class="hidden sm:inline">Edit Jawaban</span>
                                                </button>
                                            
                                                <!-- Delete Form -->
                                                <form 
                                                    method="POST" 
                                                    action="{{ route('answers.destroy', $answer->id) }}"
                                                    class="m-0"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button 
                                                        type="submit" 
                                                        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded transition-colors duration-200"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus jawaban ini?')"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                            <path fill="currentColor" d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6zM19 4h-3.5l-1-1h-5l-1 1H5v2h14z"/>
                                                        </svg>
                                                        <span class="hidden sm:inline">Hapus Jawaban</span>
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

                        <!-- Form Add Question -->
                        <div class="p-6 border rounded-lg shadow max-h-96 overflow-auto">
                            <form method="POST" action="{{ route('questions.store') }}">
                                @csrf
                                <div>
                                    <label for="question" class="block text-sm font-medium" >Pertanyaan:</label>
                                    <input type="text" name="question" id="question" class="w-full border rounded p-2" required>
                                </div>
                                <div>
                                    <label for="asked_by" class="block text-sm font-medium">Asked By :</label>
                                    <select name="asked_by" id="asked_by" class="w-full border rounded p-2" required>
                                        <option value="Divisi Marketing">Pimpinan</option>
                                        <option value="Divisi Marketing">SPI Operasional</option>
                                        <option value="Divisi Marketing">SPI IT</option>
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
                                    <label for="asked_to" class="block text-sm font-medium">Asked To:</label>
                                    <select name="asked_to" id="asked_to" class="w-full border rounded p-2" required>
                                        <option value="Divisi Marketing">Pimpinan</option>
                                        <option value="Divisi Marketing">SPI Operasional</option>
                                        <option value="Divisi Marketing">SPI IT</option>
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
                                    <label for="created_at" class="block text-sm font-medium">Tanggal & Waktu:</label>
                                    <input type="text" name="created_at" id="created_at" class="w-full border rounded p-2" readonly>
                                </div>
                                <div class="flex justify-end w-full gap-2">
                                    <button type="submit" class="mt-4 w-full bg-red-600 text-white py-2 rounded flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                <path stroke-dasharray="64" stroke-dashoffset="64" d="M13 3l6 6v12h-14v-18h8">
                                                    <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="64;0"/>
                                                </path>
                                                <path stroke-dasharray="14" stroke-dashoffset="14" stroke-width="1" d="M12.5 3v5.5h6.5">
                                                    <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.7s" dur="0.2s" values="14;0"/>
                                                </path>
                                                <path stroke-dasharray="8" stroke-dashoffset="8" d="M9 14h6">
                                                    <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.9s" dur="0.2s" values="8;0"/>
                                                </path>
                                                <path stroke-dasharray="8" stroke-dashoffset="8" d="M12 11v6">
                                                    <animate fill="freeze" attributeName="stroke-dashoffset" begin="1.1s" dur="0.2s" values="8;0"/>
                                                </path>
                                            </g>
                                        </svg>
                                        Tambah Pertanyaan
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <script>
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

            function toggleAnswerSection(id) {
                var section = document.getElementById(id);
                section.classList.toggle('hidden');
            }

        </script>

</body>
