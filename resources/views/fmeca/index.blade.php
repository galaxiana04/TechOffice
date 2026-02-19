@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="#">FMECA Parts</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow mb-4">

                    <div class="card-header bg-danger text-white">
                        <h3 class="card-title fw-bold mb-0">FMECA Parts</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ url('fmeca/critical-items') }}" class="btn btn-primary btn-sm btn-block">Critical
                            Item</a>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Form Create FMECA Part --}}
                        <div class="mb-4">
                            <div class="card shadow border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="h6 mb-0">Create New FMECA Part</h3>
                                </div>
                                <div class="card-body p-4">
                                    <form method="POST" action="{{ route('fmeca.store') }}">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <input type="text" name="name" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                placeholder="Part Name" required>
                                            <label for="name">Part Name</label>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select name="fmeca_identity_id" id="fmeca_identity_id"
                                                class="form-control @error('fmeca_identity_id') is-invalid @enderror"
                                                required>
                                                <option value="">Select FMECA Identity</option>
                                                @foreach ($projectTypes as $projectType)
                                                    @foreach ($projectType->fmecaIdentities ?? [] as $identity)
                                                        <option value="{{ $identity->id }}">
                                                            {{ $projectType->title }} - {{ $identity->name }}
                                                        </option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                            <label for="fmeca_identity_id">FMECA Identity</label>
                                            @error('fmeca_identity_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary me-2">Save</button>
                                        <a href="{{ route('fmeca.index') }}" class="btn btn-secondary">Cancel</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- FMECA Part List --}}
                        <div class="mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-light">
                                    <h3 class="h6 mb-0">FMECA Part List</h3>
                                </div>
                                <div class="card-body p-4">
                                    <div class="form-floating mb-3">
                                        <select id="project_filter" class="form-control">
                                            <option value="">All Projects</option>
                                            @foreach ($projectTypes as $projectType)
                                                <option value="{{ $projectType->id }}">
                                                    {{ $projectType->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="project_filter">Filter by Project Type</label>
                                    </div>

                                    @if ($fmecaParts->isEmpty())
                                        <p class="text-muted mb-0">No FMECA Parts found.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Part Name</th>
                                                        <th>Project</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($fmecaParts as $part)
                                                        <tr>
                                                            <td>{{ $part->id }}</td>
                                                            <td>{{ $part->name }}</td>
                                                            <td>{{ $part->fmecaIdentity->projectType->title ?? 'N/A' }}
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('fmeca.items.view', $part->id) }}"
                                                                    class="btn btn-sm btn-primary">
                                                                    <i class="bi bi-eye"></i> View Items
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush
