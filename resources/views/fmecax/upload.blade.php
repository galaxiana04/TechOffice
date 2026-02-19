@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-left">
                        <li class="breadcrumb-item"><a href="{{ route('newreports.index') }}">List Unit & Project</a></li>
                        <li class="breadcrumb-item active">Upload FMECA</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-sm-12">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Upload FMECA Excel File</h3>
                    </div>

                    <div class="card-body">
                        <form id="uploadForm" action="{{ route('fmeca.upload.excel') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="excel_file">Excel File (.xlsx, .xls)<span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="excel_file" name="excel_file"
                                        accept=".xlsx,.xls" required>
                                    <label class="custom-file-label" for="excel_file">Choose file</label>
                                </div>
                                <small class="form-text text-muted">
                                    Please upload an Excel file in .xlsx or .xls format containing FMECA data.
                                </small>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-upload mr-1"></i> Upload File
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
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('uploadForm');
            const fileInput = document.getElementById('excel_file');

            // Update file input label
            fileInput.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name || 'Choose file';
                const label = e.target.nextElementSibling;
                label.innerText = fileName;
            });

            form.addEventListener('submit', function(event) {
                event.preventDefault();

                if (!fileInput.files.length) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please select an Excel file to upload!'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Confirm Upload',
                    text: 'Are you sure you want to upload this Excel file?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Upload!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const submitBtn = document.getElementById('submitBtn');
                        submitBtn.disabled = true;
                        submitBtn.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-1"></i> Uploading...';
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
