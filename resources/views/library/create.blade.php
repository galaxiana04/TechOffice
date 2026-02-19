@extends('layouts.universal')

@section('container2')
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 shadow-sm py-6">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-cloud-upload-alt text-blue-600 mr-2"></i>Upload Dokumen
                    </h1>
                    <p class="text-gray-500 mb-0">Tambahkan dokumen baru ke koleksi perpustakaan</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb bg-white px-4 py-2 rounded-lg float-right shadow-sm mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('library.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-book-open mr-1"></i>Library
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-gray-600">Upload File</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container mx-auto px-4 py-8">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-2xl rounded-3xl overflow-hidden">
                    
                    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white py-6 px-8">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-2xl font-bold mb-1">Form Upload File</h3>
                                <p class="text-blue-100 text-sm mb-0">Silakan lengkapi detail dokumen di bawah ini</p>
                            </div>
                            <button type="button" class="btn btn-sm bg-white bg-opacity-20 hover:bg-opacity-30 text-white border-0 rounded-lg transition"
                                data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-8 bg-white">
                        
                        @if (session('success'))
                            <div class="mb-6">
                                <div class="alert alert-success bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-xl flex items-center">
                                    <i class="fas fa-check-circle text-xl mr-3"></i>
                                    <span class="font-medium">{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('library.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="file_name" class="block text-gray-700 font-bold mb-2">
                                            <i class="fas fa-file-signature text-blue-500 mr-1"></i>Nama Dokumen <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="file_name" id="file_name" 
                                            class="form-control form-control-lg border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-0 transition" 
                                            placeholder="Masukkan nama dokumen..." required>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="file_code" class="block text-gray-700 font-bold mb-2">
                                            <i class="fas fa-barcode text-blue-500 mr-1"></i>Nomor Dokumen <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="file_code" id="file_code" 
                                            class="form-control form-control-lg border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-0 transition" 
                                            placeholder="Contoh: DOC-2024-001" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="project_id" class="block text-gray-700 font-bold mb-2">
                                            <i class="fas fa-folder text-blue-500 mr-1"></i>Kategori Proyek <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <select name="project_id" id="project_id" 
                                                class="form-control form-control-lg border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-0 transition appearance-none" required>
                                                <option value="">-- Pilih Proyek --</option>
                                                @foreach ($projects as $id => $title)
                                                    <option value="{{ $id }}">{{ $title }}</option>
                                                @endforeach
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                                                <i class="fas fa-chevron-down text-sm"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="file_link" class="block text-gray-700 font-bold mb-2">
                                            <i class="fas fa-link text-blue-500 mr-1"></i>Link Dokumen <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="file_link" id="file_link" 
                                            class="form-control form-control-lg border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-0 transition" 
                                            placeholder="https://..." required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-6 mt-2">
                                <label for="fileInput" class="block text-gray-700 font-bold mb-2">
                                    <i class="fas fa-paperclip text-blue-500 mr-1"></i>Pilih File
                                </label>
                                
                                <div class="p-6 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-blue-50 transition text-center relative group">
                                    <input type="file" name="path_file" id="fileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <div class="text-center">
                                        <div class="text-blue-600 mb-3 group-hover:scale-110 transition-transform duration-300">
                                            <i class="fas fa-cloud-upload-alt text-5xl"></i>
                                        </div>
                                        <span class="text-gray-700 font-bold text-lg block">Klik atau seret file ke sini</span>
                                        <p class="text-sm text-gray-500 mt-1" id="fileNameDisplay">Mendukung format: PDF, Word, Gambar, Video, Audio</p>
                                    </div>
                                </div>

                                <div id="previewContainer" class="mt-4 p-4 border rounded-xl bg-gray-50 hidden">
                                    <h5 class="text-sm font-bold text-gray-600 mb-3 border-b pb-2">Pratinjau File:</h5>
                                    
                                    <div class="flex justify-center bg-white p-2 rounded shadow-sm">
                                        <img id="previewImage" src="#" alt="Pratinjau Gambar" class="max-h-64 rounded hidden">
                                        <embed id="previewPdf" src="#" type="application/pdf" class="w-full h-96 hidden rounded" />
                                        <video id="previewVideo" controls class="w-full h-auto hidden rounded">
                                            <source id="previewVideoSource" src="#" type="video/mp4">
                                            Browser Anda tidak mendukung tag video.
                                        </video>
                                        <audio id="previewAudio" controls class="w-full mt-2 hidden">
                                            <source id="previewAudioSource" src="#" type="audio/mpeg">
                                            Browser Anda tidak mendukung elemen audio.
                                        </audio>
                                        <iframe id="previewText" src="#" class="w-full h-64 hidden border-0"></iframe>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-6 mt-4 flex flex-col-reverse md:flex-row md:justify-end gap-3">
                                <a href="{{ route('library.index') }}" 
                                    class="w-full md:w-auto btn bg-gray-100 text-gray-700 hover:bg-gray-200 px-6 py-3 rounded-xl font-semibold transition border-0 text-center">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>
                                <button type="submit" 
                                    class="w-full md:w-auto btn bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg transform hover:scale-105 transition border-0">
                                    <i class="fas fa-save mr-2"></i>Simpan Dokumen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .form-control-lg {
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
        }
        /* Custom select styling override */
        select.form-control-lg {
            background-image: none !important;
        }
        .hidden { display: none; }
        .block { display: block; }
    </style>
@endpush

@push('scripts')
<script>
    document.getElementById('fileInput').onchange = function(event) {
        var file = event.target.files[0];
        var fileNameDisplay = document.getElementById('fileNameDisplay');
        
        // Elemen Preview
        var previewContainer = document.getElementById('previewContainer');
        var previewImage = document.getElementById('previewImage');
        var previewPdf = document.getElementById('previewPdf');
        var previewVideo = document.getElementById('previewVideo');
        var previewVideoSource = document.getElementById('previewVideoSource');
        var previewAudio = document.getElementById('previewAudio');
        var previewAudioSource = document.getElementById('previewAudioSource');
        var previewText = document.getElementById('previewText');

        // Reset semua preview
        [previewImage, previewPdf, previewVideo, previewAudio, previewText].forEach(el => el.classList.add('hidden'));

        if (file) {
            // Update teks nama file
            fileNameDisplay.innerHTML = 'File Terpilih: <span class="text-blue-600 font-bold">' + file.name + '</span>';
            fileNameDisplay.classList.remove('text-gray-500');
            fileNameDisplay.classList.add('text-gray-800');

            var reader = new FileReader();
            var fileType = file.type;

            reader.onload = function(e) {
                previewContainer.classList.remove('hidden'); // Tampilkan container

                if (fileType.startsWith('image/')) {
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('hidden');
                } else if (fileType === 'application/pdf') {
                    previewPdf.src = e.target.result;
                    previewPdf.classList.remove('hidden');
                } else if (fileType.startsWith('video/')) {
                    previewVideoSource.src = e.target.result;
                    previewVideo.load();
                    previewVideo.classList.remove('hidden');
                } else if (fileType.startsWith('audio/')) {
                    previewAudioSource.src = e.target.result;
                    previewAudio.load();
                    previewAudio.classList.remove('hidden');
                } else if (fileType.startsWith('text/') || fileType.includes('word') || fileType.includes('officedocument')) {
                    // Note: Browser support for office docs in iframe is limited without external viewers
                    // For text files it works fine.
                    previewText.src = e.target.result;
                    previewText.classList.remove('hidden');
                } else {
                    // Fallback jika format tidak didukung preview langsung
                    previewContainer.innerHTML = '<div class="text-center text-gray-500 py-4"><i class="fas fa-file text-4xl mb-2"></i><br>Pratinjau tidak tersedia untuk format ini.</div>';
                    previewContainer.classList.remove('hidden');
                }
            };

            reader.readAsDataURL(file);
        } else {
            fileNameDisplay.innerHTML = 'Mendukung format: PDF, Word, Gambar, Video, Audio';
            fileNameDisplay.classList.add('text-gray-500');
            fileNameDisplay.classList.remove('text-gray-800');
            previewContainer.classList.add('hidden');
        }
    };
</script>
@endpush