@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 k-breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('komatprocesshistory.index') }}" class="text-decoration-none">
                                    <i class="fas fa-list-ul me-1"></i>List KomRev
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                <a href="{{ route('komatprocesshistory.show', [$document->id]) }}" class="text-decoration-none">
                                    KOMREV/{{ $document->komatProcess->komat_name }}/{{ $document->revision }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit KomRev</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap');

    :root {
        --navy-900: #0a1628;
        --navy-800: #0f2040;
        --navy-700: #163058;
        --navy-600: #1e4080;
        --navy-500: #2756a8;
        --navy-400: #3a6fc4;
        --navy-300: #6090d8;
        --navy-200: #a8c0e8;
        --navy-100: #dce8f8;
        --navy-50:  #f0f5fc;
        --ink:      #0d1b2e;
        --ink-light:#2c3e5a;
        --border:   #c8d3e8;
        --white:    #ffffff;
        --radius:   14px;
        --radius-sm: 9px;
        --shadow:   0 2px 12px rgba(14,32,64,.09), 0 8px 28px rgba(14,32,64,.07);
        --transition: all .2s cubic-bezier(.4,0,.2,1);
    }

    body { font-family: 'DM Sans', sans-serif; background: #dce6f4; }

    /* ── Page wrapper ── */
    .edit-wrapper {
        max-width: 780px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }

    /* ── Top identity strip ── */
    .doc-identity-strip {
        display: flex;
        align-items: center;
        gap: 14px;
        background: linear-gradient(135deg, var(--navy-900) 0%, var(--navy-700) 100%);
        border-radius: var(--radius);
        padding: 18px 22px;
        margin-bottom: 20px;
        box-shadow: var(--shadow);
    }
    .doc-identity-strip .strip-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        font-size: 20px;
        flex-shrink: 0;
    }
    .doc-identity-strip .strip-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--navy-300);
        margin-bottom: 2px;
    }
    .doc-identity-strip .strip-value {
        font-size: 17px;
        font-weight: 700;
        color: #fff;
        font-family: 'Space Mono', monospace;
        letter-spacing: .03em;
    }
    .doc-identity-strip .strip-badge {
        margin-left: auto;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 999px;
        text-transform: uppercase;
        letter-spacing: .06em;
        flex-shrink: 0;
    }

    /* ── Main card ── */
    .edit-card {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    /* ── Section: komat requirement block ── */
    .req-block {
        border: 1.5px solid var(--navy-100);
        border-radius: var(--radius-sm);
        margin-bottom: 16px;
        overflow: hidden;
        transition: var(--transition);
    }
    .req-block:hover {
        border-color: var(--navy-300);
        box-shadow: 0 4px 16px rgba(14,32,64,.09);
    }
    .req-block:last-child { margin-bottom: 0; }

    .req-header {
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, var(--navy-800) 0%, var(--navy-600) 100%);
        padding: 12px 16px;
    }
    .req-header .req-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        background: rgba(255,255,255,.18);
        border: 1px solid rgba(255,255,255,.25);
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        font-size: 14px;
        flex-shrink: 0;
    }
    .req-header .req-title {
        font-size: 13px;
        font-weight: 700;
        color: #fff;
        margin: 0;
        flex: 1;
    }
    .req-header .req-count {
        font-size: 10px;
        font-weight: 700;
        color: rgba(255,255,255,.7);
        background: rgba(255,255,255,.15);
        padding: 2px 8px;
        border-radius: 999px;
        font-family: 'Space Mono', monospace;
    }

    .req-body {
        padding: 16px;
        background: var(--navy-50);
    }

    /* ── Unit toggle switch grid ── */
    .unit-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 8px;
    }

    .unit-toggle {
        position: relative;
    }
    .unit-toggle input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0; height: 0;
    }
    .unit-toggle label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: var(--radius-sm);
        border: 1.5px solid var(--border);
        background: var(--white);
        cursor: pointer;
        transition: var(--transition);
        font-size: 12px;
        font-weight: 500;
        color: var(--ink-light);
        user-select: none;
    }
    .unit-toggle label:hover {
        border-color: var(--navy-400);
        background: var(--navy-100);
        color: var(--navy-700);
    }
    /* Custom checkbox visual */
    .unit-toggle label::before {
        content: '';
        width: 18px; height: 18px;
        border-radius: 5px;
        border: 2px solid var(--border);
        background: var(--white);
        flex-shrink: 0;
        transition: var(--transition);
        display: flex; align-items: center; justify-content: center;
    }
    /* Checked state */
    .unit-toggle input:checked + label {
        border-color: var(--navy-600);
        background: var(--navy-100);
        color: var(--navy-800);
        font-weight: 600;
    }
    .unit-toggle input:checked + label::before {
        background: var(--navy-600);
        border-color: var(--navy-600);
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3E%3Cpath d='M2 6l3 3 5-5' stroke='white' stroke-width='2' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-size: 11px;
        background-repeat: no-repeat;
        background-position: center;
    }

    /* ── Card body padding ── */
    .edit-card-body {
        padding: 24px;
    }

    /* ── Section divider label ── */
    .section-divider {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: var(--navy-400);
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--navy-100);
    }

    /* ── Footer ── */
    .edit-card-footer {
        padding: 16px 24px;
        background: var(--navy-50);
        border-top: 1.5px solid var(--navy-100);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .edit-card-footer .footer-hint {
        font-size: 12px;
        color: var(--navy-400);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* ── Buttons ── */
    .kbtn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: var(--transition);
        letter-spacing: .01em;
        font-family: 'DM Sans', sans-serif;
    }
    .kbtn:hover { transform: translateY(-1px); text-decoration: none; }
    .kbtn-save {
        background: linear-gradient(135deg, var(--navy-700) 0%, var(--navy-500) 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(30,64,128,.35);
    }
    .kbtn-save:hover {
        background: linear-gradient(135deg, var(--navy-800) 0%, var(--navy-600) 100%);
        color: #fff;
        box-shadow: 0 6px 20px rgba(30,64,128,.45);
    }
    .kbtn-back {
        background: var(--white);
        color: var(--ink-light);
        border: 1.5px solid var(--border);
    }
    .kbtn-back:hover {
        background: var(--navy-50);
        border-color: var(--navy-300);
        color: var(--navy-700);
    }

    /* ── Breadcrumb ── */
    .k-breadcrumb {
        background: var(--white);
        padding: 8px 16px;
        border-radius: 10px;
        box-shadow: 0 1px 6px rgba(14,32,64,.08);
        font-size: 13px;
    }
    .k-breadcrumb a { color: var(--navy-600); }
    .k-breadcrumb a:hover { color: var(--navy-800); }
    .k-breadcrumb .breadcrumb-item.active { color: var(--navy-400); }
    .k-breadcrumb .breadcrumb-item + .breadcrumb-item::before { color: var(--navy-300); }
</style>

<div class="container-fluid">
    <div class="edit-wrapper">

        {{-- ── Identity Strip ── --}}
        <div class="doc-identity-strip">
            <div class="strip-icon"><i class="fas fa-file-signature"></i></div>
            <div>
                <div class="strip-label">Edit Dokumen</div>
                <div class="strip-value">KOMREV / {{ $document->komatProcess->komat_name }} / {{ $document->revision }}</div>
            </div>
            <span class="strip-badge"><i class="fas fa-pen me-1"></i> Edit Mode</span>
        </div>

        {{-- ── Main Edit Card ── --}}
        <div class="edit-card">
            <form action="{{ route('komatprocesshistory.updatePositions', $document->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="edit-card-body">
                    <div class="section-divider"><i class="fas fa-layer-group"></i> Komat Requirements</div>

                    @foreach ($document->komatHistReqs as $komatHistReq)
                        @php
                            $checkedCount = $komatHistReq->komatPositions->where('level', 'discussion')->count();
                        @endphp
                        <div class="req-block">
                            <div class="req-header">
                                <div class="req-icon"><i class="fas fa-file-alt"></i></div>
                                <p class="req-title">{{ $komatHistReq->komatRequirement->name }}</p>
                                <span class="req-count">{{ $checkedCount }} / {{ $units->count() }} unit</span>
                            </div>
                            <div class="req-body">
                                <div class="unit-grid">
                                    @foreach ($units as $unit)
                                        @php
                                            $isChecked = $komatHistReq->komatPositions
                                                ->where('unit_id', $unit->id)
                                                ->where('level', 'discussion')
                                                ->isNotEmpty();
                                        @endphp
                                        <div class="unit-toggle">
                                            <input type="checkbox"
                                                id="unit_{{ $komatHistReq->id }}_{{ $unit->id }}"
                                                name="positions[{{ $komatHistReq->id }}][]"
                                                value="{{ $unit->id }}"
                                                {{ $isChecked ? 'checked' : '' }}>
                                            <label for="unit_{{ $komatHistReq->id }}_{{ $unit->id }}">
                                                {{ $unit->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="edit-card-footer">
                    <span class="footer-hint">
                        <i class="fas fa-info-circle"></i>
                        Centang unit yang terlibat dalam diskusi
                    </span>
                    <div style="display:flex;gap:10px">
                        <a href="{{ route('komatprocesshistory.show', $document->id) }}" class="kbtn kbtn-back">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="kbtn kbtn-save">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection