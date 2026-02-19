@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <nav aria-label="breadcrumb" class="bg-white px-3 py-2 rounded shadow-sm">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('komatprocesshistory.index') }}"
                                    class="text-decoration-none">List KomRev</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><a
                                    href="{{ route('komatprocesshistory.show', [$document->id]) }}"
                                    class="text-decoration-none">KOMREV/{{ $document->komatProcess->komat_name }}/{{ $document->revision }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit KomRev</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Edit KomRev</h3>
                </div>

                <form action="{{ route('komatprocesshistory.updatePositions', $document->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <p class="card-text"><strong>Identitas Dokumen:</strong>
                            KOMREV/{{ $document->komatProcess->komat_name }}/{{ $document->revision }}</p>

                        @foreach ($document->komatHistReqs as $komatHistReq)
                            <div class="mb-4 p-3 rounded border border-secondary bg-light shadow-sm">
                                <h5 class="mb-3 text-primary fw-bold">
                                    <i class="bi bi-file-earmark-text me-2"></i>
                                    Komat Requirement: {{ $komatHistReq->komatRequirement->name }}
                                </h5>

                                <div class="row g-3">
                                    @foreach ($units as $unit)
                                        @php
                                            $isChecked = $komatHistReq->komatPositions
                                                ->where('unit_id', $unit->id)
                                                ->where('level', 'discussion')
                                                ->isNotEmpty();

                                        @endphp
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    id="unit_{{ $komatHistReq->id }}_{{ $unit->id }}"
                                                    name="positions[{{ $komatHistReq->id }}][]"
                                                    value="{{ $unit->id }}" {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="unit_{{ $komatHistReq->id }}_{{ $unit->id }}">
                                                    {{ $unit->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-success btn-lg px-4">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
