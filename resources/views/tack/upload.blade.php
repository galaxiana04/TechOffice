@extends('layouts.universal')

@php  
    $useronly = auth()->user();
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="">List Unit & Project</a></li>
                        <li class="breadcrumb-item"><a href="">Upload Excel</a></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('container3')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">


                <div class="error-container">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="card card-primary">
                    <div class="card-header">Unggah File Excel</div>

                    <div class="card-body">
                        <form id="uploadForm" action="{{ route('tack.updateexcel') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="jenisupload">Jenis Upload:</label>
                                <select name="jenisupload" id="jenisupload" class="form-control" required>
                                    <option value="formatprogress">Format_Progress</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="file">Pilih File Excel (.xlsx, .xls):</label>
                                <input type="file" class="form-control-file" id="file" name="file" accept=".xlsx, .xls">
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Unggah</button>
                        </form>
                    </div>


                    <!-- /.card-body -->
                </div>
                <!-- /.card -->



            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Include SweetAlert script -->
    <!-- Sweetalert2 (include theme bootstrap) -->
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('uploadForm');
            const jenisUploadSelect = document.getElementById('jenisupload');

            function toggleFields() {
                if (jenisUploadSelect.value === 'formatprogress' || jenisUploadSelect.value === 'formatrencana' || jenisUploadSelect.value === 'formatprogresskhusus') {
                    unitField.style.display = 'none';
                    projectTypeField.style.display = 'none';
                } else {
                    unitField.style.display = 'block';
                    projectTypeField.style.display = 'block';
                }
            }

            // Initial call to set the correct visibility
            toggleFields();

            // Add event listener for changes
            jenisUploadSelect.addEventListener('change', toggleFields);

            form.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent the form from submitting normally

                // You can add additional validation logic here if needed

                // Show SweetAlert with confirmation message
                Swal.fire({
                    title: 'Yakin ingin unggah file?',
                    text: 'Pilih "Ya" untuk mengunggah file.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'File Excel Berhasil Diunggah!',
                            text: 'Tindakan selanjutnya di sini...',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        form.submit(); // Submit the form
                    }
                });
            });
        });
    </script>
@endpush