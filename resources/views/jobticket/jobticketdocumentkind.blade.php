@extends('layouts.universal')

@php
    use Carbon\Carbon; // Import Carbon class
@endphp


@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="">Job Ticket</a></li>
                        <li class="breadcrumb-item active text-bold"><a href="">Jenis Dokumen</a></li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('container3')
    <div class="container mt-2">
        <div class="card card-danger card-outline mt-2">
            <!-- Display success message -->
            @if (session('success'))
                <div class="alert alert-success mt-2">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card-header mt-1">
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <h3 class="card-title text-bold">Jenis Dokumen <span class="badge badge-info ml-1"></span></h3>
            </div>

            <div class="card-body mt-1">
                <!-- Form to add new Newprogressreport Document Kind -->




                <!-- Form to add new Jobticket Document Kind -->
                <form id="documentKindForm" action="{{ route('jobticket.jobticket-document-kindstore') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name">
                        <div class="invalid-feedback" id="error-name"></div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                        <div class="invalid-feedback" id="error-description"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>


                <hr class="mt-2 mb-2">

                <!-- Display the list of Newprogressreport Document Kinds in a table -->
                <h2 class="h5 mt-2">List Jenis Dokumen Jobticket</h2>
                <table class="table table-bordered table-sm mt-2">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Jenis Dokumen</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documentKinds as $index => $documentKind)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $documentKind->name }}</td>
                                <td>{{ $documentKind->description ?? 'Tidak ada deskripsi' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $('#documentKindForm').submit(function(e) {
            e.preventDefault(); // Mencegah submit default (yang reload)

            let form = $(this);
            let url = form.attr('action');
            let data = form.serialize();

            // Reset error state
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').text('');

            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Reset form
                    form[0].reset();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        // Tampilkan error validasi di bawah field
                        $.each(errors, function(field, message) {
                            $('#' + field).addClass('is-invalid');
                            $('#error-' + field).text(message[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menyimpan data. Silakan coba lagi.'
                        });
                    }
                }
            });
        });
    </script>
@endpush
