{{-- resources/views/newreports/indexlevel/index.blade.php --}}

@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('newreports.indexlevel') }}">List Level & Project</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="card card-danger card-outline shadow-lg border-danger">
        <!-- Header Merah Putih -->
        <div
            class="card-header bg-gradient-danger text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
            <h3 class="card-title text-bold mb-2 mb-md-0">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Dashboard Monitoring Dokumen (Fase)
            </h3>
            <div class="card-tools mt-2 mt-md-0">
                <a href="{{ route('newreports.monitor') }}" class="btn btn-light btn-sm shadow-sm">
                    <i class="fas fa-chart-bar mr-1"></i> Monitor Detail
                </a>
                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- Dropdown Project -->
            <div class="form-group row align-items-end mb-4">
                <label for="projectSelect" class="col-md-2 col-form-label text-md-right font-weight-bold text-danger">
                    Pilih Project:
                </label>
                <div class="col-md-6">
                    <select id="projectSelect" class="form-control form-control-lg select2" style="width: 100%;">
                        <option value="">-- Pilih Project --</option>
                        @foreach ($projects as $p)
                            <option value="{{ $p->id }}" {{ $p->id == $selectedProjectId ? 'selected' : '' }}>
                                {{ $p->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Loading -->
            <div id="loadingState" class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-4x text-danger"></i>
                <p class="mt-3 text-muted h5">Memuat data project...</p>
            </div>

            <!-- Dashboard Content -->
            <div id="dashboardContent" style="display: none;">
                <div class="text-center my-5">
                    <img src="{{ asset('images/logo-inka.png') }}" alt="Logo" class="img-fluid"
                        style="max-height: 90px;">
                    <h2 class="mt-4 text-danger font-weight-bold">MONITORING DOKUMEN PER FASE</h2>
                    <p class="text-muted lead">Update: <strong id="reportDate"></strong></p>
                </div>

                <div class="text-center mb-5">
                    <h3 class="text-danger font-weight-bold">
                        <i class="fas fa-cubes mr-2"></i>
                        PROJECT: <span id="projectName" class="text-uppercase"></span>
                    </h3>
                </div>

                <div class="row" id="levelCardsContainer">
                    <!-- Card di-generate via JS -->
                </div>
            </div>

            <!-- No Project Selected -->
            <div id="noProjectState" class="text-center py-5" style="display: none;">
                <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                <h4 class="text-muted">Silakan pilih project untuk melihat monitoring</h4>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css"
        rel="stylesheet" />
    <style>
        .bg-gradient-danger {
            background: linear-gradient(87deg, #e74c3c, #c0392b) !important;
        }

        .border-left-danger-thick {
            border-left: 8px solid #dc3545 !important;
        }

        .progress-bar-custom {
            background: linear-gradient(90deg, #dc3545, #e74c3c);
        }

        .text-danger-strong {
            color: #c0392b !important;
        }

        .card-shadow-custom {
            box-shadow: 0 10px 20px rgba(220, 53, 69, 0.15) !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "-- Pilih Project --",
                allowClear: true
            });

            // Warna progress: merah tua → merah → oranye → merah muda (semakin tinggi % semakin terang)
            const getProgressClass = (percent) => {
                if (percent >= 90) return 'bg-danger'; // hampir selesai → merah terang
                if (percent >= 70) return 'bg-danger'; // masih merah
                if (percent >= 40) return 'bg-warning text-dark';
                return 'bg-danger'; // belum banyak → merah pekat
            };

            const getProgressGradient = (percent) => {
                if (percent >= 90) return 'bg-gradient-danger';
                if (percent >= 70) return 'bg-danger';
                if (percent >= 40) return 'bg-warning';
                return 'bg-danger';
            };

            const loadDashboard = (projectId) => {
                if (!projectId) {
                    $('#noProjectState').show();
                    $('#loadingState, #dashboardContent').hide();
                    return;
                }

                $('#loadingState').show();
                $('#dashboardContent, #noProjectState').hide();

                $.get("{{ url('newreports/level-data') }}/" + projectId)
                    .done(function(res) {
                        $('#reportDate').text(res.date);
                        $('#projectName').text(res.project.title.toUpperCase());

                        const container = $('#levelCardsContainer');
                        container.empty();

                        res.levels.forEach(level => {
                            const percentReleased = level.total > 0 ? Math.round((level.released /
                                level.total) * 100) : 0;

                            let kindsHtml = '';
                            const sorted = Object.keys(level.kinds).sort((a, b) => level.kinds[b] -
                                level.kinds[a]);

                            sorted.forEach(kindId => {
                                const total = level.kinds[kindId];
                                if (total <= 0) return;

                                const name = kindId == 0 ?
                                    '<i class="fas fa-question-circle"></i> Belum Ada Jenis' :
                                    (res.document_kinds[kindId] || 'Unknown');

                                const releasedKind = level.total > 0 ? Math.round((level
                                    .released / level.total) * total) : 0;
                                const unreleasedKind = total - releasedKind;

                                kindsHtml += `
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <div class="text-left">
                                        <div class="font-weight-medium ${kindId == 0 ? 'text-warning' : ''}">${name}</div>
                                        <small class="text-success">Released: <strong>${releasedKind}</strong></small>
                                        <span class="mx-2 text-muted">•</span>
                                        <small class="text-danger">Unreleased: <strong>${unreleasedKind}</strong></small>
                                    </div>
                                    <span class="badge badge-pill badge-light text-danger border border-danger px-3 py-2">
                                        ${total}
                                    </span>
                                </div>`;
                            });

                            if (!kindsHtml) {
                                kindsHtml = `<div class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i><br>Belum ada dokumen
                                </div>`;
                            }

                            const progressClass = getProgressClass(percentReleased);

                            container.append(`
                            <div class="col-xl-4 col-lg-6 col-md-6 col-12 mb-4">
                                <div class="card border-left-danger-thick card-shadow-custom h-100">
                                    <div class="card-header bg-gradient-danger text-white text-center py-3">
                                        <h5 class="mb-0 font-weight-bold">
                                            <i class="fas fa-layer-group mr-2"></i> ${level.level_title}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-4">
                                            <h2 class="font-weight-bold text-danger">${level.total}</h2>
                                            <p class="text-muted mb-2">Total Dokumen</p>

                                            <div class="progress mb-3" style="height: 32px;">
                                                <div class="progress-bar ${progressClass} progress-bar-striped font-weight-bold"
                                                     style="width: ${percentReleased}%">
                                                    ${percentReleased}% Released
                                                </div>
                                            </div>

                                            <div class="row text-center mt-3">
                                                <div class="col-6">
                                                    <h5 class="text-success font-weight-bold">${level.released}</h5>
                                                    <small class="text-muted">Released</small>
                                                </div>
                                                <div class="col-6">
                                                    <h5 class="text-danger font-weight-bold">${level.unreleased}</h5>
                                                    <small class="text-muted">Unreleased</small>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="border-danger">

                                        <div class="small" style="max-height: 280px; overflow-y: auto;">
                                            ${kindsHtml}
                                        </div>

                                        <div class="text-center mt-4">
                                            <a href="${level.view_url}"
                                               class="btn btn-danger btn-lg rounded-pill px-5 shadow">
                                                <i class="fas fa-eye mr-2"></i> Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>`);
                        });

                        $('#loadingState').hide();
                        $('#dashboardContent').show();
                    })
                    .fail(() => {
                        alert('Gagal memuat data project.');
                        $('#loadingState').hide();
                        $('#noProjectState').show();
                    });
            };

            // Auto load project
            const params = new URLSearchParams(window.location.search);
            let projectId = params.get('project');

            if (!projectId && {{ $projects->count() }} > 0) {
                projectId = "{{ $projects->first()->id }}";
                const url = new URL(window.location);
                url.searchParams.set('project', projectId);
                window.history.replaceState({}, '', url);
            }

            if (projectId) {
                $('#projectSelect').val(projectId);
                loadDashboard(projectId);
            } else {
                $('#noProjectState').show();
            }

            $('#projectSelect').on('change', function() {
                const id = $(this).val();
                const url = new URL(window.location);
                id ? url.searchParams.set('project', id) : url.searchParams.delete('project');
                window.history.pushState({}, '', url);
                loadDashboard(id);
            });
        });
    </script>
@endpush
