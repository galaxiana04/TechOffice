@extends('layouts.universal')

@section('container2')
    {{-- Breadcrumb Section --}}
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 bg-white px-4 py-2 rounded-md shadow-sm">
                <li class="inline-flex items-center">
                    <a href="{{ route('newreports.index') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-red-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                            </path>
                        </svg>
                        Dashboard
                    </a>
                </li>
            </ol>
        </nav>
    </div>
@endsection

@section('container3')
    {{-- Hidden Data Container --}}
    <div id="download-decision-container" data-downloaddecision="{{ $download }}" class="hidden"></div>

    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 pb-8">

        {{-- Main Card Wrapper --}}
        <div class="bg-white overflow-visible shadow-lg rounded-lg border-t-4 border-red-600 mb-6">

            {{-- Card Header: Title & Collapse --}}
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg leading-6 font-bold text-gray-900">
                    Dashboard <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-1"></span>
                </h3>
                <div class="flex items-center">
                    <button type="button" onclick="toggleElement('mainDashboardContent')"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            {{-- Card Body --}}
            <div id="mainDashboardContent" class="block">

                {{-- Dropdown Section --}}
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="relative inline-block text-left">
                        <button type="button" onclick="toggleDropdown('projectDropdownMenu')"
                            class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm"
                            id="projectDropdownBtn">
                            Pilih Proyek: <span id="selectedProject"
                                class="ml-1 font-bold">{{ array_key_first($projectsData) }}</span>
                            <i class="fas fa-chevron-down ml-2 mt-1"></i>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div id="projectDropdownMenu"
                            class="origin-top-left absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-50 max-h-64 overflow-y-auto">
                            <div class="py-1" role="menu" aria-orientation="vertical">
                                @foreach($projectsData as $projectName => $projectData)
                                    <a href="#"
                                        class="project-option block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                        role="menuitem" data-project="{{ $projectName }}"
                                        data-target-id="proj-{{ $loop->index }}" data-index="{{ $loop->index }}">
                                        {{ $projectName }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab Content Area --}}
                <div class="p-6 bg-gray-50">
                    @foreach($projectsData as $projectName => $projectData)
                        <div id="{{ $projectName }}" class="project-tab-pane {{ $loop->first ? 'block' : 'hidden' }}">

                            {{-- Content Header inside Tab --}}
                            <div class="w-full mb-4">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                                    {{-- Column 1: Ruang Rapat (DayPilot) --}}
                                    <div class="bg-white overflow-hidden shadow rounded-lg border-t-4 border-red-500 relative">
                                        <div
                                            class="px-4 py-3 border-b border-gray-200 flex justify-between items-center bg-white">
                                            <h3 class="text-lg font-medium leading-6 text-gray-900">Ruang Rapat Hari Ini</h3>
                                            <div class="flex space-x-2">
                                                <button type="button" class="text-gray-400 hover:text-gray-600"
                                                    onclick="toggleCardBody(this)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button type="button" class="text-gray-400 hover:text-red-500"
                                                    onclick="removeCard(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="p-4 card-body-wrapper">
                                            <div id="scheduler-{{ $projectName }}" style="height: 600px; width: 100%;"></div>
                                        </div>
                                    </div>

                                    {{-- Column 2: Progress Dokumen (Highcharts) --}}
                                    <div class="bg-white overflow-hidden shadow rounded-lg border-t-4 border-red-500 relative">
                                        <div
                                            class="px-4 py-3 border-b border-gray-200 flex justify-between items-center bg-white">
                                            <h3 class="text-lg font-medium leading-6 text-gray-900">Progress Dokumen</h3>
                                            <div class="flex space-x-2">
                                                <button type="button" class="text-gray-400 hover:text-gray-600"
                                                    onclick="toggleCardBody(this)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button type="button" class="text-gray-400 hover:text-red-500"
                                                    onclick="removeCard(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="p-4 card-body-wrapper">
                                            {{-- Container dengan ID Unik --}}
                                            <div id="ganttContainer-{{ $projectName }}" class="w-full"></div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/gantt/modules/gantt.js"></script>
    <script src="https://code.highcharts.com/gantt/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/modules/oldie.js"></script>
    <script src="https://code.highcharts.com/modules/pattern-fill.js"></script>
    <script src="{{ asset('schedulerdaypilot/js/daypilot/daypilot-all.min.css') }}" type="text/javascript"></script>
    <script src="{{ asset('schedulerdaypilot/js/jquery/jquery-1.9.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('schedulerdaypilot/js/daypilot/daypilot-all.min.js') }}" type="text/javascript"></script>

    {{-- UI Interaction Scripts --}}
    <script>
        function toggleElement(id) {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('hidden');
        }

        function toggleDropdown(id) {
            const el = document.getElementById(id);
            if (el.classList.contains('hidden')) el.classList.remove('hidden');
            else el.classList.add('hidden');
        }

        window.addEventListener('click', function (e) {
            const button = document.getElementById('projectDropdownBtn');
            const menu = document.getElementById('projectDropdownMenu');
            if (button && menu && !button.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        function toggleCardBody(btn) {
            const card = btn.closest('.shadow');
            if (card) {
                const body = card.querySelector('.card-body-wrapper');
                if (body) {
                    body.classList.toggle('hidden');
                    const icon = btn.querySelector('i');
                    if (icon) {
                        if (body.classList.contains('hidden')) {
                            icon.classList.remove('fa-minus');
                            icon.classList.add('fa-plus');
                        } else {
                            icon.classList.remove('fa-plus');
                            icon.classList.add('fa-minus');
                        }
                    }
                }
            }
        }

        function removeCard(btn) {
            const card = btn.closest('.shadow');
            if (card) card.remove();
        }
    </script>

    {{-- Script Tooltip Highcharts (Selectable) --}}
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
                    label.addEventListener('mousedown', e => {
                        e.stopPropagation();
                    });
                }
                return result;
            });
        })(Highcharts);
    </script>

    {{-- Main Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function adjustContentHeight() {
                const header = document.querySelector('.content-header');
                const footer = document.querySelector('footer');
                const contentWrapper = document.getElementById('content-wrapper-arif');

                if (!contentWrapper) return;

                const headerHeight = header ? header.offsetHeight : 0;
                const footerHeight = footer ? footer.offsetHeight : 0;
                const windowHeight = window.innerHeight;

                const contentHeight = windowHeight - headerHeight - footerHeight;
                if (contentHeight > 0) {
                    contentWrapper.style.minHeight = `${contentHeight}px`;
                }
            }

            adjustContentHeight();
            let resizeTimeout;
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(adjustContentHeight, 100);
            });

            const downloadDecisionContainer = document.getElementById('download-decision-container');
            const downloadDecision = downloadDecisionContainer?.dataset.downloaddecision ?? 'false';

            const dropdownItems = document.querySelectorAll('.project-option');
            const selectedProjectSpan = document.getElementById('selectedProject');

            if (selectedProjectSpan) {
                dropdownItems.forEach(item => {
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        const projectName = this.dataset.project;
                        if (!projectName) return;

                        selectedProjectSpan.textContent = projectName;
                        document.getElementById('projectDropdownMenu').classList.add('hidden');

                        const allPanes = document.querySelectorAll('.project-tab-pane');
                        allPanes.forEach(pane => {
                            pane.classList.remove('block');
                            pane.classList.add('hidden');
                        });

                        const selectedPane = document.getElementById(projectName);
                        if (selectedPane) {
                            selectedPane.classList.remove('hidden');
                            selectedPane.classList.add('block');
                        }

                        try {
                            loadGanttChart(projectName, downloadDecision);
                        } catch (error) {
                            console.error('Error saat memuat Gantt chart:', error);
                        }
                    });
                });
            }

            const firstProject = '{{ array_key_first($projectsData) }}';
            if (firstProject) {
                try {
                    loadGanttChart(firstProject, downloadDecision);
                } catch (error) {
                    console.error('Error saat memuat Gantt chart pertama:', error);
                }
            }
        });
    </script>

    {{-- Highcharts Gantt Logic (Updated to match Code 2) --}}
    <script>
        function loadGanttChart(projectName, downloaddecision) {

            // ID Container dinamis sesuai loop
            const containerId = `ganttContainer-${projectName}`;

            Swal.fire({
                title: 'Loading...',
                text: 'Memuat data proyek, harap tunggu.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `/ganttcharttenminutes/hasil/chart?projectName=${projectName}`,
                type: 'GET',
                success: function (projectData) {
                    if (!projectData || projectData.length === 0) {
                        Swal.close();
                        Highcharts.ganttChart(containerId, {
                            title: { text: 'Tidak ada data tersedia' },
                            series: [],
                            xAxis: { min: Date.UTC(2023, 0, 1), max: Date.UTC(2023, 11, 31) },
                            yAxis: { title: { text: 'Tasks' } },
                            credits: { enabled: false }
                        });
                        return;
                    }

                    const formattedData = [];
                    const formattedData2 = [];

                    // Gunakan Set agar nama kategori unik dan terurut sesuai data masuk
                    const uniqueNames = new Set();

                    let minDate = Infinity;
                    let maxDate = -Infinity;

                    projectData.forEach(item => {
                        const startDate = Date.UTC(...item.start_plan);
                        const endDate = Date.UTC(...item.end_plan);

                        // Kumpulkan kategori
                        uniqueNames.add(item.name);

                        formattedData.push({
                            ...item,
                            start: startDate,
                            end: endDate,
                            color: item.color,
                            completed: item.completed,
                            // Hapus y: 0 agar tidak merusak tree, biarkan Highcharts mapping otomatis berdasarkan kategori
                        });
                        minDate = Math.min(minDate, startDate);
                        maxDate = Math.max(maxDate, endDate);

                        if (item.start_real && item.end_real && item.color_real && item.completed_real) {
                            const startReal = Date.UTC(...item.start_real);
                            const endReal = Date.UTC(...item.end_real);

                            formattedData2.push({
                                ...item,
                                start: startReal,
                                end: endReal,
                                color: item.color_real,
                                completed: item.completed_real,
                                sinkronstatus: item.sinkronstatus ?? "",
                                // Hapus y: 1 juga
                            });
                            minDate = Math.min(minDate, startReal);
                            maxDate = Math.max(maxDate, endReal);
                        }
                    });

                    // Konversi Set ke Array untuk sumbu Y
                    const categoriesList = Array.from(uniqueNames);

                    // --- LOGIKA DARI KODE 2: Kalkulasi Tinggi Dinamis ---
                    const itemHeight = 80; // Tinggi estimasi per item
                    // totalItems dihitung dari jumlah baris (jumlah kategori unik)
                    const totalItems = categoriesList.length;
                    // Rumus tinggi chart adaptif
                    const chartHeight = Math.max(400, totalItems * itemHeight + 150) * 1.0;

                    // Konfigurasi Chart (Style dari Kode 2)
                    Highcharts.ganttChart(containerId, {
                        exporting: {
                            enabled: true,
                            buttons: {
                                contextButton: {
                                    menuItems: ['downloadXLS', 'downloadPDF', 'printChart', 'viewData', 'hideData', 'viewFullscreen', 'downloadPNG']
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
                            height: chartHeight, // GUNAKAN TINGGI DINAMIS
                            marginLeft: 250,     // Margin kiri agar tulisan panjang terbaca (dari Kode 2)
                            events: {
                                render: function () {
                                    const container = document.getElementById(containerId);
                                    if (container && container.scrollTop !== 0) { container.scrollTop = 0; }
                                }
                            }
                        },
                        title: { text: `${projectName}` },
                        // --- TOOLTIP DARI KODE 2 ---
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
                        // --- SERIES STYLING DARI KODE 2 ---
                        series: [{
                            name: `${projectName} Project Rencana`,
                            data: formattedData,
                            pointPlacement: -0.15,
                            pointPadding: 0.25,
                            dataLabels: {
                                enabled: true,
                                style: { fontSize: '10px', fontWeight: 'bold', color: '#000000' },
                                formatter: function () { return `${this.point.completed.amount * 100}%`; }
                            },
                            pointWidth: 20
                        }, {
                            name: `${projectName} Project Realisasi`,
                            data: formattedData2,
                            pointPlacement: 0.15,
                            pointPadding: 0.2,
                            dataLabels: {
                                enabled: true,
                                style: { fontSize: '10px', fontWeight: 'bold', color: '#000000' },
                                formatter: function () { return `${(this.point.completed.amount * 100).toFixed(2)}% ${this.point.sinkronstatus}`; }
                            },
                            pointWidth: 20
                        }],
                        xAxis: {
                            currentDateIndicator: true,
                            scrollbar: { enabled: true },
                            min: minDate,
                            max: maxDate,
                            events: { afterSetExtremes: function () { } }
                        },
                        // --- YAXIS CONFIG DARI KODE 2 ---
                        yAxis: {
                            scrollbar: { enabled: true },
                            type: 'treegrid', // PENTING: Gunakan treegrid
                            uniqueNames: true,
                            gapSize: 5,
                            reversedStacks: false,
                            grid: {
                                columns: [{
                                    title: { text: 'Part' },
                                    categories: categoriesList // Masukkan kategori dari Set
                                }]
                            },
                            title: { text: 'Tasks' },
                            labels: {
                                style: { fontSize: '14px', fontWeight: 'bold', color: '#333333' },
                                formatter: function () { return this.value; }
                            },
                            gridLineColor: '#e6e6e6',
                            gridLineWidth: 1,
                            tickWidth: 1,
                            tickColor: '#cccccc'
                        },
                        navigator: { enabled: false, liveRedraw: false, series: { accessibility: { enabled: false } } },
                        rangeSelector: { enabled: true },
                        credits: { enabled: false },
                        legend: {
                            enabled: true,
                            align: 'right',
                            verticalAlign: 'top',
                            layout: 'vertical',
                            x: 0,
                            y: 100,
                            floating: true,
                            backgroundColor: 'white',
                            borderColor: '#CCC',
                            borderWidth: 1,
                            shadow: true
                        },
                        chart: { turboThreshold: 5000 }
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
                    Highcharts.ganttChart(containerId, {
                        title: { text: 'Tidak ada data tersedia' },
                        series: [],
                        credits: { enabled: false }
                    });
                }
            });
        }
    </script>

    {{-- DayPilot Scheduler Logic --}}
    <script>
        @foreach($projectsData as $projectName => $projectData)
            var schedulerId = "scheduler-{{ $projectName }}";
            if (document.getElementById(schedulerId)) {
                var dp = new DayPilot.Scheduler(schedulerId);
                dp.startDate = DayPilot.Date.today();
                dp.days = 1;
                dp.businessBeginsHour = 6;
                dp.businessEndsHour = 23;
                dp.businessWeekends = true;
                dp.showNonBusiness = false;
                dp.timeHeaders = [
                    { groupBy: "Month", format: "dd/MM/yyyy", height: 40 },
                    { groupBy: "Day", format: "dd/MM/yyyy", height: 40 },
                    { groupBy: "Hour", format: "H:mm", height: 40 }
                ];
                dp.eventHeight = 75;
                dp.cellWidth = 60;
                dp.cellWidthMin = 60;
                dp.cellHeight = 75;
                dp.resources = [
                    @foreach($ruangrapat as $room)
                        @if($room != "All")
                            { name: "{{ $room }}", id: "{{ str_replace(['.', ' '], ['-', '_'], $room) }}" },
                        @endif
                    @endforeach
                        ];
                dp.events.list = @json($events);

                dp.onTimeRangeSelected = function (args) {
                    var name = prompt("New event name:", "Event");
                    dp.clearSelection();
                    if (!name) return;
                    var e = { start: args.start, end: args.end, id: DayPilot.guid(), text: name, resource: args.resource };
                    dp.events.add(e);
                    DayPilot.Http.ajax({
                        url: "/events/create",
                        data: e,
                        success: function (ajax) {
                            var response = ajax.data;
                            if (response && response.result) {
                                e.id = response.id;
                                dp.message("Created: " + response.message);
                            }
                        },
                        error: function (ajax) { dp.message("Saving failed"); }
                    });
                };

                dp.onEventClick = function (args) {
                    var eventId = args.e.id;
                    var url = "{{ route('events.show', ':id') }}".replace(':id', eventId);
                    window.location.href = url;
                };

                dp.bubble = new DayPilot.Bubble({
                    onLoad: function (args) {
                        var ev = args.source;
                        args.async = true;
                        var eventUrl = "{{ route('events.show', ':id') }}".replace(':id', ev.id());
                        setTimeout(function () {
                            args.html = `<div style='font-weight:bold'>${ev.text()}</div>
                                                    <div>Start: ${ev.start().toString("MM/dd/yyyy HH:mm")}</div>
                                                    <div>End: ${ev.end().toString("MM/dd/yyyy HH:mm")}</div>
                                                    <div><a href='${eventUrl}' target='_blank'>View Event</a></div>`;
                            args.loaded();
                        }, 500);
                    }
                });

                dp.onBeforeEventRender = function (args) {
                    var start = new DayPilot.Date(args.e.start);
                    var end = new DayPilot.Date(args.e.end);
                    var eventUrl = "{{ route('events.show', ':id') }}".replace(':id', args.e.id);
                    args.e.html = `<div class='calendar_white_event_inner' style='background-color: #e1f5fe; padding: 5px; border-radius: 5px; height: 100%;'>
                                                        <div style='font-weight:bold; color: #333;'>${args.e.text}</div>
                                                        <div style='color: #777;'>${start.toString("HH:mm")} - ${end.toString("HH:mm")}</div>
                                                        <div style='color: #777;'>Pic: ${args.e.pic}</div>
                                                        <div><a href='${eventUrl}' target='_blank'>View Event</a></div>
                                                    </div>`;
                    args.e.barColor = "#e1f5fe";
                    args.e.toolTip = "Event from " + start.toString("HH:mm") + " to " + end.toString("HH:mm");
                };

                dp.init();
            }
        @endforeach
    </script>
@endpush

@push('css')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .highcharts-container {
            width: 100% !important;
        }
    </style>
    
@endpush