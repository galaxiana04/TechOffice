@extends('layouts.split3')

@section('container2')

{{-- ══════════════════════════════════════════════════
     CUSTOM STYLES
══════════════════════════════════════════════════ --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap');

    :root {
        --ink:       #0d1b2e;
        --ink-light: #2c3e5a;
        --surface:   #eef1f7;
        --white:     #ffffff;
        --border:    #c8d3e8;

        --navy-900:  #0a1628;
        --navy-800:  #0f2040;
        --navy-700:  #163058;
        --navy-600:  #1e4080;
        --navy-500:  #2756a8;
        --navy-400:  #3a6fc4;
        --navy-300:  #6090d8;
        --navy-200:  #a8c0e8;
        --navy-100:  #dce8f8;
        --navy-50:   #f0f5fc;

        --accent-blue:   #1e4080;
        --accent-cyan:   #1a6b8a;
        --accent-green:  #166534;
        --accent-amber:  #92400e;
        --accent-rose:   #991b1b;
        --accent-violet: #4c1d95;
        --accent-teal:   #115e59;
        --accent-maroon: #7f1d1d;
        --accent-orange: #9a3412;
        --accent-dark:   #0a1628;

        --radius-card: 16px;
        --radius-inner: 10px;
        --shadow-card: 0 2px 8px rgba(14,32,64,.08), 0 8px 24px rgba(14,32,64,.07);
        --shadow-hover: 0 8px 32px rgba(14,32,64,.18);
        --transition: all .22s cubic-bezier(.4,0,.2,1);
    }

    /* ── Base ── */
    body { font-family: 'DM Sans', sans-serif; background: #dce6f4; }

    /* ── Pipeline wrapper ── */
    .pipeline-wrapper {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-start;
        padding: 4px 0 24px;
    }

    /* ── Pipeline connector arrow ── */
    .pipeline-step {
        position: relative;
        flex: 0 0 320px;
        max-width: 320px;
    }
    .pipeline-step:not(:last-child)::after {
        content: '›';
        position: absolute;
        right: -16px;
        top: 50px;
        font-size: 28px;
        color: var(--navy-400);
        font-weight: 700;
        z-index: 10;
        pointer-events: none;
        line-height: 1;
    }

    /* ── Card ── */
    .kpanel {
        background: var(--white);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        transition: var(--transition);
        border: 1.5px solid transparent;
    }
    .kpanel:hover {
        box-shadow: var(--shadow-hover);
        border-color: var(--border);
        transform: translateY(-3px);
    }

    /* ── Card Header ── */
    .kpanel-header {
        padding: 18px 20px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-bottom: 1.5px solid rgba(255,255,255,.18);
    }
    .kpanel-header .header-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }
    .kpanel-header .header-left > div {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .kpanel-header .header-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,.18);
        border: 1px solid rgba(255,255,255,.25);
        flex-shrink: 0;
        font-size: 17px;
        color: #fff;
    }
    .kpanel-header .header-title {
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        margin: 0;
        line-height: 1.3;
        word-break: break-word;
    }
    .kpanel-header .header-tools {
        display: flex;
        gap: 4px;
        flex-shrink: 0;
    }
    .kpanel-header .header-tools .btn-tool {
        width: 28px; height: 28px;
        padding: 0;
        border: none;
        background: rgba(255,255,255,.15);
        color: #fff;
        border-radius: 7px;
        cursor: pointer;
        font-size: 12px;
        display: flex; align-items: center; justify-content: center;
        transition: var(--transition);
    }
    .kpanel-header .header-tools .btn-tool:hover {
        background: rgba(255,255,255,.3);
    }

    /* ── Header colour themes — all navy palette ── */
    .h-blue    { background: linear-gradient(135deg, #0a1628 0%, #1e4080 100%); }
    .h-info    { background: linear-gradient(135deg, #0f2040 0%, #2756a8 100%); }
    .h-green   { background: linear-gradient(135deg, #0d1e3c 0%, #163a70 100%); }
    .h-amber   { background: linear-gradient(135deg, #0a1a35 0%, #1a3565 100%); }
    .h-maroon  { background: linear-gradient(135deg, #0a1628 0%, #1c3260 100%); }
    .h-teal    { background: linear-gradient(135deg, #0c1e40 0%, #204878 100%); }
    .h-orange  { background: linear-gradient(135deg, #101f3e 0%, #253d72 100%); }
    .h-dark    { background: linear-gradient(135deg, #060e1c 0%, #0f2040 100%); }

    /* ── Card Body ── */
    .kpanel-body {
        padding: 20px;
    }

    /* ── Info Grid (Doc Info) ── */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 16px;
    }
    .info-cell {
        background: var(--navy-50);
        border-radius: var(--radius-inner);
        padding: 10px 12px;
    }
    .info-cell .ic-label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--navy-400);
        margin-bottom: 3px;
    }
    .info-cell .ic-value {
        font-size: 13px;
        font-weight: 500;
        color: var(--ink);
        line-height: 1.4;
    }
    .info-cell.full-width { grid-column: 1 / -1; }

    /* ── Section title inside body ── */
    .section-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #8b93a7;
        margin: 16px 0 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    /* ── Status badge ── */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .02em;
    }
    .status-pill.open   { background: #fef2f2; color: #dc2626; border: 1.5px solid #fca5a5; }
    .status-pill.closed { background: #f0fdf4; color: #16a34a; border: 1.5px solid #86efac; }

    .rejection-box {
        background: #fef2f2;
        border: 1.5px solid #fca5a5;
        border-radius: var(--radius-inner);
        padding: 10px 12px;
        margin-top: 8px;
    }
    .rejection-box .rej-label {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: .06em; color: #dc2626; margin-bottom: 3px;
    }
    .rejection-box .rej-text {
        font-size: 12px; color: #7f1d1d;
    }

    /* ── Identity Table ── */
    .id-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 4px;
    }
    .id-table th, .id-table td {
        padding: 8px 12px;
        font-size: 13px;
    }
    .id-table th {
        background: var(--navy-100);
        color: var(--navy-700);
        font-weight: 700;
        border-radius: 7px 0 0 7px;
        width: 120px;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .id-table td {
        background: var(--navy-50);
        color: var(--ink);
        font-weight: 600;
        border-radius: 0 7px 7px 0;
        font-family: 'Space Mono', monospace;
        font-size: 12px;
    }

    /* ── File item ── */
    .file-requirement-group {
        background: var(--navy-50);
        border-radius: var(--radius-inner);
        padding: 10px 12px;
        margin-bottom: 8px;
    }
    .file-requirement-group .req-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--ink-light);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .file-requirement-group .req-title::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--navy-600);
        flex-shrink: 0;
    }

    /* ── Action buttons ── */
    .action-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1.5px solid var(--border);
    }
    .kbtn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: var(--transition);
        letter-spacing: .01em;
    }
    .kbtn:hover { transform: translateY(-1px); text-decoration: none; }
    .kbtn-warning  { background: #fef3c7; color: #92400e; }
    .kbtn-warning:hover  { background: #fde68a; color: #78350f; }
    .kbtn-primary  { background: var(--navy-100); color: var(--navy-800); }
    .kbtn-primary:hover  { background: var(--navy-200); color: var(--navy-900); }
    .kbtn-danger   { background: #fef2f2; color: #dc2626; }
    .kbtn-danger:hover   { background: #fee2e2; color: #b91c1c; }
    .kbtn-success  { background: #f0fdf4; color: #15803d; }
    .kbtn-success:hover  { background: #dcfce7; color: #166534; }
    .kbtn-teal     { background: #f0fdfa; color: #0f766e; }
    .kbtn-teal:hover     { background: #ccfbf1; color: #0d6b63; }
    .kbtn-purple   { background: #f5f3ff; color: #6d28d9; }
    .kbtn-purple:hover   { background: #ede9fe; color: #5b21b6; }
    .kbtn-solid-danger { background: var(--navy-700); color: #fff; }
    .kbtn-solid-danger:hover { background: var(--navy-800); }
    .kbtn-solid-warning { background: var(--navy-600); color: #fff; }
    .kbtn-solid-warning:hover { background: var(--navy-700); }

    /* ── Tabs ── */
    .k-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        padding-bottom: 12px;
        border-bottom: 1.5px solid var(--border);
        margin-bottom: 14px;
        border-top: none !important;
    }
    .k-tabs .nav-item { margin: 0; }
    .k-tab-btn {
        padding: 5px 12px;
        border-radius: 7px !important;
        border: 1.5px solid var(--border) !important;
        background: var(--navy-50) !important;
        font-size: 11px;
        font-weight: 600;
        color: var(--ink-light) !important;
        cursor: pointer;
        transition: var(--transition);
        text-transform: uppercase;
        letter-spacing: .04em;
        text-decoration: none !important;
        outline: none;
    }
    .k-tab-btn:hover,
    .k-tab-btn.active {
        background: var(--navy-700) !important;
        border-color: var(--navy-700) !important;
        color: #fff !important;
    }
    /* Kill Bootstrap's default tab underline/border */
    .k-tabs.nav-tabs { border-bottom: 1.5px solid var(--border); }
    .k-tabs.nav-tabs .nav-link { border: 1.5px solid var(--border) !important; }
    .k-tabs.nav-tabs .nav-link.active { border-color: var(--navy-700) !important; }

    /* ── Feedback / Discussion item ── */
    .feedback-card {
        background: var(--navy-50);
        border-radius: var(--radius-inner);
        padding: 14px;
        margin-bottom: 10px;
        border-left: 3px solid var(--border);
        transition: var(--transition);
    }
    .feedback-card:hover { border-left-color: var(--navy-500); background: #f0f5fc; }
    .feedback-card.status-last-accepted { border-left-color: #166534; }
    .feedback-card.status-reviewed { border-left-color: #92400e; }
    .feedback-card.status-draft { border-left-color: #94a3b8; }

    .fc-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .fc-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
    }
    .fc-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--ink);
        margin: 0;
    }
    .fc-role {
        font-size: 11px;
        color: #8b93a7;
        margin: 0;
    }
    .fc-time {
        font-size: 11px;
        color: #adb5c7;
        font-family: 'Space Mono', monospace;
        margin-bottom: 6px;
    }
    .fc-comment {
        font-size: 13px;
        color: var(--ink-light);
        margin-bottom: 6px;
        line-height: 1.5;
    }

    .kbadge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        flex-shrink: 0;
    }
    .kbadge.b-success  { background: #dcfce7; color: #15803d; }
    .kbadge.b-warning  { background: #fef3c7; color: #92400e; }
    .kbadge.b-info     { background: #dbeafe; color: #1d4ed8; }

    .fc-status-row {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
    }
    .fc-status-label {
        font-size: 11px;
        font-weight: 600;
        color: #8b93a7;
    }
    .fc-status-value {
        font-size: 11px;
        color: var(--ink);
        font-weight: 500;
    }

    .fc-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px dashed var(--border);
    }

    /* ── Approval Status indicator ── */
    .approval-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 0;
        margin-top: 3px;
    }
    .approval-chip.approved   { background: rgba(255,255,255,.22); color: #fff; border: 1px solid rgba(255,255,255,.35); }
    .approval-chip.unapproved { background: rgba(255,80,80,.25);   color: #ffd5d5; border: 1px solid rgba(255,120,120,.4); }

    /* ── Add Comment Form ── */
    .add-form {
        background: var(--navy-50);
        border-radius: var(--radius-inner);
        padding: 16px;
        margin-top: 12px;
        border: 1.5px dashed var(--border);
    }
    .add-form label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #8b93a7;
        margin-bottom: 5px;
        display: block;
    }
    .add-form .form-control,
    .add-form .form-control-file {
        border: 1.5px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        padding: 8px 10px;
        width: 100%;
        transition: border-color .15s;
        background: var(--white);
    }
    .add-form .form-control:focus {
        border-color: var(--navy-600);
        outline: none;
        box-shadow: 0 0 0 3px rgba(30,64,128,.12);
    }
    .add-form .form-group { margin-bottom: 10px; }

    /* ── Pipeline step header area ── */
    .step-num {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: rgba(255,255,255,.25);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        font-family: 'Space Mono', monospace;
    }

    /* ── Discussion status header ── */
    .disc-status-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--navy-50);
        border: 1px solid var(--navy-100);
        border-radius: var(--radius-inner);
        padding: 10px 12px;
        margin-bottom: 12px;
    }
    .disc-status-label { font-size: 11px; font-weight: 600; color: #8b93a7; }
    .disc-status-value { font-size: 12px; font-weight: 700; color: var(--ink); }

    /* ── Warning notice ── */
    .knotice {
        background: #fffbeb;
        border: 1.5px solid #fde68a;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12px;
        color: #92400e;
        margin-bottom: 12px;
        display: flex;
        gap: 6px;
        align-items: flex-start;
    }
    .knotice i { margin-top: 1px; flex-shrink: 0; color: #d97706; }

    /* ── Send-to action block in header ── */
    .header-send-block {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
</style>

<div class="pipeline-wrapper">

    {{-- ═══════════════════════════════════════════
         STEP 1 — Informasi Dokumen
    ═══════════════════════════════════════════ --}}
    <div class="pipeline-step">
        <div class="kpanel">
            <div class="kpanel-header h-blue">
                <div class="header-left">
                    <div class="header-icon"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <p class="header-title">Informasi Dokumen</p>
                    </div>
                </div>
                <div class="header-tools">
                    <button type="button" class="btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn-tool" data-card-widget="remove" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="kpanel-body">

                <div class="section-label">📌 Informasi Umum</div>
                <div class="info-grid">
                    <div class="info-cell">
                        <div class="ic-label">No. Dokumen</div>
                        <div class="ic-value">{{ $document->no_dokumen ?? 'Belum diterbitkan' }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="ic-label">Tipe Proyek</div>
                        <div class="ic-value">{{ $document->projectType->title }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="ic-label">Distributor Dokumen</div>
                        <div class="ic-value">{{ $document->unit_distributor_id ? $document->unitDistributor->name : 'Belum ditentukan' }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="ic-label"><i class="fas fa-industry" style="color:var(--navy-500)"></i> Supplier</div>
                        <div class="ic-value">{{ $document->supplier->name }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="ic-label">Tanggal Terbit</div>
                        <div class="ic-value" style="font-family:'Space Mono',monospace;font-size:11px">{{ $document->created_at->format('d M Y H:i') }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="ic-label">Catatan</div>
                        <div class="ic-value">{{ $document->note ?? '—' }}</div>
                    </div>
                </div>

                {{-- Status --}}
                <div class="section-label">🟡 Status</div>
                <span class="status-pill {{ $document->status === 'Terbuka' ? 'open' : 'closed' }}" id="statusBadge">
                    <i class="fas {{ $document->status === 'Terbuka' ? 'fa-lock-open' : 'fa-lock' }}"></i>
                    {{ $document->status }}
                </span>
                @if (!is_null($document->rejectedreason))
                    <div class="rejection-box">
                        <div class="rej-label"><i class="fas fa-exclamation-triangle"></i> Alasan Tertolak</div>
                        <div class="rej-text">{{ $document->rejectedreason }}</div>
                    </div>
                @endif

                {{-- Identitas --}}
                <div class="section-label">🆔 Identitas</div>
                <table class="id-table">
                    <tr>
                        <th>Nama Komat</th>
                        <td>{{ $document->komatProcess->komat_name }}</td>
                    </tr>
                    <tr>
                        <th>Revisi</th>
                        <td>{{ $document->revision }}</td>
                    </tr>
                    <tr>
                        <th>No. Diskusi</th>
                        <td>{{ $document->discussion_number }}</td>
                    </tr>
                </table>

                {{-- File TTD --}}
                <div class="section-label">📝 File TTD</div>
                @foreach ($document->komatHistReqs as $komatHistReq)
                    <div class="file-requirement-group">
                        <div class="req-title">{{ $komatHistReq->komatRequirement->name }}</div>
                        @foreach ($document->feedbacks->where('komat_requirement_id', $komatHistReq->komatRequirement->id) as $userinformation)
                            @if ($userinformation->files && $userinformation->komatPosition->level === 'logistik_upload')
                                @foreach ($userinformation->files as $file)
                                    @include('newmemo.memo.fileinfo', ['file' => $file, 'userinformation' => $userinformation])
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                @endforeach

                {{-- Actions --}}
                <div class="action-row">
                    @if (
                        ($yourauth->rule === $document->unitDistributor->name || $yourauth->rule === 'MTPR') &&
                        $document->status === 'Terbuka')
                        <a href="{{ route('komatprocesshistory.edit', $document->id) }}" class="kbtn kbtn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    @endif
                    <a class="kbtn kbtn-primary"
                       href="{{ route('komatprocesshistory.report', ['id' => $document->id, 'rule' => $yourauth->rule]) }}">
                        <i class="fas fa-chart-line"></i> Progress
                    </a>
                </div>

            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════
         STEP 2 — Komat Requirements (Discussion)
    ═══════════════════════════════════════════ --}}
    @foreach ($document->komatHistReqs as $komatHistReq)
        @php
            $allApproved = $komatHistReq->komatPositions
                ->where('level', 'discussion')
                ->every(fn($position) => $position->status_process === 'done');
        @endphp
        <div class="pipeline-step">
            <div class="kpanel">
                <div class="kpanel-header h-info">
                    <div class="header-left">
                        <div class="header-icon"><i class="fas fa-comments"></i></div>
                        <div>
                            <p class="header-title">{{ $komatHistReq->komatRequirement->name }}</p>
                            <span class="approval-chip {{ $allApproved ? 'approved' : 'unapproved' }}">
                                <i class="fas {{ $allApproved ? 'fa-check' : 'fa-times' }}"></i>
                                {{ $allApproved ? 'All Approved' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                    <div class="header-tools">
                        <button type="button" class="btn-tool" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
                        <button type="button" class="btn-tool" data-card-widget="remove" title="Remove"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <div class="kpanel-body">

                    {{-- Tabs --}}
                    <ul class="nav nav-tabs k-tabs" id="unitTabs-{{ $komatHistReq->id }}" role="tablist">
                        @foreach ($units as $unit)
                            @php
                                $isChecked = $komatHistReq->komatPositions
                                    ->where('unit_id', $unit->id)
                                    ->where('level', 'discussion')
                                    ->isNotEmpty();
                            @endphp
                            @if ($isChecked)
                                <li class="nav-item" role="presentation">
                                    <button class="k-tab-btn nav-link {{ $loop->first ? 'active' : '' }}"
                                        id="tab-{{ $komatHistReq->id }}-{{ $unit->id }}"
                                        data-bs-toggle="tab"
                                        data-bs-target="#content-{{ $komatHistReq->id }}-{{ $unit->id }}"
                                        type="button" role="tab"
                                        aria-controls="content-{{ $komatHistReq->id }}-{{ $unit->id }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ $unit->name }}
                                    </button>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    <div class="tab-content" id="unitTabContent-{{ $komatHistReq->id }}">
                        @foreach ($units as $unit)
                            @php
                                $isChecked = $komatHistReq->komatPositions->where('unit_id', $unit->id)->where('level', 'discussion')->isNotEmpty();
                                $feedbacks = $isChecked
                                    ? $komatHistReq->komatPositions->where('unit_id', $unit->id)->where('level', 'discussion')->first()->feedbacks
                                    : collect([]);
                                $komatposition = $isChecked
                                    ? $komatHistReq->komatPositions->where('unit_id', $unit->id)->where('level', 'discussion')->first()
                                    : null;
                            @endphp
                            @if ($isChecked)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="content-{{ $komatHistReq->id }}-{{ $unit->id }}"
                                    role="tabpanel"
                                    aria-labelledby="tab-{{ $komatHistReq->id }}-{{ $unit->id }}">

                                    <div class="disc-status-header">
                                        <span class="disc-status-label">Diskusi Status</span>
                                        <div style="display:flex;align-items:center;gap:6px">
                                            <span class="disc-status-value">{{ $komatposition ? $komatposition->status : 'N/A' }}</span>
                                            <i class="fas {{ $komatposition && $komatposition->status != 'draft' ? 'fa-check-circle' : 'fa-clock' }}"
                                               style="color:{{ $komatposition && $komatposition->status != 'draft' ? '#16a34a' : '#d97706' }};font-size:14px"></i>
                                        </div>
                                    </div>

                                    @foreach ($feedbacks as $feedback)
                                        @php
                                            $fcClass = $feedback->status === 'last_accepted' ? 'status-last-accepted' :
                                                       ($feedback->status === 'reviewed' ? 'status-reviewed' : 'status-draft');
                                        @endphp
                                        <div class="feedback-card {{ $fcClass }}">
                                            <div style="display:flex;gap:10px">
                                                <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1,5) }}.png"
                                                     alt="Avatar" class="fc-avatar">
                                                <div style="flex:1;min-width:0">
                                                    <div class="fc-header">
                                                        <div>
                                                            <p class="fc-name">{{ $feedback->user_name }}</p>
                                                            <p class="fc-role">{{ $feedback->user_rule }}</p>
                                                        </div>
                                                        <span class="kbadge {{ $feedback->status === 'last_accepted' ? 'b-success' : ($feedback->status === 'draft' ? 'b-warning' : 'b-info') }}">
                                                            {{ ucfirst($feedback->status) }}
                                                        </span>
                                                    </div>
                                                    <p class="fc-time">{{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                    <p class="fc-comment">{{ $feedback->comment }}</p>
                                                    <div class="fc-status-row">
                                                        <span class="fc-status-label">Feedback Status:</span>
                                                        <span class="fc-status-value">{{ $feedback->feedback_status }}</span>
                                                    </div>
                                                    @if ($feedback->files->isNotEmpty())
                                                        <div style="margin-bottom:6px">
                                                            <div class="fc-status-label" style="margin-bottom:4px">Files:</div>
                                                            <ul class="list-unstyled mb-0">
                                                                @foreach ($feedback->files as $file)
                                                                    <li class="mb-1">
                                                                        @include('komatprocesshistory.show.fileinfo', ['file' => $file, 'userinformation' => $feedback])
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    <div class="fc-actions">
                                                        @if (
                                                            $feedback->status === 'draft' &&
                                                            $document->status === 'Terbuka' &&
                                                            $komatposition && $komatposition->status === 'draft' &&
                                                            strpos($yourauth->rule, 'Manager') !== false)
                                                            <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $unit->id, 'feedbackId' => $feedback->id, 'level' => 'discussion']) }}" method="POST" style="display:inline">
                                                                @csrf @method('PUT')
                                                                <button type="submit" class="kbtn kbtn-warning"><i class="fas fa-eye"></i> Reviewed</button>
                                                            </form>
                                                        @elseif (
                                                            $feedback->status === 'reviewed' &&
                                                            strpos($yourauth->rule, 'Manager') !== false &&
                                                            $document->status === 'Terbuka' &&
                                                            $komatposition && $komatposition->status === 'draft')
                                                            <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $unit->id, 'feedbackId' => $feedback->id, 'level' => 'discussion']) }}" method="POST" style="display:inline">
                                                                @csrf @method('PUT')
                                                                <button type="submit" class="kbtn kbtn-success"><i class="fas fa-check"></i> Selesai</button>
                                                            </form>
                                                        @endif
                                                        @if ($document->status === 'Terbuka' && $document->unit_distributor_id === $yourauth->unit_id)
                                                            <form action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $unit->id, 'feedbackId' => $feedback->id, 'level' => 'discussion']) }}" method="POST" style="display:inline">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="kbtn kbtn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')"><i class="fas fa-trash"></i> Delete</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if (
                                        $document->status === 'Terbuka' &&
                                        $komatposition && $komatposition->status === 'draft' &&
                                        $yourauth->unit_id === $unit->id)
                                        <div class="add-form">
                                            <form action="{{ route('komatprocesshistory.addComment', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $unit->id]) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @if (config('app.url') === 'https://inka.goovicess.com')
                                                    <div class="form-group">
                                                        <label>Jumlah File (Sementara)</label>
                                                        <input type="number" name="filecount" class="form-control" min="0" max="100" step="1" value="0">
                                                    </div>
                                                @else
                                                    <div class="form-group">
                                                        <label>Pilih File</label>
                                                        <input type="file" name="file[]" class="form-control" multiple>
                                                    </div>
                                                @endif
                                                <div class="form-group">
                                                    <label>Status Feedback</label>
                                                    <select name="feedback_status" class="form-control" required>
                                                        <option value="approved">Approved</option>
                                                        <option value="notapproved">Not Approved</option>
                                                        <option value="withremarks">Approved with Remark</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tambah Komentar</label>
                                                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                                                </div>
                                                <button type="submit" class="kbtn kbtn-solid-warning" style="background:#163058;color:#fff">
                                                    <i class="fas fa-paper-plane"></i> Kirim Komentar
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    @endforeach


    {{-- ═══════════════════════════════════════════
         STEP 3 — Resume Feedback
    ═══════════════════════════════════════════ --}}
    @if (
        $document->komatHistReqs->every(function ($komatHistReq) {
            $discussions = $komatHistReq->komatPositions->where('level', 'discussion');
            return $discussions->isNotEmpty() && $discussions->every(fn($pos) => $pos->status_process === 'done');
        }))
        @php
            $allResumeApproved = $komatHistReq->komatPositions->where('level', 'resume')->every(fn($position) => $position->status_process === 'done');
            $permissiontosendSM = $komatHistReq->komatPositions->where('level', 'resume')->isNotEmpty() &&
                $komatHistReq->komatPositions->where('level', 'resume')->every(fn($position) => $position->feedbacks->contains('status', 'last_accepted'));
            $issmlevelexist = $komatHistReq->komatPositions->where('level', 'sm_level')->isNotEmpty();
        @endphp
        <div class="pipeline-step">
            <div class="kpanel">
                <div class="kpanel-header h-green">
                    <div class="header-left">
                        <div class="header-icon"><i class="fas fa-clipboard-check"></i></div>
                        <div>
                            <p class="header-title">Resume Feedback</p>
                            <span class="approval-chip {{ $allResumeApproved ? 'approved' : 'unapproved' }}">
                                <i class="fas {{ $allResumeApproved ? 'fa-check' : 'fa-times' }}"></i>
                                {{ $allResumeApproved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                    <div class="header-send-block">
                        @if ($permissiontosendSM && !$issmlevelexist)
                            <form action="{{ route('komatprocesshistory.copyTo', ['id' => $document->id, 'level' => 'sm_level']) }}" method="POST" style="display:inline">
                                @csrf
                                @if ($document->unit_distributor_id == 2)
                                    <input type="hidden" name="sendto" value="Senior Manager Engineering">
                                @elseif (in_array($document->unit_distributor_id, [5,6,7,8]))
                                    <input type="hidden" name="sendto" value="Senior Manager Desain">
                                @else
                                    <input type="hidden" name="sendto" value="Senior Manager Teknologi Produksi">
                                @endif
                                <button type="submit" class="kbtn kbtn-solid-danger" onclick="return confirm('Kirim resume ke SM?')">
                                    <i class="fas fa-paper-plane"></i> Kirim ke SM
                                </button>
                            </form>
                        @elseif ($allResumeApproved && $issmlevelexist)
                            <span style="color:#86efac;font-size:18px"><i class="fas fa-check-circle"></i></span>
                        @endif
                        <div class="header-tools">
                            <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
                <div class="kpanel-body">

                    <ul class="nav nav-tabs k-tabs" id="resumeFeedbackTabs" role="tablist">
                        @foreach ($document->komatHistReqs as $komatHistReq)
                            <li class="nav-item" role="presentation">
                                <button class="k-tab-btn nav-link {{ $loop->first ? 'active' : '' }}"
                                    id="resume-tab-{{ $komatHistReq->id }}" data-bs-toggle="tab"
                                    data-bs-target="#resume-content-{{ $komatHistReq->id }}" type="button"
                                    role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ $komatHistReq->komatRequirement->name }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content" id="resumeFeedbackTabContent">
                        @foreach ($document->komatHistReqs as $komatHistReq)
                            @php
                                $resumeFeedback = $komatHistReq->komatPositions->where('level', 'resume')->first();
                                $feedbacks = $resumeFeedback ? $resumeFeedback->feedbacks : collect([]);
                                $isthereonefeedbackandstatusislastaccepted = $feedbacks->isNotEmpty() && $feedbacks->contains('status', 'last_accepted');
                            @endphp
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                id="resume-content-{{ $komatHistReq->id }}" role="tabpanel">

                                <div class="disc-status-header">
                                    <span class="disc-status-label">Resume Status: {{ $komatHistReq->komatRequirement->name }}</span>
                                    <i class="fas {{ $isthereonefeedbackandstatusislastaccepted ? 'fa-check-circle' : 'fa-times-circle' }}"
                                       style="color:{{ $isthereonefeedbackandstatusislastaccepted ? '#16a34a' : '#dc2626' }};font-size:16px"></i>
                                </div>

                                <div class="knotice">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Setiap jenis dokumen wajib memiliki hanya satu feedback.</span>
                                </div>

                                @foreach ($feedbacks as $feedback)
                                    @php $fcClass = $feedback->status === 'last_accepted' ? 'status-last-accepted' : ($feedback->status === 'reviewed' ? 'status-reviewed' : 'status-draft'); @endphp
                                    <div class="feedback-card {{ $fcClass }}">
                                        <div style="display:flex;gap:10px">
                                            <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1,5) }}.png" alt="Avatar" class="fc-avatar">
                                            <div style="flex:1;min-width:0">
                                                <div class="fc-header">
                                                    <div><p class="fc-name">{{ $feedback->user_name }}</p><p class="fc-role">{{ $feedback->user_rule }}</p></div>
                                                    <span class="kbadge {{ $feedback->status === 'last_accepted' ? 'b-success' : ($feedback->status === 'draft' ? 'b-warning' : 'b-info') }}">{{ ucfirst($feedback->status) }}</span>
                                                </div>
                                                <p class="fc-time">{{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                <p class="fc-comment">{{ $feedback->comment }}</p>
                                                <div class="fc-status-row"><span class="fc-status-label">Status:</span><span class="fc-status-value">{{ $feedback->feedback_status }}</span></div>
                                                @if ($feedback->files->isNotEmpty())
                                                    <ul class="list-unstyled mb-0">@foreach ($feedback->files as $file)<li class="mb-1">@include('komatprocesshistory.show.fileinfo', ['file' => $file, 'userinformation' => $feedback])</li>@endforeach</ul>
                                                @endif
                                                <div class="fc-actions">
                                                    @if ($feedback->status === 'draft' && $document->status === 'Terbuka' && $resumeFeedback && $resumeFeedback->status === 'draft' && strpos($yourauth->rule, 'Manager') !== false)
                                                        <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'resume']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-warning"><i class="fas fa-eye"></i> Reviewed</button></form>
                                                    @elseif ($feedback->status === 'reviewed' && strpos($yourauth->rule, 'Manager') !== false && $document->status === 'Terbuka' && $resumeFeedback && $resumeFeedback->status === 'draft')
                                                        <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'resume']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-success"><i class="fas fa-check"></i> Selesai</button></form>
                                                    @endif
                                                    @if ($document->status === 'Terbuka' && $document->unit_distributor_id === $yourauth->unit_id)
                                                        <form action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'resume']) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="kbtn kbtn-danger" onclick="return confirm('Hapus feedback ini?')"><i class="fas fa-trash"></i> Delete</button></form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if ($yourauth->unit_id == $document->unit_distributor_id && !$isthereonefeedbackandstatusislastaccepted)
                                    <div class="add-form">
                                        <form action="{{ route('komatprocesshistory.addResumeFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id]) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group"><label>Pilih File</label><input type="file" name="file[]" class="form-control" multiple></div>
                                            <div class="form-group"><label>Status Feedback</label><select name="feedback_status" class="form-control" required><option value="approved">Approved</option><option value="notapproved">Not Approved</option><option value="withremarks">Approved with Remark</option></select></div>
                                            <div class="form-group"><label>Tambah Komentar</label><textarea name="comment" class="form-control" rows="3" required></textarea></div>
                                            <button type="submit" class="kbtn" style="background:#1e4080;color:#fff"><i class="fas fa-paper-plane"></i> Kirim Komentar</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════
         STEP 4 — Validasi SM Level
    ═══════════════════════════════════════════ --}}
    @if ($document->komatHistReqs->contains(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'sm_level')->isNotEmpty()))
        @php
            $allSMLevelApproved = $document->komatHistReqs->every(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'sm_level')->every(fn($position) => $position->status_process === 'done'));
            $isLogistikDoneExist = $document->komatHistReqs->contains(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'mtpr_review')->isNotEmpty());
        @endphp
        <div class="pipeline-step">
            <div class="kpanel">
                <div class="kpanel-header h-amber">
                    <div class="header-left">
                        <div class="header-icon"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <p class="header-title">Validasi SM Level</p>
                            <span class="approval-chip {{ $allSMLevelApproved ? 'approved' : 'unapproved' }}">
                                <i class="fas {{ $allSMLevelApproved ? 'fa-check' : 'fa-times' }}"></i>
                                {{ $allSMLevelApproved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                    <div class="header-send-block">
                        @if ($allSMLevelApproved && !$isLogistikDoneExist)
                            @if ($document->status == 'Terbuka')
                                <button type="button" class="kbtn kbtn-teal reject-sm-btn" data-form-id="reject-sm-form">
                                    <i class="fas fa-undo"></i> Tolak
                                </button>
                                <form id="reject-sm-form" action="{{ route('komatprocesshistory.close', ['id' => $document->id]) }}" method="POST" style="display:none">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="Tertutup">
                                    <input type="hidden" name="documentstatus" value="rejectedbysm">
                                    <input type="hidden" name="needincreaserevision" value="yes">
                                    <input type="hidden" name="rejectedreason" id="rejectedreason-sm">
                                </form>
                                <form action="{{ route('komatprocesshistory.copyTo', ['id' => $document->id, 'level' => 'mtpr_review']) }}" method="POST" style="display:inline">
                                    @csrf
                                    <input type="hidden" name="sendto" value="MTPR">
                                    <button type="submit" class="kbtn kbtn-solid-danger" onclick="return confirm('Kirim ke MTPR?')"><i class="fas fa-paper-plane"></i> MTPR</button>
                                </form>
                            @endif
                        @elseif ($allSMLevelApproved && $isLogistikDoneExist)
                            <span style="color:#86efac;font-size:18px"><i class="fas fa-envelope"></i></span>
                        @endif
                        <div class="header-tools">
                            <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
                <div class="kpanel-body">
                    <ul class="nav nav-tabs k-tabs" id="smLevelFeedbackTabs" role="tablist">
                        @foreach ($document->komatHistReqs as $komatHistReq)
                            @if ($komatHistReq->komatPositions->where('level', 'sm_level')->isNotEmpty())
                                <li class="nav-item" role="presentation">
                                    <button class="k-tab-btn nav-link {{ $loop->first ? 'active' : '' }}" id="sm-tab-{{ $komatHistReq->id }}" data-bs-toggle="tab" data-bs-target="#sm-content-{{ $komatHistReq->id }}" type="button" role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $komatHistReq->komatRequirement->name }}</button>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <div class="tab-content" id="smLevelFeedbackTabContent">
                        @foreach ($document->komatHistReqs as $komatHistReq)
                            @php $smLevelPosition = $komatHistReq->komatPositions->where('level', 'sm_level')->first(); $feedbacks = $smLevelPosition ? $smLevelPosition->feedbacks : collect([]); $isLastAccepted = $feedbacks->contains('status', 'last_accepted'); @endphp
                            @if ($smLevelPosition)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="sm-content-{{ $komatHistReq->id }}" role="tabpanel">
                                    <div class="disc-status-header">
                                        <span class="disc-status-label">SM Level: {{ $komatHistReq->komatRequirement->name }}</span>
                                        <i class="fas {{ $isLastAccepted ? 'fa-check-circle' : 'fa-times-circle' }}" style="color:{{ $isLastAccepted ? '#16a34a' : '#dc2626' }};font-size:16px"></i>
                                    </div>
                                    @foreach ($feedbacks as $feedback)
                                        @php $fcClass = $feedback->status === 'last_accepted' ? 'status-last-accepted' : ($feedback->status === 'reviewed' ? 'status-reviewed' : 'status-draft'); @endphp
                                        <div class="feedback-card {{ $fcClass }}">
                                            <div style="display:flex;gap:10px">
                                                <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1,5) }}.png" alt="Avatar" class="fc-avatar">
                                                <div style="flex:1;min-width:0">
                                                    <div class="fc-header">
                                                        <div><p class="fc-name">{{ $feedback->user_name }}</p><p class="fc-role">{{ $feedback->user_rule }}</p></div>
                                                        <span class="kbadge {{ $feedback->status === 'last_accepted' ? 'b-success' : ($feedback->status === 'draft' ? 'b-warning' : 'b-info') }}">{{ ucfirst($feedback->status) }}</span>
                                                    </div>
                                                    <p class="fc-time">{{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                    <p class="fc-comment">{{ $feedback->comment }}</p>
                                                    <div class="fc-status-row"><span class="fc-status-label">Status:</span><span class="fc-status-value">{{ $feedback->feedback_status }}</span></div>
                                                    @if ($feedback->files->isNotEmpty())<ul class="list-unstyled mb-0">@foreach ($feedback->files as $file)<li class="mb-1">@include('komatprocesshistory.show.fileinfo', ['file' => $file, 'userinformation' => $feedback])</li>@endforeach</ul>@endif
                                                    <div class="fc-actions">
                                                        @if ($feedback->status === 'draft' && $document->status === 'Terbuka' && $smLevelPosition && $smLevelPosition->status === 'draft' && strpos($yourauth->rule, 'Manager') !== false)
                                                            <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'sm_level']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-warning"><i class="fas fa-eye"></i> Reviewed</button></form>
                                                        @elseif ($feedback->status === 'reviewed' && $document->status === 'Terbuka' && $smLevelPosition && $smLevelPosition->status === 'draft' && strpos($yourauth->rule, 'Manager') !== false)
                                                            <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'sm_level']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-success"><i class="fas fa-check"></i> Selesai</button></form>
                                                        @endif
                                                        @if ($document->status === 'Terbuka' && $document->unit_distributor_id === $yourauth->unit_id)
                                                            <form action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'sm_level']) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="kbtn kbtn-danger" onclick="return confirm('Hapus feedback ini?')"><i class="fas fa-trash"></i> Delete</button></form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════
         STEP 5 — Validasi MTPR
    ═══════════════════════════════════════════ --}}
    @if ($document->komatHistReqs->contains(fn($histReq) => $histReq->komatPositions->where('level', 'mtpr_review')->isNotEmpty()))
        @php
            $allMtprApproved = $document->komatHistReqs->every(fn($histReq) => $histReq->komatPositions->where('level', 'mtpr_review')->every(fn($pos) => $pos->status_process === 'done'));
            $isLogistikDoneExist = $document->komatHistReqs->contains(fn($histReq) => $histReq->komatPositions->where('level', 'logistik_done')->isNotEmpty());
        @endphp
        <div class="pipeline-step">
            <div class="kpanel">
                <div class="kpanel-header h-maroon">
                    <div class="header-left">
                        <div class="header-icon"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <p class="header-title">Validasi MTPR</p>
                            <span class="approval-chip {{ $allMtprApproved ? 'approved' : 'unapproved' }}">
                                <i class="fas {{ $allMtprApproved ? 'fa-check' : 'fa-times' }}"></i>
                                {{ $allMtprApproved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                    <div class="header-send-block">
                        @if ($allMtprApproved && !$isLogistikDoneExist)
                            <form action="{{ route('komatprocesshistory.copyTo', ['id' => $document->id, 'level' => 'logistik_done']) }}" method="POST" style="display:inline">
                                @csrf
                                <input type="hidden" name="sendto" value="Logistik">
                                <button type="submit" class="kbtn kbtn-solid-danger" onclick="return confirm('Kirim ke Logistik Done?')"><i class="fas fa-paper-plane"></i> Logistik</button>
                            </form>
                        @elseif ($allMtprApproved && $isLogistikDoneExist)
                            <span style="color:#fca5a5;font-size:18px"><i class="fas fa-envelope"></i></span>
                        @endif
                        <div class="header-tools">
                            <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
                <div class="kpanel-body">
                    <ul class="nav nav-tabs k-tabs" id="mtprReviewTabs" role="tablist">
                        @foreach ($document->komatHistReqs as $histReq)
                            @if ($histReq->komatPositions->where('level', 'mtpr_review')->isNotEmpty())
                                <li class="nav-item" role="presentation">
                                    <button class="k-tab-btn nav-link {{ $loop->first ? 'active' : '' }}" id="mtpr-tab-{{ $histReq->id }}" data-bs-toggle="tab" data-bs-target="#mtpr-content-{{ $histReq->id }}" type="button" role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $histReq->komatRequirement->name }}</button>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <div class="tab-content" id="mtprReviewTabContent">
                        @foreach ($document->komatHistReqs as $histReq)
                            @php $mtprReviewPos = $histReq->komatPositions->where('level', 'mtpr_review')->first(); $mtprFeedbacks = $mtprReviewPos ? $mtprReviewPos->feedbacks : collect([]); $isMtprAccepted = $mtprFeedbacks->contains('status', 'last_accepted'); @endphp
                            @if ($mtprReviewPos)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="mtpr-content-{{ $histReq->id }}" role="tabpanel">
                                    <div class="disc-status-header">
                                        <span class="disc-status-label">MTPR: {{ $histReq->komatRequirement->name }}</span>
                                        <i class="fas {{ $isMtprAccepted ? 'fa-check-circle' : 'fa-times-circle' }}" style="color:{{ $isMtprAccepted ? '#16a34a' : '#dc2626' }};font-size:16px"></i>
                                    </div>
                                    @foreach ($mtprFeedbacks as $mtprFeedback)
                                        @php $fcClass = $mtprFeedback->status === 'last_accepted' ? 'status-last-accepted' : ($mtprFeedback->status === 'reviewed' ? 'status-reviewed' : 'status-draft'); @endphp
                                        <div class="feedback-card {{ $fcClass }}">
                                            <div style="display:flex;gap:10px">
                                                <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1,5) }}.png" alt="Avatar" class="fc-avatar">
                                                <div style="flex:1;min-width:0">
                                                    <div class="fc-header">
                                                        <div><p class="fc-name">{{ $mtprFeedback->user_name }}</p><p class="fc-role">{{ $mtprFeedback->user_rule }}</p></div>
                                                        <span class="kbadge {{ $mtprFeedback->status === 'last_accepted' ? 'b-success' : ($mtprFeedback->status === 'draft' ? 'b-warning' : 'b-info') }}">{{ ucfirst($mtprFeedback->status) }}</span>
                                                    </div>
                                                    <p class="fc-time">{{ $mtprFeedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                    <p class="fc-comment">{{ $mtprFeedback->comment }}</p>
                                                    <div class="fc-status-row"><span class="fc-status-label">Status:</span><span class="fc-status-value">{{ $mtprFeedback->feedback_status }}</span></div>
                                                    @if ($mtprFeedback->files->isNotEmpty())<ul class="list-unstyled mb-0">@foreach ($mtprFeedback->files as $mtprFile)<li class="mb-1">@include('komatprocesshistory.show.fileinfo', ['file' => $mtprFile, 'userinformation' => $mtprFeedback])</li>@endforeach</ul>@endif
                                                    <div class="fc-actions">
                                                        @if ($mtprFeedback->status === 'draft' && $document->status === 'Terbuka' && $mtprReviewPos && $mtprReviewPos->status === 'draft' && strpos($yourauth->rule, 'MTPR') !== false)
                                                            <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $histReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $mtprFeedback->id, 'level' => 'mtpr_review']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-warning"><i class="fas fa-eye"></i> Reviewed</button></form>
                                                        @elseif ($mtprFeedback->status === 'reviewed' && $document->status === 'Terbuka' && $mtprReviewPos && $mtprReviewPos->status === 'draft' && strpos($yourauth->rule, 'MTPR') !== false)
                                                            <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $histReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $mtprFeedback->id, 'level' => 'mtpr_review']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-success"><i class="fas fa-check"></i> Selesai</button></form>
                                                        @endif
                                                        @if ($document->status === 'Terbuka' && $document->unit_distributor_id === $yourauth->unit_id)
                                                            <form action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $histReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $mtprFeedback->id, 'level' => 'mtpr_review']) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="kbtn kbtn-danger" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i> Delete</button></form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($yourauth->rule == 'MTPR')
                                        <div class="add-form">
                                            <form action="{{ route('komatprocesshistory.addMTPRFeedback', ['id' => $document->id, 'komatHistReqId' => $histReq->id, 'unitId' => $yourauth->unit_id]) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group"><label>Pilih File</label><input type="file" name="file[]" class="form-control" multiple></div>
                                                <div class="form-group"><label>Status Feedback</label><select name="feedback_status" class="form-control" required><option value="approved">Approved</option><option value="notapproved">Not Approved</option><option value="withremarks">Approved with Remark</option></select></div>
                                                <div class="form-group"><label>Tambah Komentar</label><textarea name="comment" class="form-control" rows="3" required></textarea></div>
                                                <button type="submit" class="kbtn" style="background:#0f2040;color:#fff"><i class="fas fa-paper-plane"></i> Kirim Komentar</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════
         STEP 6 — Validasi Logistik
    ═══════════════════════════════════════════ --}}
    @if ($document->komatHistReqs->contains(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'logistik_done')->isNotEmpty()))
        @php
            $allLogistikDoneApproved = $document->komatHistReqs->every(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'logistik_done')->every(fn($position) => $position->status_process === 'done'));
        @endphp
        <div class="pipeline-step">
            <div class="kpanel">
                <div class="kpanel-header h-teal">
                    <div class="header-left">
                        <div class="header-icon"><i class="fas fa-truck"></i></div>
                        <div>
                            <p class="header-title">Validasi Logistik</p>
                            <span class="approval-chip {{ $allLogistikDoneApproved ? 'approved' : 'unapproved' }}">
                                <i class="fas {{ $allLogistikDoneApproved ? 'fa-check' : 'fa-times' }}"></i>
                                {{ $allLogistikDoneApproved ? 'Approved' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                    <div class="header-send-block">
                        @if ($document->status === 'Terbuka' && $allLogistikDoneApproved)
                            @if ($document->logisticauthoritylevel === 'verifiednotneeded')
                                <form action="{{ route('komatprocesshistory.close', ['id' => $document->id]) }}" method="POST" style="display:inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="Tertutup">
                                    <input type="hidden" name="documentstatus" value="approved">
                                    <input type="hidden" name="needincreaserevision" value="no">
                                    <button type="submit" class="kbtn kbtn-solid-warning" onclick="return confirm('Tutup dokumen ini?')"><i class="fas fa-lock"></i> Tutup</button>
                                </form>
                            @else
                                @php $issendtoManagerLogistik = $document->komatHistReqs->every(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'managerlogistikneeded')->isNotEmpty()); @endphp
                                @if (!$issendtoManagerLogistik)
                                    <button type="button" class="kbtn kbtn-purple reject-logistik-btn" data-form-id="reject-logistik-form">
                                        <i class="fas fa-undo"></i> Balik
                                    </button>
                                    <form id="reject-logistik-form" action="{{ route('komatprocesshistory.close', ['id' => $document->id]) }}" method="POST" style="display:none">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="Tertutup">
                                        <input type="hidden" name="documentstatus" value="rejectedbylogistik">
                                        <input type="hidden" name="needincreaserevision" value="yes">
                                        <input type="hidden" name="rejectedreason" id="rejectedreason-logistik">
                                    </form>
                                    <form action="{{ route('komatprocesshistory.copyTo', ['id' => $document->id, 'level' => 'managerlogistikneeded']) }}" method="POST" style="display:inline">
                                        @csrf
                                        <input type="hidden" name="sendto" value="Logistik">
                                        <button type="submit" class="kbtn kbtn-solid-danger" onclick="return confirm('Kirim ke Manager Logistik?')"><i class="fas fa-paper-plane"></i> Mgr</button>
                                    </form>
                                @endif
                            @endif
                        @elseif ($document->status === 'Tertutup')
                            <span style="color:#99f6e4;font-size:18px"><i class="fas fa-envelope"></i></span>
                        @endif
                        <div class="header-tools">
                            <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                            <button type="button" class="btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
                <div class="kpanel-body">
                    <ul class="nav nav-tabs k-tabs" id="logistikDoneFeedbackTabs" role="tablist">
                        @foreach ($document->komatHistReqs as $komatHistReq)
                            @if ($komatHistReq->komatPositions->where('level', 'logistik_done')->isNotEmpty())
                                <li class="nav-item" role="presentation">
                                    <button class="k-tab-btn nav-link {{ $loop->first ? 'active' : '' }}" id="logistik-done-tab-{{ $komatHistReq->id }}" data-bs-toggle="tab" data-bs-target="#logistik-done-content-{{ $komatHistReq->id }}" type="button" role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $komatHistReq->komatRequirement->name }}</button>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <div class="tab-content" id="logistikDoneFeedbackTabContent">
                        @foreach ($document->komatHistReqs as $komatHistReq)
                            @php $logistikDonePosition = $komatHistReq->komatPositions->where('level', 'logistik_done')->first(); $feedbacks = $logistikDonePosition ? $logistikDonePosition->feedbacks : collect([]); $isLastAccepted = $feedbacks->contains('status', 'last_accepted'); @endphp
                            @if ($logistikDonePosition)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="logistik-done-content-{{ $komatHistReq->id }}" role="tabpanel">
                                    <div class="disc-status-header">
                                        <span class="disc-status-label">Logistik: {{ $komatHistReq->komatRequirement->name }}</span>
                                        <i class="fas {{ $isLastAccepted ? 'fa-check-circle' : 'fa-times-circle' }}" style="color:{{ $isLastAccepted ? '#16a34a' : '#dc2626' }};font-size:16px"></i>
                                    </div>
                                    @foreach ($feedbacks as $feedback)
                                        @php $fcClass = $feedback->status === 'last_accepted' ? 'status-last-accepted' : ($feedback->status === 'reviewed' ? 'status-reviewed' : 'status-draft'); @endphp
                                        <div class="feedback-card {{ $fcClass }}">
                                            <div style="display:flex;gap:10px">
                                                <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1,5) }}.png" alt="Avatar" class="fc-avatar">
                                                <div style="flex:1;min-width:0">
                                                    <div class="fc-header">
                                                        <div><p class="fc-name">{{ $feedback->user_name }}</p><p class="fc-role">{{ $feedback->user_rule }}</p></div>
                                                        <span class="kbadge {{ $feedback->status === 'last_accepted' ? 'b-success' : ($feedback->status === 'draft' ? 'b-warning' : 'b-info') }}">{{ ucfirst($feedback->status) }}</span>
                                                    </div>
                                                    <p class="fc-time">{{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                    <p class="fc-comment">{{ $feedback->comment }}</p>
                                                    <div class="fc-status-row"><span class="fc-status-label">Status:</span><span class="fc-status-value">{{ $feedback->feedback_status }}</span></div>
                                                    @if ($feedback->files->isNotEmpty())<ul class="list-unstyled mb-0">@foreach ($feedback->files as $file)<li class="mb-1">@include('komatprocesshistory.show.fileinfo', ['file' => $file, 'userinformation' => $feedback])</li>@endforeach</ul>@endif
                                                    <div class="fc-actions">
                                                        @if ($feedback->status === 'draft' && $document->status === 'Terbuka' && $logistikDonePosition && $logistikDonePosition->status === 'draft' && $yourauth->rule === 'Logistik')
                                                            <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'logistik_done']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-warning"><i class="fas fa-eye"></i> Reviewed</button></form>
                                                        @elseif ($feedback->status === 'reviewed' && $yourauth->rule === 'Logistik' && $document->status === 'Terbuka' && $logistikDonePosition && $logistikDonePosition->status === 'draft')
                                                            <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'logistik_done']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-success"><i class="fas fa-check"></i> Selesai</button></form>
                                                        @endif
                                                        @if ($document->status === 'Terbuka' && isset($document->unit_distributor_id) && isset($yourauth->unit_id) && $document->unit_distributor_id === $yourauth->unit_id)
                                                            <form action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'logistik_done']) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="kbtn kbtn-danger" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i> Delete</button></form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════
         STEP 7 — Validasi Manager Logistik
    ═══════════════════════════════════════════ --}}
    @if ($document->logisticauthoritylevel == 'managerneeded' || $document->logisticauthoritylevel == 'seniormanagerneeded')
        @if ($document->komatHistReqs->contains(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'managerlogistikneeded')->isNotEmpty()))
            @php
                $allManagerLogistikApproved = $document->komatHistReqs->every(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'managerlogistikneeded')->every(fn($position) => $position->status_process === 'done'));
            @endphp
            <div class="pipeline-step">
                <div class="kpanel">
                    <div class="kpanel-header h-orange">
                        <div class="header-left">
                            <div class="header-icon"><i class="fas fa-user-check"></i></div>
                            <div>
                                <p class="header-title">Validasi Manager Logistik</p>
                                <span class="approval-chip {{ $allManagerLogistikApproved ? 'approved' : 'unapproved' }}">
                                    <i class="fas {{ $allManagerLogistikApproved ? 'fa-check' : 'fa-times' }}"></i>
                                    {{ $allManagerLogistikApproved ? 'Approved' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                        <div class="header-send-block">
                            @if ($document->status === 'Terbuka' && $allManagerLogistikApproved)
                                @if ($document->logisticauthoritylevel === 'managerneeded')
                                    <form action="{{ route('komatprocesshistory.close', ['id' => $document->id]) }}" method="POST" style="display:inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="Tertutup">
                                        <input type="hidden" name="rejectedreason" value="">
                                        <input type="hidden" name="documentstatus" value="approved">
                                        <input type="hidden" name="needincreaserevision" value="no">
                                        <button type="submit" class="kbtn kbtn-solid-warning" onclick="return confirm('Tutup dokumen?')"><i class="fas fa-lock"></i> Tutup</button>
                                    </form>
                                @else
                                    @php $issendtoSeniorManagerLogistik = $document->komatHistReqs->every(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'seniormanagerlogistikneeded')->isNotEmpty()); @endphp
                                    @if (!$issendtoSeniorManagerLogistik)
                                        <form action="{{ route('komatprocesshistory.copyTo', ['id' => $document->id, 'level' => 'seniormanagerlogistikneeded']) }}" method="POST" style="display:inline">
                                            @csrf
                                            <input type="hidden" name="sendto" value="Senior Manager Logistik">
                                            <button type="submit" class="kbtn kbtn-solid-danger" onclick="return confirm('Kirim ke Senior Manager Logistik?')"><i class="fas fa-paper-plane"></i> SM Logistik</button>
                                        </form>
                                    @endif
                                @endif
                            @elseif ($document->status === 'Tertutup')
                                <span style="color:#fed7aa;font-size:18px"><i class="fas fa-envelope"></i></span>
                            @endif
                            <div class="header-tools">
                                <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                <button type="button" class="btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="kpanel-body">
                        <ul class="nav nav-tabs k-tabs" id="managerLogistikFeedbackTabs" role="tablist">
                            @foreach ($document->komatHistReqs as $komatHistReq)
                                @if ($komatHistReq->komatPositions->where('level', 'managerlogistikneeded')->isNotEmpty())
                                    <li class="nav-item" role="presentation">
                                        <button class="k-tab-btn nav-link {{ $loop->first ? 'active' : '' }}" id="manager-logistik-tab-{{ $komatHistReq->id }}" data-bs-toggle="tab" data-bs-target="#manager-logistik-content-{{ $komatHistReq->id }}" type="button" role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $komatHistReq->komatRequirement->name }}</button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="tab-content" id="managerLogistikFeedbackTabContent">
                            @foreach ($document->komatHistReqs as $komatHistReq)
                                @php $managerLogistikPosition = $komatHistReq->komatPositions->where('level', 'managerlogistikneeded')->first(); $managerFeedbacks = $managerLogistikPosition ? $managerLogistikPosition->feedbacks : collect([]); $isManagerLastAccepted = $managerFeedbacks->contains('status', 'last_accepted'); @endphp
                                @if ($managerLogistikPosition)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="manager-logistik-content-{{ $komatHistReq->id }}" role="tabpanel">
                                        <div class="disc-status-header">
                                            <span class="disc-status-label">Manager Logistik: {{ $komatHistReq->komatRequirement->name }}</span>
                                            <i class="fas {{ $isManagerLastAccepted ? 'fa-check-circle' : 'fa-times-circle' }}" style="color:{{ $isManagerLastAccepted ? '#16a34a' : '#dc2626' }};font-size:16px"></i>
                                        </div>
                                        @foreach ($managerFeedbacks as $feedback)
                                            @php $fcClass = $feedback->status === 'last_accepted' ? 'status-last-accepted' : ($feedback->status === 'reviewed' ? 'status-reviewed' : 'status-draft'); @endphp
                                            <div class="feedback-card {{ $fcClass }}">
                                                <div style="display:flex;gap:10px">
                                                    <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1,5) }}.png" alt="Avatar" class="fc-avatar">
                                                    <div style="flex:1;min-width:0">
                                                        <div class="fc-header">
                                                            <div><p class="fc-name">{{ $feedback->user_name }}</p><p class="fc-role">{{ $feedback->user_rule }}</p></div>
                                                            <span class="kbadge {{ $feedback->status === 'last_accepted' ? 'b-success' : ($feedback->status === 'draft' ? 'b-warning' : 'b-info') }}">{{ ucfirst($feedback->status) }}</span>
                                                        </div>
                                                        <p class="fc-time">{{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                        <p class="fc-comment">{{ $feedback->comment }}</p>
                                                        <div class="fc-status-row"><span class="fc-status-label">Status:</span><span class="fc-status-value">{{ $feedback->feedback_status }}</span></div>
                                                        @if ($feedback->files->isNotEmpty())<ul class="list-unstyled mb-0">@foreach ($feedback->files as $file)<li class="mb-1">@include('komatprocesshistory.show.fileinfo', ['file' => $file, 'userinformation' => $feedback])</li>@endforeach</ul>@endif
                                                        <div class="fc-actions">
                                                            @if ($feedback->status === 'draft' && $document->status === 'Terbuka' && $managerLogistikPosition && $managerLogistikPosition->status === 'draft' && $yourauth->rule === 'Manager Logistik')
                                                                <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'managerlogistikneeded']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-warning"><i class="fas fa-eye"></i> Reviewed</button></form>
                                                            @elseif ($feedback->status === 'reviewed' && $yourauth->rule === 'Manager Logistik' && $document->status === 'Terbuka' && $managerLogistikPosition && $managerLogistikPosition->status === 'draft')
                                                                <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'managerlogistikneeded']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-success"><i class="fas fa-check"></i> Selesai</button></form>
                                                            @endif
                                                            @if ($document->status === 'Terbuka' && isset($document->unit_distributor_id) && isset($yourauth->unit_id) && $document->unit_distributor_id === $yourauth->unit_id)
                                                                <form action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'logistik_done']) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="kbtn kbtn-danger" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i> Delete</button></form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif


    {{-- ═══════════════════════════════════════════
         STEP 8 — Validasi Senior Manager Logistik
    ═══════════════════════════════════════════ --}}
    @if ($document->logisticauthoritylevel == 'seniormanagerneeded')
        @if ($document->komatHistReqs->contains(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'seniormanagerlogistikneeded')->isNotEmpty()))
            @php $allSeniorLogistikApproved = $document->komatHistReqs->every(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'seniormanagerlogistikneeded')->every(fn($position) => $position->status_process === 'done')); @endphp
            <div class="pipeline-step">
                <div class="kpanel">
                    <div class="kpanel-header h-dark">
                        <div class="header-left">
                            <div class="header-icon"><i class="fas fa-crown"></i></div>
                            <div>
                                <p class="header-title">Validasi Senior Manager Logistik</p>
                                <span class="approval-chip {{ $allSeniorLogistikApproved ? 'approved' : 'unapproved' }}">
                                    <i class="fas {{ $allSeniorLogistikApproved ? 'fa-check' : 'fa-times' }}"></i>
                                    {{ $allSeniorLogistikApproved ? 'Approved' : 'Pending' }}
                                </span>
                            </div>
                        </div>
                        <div class="header-send-block">
                            @if ($document->status === 'Terbuka' && $allSeniorLogistikApproved)
                                <form action="{{ route('komatprocesshistory.close', ['id' => $document->id]) }}" method="POST" style="display:inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="Tertutup">
                                    <input type="hidden" name="documentstatus" value="approved">
                                    <input type="hidden" name="needincreaserevision" value="no">
                                    <button type="submit" class="kbtn kbtn-solid-warning" onclick="return confirm('Tutup dokumen?')"><i class="fas fa-lock"></i> Tutup</button>
                                </form>
                            @elseif ($document->status === 'Tertutup')
                                <span style="color:#94a3b8;font-size:18px"><i class="fas fa-envelope"></i></span>
                            @endif
                            <div class="header-tools">
                                <button type="button" class="btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                <button type="button" class="btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="kpanel-body">
                        <ul class="nav nav-tabs k-tabs" id="seniorLogistikFeedbackTabs" role="tablist">
                            @foreach ($document->komatHistReqs as $komatHistReq)
                                @if ($komatHistReq->komatPositions->where('level', 'seniormanagerlogistikneeded')->isNotEmpty())
                                    <li class="nav-item" role="presentation">
                                        <button class="k-tab-btn nav-link {{ $loop->first ? 'active' : '' }}" id="senior-logistik-tab-{{ $komatHistReq->id }}" data-bs-toggle="tab" data-bs-target="#senior-logistik-content-{{ $komatHistReq->id }}" type="button" role="tab" aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $komatHistReq->komatRequirement->name }}</button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="tab-content" id="seniorLogistikFeedbackTabContent">
                            @foreach ($document->komatHistReqs as $komatHistReq)
                                @php $seniorLogistikPosition = $komatHistReq->komatPositions->where('level', 'seniormanagerlogistikneeded')->first(); $seniorFeedbacks = $seniorLogistikPosition ? $seniorLogistikPosition->feedbacks : collect([]); $isSeniorLastAccepted = $seniorFeedbacks->contains('status', 'last_accepted'); @endphp
                                @if ($seniorLogistikPosition)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="senior-logistik-content-{{ $komatHistReq->id }}" role="tabpanel">
                                        <div class="disc-status-header">
                                            <span class="disc-status-label">SM Logistik: {{ $komatHistReq->komatRequirement->name }}</span>
                                            <i class="fas {{ $isSeniorLastAccepted ? 'fa-check-circle' : 'fa-times-circle' }}" style="color:{{ $isSeniorLastAccepted ? '#16a34a' : '#dc2626' }};font-size:16px"></i>
                                        </div>
                                        @foreach ($seniorFeedbacks as $feedback)
                                            @php $fcClass = $feedback->status === 'last_accepted' ? 'status-last-accepted' : ($feedback->status === 'reviewed' ? 'status-reviewed' : 'status-draft'); @endphp
                                            <div class="feedback-card {{ $fcClass }}">
                                                <div style="display:flex;gap:10px">
                                                    <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1,5) }}.png" alt="Avatar" class="fc-avatar">
                                                    <div style="flex:1;min-width:0">
                                                        <div class="fc-header">
                                                            <div><p class="fc-name">{{ $feedback->user_name }}</p><p class="fc-role">{{ $feedback->user_rule }}</p></div>
                                                            <span class="kbadge {{ $feedback->status === 'last_accepted' ? 'b-success' : ($feedback->status === 'draft' ? 'b-warning' : 'b-info') }}">{{ ucfirst($feedback->status) }}</span>
                                                        </div>
                                                        <p class="fc-time">{{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                        <p class="fc-comment">{{ $feedback->comment }}</p>
                                                        <div class="fc-status-row"><span class="fc-status-label">Status:</span><span class="fc-status-value">{{ $feedback->feedback_status }}</span></div>
                                                        @if ($feedback->files->isNotEmpty())<ul class="list-unstyled mb-0">@foreach ($feedback->files as $file)<li class="mb-1">@include('komatprocesshistory.show.fileinfo', ['file' => $file, 'userinformation' => $feedback])</li>@endforeach</ul>@endif
                                                        <div class="fc-actions">
                                                            @if ($feedback->status === 'draft' && $document->status === 'Terbuka' && $seniorLogistikPosition && $seniorLogistikPosition->status === 'draft' && $yourauth->rule === 'Senior Manager Logistik')
                                                                <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'seniormanagerlogistikneeded']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-warning"><i class="fas fa-eye"></i> Reviewed</button></form>
                                                            @elseif ($feedback->status === 'reviewed' && $yourauth->rule === 'Senior Manager Logistik' && $document->status === 'Terbuka' && $seniorLogistikPosition && $seniorLogistikPosition->status === 'draft')
                                                                <form action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'seniormanagerlogistikneeded']) }}" method="POST" style="display:inline">@csrf @method('PUT')<button type="submit" class="kbtn kbtn-success"><i class="fas fa-check"></i> Selesai</button></form>
                                                            @endif
                                                            @if ($document->status === 'Terbuka' && isset($document->unit_distributor_id) && isset($yourauth->unit_id) && $document->unit_distributor_id === $yourauth->unit_id)
                                                                <form action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'logistik_done']) }}" method="POST" style="display:inline">@csrf @method('DELETE')<button type="submit" class="kbtn kbtn-danger" onclick="return confirm('Hapus?')"><i class="fas fa-trash"></i> Delete</button></form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>{{-- end pipeline-wrapper --}}
