@extends('layouts.universal')

@section('container2')
    <div class="content-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.5rem; line-height: 1.2;">Ekspedisi</h1>
                    <p class="text-muted small mb-0">Manajemen pengunggahan dokumen ekspedisi</p>
                </div>
                <div class="col-sm-6 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="/" class="text-muted hover-primary">
                                <i class="fas fa-home mr-1"></i> Dashboard
                            </a>
                        </li>
                        {{-- Ubah warna teks breadcrumb aktif menjadi merah --}}
                        <li class="breadcrumb-item active text-danger font-weight-bold">Upload Ekspedisi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <style>
        /* --- MODERN STYLES (RED THEME) --- */
        .card-modern {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            background: #fff;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        /* Header Gradient - Merah */
        .header-gradient {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            padding: 1.5rem;
            color: white;
        }

        .icon-circle-bg {
            background-color: rgba(255, 255, 255, 0.2);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        /* Upload Zone Styling */
        .upload-container {
            border: 2px dashed #cbd5e1;
            /* slate-300 */
            border-radius: 1rem;
            background-color: #f8fafc;
            /* slate-50 */
            padding: 3rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        /* Hover Effect Merah */
        .upload-container:hover {
            border-color: #ef4444;
            background-color: #fef2f2;
            /* red-50 */
        }

        .upload-icon {
            font-size: 3rem;
            color: #94a3b8;
            /* slate-400 */
            margin-bottom: 1rem;
            transition: color 0.3s;
        }

        .upload-container:hover .upload-icon {
            color: #ef4444;
        }

        /* File Input Hidden Overlay */
        .file-input-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .btn-gradient {
            background: linear-gradient(to right, #3b82f6, #2563eb);
            border: none;
            color: white !important;
            border-radius: 0.5rem;
            padding: 0.75rem 2.5rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            transition: all 0.2s;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(239, 68, 68, 0.3);
            filter: brightness(110%);
        }

        .file-list {
            margin-top: 1rem;
            text-align: left;
            display: none;
        }

        .file-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #475569;
            display: flex;
            align-items: center;
        }

        .file-item i {
            margin-right: 0.75rem;
            color: #ef4444;
        }
    </style>

    <div class="container-fluid pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card card-modern">
                    {{-- Header --}}
                    <div class="header-gradient d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-bg">
                                <i class="fas fa-shipping-fast text-white fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold m-0 text-white" style="font-size: 1.1rem;">Upload Dokumen</h5>
                                <p class="m-0 text-white-50 small">Ekspedisi</p>
                            </div>
                        </div>

                        <button type="button"
                            class="btn btn-sm btn-white bg-white bg-opacity-20 text-white border-0 shadow-none"
                            data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        {{-- Info Box (Merah Muda agar senada) --}}
                        <div class="alert alert-light border shadow-sm mb-4 d-flex align-items-start"
                            style="border-radius: 0.5rem; border-left: 4px solid #ef4444 !important; background-color: #fff1f2;">
                            <i class="fas fa-info-circle text-danger mt-1 mr-3 fa-lg"></i>
                            <div>
                                <h6 class="font-weight-bold text-dark mb-1">Informasi Upload</h6>
                                <p class="mb-0 text-muted small">Silakan unggah file PDF Ekspedisi Anda. Anda dapat memilih
                                    <strong>lebih dari satu file</strong> sekaligus.</p>
                            </div>
                        </div>

                        {{-- Form Upload --}}
                        <form action="{{ route('ekspedisi.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Custom Upload Zone --}}
                            <div class="form-group mb-4">
                                <div class="upload-container">
                                    <input type="file" name="file[]" id="fileInput" accept=".pdf" multiple required
                                        class="file-input-overlay">

                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <h6 class="font-weight-bold text-dark">Klik atau Seret File ke Sini</h6>
                                    <p class="text-muted small mb-0">Format yang didukung: <strong>.PDF</strong></p>
                                </div>

                                {{-- Area untuk menampilkan nama file yang dipilih --}}
                                <div class="file-list mt-3" id="fileListContainer">
                                    <small class="text-muted font-weight-bold mb-2 d-block">File Terpilih:</small>
                                    <div id="fileList"></div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-gradient">
                                    <i class="fas fa-upload mr-2"></i> Upload File Ekspedisi
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Script sederhana untuk menampilkan nama file yang dipilih
        document.getElementById('fileInput').addEventListener('change', function (e) {
            var fileList = document.getElementById('fileList');
            var container = document.getElementById('fileListContainer');

            fileList.innerHTML = ''; // Reset list

            if (this.files && this.files.length > 0) {
                container.style.display = 'block';

                for (var i = 0; i < this.files.length; i++) {
                    var file = this.files[i];
                    var div = document.createElement('div');
                    div.className = 'file-item';
                    // Menambahkan ikon PDF dan ukuran file
                    div.innerHTML = '<i class="fas fa-file-pdf"></i> ' + file.name + ' <span class="ml-auto text-muted small">' + (file.size / 1024).toFixed(1) + ' KB</span>';
                    fileList.appendChild(div);
                }
            } else {
                container.style.display = 'none';
            }
        });
    </script>
@endpush