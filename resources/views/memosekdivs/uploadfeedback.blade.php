@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('memosekdivs.index') }}">List Memo Sekdiv</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('memosekdivs.show', ['id' => $document->id]) }}">{{ $document->documentnumber }}</a></li>
                    <li class="breadcrumb-item active">Upload Feedback</li>
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

            {{-- Tampilkan Error Validasi --}}
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Form Upload Feedback --}}
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Unggah Feedback</h3>
                </div>
                <div class="card-body">
                    <form id="uploadForm" action="{{ route('memosekdivs.allfeedback', ['id' => $document->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="isread">Apakah Anda sudah melakukan review atas dokumen approval?</label>
                            <select id="isread" name="isread" class="form-control" required>
                                <option value=1>Sudah</option>
                                <option value=0>Belum</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="reviewresult">Hasil review atas dokumen approval tersebut:</label>
                            <select id="reviewresult" name="reviewresult" class="form-control" required>
                                <option value="Ya, dapat diterima">Ya, dapat diterima</option>
                                <option value="Ya, dapat diterima dengan catatan">Ya, dapat diterima dengan catatan</option>
                                <option value="Ada catatan">Ada catatan</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="comment">Komentar (opsional):</label>
                            <textarea class="form-control" id="comment" name="comment" rows="5"></textarea>
                        </div>

                        @if (config('app.url') === 'https://inka.goovicess.com')
                        <div class="form-group">
                            <label for="filecount">Jumlah File (sementara kosong):</label>
                            <input type="number" id="filecount" name="filecount" class="form-control" min="0" max="100" value="0">
                        </div>
                        @else
                        <div class="form-group">
                            <label for="file">Upload File:</label>
                            <input type="file" name="file[]" id="file" class="form-control-file" multiple>
                        </div>
                        @endif

                        {{-- Hidden Inputs --}}
                        <input type="hidden" name="aksi" value="uploaddocument">
                        <input type="hidden" name="rule" value="{{ auth()->user()->rule }}">
                        <input type="hidden" name="picrule" value="{{ auth()->user()->rule }}">
                        <input type="hidden" name="author" value="{{ auth()->user()->name }}">
                        <input type="hidden" name="time" value="">
                        <input type="hidden" name="level" value="{{ auth()->user()->rule }}">
                        <input type="hidden" name="condition1" value="draft">
                        <input type="hidden" name="condition2" value="feedback">

                        <div>
                            <button type="button" onclick="confirmUpload()" class="btn btn-success">Upload</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .file-input-group {
        margin-bottom: 10px;
    }

    .remove-file-btn {
        cursor: pointer;
        color: red;
        margin-left: 10px;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    function confirmUpload() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Anda akan mengunggah berkas ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, unggah!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('uploadForm').submit();
            }
        });
    }
</script>
@endpush