@endsection


@section('container3')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('komatprocesshistory.index') }}" class="text-decoration-none">
                                <i class="fas fa-list-ul" style="margin-right:4px"></i>List KomRev
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <a href="{{ route('komatprocesshistory.show', [$document->id]) }}" class="text-decoration-none">
                                KOMREV / {{ $document->komatProcess->komat_name }} / {{ $document->revision }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Show</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('css')
<style>
    /* ── Kill Bootstrap nav-tabs default blue text in kpanel ── */
    .kpanel .nav-tabs { border-bottom: none; }
    .kpanel .nav-tabs .nav-link { color: var(--ink-light); }
    .kpanel .nav-tabs .nav-link:hover { color: var(--navy-800); }
    .kpanel .nav-tabs .nav-link.active { color: #fff; background: var(--navy-700); border-color: var(--navy-700); }
    .kpanel .nav-tabs .nav-link:focus { box-shadow: none; }

    /* ── Discussion items in second card show as links - restyle ── */
    .kpanel-body .tab-content { padding-top: 2px; }

    /* ── Ensure AdminLTE card collapse still works ── */
    .kpanel .card-body { display: block; }
</style>
@endpush

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.reject-sm-btn, .reject-logistik-btn').forEach(button => {
        button.addEventListener('click', function () {
            const formId = this.getAttribute('data-form-id');
            const form = document.getElementById(formId);
            const inputId = formId === 'reject-sm-form' ? 'rejectedreason-sm' : 'rejectedreason-logistik';
            const title = formId === 'reject-sm-form' ? 'Tolak dan Balik Logistik' : 'Balik ke Teknologi';

            Swal.fire({
                title: title,
                text: 'Masukkan alasan penolakan:',
                input: 'textarea',
                inputPlaceholder: 'Ketik alasan penolakan di sini...',
                inputAttributes: { 'aria-label': 'Alasan penolakan' },
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#163058',
                preConfirm: (reason) => {
                    if (!reason) { Swal.showValidationMessage('Alasan penolakan wajib diisi'); }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(inputId).value = result.value;
                    form.submit();
                }
            });
        });
    });
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function () {
        Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Teks berhasil disalin ke clipboard', timer: 1500, showConfirmButton: false });
    }, function () {
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyalin teks', timer: 1500, showConfirmButton: false });
    });
}
</script>
@endpush