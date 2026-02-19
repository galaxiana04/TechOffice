@extends('layouts.universal')

@section('container2')
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 shadow-sm py-6">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-edit text-blue-600 mr-2"></i>Edit Dokumen
                    </h1>
                    <p class="text-gray-500 mb-0">Perbarui informasi dokumen perpustakaan</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb bg-white px-4 py-2 rounded-lg float-right shadow-sm mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('library.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-book-open mr-1"></i>Library
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-gray-600">Edit File</li>
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
                                <h3 class="text-2xl font-bold mb-1">Form Edit File</h3>
                                <!-- <p class="text-blue-100 text-sm mb-0">ID Dokumen: #{{ $file->id }}</p> -->
                            </div>
                            <button type="button"
                                class="btn btn-sm bg-white bg-opacity-20 hover:bg-opacity-30 text-white border-0 rounded-lg transition"
                                data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-8 bg-white">

                        @if (session('success'))
                            <div class="mb-6">
                                <div
                                    class="alert alert-success bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-xl flex items-center">
                                    <i class="fas fa-check-circle text-xl mr-3"></i>
                                    <span class="font-medium">{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('library.update', $file->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="file_name" class="block text-gray-700 font-bold mb-2">
                                            <i class="fas fa-file-signature text-blue-500 mr-1"></i>Nama File <span
                                                class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="file_name" id="file_name"
                                            class="form-control form-control-lg border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-0 transition"
                                            value="{{ old('file_name', $file->file_name) }}"
                                            placeholder="Masukkan nama dokumen..." required>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="file_code" class="block text-gray-700 font-bold mb-2">
                                            <i class="fas fa-barcode text-blue-500 mr-1"></i>Kode File <span
                                                class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="file_code" id="file_code"
                                            class="form-control form-control-lg border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-0 transition"
                                            value="{{ old('file_code', $file->file_code) }}"
                                            placeholder="Contoh: DOC-2024-001" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="project_id" class="block text-gray-700 font-bold mb-2">
                                            <i class="fas fa-folder text-blue-500 mr-1"></i>Kategori Proyek <span
                                                class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <select name="project_id" id="project_id"
                                                class="form-control form-control-lg border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-0 transition appearance-none"
                                                required>
                                                <option value="">-- Pilih Proyek --</option>
                                                @foreach ($projects as $id => $title)
                                                    <option value="{{ $id }}" {{ $file->project_id == $id ? 'selected' : '' }}>
                                                        {{ $title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-700">
                                                <i class="fas fa-chevron-down text-sm"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="file_link" class="block text-gray-700 font-bold mb-2">
                                            <i class="fas fa-link text-blue-500 mr-1"></i>Link Dokumen
                                        </label>
                                        <input type="text" name="file_link" id="file_link"
                                            class="form-control form-control-lg border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-0 transition"
                                            value="{{ old('file_link', $file->file_link) }}" placeholder="https://...">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-6 mt-2">
                                <label for="path_file" class="block text-gray-700 font-bold mb-2">
                                    <i class="fas fa-paperclip text-blue-500 mr-1"></i>Upload File Baru (Opsional)
                                </label>
                                <div
                                    class="p-4 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-blue-50 transition text-center">
                                    <input type="file" name="path_file" id="path_file" class="form-control-file d-none"
                                        onchange="previewFile()">
                                    <label for="path_file" class="cursor-pointer d-block mb-0">
                                        <div class="text-blue-600 mb-2">
                                            <i class="fas fa-cloud-upload-alt text-4xl"></i>
                                        </div>
                                        <span class="text-gray-600 font-medium">Klik untuk memilih file baru</span>
                                        <p class="text-xs text-gray-400 mt-1" id="file-name-display">Biarkan kosong jika
                                            tidak ingin mengganti file</p>
                                    </label>
                                </div>
                                @if($file->path_file)
                                    <small class="text-gray-500 mt-2 d-block">
                                        <i class="fas fa-info-circle mr-1"></i>File saat ini: <a
                                            href="{{ asset('storage/' . $file->path_file) }}" target="_blank"
                                            class="text-blue-600 hover:underline">Lihat File</a>
                                    </small>
                                @endif
                            </div>

                            <div
                                class="border-t border-gray-200 pt-6 mt-4 flex flex-col-reverse md:flex-row md:justify-end gap-3">
                                <a href="{{ route('library.index') }}"
                                    class="w-full md:w-auto btn bg-gray-100 text-gray-700 hover:bg-gray-200 px-6 py-3 rounded-xl font-semibold transition border-0 text-center">
                                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                                </a>
                                <button type="submit"
                                    class="w-full md:w-auto btn bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg transform hover:scale-105 transition border-0">
                                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
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
            /* Hapus panah default browser */
        }
    </style>
@endpush

@push('scripts')
    <script>
        function previewFile() {
            const input = document.getElementById('path_file');
            const display = document.getElementById('file-name-display');

            if (input.files && input.files[0]) {
                display.innerHTML = 'File terpilih: <span class="text-blue-600 font-bold">' + input.files[0].name + '</span>';
                display.classList.remove('text-gray-400');
                display.classList.add('text-gray-700');
            } else {
                display.innerText = 'Biarkan kosong jika tidak ingin mengganti file';
                display.classList.add('text-gray-400');
            }
        }
    </script>
@endpush