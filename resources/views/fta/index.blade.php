@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <ol class="breadcrumb bg-white px-3 py-2 float-left rounded shadow-sm">
                        <li class="breadcrumb-item"><a href="{{ route('fta.index') }}"
                                class="text-decoration-none text-primary">FTA Identities</a></li>
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
                        <h3 class="card-title fw-bold mb-0">FTA Identities</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Form Create FTA Identity --}}
                        <div class="mb-4">
                            <div class="card shadow border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="h6 mb-0">Create New FTA Identity</h3>
                                </div>
                                <div class="card-body p-4">
                                    <form method="POST" action="{{ route('fta.store') }}">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <input type="text" name="componentname" id="componentname"
                                                class="form-control @error('componentname') is-invalid @enderror"
                                                placeholder="Component Name" required>
                                            <label for="componentname">Component Name</label>
                                            @error('componentname')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select name="proyek_type_id" id="proyek_type_id"
                                                class="form-control @error('proyek_type_id') is-invalid @enderror">
                                                <option value="">Select Project Type</option>
                                                @foreach ($projectTypes as $projectType)
                                                    <option value="{{ $projectType->id }}"
                                                        {{ old('proyek_type_id') == $projectType->id ? 'selected' : '' }}>
                                                        {{ $projectType->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label for="proyek_type_id">Project Type</label>
                                            @error('proyek_type_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input type="url" name="diagram_url" id="diagram_url"
                                                class="form-control @error('diagram_url') is-invalid @enderror"
                                                placeholder="Diagram URL" value="{{ old('diagram_url') }}">
                                            <label for="diagram_url">Diagram URL (optional)</label>
                                            @error('diagram_url')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-primary me-2">Save</button>
                                        <a href="{{ route('fta.index') }}" class="btn btn-secondary">Cancel</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- FTA Identity List --}}
                        <div class="mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-light">
                                    <h3 class="h6 mb-0">FTA Identity List</h3>
                                </div>
                                <div class="card-body p-4">
                                    <div class="form-floating mb-3">
                                        <select id="project_filter" class="form-control">
                                            <option value="">All Projects</option>
                                            @foreach ($projectTypes as $projectType)
                                                <option value="{{ $projectType->id }}"
                                                    {{ request('project_filter') == $projectType->id ? 'selected' : '' }}>
                                                    {{ $projectType->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="project_filter">Filter by Project Type</label>
                                    </div>

                                    @if ($ftaIdentities->isEmpty())
                                        <p class="text-muted mb-0">No FTA Identities found.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Component Name</th>
                                                        <th>Project</th>

                                                        <th>CFI (λ)</th>
                                                        <th>Diagram</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="fta-table-body">
                                                    @foreach ($ftaIdentities as $ftaIdentity)
                                                        <tr>
                                                            <td>{{ $ftaIdentity->id }}</td>
                                                            <td>{{ $ftaIdentity->componentname }}</td>
                                                            <td>{{ $ftaIdentity->projectType->title ?? 'N/A' }}</td>
                                                            <td>{{ $ftaIdentity->cfi ? $ftaIdentity->cfi : 'Not Calculated' }}
                                                            </td>
                                                            <td>
                                                                @if ($ftaIdentity->diagram_url)
                                                                    <a href="{{ $ftaIdentity->diagram_url }}"
                                                                        target="_blank" class="btn btn-sm btn-outline-info">
                                                                        <i class="bi bi-diagram-3"></i> View
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">N/A</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="d-flex gap-2">
                                                                    <a href="{{ route('fta.project', $ftaIdentity->id) }}"
                                                                        class="btn btn-sm btn-primary" title="View">
                                                                        <i class="bi bi-eye"></i> View
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('fta.calculate', $ftaIdentity->id) }}"
                                                                        method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-success calculate-btn"
                                                                            title="Calculate CFI"
                                                                            data-id="{{ $ftaIdentity->id }}"
                                                                            data-component="{{ $ftaIdentity->componentname }}">
                                                                            <i class="bi bi-calculator"></i> Calculate
                                                                        </button>
                                                                    </form>
                                                                    @if ($yourauth->id === 1)
                                                                        <form id="delete-form-{{ $ftaIdentity->id }}"
                                                                            action="{{ route('fta.destroy', $ftaIdentity->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-danger"
                                                                                onclick="return confirm('Are you sure you want to delete {{ $ftaIdentity->componentname }}? This action cannot be undone.');">
                                                                                <i class="bi bi-trash"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                    <a href="{{ route('fta.nodes.edit', $ftaIdentity->id) }}"
                                                                        class="btn btn-sm btn-outline-secondary edit-node-btn"
                                                                        title="Edit Node List"
                                                                        data-id="{{ $ftaIdentity->id }}"
                                                                        data-component="{{ $ftaIdentity->componentname }}">
                                                                        <i class="bi bi-pencil"></i> Edit Nodes
                                                                    </a>
                                                                    <a href="{{ route('fta.events.edit', $ftaIdentity->id) }}"
                                                                        class="btn btn-sm btn-outline-secondary edit-event-btn"
                                                                        title="Edit Event List"
                                                                        data-id="{{ $ftaIdentity->id }}"
                                                                        data-component="{{ $ftaIdentity->componentname }}">
                                                                        <i class="bi bi-layout-text-sidebar-reverse"></i>
                                                                        Edit Events
                                                                    </a>
                                                                </div>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function() {
            $('#project_filter').on('change', function() {
                const projectId = $(this).val();
                $.ajax({
                    url: '{{ route('fta.json') }}',
                    method: 'GET',
                    data: {
                        project_filter: projectId
                    },
                    success: function(response) {
                        const tbody = $('#fta-table-body');
                        tbody.empty();

                        if (response.ftaIdentities.length === 0) {
                            tbody.append(
                                '<tr><td colspan="7" class="text-muted text-center">No FTA Identities found.</td></tr>'
                            );
                            return;
                        }

                        response.ftaIdentities.forEach(function(fta) {
                            const row = `
                                <tr>
                                    <td>${fta.id}</td>
                                    <td>${fta.componentname}</td>
                                    <td>${fta.project_type ? fta.project_type.title : 'N/A'}</td>
                                    <td>${fta.time_interval || 'N/A'}</td>
                                    <td>${fta.cfi ? Number(fta.cfi).toFixed(4) : 'Not Calculated'}</td>
                                    <td>
                                        ${fta.diagram_url ? `
                                                            <a href="${fta.diagram_url}" target="_blank" class="btn btn-sm btn-outline-info">
                                                                <i class="bi bi-diagram-3"></i> View
                                                            </a>
                                                        ` : '<span class="text-muted">N/A</span>'}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="${response.routes.project.replace(':fta_identity_id', fta.id)}" class="btn btn-sm btn-primary" title="View">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <form action="${response.routes.calculate.replace(':fta_identity_id', fta.id)}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success calculate-btn" title="Calculate CFI" data-id="${fta.id}" data-component="${fta.componentname}">
                                                    <i class="bi bi-calculator"></i> Calculate
                                                </button>
                                            </form>
                                            ${response.yourauth_id === 1 ? `
                                                                <form id="delete-form-${fta.id}" action="${response.routes.destroy.replace(':fta_identity_id', fta.id)}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete ${fta.componentname}? This action cannot be undone.');">
                                                                        <i class="bi bi-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            ` : ''}
                                            <a href="${response.routes.nodes_edit.replace(':fta_identity_id', fta.id)}" class="btn btn-sm btn-outline-secondary edit-node-btn" title="Edit Node List" data-id="${fta.id}" data-component="${fta.componentname}">
                                                <i class="bi bi-pencil"></i> Edit Nodes
                                            </a>
                                            <a href="${response.routes.events_edit.replace(':fta_identity_id', fta.id)}" class="btn btn-sm btn-outline-secondary edit-event-btn" title="Edit Event List" data-id="${fta.id}" data-component="${fta.componentname}">
                                                <i class="bi bi-layout-text-sidebar-reverse"></i> Edit Events
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            tbody.append(row);
                        });
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to load FTA Identities.',
                            icon: 'error',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                });
            });

            if ($('#project_filter').val()) {
                $('#project_filter').trigger('change');
            }

            $(document).on('click', '.edit-node-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const component = $(this).data('component');
                Swal.fire({
                    title: 'Edit Node List',
                    text: `Are you sure you want to edit the node list for "${component}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Edit Nodes',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary mx-2',
                        cancelButton: 'btn btn-outline-secondary mx-2'
                    }
                }).then((result) => {
                    if (result.isConfirmed) window.location.href = url;
                });
            });

            $(document).on('click', '.edit-event-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const component = $(this).data('component');
                Swal.fire({
                    title: 'Edit Event List',
                    text: `Are you sure you want to edit the event list for "${component}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Edit Events',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary mx-2',
                        cancelButton: 'btn btn-outline-secondary mx-2'
                    }
                }).then((result) => {
                    if (result.isConfirmed) window.location.href = url;
                });
            });

            $(document).on('click', '.calculate-btn', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const component = $(this).data('component');
                Swal.fire({
                    title: 'Calculate CFI',
                    text: `Are you sure you want to calculate CFI for "${component}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Calculate',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-success mx-2',
                        cancelButton: 'btn btn-outline-secondary mx-2'
                    }
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
@endpush
