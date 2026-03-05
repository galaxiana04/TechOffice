{{-- ============================================================
     MODERN DOCUMENT INFO CARD - Clean Light UI
     ============================================================ --}}

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    :root {
        --bg: #f5f6fa;
        --surface: #ffffff;
        --panel: #f9fafb;
        --border: #e8ecf1;
        --border-hover: #c9d3e0;
        --accent: #2563eb;
        --accent-soft: #eff4ff;
        --accent-2: #0891b2;
        --accent-2-soft: #ecfeff;
        --success: #059669;
        --success-soft: #ecfdf5;
        --warning: #d97706;
        --warning-soft: #fffbeb;
        --danger: #dc2626;
        --danger-soft: #fef2f2;
        --text: #111827;
        --subtext: #6b7280;
        --muted: #9ca3af;
        --radius: 18px;
        --radius-sm: 12px;
        --radius-xs: 8px;
        --shadow: 0 1px 3px rgba(0,0,0,0.07), 0 8px 32px rgba(0,0,0,0.06);
        --shadow-sm: 0 1px 2px rgba(0,0,0,0.05), 0 2px 8px rgba(0,0,0,0.04);
        --transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        --font: 'Plus Jakarta Sans', sans-serif;
    }

    .doc-card-root {
        font-family: var(--font);
        color: var(--text);
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .doc-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 26px;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        position: relative;
    }

    .doc-header::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent), var(--accent-2));
        border-radius: var(--radius) var(--radius) 0 0;
    }

    .doc-header-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-icon-wrap {
        width: 34px; height: 34px;
        border-radius: var(--radius-xs);
        background: var(--accent-soft);
        border: 1px solid #dbeafe;
        display: grid;
        place-items: center;
        color: var(--accent);
        font-size: 14px;
    }

    .doc-header-tools { display: flex; gap: 6px; }

    .tool-btn {
        width: 30px; height: 30px;
        border: 1px solid var(--border);
        background: var(--panel);
        border-radius: var(--radius-xs);
        color: var(--muted);
        cursor: pointer;
        display: grid;
        place-items: center;
        font-size: 11px;
        transition: var(--transition);
    }
    .tool-btn:hover { background: var(--border); color: var(--subtext); }

    .doc-body { padding: 26px; background: var(--bg); }

    .info-grid-modern {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .info-chip {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        animation: fadeUp 0.35s ease both;
    }
    .info-chip:hover {
        border-color: var(--border-hover);
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .chip-icon {
        width: 36px; height: 36px;
        border-radius: var(--radius-xs);
        background: var(--accent-soft);
        border: 1px solid #dbeafe;
        display: grid;
        place-items: center;
        color: var(--accent);
        font-size: 13px;
        flex-shrink: 0;
    }

    .chip-label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 4px;
    }

    .chip-value {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text);
        line-height: 1.4;
    }

    .status-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 18px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        animation: fadeUp 0.35s 0.1s ease both;
    }

    .status-row-label {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: var(--subtext);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-pill.open { background: var(--success-soft); color: var(--success); border: 1px solid #a7f3d0; }
    .status-pill.closed { background: var(--danger-soft); color: var(--danger); border: 1px solid #fecaca; }

    .status-pill .dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    .detail-block {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
        animation: fadeUp 0.35s 0.15s ease both;
    }

    .detail-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        transition: var(--transition);
    }
    .detail-row:last-child { border-bottom: none; }
    .detail-row:hover { background: var(--panel); }

    .detail-row-icon {
        width: 32px; height: 32px;
        border-radius: var(--radius-xs);
        background: var(--panel);
        border: 1px solid var(--border);
        display: grid;
        place-items: center;
        font-size: 12px;
        color: var(--accent);
        flex-shrink: 0;
        margin-top: 1px;
    }

    .detail-key {
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: var(--muted);
        display: block;
        margin-bottom: 3px;
    }

    .detail-val { font-size: 13.5px; color: var(--text); font-weight: 500; }
    .detail-val.muted { color: var(--muted); font-style: italic; font-weight: 400; }

    .count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px; height: 22px;
        padding: 0 8px;
        border-radius: 999px;
        background: var(--accent-soft);
        border: 1px solid #bfdbfe;
        color: var(--accent);
        font-size: 11px;
        font-weight: 700;
    }

    .section-heading {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--subtext);
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-heading::after { content: ''; flex: 1; height: 1px; background: var(--border); }

    .komat-wrap { margin-bottom: 20px; animation: fadeUp 0.35s 0.2s ease both; }

    .komat-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
        font-size: 13px;
        box-shadow: var(--shadow-sm);
    }

    .komat-table thead tr { background: var(--panel); }

    .komat-table th {
        padding: 11px 16px;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--muted);
        border-bottom: 1px solid var(--border);
    }

    .komat-table td {
        padding: 12px 16px;
        color: var(--text);
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .komat-table tbody tr:last-child td { border-bottom: none; }
    .komat-table tbody tr { transition: var(--transition); }
    .komat-table tbody tr:hover { background: var(--panel); }

    .code-tag {
        display: inline-block;
        padding: 3px 9px;
        background: var(--accent-2-soft);
        border: 1px solid #a5f3fc;
        color: var(--accent-2);
        border-radius: 5px;
        font-family: monospace;
        font-size: 12px;
    }

    .timeline-wrap { margin-bottom: 20px; animation: fadeUp 0.35s 0.25s ease both; }

    .timeline-items {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .tl-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        transition: var(--transition);
    }
    .tl-item:last-child { border-bottom: none; }
    .tl-item:hover { background: var(--panel); }

    .tl-icon {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        font-size: 13px;
        flex-shrink: 0;
    }
    .tl-icon.active { background: var(--success-soft); color: var(--success); border: 1px solid #a7f3d0; }
    .tl-icon.inactive { background: var(--panel); color: var(--muted); border: 1px solid var(--border); }

    .tl-info-label { font-size: 10.5px; font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 2px; }
    .tl-info-val { font-size: 13px; font-weight: 600; color: var(--text); }
    .tl-info-val.muted { color: var(--muted); font-style: italic; font-weight: 400; }

    .pic-wrap { margin-bottom: 20px; animation: fadeUp 0.35s 0.3s ease both; }
    .pic-badges { display: flex; flex-wrap: wrap; gap: 8px; }

    .pic-badge-link {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 14px;
        border-radius: 999px;
        background: var(--accent-soft);
        border: 1px solid #bfdbfe;
        color: var(--accent);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: var(--transition);
    }
    .pic-badge-link:hover {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37,99,235,0.25);
        text-decoration: none;
    }

    .files-wrap { margin-bottom: 20px; animation: fadeUp 0.35s 0.32s ease both; }

    .file-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 14px 16px;
        margin-bottom: 10px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
    }
    .file-card:hover { border-color: var(--border-hover); box-shadow: 0 4px 14px rgba(0,0,0,0.08); }

    .action-buttons-modern {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
        animation: fadeUp 0.35s 0.35s ease both;
    }

    .btn-mod {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: var(--radius-sm);
        font-family: var(--font);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
        transition: var(--transition);
    }

    .btn-mod-primary { background: var(--accent); color: #fff; box-shadow: 0 2px 8px rgba(37,99,235,0.3); }
    .btn-mod-primary:hover { background: #1d4ed8; box-shadow: 0 4px 16px rgba(37,99,235,0.4); transform: translateY(-1px); color: #fff; text-decoration: none; }

    .btn-mod-warning { background: var(--warning-soft); color: var(--warning); border-color: #fde68a; }
    .btn-mod-warning:hover { background: #fef3c7; border-color: var(--warning); transform: translateY(-1px); text-decoration: none; color: var(--warning); }

    .btn-mod-success { background: var(--success-soft); color: var(--success); border-color: #a7f3d0; }
    .btn-mod-success:hover { background: #d1fae5; border-color: var(--success); transform: translateY(-1px); text-decoration: none; color: var(--success); }

    @media (max-width: 600px) {
        .info-grid-modern { grid-template-columns: 1fr; }
        .doc-body { padding: 16px; }
    }
</style>

<div class="doc-card-root">

    <div class="doc-header">
        <div class="doc-header-title">
            <div class="header-icon-wrap"><i class="fas fa-file-alt"></i></div>
            Informasi Dokumen
        </div>
        <div class="doc-header-tools">
            <button class="tool-btn" data-card-widget="collapse" title="Collapse"><i class="fas fa-minus"></i></button>
            <button class="tool-btn" data-card-widget="remove" title="Remove"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <div class="doc-body">

        {{-- Info Grid --}}
        <div class="info-grid-modern">
            <div class="info-chip" style="animation-delay:0.04s">
                <div class="chip-icon"><i class="fas fa-hashtag"></i></div>
                <div><span class="chip-label">Nomor Dokumen</span><span class="chip-value">{{ $document->documentnumber }}</span></div>
            </div>
            <div class="info-chip" style="animation-delay:0.08s">
                <div class="chip-icon"><i class="fas fa-file-signature"></i></div>
                <div><span class="chip-label">Nama Dokumen</span><span class="chip-value">{{ $document->documentname }}</span></div>
            </div>
            <div class="info-chip" style="animation-delay:0.12s">
                <div class="chip-icon"><i class="fas fa-tags"></i></div>
                <div><span class="chip-label">Kategori</span><span class="chip-value">{{ $document->category }}</span></div>
            </div>
            <div class="info-chip" style="animation-delay:0.16s">
                <div class="chip-icon"><i class="fas fa-project-diagram"></i></div>
                <div><span class="chip-label">Tipe Proyek</span><span class="chip-value">{{ $projectname }}</span></div>
            </div>
        </div>

        {{-- Status --}}
        <div class="status-row">
            <span class="status-row-label"><i class="fas fa-shield-halved"></i> Status Dokumen</span>
            <span class="status-pill" id="statusPill"><span class="dot"></span><span id="statusText">–</span></span>
        </div>
        <script>
            (function () {
                const pill = document.getElementById('statusPill');
                const txt  = document.getElementById('statusText');
                const st   = '{{ $document->documentstatus }}';
                if (st.toLowerCase() === 'terbuka') { pill.classList.add('open'); txt.textContent = 'Terbuka'; }
                else { pill.classList.add('closed'); txt.textContent = 'Tertutup'; }
            })();
        </script>

        {{-- Detail Rows --}}
        @php
            $dasarinformasi = $document->feedbacks;
            $jumlahLampiran = null;
            foreach ($dasarinformasi as $userinformation) {
                if ($userinformation != '') {
                    if ($userinformation->pic == 'MTPR' && $userinformation->level == 'pembukadokumen') {
                        if ($document->operatorsignature != 'Aktif') {
                            $files = $userinformation->files;
                            $jumlahLampiran = count($files);
                        }
                    } else {
                        $files = $userinformation->files;
                        if ($files) { $jumlahLampiran = count($files); }
                    }
                }
            }
        @endphp

        <div class="detail-block">
            <div class="detail-row">
                <div class="detail-row-icon"><i class="fas fa-paper-plane"></i></div>
                <div>
                    <span class="detail-key">Asal Memo</span>
                    @if ($document->memoorigin)
                        <span class="detail-val">{{ $document->memoorigin }}</span>
                    @else
                        <span class="detail-val muted">MTPR belum menentukan asal memo</span>
                    @endif
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-row-icon"><i class="fas fa-user-tie"></i></div>
                <div>
                    <span class="detail-key">Distributor Dokumen</span>
                    @if ($document->operator)
                        <span class="detail-val">{{ $document->operator }}</span>
                    @else
                        <span class="detail-val muted">Belum menentukan distributor dokumen</span>
                    @endif
                </div>
            </div>
            @if (isset($jumlahLampiran))
                <div class="detail-row">
                    <div class="detail-row-icon"><i class="fas fa-paperclip"></i></div>
                    <div>
                        <span class="detail-key">Jumlah Lampiran</span>
                        <span class="detail-val"><span class="count-badge">{{ $jumlahLampiran }}</span></span>
                    </div>
                </div>
            @endif
            <div class="detail-row">
                <div class="detail-row-icon"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <span class="detail-key">Tanggal Terbit Memo</span>
                    @if (isset(json_decode($document->timeline)->documentopened))
                        @php $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s'); @endphp
                        <span class="detail-val">{{ $formattedTime }}</span>
                    @else
                        <span class="detail-val muted">Belum Terbit</span>
                    @endif
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-row-icon"><i class="fas fa-bookmark"></i></div>
                <div>
                    <span class="detail-key">Kategori Memo</span>
                    @if ($document->memokind)
                        <span class="detail-val">{{ $document->memokind }}</span>
                    @else
                        <span class="detail-val muted">{{ $document->operator }} belum menentukan kategori memo</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Komat Table --}}
        @php $komats = $document->komats; @endphp
        @if (isset($komats))
            <div class="komat-wrap">
                <h5 class="section-heading"><i class="fas fa-boxes"></i> Informasi Komat</h5>
                <div class="table-responsive">
                    <table class="komat-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-cube me-1"></i> Komponen</th>
                                <th><i class="fas fa-barcode me-1"></i> Kode Material</th>
                                <th><i class="fas fa-truck me-1"></i> Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($komats as $komat)
                                <tr>
                                    <td>{{ $komat->material }}</td>
                                    <td><span class="code-tag">{{ $komat->kodematerial }}</span></td>
                                    <td>{{ $komat->supplier }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Timeline --}}
        @php $timeline = json_decode($document->timeline, true); @endphp
        <div class="timeline-wrap">
            <h5 class="section-heading"><i class="fas fa-timeline"></i> Timeline Dokumen</h5>
            <div class="timeline-items">
                <div class="tl-item">
                    @if (isset($timeline['documentshared']))
                        @php $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s'); @endphp
                        <div class="tl-icon active"><i class="fas fa-share-alt"></i></div>
                        <div><span class="tl-info-label">Disebarkan</span><span class="tl-info-val">{{ $formattedTime }}</span></div>
                    @else
                        <div class="tl-icon inactive"><i class="fas fa-share-alt"></i></div>
                        <div><span class="tl-info-label">Disebarkan</span><span class="tl-info-val muted">Belum disebarkan</span></div>
                    @endif
                </div>
                <div class="tl-item">
                    @if (isset($timeline['documentclosed']))
                        @php $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s'); @endphp
                        <div class="tl-icon active"><i class="fas fa-check-circle"></i></div>
                        <div><span class="tl-info-label">Ditutup</span><span class="tl-info-val">{{ $formattedTime }}</span></div>
                    @else
                        <div class="tl-icon inactive"><i class="fas fa-check-circle"></i></div>
                        <div><span class="tl-info-label">Ditutup</span><span class="tl-info-val muted">Belum ditutup</span></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- PIC Project --}}
        @if ($yourrule === $document->operator)
            <div class="pic-wrap">
                <h5 class="section-heading"><i class="fas fa-users"></i> PIC Proyek</h5>
                <div class="pic-badges">
                    @if (isset($document->project_pic))
                        @foreach (json_decode($document->project_pic) as $pic)
                            <a href="{{ url('/mail') }}?namafile={{ urlencode($document->documentname) }}&namaproject={{ $document->project_type }}&iddocument={{ $document->id }}&namadivisi={{ $pic }}&notificationcategory={{ $document->category }}" class="pic-badge-link">
                                <i class="fas fa-user" style="font-size:11px"></i> {{ $pic }}
                            </a>
                        @endforeach
                    @else
                        <span style="color:var(--muted);font-size:13px;font-style:italic">Tidak ada PIC proyek tersedia</span>
                    @endif
                </div>
            </div>
        @endif

        {{-- Files (MTPR / pembukadokumen) --}}
        @foreach ($dasarinformasi as $userinformation)
            @if ($userinformation != '' && $userinformation->pic == 'MTPR' && $userinformation->level == 'pembukadokumen')
                @php $files = $userinformation->files; @endphp
                @if ($files && $document->operatorsignature != 'Aktif')
                    <div class="files-wrap">
                        <h5 class="section-heading"><i class="fas fa-file-contract"></i> File dengan Kolom TTD</h5>
                        @foreach ($files as $file)
                            <div class="file-card">@include('newmemo.memo.fileinfo', ['file' => $file, 'userinformation' => $userinformation])</div>
                        @endforeach
                    </div>
                @endif
            @endif
        @endforeach

        @foreach ($dasarinformasi as $userinformation)
            @if ($userinformation != '' && $userinformation->pic == $document->operator && $userinformation->level == 'signature')
                @php $files = $userinformation->files; @endphp
                @if ($files)
                    <div class="files-wrap">
                        <h5 class="section-heading"><i class="fas fa-file-contract"></i> File dengan Kolom TTD</h5>
                        @foreach ($files as $file)
                            <div class="file-card">@include('newmemo.memo.fileinfo', ['file' => $file, 'userinformation' => $userinformation])</div>
                        @endforeach
                    </div>
                @endif
            @endif
        @endforeach

        {{-- Action Buttons --}}
        @if (($yourrule === $document->operator || $yourrule === 'MTPR') && $document->documentstatus === 'Terbuka')
            <div class="action-buttons-modern">
                @if ($yourrule === $document->operator && $document->operatorsignature == 'Aktif')
                    <a href="{{ route('new-memo.edit', $document->id) }}" class="btn-mod btn-mod-warning">
                        <i class="fas fa-pen"></i> Edit Dokumen
                    </a>
                @else
                    @if ($document->MTPRsend == 'Aktif' && $document->operator == null)
                        <a href="{{ route('new-memo.chooseoperator', $document->id) }}" class="btn-mod btn-mod-primary">
                            <i class="fas fa-user-check"></i> Pilih Operator
                        </a>
                    @else
                        @if ($yourrule === $document->operator)
                            <a href="{{ route('new-memo.uploadsignature', $document->id) }}" class="btn-mod btn-mod-success">
                                <i class="fas fa-signature"></i> Upload Signature
                            </a>
                        @endif
                    @endif
                @endif
            </div>
        @endif

    </div>
</div>