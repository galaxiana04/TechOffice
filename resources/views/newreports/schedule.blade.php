@extends('layouts.universal')

@section('container2')
    <div class="content-header py-3 py-md-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6 col-12 mb-2 mb-sm-0">
                    <h1 class="m-0 text-dark font-weight-bold header-title">Monitoring Dokumen</h1>
                    <p class="text-muted small mb-0">Visualisasi timeline dan progres proyek (Gantt Chart)</p>
                </div>
                <div class="col-sm-6 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('newreports.index') }}" class="text-muted hover-primary">
                                <i class="fas fa-home mr-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-primary font-weight-bold">Gantt Chart</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    {{-- Hidden Data Containers --}}
    <div id="download-decision-container" data-downloaddecision="{{ $download ?? 'false' }}"></div>
    <div id="startendContainer" data-start-date="{{ $start_date ?? '' }}" data-end-date="{{ $end_date ?? '' }}"></div>

    <div class="container-fluid pb-5">
        <div class="card card-modern">

            {{-- Header Card --}}
            <div class="header-gradient">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    {{-- Bagian Kiri: Judul & Badge --}}
                    <div class="d-flex align-items-center flex-grow-1" style="min-width: 0;">
                        <div class="icon-circle-bg d-none d-sm-flex">
                            <i class="fas fa-tasks text-white"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <h5 class="font-weight-bold m-0 text-white header-chart-title">
                                    Page Monitoring Dokumen (Gantt Chart)
                                </h5>
                                <span class="badge badge-modern-light" id="project-title-badge">
                                    @if (!empty($project))
                                        {{ $project }}
                                    @else
                                        No Project Selected
                                    @endif
                                </span>
                            </div>
                            <p class="m-0 text-white-80 small d-none d-sm-block mt-1">
                                Monitoring timeline dokumen
                            </p>
                        </div>
                    </div>

                    {{-- Bagian Kanan: Tombol Collapse --}}
                    <div class="ml-2 ml-sm-3 mt-2 mt-sm-0">
                        <button type="button" class="btn btn-sm btn-header-action" data-card-widget="collapse"
                            title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Filter Section --}}
            <div class="bg-light p-3 p-md-4 border-bottom">
                <div class="row g-3 align-items-end">

                    {{-- 1. Filter Date dengan Reset --}}
                    <div class="col-12 col-lg-5 mb-3 mb-lg-0">
                        <form action="{{ route('newreports.target') }}" method="GET" id="filterForm">
                            <label for="end_date" class="label-modern">
                                <i class="fas fa-calendar-alt mr-1"></i> Filter Tanggal Akhir
                            </label>

                            {{-- Input Hidden agar Project tidak hilang saat submit tanggal --}}
                            <input type="hidden" name="project" id="hiddenProjectInput" value="{{ $project ?? '' }}">

                            <div class="input-group">
                                <input type="date" id="end_date" name="end_date" class="form-control form-control-modern"
                                    value="{{ $end_date ?? '' }}">

                                {{-- Tombol Reset --}}
                                <button type="button"
                                    class="btn btn-light border btn-reset {{ empty($end_date) ? 'd-none' : '' }}"
                                    id="resetDateBtn" onclick="resetDate()" title="Reset Tanggal">
                                    <i class="fas fa-times text-muted"></i>
                                </button>

                                <button type="submit" class="btn btn-gradient">
                                    <i class="fas fa-filter mr-1"></i>
                                    <span class="d-none d-sm-inline">Terapkan</span>
                                    <span class="d-inline d-sm-none">Apply</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- 2. Project Selector --}}
                    <div class="col-12 col-lg-7">
                        <div class="form-group mb-0">
                            <label for="projectSelector" class="label-modern">
                                <i class="fas fa-project-diagram mr-1 text-primary"></i> Pilih Proyek
                            </label>
                            <select id="projectSelector" class="form-control" name="project">
                                <option value="">-- Pilih Proyek --</option>
                                @foreach ($projectsData as $projectName => $projectData)
                                    <option value="{{ $projectName }}" {{ (isset($project) && $project == $projectName) ? 'selected' : '' }}>
                                        {{ $projectName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Chart Container Wrapper --}}
            <div class="card-body p-0" id="content-wrapper-arif">
                <div class="chart-scroll-wrapper">
                    <div id="ganttContainer">
                        <div class="d-flex justify-content-center align-items-center h-100 py-5" id="emptyState">
                            <div class="text-center text-muted px-3">
                                <i class="fas fa-chart-bar fa-3x mb-3 text-gray-300"></i>
                                <p class="mb-0">Silakan pilih proyek untuk menampilkan Gantt Chart.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* --- GENERAL STYLE --- */
        body {
            font-family: 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .card-modern {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            background: #fff;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .header-gradient {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            padding: 1.25rem 1.5rem;
            color: white;
        }

        .label-modern {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            display: block;
        }

        /* --- INPUT & BUTTONS --- */
        .form-control-modern {
            border-radius: 0.375rem;
            border: 1px solid #d1d5db;
            padding: 0.5rem 0.75rem;
            height: 42px;
            font-size: 0.875rem;
            color: #1f2937;
            background-color: #fff;
            transition: all 0.15s ease-in-out;
        }

        .form-control-modern:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
            outline: none;
        }

        .input-group .form-control-modern {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group .btn {
            z-index: 2;
        }

        .input-group .btn-light {
            border-left: 0;
            border-right: 0;
            border-color: #d1d5db;
        }

        .btn-gradient {
            background: linear-gradient(to right, #ef4444, #dc2626);
            border: none;
            color: white !important;
            height: 42px;
            font-weight: 600;
            padding: 0 1.25rem;
            border-top-right-radius: 0.375rem;
            border-bottom-right-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.2);
        }

        .btn-gradient:hover {
            filter: brightness(110%);
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(220, 38, 38, 0.3);
        }

        /* --- SELECT2 CUSTOM STYLE --- */
        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 42px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 0.35rem 0.75rem;
            display: flex;
            align-items: center;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 0;
            color: #1f2937;
            font-size: 0.875rem;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }

        /* Fokus Select2 Merah */
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
        }

        /* --- CHART LAYOUT (FIT TO SCREEN) --- */
        .chart-container-wrapper {
            width: 100%;
            overflow: hidden;
        }

        #ganttContainer {
            width: 100%;
            min-width: 0;
            min-height: 500px;
        }

        .icon-circle-bg {
            background-color: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        .badge-modern-light {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.35em 0.8em;
            border-radius: 9999px;
            font-weight: 500;
            font-size: 0.75rem;
            letter-spacing: 0.025em;
        }

        /* --- MOBILE RESPONSIVENESS --- */
        @media (max-width: 767.98px) {
            .header-gradient {
                padding: 1rem;
            }

            .header-title {
                font-size: 1.25rem;
            }

            .icon-circle-bg {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
            }

            .form-control-modern,
            .btn-gradient,
            .select2-container .select2-selection--single {
                height: 45px !important;
            }

            .select2-container .select2-selection--single .select2-selection__arrow {
                height: 43px !important;
            }

            .col-md-5,
            .col-md-7 {
                margin-bottom: 1rem;
            }
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/gantt/modules/gantt.js"></script>
    <script src="https://code.highcharts.com/gantt/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/modules/oldie.js"></script>
    <script src="https://code.highcharts.com/modules/pattern-fill.js"></script>

    <!-- Penambahan untuk membuat tooltip selectable -->
    <script>
        (function (H) {
            H.wrap(H.Tooltip.prototype, 'getLabel', function (proceed) {
                const justCreated = !this.label;
                const result = proceed.call(this);
                if (justCreated) {
                    const label = this.label.element;
                    label.style.userSelect = 'text';
                    label.style.webkitUserSelect = 'text';
                    label.style.msUserSelect = 'text';
                    label.style.cursor = 'text';
                    label.addEventListener('mousedown', e => e.stopPropagation());
                }
                return result;
            });
        })(Highcharts);
    </script>

    <script>
        function resetDate() {
            // 1. Kosongkan tampilan input tanggal
            document.getElementById('end_date').value = '';

            // 2. Ambil proyek yang sedang aktif dari Select2
            const projectSelector = $('#projectSelector');
            const currentProject = projectSelector.val();

            // 3. Redirect halaman
            if (currentProject) {
                // Jika ada proyek, reload halaman HANYA dengan parameter project (tanggal hilang)
                window.location.href = "{{ route('newreports.target') }}?project=" + encodeURIComponent(currentProject);
            } else {
                // Jika tidak ada proyek, reload ke halaman awal bersih
                window.location.href = "{{ route('newreports.target') }}";
            }
        }
        function adjustContentHeight() {
            const header = document.querySelector('.content-header');
            const footer = document.querySelector('.main-footer');
            const contentWrapper = document.getElementById('content-wrapper-arif');

            if (!header || !footer || !contentWrapper) return;

            const headerHeight = header.offsetHeight || 0;
            const footerHeight = footer.offsetHeight || 0;
            const windowHeight = window.innerHeight;

            const computedStyle = window.getComputedStyle(contentWrapper);
            const paddingTop = parseFloat(computedStyle.paddingTop) || 0;
            const paddingBottom = parseFloat(computedStyle.paddingBottom) || 0;

            const contentHeight = windowHeight - headerHeight - footerHeight - paddingTop - paddingBottom;
            contentWrapper.style.minHeight = `${contentHeight}px`;
        }

        function updateProjectBadge(projectName) {
            const badge = document.getElementById('project-title-badge');
            if (badge) {
                // Jika ada nama proyek, tampilkan. Jika tidak, tampilkan default.
                const text = projectName ? projectName : 'No Project Selected';
                badge.textContent = text;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            adjustContentHeight();
            window.addEventListener('resize', adjustContentHeight);

            const downloadDecisionContainer = document.getElementById('download-decision-container');
            const downloadDecision = downloadDecisionContainer?.dataset.downloaddecision || 'false';
            const projectSelector = document.getElementById('projectSelector');
            const projectTitleBadge = document.getElementById('project-title-badge');

            // Initial load if a project is already selected (e.g., after form submission)
            if (projectSelector && projectSelector.value) {
                loadGanttChart(projectSelector.value, downloadDecision);
            }

            // Event listener for project selection changes
            $('#projectSelector').on('change', function () {
                const selectedProject = $(this).val();

                // 1. Panggil fungsi update badge (INILAH YANG KURANG SEBELUMNYA)
                updateProjectBadge(selectedProject);

                // 2. Update input hidden agar fitur Reset Tanggal tetap jalan
                const hiddenInput = document.getElementById('hiddenProjectInput');
                if (hiddenInput) {
                    hiddenInput.value = selectedProject;
                }

                // 3. Load Chart
                loadGanttChart(selectedProject, downloadDecision);
            });
        });

        function loadGanttChart(projectName, downloaddecision) {
            Swal.fire({
                title: 'Loading Project Data',
                html: '<i class="fas fa-spinner fa-spin"></i> Please wait while we fetch the Gantt chart data for <b>' +
                    projectName + '</b>.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            // Ambil start_date dan end_date dari elemen #ganttContainer
            const startendContainer = document.getElementById('startendContainer');
            const startDate = startendContainer.dataset.startDate || null;
            const endDate = startendContainer.dataset.endDate || null;

            // Buat parameter tambahan jika start_date dan end_date tersedia
            let queryParams = `?projectName=${encodeURIComponent(projectName)}`;
            if (startDate) queryParams += `&start_date=${encodeURIComponent(startDate)}`;
            if (endDate) queryParams += `&end_date=${encodeURIComponent(endDate)}`;

            $.ajax({
                url: `/ganttchart/hasil/chart${queryParams}`,
                type: 'GET',
                success: function (projectData) {
                    if (!projectData || projectData.length === 0) {
                        Swal.close();
                        Highcharts.ganttChart('ganttContainer', {
                            title: {
                                text: 'Tidak ada data tersedia'
                            },
                            series: [],
                            xAxis: {
                                min: Date.UTC(2023, 0, 1),
                                max: Date.UTC(2023, 11, 31)
                            },
                            yAxis: {
                                scrollbar: {
                                    enabled: true
                                },
                                title: {
                                    text: 'Tasks'
                                }
                            },
                            credits: {
                                enabled: false
                            }
                        });
                        return;
                    }

                    const formattedData = [];
                    const formattedData2 = [];
                    let minDate = Infinity;
                    let maxDate = -Infinity;
                    const categories = [];

                    projectData.forEach(item => {
                        const startDate = Date.UTC(...item.start_plan);
                        const endDate = Date.UTC(...item.end_plan);

                        formattedData.push({
                            ...item,
                            start: startDate,
                            end: endDate,
                            color: item.color,
                            completed: item.completed,
                            y: 0
                        });
                        categories.push(item.name); // Tambahkan ke kategori untuk treegrid
                        minDate = Math.min(minDate, startDate);
                        maxDate = Math.max(maxDate, endDate);

                        if (item.start_real && item.end_real && item.color_real && item
                            .completed_real) {
                            const startReal = Date.UTC(...item.start_real);
                            const endReal = Date.UTC(...item.end_real);

                            formattedData2.push({
                                ...item,
                                start: startReal,
                                end: endReal,
                                color: item.color_real,
                                completed: item.completed_real,
                                sinkronstatus: item.sinkronstatus ?? "",
                                y: 1,
                                //pointPadding: 0.3
                            });

                            minDate = Math.min(minDate, startReal);
                            maxDate = Math.max(maxDate, endReal);
                        }
                    });

                    // Hitung tinggi chart
                    const itemHeight = 80; // Tinggi per item (rencana + realisasi)
                    const totalItems = formattedData.length + formattedData2.length;
                    const chartHeight = Math.max(400, totalItems * itemHeight + 150) *
                        1.2; // Tambahkan padding ekstra

                    Highcharts.ganttChart('ganttContainer', {
                        exporting: {
                            enabled: true,
                            buttons: {
                                contextButton: {
                                    menuItems: ['downloadXLS', 'downloadPDF', 'printChart', 'viewData',
                                        'hideData', 'viewFullscreen', 'downloadPNG'
                                    ]
                                }
                            }
                        },
                        lang: {
                            downloadXLS: "Download XLS",
                            downloadPNG: "Download PNG",
                            downloadPDF: "Download PDF",
                            viewData: "Lihat Data",
                            viewFullscreen: "Full View",
                            hideData: "Sembunyikan Data",
                            printChart: "Print"
                        },
                        chart: {
                            events: {
                                load() {

                                    chart.exportChart({
                                        type: 'application/pdf',
                                        filename: `${projectName}_Gantt_Chart_Automatically_Exported`
                                    });
                                }
                            },
                            height: chartHeight,
                            marginLeft: 250, // Margin kiri untuk label treegrid
                        },
                        title: {
                            text: `${projectName}`
                        },
                        tooltip: {
                            stickOnContact: true,
                            hideDelay: 6000,
                            formatter: function () {
                                var releasedCountAsync = this.point.counts.real_Released ? this
                                    .point.counts.real_Released - this.point.counts.Released : 0;
                                var unreleasedCountAsync = this.point.counts.real_Unreleased ? this
                                    .point.counts.real_Unreleased - this.point.counts.Unreleased :
                                    0;

                                var tooltipContent =
                                    `
                                                                                                                                                                <strong>${this.point.name}</strong><br>
                                                                                                                                                                <span>Rentang:</span> <b>${Highcharts.dateFormat('%e %b %Y', this.point.start)}</b> - 
                                                                                                                                                                <b>${Highcharts.dateFormat('%e %b %Y', this.point.end)}</b><br><br>
                                                                                                                                                                <strong>Sinkronisasi:</strong><br>
                                                                                                                                                                ✅ Rilis: <b>${this.point.counts.Released}</b><br>
                                                                                                                                                                ❌ Belum Rilis: <b>${this.point.counts.Unreleased}</b><br>`;

                                if (this.point.counts.real_Released !== undefined && this.point
                                    .counts.real_Unreleased !== undefined) {
                                    tooltipContent +=
                                        `
                                                                                                                                                                    <br><strong>Asinkronisasi:</strong><br>
                                                                                                                                                                    ✅ Rilis: <b>${releasedCountAsync}</b><br>
                                                                                                                                                                    ❌ Belum Rilis: <b>${unreleasedCountAsync}</b><br>`;
                                }

                                if (this.point.counts.Ontimereleased !== undefined && this.point
                                    .counts.Latereleased !== undefined) {
                                    tooltipContent +=
                                        `
                                                                                                                                                                    <br><strong>Detail Rilis (Dipakai untuk dokumen yang sudah sinkron):</strong><br>
                                                                                                                                                                    ⏳ Release Sesuai Master Schedule (Ontime): <b>${this.point.counts.Ontimereleased || 0}</b><br>
                                                                                                                                                                    ⏰ Release Tidak Sesuai Master Schedule (Late): <b>${this.point.counts.Latereleased || 0}</b><br>`;
                                }

                                if (this.point.counts.Ontimeunreleased !== undefined && this.point
                                    .counts.Lateunreleased !== undefined) {
                                    tooltipContent +=
                                        `
                                                                                                                                                                    <br><strong>Detail Belum Rilis (Dipakai untuk dokumen yang sudah sinkron):</strong><br>
                                                                                                                                                                    ⏳ Tidak Release Masih Sesuai Master Schedule (Ontime): <b>${this.point.counts.Ontimeunreleased || 0}</b><br>
                                                                                                                                                                    ⏰ Tidak Release Tidak Sesuai Master Schedule (Late): <b>${this.point.counts.Lateunreleased || 0}</b><br>`;
                                }

                                if (this.point.counts.Ontimereleased !== undefined &&
                                    this.point.counts.Latereleased !== undefined &&
                                    this.point.counts.Ontimeunreleased !== undefined &&
                                    this.point.counts.Lateunreleased !== undefined) {
                                    const pembilang = this.point.counts.Ontimereleased;
                                    const penyebut = this.point.counts.Ontimereleased + this.point
                                        .counts.Latereleased;
                                    const percentage = penyebut > 0 ? (pembilang / penyebut) * 100 :
                                        0;
                                    const roundedPercentage = Math.round(percentage);
                                    tooltipContent +=
                                        `
                                                                                                                                                                    <br><strong>Presentase (Dipakai untuk dokumen yang sudah sinkron):</strong><br>
                                                                                                                                                                    📊 <b>(${pembilang} / ${penyebut}) * 100% = ${roundedPercentage}%</b><br>`;
                                }

                                return tooltipContent;
                            }
                        },
                        // --- PERBAIKAN POSISI BAR DI SINI ---
                        series: [{
                            name: `${projectName} Project Rencana`,
                            data: formattedData,
                            pointPlacement: -0.15, // Rencana digeser sedikit ke ATAS
                            pointPadding: 0.25,    // Rencana dibuat sedikit lebih TIPIS
                            dataLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '10px',
                                    fontWeight: 'bold',
                                    color: '#000000'
                                },
                                formatter: function () {
                                    return `${this.point.completed.amount * 100}%`;
                                }
                            },
                            pointWidth: 20
                        }, {
                            name: `${projectName} Project Realisasi`,
                            data: formattedData2,
                            pointPlacement: 0.15,  // Realisasi digeser sedikit ke BAWAH
                            pointPadding: 0.2,     // Realisasi dibuat sedikit lebih TEBAL
                            dataLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '10px',
                                    fontWeight: 'bold',
                                    color: '#000000'
                                },
                                formatter: function () {
                                    return `${(this.point.completed.amount * 100).toFixed(2)}% ${this.point.sinkronstatus}`;
                                }
                            },
                            pointWidth: 20
                        }],
                        xAxis: {
                            currentDateIndicator: true,
                            scrollbar: {
                                enabled: true
                            },
                            min: minDate,
                            max: maxDate,
                            events: {
                                afterSetExtremes: function () {
                                    if (this.min === minDate && this.max === maxDate) { }
                                }
                            }
                        },
                        yAxis: {
                            scrollbar: {
                                enabled: true
                            },
                            type: 'treegrid', // Kembalikan treegrid
                            uniqueNames: true,
                            gapSize: 5,
                            reversedStacks: false,
                            grid: {
                                columns: [{
                                    title: {
                                        text: 'Part'
                                    },
                                    categories: categories // Gunakan kategori yang telah dikumpulkan

                                }]
                            },
                            title: {
                                text: 'Tasks'
                            },
                            labels: {
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    color: '#333333'
                                },
                                formatter: function () {
                                    return this.value;
                                }
                            },
                            gridLineColor: '#e6e6e6',
                            gridLineWidth: 1,
                            tickWidth: 1,
                            tickColor: '#cccccc'
                        },
                        navigator: {
                            enabled: false,
                            liveRedraw: false,
                            series: {
                                accessibility: {
                                    enabled: false
                                }
                            }
                        },
                        rangeSelector: {
                            enabled: true
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true,
                            align: 'right',
                            verticalAlign: 'top',
                            layout: 'vertical',
                            x: 0,
                            y: 100,
                            floating: true,
                            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) ||
                                'white',
                            borderColor: '#CCC',
                            borderWidth: 1,
                            shadow: true,
                            itemStyle: {
                                fontWeight: 'bold',
                                fontSize: '14px'
                            }
                        },
                        chart: {
                            height: null, // Biarkan tinggi menyesuaikan konten
                            turboThreshold: 5000,

                        }
                    });

                    if (downloaddecision === 'true') {
                        Highcharts.charts[Highcharts.charts.length - 1].exportChart({
                            type: 'application/pdf',
                            filename: `${projectName}_Gantt_Chart_Automatically_Exported`
                        });
                    }

                    Swal.close();
                },
                error: function () {
                    Swal.close();
                    Highcharts.ganttChart('ganttContainer', {
                        title: {
                            text: 'Tidak ada data tersedia'
                        },
                        series: [],
                        xAxis: {
                            min: Date.UTC(2023, 0, 1),
                            max: Date.UTC(2023, 11, 31)
                        },
                        yAxis: {
                            title: {
                                text: 'Tasks'
                            }
                        },
                        credits: {
                            enabled: false
                        }
                    });
                }
            });
        }
    </script>
@endpush