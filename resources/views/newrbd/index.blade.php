@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('newrbd.index') }}">RBD Management</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title fw-bold mb-0">
                        <i class="bi bi-diagram-3"></i> RBD Models Management
                    </h3>
                </div>

                <div class="card-body">
                    {{-- Alert Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <a href="{{ route('newrbd.jsoncreatemodelview') }}" class="btn btn-sm btn-primary">Create Model</a>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle" id="models-table">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Created by</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($models as $model)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $model->name }}</strong>
                                        </td>
                                        <td>
                                            @if ($model->description)
                                                <span title="{{ $model->description }}">
                                                    {{ $model->description }}
                                                </span>
                                            @else
                                                <em class="text-muted">No description</em>
                                            @endif
                                        </td>
                                        <td>{{ $model->user->name }}</td>
                                        <td>
                                            <a href="{{ route('newrbd.newrbdinstances', ['id' => $model->id]) }}"
                                                class="btn btn-sm bg-pink shadow-sm">View Instances</a>
                                            <a href="{{ route('newrbd.jsonshowmodel', ['id' => $model->id]) }}"
                                                class="btn btn-sm bg-purple shadow-sm">View Json</a>
                                            @if (Auth::id() === 1 || Auth::id() === $model->user_id)
                                                <form action="{{ route('newrbd.modelsdestroy', $model->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Yakin ingin menghapus model: {{ addslashes($model->name) }}?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button"
                                                class="btn btn-sm btn-info text-white duplicate-model-btn"
                                                data-id="{{ $model->id }}" data-name="{{ $model->name }}"
                                                data-description="{{ $model->description ?? '' }}">
                                                <i class="bi bi-copy"></i> Duplicate
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-1"></i><br>
                                            No RBD Models found. Create your first one!
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

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        .swal2-popup-custom .swal2-title {
            font-size: 1.25rem;
        }

        .swal2-textarea {
            width: 100% !important;
            resize: vertical;
        }

        .swal2-checkbox {
            width: auto !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        document.querySelectorAll('.duplicate-model-btn').forEach(button => {
            button.addEventListener('click', async function() {
                const modelId = this.getAttribute('data-id');
                const modelName = this.getAttribute('data-name');
                const modelDescription = this.getAttribute('data-description') || '';

                const {
                    value: formValues
                } = await Swal.fire({
                    title: `Duplicate Model: <strong>${modelName}</strong>`,
                    html: `
                        <div class="text-start">
                            <div class="mb-3">
                                <label class="form-label">New Model Name <span class="text-danger">*</span></label>
                                <input id="swal-name" class="swal2-input" value="${modelName} (Copy)" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea id="swal-description" class="swal2-textarea" rows="2">${modelDescription}</textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" id="swal-duplicate-fr" class="swal2-checkbox">
                                <label class="form-check-label" for="swal-duplicate-fr">
                                    Duplicate Failure Rates juga
                                </label>
                            </div>
                        </div>
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-copy"></i> Duplicate Model',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const name = document.getElementById('swal-name').value.trim();
                        if (!name) {
                            Swal.showValidationMessage('New Model Name is required');
                            return false;
                        }
                        return {
                            name: name,
                            description: document.getElementById('swal-description').value,
                            duplicate_failure_rates: document.getElementById(
                                'swal-duplicate-fr').checked ? 1 : 0
                        };
                    },
                    width: '600px',
                    customClass: {
                        popup: 'swal2-popup-custom',
                        confirmButton: 'btn btn-info',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                });

                if (formValues) {
                    const formData = new FormData();
                    formData.append('name', formValues.name);
                    formData.append('description', formValues.description);
                    formData.append('duplicate_failure_rates', formValues.duplicate_failure_rates);
                    formData.append('_token', '{{ csrf_token() }}');

                    Swal.fire({
                        title: 'Duplicating...',
                        text: 'Please wait while we duplicate the model and all instances.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch(`{{ url('newrbd/newrbdmodels') }}/${modelId}/duplicate`, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            // Cek status HTTP
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw err;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: data.success,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                throw new Error(data.error || 'Unknown error');
                            }
                        })
                        .catch(error => {
                            console.error('Duplicate Error:', error);
                            const msg = error.error || error.message ||
                                'Failed to duplicate model.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: msg
                            });
                        });
                }
            });
        });
    </script>
@endpush
