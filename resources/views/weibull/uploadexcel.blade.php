{{-- resources/views/weibull/uploadexcel.blade.php --}}

@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('weibull.dashboard') }}">Weibull Analysis</a></li>
                        <li class="breadcrumb-item active">Upload Excel Data</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <!-- Pesan Sukses / Error -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="icon fas fa-check"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">×</button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="icon fas fa-ban"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">×</button>
                    </div>
                @endif

                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Upload File Excel</h3>
                    </div>

                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Upload file Excel berisi data kegagalan komponen untuk dianalisis Weibull otomatis.
                        </p>

                        <!-- Tabel Format -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Kolom</th>
                                        <th>Isi</th>
                                        <th>Contoh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>A</td>
                                        <td>Nama Project</td>
                                        <td>KRL INKA 2023</td>
                                    </tr>
                                    <tr>
                                        <td>B</td>
                                        <td>L1</td>
                                        <td>AC System</td>
                                    </tr>
                                    <tr>
                                        <td>C</td>
                                        <td>L2</td>
                                        <td>AC Control</td>
                                    </tr>
                                    <tr>
                                        <td>D</td>
                                        <td>L3</td>
                                        <td>PLC</td>
                                    </tr>
                                    <tr>
                                        <td>E</td>
                                        <td>L4</td>
                                        <td>Instalasi kabel
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>F</td>
                                        <td>Tanggal Mulai Operasi</td>
                                        <td>01/01/2025</td>
                                    </tr>
                                    <tr>
                                        <td>G</td>
                                        <td>Tanggal Kegagalan</td>
                                        <td>15/06/2025</td>
                                    </tr>
                                    <tr>
                                        <td>H</td>
                                        <td>Jam Kegagalan</td>
                                        <td>14:30</td>
                                    </tr>
                                    <tr>
                                        <td>I</td>
                                        <td>Is Repairable?</td>
                                        <td>1 untuk ya atau 0 untuk tidak</td>
                                    </tr>
                                    <tr>
                                        <td>J</td>
                                        <td>Jenis Service</td>
                                        <td>Penggantian Komponen
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>K</td>
                                        <td>Apakah komponen baru</td>
                                        <td>1 untuk ya atau 0 untuk tidak</td>
                                    </tr>
                                    <tr>
                                        <td>L</td>
                                        <td>Trainset</td>
                                        <td>TSA 1
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>M</td>
                                        <td>Train No</td>
                                        <td>K1 0 23 010
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>N</td>
                                        <td>Tipe Car</td>
                                        <td>K1
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>O</td>
                                        <td>Relation</td>
                                        <td>Argo Lawu
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>P</td>
                                        <td>Temuan</td>
                                        <td>K102310 AC tidak bisa auto .. setelah dichek PLC sebagian indikator tidak nya..
                                            kadang lap lip..sudah di chek di koneksinya..
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Q</td>
                                        <td>Solution</td>
                                        <td>Ganti inverter</td>
                                    </tr>
                                    <tr>
                                        <td>R</td>
                                        <td>Klasifikasi Penyebab</td>
                                        <td>Elektrik</td>
                                    </tr>
                                    <tr>
                                        <td>S</td>
                                        <td>Link Support Dokumen</td>
                                        <td>http://drive.inka.co.id/...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Form Upload -->
                        <form action="{{ route('weibull.updateexcel') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="jenisupload" value="formatprogress">

                            <div class="form-group">
                                <label>Pilih File Excel</label>
                                <div class="custom-file">
                                    <input type="file" name="file" class="custom-file-input" id="customFile"
                                        accept=".xlsx,.xls" required>
                                    <label class="custom-file-label" for="customFile">Pilih file...</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-upload mr-2"></i>
                                Upload & Proses Data
                            </button>
                        </form>
                    </div>

                    <div class="card-footer text-muted small">
                        Setelah upload berhasil, data akan langsung diproses dan analisis Weibull di dashboard akan otomatis
                        update.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('adminlte3/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
@endpush
