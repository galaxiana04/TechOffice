@extends('layouts.universal')

@section('container2')
    <div class="content-header bg-white border-bottom py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.5rem;">Upload Data Excel</h1>
                    <p class="text-muted mb-0 small">Formulir impor data via file Excel</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-muted"><i class="fas fa-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="#" class="text-muted">List Unit & Project</a></li>
                        <li class="breadcrumb-item active text-danger font-weight-bold">Upload Excel</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')

    <style>
        .icon-circle-bg {
            width: 45px;
            height: 45px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            transition: background-color 0.3s;
        }

        /* Efek hover opsional: jadi lebih putih saat disorot */
        .card-header:hover .icon-circle-bg {
            background-color: rgba(255, 255, 255, 0.3);
        }

        /* Custom Styles untuk Nuansa Modern */
        .card-modern {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        /* Section Download Hijau */
        .download-section {
            background-color: #f0fdf4;
            /* green-50 */
            border: 1px solid #bbf7d0;
            /* green-200 */
            border-radius: 0.5rem;
            padding: 1.25rem;
        }

        /* Section Form Pink */
        .form-section {
            background-color: #fef2f2;
            /* red-50 */
            border: 1px solid #fecaca;
            /* red-200 */
            border-radius: 0.5rem;
            padding: 1.5rem;
        }

        .label-bold-red {
            color: #b91c1c;
            /* red-700 */
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Custom File Input Override */
        .custom-file-label {
            border-radius: 0.375rem;
            border-color: #d1d5db;
        }

        .custom-file-label::after {
            background-color: #dc2626;
            color: white;
            content: "Browse";
        }

        /* Custom Blue Button Style */
        .btn-blue-solid {
            background-color: #2563eb;
            /* Blue-600 */
            border: 1px solid #2563eb;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
            /* Bayangan biru halus */
            transition: all 0.2s ease-in-out;
        }

        .btn-blue-solid:hover {
            background-color: #1d4ed8;
            /* Blue-700 (Lebih gelap saat hover) */
            border-color: #1d4ed8;
            transform: translateY(-1px);
            /* Efek naik sedikit saat hover */
            color: white;
        }
    </style>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">

                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm border-0 mb-4" style="border-left: 5px solid #dc2626;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-lg mr-2"></i>
                            <h6 class="font-weight-bold m-0">Terjadi Kesalahan</h6>
                        </div>
                        <hr class="my-2">
                        <ul class="mb-0 small pl-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card card-modern bg-white">
                    <div class="card-header bg-danger text-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-bg">
                                <i class="fas fa-file-excel fa-lg text-white"></i>
                            </div>

                            <div>
                                <h5 class="font-weight-bold mb-0" style="line-height: 1.2;">Formulir Upload</h5>
                                <small class="text-white-50" style="font-size: 0.85rem;">Silakan lengkapi form di bawah
                                    ini</small>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">

                        <div
                            class="download-section mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-center">
                            <div class="mb-3 mb-sm-0">
                                <h6 class="font-weight-bold text-success mb-1">
                                    <i class="fas fa-file-download mr-1"></i> Template Excel
                                </h6>
                                <p class="text-muted small mb-0">Unduh format terbaru agar data sesuai.</p>
                            </div>
                            <a href="https://drive.google.com/drive/folders/1qL-MQCbp67ndb8U_K0TLC1gmBSdLzisk?usp=sharing"
                                class="btn btn-success btn-sm font-weight-bold shadow-sm px-3" target="_blank">
                                <i class="fas fa-download mr-1"></i> Download Format
                            </a>
                        </div>

                        <div class="form-section">
                            <h6 class="font-weight-bold text-dark mb-3 border-bottom pb-2"
                                style="border-color: #fecaca !important;">
                                <i class="fas fa-upload text-danger mr-2"></i> Detail Upload
                            </h6>

                            <form id="uploadForm" action="{{ route('katalogkomat.excel') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label for="jenisupload" class="label-bold-red">Jenis Upload <span
                                            class="text-danger">*</span></label>
                                    <select name="jenisupload" id="jenisupload" class="form-control" required>
                                        <option value="" disabled selected>-- Pilih Jenis --</option>
                                        <option value="formatprogress">Format_Progress</option>
                                        <option value="formatrencana">Format_Rencana</option>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="file" class="label-bold-red">File Excel <span
                                            class="text-danger">*</span></label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file" name="file"
                                            accept=".xlsx, .xls" required>
                                        <label class="custom-file-label text-muted" for="file">Pilih file .xlsx /
                                            .xls...</label>
                                    </div>
                                    <small class="form-text text-muted">Pastikan ukuran file tidak melebihi batas yang
                                        ditentukan.</small>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-blue-solid font-weight-bold px-4 py-2"
                                        id="submitBtn">
                                        <i class="fas fa-paper-plane mr-2"></i> Unggah File
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Pastikan jQuery sudah dimuat di layout utama (layouts.universal). Jika belum, uncomment baris bawah ini --}}
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

    <script>
        $(document).ready(function () {
            // 1. Inisialisasi input file custom (agar nama file muncul saat dipilih)
            bsCustomFileInput.init();

            // 2. Event Listener saat Form di-Submit
            $('#uploadForm').on('submit', function (e) {
                
                // PENTING: Mencegah halaman reload / pindah ke halaman hitam JSON
                e.preventDefault();

                // Ambil elemen tombol untuk efek loading
                let btn = $('#submitBtn');
                let originalBtnText = btn.html(); // Simpan teks asli tombol

                // Buat objek data form (termasuk file)
                let formData = new FormData(this);

                // Tampilkan Loading (SweetAlert)
                Swal.fire({
                    title: 'Sedang Mengunggah...',
                    text: 'Mohon jangan tutup halaman ini.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Ubah status tombol jadi disabled
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...');

                // Kirim data via AJAX
                $.ajax({
                    url: $(this).attr('action'), // Mengambil URL dari atribut action form
                    type: 'POST',
                    data: formData,
                    contentType: false, // Wajib false untuk upload file
                    processData: false, // Wajib false untuk upload file
                    success: function (response) {
                        // Jika Berhasil
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.success || 'Data berhasil diimpor.', // Menangkap pesan dari JSON controller
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Reset form setelah sukses
                            $('#uploadForm')[0].reset();
                            // Reset label custom file input kembali ke default
                            $('.custom-file-label').html('Pilih file .xlsx / .xls...');
                        });
                    },
                    error: function (xhr) {
                        // Jika Gagal
                        let errorMessage = 'Terjadi kesalahan saat mengunggah.';
                        
                        // Cek apakah ada pesan error spesifik dari server (Validasi Laravel)
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        // Kembalikan tombol ke keadaan semula (baik sukses maupun gagal)
                        btn.prop('disabled', false);
                        btn.html(originalBtnText);
                    }
                });
            });
        });
    </script>
@endpush