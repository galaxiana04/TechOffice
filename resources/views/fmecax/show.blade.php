@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left shadow-sm rounded">
                        <li class="breadcrumb-item"><a href="{{ route('fmeca.index') }}"
                                class="text-primary font-weight-bold">Informasi Critical Part Dashboard</a></li>
                        <li class="breadcrumb-item active">Detail Critical Part</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="card card-danger card-outline shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title text-bold text-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Detail Critical Part Dashboard
                <span class="badge badge-danger ml-2">Live</span>
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="card card-outline card-danger mt-3 shadow-sm">
                <div class="card-header bg-light border-bottom border-danger">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <td rowspan="4" class="text-center align-middle" style="width: 25%">
                                    <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo" class="p-2"
                                        style="max-width: 200px">
                                </td>
                                <td rowspan="4" class="text-center align-middle" style="width: 50%">
                                    <h2 class="text-uppercase font-weight-bold text-danger">Detail Critical Part</h2>
                                </td>
                                <td class="p-2"><strong class="text-muted">Project:</strong> <span id="selectedProject"
                                        class="text-dark font-weight-bold">{{ $fmeca_identity->projectType->title }}</span>
                                </td>

                            </tr>
                            <tr>
                                <td class="p-2">
                                    <strong class="text-muted">Tanggal:</strong>
                                    <span class="text-primary">{{ $fmeca_identity->created_at }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">
                                    <strong class="text-muted">Total Issues:</strong>
                                    <span id="totalIssues" class="badge badge-danger shadow"
                                        style="font-size: 1.2rem;">{{ $fmeca_identity->fmecas->count() }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">
                                    <strong class="text-muted">Safety:</strong>
                                    <span id="safetyIssues" class="badge badge-warning shadow"
                                        style="font-size: 1.1rem;">{{ $fmeca_identity->fmecas->where('issafetyorisreliability', 'safety')->count() }}</span>
                                    /
                                    <strong class="text-muted">Reliability:</strong>
                                    <span id="reliabilityIssues" class="badge badge-success shadow"
                                        style="font-size: 1.1rem;">{{ $fmeca_identity->fmecas->where('issafetyorisreliability', 'reliability')->count() }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-body">
                    @if (!$file)
                        <div class="alert alert-warning text-center" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i> Tidak ada file terkait untuk FMECA ini.
                        </div>
                    @else
                        @include('fmeca.fileinfo', ['file' => $file])
                    @endif

                    <div class="table-responsive mt-4">
                        <table class="table table-hover table-striped table-bordered shadow-sm" id="fmecaTable">
                            <thead class="bg-danger text-white text-center">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 15%">Project</th>
                                    <th style="width: 15%">Subsystem</th>
                                    <th style="width: 25%">Issue</th>
                                    <th style="width: 10%">Type</th>
                                    <th style="width: 15%">Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($fmeca_identity->fmecas as $index => $item)
                                    <tr>
                                        <td class="text-center align-middle">{{ $index + 1 }}</td>
                                        <td class="align-middle">{{ $item->projectType->title }}</td>
                                        <td class="align-middle">{{ Str::title($item->subsystemname) }}</td>
                                        <td class="align-middle">{{ Str::title($item->notifvalue) }}</td>
                                        <td class="align-middle text-center">
                                            <span
                                                class="badge 
                                                {{ $item->issafetyorisreliability === 'safety' ? 'badge-warning' : 'badge-success' }}">
                                                {{ Str::title($item->issafetyorisreliability) }}
                                            </span>
                                        </td>
                                        <td class="align-middle">{{ $item->created_at->toDateTimeString() }}</td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-info-circle mr-2"></i> Tidak ada data
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
