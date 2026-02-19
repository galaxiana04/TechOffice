@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('new-memo.index') }}">List Memo Sekdiv</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">

            <div class="card card-primary">
                <div class="card-header">
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <h3 class="card-title text-bold">Page Monitoring Memo Sekdiv <span class="badge badge-info ml-1"></span></h3>
                </div>
                <div class="card-body">
                    <div class="container">
                        <h2>Buat Memo Sekdiv Baru</h2>

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('memosekdivs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Nama Dokumen</label>
                                <input type="text" name="documentname" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nomor Dokumen</label>
                                <input type="text" name="documentnumber" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="documentkind" class="form-label">Jenis Dokumen</label>
                                <select name="documentkind" id="documentkind" class="form-control" required>
                                    <option value="" disabled selected>Pilih jenis dokumen</option>
                                    <option value="Memo" {{ old('documentkind') == 'Memo' ? 'selected' : '' }}>Memo</option>
                                    <option value="Surat Dinas" {{ old('documentkind') == 'Surat Dinas' ? 'selected' : '' }}>Surat Dinas</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tipe Proyek</label>
                                <select name="project_type_id" class="form-control">
                                    <option value="">Pilih Tipe Proyek</option>
                                    @foreach ($projectTypes as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="mb-3">
                                <label class="form-label d-block">Pilih SM Decisions</label>
                                <div class="row">
                                    @foreach ($smDecisions as $sm)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input type="checkbox" name="smdecisions[]" value="{{ $sm }}" class="form-check-input" id="sm_{{ Str::slug($sm) }}">
                                            <label class="form-check-label" for="sm_{{ Str::slug($sm) }}">{{ $sm }}</label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>




                            <div class="mb-3">
                                <label class="form-label">File Dokumen</label>
                                <input type="file" name="file[]" class="form-control" multiple>
                                <small class="text-muted">Bisa mengunggah beberapa file sekaligus</small>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Memo</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
@endpush