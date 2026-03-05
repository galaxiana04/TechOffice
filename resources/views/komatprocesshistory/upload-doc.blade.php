@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 k-breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('komatprocesshistory.showuploaddoc') }}" class="text-decoration-none">
                                    <i class="fas fa-upload me-1"></i>Upload Dokumen Komat
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Upload Dokumen</li>
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
            --navy-50: #f0f5fc;
            --ink: #0d1b2e;
            --ink-light: #2c3e5a;
            --border: #c8d3e8;
            --white: #ffffff;
            --radius: 14px;
            --radius-sm: 9px;
            --shadow: 0 2px 12px rgba(14, 32, 64, .09), 0 8px 28px rgba(14, 32, 64, .07);
            --transition: all .2s cubic-bezier(.4, 0, .2, 1);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #dce6f4;
        }

        /* ── Layout ── */
        .upload-wrapper {
            max-width: 820px;
            margin: 0 auto;
            padding: 8px 0 48px;
        }

        /* ── Breadcrumb ── */
        .k-breadcrumb {
            background: var(--white);
            padding: 8px 16px;
            border-radius: 10px;
            box-shadow: 0 1px 6px rgba(14, 32, 64, .08);
            font-size: 13px;
        }

        .k-breadcrumb a {
            color: var(--navy-600);
        }

        .k-breadcrumb a:hover {
            color: var(--navy-800);
        }

        .k-breadcrumb .breadcrumb-item.active {
            color: var(--navy-400);
        }

        .k-breadcrumb .breadcrumb-item+.breadcrumb-item::before {
            color: var(--navy-300);
        }

        /* ── Page header strip ── */
        .page-strip {
            display: flex;
            align-items: center;
            gap: 16px;
            background: linear-gradient(135deg, var(--navy-900) 0%, var(--navy-600) 100%);
            border-radius: var(--radius);
            padding: 20px 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
        }

        .page-strip .ps-icon {
            width: 50px;
            height: 50px;
            border-radius: 13px;
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 22px;
            flex-shrink: 0;
        }

        .page-strip .ps-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--navy-300);
            margin-bottom: 2px;
        }

        .page-strip .ps-title {
            font-size: 19px;
            font-weight: 700;
            color: #fff;
        }

        /* ── Error alert ── */
        .k-alert-danger {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            border-left: 4px solid #dc2626;
            border-radius: var(--radius-sm);
            padding: 14px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #7f1d1d;
        }

        .k-alert-danger ul {
            margin: 0;
            padding-left: 18px;
        }

        .k-alert-close {
            float: right;
            background: none;
            border: none;
            cursor: pointer;
            color: #dc2626;
            font-size: 16px;
            padding: 0;
            line-height: 1;
        }

        /* ── Main form card ── */
        .form-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .form-card-body {
            padding: 28px;
        }

        .form-card-footer {
            padding: 18px 28px;
            background: var(--navy-50);
            border-top: 1.5px solid var(--navy-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .form-card-footer .footer-hint {
            font-size: 12px;
            color: var(--navy-400);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── Section label ── */
        .field-section {
            margin-bottom: 22px;
        }

        .field-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--navy-600);
            margin-bottom: 7px;
        }

        .field-hint {
            font-size: 11px;
            color: var(--navy-400);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── Inputs ── */
        .k-input,
        .k-select,
        .k-textarea {
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 13px;
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            color: var(--ink);
            background: var(--white);
            transition: var(--transition);
            outline: none;
        }

        .k-input:focus,
        .k-select:focus,
        .k-textarea:focus {
            border-color: var(--navy-500);
            box-shadow: 0 0 0 3px rgba(30, 64, 128, .12);
        }

        .k-textarea {
            resize: vertical;
            min-height: 90px;
        }

        /* ── Select2 overrides ── */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1.5px solid var(--border) !important;
            border-radius: var(--radius-sm) !important;
            background: var(--white) !important;
            min-height: 42px !important;
            padding: 4px 8px !important;
            transition: var(--transition) !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--multiple {
            border-color: var(--navy-500) !important;
            box-shadow: 0 0 0 3px rgba(30, 64, 128, .12) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
            color: var(--ink) !important;
            font-size: 13px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 4px !important;
            padding: 2px 4px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: var(--navy-100) !important;
            border: 1px solid var(--navy-200) !important;
            border-radius: 6px !important;
            color: var(--navy-700) !important;
            font-size: 12px !important;
            padding: 2px 8px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: var(--navy-500) !important;
            margin-right: 4px !important;
        }

        .select2-dropdown {
            border: 1.5px solid var(--border) !important;
            border-radius: var(--radius-sm) !important;
            box-shadow: 0 8px 24px rgba(14, 32, 64, .13) !important;
        }

        .select2-results__option {
            font-size: 13px !important;
            padding: 9px 12px !important;
            transition: background .15s !important;
        }

        .select2-results__option--highlighted {
            background: var(--navy-700) !important;
            color: #fff !important;
        }

        .select2-results__option[aria-selected="true"] {
            background: var(--navy-50) !important;
            color: var(--navy-700) !important;
            font-weight: 600 !important;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1.5px solid var(--border) !important;
            border-radius: 7px !important;
            padding: 7px 10px !important;
            font-size: 13px !important;
            outline: none !important;
        }

        .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--navy-500) !important;
        }

        /* ── Reference + Selected lists ── */
        .k-list {
            list-style: none;
            padding: 0;
            margin: 8px 0 0;
        }

        .k-list .k-list-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            background: var(--navy-50);
            border: 1.5px solid var(--navy-100);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            margin-bottom: 6px;
            font-size: 13px;
            color: var(--ink);
            transition: var(--transition);
        }

        .k-list .k-list-item:hover {
            border-color: var(--navy-300);
            background: var(--navy-100);
        }

        .k-list .k-list-item-body {
            flex: 1;
        }

        .k-list .k-list-item-name {
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 2px;
        }

        .k-list .k-list-item-sub {
            font-size: 11px;
            color: var(--navy-400);
        }

        /* ── Delete button ── */
        .k-del-btn {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #dc2626;
            border-radius: 7px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .k-del-btn:hover {
            background: #fee2e2;
            border-color: #f87171;
        }

        /* ── File upload area ── */
        .file-upload-area {
            border: 2px dashed var(--navy-200);
            border-radius: var(--radius-sm);
            padding: 12px 14px;
            background: var(--navy-50);
            margin-top: 8px;
            transition: var(--transition);
        }

        .file-upload-area:hover {
            border-color: var(--navy-400);
            background: var(--navy-100);
        }

        .file-upload-area input[type="file"] {
            width: 100%;
            font-size: 12px;
            color: var(--ink-light);
            border: none;
            background: transparent;
            cursor: pointer;
        }

        .file-upload-area .fua-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--navy-500);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        /* ── Section separator ── */
        .section-sep {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--navy-400);
            margin: 24px 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-sep::before,
        .section-sep::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--navy-100);
        }

        /* ── Reference results box ── */
        .reference-box {
            background: var(--navy-50);
            border: 1.5px solid var(--navy-100);
            border-radius: var(--radius-sm);
            padding: 14px;
            margin-top: 10px;
        }

        .reference-box .ref-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--navy-500);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .reference-results-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .reference-results-list li {
            font-size: 13px;
            color: var(--ink);
            padding: 5px 0;
            border-bottom: 1px solid var(--navy-100);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .reference-results-list li:last-child {
            border-bottom: none;
        }

        .reference-results-list li::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--navy-400);
            flex-shrink: 0;
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

        .kbtn:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        .kbtn-primary {
            background: linear-gradient(135deg, var(--navy-700) 0%, var(--navy-500) 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(30, 64, 128, .3);
        }

        .kbtn-primary:hover {
            background: linear-gradient(135deg, var(--navy-800) 0%, var(--navy-600) 100%);
            color: #fff;
            box-shadow: 0 6px 20px rgba(30, 64, 128, .4);
        }

        .kbtn-outline {
            background: var(--white);
            color: var(--navy-600);
            border: 1.5px solid var(--navy-200);
        }

        .kbtn-outline:hover {
            background: var(--navy-50);
            border-color: var(--navy-400);
            color: var(--navy-800);
        }

        .kbtn-success {
            background: linear-gradient(135deg, #15803d 0%, #16a34a 100%);
            color: #fff;
            box-shadow: 0 4px 14px rgba(22, 163, 74, .25);
        }

        .kbtn-success:hover {
            background: linear-gradient(135deg, #166534 0%, #15803d 100%);
            color: #fff;
        }

        .kbtn-sm {
            padding: 6px 12px;
            font-size: 12px;
            border-radius: 8px;
        }
    </style>

    <div class="container-fluid">
        <div class="upload-wrapper">

            {{-- ── Page Header Strip ── --}}
            <div class="page-strip">
                <div class="ps-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                <div>
                    <div class="ps-label">Komat Process</div>
                    <div class="ps-title">Upload Dokumen Komat</div>
                </div>
            </div>

            {{-- ── Error Alert ── --}}
            <div class="error-container">
                @if ($errors->any())
                    <div class="k-alert-danger">
                        <button class="k-alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                        <strong style="display:block;margin-bottom:6px"><i class="fas fa-exclamation-triangle me-1"></i>
                            Terdapat kesalahan:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- ── Form Card ── --}}
            <div class="form-card">
                <form id="uploadLogistikForm" action="{{ route('komatprocesshistory.uploaddoc') }}" method="POST"
                    enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="category" value="komat">

                    <div class="form-card-body">

                        {{-- Kode Material --}}
                        <div class="field-section">
                            <label class="field-label" for="kodematerial">
                                <i class="fas fa-barcode me-1"></i> Kode Material
                            </label>
                            <select id="kodematerial" name="kodematerial" class="select2" required>
                                <option value="" disabled selected>-- Pilih atau ketik kode material --</option>
                                @foreach ($komats as $komat)
                                    <option value="{{ $komat->kodematerial }}">
                                        {{ $komat->kodematerial }} - {{ $komat->material }} -
                                        {{ $komat->newbom->unit ?? 'No Unit' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="field-hint"><i class="fas fa-info-circle"></i> Pilih dari daftar atau ketik kode
                                material baru.</p>
                        </div>

                        {{-- Catatan --}}
                        <div class="field-section">
                            <label class="field-label" for="note">
                                <i class="fas fa-sticky-note me-1"></i> Catatan (Opsional)
                            </label>
                            <textarea name="note" id="note" class="k-textarea"
                                placeholder="Masukkan catatan untuk dokumen ini (opsional)"></textarea>
                        </div>

                        {{-- Search Reference --}}
                        <div class="field-section">
                            <button type="button" class="kbtn kbtn-outline kbtn-sm" id="searchReferenceBtn">
                                <i class="fas fa-search"></i> Search Reference
                            </button>
                            <div id="reference-results" class="reference-box" style="display:none">
                                <div class="ref-title"><i class="fas fa-link"></i> Reference Results</div>
                                <ul class="reference-results-list"></ul>
                            </div>
                        </div>

                        <div class="section-sep">Detail Dokumen</div>

                        {{-- Tipe Proyek --}}
                        <div class="field-section">
                            <label class="field-label" for="proyek_type_id">
                                <i class="fas fa-project-diagram me-1"></i> Tipe Proyek
                            </label>
                            <select name="proyek_type_id[]" id="proyek_type_id" class="select2" multiple required>
                                <option value="" disabled>-- Pilih proyek --</option>
                                @foreach ($listproject as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                            <p class="field-hint"><i class="fas fa-info-circle"></i> Pilih satu atau lebih tipe proyek.</p>
                            <div id="selected-projects-list" class="mt-2" style="display:none">
                                <p class="field-hint" style="font-weight:700;color:var(--navy-600);margin-bottom:6px">Proyek
                                    Terpilih:</p>
                                <ul class="k-list selected-projects"></ul>
                            </div>
                        </div>

                        {{-- Authority Level --}}
                        <div class="field-section">
                            <label class="field-label" for="authority_level">
                                <i class="fas fa-shield-alt me-1"></i> Authority Level
                            </label>
                            <select name="authority_level" id="authority_level" class="k-select" required>
                                <option value="" disabled selected>-- Pilih authority level --</option>
                                <option value="verifiednotneeded">Purchaser</option>
                                <option value="managerneeded">Manager</option>
                                <option value="seniormanagerneeded">Manager + SM</option>
                            </select>
                        </div>

                        {{-- Supplier --}}
                        <div class="field-section">
                            <label class="field-label" for="komat_supplier_id">
                                <i class="fas fa-industry me-1"></i> Supplier
                            </label>
                            <select name="komat_supplier_id" id="komat_supplier_id" class="select2" required>
                                <option value="" disabled selected>-- Pilih supplier --</option>
                                @foreach ($komatSupplier as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <p class="field-hint"><i class="fas fa-info-circle"></i> Ketik untuk mencari atau tambahkan
                                supplier baru.</p>
                        </div>

                        {{-- Requirement --}}
                        <div class="field-section">
                            <label class="field-label" for="requirement_list_id">
                                <i class="fas fa-clipboard-list me-1"></i> Requirement
                            </label>
                            <select name="requirement_list_id[]" id="requirement_list_id" class="select2" multiple required>
                                <option value="" disabled>-- Pilih requirement --</option>
                                @foreach ($requirements as $requirement)
                                    <option value="{{ $requirement->id }}">
                                        {{ $requirement->name ?? 'Requirement ' . $requirement->id }}
                                    </option>
                                @endforeach
                            </select>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px">
                                <p class="field-hint" style="margin:0"><i class="fas fa-info-circle"></i> Pilih satu atau
                                    lebih requirement. Ketik untuk menambahkan baru.</p>
                                <button type="button" class="kbtn kbtn-outline kbtn-sm" id="addNewRequirementBtn">
                                    <i class="fas fa-plus"></i> Tambah Baru
                                </button>
                            </div>
                            <div id="selected-requirements-list" class="mt-2" style="display:none">
                                <p class="field-hint" style="font-weight:700;color:var(--navy-600);margin-bottom:6px">
                                    Requirement & File Upload:</p>
                                <ul class="k-list selected-requirements"></ul>
                            </div>
                        </div>

                    </div>

                    <div class="form-card-footer">
                        <span class="footer-hint">
                            <i class="fas fa-info-circle"></i>
                            Pastikan semua field wajib telah terisi
                        </span>
                        <button type="submit" class="kbtn kbtn-success">
                            <i class="fas fa-cloud-upload-alt"></i> Upload Dokumen
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            const form = document.getElementById('uploadLogistikForm');
            const selectedProjectsList = $('.selected-projects');
            const selectedRequirementsList = $('.selected-requirements');
            const referenceResultsList = $('.reference-results-list');

            // Initialize Select2 for kodematerial
            $('#kodematerial').select2({
                placeholder: "Pilih atau ketik kode material",
                allowClear: true,
                tags: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: term, newTag: true };
                }
            });

            // Initialize Select2 for project types
            $('#proyek_type_id').select2({
                placeholder: "Pilih satu atau lebih tipe proyek",
                allowClear: true,
                closeOnSelect: false,
                minimumResultsForSearch: 1,
            });

            // Initialize Select2 for requirements
            $('#requirement_list_id').select2({
                placeholder: "Pilih satu atau lebih requirement",
                allowClear: true,
                closeOnSelect: false,
                minimumResultsForSearch: 1,
                tags: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '' || /\s/.test(term)) return null;
                    return { id: term, text: term, newTag: true };
                },
                language: {
                    noResults: function () { return ''; },
                    inputTooShort: function () { return 'Ketik nama requirement tanpa spasi'; }
                }
            });

            function addNewRequirement(name, description, callback) {
                if (/\s/.test(name)) {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Nama requirement tidak boleh mengandung spasi.', confirmButtonColor: '#163058' });
                    callback(false); return;
                }
                $.ajax({
                    url: '{{ route('komatprocesshistory.addRequirement') }}',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { name, description },
                    success: function (data) {
                        if (data.success) {
                            const newOption = new Option(data.requirement.name, data.requirement.id, true, true);
                            $('#requirement_list_id').append(newOption).trigger('change');
                            updateSelectedRequirementsList();
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Requirement berhasil ditambahkan.', confirmButtonColor: '#163058' });
                            callback(true);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Gagal!', text: data.error || 'Terjadi kesalahan.', confirmButtonColor: '#163058' });
                            callback(false);
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.error || 'Server error', confirmButtonColor: '#163058' });
                        callback(false);
                    }
                });
            }

            $('#addNewRequirementBtn').on('click', function () {
                Swal.fire({
                    title: 'Tambah Requirement Baru',
                    html: `<input type="text" id="requirementName" class="swal2-input" placeholder="Nama Requirement (tanpa spasi)">
                               <textarea id="requirementDescription" class="swal2-textarea" placeholder="Deskripsi (opsional)"></textarea>`,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#163058',
                    cancelButtonColor: '#6B7280',
                    preConfirm: () => {
                        const name = Swal.getPopup().querySelector('#requirementName').value;
                        const description = Swal.getPopup().querySelector('#requirementDescription').value;
                        if (!name) { Swal.showValidationMessage('Nama requirement harus diisi'); return false; }
                        if (/\s/.test(name)) { Swal.showValidationMessage('Nama requirement tidak boleh mengandung spasi'); return false; }
                        return { name, description };
                    }
                }).then((result) => {
                    if (result.isConfirmed) addNewRequirement(result.value.name, result.value.description, () => { });
                });
            });

            $('#requirement_list_id').on('select2:select', function (e) {
                var data = e.params.data;
                if (data.newTag) {
                    Swal.fire({
                        title: 'Tambah Requirement Baru',
                        html: `<input type="text" id="requirementName" class="swal2-input" value="${data.text}" placeholder="Nama Requirement (tanpa spasi)">
                                   <textarea id="requirementDescription" class="swal2-textarea" placeholder="Deskripsi (opsional)"></textarea>`,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#163058',
                        cancelButtonColor: '#6B7280',
                        preConfirm: () => {
                            const name = Swal.getPopup().querySelector('#requirementName').value;
                            const description = Swal.getPopup().querySelector('#requirementDescription').value;
                            if (!name) { Swal.showValidationMessage('Nama requirement harus diisi'); return false; }
                            if (/\s/.test(name)) { Swal.showValidationMessage('Nama requirement tidak boleh mengandung spasi'); return false; }
                            return { name, description };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) addNewRequirement(result.value.name, result.value.description, (s) => { if (!s) $('#requirement_list_id').val(null).trigger('change'); });
                        else $('#requirement_list_id').val(null).trigger('change');
                    });
                }
            });

            function updateSelectedProjectsList() {
                const selectedOptions = $('#proyek_type_id').select2('data');
                selectedProjectsList.empty();
                if (selectedOptions.length) {
                    $('#selected-projects-list').show();
                    selectedOptions.forEach(option => {
                        selectedProjectsList.append(`
                                <li class="k-list-item" data-id="${option.id}">
                                    <div class="k-list-item-body">
                                        <div class="k-list-item-name"><i class="fas fa-project-diagram me-1" style="color:var(--navy-400)"></i>${option.text}</div>
                                    </div>
                                    <button type="button" class="k-del-btn delete-project-btn"><i class="fas fa-times"></i> Hapus</button>
                                </li>`);
                    });
                } else {
                    $('#selected-projects-list').hide();
                }
            }

            function updateSelectedRequirementsList() {
                const selectedOptions = $('#requirement_list_id').select2('data');
                selectedRequirementsList.empty();
                if (selectedOptions.length) {
                    $('#selected-requirements-list').show();
                    selectedOptions.forEach(option => {
                        selectedRequirementsList.append(`
                                <li class="k-list-item" data-id="${option.id}" style="flex-direction:column;align-items:stretch;">
                                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                                        <div class="k-list-item-name"><i class="fas fa-clipboard-list me-1" style="color:var(--navy-400)"></i>${option.text}</div>
                                        <button type="button" class="k-del-btn delete-requirement-btn"><i class="fas fa-times"></i> Hapus</button>
                                    </div>
                                    <div class="file-upload-area">
                                        <div class="fua-label"><i class="fas fa-paperclip me-1"></i> Upload File</div>
                                        <input type="file" name="file_${option.id}[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png,.jpeg" required />
                                        <p style="font-size:11px;color:var(--navy-400);margin:4px 0 0">Format: PDF, DOC, XLS, JPG, PNG</p>
                                    </div>
                                </li>`);
                    });
                } else {
                    $('#selected-requirements-list').hide();
                }
            }

            $('#proyek_type_id').on('select2:select select2:unselect', function () { updateSelectedProjectsList(); });
            $('#requirement_list_id').on('select2:select select2:unselect', function () { updateSelectedRequirementsList(); });

            selectedProjectsList.on('click', '.delete-project-btn', function () {
                const id = String($(this).closest('li').data('id'));
                const vals = ($('#proyek_type_id').val() || []).filter(v => v !== id);
                $('#proyek_type_id').val(vals).trigger('change');
                updateSelectedProjectsList();
            });

            selectedRequirementsList.on('click', '.delete-requirement-btn', function () {
                const id = String($(this).closest('li').data('id'));
                const vals = ($('#requirement_list_id').val() || []).filter(v => v !== id);
                $('#requirement_list_id').val(vals).trigger('change');
                updateSelectedRequirementsList();
            });

            // Supplier Select2 with AJAX
            $('#komat_supplier_id').select2({
                placeholder: "Ketik untuk mencari atau tambahkan supplier",
                allowClear: true,
                tags: true,
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: term, newTag: true };
                },
                ajax: {
                    url: '{{ route('komatprocesshistory.searchSuppliers') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) { return { q: params.term || '', page: params.page || 1 }; },
                    processResults: function (data) {
                        return {
                            results: data.suppliers.map(s => ({ id: s.id, text: s.name })),
                            pagination: { more: data.pagination.more }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                templateResult: function (data) {
                    if (data.loading) return data.text;
                    var $r = $('<span>' + data.text + '</span>');
                    if (data.newTag) $r.append(' <small style="color:var(--navy-400)">(Tambah baru)</small>');
                    return $r;
                }
            });

            $('#komat_supplier_id').on('select2:select', function (e) {
                var data = e.params.data;
                if (data.newTag) {
                    Swal.fire({
                        title: 'Tambah Supplier Baru',
                        html: `<input type="text" id="supplierName" class="swal2-input" value="${data.text}" placeholder="Nama Supplier">
                                   <input type="text" id="supplierDescription" class="swal2-input" placeholder="Deskripsi (opsional)">`,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#163058',
                        cancelButtonColor: '#6B7280',
                        preConfirm: () => {
                            const name = Swal.getPopup().querySelector('#supplierName').value;
                            const description = Swal.getPopup().querySelector('#supplierDescription').value;
                            if (!name) { Swal.showValidationMessage('Nama supplier harus diisi'); return false; }
                            return { name, description };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('komatprocesshistory.addSupplier') }}',
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                data: result.value,
                                success: function (data) {
                                    if (data.success) {
                                        const newOption = new Option(data.supplier.name, data.supplier.id, true, true);
                                        $('#komat_supplier_id').append(newOption).trigger('change');
                                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Supplier berhasil ditambahkan.', confirmButtonColor: '#163058' });
                                    } else {
                                        Swal.fire({ icon: 'error', title: 'Gagal!', text: data.error || 'Terjadi kesalahan.', confirmButtonColor: '#163058' });
                                        $('#komat_supplier_id').val(null).trigger('change');
                                    }
                                },
                                error: function (xhr) {
                                    Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.error || 'Server error', confirmButtonColor: '#163058' });
                                    $('#komat_supplier_id').val(null).trigger('change');
                                }
                            });
                        } else {
                            $('#komat_supplier_id').val(null).trigger('change');
                        }
                    });
                }
            });

            // Search Reference
            $('#searchReferenceBtn').on('click', function () {
                const kodematerial = $('#kodematerial').val();
                if (!kodematerial) {
                    Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Harap pilih kode material terlebih dahulu.', confirmButtonColor: '#163058' });
                    return;
                }
                Swal.fire({ title: 'Memuat...', text: 'Sedang mencari referensi requirement.', allowOutsideClick: false, allowEscapeKey: false, didOpen: () => Swal.showLoading() });
                $.ajax({
                    url: '{{ route('komatprocesshistory.referencerelation') }}',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    data: { kodematerial },
                    success: function (data) {
                        referenceResultsList.empty();
                        $('#reference-results').show();
                        if (data.success && data.data && data.data.length > 0) {
                            data.data.forEach(req => referenceResultsList.append(`<li>${req}</li>`));
                            $('#requirement_list_id').val(null).trigger('change');
                            const reqOptions = $('#requirement_list_id option').map(function () { return $(this).text(); }).get();
                            data.data.filter(r => reqOptions.includes(r)).forEach(r => {
                                const option = $('#requirement_list_id option').filter(function () { return $(this).text() === r; });
                                if (option.length) {
                                    const currentVals = $('#requirement_list_id').val() || [];
                                    $('#requirement_list_id').val([...currentVals, option.val()]).trigger('change');
                                }
                            });
                            updateSelectedRequirementsList();
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Referensi requirement berhasil dimuat.', confirmButtonColor: '#163058' });
                        } else {
                            referenceResultsList.append('<li>Tidak ada referensi ditemukan</li>');
                            Swal.fire({ icon: 'info', title: 'Informasi', text: 'Tidak ada referensi requirement ditemukan.', confirmButtonColor: '#163058' });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({ icon: 'error', title: 'Gagal!', text: xhr.responseJSON?.message || 'Terjadi kesalahan.', confirmButtonColor: '#163058' });
                    }
                });
            });

            // Form submit validation
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const selectedRequirements = $('#requirement_list_id').val() || [];
                if (!selectedRequirements.length) {
                    Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Harap pilih minimal satu requirement dan upload file terkait.', confirmButtonColor: '#163058' });
                    return;
                }
                let validFiles = true;
                selectedRequirements.forEach(id => {
                    const fi = document.querySelector(`input[name="file_${id}[]"]`);
                    if (!fi || !fi.files.length) validFiles = false;
                });
                if (!validFiles) {
                    Swal.fire({ icon: 'warning', title: 'Oops...', text: 'File untuk setiap requirement yang dipilih harus diupload.', confirmButtonColor: '#163058' });
                    return;
                }
                const selectedProjects = $('#proyek_type_id').val();
                if (!selectedProjects || !selectedProjects.length) {
                    Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Harap pilih minimal satu tipe proyek.', confirmButtonColor: '#163058' });
                    return;
                }
                const hasSpaces = $('#requirement_list_id').select2('data').some(o => /\s/.test(o.text));
                if (hasSpaces) {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Nama requirement tidak boleh mengandung spasi.', confirmButtonColor: '#163058' });
                    return;
                }
                Swal.fire({
                    title: 'Konfirmasi Upload',
                    text: 'Apakah Anda yakin ingin meng-upload dokumen ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#163058',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, upload sekarang',
                    cancelButtonText: 'Batal'
                }).then((result) => { if (result.isConfirmed) form.submit(); });
            });
        });
    </script>
@endpush