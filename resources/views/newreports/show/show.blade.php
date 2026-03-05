@extends('layouts.universal')

@push('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=DM+Mono:ital,wght@0,400;0,500;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/dist/css/adminlte.min.css') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/INKAICON.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0-rc"></script>

    <style>
        /*
         * ══════════════════════════════════════════
         * COLOR SYSTEM — Unified Red Theme
         * ══════════════════════════════════════════
         * --primary:      #b52b1e  (crimson red — main accent)
         * --primary-dark: #8c1f15  (deep red — hover/dark)
         * --primary-mid:  #c0392b  (vivid red — mid tone)
         * --primary-light:#d63b2c  (light red)
         * --primary-soft: #fce8e6  (blush — subtle bg)
         * --primary-fade: #fdf5f4  (near-white red tint)
         * Dark header: #2a0f0c     (near-black red-tinted)
         */

        :root {
            --primary:       #b52b1e;
            --primary-mid:   #c0392b;
            --primary-light: #d63b2c;
            --primary-dark:  #8c1f15;
            --primary-soft:  #fce8e6;
            --primary-fade:  #fdf5f4;
            --success: #2a9d5c;
            --info:    #2779bd;
            --accent:  #e8773a;
            --bg-main: #f9f5f4;
            --bg-card: #ffffff;
            --text-primary:   #1c1c1e;
            --text-secondary: #48484a;
            --text-muted:     #8e8e93;
            --border:       #e8dedd;
            --border-light: #f5efee;
            --shadow-sm: 0 1px 4px rgba(181,43,30,.06);
            --shadow-md: 0 4px 16px rgba(181,43,30,.09);
            --shadow-lg: 0 10px 32px rgba(181,43,30,.12);
            --radius-sm: 5px;
            --radius-md: 9px;
            --radius-lg: 13px;
            --radius-xl: 17px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-main);
            color: var(--text-primary);
        }

        /* ── Breadcrumb ── */
        .modern-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 16px 0 8px;
            font-size: 13px;
            font-weight: 500;
        }
        .modern-breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            transition: color .2s;
            font-weight: 600;
        }
        .modern-breadcrumb a:hover { color: var(--primary-dark); }
        .modern-breadcrumb .sep { color: var(--text-muted); }
        .modern-breadcrumb .current { color: var(--text-secondary); }

        /* ── Stat Cards ── */
        .stat-cards-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #fff;
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--border);
            box-shadow: var(--shadow-sm);
            cursor: pointer;
            transition: transform .2s, box-shadow .2s;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(181,43,30,.16);
        }
        .stat-card-inner {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 18px 16px;
            flex: 1;
        }
        .stat-card-icon-wrap {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .stat-card-text { flex: 1; }
        .stat-card .stat-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--text-muted);
            display: block;
            margin-bottom: 5px;
        }
        .stat-card .stat-value {
            font-size: 32px;
            font-weight: 500;
            line-height: 1;
            font-family: 'DM Mono', monospace;
            display: block;
            letter-spacing: -0.02em;
        }
        .stat-card-bar { height: 3px; }

        /* ── Stat Card Variants (all in red family) ── */
        .stat-card.card-progress   { border-top: 3px solid #b52b1e; }
        .stat-card.card-unreleased { border-top: 3px solid #7b1a12; }
        .stat-card.card-released   { border-top: 3px solid #27ae60; }
        .stat-card.card-total      { border-top: 3px solid #c0392b; }

        .stat-card.card-progress .stat-card-icon-wrap   { background: #fce8e6; color: #b52b1e; }
        .stat-card.card-unreleased .stat-card-icon-wrap { background: #f5d5d2; color: #7b1a12; }
        .stat-card.card-released .stat-card-icon-wrap   { background: #d1fae5; color: #27ae60; }
        .stat-card.card-total .stat-card-icon-wrap      { background: #fdecea; color: #c0392b; }

        .stat-card.card-progress .stat-value   { color: #b52b1e; }
        .stat-card.card-unreleased .stat-value { color: #7b1a12; }
        .stat-card.card-released .stat-value   { color: #27ae60; }
        .stat-card.card-total .stat-value      { color: #c0392b; }

        .stat-card.card-progress .stat-card-bar   { background: #b52b1e; }
        .stat-card.card-unreleased .stat-card-bar { background: #7b1a12; }
        .stat-card.card-released .stat-card-bar   { background: #27ae60; }
        .stat-card.card-total .stat-card-bar      { background: #c0392b; }

        .stat-card.card-progress:hover   { border-color: #b52b1e; }
        .stat-card.card-unreleased:hover { border-color: #7b1a12; }
        .stat-card.card-released:hover   { border-color: #27ae60; }
        .stat-card.card-total:hover      { border-color: #c0392b; }

        @media (max-width: 900px) {
            .stat-cards-row { grid-template-columns: 1fr 1fr; }
        }

        /* ── Document Header ── */
        .doc-header {
            display: grid;
            grid-template-columns: 200px 1fr 280px;
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin-bottom: 20px;
            overflow: hidden;
            min-height: 130px;
        }
        .doc-header-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 20px;
            background: #ffffff;
            border-right: 1.5px solid var(--border);
        }
        .doc-header-logo img {
            max-width: 160px;
            max-height: 80px;
            object-fit: contain;
        }
        .doc-header-title {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 40px;
            background: #ffffff;
            border-right: 1.5px solid var(--border);
            position: relative;
        }
        .doc-header-title::before {
            content: '';
            position: absolute;
            left: 0; top: 12px; bottom: 12px;
            width: 4px;
            border-radius: 0 4px 4px 0;
            background: var(--primary);
        }
        .doc-header-title h1 {
            font-size: clamp(18px, 2.4vw, 32px);
            font-weight: 800;
            color: var(--primary);
            text-align: center;
            letter-spacing: .08em;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.25;
        }
        .doc-header-meta {
            display: flex;
            flex-direction: column;
            justify-content: stretch;
            background: #fdf7f6;
        }
        .doc-meta-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 18px;
            flex: 1;
            border-bottom: 1px solid var(--border-light);
            min-height: 43px;
        }
        .doc-meta-item:last-child { border-bottom: none; }
        .doc-meta-icon {
            width: 26px; height: 26px;
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px;
            flex-shrink: 0;
        }
        .doc-meta-icon.icon-project { background: #fce8e6; color: var(--primary); }
        .doc-meta-icon.icon-bagian  { background: #fdecea; color: var(--primary-mid); }
        .doc-meta-icon.icon-tanggal { background: #d1fae5; color: #2a9d5c; }
        .doc-meta-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .14em;
            color: var(--text-muted);
            min-width: 52px;
            flex-shrink: 0;
        }
        .doc-meta-value {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
        }
        @media (max-width: 768px) {
            .doc-header { grid-template-columns: 1fr; min-height: unset; }
            .doc-header-logo, .doc-header-title { border-right: none; border-bottom: 1.5px solid var(--border); }
        }

        /* ── Main Card ── */
        .modern-card {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            border: 1.5px solid #e0d4d2;
            box-shadow: 0 4px 20px rgba(181,43,30,.07), 0 1px 4px rgba(181,43,30,.04);
            overflow: hidden;
            margin-bottom: 24px;
        }

        /* ── Tab Nav — RED THEMED ── */
        .modern-tabs {
            display: flex;
            gap: 0;
            background: #fff;
            border-bottom: 2px solid var(--border);
            padding: 0 8px;
            overflow-x: auto;
        }
        .modern-tabs .nav-item { list-style: none; }
        .modern-tabs .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 13px 18px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all .2s;
            white-space: nowrap;
            letter-spacing: .01em;
        }
        .modern-tabs .nav-link:hover { color: var(--primary); }
        .modern-tabs .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 700;
        }

        /* ── Toolbar ── */
        .tab-toolbar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            background: var(--primary-fade);
            border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
        }

        /* ── Modern Buttons ── */
        .btn-modern {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }
        .btn-modern:hover { transform: translateY(-1px); filter: brightness(1.08); }
        .btn-modern:active { transform: translateY(0); }

        /* Download = biru profesional */
        .btn-download-mod { background: #1d6fa4; color: #fff; }

        /* Primary = crimson red */
        .btn-primary-mod { background: var(--primary); color: #fff; }

        /* Danger = merah terang */
        .btn-danger-mod  { background: #dc2626; color: #fff; }

        /* Success = green */
        .btn-success-mod { background: #27ae60; color: #fff; }

        /* Pending = amber/kuning — untuk unrelease/reset status */
        .btn-pending-mod {
            background: #fef3c7;
            color: #92400e;
            border: 1.5px solid #f59e0b;
        }
        .btn-pending-mod:hover {
            background: #fde68a;
            color: #78350f;
            border-color: #d97706;
            filter: none;
        }

        /* Info = rose-tinted ghost */
        .btn-info-mod    { background: var(--primary-soft); color: var(--primary-dark); border: 1px solid var(--border); }

        /* Ghost */
        .btn-ghost       { background: transparent; color: var(--text-secondary); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--primary-soft); color: var(--primary); border-color: var(--primary); }

        .btn-internal-toggle {
            width: 38px; height: 38px; padding: 0;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
        }

        /* ── Tables ── */
        .modern-table-wrap {
            padding: 0 4px 4px;
            overflow-x: auto;
        }
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
        }
        /* Red-tinted table header */
        .modern-table thead tr {
            background: #2a0f0c;
        }
        .modern-table thead th {
            padding: 11px 14px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: rgba(255,255,255,.6);
            border-bottom: none;
            white-space: nowrap;
        }
        .modern-table thead th:first-child { border-radius: 8px 0 0 0; padding-left: 16px; }
        .modern-table thead th:last-child  { border-radius: 0 8px 0 0; }

        .modern-table tbody tr {
            transition: background .15s;
            border-bottom: 1px solid var(--border-light);
        }
        .modern-table tbody tr:hover { background: #fdf7f6; }
        .modern-table tbody tr.checked { background: #fce8e6; }
        .modern-table tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-light);
            color: var(--text-primary);
            vertical-align: middle;
        }
        .modern-table tbody tr:last-child td { border-bottom: none; }

        /* ── Badges ── */
        .badge-modern {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
            letter-spacing: .02em;
        }
        .badge-released  { background: #d1fae5; color: #065f46; }
        .badge-unreleased{ background: #fce8e6; color: #7b1a12; }
        .badge-wip       { background: #fef3c7; color: #92400e; }
        .badge-info      { background: #fce8e6; color: #b52b1e; }
        .badge-neutral   { background: var(--border-light); color: var(--text-secondary); }

        .badge-combined-modern {
            display: inline-flex;
            border-radius: var(--radius-sm);
            overflow: hidden;
            border: 1px solid var(--border);
            font-size: 11px;
            margin-bottom: 4px;
        }
        .badge-combined-modern .part {
            padding: 3px 8px;
            font-weight: 600;
        }
        .badge-combined-modern .part-name   { background: #fce8e6; color: #7b1a12; }
        .badge-combined-modern .part-no     { background: #fdf5f4; color: #b52b1e; }
        .badge-combined-modern .part-status { background: #d1fae5; color: #065f46; }
        .badge-combined-modern .part-action { background: #f5d5d2; color: #8c1f15; cursor: pointer; }
        .badge-combined-modern .part-action:hover { background: #f0b9b5; }

        /* ── Checkbox ── */
        .check-icon { cursor: pointer; font-size: 16px; color: rgba(255,255,255,.5); transition: color .15s; }
        .check-icon:hover { color: rgba(255,255,255,.95); }

        .icheck-primary input[type=checkbox] { display: none; }
        .icheck-primary label {
            width: 17px; height: 17px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            display: inline-flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .15s;
        }
        .icheck-primary input:checked + label {
            background: var(--primary);
            border-color: var(--primary-dark);
        }
        .icheck-primary input:checked + label::after {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: white;
            font-size: 9px;
        }

        /* ── Action Buttons in Table ── */
        .action-group {
            display: flex; gap: 6px; flex-wrap: wrap;
        }
        .btn-action {
            padding: 5px 10px;
            border-radius: var(--radius-sm);
            font-size: 11px; font-weight: 600;
            border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 4px;
            transition: all .15s; text-decoration: none;
        }
        .btn-action:hover { filter: brightness(.9); }
        .btn-act-delete { background: #f5d5d2; color: #7b1a12; }
        .btn-act-edit   { background: #fce8e6; color: #b52b1e; }
        .btn-act-view   { background: #d1fae5; color: #065f46; }

        /* ── Section Header ── */
        .section-header {
            padding: 16px 20px 0;
            display: flex; align-items: center; justify-content: space-between;
        }
        .section-title {
            font-size: 15px; font-weight: 700;
            color: var(--text-primary);
        }

        /* ── Chart Card ── */
        .chart-card {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .chart-card-header {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-light);
            display: flex; align-items: center; justify-content: space-between;
            background: var(--primary-fade);
        }
        .chart-card-title {
            font-size: 13px; font-weight: 700;
            color: var(--primary-dark);
        }
        .chart-card-body { padding: 16px; }

        /* ── Progress bar ── */
        .progress-bar-modern {
            height: 6px; border-radius: 99px;
            background: var(--border-light); overflow: hidden;
        }
        .progress-bar-modern .fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
        }

        /* ── Form inputs ── */
        .modern-input {
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            transition: border-color .15s, box-shadow .15s;
            background: var(--bg-card);
        }
        .modern-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(181,43,30,.1);
        }

        /* ── Tab Content ── */
        .modern-tab-content { padding: 20px; }

        /* ── Utility ── */
        .d-none { display: none !important; }
        .font-mono { font-family: 'DM Mono', monospace; }

        /* ── Info pills ── */
        .info-pills {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .info-pill {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 10px 16px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
            min-width: 120px;
        }
        .info-pill-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--text-muted);
        }
        .info-pill-value {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .stat-cards-row { grid-template-columns: 1fr 1fr; }
        }

        /* ── DataTables override ── */
        div.dataTables_wrapper div.dataTables_filter input {
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 5px 10px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
        }
        div.dataTables_wrapper div.dataTables_paginate .paginate_button {
            border-radius: var(--radius-sm) !important;
            font-size: 12px;
        }
        div.dataTables_wrapper div.dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: white !important;
        }
        div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover {
            background: var(--primary-soft) !important;
            border-color: var(--border) !important;
            color: var(--primary) !important;
        }
    </style>
@endpush

@section('container2')
    <div id="encoded-datajenisdokumen" data-listjenisdokumen="{{ $jenisdokumen }}"></div>
    <div id="encoded-data" data-listprogressnodokumen="{{ $listprogressnodokumenencode }}"></div>

    <div class="container-fluid pt-2 pb-1">
        <nav class="modern-breadcrumb">
            <a href="{{ route('newreports.index') }}"><i class="fas fa-home"></i> List Unit &amp; Project</a>
            <span class="sep"><i class="fas fa-chevron-right" style="font-size:10px"></i></span>
            <a href="{{ route('newreports.show', ['newreport' => $newreport->id]) }}">List Dokumen</a>
        </nav>
    </div>
@endsection

@section('container3')
<div class="container-fluid pb-4">

    {{-- ── Document Header ── --}}
    <div class="doc-header">
        <div class="doc-header-logo">
            <img src="{{ asset('images/logo-inka.png') }}" alt="INKA Logo">
        </div>
        <div class="doc-header-title">
            <h1>DAFTAR DOKUMEN &amp; GAMBAR</h1>
        </div>
        <div class="doc-header-meta">
            <div class="doc-meta-item">
                <div class="doc-meta-icon icon-project"><i class="fas fa-folder"></i></div>
                <span class="doc-meta-label">Project</span>
                <span class="doc-meta-value">{{ ucwords(str_replace('-', ' ', $newreport->projectType->title)) }}</span>
            </div>
            <div class="doc-meta-item">
                <div class="doc-meta-icon icon-bagian"><i class="fas fa-layer-group"></i></div>
                <span class="doc-meta-label">Bagian</span>
                <span class="doc-meta-value">{{ ucfirst($newreport->unit) }}</span>
            </div>
            <div class="doc-meta-item">
                <div class="doc-meta-icon icon-tanggal"><i class="fas fa-calendar-day"></i></div>
                <span class="doc-meta-label">Tanggal</span>
                <span class="doc-meta-value">{{ date('d F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ── --}}
    <div class="stat-cards-row">
        <button class="stat-card card-progress" id="btn-total">
            <div class="stat-card-inner">
                <div class="stat-card-icon-wrap"><i class="fas fa-chart-line"></i></div>
                <div class="stat-card-text">
                    <span class="stat-label">Progress</span>
                    <span class="stat-value">{{ $newreport->nilaipersentase }}</span>
                </div>
            </div>
            <div class="stat-card-bar"></div>
        </button>
        <button class="stat-card card-unreleased" id="btn-unrelease">
            <div class="stat-card-inner">
                <div class="stat-card-icon-wrap"><i class="fas fa-exclamation-circle"></i></div>
                <div class="stat-card-text">
                    <span class="stat-label">Unreleased</span>
                    <span class="stat-value">{{ $newreport->unrelease }}</span>
                </div>
            </div>
            <div class="stat-card-bar"></div>
        </button>
        <button class="stat-card card-released" id="btn-release">
            <div class="stat-card-inner">
                <div class="stat-card-icon-wrap"><i class="fas fa-check-circle"></i></div>
                <div class="stat-card-text">
                    <span class="stat-label">Released</span>
                    <span class="stat-value">{{ $newreport->release }}</span>
                </div>
            </div>
            <div class="stat-card-bar"></div>
        </button>
        <button class="stat-card card-total" id="btn-total-docs">
            <div class="stat-card-inner">
                <div class="stat-card-icon-wrap"><i class="fas fa-layer-group"></i></div>
                <div class="stat-card-text">
                    <span class="stat-label">Total Dokumen</span>
                    <span class="stat-value">{{ $progressReports->count() }}</span>
                </div>
            </div>
            <div class="stat-card-bar"></div>
        </button>
    </div>

    {{-- ── Main Tabbed Card ── --}}
    <div class="modern-card">

        {{-- Tab Nav --}}
        <ul class="modern-tabs nav" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="progress-tab" data-toggle="tab" href="#progress" role="tab">
                    <i class="fas fa-tasks mr-1"></i> Progress
                </a>
            </li>
            @can('InterInternal Teknologi')
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="laporan-tanggal-tab" data-toggle="tab" href="#laporan-tanggal" role="tab">
                        <i class="fas fa-calendar-alt mr-1"></i> Laporan Tanggal
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="member-tab" data-toggle="tab" href="#member" role="tab">
                        <i class="fas fa-users mr-1"></i> Pembagian Tugas
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="duplicate-tab" data-toggle="tab" href="#duplicate" role="tab">
                        <i class="fas fa-copy mr-1"></i> Duplikat
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="Treediagram-tab" data-toggle="tab" href="#Treediagram" role="tab">
                        <i class="fas fa-sitemap mr-1"></i> Treediagram
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab">
                        <i class="fas fa-history mr-1"></i> History
                    </a>
                </li>
            @endcan
        </ul>

        <div class="tab-content" id="myTabContent">

            {{-- ═══════════════════════════════════════════
                 TAB: PROGRESS
            ═══════════════════════════════════════════ --}}
            <div class="tab-pane fade show active" id="progress" role="tabpanel">

                {{-- Toolbar --}}
                <div class="tab-toolbar">
                    <form action="{{ route('newreports.downloadlaporan', $newreport->id) }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-modern btn-download-mod" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </form>

                    @if ($useronly->rule == 'MTPR' || $useronly->rule == 'superuser')
                        <button type="button" class="btn-modern btn-danger-mod" onclick="handleDeleteMultipleItems()">
                            <i class="fas fa-trash"></i> Hapus Dipilih
                        </button>
                        <button type="button" class="btn-modern btn-success-mod" onclick="handleReleaseMultipleItems()">
                            <i class="fas fa-check-circle"></i> Release Dipilih
                        </button>
                        <button type="button" class="btn-modern btn-pending-mod" onclick="handleUnreleaseMultipleItems()">
                            <i class="fas fa-clock"></i> Unrelease Dipilih
                        </button>
                    @endif

                    @if (session('internalon'))
                        <button id="internalOffButton" class="btn-modern btn-success-mod btn-internal-toggle" title="Nonaktifkan internal view">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <button id="internalButton" class="btn-modern btn-ghost btn-internal-toggle d-none"></button>
                    @else
                        <button id="internalOffButton" class="btn-modern btn-success-mod btn-internal-toggle d-none" title="Nonaktifkan internal view">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <button id="internalButton" class="btn-modern btn-ghost btn-internal-toggle" title="Aktifkan internal view">
                            <i class="fas fa-lock"></i>
                        </button>
                    @endif
                </div>

                {{-- Tables --}}
                <div class="modern-table-wrap p-3">
                    <div id="default-table">
                        @component('newreports.show.componentstable', [
                            'newreport_id' => $newreport_id,
                            'id' => 'example2',
                            'useronly' => $useronly,
                            'newreport' => $newreport,
                            'listanggota' => $listanggota,
                            'penghitung' => 0,
                            'documentNoHeader' => 'No Dokumen All',
                            'documentNameHeader' => 'Nama Dokumen All',
                            'progressReports' => $progressReports,
                            'checklist' => 'checkAll',
                            'name' => 'document_ids[]',
                            'jenisdokumen' => $jenisdokumen,
                        ])
                        @endcomponent
                    </div>

                    <div id="table-release" class="d-none">
                        @component('newreports.show.componentstable', [
                            'newreport_id' => $newreport_id,
                            'id' => 'example2-release',
                            'useronly' => $useronly,
                            'newreport' => $newreport,
                            'listanggota' => $listanggota,
                            'penghitung' => 0,
                            'documentNoHeader' => 'No Dokumen Release',
                            'documentNameHeader' => 'Nama Dokumen Release',
                            'progressReports' => $revisiall['RELEASED']['progressReports'],
                            'checklist' => 'checkAllrelease',
                            'name' => 'document_ids_release[]',
                            'jenisdokumen' => $jenisdokumen,
                        ])
                        @endcomponent
                    </div>

                    <div id="table-unrelease" class="d-none">
                        @component('newreports.show.componentstable', [
                            'newreport_id' => $newreport_id,
                            'id' => 'example2-unrelease',
                            'useronly' => $useronly,
                            'newreport' => $newreport,
                            'listanggota' => $listanggota,
                            'penghitung' => 0,
                            'documentNoHeader' => 'No Dokumen Unrelease',
                            'documentNameHeader' => 'Nama Dokumen Unrelease',
                            'progressReports' => $revisiall['UNRELEASED']['progressReports'],
                            'checklist' => 'checkAllunrelease',
                            'name' => 'document_ids_unrelease[]',
                            'jenisdokumen' => $jenisdokumen,
                        ])
                        @endcomponent
                    </div>
                </div>

            </div><!-- /progress tab -->


            @can('InterInternal Teknologi')

            {{-- ═══════════════════════════════════════════
                 TAB: LAPORAN TANGGAL
            ═══════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="laporan-tanggal" role="tabpanel">
                <div class="modern-tab-content">

                    {{-- Info row --}}
                    <div class="info-pills mb-4">
                        <div class="info-pill"><span class="info-pill-label">Unit</span><span class="info-pill-value">{{ $newreport->unit }}</span></div>
                        <div class="info-pill"><span class="info-pill-label">Proyek</span><span class="info-pill-value">{{ $newreport->proyek_type }}</span></div>
                        <div class="info-pill"><span class="info-pill-label">Penyelesaian</span><span class="info-pill-value" style="color:var(--success)">{{ number_format($progresspercentage, 2) }}%</span></div>
                        <div class="info-pill"><span class="info-pill-label">Total</span><span class="info-pill-value">{{ count($progressReports) }}</span></div>
                    </div>

                    <div class="row">
                        <div class="col-lg-7 mb-4">
                            <div class="chart-card">
                                <div class="chart-card-header">
                                    <span class="chart-card-title"><i class="fas fa-table mr-1"></i> Progress Status</span>
                                    <form action="{{ route('newreports.download', ['newreport' => $newreport->id]) }}" method="POST">
                                        @csrf
                                        <div style="display:flex;gap:8px;align-items:center">
                                            <input type="date" id="start_date" name="start_date" class="modern-input" required>
                                            <input type="date" id="end_date"   name="end_date"   class="modern-input" required>
                                            <button type="submit" class="btn-modern btn-download-mod">
                                                <i class="fas fa-download"></i> Download
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="chart-card-body" style="overflow-x:auto">
                                    @php $penghitung = 0; @endphp
                                    <table id="example3" class="modern-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Week</th>
                                                <th>Start</th>
                                                <th>End</th>
                                                <th>Plan</th>
                                                <th>Realisasi</th>
                                                <th>% Plan</th>
                                                <th>% Real</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $week => $item)
                                            <tr>
                                                <td class="font-mono">{{ $penghitung++ }}</td>
                                                <td class="font-mono" style="font-weight:600">{{ $week }}</td>
                                                <td>{{ $data[$week]['start'] }}</td>
                                                <td>{{ $data[$week]['end'] }}</td>
                                                <td><span class="badge-modern badge-info">{{ $weekData[$week]['value'] }}</span></td>
                                                <td><span class="badge-modern badge-released">{{ $data[$week]['nilai'] }}</span></td>
                                                <td>{{ number_format($weekData[$week]['percentage'], 2) }}%</td>
                                                <td>{{ number_format($data[$week]['nilaipresentase'], 2) }}%</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5 mb-4">
                            <div class="chart-card h-100">
                                <div class="chart-card-header">
                                    <span class="chart-card-title"><i class="fas fa-chart-line mr-1"></i> S-Curve</span>
                                </div>
                                <div class="chart-card-body">
                                    <canvas id="sCurveChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- S-Curve script --}}
            <script>
                var labels      = {!! json_encode(array_keys($data)) !!};
                var plannedData = {!! json_encode(array_column($weekData, 'percentage')) !!};
                var actualData  = {!! json_encode(array_column($data, 'nilaipresentase')) !!};

                var ctx = document.getElementById('sCurveChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            { label:'Planned', data: plannedData, borderColor:'rgba(181,43,30,1)',   borderWidth:2, fill:false, tension:.4, pointRadius:3 },
                            { label:'Actual',  data: actualData,  borderColor:'rgba(140,31,21,1)',   borderWidth:2, fill:false, tension:.4, pointRadius:3, borderDash:[5,3] }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { title:{ display:true, text:'S Curve — Project Revisions', font:{size:13} } },
                        scales: {
                            x: { title:{display:true, text:'Week'} },
                            y: { title:{display:true, text:'Percentage'}, min:0, max:100 }
                        }
                    }
                });
            </script>


            {{-- ═══════════════════════════════════════════
                 TAB: PEMBAGIAN TUGAS (MEMBER)
            ═══════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="member" role="tabpanel">
                <div class="modern-tab-content">
                    <ul class="nav nav-tabs mb-3" id="custom-tabs-one-tab" role="tablist" style="border-bottom:1px solid var(--border)">
                        @foreach ($datastatus as $keyan => $revisi)
                        <li class="nav-item">
                            <a class="nav-link @if($loop->first) active @endif"
                               id="custom-tabs-one-{{ $keyan }}-tab"
                               data-toggle="pill" href="#custom-tabs-one-{{ $keyan }}"
                               role="tab">{{ $keyan }}</a>
                        </li>
                        @endforeach
                    </ul>
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        @foreach ($datastatus as $keyan => $revisi)
                        <div class="tab-pane fade @if($loop->first) show active @endif"
                             id="custom-tabs-one-{{ $keyan }}" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="chart-card">
                                        <div class="chart-card-header">
                                            <span class="chart-card-title">Progress Level — {{ str_replace('_',' ',$keyan) }}</span>
                                        </div>
                                        <div class="chart-card-body">
                                            <canvas id="canvas-level-detailed-{{ $keyan }}"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="chart-card">
                                        <div class="chart-card-header">
                                            <span class="chart-card-title">Progress Status — {{ str_replace('_',' ',$keyan) }}</span>
                                        </div>
                                        <div class="chart-card-body">
                                            <canvas id="canvas-status-detailed-{{ $keyan }}"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


            {{-- ═══════════════════════════════════════════
                 TAB: DUPLIKAT
            ═══════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="duplicate" role="tabpanel">
                <div class="tab-toolbar">
                    @if ($useronly->rule == 'MTPR' || $useronly->rule == 'superuser')
                        <button type="button" class="btn-modern btn-danger-mod" onclick="handleDeleteMultipleItems()">
                            <i class="fas fa-trash"></i> Hapus Duplikat Dipilih
                        </button>
                    @endif
                    @if (session('internalon'))
                        <button id="internalOffButton" class="btn-modern btn-success-mod btn-internal-toggle"><i class="fas fa-arrow-left"></i></button>
                        <button id="internalButton"    class="btn-modern btn-ghost btn-internal-toggle d-none"></button>
                    @else
                        <button id="internalOffButton" class="btn-modern btn-success-mod btn-internal-toggle d-none"><i class="fas fa-arrow-left"></i></button>
                        <button id="internalButton"    class="btn-modern btn-ghost btn-internal-toggle"><i class="fas fa-lock"></i></button>
                    @endif
                </div>

                <div class="modern-table-wrap p-3">
                    @php
                        $penghitung = 1;
                        $sortedReports = $progressReports->sortBy('nodokumen');
                    @endphp
                    <table id="example2-duplicate" class="modern-table">
                        <thead>
                            <tr>
                                <th><span class="check-icon" id="checkAllduplicate"><i class="far fa-square"></i></span></th>
                                <th>#</th>
                                <th>No Dokumen</th>
                                <th>Nama Dokumen</th>
                                <th>Rev</th>
                                <th>Status</th>
                                <th>Dok. Pendukung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortedReports as $index => $progressReport)
                                @if (in_array(trim($progressReport->nodokumen), $duplicates))
                                <tr>
                                    <td>
                                        <div class="icheck-primary">
                                            <input type="checkbox" value="{{ $progressReport->id }}"
                                                   name="document_ids_duplicate[]" id="checkbox{{ $progressReport->id }}">
                                            <label for="checkbox{{ $progressReport->id }}"></label>
                                        </div>
                                    </td>
                                    <td class="font-mono">{{ $penghitung }}</td>
                                    <td id="nodokumen_{{ $progressReport->id }}_{{ $index }}" class="font-mono" style="font-weight:600">
                                        {{ $progressReport->nodokumen }}
                                    </td>
                                    <td id="namadokumen_{{ $progressReport->id }}_{{ $index }}">{{ $progressReport->namadokumen }}</td>
                                    <td class="font-mono">{{ $progressReport->rev }}</td>
                                    <td id="status_{{ $progressReport->id }}_{{ $index }}">
                                        @if($progressReport->statusterbaru === 'RELEASED')
                                            <span class="badge-modern badge-released"><i class="fas fa-check-circle"></i> RELEASED</span>
                                        @elseif($progressReport->statusterbaru === 'Working Progress')
                                            <span class="badge-modern badge-wip">Working Progress</span>
                                        @else
                                            <span class="badge-modern badge-neutral">{{ $progressReport->statusterbaru }}</span>
                                        @endif
                                    </td>
                                    <td id="supportdocument_{{ $progressReport->id }}">
                                        @if ($progressReport->children->count() > 0)
                                            @foreach ($progressReport->children as $anak)
                                                <div class="badge-combined-modern mb-1">
                                                    <span class="part part-name">{{ $anak->namadokumen ?? '' }}</span>
                                                    <span class="part part-no">{{ $anak->nodokumen ?? '' }}</span>
                                                    <span class="part part-status">{{ $anak->status ?? '' }}</span>
                                                    <a href="#" class="part part-action" onclick="unlink('{{ $anak->id }}')">
                                                        <i class="fas fa-unlink"></i> Unlink
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="badge-modern badge-neutral">Tidak ada</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <a href="#" class="btn-action btn-act-delete" onclick="opendeleteForm('{{ $progressReport->id }}', '{{ $index }}')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                            <a href="#" class="btn-action btn-act-edit" onclick="showDocumentSummaryduplicate('{{ json_encode($progressReport) }}', '{{ $progressReport->id }}', '{{ $index }}', '{{ json_encode($listanggota) }}', '{{ $useronly->rule }}')">
                                                <i class="fas fa-pen"></i> Edit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @php $penghitung++; @endphp
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


            {{-- ═══════════════════════════════════════════
                 TAB: TREEDIAGRAM
            ═══════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="Treediagram" role="tabpanel">
                <div class="modern-table-wrap p-3">
                    @php
                        $penghitung = 1;
                        $sortedReports = $progressReports->sortBy('nodokumen');
                    @endphp
                    <table id="example5" class="modern-table">
                        <thead>
                            <tr>
                                <th>Expand</th>
                                <th>#</th>
                                <th>No Dokumen</th>
                                <th>Nama Dokumen</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortedReports as $index => $progressReport)
                                @if ($progressReport->parent_revision_id == null)
                                <tr>
                                    <td>
                                        @if ($generasi[$progressReport->id]['count'] > 0)
                                            <button class="btn-action btn-act-view toggle-children" data-id="{{ $progressReport->id }}">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        @else
                                            <button class="btn-action" style="background:var(--border-light);color:var(--text-muted)" disabled>—</button>
                                        @endif
                                    </td>
                                    <td class="font-mono">{{ $penghitung++ }}</td>
                                    <td class="font-mono" style="font-weight:600">{{ $progressReport->nodokumen }}</td>
                                    <td>{{ $progressReport->namadokumen }}</td>
                                    <td>
                                        @if($progressReport->status === 'RELEASED')
                                            <span class="badge-modern badge-released"><i class="fas fa-check-circle"></i> RELEASED</span>
                                        @elseif($progressReport->status === 'Working Progress')
                                            <span class="badge-modern badge-wip">Working Progress</span>
                                        @else
                                            <span class="badge-modern badge-neutral">{{ $progressReport->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($generasi[$progressReport->id]['count'] > 0)
                                    <tr class="child-rows" data-parent-id="{{ $progressReport->id }}" style="display:none">
                                        <td colspan="5" style="padding:0 0 0 32px; background:#fdf7f6">
                                            @include('newreports.child', [
                                                'progressReports' => $generasi[$progressReport->id]['childreen'],
                                                'generasi' => $generasi,
                                            ])
                                        </td>
                                    </tr>
                                @endif
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.querySelectorAll('.toggle-children').forEach(function(btn) {
                            btn.addEventListener('click', function() {
                                var id = this.getAttribute('data-id');
                                document.querySelectorAll('.child-rows[data-parent-id="' + id + '"]').forEach(function(row) {
                                    row.style.display = row.style.display === 'none' ? '' : 'none';
                                });
                                var icon = this.querySelector('i');
                                icon.classList.toggle('fa-plus');
                                icon.classList.toggle('fa-minus');
                            });
                        });
                    });
                </script>
            </div>


            {{-- ═══════════════════════════════════════════
                 TAB: HISTORY
            ═══════════════════════════════════════════ --}}
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="modern-tab-content">
                    @if ($newreport->systemLogs && $newreport->systemLogs->isEmpty())
                        <div style="text-align:center;padding:40px;color:var(--text-muted)">
                            <i class="fas fa-history" style="font-size:48px;opacity:.3;display:block;margin-bottom:12px"></i>
                            Belum ada history untuk project ini.
                        </div>
                    @else
                        <div style="overflow-x:auto">
                            @php $penghitung = 1; @endphp
                            <table id="example6" class="modern-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Level</th>
                                        <th>Uploader</th>
                                        <th>Waktu</th>
                                        <th>Aksi</th>
                                        <th>% External</th>
                                        <th>% Internal</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($newreport->systemLogs as $riwayat)
                                    <tr>
                                        <td class="font-mono">{{ $penghitung++ }}</td>
                                        <td><span class="badge-modern badge-info">{{ $riwayat->level }}</span></td>
                                        <td style="font-weight:600">{{ $riwayat->user }}</td>
                                        <td>{{ $riwayat->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $riwayat->aksi }}</td>
                                        <td>
                                            @php $message = json_decode($riwayat->message, true); @endphp
                                            @if (isset($message['persentase']) && is_array($message['persentase']))
                                                @foreach ($message['persentase'] as $key => $value)
                                                    <div class="badge-combined-modern mb-1">
                                                        <span class="part part-name">{{ $key }}</span>
                                                        <span class="part part-no">{{ $value }}</span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="badge-modern badge-neutral">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (isset($message['persentase_internal']) && is_array($message['persentase_internal']))
                                                @foreach ($message['persentase_internal'] as $key => $value)
                                                    <div class="badge-combined-modern mb-1">
                                                        <span class="part part-name">{{ $key }}</span>
                                                        <span class="part part-no">{{ $value }}</span>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="badge-modern badge-neutral">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($riwayat->aksi == 'progressaddition')
                                                <a href="{{ route('newreports.showlog', ['newreport' => $newreport->id, 'logid' => $riwayat->id]) }}"
                                                   class="btn-action btn-act-view">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            @endcan

        </div>{{-- /tab-content --}}
    </div>{{-- /modern-card --}}

</div>{{-- /container-fluid --}}
@endsection


@push('scripts')
    <script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.11.3/sorting/datetime-moment.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.6/sorting/datetime-moment.js"></script>

    {{-- ── SEMUA SCRIPT FUNGSIONAL (tidak berubah) ── --}}
    <script>
        document.getElementById('btn-unrelease').addEventListener('click', function() {
            document.getElementById('default-table').classList.add('d-none');
            document.getElementById('table-release').classList.add('d-none');
            document.getElementById('table-unrelease').classList.remove('d-none');
        });
        document.getElementById('btn-release').addEventListener('click', function() {
            document.getElementById('default-table').classList.add('d-none');
            document.getElementById('table-release').classList.remove('d-none');
            document.getElementById('table-unrelease').classList.add('d-none');
        });
        document.getElementById('btn-total').addEventListener('click', function() {
            document.getElementById('default-table').classList.remove('d-none');
            document.getElementById('table-release').classList.add('d-none');
            document.getElementById('table-unrelease').classList.add('d-none');
        });
        document.getElementById('btn-total-docs').addEventListener('click', function() {
            document.getElementById('default-table').classList.remove('d-none');
            document.getElementById('table-release').classList.add('d-none');
            document.getElementById('table-unrelease').classList.add('d-none');
        });
    </script>

    <script>
        function handleCheckboxChange(checkbox) {
            checkbox.closest('tr')[checkbox.checked ? 'classList' : 'classList'][checkbox.checked ? 'add' : 'remove']('checked');
        }
    </script>

    <script>
        document.getElementById('internalButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Enter Password', input: 'password',
                inputAttributes: { autocapitalize: 'off' },
                showCancelButton: true, confirmButtonText: 'Submit',
                showLoaderOnConfirm: true,
                preConfirm: (password) => {
                    if (password === '12345') {
                        return $.ajax({
                            url: '{{ route('set.internalon') }}', type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function() { location.reload(); },
                            error: function() { Swal.showValidationMessage('Failed to set session'); }
                        });
                    } else { Swal.showValidationMessage('Incorrect password'); }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) Swal.fire({ title:'Success!', text:'Password correct.', icon:'success' });
            });
        });

        document.getElementById('internalOffButton').addEventListener('click', function() {
            Swal.fire({
                title: 'Confirmation', text: 'Turn off internal details?', icon: 'question',
                showCancelButton: true, confirmButtonText: 'Yes, turn off',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: '{{ route('set.internaloff') }}', type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function() { location.reload(); },
                        error: function() { Swal.showValidationMessage('Failed to set session'); }
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) Swal.fire({ title:'Success!', text:'Internal details turned off.', icon:'success' });
            });
        });
    </script>

    <script>
        function opendeleteForm(id, index) {
            Swal.fire({
                title: 'Konfirmasi', icon: 'warning',
                text: 'Hapus data ini? (Anak dokumen ikut terhapus kecuali sudah di-unlink)',
                showCancelButton: true, confirmButtonColor: '#dc2626',
                confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/newprogressreports/${id}/delete`,
                        method: 'POST', data: { _token: "{{ csrf_token() }}" },
                        success: function() {
                            [`nodokumen_${id}_${index}`, `namadokumen_${id}_${index}`,
                             `status_${id}_${index}`].forEach(function(eid) {
                                var el = document.getElementById(eid);
                                if (el) el.closest('tr').remove();
                            });
                            Swal.fire({ icon:'success', title:'Dihapus!', showConfirmButton:false, timer:1500 });
                        },
                        error: function(xhr) { console.error(xhr); }
                    });
                }
            });
        }

        function showDocumentSummaryduplicate(item, id, index) {
            var nodokumen   = document.getElementById(`nodokumen_${id}_${index}`).innerText;
            var namadokumen = document.getElementById(`namadokumen_${id}_${index}`).innerText;
            var statusEl    = document.getElementById(`status_${id}_${index}`);
            var status      = statusEl ? statusEl.innerText.trim() : '';

            Swal.fire({
                title: 'Edit Dokumen',
                html: `
                    <div style="display:flex;flex-direction:column;gap:12px;text-align:left">
                        <div><label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6b7280">No Dokumen</label>
                        <input id="edit-no-dokumen"   class="swal2-input" value="${nodokumen}"   placeholder="No Dokumen"   style="margin:4px 0 0"></div>
                        <div><label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6b7280">Nama Dokumen</label>
                        <input id="edit-nama-dokumen" class="swal2-input" value="${namadokumen}" placeholder="Nama Dokumen" style="margin:4px 0 0"></div>
                        <div><label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;color:#6b7280">Status</label>
                        <select id="edit-status" class="swal2-input" style="margin:4px 0 0">
                            <option value="RELEASED"         ${status.includes('RELEASED')         ? 'selected' : ''}>RELEASED</option>
                            <option value="Working Progress" ${status.includes('Working Progress') ? 'selected' : ''}>Working Progress</option>
                            <option value="-"                ${status === '-'                       ? 'selected' : ''}>-</option>
                        </select></div>
                    </div>`,
                showCancelButton: true, confirmButtonText: 'Update',
                preConfirm: () => [
                    document.getElementById('edit-no-dokumen').value,
                    document.getElementById('edit-nama-dokumen').value,
                    document.getElementById('edit-status').value
                ]
            }).then((result) => {
                if (!result.isConfirmed) return;
                var [newNo, newNama, newStatus] = result.value;
                Swal.fire({
                    title:'Konfirmasi', text:'Perbarui data ini?', icon:'question',
                    showCancelButton:true, confirmButtonText:'Ya, perbarui!'
                }).then((c) => {
                    if (!c.isConfirmed) return;
                    $.ajax({
                        url: `/newprogressreports/updateprogressreport/${id}/`,
                        method:'POST',
                        data: { nodokumen: newNo, namadokumen: newNama, status: newStatus, _token: "{{ csrf_token() }}" },
                        success: function(response) {
                            $(`#nodokumen_${id}_${index}`).text(newNo);
                            $(`#namadokumen_${id}_${index}`).text(newNama);
                            $(`#status_${id}_${index}`).text(newStatus);
                            Swal.fire({ icon:'success', title:'Diperbarui!', showConfirmButton:false, timer:1500 });
                        },
                        error: function(xhr) { Swal.fire({ icon:'error', title:'Gagal', text:'Silakan coba lagi.' }); }
                    });
                });
            });
        }

        function handleDeleteMultipleItems() {
            Swal.fire({
                title:'Konfirmasi', icon:'question',
                text:'Hapus item dipilih? (Anak dokumen ikut terhapus kecuali di-unlink)',
                showCancelButton:true, confirmButtonColor:'#dc2626', confirmButtonText:'Ya, hapus!'
            }).then((result) => {
                if (!result.isConfirmed) return;
                var ids = [];
                var tableIds    = ['example2','example2-release','example2-unrelease','example2-duplicate'];
                var checkNames  = ['document_ids[]','document_ids_release[]','document_ids_unrelease[]','document_ids_duplicate[]'];
                tableIds.forEach(function(tid, i) {
                    var t = $('#' + tid).DataTable();
                    t.$(`input[name="${checkNames[i]}"]:checked`).each(function() { ids.push($(this).val()); });
                });
                if (!ids.length) { Swal.fire({ title:'Gagal!', text:'Tidak ada item dipilih.', icon:'warning' }); return; }
                $.ajax({
                    url: '{{ route('newprogressreports.handleDeleteMultipleItems') }}',
                    type:'POST', data: { _token:'{{ csrf_token() }}', document_ids: ids },
                    success: function(r) {
                        Swal.fire({ title:'Berhasil!', text: r.success || 'Dihapus.', icon:'success' }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON?.error || 'Gagal menghapus.';
                        if (xhr.responseJSON?.released_documents) msg += ' (ID: ' + xhr.responseJSON.released_documents.join(', ') + ')';
                        Swal.fire({ title:'Gagal!', text:msg, icon:'error' });
                    }
                });
            });
        }

        function handleUnreleaseMultipleItems() {
            Swal.fire({
                title:'Konfirmasi', icon:'question',
                text:'Set item dipilih menjadi Pending? (Status jadi null)',
                showCancelButton:true, confirmButtonText:'Ya, Unrelease!'
            }).then((result) => {
                if (!result.isConfirmed) return;
                var ids = [];
                var tableIds   = ['example2','example2-release','example2-unrelease','example2-duplicate'];
                var checkNames = ['document_ids[]','document_ids_release[]','document_ids_unrelease[]','document_ids_duplicate[]'];
                tableIds.forEach(function(tid, i) {
                    $('#' + tid).DataTable().$(`input[name="${checkNames[i]}"]:checked`).each(function() { ids.push($(this).val()); });
                });
                if (!ids.length) { Swal.fire({ title:'Gagal!', text:'Tidak ada item dipilih.', icon:'warning' }); return; }
                $.ajax({
                    url: '{{ route('newprogressreports.handleUnreleaseMultipleItems') }}',
                    type:'POST', data: { _token:'{{ csrf_token() }}', document_ids: ids },
                    success: function(r) {
                        Swal.fire({ title:'Berhasil!', text: r.success || 'Dikembalikan ke pending.', icon:'success' }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        var msg = xhr.responseJSON?.error || 'Gagal.';
                        if (xhr.responseJSON?.non_released_documents) msg += ' (ID: ' + xhr.responseJSON.non_released_documents.join(', ') + ')';
                        Swal.fire({ title:'Gagal!', text:msg, icon:'error' });
                    }
                });
            });
        }

        function handleReleaseMultipleItems() {
            Swal.fire({
                title:'Konfirmasi', icon:'question',
                text:'Rilis item dipilih? (Status jadi RELEASED)',
                showCancelButton:true, confirmButtonText:'Ya, rilis!'
            }).then((result) => {
                if (!result.isConfirmed) return;
                var ids = [];
                var tableIds   = ['example2','example2-release','example2-unrelease','example2-duplicate'];
                var checkNames = ['document_ids[]','document_ids_release[]','document_ids_unrelease[]','document_ids_duplicate[]'];
                tableIds.forEach(function(tid, i) {
                    $('#' + tid).DataTable().$(`input[name="${checkNames[i]}"]:checked`).each(function() { ids.push($(this).val()); });
                });
                if (!ids.length) { Swal.fire({ title:'Gagal!', text:'Tidak ada item dipilih.', icon:'warning' }); return; }
                $.ajax({
                    url: '{{ route('newprogressreports.handleReleaseMultipleItems') }}',
                    type:'POST', data: { _token:'{{ csrf_token() }}', document_ids: ids },
                    success: function(r) {
                        Swal.fire({ title:'Berhasil!', text: r.success || 'Dirilis.', icon:'success' }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire({ title:'Gagal!', text: xhr.responseJSON?.error || 'Gagal.', icon:'error' });
                    }
                });
            });
        }
    </script>

    {{-- DataTables init --}}
    <script>
        $(document).ready(function() {
            $.fn.dataTable.moment('DD/MM/YYYY');
            ['#example2','#example2-release','#example2-unrelease'].forEach(function(sel) {
                $(sel).DataTable({ paging:true, lengthChange:false, searching:true, ordering:true, info:true, autoWidth:false, responsive:true });
            });
            $('#example3').DataTable({ paging:true, lengthChange:false, searching:true, ordering:true, info:true, autoWidth:false, responsive:true });
            $('#example5').DataTable({ paging:false, lengthChange:false, searching:true, ordering:true, info:true, autoWidth:false, responsive:true });
            $('#example6').DataTable({ paging:true, lengthChange:false, searching:true, ordering:true, info:true, autoWidth:false, responsive:true });
            $('#example2-duplicate').DataTable({ paging:true, lengthChange:false, searching:true, ordering:true, info:true, autoWidth:false, responsive:true });
        });
    </script>

    {{-- Checkbox toggles --}}
    <script>
        $(function() {
            function toggleCheckbox(btn, selector) {
                var clicks = $(btn).data('clicks');
                if (clicks) { $(selector).prop('checked', false); $(btn).find('i').removeClass('fa-check-square').addClass('fa-square'); }
                else         { $(selector + ':lt(10)').prop('checked', true); $(btn).find('i').removeClass('fa-square').addClass('fa-check-square'); }
                $(btn).data('clicks', !clicks);
            }
            $('#checkAll').click(function()        { toggleCheckbox(this, 'input[name="document_ids[]"]'); });
            $('#checkAllrelease').click(function()  { toggleCheckbox(this, 'input[name="document_ids_release[]"]'); });
            $('#checkAllunrelease').click(function(){ toggleCheckbox(this, 'input[name="document_ids_unrelease[]"]'); });
            $('#checkAllduplicate').click(function(){ toggleCheckbox(this, 'input[name="document_ids_duplicate[]"]'); });
        });
    </script>

    {{-- Document number inline edit --}}
    <script>
        function enableEdit(id)  { document.getElementById('documentNumberDisplay'+id).style.display='none'; document.getElementById('editDocumentForm'+id).style.display='inline-block'; }
        function cancelEdit(id)  { document.getElementById('documentNumberDisplay'+id).style.display='inline'; document.getElementById('editDocumentForm'+id).style.display='none'; }

        function updateDocumentNumber(id, newreport_id, nodokumenlama) {
            const nodokumen = document.getElementById('nodokumen'+id).value;
            const btn = document.getElementById('saveButton'+id);
            const orig = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
            btn.disabled = true;
            $.ajax({
                url: '/newreports/update-documentnumber', method:'POST',
                data: { _token:'{{ csrf_token() }}', nodokumen, nodokumenlama, newreport_id },
                success: function(r) {
                    if (r.status === 'success') {
                        Swal.fire({ icon:'success', title:'Berhasil!', text:r.message, confirmButtonColor:'#1d6fa4' });
                        document.getElementById('documentNumberDisplay'+id).innerText = nodokumen;
                        cancelEdit(id);
                    } else { Swal.fire({ icon:'error', title:'Gagal!', text:r.message, confirmButtonColor:'#dc2626' }); }
                },
                error: function(xhr) { Swal.fire({ icon:'error', title:'Kesalahan!', text:xhr.responseJSON?.message || 'Terjadi kesalahan.' }); },
                complete: function() { btn.innerHTML = orig; btn.disabled = false; }
            });
        }
    </script>

    {{-- Document kind select --}}
    <script>
        $(document).ready(function() {
            $('.select-documentkind').change(function() {
                var docKindId = $(this).val();
                var reportId  = $(this).data('id');
                var index     = $(this).data('index');
                $.ajax({
                    url: "{{ route('newprogressreports.updateDocumentKind') }}",
                    type:'POST',
                    data: { _token:'{{ csrf_token() }}', documentkind_id:docKindId, progressreport_id:reportId },
                    success: function(r) {
                        if (r.success) { alert(r.message); $('#documentkind_'+reportId+'_'+index).text(r.documentkind_name); }
                        else { alert(r.documentkind_name); }
                    },
                    error: function(xhr) { alert(xhr.responseJSON.message); }
                });
            });
        });
    </script>

    {{-- Confirm decision helper --}}
    <script>
        function confirmDecision(formId) {
            Swal.fire({
                title:'Apakah Anda yakin?', text:'Anda akan mengambil keputusan ini.', icon:'question',
                showCancelButton:true, confirmButtonColor:'#1d6fa4',
                cancelButtonColor:'#dc2626', confirmButtonText:'Ya, lanjutkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title:'Updated!', text:'Your information has been uploaded.', icon:'success' });
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

    {{-- Charts (member tab) --}}
    <script type="text/javascript">
        $(document).ready(function() {
            const CC = { red:'rgb(181,43,30)', crimson:'rgb(140,31,21)', green:'rgb(75,192,192)' };
            @foreach ($datastatus as $keyan => $revisi)
            (function(){
                var levelData = {
                    labels: ['Predesign','Intermediate Design','Final Design','Belum Diidentifikasi'],
                    datasets: [
                        { label:'RELEASED',         backgroundColor: CC.red,
                          data:          [{{ $percentageLevel[$keyan]['Predesign']['RELEASED'] }},{{ $percentageLevel[$keyan]['Intermediate Design']['RELEASED'] }},{{ $percentageLevel[$keyan]['Final Design']['RELEASED'] }},{{ $percentageLevel[$keyan]['Belum Diidentifikasi']['RELEASED'] }}],
                          absoluteValues:[{{ $datalevel[$keyan]['Predesign']['RELEASED'] }},{{ $datalevel[$keyan]['Intermediate Design']['RELEASED'] }},{{ $datalevel[$keyan]['Final Design']['RELEASED'] }},{{ $datalevel[$keyan]['Belum Diidentifikasi']['RELEASED'] }}] },
                        { label:'Working Progress',  backgroundColor: CC.crimson,
                          data:          [{{ $percentageLevel[$keyan]['Predesign']['Working Progress'] }},{{ $percentageLevel[$keyan]['Intermediate Design']['Working Progress'] }},{{ $percentageLevel[$keyan]['Final Design']['Working Progress'] }},{{ $percentageLevel[$keyan]['Belum Diidentifikasi']['Working Progress'] }}],
                          absoluteValues:[{{ $datalevel[$keyan]['Predesign']['Working Progress'] }},{{ $datalevel[$keyan]['Intermediate Design']['Working Progress'] }},{{ $datalevel[$keyan]['Final Design']['Working Progress'] }},{{ $datalevel[$keyan]['Belum Diidentifikasi']['Working Progress'] }}] },
                        { label:'Belum Dimulai',    backgroundColor: CC.green,
                          data:          [{{ $percentageLevel[$keyan]['Predesign']['Belum Dimulai'] }},{{ $percentageLevel[$keyan]['Intermediate Design']['Belum Dimulai'] }},{{ $percentageLevel[$keyan]['Final Design']['Belum Dimulai'] }},{{ $percentageLevel[$keyan]['Belum Diidentifikasi']['Belum Dimulai'] }}],
                          absoluteValues:[{{ $datalevel[$keyan]['Predesign']['Belum Dimulai'] }},{{ $datalevel[$keyan]['Intermediate Design']['Belum Dimulai'] }},{{ $datalevel[$keyan]['Final Design']['Belum Dimulai'] }},{{ $datalevel[$keyan]['Belum Diidentifikasi']['Belum Dimulai'] }}] }
                    ]
                };
                new Chart(document.getElementById("canvas-level-detailed-{{ $keyan }}").getContext("2d"), {
                    plugins:[ChartDataLabels], type:'bar', data:levelData,
                    options:{ plugins:{ title:{display:true,text:"Progress Level — {{ str_replace('_',' ',$keyan) }}"},
                                        datalabels:{color:'white',font:{size:12},formatter:(v,ctx)=>`${ctx.dataset.absoluteValues[ctx.dataIndex]} (${v.toFixed(1)}%)`}},
                              responsive:true, scales:{x:{stacked:true},y:{stacked:true,min:-15,max:115}} }
                });

                var statusData = {
                    labels:['{{ $datastatus[$keyan]['RELEASED'] }} RELEASED','{{ $datastatus[$keyan]['Working Progress'] }} WIP','{{ $datastatus[$keyan]['Belum Dimulai'] }} Belum Dimulai'],
                    datasets:[{ data:[{{ $percentageStatus[$keyan]['RELEASED'] }},{{ $percentageStatus[$keyan]['Working Progress'] }},{{ $percentageStatus[$keyan]['Belum Dimulai'] }}],
                                backgroundColor:['#b52b1e','#8c1f15','#bdc3c7'], borderColor:'#fff' }]
                };
                new Chart(document.getElementById("canvas-status-detailed-{{ $keyan }}").getContext("2d"), {
                    plugins:[ChartDataLabels], type:'doughnut', data:statusData,
                    options:{ maintainAspectRatio:false, responsive:true,
                              plugins:{ title:{display:true,text:"Progress Status — {{ str_replace('_',' ',$keyan) }}"},
                                        datalabels:{color:'white',font:{size:12},formatter:(v)=>v.toFixed(1)+'%'},
                                        legend:{display:true,labels:{font:{size:13},generateLabels:(chart)=>{
                                            return chart.data.labels.map((l,i)=>({text:l+' ('+chart.data.datasets[0].data[i].toFixed(1)+'%)',fillStyle:chart.data.datasets[0].backgroundColor[i]}));
                                        }}} } }
                });
            })();
            @endforeach
        });
    </script>
@endpush