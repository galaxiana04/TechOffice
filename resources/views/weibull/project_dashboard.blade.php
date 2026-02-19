@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('weibull.dashboard') }}">Weibull Analysis</a></li>
                        <li class="breadcrumb-item active">Project Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container mt-4">

        <h2 class="mb-4">Project Dashboard</h2>

        <button class="btn btn-primary mb-3" id="addProjectBtn">Tambah Project</button>

        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Nama Project</th>
                    <th>Daily Operation Hours</th>
                    <th>Weekly Operation Days</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($profiles as $project)
                    <tr>
                        <td>{{ $project->projectType->title }}</td>
                        <td>{{ $project->daily_operation_hours }}</td>
                        <td>{{ $project->weekly_operation_days }}</td>
                        <td>
                            <button class="btn btn-sm btn-warning editProjectBtn" data-id="{{ $project->id }}"
                                data-project_type_id="{{ $project->projectType->id }}"
                                data-daily="{{ $project->daily_operation_hours }}"
                                data-weekly="{{ $project->weekly_operation_days }}">Edit</button>

                            <button class="btn btn-sm btn-danger deleteProjectBtn" data-id="{{ $project->id }}"
                                data-name="{{ $project->projectType->title }}">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function validateDailyWeekly(daily, weekly) {
            daily = parseFloat(daily);
            weekly = parseInt(weekly);

            if (isNaN(daily) || daily < 1 || daily > 24) {
                Swal.showValidationMessage('Daily Operation Hours harus antara 1 - 24 jam.');
                return false;
            }
            if (isNaN(weekly) || weekly < 1 || weekly > 7) {
                Swal.showValidationMessage('Weekly Operation Days harus antara 1 - 7 hari.');
                return false;
            }
            return true;
        }

        // Tambah Project
        document.getElementById('addProjectBtn').addEventListener('click', function() {
            const projectOptions = `
                @foreach ($allproject as $type)
                    <option value="{{ $type->id }}">{{ $type->title }}</option>
                @endforeach
            `;

            Swal.fire({
                title: '<strong>Tambah Project Baru</strong>',
                icon: 'info',
                html: `
                    <div class="text-left">
                        <label class="swal2-label">Nama Project</label>
                        <select id="project_type_id" class="swal2-select w-100">${projectOptions}</select>
                        
                        <label class="swal2-label mt-3">Daily Operation Hours</label>
                        <input type="number" id="daily" class="swal2-input" placeholder="Contoh: 8" min="1" max="24">
                        <small class="text-muted">Jam operasional per hari (1-24)</small>
                        
                        <label class="swal2-label mt-3">Weekly Operation Days</label>
                        <input type="number" id="weekly" class="swal2-input" placeholder="Contoh: 5" min="1" max="7">
                        <small class="text-muted">Hari operasional per minggu (1-7)</small>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: '<i class="fa fa-save"></i> Simpan',
                cancelButtonText: 'Batal',
                width: '600px',
                preConfirm: () => {
                    const project_type_id = document.getElementById('project_type_id').value;
                    const daily = document.getElementById('daily').value;
                    const weekly = document.getElementById('weekly').value;

                    if (!project_type_id || !daily || !weekly) {
                        Swal.showValidationMessage('Semua field wajib diisi!');
                        return false;
                    }

                    if (!validateDailyWeekly(daily, weekly)) return false;

                    return {
                        project_type_id,
                        daily,
                        weekly
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('weibull.project-store') }}';
                    form.innerHTML = `
                        @csrf
                        <input type="hidden" name="project_type_id" value="${result.value.project_type_id}">
                        <input type="hidden" name="daily_operation_hours" value="${result.value.daily}">
                        <input type="hidden" name="weekly_operation_days" value="${result.value.weekly}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Edit Project
        document.querySelectorAll('.editProjectBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const daily = btn.dataset.daily;
                const weekly = btn.dataset.weekly;
                const currentTypeId = btn.dataset.project_type_id;

                const projectOptions = `
                    @foreach ($allproject as $type)
                        <option value="{{ $type->id }}" ${currentTypeId == {{ $type->id }} ? 'selected' : ''}>
                            {{ $type->title }}
                        </option>
                    @endforeach
                `;

                Swal.fire({
                    title: '<strong>Edit Project</strong>',
                    icon: 'edit',
                    html: `
                        <div class="text-left">
                            <label class="swal2-label">Nama Project</label>
                            <select id="project_type_id" class="swal2-select w-100">${projectOptions}</select>
                            
                            <label class="swal2-label mt-3">Daily Operation Hours</label>
                            <input type="number" id="daily" class="swal2-input" value="${daily}" min="1" max="24">
                            <small class="text-muted">Jam operasional per hari (1-24)</small>
                            
                            <label class="swal2-label mt-3">Weekly Operation Days</label>
                            <input type="number" id="weekly" class="swal2-input" value="${weekly}" min="1" max="7">
                            <small class="text-muted">Hari operasional per minggu (1-7)</small>
                        </div>
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-save"></i> Update',
                    cancelButtonText: 'Batal',
                    width: '600px',
                    preConfirm: () => {
                        const project_type_id = document.getElementById('project_type_id')
                            .value;
                        const daily = document.getElementById('daily').value;
                        const weekly = document.getElementById('weekly').value;

                        if (!project_type_id || !daily || !weekly) {
                            Swal.showValidationMessage('Semua field wajib diisi!');
                            return false;
                        }

                        if (!validateDailyWeekly(daily, weekly)) return false;

                        return {
                            project_type_id,
                            daily,
                            weekly
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/failurerate/projects/${id}`;
                        form.innerHTML = `
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="project_type_id" value="${result.value.project_type_id}">
                            <input type="hidden" name="daily_operation_hours" value="${result.value.daily}">
                            <input type="hidden" name="weekly_operation_days" value="${result.value.weekly}">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // Hapus tetap sama (sudah bagus)
        document.querySelectorAll('.deleteProjectBtn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const name = btn.dataset.name;
                Swal.fire({
                    title: `Hapus Project "${name}"?`,
                    text: "Data akan dihapus permanen dan tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/failurerate/projects/${id}`;
                        form.innerHTML = `@csrf @method('DELETE')`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
@push('css')
    <style>
        .swal2-label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #495057;
        }

        .swal2-select,
        .swal2-input {
            width: 100% !important;
        }

        .text-left {
            text-align: left;
        }

        .w-100 {
            width: 100%;
        }

        .mt-3 {
            margin-top: 1rem;
        }
    </style>
@endpush
