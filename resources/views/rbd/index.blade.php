@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="#">RBD Identities</a></li>
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
                {{-- Card utama --}}
                <div class="card shadow mb-4">
                    <div class="card-header bg-danger text-white">
                        <h3 class="card-title fw-bold mb-0">RBD Identities</h3>
                    </div>
                    <div class="card-body">
                        {{-- Pesan Sukses --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Form Create RBD Identity --}}
                        <div class="mb-4">
                            <div class="card shadow border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h3 class="h6 mb-0">Create New RBD Identity</h3>
                                </div>
                                <div class="card-body p-4">
                                    <form method="POST" action="{{ route('rbdidentity.store') }}">
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
                                            <input type="number" name="time_interval" id="time_interval"
                                                class="form-control @error('time_interval') is-invalid @enderror"
                                                placeholder="Time Interval" value="{{ old('time_interval') }}"
                                                min="0" step="1">
                                            <label for="time_interval">Time Interval</label>
                                            @error('time_interval')
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
                                        <a href="{{ route('rbd.index') }}" class="btn btn-secondary">Cancel</a>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Daftar RBD Identities --}}
                        <div class="mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-light">
                                    <h3 class="h6 mb-0">RBD Identity List</h3>
                                </div>
                                <div class="card-body p-4">
                                    {{-- Project Type Filter Dropdown --}}
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

                                    @if ($rbdIdentities->isEmpty())
                                        <p class="text-muted mb-0">No RBD Identities found.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Component Name</th>
                                                        <th>Project</th>
                                                        <th>Time Interval</th>
                                                        <th>Reliability</th>
                                                        <th>Diagram</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="rbd-table-body">
                                                    @foreach ($rbdIdentities as $rbdIdentity)
                                                        <tr>
                                                            <td>{{ $rbdIdentity->id }}</td>
                                                            <td>{{ $rbdIdentity->componentname }}</td>
                                                            <td>{{ $rbdIdentity->projectType->title ?? 'N/A' }}</td>
                                                            <td>{{ $rbdIdentity->time_interval ?? 'N/A' }}</td>
                                                            <td>{{ $rbdIdentity->temporary_reliability_value ? number_format($rbdIdentity->temporary_reliability_value, 4) : 'Not Calculated' }}
                                                            </td>
                                                            <td>
                                                                @if ($rbdIdentity->diagram_url)
                                                                    <a href="{{ $rbdIdentity->diagram_url }}"
                                                                        target="_blank" class="btn btn-sm btn-outline-info">
                                                                        <i class="bi bi-diagram-3"></i> View
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">N/A</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="d-flex gap-2">
                                                                    <a href="{{ route('rbd.project', $rbdIdentity->id) }}"
                                                                        class="btn btn-sm btn-primary" title="View">
                                                                        <i class="bi bi-eye"></i> View
                                                                    </a>
                                                                    <form
                                                                        action="{{ route('rbd.calculate', $rbdIdentity->id) }}"
                                                                        method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-success calculate-btn"
                                                                            title="Calculate Reliability"
                                                                            data-id="{{ $rbdIdentity->id }}"
                                                                            data-component="{{ $rbdIdentity->componentname }}">
                                                                            <i class="bi bi-calculator"></i> Calculate
                                                                        </button>
                                                                    </form>
                                                                    @if ($yourauth->id === 1)
                                                                        <form id="delete-form-{{ $rbdIdentity->id }}"
                                                                            action="{{ route('rbd.destroy', $rbdIdentity->id) }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-sm btn-danger"
                                                                                onclick="return confirm('Are you sure you want to delete {{ $rbdIdentity->componentname }}? This action cannot be undone.');">
                                                                                <i class="bi bi-trash"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                    <a href="{{ route('rbd.nodes.edit', $rbdIdentity->id) }}"
                                                                        class="btn btn-sm btn-outline-secondary edit-node-btn"
                                                                        title="Edit Node List"
                                                                        data-id="{{ $rbdIdentity->id }}"
                                                                        data-component="{{ $rbdIdentity->componentname }}">
                                                                        <i class="bi bi-pencil"></i> Edit Nodes
                                                                    </a>
                                                                    <a href="{{ route('rbd.blocks.edit', $rbdIdentity->id) }}"
                                                                        class="btn btn-sm btn-outline-secondary edit-block-btn"
                                                                        title="Edit Block List"
                                                                        data-id="{{ $rbdIdentity->id }}"
                                                                        data-component="{{ $rbdIdentity->componentname }}">
                                                                        <i class="bi bi-layout-text-sidebar-reverse"></i>
                                                                        Edit Blocks
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
                    </div> {{-- card-body --}}
                </div> {{-- card utama --}}
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
            // Project Filter Change Handler
            $('#project_filter').on('change', function() {
                const projectId = $(this).val();
                $.ajax({
                    url: '{{ route('rbd.json') }}',
                    method: 'GET',
                    data: {
                        project_filter: projectId
                    },
                    success: function(response) {
                        const tbody = $('#rbd-table-body');
                        tbody.empty(); // Clear existing rows

                        if (response.rbdIdentities.length === 0) {
                            tbody.append(
                                '<tr><td colspan="6" class="text-muted text-center">No RBD Identities found.</td></tr>'
                            );
                            return;
                        }

                        response.rbdIdentities.forEach(function(rbd) {
                            const row = `
                                <tr>
                                    <td>${rbd.id}</td>
                                    <td>${rbd.componentname}</td>
                                    <td>${rbd.project_type ? rbd.project_type.title : 'N/A'}</td>
                                    <td>${rbd.time_interval || 'N/A'}</td>
                                    <td>${rbd.temporary_reliability_value ? Number(rbd.temporary_reliability_value).toFixed(4) : 'Not Calculated'}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="${response.routes.project.replace(':rbdidentity_id', rbd.id)}" class="btn btn-sm btn-primary" title="View">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <form action="${response.routes.calculate.replace(':rbdidentity_id', rbd.id)}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success calculate-btn" title="Calculate Reliability" data-id="${rbd.id}" data-component="${rbd.componentname}">
                                                    <i class="bi bi-calculator"></i> Calculate
                                                </button>
                                            </form>
                                            ${response.yourauth_id === 1 ? `
                                                        <form id="delete-form-${rbd.id}" action="${response.routes.destroy.replace(':rbdidentity_id', rbd.id)}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete ${rbd.componentname}? This action cannot be undone.');">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    ` : ''}
                                            <a href="${response.routes.nodes_edit.replace(':rbdidentity_id', rbd.id)}" class="btn btn-sm btn-outline-secondary edit-node-btn" title="Edit Node List" data-id="${rbd.id}" data-component="${rbd.componentname}">
                                                <i class="bi bi-pencil"></i> Edit Nodes
                                            </a>
                                            <a href="${response.routes.blocks_edit.replace(':rbdidentity_id', rbd.id)}" class="btn btn-sm btn-outline-secondary edit-block-btn" title="Edit Block List" data-id="${rbd.id}" data-component="${rbd.componentname}">
                                                <i class="bi bi-layout-text-sidebar-reverse"></i> Edit Blocks
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
                            text: 'Failed to load RBD Identities.',
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

            // Trigger filter on page load if a project is already selected
            if ($('#project_filter').val()) {
                $('#project_filter').trigger('change');
            }

            // Edit Node List Confirmation
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
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });

            // Edit Block List Confirmation
            $(document).on('click', '.edit-block-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const component = $(this).data('component');
                Swal.fire({
                    title: 'Edit Block List',
                    text: `Are you sure you want to edit the block list for "${component}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Edit Blocks',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary mx-2',
                        cancelButton: 'btn btn-outline-secondary mx-2'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });

            // Calculate Reliability Confirmation
            $(document).on('click', '.calculate-btn', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                const component = $(this).data('component');
                Swal.fire({
                    title: 'Calculate Reliability',
                    text: `Are you sure you want to calculate reliability for "${component}"?`,
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
                    if (result.isConfirmed) {
                        form.submit();
                    }
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
