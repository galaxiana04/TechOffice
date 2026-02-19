@extends('layouts.universal')

@section('container3')
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        #monitoringDash *,
        #monitoringDash *::before,
        #monitoringDash *::after { box-sizing: border-box; }

        #monitoringDash {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            padding: 0;
            color: #0f172a;
        }

        /* ── HERO ── */
        .md-hero {
            background: linear-gradient(135deg, #1e40af 0%, #0ea5e9 60%, #06b6d4 100%);
            padding: 32px 32px 52px;
            position: relative;
            overflow: hidden;
        }
        .md-hero::before {
            content:''; position:absolute; top:-40px; right:-40px;
            width:280px; height:280px; background:rgba(255,255,255,.06); border-radius:50%;
        }
        .md-hero::after {
            content:''; position:absolute; bottom:-60px; left:30%;
            width:200px; height:200px; background:rgba(255,255,255,.04); border-radius:50%;
        }
        .md-hero-title {
            font-size:1.75rem; font-weight:800; color:#fff;
            margin:0; letter-spacing:-.5px; position:relative; z-index:1;
        }
        .md-hero-sub {
            color:rgba(255,255,255,.75); font-size:.88rem;
            margin:5px 0 0; position:relative; z-index:1;
        }
        .md-hero-badge {
            background:rgba(255,255,255,.15); backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,.25); border-radius:10px;
            padding:8px 16px; color:#fff; font-size:.78rem; font-weight:600;
            position:relative; z-index:1; white-space:nowrap;
        }

        /* ── STAT CARDS ───*/
        .md-stats-row {
            margin:-28px 24px 0;
            display:grid; grid-template-columns:repeat(4,1fr);
            gap:16px; position:relative; z-index:10;
        }
        @media(max-width:768px){
            .md-stats-row{ grid-template-columns:repeat(2,1fr); margin:-20px 12px 0; }
            .md-hero{ padding:24px 16px 44px; }
        }
        .md-stat {
            background:#fff; border-radius:16px; padding:20px 20px 18px;
            box-shadow:0 4px 24px rgba(15,23,42,.10);
            display:flex; flex-direction:column; gap:10px;
            border:1px solid rgba(255,255,255,.8);
            transition:transform .2s ease, box-shadow .2s ease; cursor:default;
        }
        .md-stat:hover{ transform:translateY(-4px); box-shadow:0 12px 32px rgba(15,23,42,.14); }
        .md-stat-top{ display:flex; align-items:center; justify-content:space-between; }
        .md-stat-icon {
            width:40px; height:40px; border-radius:11px;
            display:flex; align-items:center; justify-content:center; font-size:.95rem;
        }
        .md-stat-icon.blue{ background:#dbeafe; color:#1d4ed8; }
        .md-stat-icon.green{ background:#d1fae5; color:#059669; }
        .md-stat-icon.amber{ background:#fef3c7; color:#d97706; }
        .md-stat-icon.purple{ background:#ede9fe; color:#7c3aed; }
        .md-stat-trend {
            font-size:.7rem; font-weight:700; padding:3px 8px;
            border-radius:99px; letter-spacing:.3px;
        }
        .md-stat-trend.up{ background:#d1fae5; color:#059669; }
        .md-stat-trend.neu{ background:#f1f5f9; color:#64748b; }
        .md-stat-value {
            font-size:2rem; font-weight:800; letter-spacing:-1px;
            line-height:1; font-family:'JetBrains Mono',monospace; color:#0f172a;
        }
        .md-stat-label {
            font-size:.77rem; color:#64748b; font-weight:600;
            text-transform:uppercase; letter-spacing:.5px;
        }

        /* ── BODY ─── */
        .md-body{ padding:28px 24px 40px; }
        @media(max-width:768px){ .md-body{ padding:20px 12px 32px; } }

        .md-section-title {
            font-size:.72rem; font-weight:700; text-transform:uppercase;
            letter-spacing:1.3px; color:#94a3b8; margin-bottom:16px;
            display:flex; align-items:center; gap:8px;
        }
        .md-section-title::after{ content:''; flex:1; height:1px; background:#e2e8f0; }

        /* ── PANEL ── */
        .md-panel {
            background:#fff; border-radius:18px;
            box-shadow:0 2px 16px rgba(15,23,42,.07);
            border:1px solid #f1f5f9; overflow:hidden;
        }
        .md-panel-head {
            padding:18px 22px 0; font-size:.88rem; font-weight:700;
            color:#1e293b; display:flex; align-items:center; gap:8px;
        }
        .md-panel-head i{ color:#0ea5e9; }
        .md-panel-body{ padding:16px 22px 22px; }

        /* ── DUAL CHART GRID ── */
        .md-chart-grid {
            display:grid;
            grid-template-columns:1fr 1fr 300px;
            gap:20px; margin-bottom:32px;
        }
        @media(max-width:1100px){
            .md-chart-grid{ grid-template-columns:1fr 1fr; }
            .md-chart-grid > .md-panel:last-child{ grid-column:1/-1; }
        }
        @media(max-width:680px){
            .md-chart-grid{ grid-template-columns:1fr; }
            .md-chart-grid > .md-panel:last-child{ grid-column:auto; }
        }

        /* ── TOP PERFORMERS TABS ───*/
        .tp-tabs {
            display:flex; padding:12px 22px 0;
            border-bottom:1px solid #f1f5f9; gap:4px;
        }
        .tp-tab {
            padding:8px 16px; font-size:.78rem; font-weight:700;
            cursor:pointer; border-radius:8px 8px 0 0;
            color:#94a3b8; border:1px solid transparent;
            border-bottom:none; transition:all .18s;
            background:transparent; display:flex; align-items:center; gap:6px;
        }
        .tp-tab.active.drafter{ background:#ede9fe; color:#7c3aed; border-color:#e2e8f0; }
        .tp-tab.active.checker{ background:#fef3c7; color:#d97706; border-color:#e2e8f0; }
        .tp-tab:not(.active):hover{ background:#f8fafc; }
        .tp-list{ display:none; }
        .tp-list.active{ display:block; }

        .tp-item {
            display:flex; align-items:center; gap:12px;
            padding:10px 0; border-bottom:1px solid #f8fafc;
        }
        .tp-item:last-child{ border-bottom:none; }
        .tp-rank {
            width:28px; height:28px; border-radius:9px;
            display:flex; align-items:center; justify-content:center;
            font-size:.78rem; font-weight:800; flex-shrink:0;
        }
        .tp-rank.gold{ background:#fef3c7; color:#d97706; }
        .tp-rank.silver{ background:#f1f5f9; color:#475569; }
        .tp-rank.bronze{ background:#fdf2ec; color:#b45309; }
        .tp-rank.other{ background:#f8fafc; color:#94a3b8; }
        .tp-name{ font-size:.84rem; font-weight:600; flex:1; min-width:0; }
        .tp-name small {
            display:block; font-size:.72rem; color:#94a3b8; font-weight:400;
            white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        }
        .tp-score {
            font-family:'JetBrains Mono',monospace; font-size:.8rem;
            font-weight:600; padding:3px 10px; border-radius:8px;
        }
        .tp-score.drafter{ background:#ede9fe; color:#7c3aed; }
        .tp-score.checker{ background:#fef3c7; color:#d97706; }

        /* ── SEARCH ─ */
        .md-search{ position:relative; width:100%; max-width:280px; }
        .md-search i {
            position:absolute; left:13px; top:50%; transform:translateY(-50%);
            color:#94a3b8; font-size:.82rem;
        }
        .md-search input {
            width:100%; padding:9px 14px 9px 36px;
            border:1.5px solid #e2e8f0; border-radius:11px;
            font-family:'Plus Jakarta Sans',sans-serif; font-size:.85rem;
            outline:none; color:#1e293b; background:#fff;
            transition:border-color .2s, box-shadow .2s;
        }
        .md-search input:focus{ border-color:#0ea5e9; box-shadow:0 0 0 3px rgba(14,165,233,.12); }
        .md-search input::placeholder{ color:#cbd5e1; }

        /* ── USER CARDS ──────────────────────────────── */
        .md-user-grid{ display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
        @media(max-width:992px){ .md-user-grid{ grid-template-columns:repeat(3,1fr); } }
        @media(max-width:640px){ .md-user-grid{ grid-template-columns:repeat(2,1fr); } }

        .md-user-card {
            background:#fff; border-radius:16px;
            border:1.5px solid #f1f5f9;
            box-shadow:0 2px 12px rgba(15,23,42,.06); overflow:hidden;
            transition:transform .22s ease, box-shadow .22s ease, border-color .22s ease;
            display:flex; flex-direction:column;
        }
        .md-user-card:hover {
            transform:translateY(-5px);
            box-shadow:0 16px 36px rgba(15,23,42,.12); border-color:#bae6fd;
        }
        .md-user-card-body{ padding:22px 16px 16px; text-align:center; flex:1; }
        .md-avatar {
            width:56px; height:56px; border-radius:50%;
            display:inline-flex; align-items:center; justify-content:center;
            font-weight:800; font-size:1.05rem; margin-bottom:12px;
            box-shadow:0 6px 16px rgba(14,165,233,.3);
            background:linear-gradient(135deg,#38bdf8,#1d4ed8); color:#fff; position:relative;
        }
        .md-avatar-ring{ position:absolute; inset:-3px; border-radius:50%; border:2px solid rgba(14,165,233,.2); }
        .md-user-name{ font-weight:700; font-size:.9rem; margin-bottom:3px; color:#0f172a; }
        .md-user-unit{ font-size:.75rem; color:#94a3b8; margin-bottom:10px; font-weight:500; }
        .md-doc-chip {
            display:inline-flex; align-items:center; gap:5px;
            background:#f0f9ff; color:#0284c7; font-size:.75rem; font-weight:700;
            padding:4px 12px; border-radius:99px;
            font-family:'JetBrains Mono',monospace; border:1px solid #bae6fd;
            margin-bottom:8px;
        }
        .md-role-pill-row {
            display:flex; justify-content:center; gap:6px;
            margin-top:4px; flex-wrap:wrap;
        }
        .md-role-pill {
            display:inline-flex; align-items:center; gap:4px;
            padding:3px 10px; border-radius:99px; font-size:.7rem; font-weight:700;
        }
        .md-role-pill.drafter{ background:#ede9fe; color:#7c3aed; border:1px solid #ddd6fe; }
        .md-role-pill.checker{ background:#fef3c7; color:#d97706; border:1px solid #fde68a; }

        .md-user-card-foot{ padding:11px 14px; background:#fafbfc; border-top:1px solid #f1f5f9; }
        .md-btn-detail {
            width:100%; padding:8px 12px; border:1.5px solid #0ea5e9;
            border-radius:10px; background:transparent; color:#0284c7;
            font-size:.8rem; font-weight:700; cursor:pointer;
            font-family:'Plus Jakarta Sans',sans-serif;
            transition:all .18s ease; display:flex; align-items:center;
            justify-content:center; gap:6px;
        }
        .md-btn-detail:hover {
            background:linear-gradient(135deg,#0ea5e9,#1d4ed8); color:#fff;
            border-color:transparent; box-shadow:0 4px 14px rgba(14,165,233,.4);
            transform:scale(1.02);
        }

        /* ── MODAL ──*/
        #mdModal .modal-content {
            border:none; border-radius:20px;
            box-shadow:0 24px 64px rgba(15,23,42,.2);
            overflow:hidden; font-family:'Plus Jakarta Sans',sans-serif;
        }
        #mdModal .modal-header {
            background:linear-gradient(135deg,#1e40af,#0ea5e9);
            padding:20px 24px; border:none;
        }
        #mdModal .modal-title{ font-size:.95rem; font-weight:700; color:#fff; }
        #mdModal .close{ color:rgba(255,255,255,.8); text-shadow:none; opacity:1; }
        #mdModal .close:hover{ color:#fff; }

        .md-modal-stat {
            display:flex; gap:10px; padding:14px 22px;
            background:#f8fafc; border-bottom:1px solid #f1f5f9;
            flex-wrap:wrap; align-items:center;
        }
        .md-modal-chip {
            display:inline-flex; align-items:center; gap:6px;
            padding:6px 14px; border-radius:10px; font-size:.78rem; font-weight:600;
        }
        .md-modal-chip.blue{ background:#dbeafe; color:#1d4ed8; }
        .md-modal-chip.green{ background:#d1fae5; color:#059669; }
        .md-modal-chip.purple{ background:#ede9fe; color:#7c3aed; }
        .md-modal-chip.amber{ background:#fef3c7; color:#d97706; }

        /* ── TABLE ─── */
        .md-table{ width:100%; border-collapse:collapse; }
        .md-table thead th {
            font-size:.7rem; text-transform:uppercase; letter-spacing:.8px;
            font-weight:700; color:#94a3b8; background:#f8fafc;
            padding:12px 18px; border-bottom:1px solid #f1f5f9; white-space:nowrap;
        }
        .md-table tbody td {
            padding:12px 18px; font-size:.85rem;
            border-bottom:1px solid #f8fafc; vertical-align:middle;
        }
        .md-table tbody tr:last-child td{ border-bottom:none; }
        .md-table tbody tr:hover{ background:#f8fafc; }

        .md-nodok {
            font-family:'JetBrains Mono',monospace;
            font-size:.78rem; font-weight:600; color:#1e293b;
        }
        .md-status-badge {
            display:inline-block; padding:3px 10px; border-radius:7px;
            font-size:.72rem; font-weight:700; background:#e0f2fe; color:#0284c7;
            font-family:'JetBrains Mono',monospace;
        }

        /* Role badges */
        .md-role-badge {
            display:inline-flex; align-items:center; gap:5px;
            padding:5px 14px; border-radius:99px; font-size:.75rem;
            font-weight:700; white-space:nowrap;
        }
        .md-role-drafter{ background:#ede9fe; color:#7c3aed; border:1px solid #ddd6fe; }
        .md-role-checker{ background:#fef3c7; color:#d97706; border:1px solid #fde68a; }
        .md-role-none{ color:#cbd5e1; }

        .md-realisasi-done{ font-weight:700; color:#059669; }
        .md-realisasi-none{ color:#cbd5e1; }

        .md-modal-footer {
            padding:12px 22px; background:#f8fafc; border-top:1px solid #f1f5f9;
            display:flex; align-items:center; justify-content:space-between;
            font-size:.78rem; color:#94a3b8;
        }

        /* ── ANIMATIONS ──*/
        @keyframes mdFadeUp{
            from{ opacity:0; transform:translateY(18px); }
            to{ opacity:1; transform:translateY(0); }
        }
        .md-anim{ animation:mdFadeUp .4s ease both; }
        .md-anim:nth-child(1){ animation-delay:0ms; }
        .md-anim:nth-child(2){ animation-delay:55ms; }
        .md-anim:nth-child(3){ animation-delay:110ms; }
        .md-anim:nth-child(4){ animation-delay:165ms; }
        .md-anim:nth-child(5){ animation-delay:220ms; }
        .md-anim:nth-child(6){ animation-delay:275ms; }
        .md-anim:nth-child(7){ animation-delay:330ms; }
        .md-anim:nth-child(8){ animation-delay:385ms; }
        .md-anim:nth-child(9){ animation-delay:440ms; }
        .md-anim:nth-child(10){ animation-delay:495ms; }
        .md-anim:nth-child(11){ animation-delay:550ms; }
        .md-anim:nth-child(12){ animation-delay:605ms; }
    </style>

    <div id="monitoringDash">

        {{-- ── HERO ── --}}
        <div class="md-hero">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                    <h2 class="md-hero-title">
                        <i class="fas fa-chart-bar" style="margin-right:10px;opacity:.85;"></i>Monitoring Kinerja
                    </h2>
                    <p class="md-hero-sub">Pantau hasil kerja seluruh pegawai secara real-time</p>
                </div>
                <div class="md-hero-badge">
                    <i class="fas fa-circle" style="font-size:.45rem;margin-right:6px;color:#4ade80;vertical-align:middle;"></i>
                    Live &nbsp;·&nbsp; <span id="mdTimestamp">Memuat...</span>
                </div>
            </div>
        </div>

        {{-- ── STAT CARDS ── --}}
        <div class="md-stats-row">
            <div class="md-stat">
                <div class="md-stat-top">
                    <div class="md-stat-icon blue"><i class="fas fa-users"></i></div>
                    <span class="md-stat-trend up">Aktif</span>
                </div>
                <div class="md-stat-value">{{ count($users) }}</div>
                <div class="md-stat-label">Total Pegawai</div>
            </div>
            <div class="md-stat">
                <div class="md-stat-top">
                    <div class="md-stat-icon green"><i class="fas fa-file-alt"></i></div>
                    <span class="md-stat-trend neu">Docs</span>
                </div>
                <div class="md-stat-value">{{ $users->sum('total_work') }}</div>
                <div class="md-stat-label">Total Dikerjakan</div>
            </div>
            <div class="md-stat">
                <div class="md-stat-top">
                    <div class="md-stat-icon amber"><i class="fas fa-trophy"></i></div>
                    <span class="md-stat-trend up">Top</span>
                </div>
                <div class="md-stat-value">{{ $users->max('total_work') ?? 0 }}</div>
                <div class="md-stat-label">Nilai Tertinggi</div>
            </div>
            <div class="md-stat">
                <div class="md-stat-top">
                    <div class="md-stat-icon purple"><i class="fas fa-chart-line"></i></div>
                    <span class="md-stat-trend neu">Avg</span>
                </div>
                <div class="md-stat-value">{{ $users->count() ? number_format($users->avg('total_work'),1) : '0' }}</div>
                <div class="md-stat-label">Rata-rata</div>
            </div>
        </div>

        {{-- ── BODY ── --}}
        <div class="md-body">

            {{-- ── DUAL CHART + TOP PERFORMERS ── --}}
            <div class="md-chart-grid">

                {{-- Chart Drafter --}}
                <div class="md-panel">
                    <div class="md-panel-head">
                        <i class="fas fa-pen" style="color:#7c3aed !important;"></i>
                        <span style="color:#7c3aed;">Top Drafter</span>
                    </div>
                    <div style="padding:12px 18px 18px;">
                        <canvas id="mdDrafterChart" height="115"></canvas>
                    </div>
                </div>

                {{-- Chart Checker --}}
                <div class="md-panel">
                    <div class="md-panel-head">
                        <i class="fas fa-check-double" style="color:#d97706 !important;"></i>
                        <span style="color:#d97706;">Top Checker</span>
                    </div>
                    <div style="padding:12px 18px 18px;">
                        <canvas id="mdCheckerChart" height="115"></canvas>
                    </div>
                </div>

                {{-- Top Performers tabbed --}}
                <div class="md-panel">
                    <div class="md-panel-head">
                        <i class="fas fa-medal" style="color:#d97706 !important;"></i> Top Performers
                    </div>
                    <div class="tp-tabs">
                        <button class="tp-tab drafter active" onclick="switchTP('Drafter',this)">
                            <i class="fas fa-pen" style="font-size:.65rem;"></i> Drafter
                        </button>
                        <button class="tp-tab checker" onclick="switchTP('Checker',this)">
                            <i class="fas fa-check-double" style="font-size:.65rem;"></i> Checker
                        </button>
                    </div>
                    <div class="md-panel-body" style="padding-top:8px;">
                        <div class="tp-list active" id="tpListDrafter">
                            @foreach($users->sortByDesc('total_drafter')->take(5) as $u)
                                @php $rc = match($loop->iteration){ 1=>'gold',2=>'silver',3=>'bronze',default=>'other' }; @endphp
                                <div class="tp-item">
                                    <div class="tp-rank {{ $rc }}">{{ $loop->iteration }}</div>
                                    <div class="tp-name">
                                        {{ $u->name }}
                                        <small>{{ $u->unit->nama_unit ?? '—' }}</small>
                                    </div>
                                    <div class="tp-score drafter">{{ number_format($u->total_drafter ?? 0) }} dok</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="tp-list" id="tpListChecker">
                            @foreach($users->sortByDesc('total_checker')->take(5) as $u)
                                @php $rc = match($loop->iteration){ 1=>'gold',2=>'silver',3=>'bronze',default=>'other' }; @endphp
                                <div class="tp-item">
                                    <div class="tp-rank {{ $rc }}">{{ $loop->iteration }}</div>
                                    <div class="tp-name">
                                        {{ $u->name }}
                                        <small>{{ $u->unit->nama_unit ?? '—' }}</small>
                                    </div>
                                    <div class="tp-score checker">{{ number_format($u->total_checker ?? 0) }} dok</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>{{-- /md-chart-grid --}}

            {{-- ── GRID HEADER ── --}}
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
                <div class="md-section-title" style="margin-bottom:0;flex:1;">
                    <i class="fas fa-id-card" style="color:#0ea5e9;"></i> Semua Pegawai
                </div>
                <div class="md-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="mdSearch" placeholder="Cari nama pegawai…">
                </div>
            </div>

            {{-- ── USER CARDS PER UNIT ── --}}
            <div id="mdGroupedContainer">
                @foreach($groupedUsers as $ruleName => $usersInRule)
                    <div class="md-section-title"
                        style="margin-top:40px;background:#fff;padding:15px;border-radius:12px 12px 0 0;border-left:5px solid #0ea5e9;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                        <i class="fas fa-layer-group" style="color:#0ea5e9;margin-right:10px;"></i>
                        UNIT: <span style="color:#1e40af;font-weight:800;">{{ $ruleName }}</span>
                        <span style="margin-left:auto;font-size:0.75rem;color:#64748b;font-weight:600;">
                            {{ $usersInRule->count() }} Personel · Total {{ number_format($usersInRule->sum('total_work')) }} Dokumen
                        </span>
                    </div>
                    <div class="md-user-grid"
                        style="background:rgba(255,255,255,0.4);padding:25px;border-radius:0 0 12px 12px;border:1px solid #e2e8f0;border-top:none;margin-bottom:20px;">
                        @foreach($usersInRule as $user)
                            <div class="md-user-card md-anim" data-name="{{ strtolower($user->name) }}">
                                <div class="md-user-card-body">
                                    <div class="md-avatar">
                                        {{ strtoupper(substr($user->name,0,2)) }}
                                        <div class="md-avatar-ring"></div>
                                    </div>
                                    <div class="md-user-name">{{ $user->name }}</div>
                                    <div class="md-doc-chip">
                                        <i class="fas fa-file-alt" style="font-size:.65rem;"></i>
                                        {{ number_format($user->total_work) }} Dok
                                    </div>
                                    <div class="md-role-pill-row">
                                        <span class="md-role-pill drafter">
                                            <i class="fas fa-pen" style="font-size:.6rem;"></i>
                                            {{ number_format($user->total_drafter ?? 0) }} Drafter
                                        </span>
                                        <span class="md-role-pill checker">
                                            <i class="fas fa-check-double" style="font-size:.6rem;"></i>
                                            {{ number_format($user->total_checker ?? 0) }} Checker
                                        </span>
                                    </div>
                                </div>
                                <div class="md-user-card-foot">
                                    <button class="md-btn-detail btn-detail"
                                        data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                                        <i class="fas fa-search" style="font-size:.75rem;"></i>
                                        Lihat Hasil Kerja
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

        </div>{{-- /md-body --}}
    </div>{{-- /monitoringDash --}}

    {{-- ── MODAL ── --}}
    <div class="modal fade" id="mdModal" tabindex="-1" role="dialog" aria-labelledby="mdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mdModalLabel">
                        <i class="fas fa-tasks" style="margin-right:8px;opacity:.8;"></i>
                        Riwayat Pekerjaan &nbsp;·&nbsp;
                        <span id="mdModalName" style="font-family:'JetBrains Mono',monospace;font-size:.88rem;"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- Stat chips --}}
                <div class="md-modal-stat">
                    <span class="md-modal-chip blue" id="mdChipTotal">
                        <i class="fas fa-file-alt"></i> — dokumen
                    </span>
                    <span class="md-modal-chip green" id="mdChipDone">
                        <i class="fas fa-check-circle"></i> — selesai
                    </span>
                    <span class="md-modal-chip purple" id="mdChipDrafter">
                        <i class="fas fa-pen"></i> — sebagai drafter
                    </span>
                    <span class="md-modal-chip amber" id="mdChipChecker">
                        <i class="fas fa-check-double"></i> — sebagai checker
                    </span>
                </div>

                <div style="overflow-x:auto;">
                    <table class="md-table">
                        <thead>
                            <tr>
                                <th>No. Dokumen</th>
                                <th>Nama Dokumen</th>
                                <th style="text-align:center;">Status</th>
                                <th style="text-align:center;">Peran</th>
                                <th style="text-align:center;">Realisasi</th>
                            </tr>
                        </thead>
                        <tbody id="mdModalBody"></tbody>
                    </table>
                </div>

                <div class="md-modal-footer">
                    <span id="mdModalFooterInfo">—</span>
                    <span>Data real-time</span>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        //TIMESTAMP
        function updateLiveDateTime() {
            const now = new Date();
            const formatter = new Intl.DateTimeFormat('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            document.getElementById('mdTimestamp').textContent = formatter.format(now).replace('pukul', '-');
        }
        setInterval(updateLiveDateTime, 1000);
        updateLiveDateTime();

        // HELPER: build bar chart
        function buildChart(canvasId, users, key, colors){
            var canvas = document.getElementById(canvasId);
            if(!canvas) return;
            var sorted = users.slice().sort(function(a,b){ return (b[key]||0)-(a[key]||0); }).slice(0,10);
            var labels = sorted.map(function(u){ return u.name.split(' ').slice(0,2).join(' '); });
            var values = sorted.map(function(u){ return u[key] || 0; });
            var max = Math.max.apply(null, values) || 1;

            new Chart(canvas.getContext('2d'),{
                type:'bar',
                data:{
                    labels: labels,
                    datasets:[{
                        label:'Dokumen',
                        data: values,
                        backgroundColor: values.map(function(v){
                            return v===max ? colors.solid : colors.faded;
                        }),
                        borderColor: values.map(function(v){
                            return v===max ? colors.border : colors.borderFaded;
                        }),
                        borderWidth:1.5, borderRadius:8, borderSkipped:false
                    }]
                },
                options:{
                    responsive:true,
                    plugins:{
                        legend:{ display:false },
                        tooltip:{
                            backgroundColor:'#1e293b', padding:10, cornerRadius:10,
                            callbacks:{ label:function(ctx){ return '  '+ctx.parsed.y+' dokumen'; } }
                        }
                    },
                    scales:{
                        y:{
                            beginAtZero:true,
                            grid:{ color:'rgba(0,0,0,.04)' },
                            ticks:{ font:{size:11}, color:'#94a3b8', stepSize:1 }
                        },
                        x:{
                            grid:{ display:false },
                            ticks:{ font:{size:11}, color:'#64748b', maxRotation:30 }
                        }
                    }
                }
            });
        }

        var allUsers = @json($users->values());

        buildChart('mdDrafterChart', allUsers, 'total_drafter', {
            solid:'rgba(124,58,237,.85)', faded:'rgba(124,58,237,.2)',
            border:'#7c3aed', borderFaded:'rgba(124,58,237,.35)'
        });

        buildChart('mdCheckerChart', allUsers, 'total_checker', {
            solid:'rgba(217,119,6,.85)', faded:'rgba(217,119,6,.2)',
            border:'#d97706', borderFaded:'rgba(217,119,6,.35)'
        });

        //TOP PERFORMERS TAB SWITCH 
        function switchTP(type, btn){
            document.querySelectorAll('.tp-tab').forEach(function(t){ t.classList.remove('active'); });
            document.querySelectorAll('.tp-list').forEach(function(l){ l.classList.remove('active'); });
            btn.classList.add('active');
            document.getElementById('tpList'+type).classList.add('active');
        }

        //SEARCH
        document.getElementById('mdSearch').addEventListener('input', function(){
            var q = this.value.toLowerCase().trim();
            document.querySelectorAll('#mdGroupedContainer .md-user-card').forEach(function(card){
                card.style.display = card.dataset.name.includes(q) ? '' : 'none';
            });
        });

       // MODAL 
        $(document).on('click', '.btn-detail', function(){
            var userId   = $(this).data('id');
            var userName = $(this).data('name');

            $('#mdModalName').text(userName);
            $('#mdChipTotal').html('<i class="fas fa-spinner fa-spin"></i> Loading…');
            $('#mdChipDone, #mdChipDrafter, #mdChipChecker').html('');
            $('#mdModalFooterInfo').text('Memuat data…');
            $('#mdModalBody').html(
                '<tr><td colspan="5" style="text-align:center;padding:40px;color:#94a3b8;">' +
                '<i class="fas fa-spinner fa-spin" style="font-size:1.5rem;display:block;margin-bottom:10px;"></i>' +
                'Memuat riwayat pekerjaan…</td></tr>'
            );
            $('#mdModal').modal('show');

            $.ajax({
                url: '/monitoring-user/' + userId,
                method: 'GET',
                success: function(data){
                    var histories    = data.histories || [];
                    var doneCount    = histories.filter(function(h){ return h.realisasidate; }).length;
                    var drafterCount = histories.filter(function(h){ return h.role === 'drafter'; }).length;
                    var checkerCount = histories.filter(function(h){ return h.role === 'checker'; }).length;

                    $('#mdChipTotal').html('<i class="fas fa-file-alt"></i> ' + histories.length + ' dokumen');
                    $('#mdChipDone').html('<i class="fas fa-check-circle"></i> ' + doneCount + ' selesai');
                    $('#mdChipDrafter').html('<i class="fas fa-pen"></i> ' + drafterCount + ' sebagai drafter');
                    $('#mdChipChecker').html('<i class="fas fa-check-double"></i> ' + checkerCount + ' sebagai checker');
                    $('#mdModalFooterInfo').text(histories.length + ' dokumen ditemukan untuk ' + userName);

                    var html = '';
                    if(histories.length > 0){
                        histories.forEach(function(h){

                            var roleBadge = '';
                            if(h.role === 'drafter'){
                                roleBadge =
                                    '<span class="md-role-badge md-role-drafter">' +
                                    '<i class="fas fa-pen" style="font-size:.62rem;"></i> Drafter</span>';
                            } else if(h.role === 'checker'){
                                roleBadge =
                                    '<span class="md-role-badge md-role-checker">' +
                                    '<i class="fas fa-check-double" style="font-size:.62rem;"></i> Checker</span>';
                            } else {
                                roleBadge = '<span class="md-role-none"><i class="fas fa-minus"></i></span>';
                            }

                            /* Kolom Realisasi */
                            var realisasiCell = h.realisasidate
                                ? '<span class="md-realisasi-done">' + h.realisasidate + '</span>'
                                : '<span class="md-realisasi-none"><i class="fas fa-minus"></i></span>';

                            html +=
                                '<tr>' +
                                '<td><span class="md-nodok">' + h.nodokumen + '</span></td>' +
                                '<td style="font-size:.84rem;">' + h.namadokumen + '</td>' +
                                '<td style="text-align:center;"><span class="md-status-badge">' + h.status + '</span></td>' +
                                '<td style="text-align:center;">' + roleBadge + '</td>' +
                                '<td style="text-align:center;">' + realisasiCell + '</td>' +
                                '</tr>';
                        });
                    } else {
                        html =
                            '<tr><td colspan="5" style="text-align:center;padding:48px;color:#94a3b8;">' +
                            '<i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:10px;opacity:.4;"></i>' +
                            'Belum ada dokumen yang dikerjakan.</td></tr>';
                    }
                    $('#mdModalBody').html(html);
                },
                error: function(xhr){
                    var msgs = {403:'Anda tidak memiliki akses.',404:'Data tidak ditemukan.',500:'Server error.'};
                    var msg  = msgs[xhr.status] || 'Terjadi kesalahan. Silakan coba lagi.';
                    $('#mdChipTotal').html('<i class="fas fa-exclamation-circle"></i> Error');
                    $('#mdChipDone, #mdChipDrafter, #mdChipChecker').html('');
                    $('#mdModalFooterInfo').text(msg);
                    $('#mdModalBody').html(
                        '<tr><td colspan="5" style="text-align:center;padding:48px;color:#ef4444;">' +
                        '<i class="fas fa-exclamation-triangle" style="font-size:1.8rem;display:block;margin-bottom:10px;opacity:.6;"></i>' +
                        msg+'</td></tr>'
                    );
                }
            });
        });
    </script>
@endsection