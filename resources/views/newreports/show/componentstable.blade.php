{{-- resources/views/newreports/show/componentstable.blade.php --}}

{{-- DM Mono — professional numeric rendering --}}
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
    /*
     * Button Color Convention (Universal UX Standard)
     * ─────────────────────────────────────────────
     * Revisi / Edit  → #e67e22  Orange  — modify action
     * Lihat PDF      → #8e44ad  Purple  — view/PDF (Adobe identity)
     * Delete         → #e74c3c  Red     — destructive
     * Save           → #27ae60  Green   — confirm/save
     * Download       → #2980b9  Blue    — primary fetch
     *
     * Table header   → #c0392b  Red     — brand theme
     * Mono font      → DM Mono           — crisp numerics
     */

    .modern-doc-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12.5px;
        font-family: 'Inter', sans-serif;
    }

    /* ── Header: clean brand red ── */
    .modern-doc-table thead tr {
        background: #c0392b;
    }
    .modern-doc-table thead th {
        padding: 12px 13px;
        font-size: 9.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: rgba(255,255,255,.88);
        border-bottom: none;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 1;
    }
    .modern-doc-table thead th:first-child { border-radius: 10px 0 0 0; padding-left: 16px; }
    .modern-doc-table thead th:last-child  { border-radius: 0 10px 0 0; }
    .modern-doc-table thead {
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
    }

    /* ── Body ── */
    .modern-doc-table tbody tr {
        transition: background .12s;
    }
    .modern-doc-table tbody tr:hover  { background: #fdf7f6; }
    .modern-doc-table tbody tr.checked { background: #fce8e6; }
    .modern-doc-table tbody tr:last-child td { border-bottom: none; }
    .modern-doc-table tbody td {
        padding: 10px 13px;
        color: #1c1c1e;
        vertical-align: middle;
        border: none;
        border-bottom: 1px solid #f5eeee;
    }
    .modern-doc-table tbody td:first-child { padding-left: 16px; }

    /* ── Row number ── */
    .row-num {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: #c9a8a6;
        font-weight: 500;
        letter-spacing: -.02em;
    }

    /* ── Doc number ── */
    .doc-number-wrap { display: flex; align-items: center; gap: 6px; }
    .doc-number-text {
        font-family: 'DM Mono', monospace;
        font-size: 12px;
        font-weight: 600;
        color: #1c1c1e;
        letter-spacing: -.01em;
    }
    .btn-edit-inline {
        width: 22px; height: 22px;
        border-radius: 5px;
        border: 1px solid #fde8cc;
        background: #fef6ee;
        color: #e67e22;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 10px;
        cursor: pointer;
        transition: all .15s;
        flex-shrink: 0;
    }
    .btn-edit-inline:hover { background: #e67e22; color: #fff; border-color: #ca6f1e; }

    .inline-edit-form { display: flex; gap: 5px; align-items: center; flex-wrap: wrap; }
    .inline-edit-form input {
        padding: 4px 8px;
        border: 1px solid #dde2ed;
        border-radius: 5px;
        font-size: 12px;
        font-family: 'DM Mono', monospace;
        width: 140px;
    }
    .inline-edit-form input:focus {
        outline: none;
        border-color: #e67e22;
        box-shadow: 0 0 0 2px rgba(230,126,34,.12);
    }

    /* ── Doc name ── */
    .doc-name { font-weight: 600; color: #1c1c1e; line-height: 1.35; }

    /* ── Jenis dokumen select ── */
    .select-documentkind {
        padding: 5px 8px;
        border: 1px solid #dde2ed;
        border-radius: 6px;
        font-size: 11.5px;
        font-family: 'Inter', sans-serif;
        color: #1c1c1e;
        background: #fff;
        min-width: 160px;
        transition: border-color .15s;
    }
    .select-documentkind:focus {
        outline: none;
        border-color: #2980b9;
        box-shadow: 0 0 0 2px rgba(41,128,185,.1);
    }
    .jenis-text { font-size: 12px; color: #48484a; font-weight: 500; }

    /* ── Mono badges (Rev, Paper, Sheet) ── */
    .mono-badge {
        font-family: 'DM Mono', monospace;
        font-size: 11.5px;
        font-weight: 600;
        color: #1c1c1e;
        background: #f0f2f7;
        padding: 2px 9px;
        border-radius: 4px;
        display: inline-block;
        letter-spacing: -.01em;
        border: 1px solid #e4e7f0;
    }

    /* ── Date ── */
    .date-text {
        font-family: 'DM Mono', monospace;
        font-size: 11.5px;
        color: #48484a;
        font-weight: 500;
        white-space: nowrap;
        letter-spacing: -.01em;
    }

    /* ── Status pills ── */
    .status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 700; white-space: nowrap;
    }
    .status-pill::before {
        content: ''; width: 6px; height: 6px;
        border-radius: 50%; flex-shrink: 0;
    }
    .status-released        { background: #d1fae5; color: #065f46; }
    .status-released::before{ background: #10b981; }
    .status-wip             { background: #fef3c7; color: #92400e; }
    .status-wip::before     { background: #f59e0b; }
    .status-empty           { background: #f0f2f7; color: #6b7280; }
    .status-empty::before   { background: #9ca3af; }

    /* ── Action column ── */
    .action-col { display: flex; flex-direction: column; gap: 5px; min-width: 130px; }

    .btn-act {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px; padding: 6px 11px;
        border-radius: 6px; font-size: 11px; font-weight: 700;
        border: none; cursor: pointer; text-decoration: none;
        transition: filter .15s, transform .1s, box-shadow .15s;
        font-family: 'Inter', sans-serif; width: 100%;
        letter-spacing: .01em;
    }
    .btn-act:hover {
        filter: brightness(.92);
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0,0,0,.15);
        text-decoration: none;
    }
    .btn-act:active { transform: translateY(0); filter: brightness(.85); }

    /* ─ Revisi: orange — modify/edit action ─ */
    .btn-act-revisi {
        background: #808080;
        color: #fff;
    }

    /* ─ Save: green — confirm action ─ */
    .btn-act-save {
        background: #27ae60;
        color: #fff;
    }

    /* ─ PDF: purple — view/document action ─ */
    .btn-act-pdf {
        background: #c0392b;
        color: #fff;
    }

    /* ─ Delete: red — destructive action ─ */
    .btn-act-delete {
        background: #fde8e8;
        color: #c0392b;
        border: 1px solid #f5c6c6;
    }
    .btn-act-delete:hover {
        background: #e74c3c;
        color: #fff;
        border-color: #e74c3c;
    }

    /* ── PDF warning ── */
    .pdf-warning {
        display: flex; align-items: center; gap: 5px;
        padding: 5px 8px;
        background: #fef9e7;
        border-radius: 6px; font-size: 10.5px; font-weight: 600;
        color: #9a7d0a; border: 1px solid #f9e79f;
    }
    .pdf-warning i { font-size: 10px; }

    /* ── Checkbox ── */
    .icheck-primary input[type=checkbox] { display: none; }
    .icheck-primary label {
        width: 16px; height: 16px;
        border: 2px solid rgba(255,255,255,.55);
        border-radius: 4px;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all .15s; margin: 0;
    }
    .modern-doc-table tbody .icheck-primary label {
        border-color: #dde2ed;
    }
    .icheck-primary input:checked + label {
        background: #c0392b;
        border-color: #a93226;
    }
    .icheck-primary input:checked + label::after {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900; color: white; font-size: 8px;
    }

    /* check-icon header */
    .check-icon { cursor: pointer; color: rgba(255,255,255,.6); font-size: 15px; transition: color .15s; }
    .check-icon:hover { color: #fff; }
</style>

<table id="{{ $id }}" class="modern-doc-table">
    <thead>
        <tr>
            <th style="width:36px">
                <span class="check-icon" id="{{ $checklist }}"><i class="far fa-square"></i></span>
            </th>
            <th style="width:40px">No</th>
            <th>{{ $documentNoHeader }}</th>
            <th>{{ $documentNameHeader }}</th>
            <th>Jenis Dokumen</th>
            <th style="width:70px">Rev</th>
            <th style="width:90px">Realisasi</th>
            <th style="width:80px">Paper Size</th>
            <th style="width:50px">Sheet</th>
            <th style="width:110px">Status</th>
            <th style="width:140px">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php $penghitung = 1; @endphp

        @foreach ($progressReports as $index => $progressReport)
        <tr>
            {{-- Checkbox --}}
            <td>
                <div class="icheck-primary">
                    <input type="checkbox" value="{{ $progressReport->id }}" name="{{ $name }}"
                           id="checkbox{{ $progressReport->id }}" onchange="handleCheckboxChange(this)">
                    <label for="checkbox{{ $progressReport->id }}"></label>
                </div>
            </td>

            {{-- No --}}
            <td><span class="row-num">{{ $penghitung }}</span></td>

            {{-- No Dokumen --}}
            @if (auth()->user()->rule == 'MTPR')
                <td>
                    <div class="doc-number-wrap">
                        <span class="doc-number-text" id="documentNumberDisplay{{ $progressReport->id }}">
                            {{ $progressReport->nodokumen ?? 'No Document' }}
                        </span>
                        <button class="btn-edit-inline" onclick="enableEdit({{ $progressReport->id }})" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                    </div>
                    <div class="inline-edit-form" id="editDocumentForm{{ $progressReport->id }}" style="display:none; margin-top:5px">
                        <input type="text" name="nodokumen" id="nodokumen{{ $progressReport->id }}"
                               value="{{ $progressReport->nodokumen ?? '' }}"
                               placeholder="No dokumen">
                        <button type="button" class="btn-act btn-act-save" style="width:auto;padding:4px 10px"
                                id="saveButton{{ $progressReport->id }}"
                                onclick="updateDocumentNumber({{ $progressReport->id }}, {{ $progressReport->newreport_id }}, '{{ $progressReport->nodokumen ?? '' }}')">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <button type="button" class="btn-act btn-act-delete" style="width:auto;padding:4px 10px"
                                onclick="cancelEdit({{ $progressReport->id }})">
                            Cancel
                        </button>
                    </div>
                </td>
            @else
                <td>
                    <span class="doc-number-text">{{ $progressReport->nodokumen }}</span>
                </td>
            @endif

            {{-- Nama Dokumen --}}
            <td id="namadokumen_{{ $progressReport->id }}_{{ $index }}">
                <span class="doc-name">{{ $progressReport->namadokumen ?? '' }}</span>
            </td>

            {{-- Jenis Dokumen --}}
            @if (auth()->user()->rule == 'MTPR')
                <td>
                    <select class="select-documentkind" data-id="{{ $progressReport->id }}" data-index="{{ $index }}">
                        <option value="">-- Pilih --</option>
                        @foreach ($jenisdokumen as $jenis)
                            <option value="{{ $jenis->id }}" {{ $progressReport->documentkind_id == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
            @else
                <td id="documentkind_{{ $progressReport->id }}_{{ $index }}">
                    <span class="jenis-text">{{ $progressReport->documentKind->name ?? '—' }}</span>
                </td>
            @endif

            {{-- Rev --}}
            <td id="rev_{{ $progressReport->id }}_{{ $index }}">
                @if($progressReport->rev)
                    <span class="mono-badge">{{ $progressReport->rev }}</span>
                @else
                    <span style="color:#ccc">—</span>
                @endif
            </td>

            {{-- Realisasi --}}
            <td id="realisasi_{{ $progressReport->id }}_{{ $index }}">
                @if (!empty($progressReport->realisasidate))
                    <span class="date-text">{{ \Carbon\Carbon::parse($progressReport->realisasidate)->format('d/m/Y') }}</span>
                @else
                    <span style="color:#ccc">—</span>
                @endif
            </td>

            {{-- Paper Size --}}
            <td id="papersize_{{ $progressReport->id }}_{{ $index }}">
                @if($progressReport->papersize)
                    <span class="mono-badge">{{ $progressReport->papersize }}</span>
                @endif
            </td>

            {{-- Sheet --}}
            <td id="sheet_{{ $progressReport->id }}_{{ $index }}">
                <span class="mono-badge">{{ $progressReport->sheet }}</span>
            </td>

            {{-- Status --}}
            <td id="status_{{ $progressReport->id }}_{{ $index }}">
                @php $st = $progressReport->status; @endphp
                @if($st === 'RELEASED')
                    <span class="status-pill status-released"><i class="fas fa-check"></i> Released</span>
                @elseif($st === 'Working Progress')
                    <span class="status-pill status-wip"><i class="fas fa-spinner"></i> WIP</span>
                @elseif($st)
                    <span class="status-pill status-empty">{{ $st }}</span>
                @else
                    <span style="color:#ccc; font-size:12px">—</span>
                @endif
            </td>

            {{-- Aksi --}}
            @php $hasilwaktu = json_decode($progressReport->temporystatus, true); @endphp
            <td>
                @if ($newreport_id)
                    <div class="action-col">
                        {{-- Revisi: orange --}}
                        <a href="{{ route('newreports.showrev', ['newreport' => $newreport_id, 'id' => $progressReport->id]) }}"
                           class="btn-act btn-act-revisi">
                            <i class="fas fa-history"></i> Revisi
                        </a>

                        @if ($progressReport->latestHistory && $progressReport->latestHistory->fileid)
                            @if (config('app.url') != 'https://inka.goovicess.com')
                                {{-- PDF: purple --}}
                                <a href="http://10.10.0.40/AutodeskTC/10.10.0.40/TekVault_0003_Dec2011/Document/Download?fileId={{ $progressReport->latestHistory->fileid }}&downloadAsInline=true"
                                   class="btn-act btn-act-pdf" target="_blank" rel="noopener noreferrer">
                                    <i class="fas fa-file-pdf"></i> Lihat PDF
                                </a>
                            @else
                                <div class="pdf-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <code style="font-size:10px">Downloadfile_{{ $progressReport->latestHistory->fileid }}</code>
                                </div>
                            @endif
                        @else
                            <div class="pdf-warning">
                                <i class="fas fa-exclamation-triangle"></i> PDF tidak tersedia
                            </div>
                        @endif

                        @if (auth()->user()->rule == 'MTPR' && $progressReport->status != 'RELEASED')
                            {{-- Delete: red --}}
                            <a href="#" class="btn-act btn-act-delete"
                               onclick="opendeleteForm('{{ $progressReport->id }}', '{{ $index }}')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        @endif
                    </div>
                @endif
            </td>
        </tr>
        @php $penghitung++; @endphp
        @endforeach
    </tbody>
</table>