@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="#">Library</a></li>
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
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h3 class="card-title text-bold">Library</h3>
                    </div>
                    <div class="card-body">
                        <button id="createProjectType" class="btn btn-primary mb-3">Create New Project Type</button>

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <table id="example2" class="table table-bordered table-hover mt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Project Code</th>
                                    <th>Vault Link</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($projectTypes as $index => $projectType)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $projectType->title }}</td>
                                        <td>{{ $projectType->project_code }}</td>
                                        <td>
                                            @if($projectType->vault_link)
                                                <a href="{{ $projectType->vault_link }}" target="_blank" rel="noopener noreferrer">
                                                    {{ $projectType->vault_link }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            <button class="btn btn-warning btn-sm editProjectType"
                                                data-id="{{ $projectType->id }}" data-title="{{ $projectType->title }}"
                                                data-project_code="{{ $projectType->project_code }}"
                                                data-vault_link="{{ $projectType->vault_link }}">Edit</button>

                                            @if(auth()->user()->id == 1)
                                                <button class="btn btn-danger btn-sm deleteProjectType"
                                                    data-id="{{ $projectType->id }}">
                                                    Delete
                                                </button>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#example2').DataTable();
        });

        $('#createProjectType').on('click', function () {
            Swal.fire({
                title: 'Create Project Type',
                html:
                    '<input id="swal-title" class="swal2-input" placeholder="Enter Title">' +
                    '<input id="swal-project-code" class="swal2-input" placeholder="Enter Project Code">' +
                    '<input id="swal-vault-link" class="swal2-input" placeholder="Enter Vault Link">',
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    let title = document.getElementById('swal-title').value.trim();
                    let projectCode = document.getElementById('swal-project-code').value.trim();
                    let vaultLink = document.getElementById('swal-vault-link').value.trim();

                    if (!title || !projectCode) {
                        Swal.showValidationMessage('Title and Project Code are required!');
                        return false;
                    }

                    return fetch("{{ route('project_types.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            title: title,
                            project_code: projectCode,
                            vault_link: vaultLink
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message
                            }).then(() => {
                                location.reload(); // Reload halaman setelah sukses
                            });
                        })
                        .catch(error => {
                            let errorMessage = 'Something went wrong!';
                            if (error.errors) {
                                errorMessage = Object.values(error.errors).join(' ');
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage
                            });
                        });
                }
            });
        });

    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Edit Project Type
            // Edit Project Type
            document.querySelectorAll(".editProjectType").forEach(button => {
                button.addEventListener("click", function () {
                    let id = this.dataset.id;
                    let title = this.dataset.title;
                    let project_code = this.dataset.project_code;
                    let vault_link = this.dataset.vault_link ?? "";

                    Swal.fire({
                        title: 'Edit Project Type',
                        html:
                            `<input id="swal-title" class="swal2-input" value="${title}">` +
                            `<input id="swal-project-code" class="swal2-input" value="${project_code}">` +
                            `<input id="swal-vault-link" class="swal2-input" value="${vault_link}">`,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        preConfirm: () => {
                            return fetch(`/project_types/update/${id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    title: document.getElementById('swal-title').value,
                                    project_code: document.getElementById('swal-project-code').value,
                                    vault_link: document.getElementById('swal-vault-link').value
                                })
                            }).then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Updated!', 'Project Type has been updated.', 'success');
                                        location.reload();
                                    } else {
                                        Swal.fire('Error!', 'Something went wrong.', 'error');
                                    }
                                }).catch(error => {
                                    Swal.fire('Error!', 'Failed to update project type.', 'error');
                                });
                        }
                    });
                });
            });
            // Delete Project Type
            document.querySelectorAll(".deleteProjectType").forEach(button => {
                button.addEventListener("click", function () {
                    let id = this.dataset.id;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/project_types/delete/${id}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({})
                            }).then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire('Deleted!', 'Project Type has been deleted.', 'success');
                                        location.reload();
                                    } else {
                                        Swal.fire('Error!', 'Something went wrong.', 'error');
                                    }
                                }).catch(error => {
                                    Swal.fire('Error!', 'Failed to delete project type.', 'error');
                                });
                        }
                    });
                });
            });
        });
    </script>
@endpush