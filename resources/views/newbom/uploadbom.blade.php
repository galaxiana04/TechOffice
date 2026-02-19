@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 col-12 text-center text-sm-left mb-2 mb-sm-0">
                <h1 class="m-0 text-dark">
                    {{-- UBAH: text-primary menjadi text-danger --}}
                    <i class="fas fa-file-import mr-2 text-danger"></i>Import Data
                </h1>
            </div>
            <div class="col-sm-6 col-12">
                <ol class="breadcrumb float-sm-right justify-content-center justify-content-sm-end bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('newbom.index') }}" class="text-danger">List Unit & Project</a></li>
                    <li class="breadcrumb-item active">Upload Excel</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')

<style>
    /* Custom CSS */
   .upload-box {
        border: 2px dashed #dbe0e6;
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 30px 15px; 
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    
    @media (min-width: 768px) {
        .upload-box {
            padding: 50px 20px; 
        }
    }

    .upload-box:hover, .upload-box.dragover {
        border-color: #dc3545; 
        background-color: #fff5f5; 
        transform: translateY(-2px);
    }

    .upload-box input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    .upload-icon {
        font-size: 2.5rem;
        color: #6c757d;
        margin-bottom: 15px;
        transition: color 0.3s;
    }

    .upload-box:hover .upload-icon {
        color: #dc3545; 
    }

    .card-modern {
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border-radius: 15px;
    }

    .form-label-custom {
        font-weight: 600;
        color: #343a40;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .input-group-text {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
        border-right: 0;
        background-color: #f4f6f9;
    }
    
    .form-control {
        height: calc(2.25rem + 8px); 
        border-radius: 8px;
    }
    
    .form-control:focus {
        box-shadow: none;
        border-color: #dc3545; 
    }
</style>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10 col-md-12 col-12">

            @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 rounded-lg mb-4 fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
                    <div>
                        <strong>Terjadi Kesalahan!</strong>
                        <ul class="mb-0 pl-3 mt-1 small">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <div class="card card-modern bg-white">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <h4 class="font-weight-bold text-dark">Upload Data Excel</h4>
                        <p class="text-muted small">Lengkapi formulir di bawah ini untuk import data</p>
                    </div>

                    <div class="alert shadow-sm border-0 rounded-lg d-flex flex-column flex-md-row align-items-center justify-content-between mb-4 p-4"
                        style="background-color: #d1e7dd; color: #0f5132;">

                        <div class="d-flex align-items-center mb-3 mb-md-0 w-100">
                            <i class="fas fa-file-excel fa-2x mr-3 text-success" style="opacity: 0.8;"></i>
                            <div>
                                <h6 class="font-weight-bold mb-0">Butuh template format?</h6>
                                <small class="d-block" style="opacity: 0.9;">Wajib unduh template excel sebelum upload.</small>
                            </div>
                        </div>

                        <a href="https://drive.google.com/drive/folders/1qL-MQCbp67ndb8U_K0TLC1gmBSdLzisk?usp=sharing"
                            class="btn btn-light font-weight-bold rounded-pill px-4 shadow-sm w-100 w-md-auto text-nowrap"
                            style="color: #282C35;" 
                            target="_blank">
                            <i class="fas fa-download mr-1"></i> Download
                        </a>
                    </div>

                    <form id="uploadForm" action="{{ route('importnewbom.excel') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf

                        {{-- Upload Type --}}
                        <div class="form-group mb-4">
                            <label for="jenisupload" class="form-label-custom">Jenis Dokumen</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                                </div>
                                <select name="jenisupload" id="jenisupload" class="form-control custom-select" required>
                                    <option value="formatprogress">Format Progress</option>
                                    <option value="formatupdateprogress">Format Relasi Dokumen</option>
                                    <option value="formatrencana">Format Rencana</option>
                                </select>
                            </div>
                        </div>

                        {{-- Dynamic Content Wrapper --}}
                        <div id="formContent" class="p-4 bg-light rounded-lg mb-4" style="border: 1px solid #f1f3f5;">
                            <div class="d-flex align-items-center mb-3 border-bottom pb-2">
                                <i class="fas fa-info-circle text-muted mr-2"></i>
                                <h6 class="text-muted font-weight-bold text-uppercase small mb-0">Detail Proyek</h6>
                            </div>

                            <div class="form-group">
                                <label for="bomnumber" class="form-label-custom">Nomor BOM</label>
                                <input type="text" class="form-control" id="bomnumber" name="bomnumber"
                                    value="{{ old('bomnumber') }}" placeholder="Contoh: BOM-2023-001">
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="project_type_id" class="form-label-custom">Tipe Proyek</label>
                                        <select name="project_type_id" id="project_type_id"
                                            class="form-control custom-select">
                                            @foreach($projects as $project)
                                            <option value="{{$project->id}}">{{$project->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="unit" class="form-label-custom">Unit</label>
                                        <select name="unit" id="unit" class="form-control custom-select">
                                            @foreach($units as $unit)
                                            <option value="{{$unit}}">{{$unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div> 

                        {{-- File Upload Area --}}
                        <div class="form-group mb-4">
                            <label class="form-label-custom">File Excel</label>
                            <div class="upload-box" id="dropArea">
                                <input type="file" id="file" name="file" accept=".xlsx, .xls" required>
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <h6 class="font-weight-bold text-dark mb-1" id="fileName">Klik atau Tarik File</h6>
                                <p class="text-muted small mb-0">Format (.xlsx, .xls)</p>
                            </div>
                        </div>

                        {{-- UBAH: btn-primary menjadi btn-danger --}}
                        <button type="submit"
                            class="btn btn-danger btn-block btn-lg rounded-pill shadow-sm font-weight-bold py-3"
                            id="submitBtn">
                            <i class="fas fa-paper-plane mr-2"></i> Unggah Data
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('uploadForm');
            const jenisUpload = document.getElementById('jenisupload');
            const formContent = document.getElementById('formContent');
            const fileInput = document.getElementById('file');
            const fileNameDisplay = document.getElementById('fileName');
            const dropArea = document.getElementById('dropArea');

            // 1. File Upload Logic
            fileInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    let name = this.files[0].name;
                    if (name.length > 25) name = name.substring(0, 22) + '...';

                    fileNameDisplay.innerText = name;
                    fileNameDisplay.classList.remove('text-dark');
                    fileNameDisplay.classList.add('text-success'); 
                    dropArea.style.borderColor = '#28a745';
                    dropArea.style.backgroundColor = '#e8f5e9';
                } else {
                    fileNameDisplay.innerText = 'Klik atau Tarik File';
                    fileNameDisplay.classList.remove('text-success');
                    fileNameDisplay.classList.add('text-dark');
                    dropArea.style.borderColor = '#dbe0e6';
                    dropArea.style.backgroundColor = '#f8f9fa';
                }
            });

            dropArea.addEventListener('dragover', (e) => { dropArea.classList.add('dragover'); });
            dropArea.addEventListener('dragleave', (e) => { dropArea.classList.remove('dragover'); });

            // 2. Toggle Form Fields
            function toggleFormFields() {
                if (jenisUpload.value === 'formatprogress' || jenisUpload.value === 'formatupdateprogress') {
                    formContent.style.display = 'none';
                    document.getElementById('bomnumber').removeAttribute('required');
                    document.getElementById('project_type_id').removeAttribute('required');
                    document.getElementById('unit').removeAttribute('required');
                } else {
                    formContent.style.display = 'block';
                }
            }

            toggleFormFields();
            jenisUpload.addEventListener('change', toggleFormFields);

            // 3. SweetAlert
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Pastikan file yang diunggah sudah benar.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545', 
                    cancelButtonColor: '#6c757d', 
                    confirmButtonText: 'Ya, Proses!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Mengunggah...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading() }
                        });
                        form.submit();
                    }
                });
            });
        });
</script>
@endpush