@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="#">Memo Sekdiv</a></li>
                    <li class="breadcrumb-item"><a href="#">Edit Dokumen</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')
<div class="row justify-content-center">
    <div class="col-10">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Memo</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('memosekdivs.posteditdocument', ['id' => $document->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="documentname">Nama Memo:</label>
                        <textarea class="form-control" name="documentname" id="documentname" rows="5">{{ $document->documentname }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Project PIC:</label><br>

                        @php
                        $picArray = $document->project_pic ? json_decode($document->project_pic, true) : [];
                        @endphp

                        @if (empty($project_pic) || $yourauth->id == 1)
                        @foreach ($listpic as $pic)
                        @php $isChecked = in_array($pic, $picArray); @endphp
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="project_pic[]" value="{{ $pic }}" {{ $isChecked ? 'checked' : '' }}>
                            <label class="form-check-label">{{ $pic }}</label>
                        </div>
                        @endforeach
                        @else
                        @foreach ($picArray as $pic)
                        <span class="badge badge-info">{{ $pic }}</span>
                        @endforeach
                        @endif
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<!-- Fonts & Icons -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">

<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

<!-- Bootstrap & Theme -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/INKAICON.png') }}">

<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('adminlte3/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endpush

@push('scripts')
<!-- jQuery & Bootstrap -->
<script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

<!-- AdminLTE -->
<script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>

<!-- SweetAlert2 -->
<script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>

<!-- Moment.js & Chart.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.11.3/sorting/datetime-moment.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0-rc"></script>
@endpush