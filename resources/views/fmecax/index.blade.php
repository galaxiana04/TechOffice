@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left shadow-sm rounded">
                        <li class="breadcrumb-item">
                            <a href="{{ route('fmeca.index') }}" class="text-primary font-weight-bold">
                                Informasi Critical Part Dashboard
                            </a>
                        </li>
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
                Critical Part Monitoring Dashboard
                <span class="badge badge-danger ml-2">Live</span>
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="form-group mb-4">
                <label for="project" class="font-weight-bold text-uppercase text-secondary">Pilih Project:</label>
                <select id="project" class="form-control border border-danger shadow-sm">
                    <option value="">-- Pilih Project --</option>
                    @foreach ($allproject as $project)
                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                    @endforeach
                </select>
            </div>

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
                                    <h2 class="text-uppercase font-weight-bold text-danger">List Notif Critical Part</h2>
                                </td>
                                <td class="p-2">
                                    <strong class="text-muted">Project:</strong>
                                    <span id="selectedProject" class="text-dark font-weight-bold"></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">
                                    <strong class="text-muted">Tanggal:</strong>
                                    <span class="text-primary">{{ date('d F Y') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">
                                    <strong class="text-muted">Total Issues:</strong>
                                    <span id="totalIssues" class="badge badge-danger shadow"
                                        style="font-size: 1.2rem;">0</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2">
                                    <strong class="text-muted">Safety:</strong>
                                    <span id="safetyIssues" class="badge badge-warning shadow"
                                        style="font-size: 1.1rem;">0</span>
                                    &nbsp;/&nbsp;
                                    <strong class="text-muted">Reliability:</strong>
                                    <span id="reliabilityIssues" class="badge badge-success shadow"
                                        style="font-size: 1.1rem;">0</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="card-body">
                    <table class="table table-hover table-striped table-bordered shadow-sm" id="fmecaTable">
                        <thead class="bg-danger text-white text-center">
                            <tr>
                                <th>No</th>
                                <th>Project</th>
                                <th>User</th>
                                <th>Files</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function fetchFmecaData(projectId, projectName) {
            if (!projectId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Project!',
                    text: 'Silakan pilih project terlebih dahulu.'
                });
                return;
            }

            $('#selectedProject').text(projectName);

            Swal.fire({
                title: 'Loading...',
                text: 'Mengambil data Critical Part',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route('fmeca.data') }}',
                type: 'GET',
                data: {
                    project_id: projectId
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.close();

                    const tableBody = $('#fmecaTable tbody');
                    tableBody.empty();

                    if (response.data.length === 0) {
                        tableBody.append(
                            `<tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>`);
                    } else {
                        response.data.forEach((item, index) => {
                            const createdAt = new Date(item.created_at).toLocaleString('id-ID');
                            tableBody.append(`
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>${item.project_title}</td>
                                    <td>${item.user}</td>
                                    <td>${item.files.map(file => `<a href="${file.link}" target="_blank">${file.filename}</a>`).join(', ')}</td>
                                    <td>${createdAt}</td>
                                    <td><a href="fmeca/show/${item.id}" class="btn btn-sm btn-primary">Detail</a></td>
                                </tr>
                            `);
                        });

                        $('#totalIssues').text(response.data.length);
                        // Jika ingin memisahkan safety/reliability, tambahkan logika di response
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.error || 'Terjadi kesalahan saat mengambil data'
                    });
                }
            });
        }

        $(document).ready(function() {
            const firstOption = $('#project option:nth-child(2)');
            const firstProjectId = firstOption.val();
            const firstProjectName = firstOption.text();

            if (firstProjectId) {
                $('#project').val(firstProjectId);
                fetchFmecaData(firstProjectId, firstProjectName);
            }

            $('#project').change(function() {
                const selectedId = $(this).val();
                const selectedText = $('#project option:selected').text();
                fetchFmecaData(selectedId, selectedText);
            });
        });
    </script>
@endpush
