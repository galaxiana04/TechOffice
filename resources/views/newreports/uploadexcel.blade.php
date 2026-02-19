@extends('layouts.universal')

@php
    // --- DATA PROCESSING ---
    $categoryprojectbaru = json_decode($categoryproject ?? '[]', true);
    $rawListProject = $categoryprojectbaru[0] ?? '[]';
    $listproject = json_decode(trim($rawListProject, '"'), true) ?? [];

    $categoryUnitRaw = json_decode($unit_for_progres_dokumen ?? '[]', true);
    $rawUnit = $categoryUnitRaw[0] ?? '[]';
    $unitforprogresdokumen = json_decode(trim($rawUnit, '"'), true) ?? [];

    $useronly = auth()->user();
@endphp

@section('container2')
    <div class="content-header py-3 py-md-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold header-title" style="font-size: 1.8rem;">Upload Data Excel
                    </h1>
                    <p class="text-muted small mb-0">Impor data laporan, rencana, atau update link via file Excel</p>
                </div>
                <div class="col-sm-6 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('newreports.index') }}" class="text-muted hover-primary transition-color">
                                <i class="fas fa-home mr-1"></i> List Unit & Project
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-danger font-weight-bold">Upload Excel</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <style>
        /* --- THEME UTILITIES --- */
        :root {
            --primary-red-start: #ef4444;
            --primary-red-end: #b91c1c;
            --primary-blue-start: #3b82f6;
            --primary-blue-end: #2563eb;
            --soft-red-bg: #fff1f2;
            --soft-red-border: #fecaca;
        }

        .transition-color {
            transition: color 0.3s ease;
        }

        .hover-primary:hover {
            color: var(--primary-blue-end) !important;
        }

        /* --- CARD MODERN --- */
        .card-modern {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .header-gradient {
            background: linear-gradient(135deg, var(--primary-red-start) 0%, var(--primary-red-end) 100%);
            padding: 1.5rem;
            color: white;
        }

        .icon-circle-bg {
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        /* --- INFO BOX --- */
        .info-box-download {
            background: linear-gradient(to right, #f0fdf4, #ffffff);
            border-left: 5px solid #10b981;
            padding: 1.25rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #d1fae5;
        }

        /* --- FORM ELEMENTS --- */
        .form-section {
            background-color: var(--soft-red-bg);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--soft-red-border);
        }

        .form-control-modern {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.5rem 0.8rem;
            height: 42px;
            font-size: 0.875rem;
            width: 100%;
            background-color: #fff;
            color: #374151;
            transition: all 0.2s;
        }

        .form-control-modern:focus {
            border-color: var(--primary-red-start);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
            outline: none;
        }

        /* --- BUTTONS --- */
        .btn-gradient-blue {
            background: linear-gradient(to right, var(--primary-blue-start), var(--primary-blue-end));
            border: none;
            color: white !important;
            border-radius: 0.5rem;
            padding: 0.6rem 2rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.25);
            transition: all 0.3s ease;
        }

        .btn-gradient-blue:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        }

        .btn-gradient-blue:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }

        /* Custom File Input */
        .custom-file-label {
            border-radius: 0.5rem;
            height: 42px;
            padding: 0.5rem 0.8rem;
            display: flex;
            align-items: center;
        }

        .custom-file-label::after {
            height: 40px;
            padding: 0.5rem 1rem;
            border-radius: 0 0.5rem 0.5rem 0;
            background-color: #fce7f3;
            color: #be185d;
            content: "Browse";
            display: flex;
            align-items: center;
        }

        /* --- RESPONSIVE --- */
        @media (max-width: 768px) {
            .info-box-download {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .info-box-download .btn {
                width: 100%;
            }
        }
    </style>

    <div class="container-fluid pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="card card-modern">
                    {{-- Header Gradient --}}
                    <div class="header-gradient d-flex align-items-center">
                        <div class="icon-circle-bg">
                            <i class="fas fa-file-excel text-white"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold m-0 text-white">Formulir Upload Progres Dokumen</h5>
                            <p class="m-0 text-white-50 small">Pastikan format file sesuai sebelum diunggah</p>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">

                        {{-- Section: Download Format --}}
                        <div class="info-box-download">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-1">
                                    <i class="fas fa-file-download text-success mr-2"></i>Template Excel
                                </h6>
                                <p class="text-muted small mb-0">Gunakan template terbaru untuk menghindari kesalahan
                                    format.</p>
                            </div>
                            <a href="https://drive.google.com/drive/folders/16k6AIIdUc5LYC78RNe-A1i_JkslHEL9d?usp=sharing"
                                class="btn btn-success btn-sm font-weight-bold shadow-sm rounded-pill px-4" target="_blank">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>

                        {{-- Section: Form --}}
                        <div class="form-section">
                            <h6 class="text-dark font-weight-bold mb-4 d-flex align-items-center border-bottom pb-2"
                                style="border-color: #fecaca !important;">
                                <i class="fas fa-cloud-upload-alt text-danger mr-2"></i> Detail & File
                            </h6>

                            <form id="uploadForm" action="{{ route('newprogressreports.updateexcel') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    {{-- Jenis Upload --}}
                                    <div class="col-12 mb-4">
                                        <label for="jenisupload" class="small text-danger font-weight-bold text-uppercase">
                                            Jenis Upload <span class="text-danger">*</span>
                                        </label>
                                        <select name="jenisupload" id="jenisupload" class="form-control form-control-modern"
                                            required>
                                            <option value="formatprogress">Format_Progress</option>
                                            <option value="formatprogresskhusus">Format_Progress Khusus</option>
                                            <option value="formatrencana">Format_Rencana</option>
                                            <option value="formatupdatelink">Format Update Link Vault</option>

                                            @if (isset($useronly) && $useronly->rule == 'superuser')
                                                <option value="format">Format_Dasar</option>
                                                <option value="Treediagram">Format_Treediagram</option>
                                                <option value="formatprogressjamketersediaan">Format_Progress_Jam_Ketersediaan
                                                </option>
                                            @endif
                                        </select>
                                    </div>

                                    {{-- Dynamic Field: Unit --}}
                                    <div class="col-md-6 mb-4" id="unitField" style="display: none;">
                                        <label for="progressreportname"
                                            class="small text-danger font-weight-bold text-uppercase">Unit</label>
                                        <select name="progressreportname" id="progressreportname"
                                            class="form-control form-control-modern">
                                            @foreach ($unitforprogresdokumen as $memberunit)
                                                <option value="{{ $memberunit }}">{{ $memberunit }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Dynamic Field: Project Type --}}
                                    <div class="col-md-6 mb-4" id="projectTypeField" style="display: none;">
                                        <label for="proyek_type"
                                            class="small text-danger font-weight-bold text-uppercase">Tipe Project</label>
                                        <select name="proyek_type" id="proyek_type"
                                            class="form-control form-control-modern">
                                            @foreach ($listproject as $memberproject)
                                                <option value="{{ $memberproject }}">{{ $memberproject }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- File Upload --}}
                                    <div class="col-12 mb-2">
                                        <label for="file" class="small text-danger font-weight-bold text-uppercase">
                                            Pilih File (.xlsx, .xls) <span class="text-danger">*</span>
                                        </label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="file" name="file"
                                                accept=".xlsx, .xls" required>
                                            <label class="custom-file-label text-truncate" for="file">Pilih file
                                                excel...</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right border-top pt-4 mt-3" style="border-color: #fecaca !important;">
                                    <button type="submit" class="btn btn-gradient-blue" id="submitBtn">
                                        <i class="fas fa-paper-plane mr-2"></i> Unggah
                                    </button>
                                </div>
                            </form>
                        </div>
                        {{-- Keterangan Section --}}
                        <div class="mt-4 pl-2">
                            <h6 class="font-weight-bold text-dark mb-3">
                                <i class="fas fa-info-circle text-info mr-1"></i> Keterangan Jenis Upload
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-3">
                                            <strong class="text-primary d-block">Format Progress</strong>
                                            <small class="text-muted">Upload data progres dokumen biasa.</small>
                                        </li>
                                        <li class="mb-3">
                                            <strong class="text-primary d-block">Format Progress Khusus</strong>
                                            <small class="text-muted">Update level, deadline, dan data khusus
                                                lainnya.</small>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li class="mb-3">
                                            <strong class="text-primary d-block">Format Rencana</strong>
                                            <small class="text-muted">Upload rencana dokumen baru
                                                (planning).</small>
                                        </li>
                                        <li class="mb-3">
                                            <strong class="text-primary d-block">Format Update Link Vault</strong>
                                            <small class="text-muted">Hanya untuk memperbarui tautan file di Vault
                                                (PDF/DWG).</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Pastikan jQuery sudah dimuat --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

    <script>
        $(document).ready(function () {
            // 1. Inisialisasi Custom File Input
            bsCustomFileInput.init();

            const jenisUploadSelect = $('#jenisupload');
            const unitField = $('#unitField');
            const projectTypeField = $('#projectTypeField');
            const unitInput = $('#progressreportname');
            const projectInput = $('#proyek_type');

            // 2. Logic Toggle Fields
            function toggleFields() {
                const val = jenisUploadSelect.val();
                const hideOptions = [
                    'formatprogress',
                    'formatrencana',
                    'formatprogresskhusus',
                    'formatupdatelink',
                    'formatprogressjamketersediaan'
                ];

                if (hideOptions.includes(val)) {
                    unitField.hide();
                    projectTypeField.hide();
                    unitInput.prop('required', false);
                    projectInput.prop('required', false);
                } else {
                    unitField.show();
                    projectTypeField.show();
                    unitInput.prop('required', true);
                    projectInput.prop('required', true);
                }
            }

            // Jalankan saat load dan change
            toggleFields();
            jenisUploadSelect.on('change', toggleFields);

            // 3. Logic AJAX Submit
            $('#uploadForm').on('submit', function (e) {
                e.preventDefault(); // Mencegah reload halaman

                let form = this;
                let btn = $('#submitBtn');
                let originalText = btn.html();

                // Validasi HTML5 sederhana
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Unggah',
                    text: 'Pastikan file excel Anda sesuai dengan format yang dipilih.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Unggah',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {

                        // Persiapkan Data Form (termasuk file)
                        let formData = new FormData(form);

                        $.ajax({
                            url: $(form).attr('action'), // Ambil URL dari form action
                            type: 'POST',
                            data: formData,
                            contentType: false, // Wajib false untuk file upload
                            processData: false, // Wajib false untuk file upload
                            beforeSend: function () {
                                // Disable tombol dan tampilkan loading
                                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Mengunggah...');
                                Swal.fire({
                                    title: 'Sedang Mengunggah...',
                                    text: 'Mohon jangan tutup halaman ini.',
                                    icon: 'info',
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    willOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function (response) {
                                // Jika Berhasil (HTTP 200)
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.success || 'Data berhasil diunggah.', // Sesuaikan key JSON dari controller
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    form.reset(); // Reset Form
                                    $('.custom-file-label').html('Pilih file excel...'); // Reset label file
                                    toggleFields(); // Reset tampilan field
                                });
                            },
                            error: function (xhr) {
                                // Jika Gagal (HTTP 4xx/5xx)
                                let errorMessage = 'Terjadi kesalahan saat mengunggah.';

                                // Coba ambil pesan error dari validasi Laravel
                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    } else if (xhr.responseJSON.error) {
                                        errorMessage = xhr.responseJSON.error;
                                    }
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage
                                });
                            },
                            complete: function () {
                                // Kembalikan tombol ke kondisi semula
                                btn.prop('disabled', false).html(originalText);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush