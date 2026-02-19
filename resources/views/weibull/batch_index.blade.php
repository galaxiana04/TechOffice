@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item">
                            <a href="{{ route('weibull.dashboard') }}">Weibull Analysis</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Weibull Failure Rate Analysis
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="card card-danger card-outline shadow-lg animated-card">
        <div class="card-header bg-gradient-danger">
            <div class="card-tools">
                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <h3 class="card-title text-bold text-white">
                <i class="fas fa-chart-line mr-2"></i>
                Page Dashboard Weibull Analysis
            </h3>
        </div>



        {{-- TABLE --}}

        <div class="table-responsive px-3 pb-3">

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Source</th>
                        <th>Uploaded By</th>
                        <th>Uploaded At</th>
                        <th class="text-center">Jumlah Record</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $i => $batch)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $batch->source_file }}</td>
                            <td>{{ $batch->user->name ?? '-' }}</td>
                            <td>{{ $batch->uploaded_at }}</td>
                            <td class="text-center">{{ $batch->failure_records_count }}</td>
                            <td class="text-center">
                                <form action="{{ route('weibull.batch.destroy', $batch) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus seluruh data dalam batch ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Tidak ada batch import
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
