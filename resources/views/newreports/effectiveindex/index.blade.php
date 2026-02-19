@extends('layouts.universal')


@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('newreports.index') }}">List Unit & Project</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="card card-danger card-outline">
        <div class="card-header">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <h3 class="card-title text-bold">Page Monitoring Dokumen <span class="badge badge-info ml-1"></span></h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="project">Pilih Project:</label>
                        <select id="project" class="form-control">
                            <option value="">-- Pilih Project --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-9">
                                <label for="deadlineDate">Deadline Date (Opsional):</label>
                                <input type="date" id="deadlineDate" class="form-control">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button id="viewDeadlineBtn" class="btn btn-primary btn-block"
                                    style="margin-bottom: 0.375rem;">View</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-danger">
                <div class="card-header">
                    <table class="table table-bordered my-2">
                        <tbody>
                            <tr>
                                <td rowspan="5" style="width: 25%" class="text-center">
                                    <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo" class="p-2"
                                        style="max-width: 250px">
                                </td>
                                <td rowspan="5" style="width: 50%">
                                    <h1 class="text-xl text-center mt-2">DAFTAR PROGRES</h1>
                                </td>
                                <td style="width: 25%" class="p-1">Project: <b id="selectedProject"></b></td>
                            </tr>
                            <tr>
                                <td style="width: 25%" class="p-1">Tanggal: <b>{{ date('d F Y') }}</b></td>
                            </tr>
                            <tr>
                                <td style="width: 25%" class="p-1">
                                    Progres: <b><span id="totalPercentage" class="badge badge-success"
                                            style="font-size: 2rem;">0%</span></b>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%" class="p-1">
                                    Total Dokumen: <b><span id="totalDocs" class="badge badge-info"
                                            style="font-size: 1.5rem;">0</span></b>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 25%" class="p-1">
                                    Dokumen Release: <b><span id="totalReleasedDocs" class="badge badge-success"
                                            style="font-size: 1.5rem;">0</span></b> /
                                    Non-Release: <b><span id="totalNullDocs" class="badge badge-warning"
                                            style="font-size: 1.5rem;">0</span></b>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Rest of the card-header content remains unchanged -->
                    <div class="row mt-3">
                        @if ($user->rule == 'superuser')
                            <div class="col-md-3 col-sm-6 col-6 p-0">
                                <button type="button" class="btn btn-danger btn-sm btn-block"
                                    onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                            </div>
                        @endif
                        @if ($user->rule == 'MTPR' || $user->rule == 'superuser')
                            <div class="col-md-3 col-sm-6 col-6 p-0">
                                <a href="{{ url('newprogressreports/upload') }}"
                                    class="btn btn-primary btn-sm btn-block">Upload
                                    Progress Report</a>
                            </div>
                        @endif
                        <div class="col-md-3 col-sm-6 col-6 p-0">
                            @if (session('internalon'))
                                <button id="internalOffButton" class="btn btn-success mt-0 btn-borderless">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                                <button id="internalButton"
                                    class="btn btn-default bg-white mt-0 btn-borderless d-none"></button>
                            @else
                                <button id="internalOffButton" class="btn btn-success mt-2 btn-borderless d-none">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                                <button id="internalButton" class="btn btn-default bg-white mt-2 btn-borderless"></button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    @if ($user->rule == 'MTPR' || $user->rule == 'superuser' || $user->id == 1)
                        <div class="col-md-3 col-sm-6 col-6 p-0">
                            <form id="downloadAllForm" method="POST" action="">
                                @csrf
                                <button type="submit" class="btn btn-default bg-maroon btn-sm btn-block mt-0">
                                    Download Laporan All Unit
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 p-0">
                            <form id="downloadAllFormrevnol" method="POST" action=""
                                class="d-flex align-items-end gap-2">
                                @csrf
                                <div class="form-group mb-0 flex-grow-1">
                                    <label for="cutoff_date">Batas Tanggal (Cut Off Realisasi)</label>
                                    <input type="date" name="cutoff_date" id="cutoff_date" class="form-control"
                                        required>
                                </div>
                                <button type="submit" class="btn btn-default bg-teal btn-sm">
                                    Download Laporan Rev 0
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    <table class="table table-bordered" id="dashboardTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Proyek</th>
                                <th>Unit</th>
                                <th>Output</th>
                                <th>Persentase</th>
                                <th>Jumlah Dokumen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh JavaScript -->
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
        $(document).ready(function() {
            function fetchDashboardData(projectId, projectTitle, deadlineDate = null) {
                if (projectId) {
                    Swal.fire({
                        title: 'Loading...',
                        text: 'Mengambil data dashboard',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('newreports.dashboarddata') }}",
                        method: 'POST',
                        data: {
                            project_id: projectId,
                            deadline_date: deadlineDate,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.close();
                            $('#dashboardTable tbody').empty();
                            $('#selectedProject').text(projectTitle);

                            let totalDocs = response.total_docs || 0;
                            let totalReleasedDocs = response.total_released_docs || 0;
                            let totalNullDocs = response.total_null_docs || 0;

                            let overallPercentage = totalDocs > 0 ? (totalReleasedDocs / totalDocs) *
                                100 : 0;

                            if (response.data.length > 0) {
                                $.each(response.data, function(index, item) {
                                    const releasedDocs = parseInt(item.released_docs || 0, 10);
                                    const nullDocs = parseInt(item.null_docs || 0, 10);
                                    const docsSum = releasedDocs + nullDocs;

                                    const badgeClass = window.internalOn ? 'badge-warning' :
                                        'badge-success';
                                    const csrfToken = $('meta[name="csrf-token"]').attr(
                                        'content');

                                    let doubleDetectorButton = '';
                                    let deleteReportButton = '';
                                    @if ($user->rule == 'superuser' || $user->rule == 'MTPR')
                                        doubleDetectorButton = `
                                        <a href="{{ url('newreports/doubledetector') }}/${item.id}" class="btn btn-default bg-khaki">
                                            Double Detector: ${item.doubledetectorcount || 0}
                                        </a>
                                    `;
                                        deleteReportButton = `
                                        <button type="button" class="btn btn-default bg-maroon" onclick="deleteReport(${item.id})">Delete</button>
                                    `;
                                    @endif


                                    const downloadForm = `
                                        <form action="/newreports/${item.id}/downloadlaporan" method="POST" style="display:inline;">
                                            <input type="hidden" name="_token" value="${csrfToken}">
                                            <button type="submit" class="btn btn-default bg-purple"
                                                onclick="return confirm('Are you sure?')">Download Report</button>
                                        </form>
                                    `;


                                    $('#dashboardTable tbody').append(`
                                    <tr>
                                        <td>${item.no}</td>
                                        <td>${item.project_type}</td>
                                        <td>${item.unit_type}</td>
                                        <td>
                                            ${item.progressDocumentKinds
                                                .map(kind => `<span class="badge bg-info d-block mb-1" style="font-size: 1.5rem;">${kind}</span>`)
                                                .join('')}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge ${badgeClass}" style="font-size: 2rem;">${item.percentage || '0%'}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger d-block mb-1" style="font-size: 1.5rem;">
                                                Total Dokumen: ${docsSum}
                                            </span>

                                            <span class="badge d-block mb-1" style="background-color: #fffd19; color: #000; font-size: 1.5rem;">
                                                Dokumen Release: ${releasedDocs}
                                            </span>

                                            <span class="badge badge-warning d-block" style="font-size: 1.5rem;">
                                                Dokumen Non-Release: ${nullDocs}
                                            </span>
                                        </td>

                                        <td>
                                            <a href="/newreports/${item.id}" class="btn btn-primary d-block w-100 mb-1">View</a>

                                            ${downloadForm.replace('btn ', 'btn d-block w-100 mb-1 ')}

                                            ${deleteReportButton.replace('btn ', 'btn d-block w-100 mb-1 ')}

                                            ${doubleDetectorButton.replace('btn ', 'btn d-block w-100 mb-1 ')}
                                        </td>
                                    </tr>
                                `);
                                });

                                $('#totalPercentage').text(overallPercentage.toFixed(2) + '%');
                                $('#totalDocs').text(totalDocs);
                                $('#totalReleasedDocs').text(totalReleasedDocs);
                                $('#totalNullDocs').text(totalNullDocs);
                            } else {
                                $('#dashboardTable tbody').append(`
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data</td>
                                </tr>
                            `);
                                $('#totalPercentage').text('0%');
                                $('#totalDocs').text('0');
                                $('#totalReleasedDocs').text('0');
                                $('#totalNullDocs').text('0');
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terjadi kesalahan saat mengambil data!'
                            });
                        }
                    });
                }
            }

            function updateDashboard() {
                var projectId = $('#project').val();
                var projectTitle = $('#project').find('option:selected').text();
                var deadlineDate = $('#deadlineDate').val();

                if (!projectId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Project!',
                        text: 'Silakan pilih project terlebih dahulu.'
                    });
                    return;
                }

                fetchDashboardData(projectId, projectTitle, deadlineDate);
            }

            $('#project').change(updateDashboard);
            $('#viewDeadlineBtn').click(updateDashboard);

            var $projectSelect = $('#project');
            var firstOption = $projectSelect.find('option:nth-child(2)');
            if (firstOption.length) {
                $projectSelect.val(firstOption.val()).trigger('change');
            }
        });
    </script>


    <script>
        document.getElementById('internalButton').addEventListener('click', function() {

            Swal.fire({
                title: 'Enter Password',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    if (password === '12345') {
                        // Save the status to the session
                        return $.ajax({
                            url: '{{ route('set.internalon') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                location.reload();
                                // Reveal the hidden elements
                                document.querySelectorAll('.badge-warning.d-none').forEach(
                                    element => {
                                        element.classList.remove('d-none');
                                    });
                                document.querySelectorAll('.badge-success.d-1').forEach(
                                    element => {
                                        element.classList.add('d-none');
                                    });
                            },
                            error: function() {
                                Swal.showValidationMessage('Failed to set session');
                            }
                        });
                    } else {
                        Swal.showValidationMessage('Incorrect password');
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Password correct, internal details revealed.',
                        icon: 'success'
                    });
                }
            });
        });

        document.getElementById('internalOffButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Confirmation',
                text: 'Are you sure you want to turn off internal details?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, turn off',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Save the status to the session
                    return $.ajax({
                        url: '{{ route('set.internaloff') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            location.reload();
                            // Reveal the hidden elements
                            document.querySelectorAll('.badge-warning').forEach(element => {
                                element.classList.add('d-none');
                            });
                            document.querySelectorAll('.badge-success.d-1.d-none').forEach(
                                element => {
                                    element.classList.remove('d-none');
                                });
                        },
                        error: function() {
                            Swal.showValidationMessage('Failed to set session');
                        }
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Internal details turned off.',
                        icon: 'success'
                    });
                }
            });
        });

        $(document).ready(function() {
            // Handle form submission for Download Laporan All Unit
            $('#downloadAllForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default submission

                const projectId = $('#project').val(); // Get the selected project ID
                const projectTitle = $('#project').find('option:selected')
                    .text(); // Get the selected project title

                if (!projectId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Project!',
                        text: 'Silakan pilih project terlebih dahulu sebelum mendownload laporan.'
                    });
                    return;
                }

                // Set the form action dynamically
                const downloadUrl =
                    "{{ route('newreports.downloadlaporanall', ['project' => 'PROJECT_ID']) }}".replace(
                        'PROJECT_ID', projectId);
                $(this).attr('action', downloadUrl);

                // Show confirmation dialog
                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    text: `Laporan untuk project "${projectTitle}" akan didownload`,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, download it!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form programmatically
                        this.submit();
                        Swal.fire('Terdownload!', 'Laporan telah didownload.', 'success');
                    }
                });
            });

            // ... (rest of your existing JavaScript code remains unchanged)


            $('#downloadAllFormrevnol').on('submit', function(e) {
                e.preventDefault(); // Prevent default submission

                const projectId = $('#project').val(); // Get the selected project ID
                const projectTitle = $('#project').find('option:selected')
                    .text(); // Get the selected project title

                if (!projectId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Project!',
                        text: 'Silakan pilih project terlebih dahulu sebelum mendownload laporan.'
                    });
                    return;
                }

                // Set the form action dynamically
                const downloadUrl =
                    "{{ route('newreports.downloadlaporanallrevnol', ['project' => 'PROJECT_ID']) }}"
                    .replace('PROJECT_ID', projectId);
                $(this).attr('action', downloadUrl);

                // Show confirmation dialog
                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    text: `Laporan untuk project "${projectTitle}" akan didownload`,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, download it!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form programmatically
                        this.submit();
                        Swal.fire('Terdownload!', 'Laporan telah didownload.', 'success');
                    }
                });
            });

            // ... (rest of your existing JavaScript code remains unchanged)



        });
    </script>
@endpush
