@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark h4"><i class="fas fa-upload mr-2"></i> Upload Feedback</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 small">
                        <li class="breadcrumb-item"><a href="{{ route('new-memo.index') }}">List Memo</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('new-memo.show', ['memoId' => $document->id]) }}">{{ $document->documentnumber }}</a></li>
                        <li class="breadcrumb-item active">Upload Feedback</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container-fluid pb-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12">

                {{-- Error Handling --}}
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 rounded-lg mb-4">
                        <div class="d-flex align-items-start">
                            <i class="icon fas fa-ban mr-2 mt-1"></i>
                            <div>
                                <h6 class="font-weight-bold mb-1">Terdapat Kesalahan!</h6>
                                <ul class="mb-0 pl-3 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Main Card --}}
                <div class="card card-primary card-outline shadow-lg border-0 modern-card">
                    
                    {{-- Header --}}
                    <div class="card-header gradient-header">
                        <h5 class="card-title font-weight-bold mb-0 text-white text-wrap" style="white-space: normal;">
                            <i class="fas fa-comments mr-2"></i> Unggah Feedback {{ $document->documentname }}
                        </h5>
                    </div>

                    <div class="card-body modern-body p-3 p-md-4">
                        <form id="uploadForm" action="{{ route('new-memo.allfeedback', ['memoId' => $document->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Informasi Dokumen --}}
                            <div class="alert alert-light border-left-primary shadow-sm mb-4">
                                <div class="d-flex flex-wrap align-items-center">
                                    <div class="mr-3 mb-2 mb-md-0 text-center">
                                        <i class="fas fa-file-alt text-primary fa-2x"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <h6 class="font-weight-bold mb-1 text-break">{{ $document->documentname }}</h6>
                                        <div class="d-flex flex-wrap text-muted small">
                                            <span class="mr-3"><i class="fas fa-hashtag mr-1"></i> {{ $document->documentnumber }}</span>
                                            <span><i class="fas fa-building mr-1"></i> {{ auth()->user()->rule }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Status Review --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="review" class="font-weight-bold text-dark small text-uppercase">Apakah anda sudah melakukan review atas dokumen approval?</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-check-circle text-primary"></i></span>
                                            </div>
                                            <select id="review" name="review" class="form-control custom-select shadow-sm border-left-0 bg-light" required>
                                                <option value="Sudah">Sudah</option>
                                                <option value="Belum">Belum</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hasil Review --}}
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="hasil_review" class="font-weight-bold text-dark small text-uppercase">Dari hasil review atas dokumen approval tersebut, apakah dapat diterima?</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-right-0"><i class="fas fa-poll text-primary"></i></span>
                                            </div>
                                            <select id="hasil_review" name="hasil_review" class="form-control custom-select shadow-sm border-left-0 bg-light" required>
                                                <option value="Ya, dapat diterima">Ya, dapat diterima</option>
                                                <option value="Ya, dapat diterima dengan catatan">Ya, dapat diterima dengan catatan</option>
                                                <option value="Ada catatan">Ada catatan</option>
                                                <option value="Tidak">Tidak</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Komentar (Summernote) --}}
                            <div class="form-group mb-4">
                                <label for="comment" class="font-weight-bold text-dark small text-uppercase">Comment (optional):</label>
                                <textarea class="form-control" id="comment" name="comment"></textarea>
                            </div>

                            {{-- File Upload --}}
                            @if (config('app.url') === 'https://inka.goovicess.com')
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase">Jumlah File (Sementara File Kosong):</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-sort-numeric-up text-primary"></i></span>
                                        </div>
                                        <input type="number" id="filecount" name="filecount" class="form-control shadow-sm border-left-0 bg-light" min="0" max="100" step="1" value="0">
                                    </div>
                                </div>
                            @else
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark small text-uppercase">Lampiran File</label>
                                    <div class="custom-file shadow-sm">
                                        <input type="file" class="custom-file-input" id="file" name="file[]" multiple onchange="handleFileChange(this)">
                                        {{-- Menggunakan class 'border' standar agar garis terlihat, dan 'text-truncate' agar responsif --}}
                                        <label class="custom-file-label border text-truncate" for="file">Pilih file...</label>
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        <i class="fas fa-info-circle mr-1"></i> PDF, Docx, Xlsx, Gambar (Bisa banyak)
                                    </small>
                                </div>
                            @endif

                            {{-- Hidden Inputs --}}
                            <input type="hidden" name="aksi" value="uploaddocument">
                            <input type="hidden" name="rule" value="{{ auth()->user()->rule }}">
                            <input type="hidden" name="picrule" value="{{ auth()->user()->rule }}">
                            <input type="hidden" name="author" value="{{ auth()->user()->name }}">
                            <input type="hidden" name="time" value="">
                            <input type="hidden" name="level" value="{{ auth()->user()->rule }}">
                            <input type="hidden" name="conditionoffile" value="draft">
                            <input type="hidden" name="conditionoffile2" value="feedback">

                            <hr class="my-4">

                            {{-- Tombol Aksi --}}
                            <div class="d-flex flex-column flex-sm-row justify-content-between w-100">
                                <a href="{{ url()->previous() }}" class="btn btn-secondary rounded-pill px-4 py-2 mb-2 mb-sm-0 shadow-sm font-weight-bold order-2 order-sm-1">
                                    <i class="fas fa-arrow-left mr-1"></i> Batal
                                </a>
                                <button type="button" onclick="confirmUpload()" class="btn btn-success rounded-pill px-5 py-2 shadow-sm font-weight-bold order-1 order-sm-2">
                                    <i class="fas fa-paper-plane mr-1"></i> Kirim Feedback
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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    
    <style>
        .modern-card {
            border-radius: 15px;
            overflow: hidden;
        }
        .gradient-header {
            background: linear-gradient(45deg, #007bff, #0056b3); 
            padding: 15px 20px;
        }
        .border-left-primary {
            border-left: 4px solid #007bff !important;
            background-color: #f8f9fc;
        }
        
        /* Input Group Styling */
        .input-group-text {
            border-color: #ced4da;
            background-color: #fff;
            border-radius: 10px 0 0 10px;
        }
        .form-control:focus, .custom-select:focus {
            border-color: #007bff;
            box-shadow: none;
            background-color: #fff !important;
        }
        .form-control, .custom-select {
            border-radius: 0 10px 10px 0;
            height: calc(2.5rem + 2px);
        }

        /* Custom File Input */
        .custom-file-label {
            border-radius: 10px;
            height: calc(2.5rem + 2px);
            line-height: 1.8;
            padding-left: 15px;
            /* Pastikan border terlihat jika menggunakan class 'border' atau 'border-1' */
            border-color: #ced4da; 
        }
        .custom-file-label::after {
            height: auto;
            border-radius: 0 10px 10px 0;
            background-color: #e9ecef;
            line-height: 1.8;
        }

        /* Summernote Fixes */
        .note-editor.note-frame {
            border: 1px solid #ced4da !important; 
            border-radius: 10px; 
            overflow: hidden;
        }
        .note-toolbar {
            background-color: #f8f9fa !important; 
            border-bottom: 1px solid #dee2e6 !important;
        }
        .note-statusbar { display: none; }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#comment').summernote({
                placeholder: 'Tuliskan detail feedback disini...',
                tabsize: 2,
                minHeight: 200,
                height: 'auto',
                toolbar: [
                    ['font', ['bold', 'italic', 'underline']],
                    ['para', ['ul', 'paragraph']]
                ],
                disableDragAndDrop: true,
                dialogsInBody: true
            });
        });

        function handleFileChange(input) {
            let files = Array.from(input.files);
            let fileName = files.length > 1 ? files.length + " file dipilih" : (files[0]?.name || "Pilih file...");
            // Menggunakan .html() agar jika ada karakter khusus aman
            $(input).next('.custom-file-label').addClass("selected").html(fileName);
        }

        function confirmUpload() {
            Swal.fire({
                title: 'Konfirmasi Kirim',
                text: 'Pastikan data feedback sudah benar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengirim...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading() }
                    });
                    document.getElementById('uploadForm').submit();
                }
            });
        }
    </script>
@endpush