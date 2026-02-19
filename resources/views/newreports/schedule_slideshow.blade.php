@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left shadow-sm rounded">
                        <li class="breadcrumb-item"><a href="{{ route('newreports.index') }}" class="text-primary">List Unit &
                                Project</a></li>
                        <li class="breadcrumb-item active">Page Monitoring Dokumen (Gantt Chart)</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div id="download-decision-container" data-downloaddecision="{{ $download }}"></div>
    <div id="startendContainer" data-start-date="{{ $start_date }}" data-end-date="{{ $end_date }}"></div>

    <div class="card card-danger card-outline shadow-lg">
        <div class="card-header bg-danger text-white d-flex align-items-center">
            <div class="card-tools mr-3">
                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <h3 class="card-title text-bold mb-0">
                Page Monitoring Dokumen (Gantt Chart)
                <span class="badge badge-light ml-2" id="project-title-badge">
                    @if (!empty($project))
                        {{ $project }}
                    @else
                        No Project Selected
                    @endif
                </span>
            </h3>
        </div>
        <div class="card-header border-bottom-0">
            <div class="form-group mb-0">
                <label for="projectSelector" class="form-label text-muted">Select Project:</label>
                <select id="projectSelector" class="form-control form-control-lg">
                    @php
                        // Ambil project pertama jika $project kosong
                        $firstProject = !empty($project) ? $project : collect($projectsData)->keys()->first() ?? '';
                    @endphp

                    @foreach ($projectsData as $projectName => $projectData)
                        <option value="{{ $projectName }}" {{ $projectName == $firstProject ? 'selected' : '' }}>
                            {{ $projectName }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body" id="content-wrapper-arif">
            <div id="ganttContainer" style="min-height: 500px;">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <p class="text-muted">Please select a project to view the Gantt chart.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
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
        (function(H) {
            H.wrap(H.Tooltip.prototype, 'getLabel', function(proceed) {
                const justCreated = !this.label;
                const result = proceed.call(this);

                if (justCreated) {
                    const label = this.label.element;
                    label.style.userSelect = 'text'; // Membuat teks bisa diseleksi
                    label.style.webkitUserSelect = 'text'; // Support untuk Webkit
                    label.style.msUserSelect = 'text'; // Support untuk IE
                    label.style.cursor = 'text'; // Mengubah cursor menjadi text
                    label.addEventListener('mousedown', e => {
                        e.stopPropagation(); // Mencegah event menyebar
                    });
                }

                return result;
            });
        })(Highcharts);
    </script>

    <script>
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

        document.addEventListener('DOMContentLoaded', function() {
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
            // === TAMBAHAN: ROTASI OTOMATIS TIAP 1 MENIT ===
            const projectOptions = Array.from(projectSelector.options)
                .map(opt => opt.value)
                .filter(val => val && val !== '');

            if (projectOptions.length > 1) {
                let currentIndex = projectOptions.indexOf(projectSelector.value);
                if (currentIndex === -1) currentIndex = 0;

                setInterval(() => {
                    currentIndex = (currentIndex + 1) % projectOptions.length;
                    const nextProject = projectOptions[currentIndex];

                    projectSelector.value = nextProject;
                    projectTitleBadge.textContent = nextProject;

                    loadGanttChart(nextProject, downloadDecision);
                }, 60000); // 1 menit = 60000 ms
            }

            // Hentikan rotasi jika user pilih manual
            projectSelector.addEventListener('change', function() {
                // Tidak perlu clearInterval karena setInterval tetap jalan
                // Tapi kita biarkan — user pilih manual = tetap tampilkan project itu
                // Rotasi otomatis tetap jalan di background (tidak ganggu)
            });
            // === AKHIR TAMBAHAN ===

            // Event listener for project selection changes
            if (projectSelector) {
                projectSelector.addEventListener('change', function() {
                    loadGanttChart(this.value, downloadDecision);
                });
            }
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
                url: `/ganttchart/hasil/chart/onehour${queryParams}`,
                type: 'GET',
                success: function(projectData) {
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
                                pointPadding: 0.3
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
                            formatter: function() {
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
                        series: [{
                                name: `${projectName} Project Rencana`,
                                data: formattedData,
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        fontSize: '10px',
                                        fontWeight: 'bold',
                                        color: '#000000'
                                    },
                                    formatter: function() {
                                        return `${this.point.completed.amount * 100}%`;
                                    }
                                },
                                pointWidth: 20 // Example value, adjust as needed. Make sure it's the same or slightly different from the first series

                            },
                            {
                                name: `${projectName} Project Realisasi`,
                                data: formattedData2,
                                pointPlacement: 0.5,
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        fontSize: '10px',
                                        fontWeight: 'bold',
                                        color: '#000000'
                                    },
                                    formatter: function() {
                                        return `${(this.point.completed.amount * 100).toFixed(2)}% ${this.point.sinkronstatus}`;
                                    }
                                },
                                pointWidth: 20 // Example value, adjust as needed. Make sure it's the same or slightly different from the first series

                            }
                        ],
                        xAxis: {
                            currentDateIndicator: true,
                            scrollbar: {
                                enabled: true
                            },
                            min: minDate,
                            max: maxDate,
                            events: {
                                afterSetExtremes: function() {
                                    if (this.min === minDate && this.max === maxDate) {}
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
                                formatter: function() {
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
                error: function() {
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

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endpush
