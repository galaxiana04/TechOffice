@extends('layouts.universal')

@php
    $authuser = auth()->user();
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-gradient-light px-3 py-2 rounded shadow-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ route('new-memo.index') }}" class="text-primary">
                                <i class="fas fa-home mr-1"></i>List Memo
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="card card-danger card-outline shadow-lg animated-card">
        <div class="card-header bg-gradient-danger">
            <div class="card-tools">
                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <h3 class="card-title text-bold text-white">
                <i class="fas fa-chart-line mr-2"></i>Page Monitoring Memo
                <span class="badge badge-light ml-2 pulse-badge"></span>
            </h3>
        </div>
        <div class="card-body">
            <!-- Dropdown for revisiall -->
            <div class="form-group">
                <label for="revisiSelect" class="font-weight-bold text-secondary">
                    <i class="fas fa-project-diagram mr-2"></i>Pilih Project :
                </label>
                <select class="form-control custom-select-animated" id="revisiSelect"
                    onchange="showRevisiContent(this.value)">
                    @foreach ($revisiall as $keyan => $revisi)
                        <option value="{{ $keyan }}" @if ($loop->first) selected @endif>
                            {{ $keyan }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Content for revisiall -->
            <div class="revisi-content">
                @foreach ($revisiall as $keyan => $revisi)
                    <div id="revisi-{{ $keyan }}" class="revisi-section fade-in"
                        @if (!$loop->first) style="display:none;" @endif>
                        <div class="row mb-3">
                            @if ($authuser->rule == 'superuser')
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                    <button type="button" class="btn btn-danger btn-sm btn-block btn-animated shadow-sm"
                                        onclick="handleDeleteMultipleItems()">
                                        <i class="fas fa-trash-alt mr-2"></i>Hapus yang dipilih
                                    </button>
                                </div>

                                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                    <a href="" class="btn btn-primary btn-sm btn-block btn-animated shadow-sm">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Dokumen
                                    </a>
                                </div>
                            @endif

                            <div class="col-lg-4 col-md-4 col-sm-12 col-12 mb-2">
                                <form action="{{ route('new-memo.downloadall') }}" method="GET">
                                    <div class="input-group input-group-animated">
                                        <select name="unit" class="form-control">
                                            <option value="all">All Units</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->name }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fas fa-download mr-1"></i>Download Report
                                            </button>

                                        </div>

                                    </div>
                                </form>
                            </div>


                        </div>
                        <div class="mb-3">
                            <div class="row g-2">
                                <div class="col-auto">
                                    <a href="{{ url('new-memo/upload') }}" class="btn bg-maroon btn-sm">
                                        <i class="fas fa-upload mr-1"></i> Upload Memo
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('new-memo.indextertutup') }}" class="btn bg-teal btn-sm">
                                        <i class="fas fa-lock mr-1"></i> Memo Tertutup
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('new-memo.monitoring.unit') }}"
                                        class="btn btn-primary btn-sm btn-animated shadow-sm">
                                        <i class="fas fa-tasks mr-1"></i> Monitoring Unit
                                    </a>
                                </div>
                            </div>
                        </div>



                        <!-- Nav tabs for units -->
                        <ul class="nav nav-tabs nav-tabs-custom" id="unitTabs-{{ $keyan }}" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="all-tab-{{ $keyan }}" data-toggle="tab"
                                    href="#all-{{ $keyan }}" role="tab" aria-controls="all-{{ $keyan }}"
                                    aria-selected="true">
                                    <i class="fas fa-th-large mr-1"></i>All
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="unbound-tab-{{ $keyan }}" data-toggle="tab"
                                    href="#unbound-{{ $keyan }}" role="tab"
                                    aria-controls="unbound-{{ $keyan }}" aria-selected="false">
                                    <i class="fas fa-unlink mr-1"></i>Unbound
                                </a>
                            </li>
                            @foreach ($units as $unit)
                                <li class="nav-item">
                                    <a class="nav-link"
                                        id="{{ str_replace(' ', '_', $unit->singkatan) }}-tab-{{ $keyan }}"
                                        data-toggle="tab"
                                        href="#{{ str_replace(' ', '_', $unit->singkatan) }}-{{ $keyan }}"
                                        role="tab"
                                        aria-controls="{{ str_replace(' ', '_', $unit->singkatan) }}-{{ $keyan }}"
                                        aria-selected="false">
                                        <i class="fas fa-folder mr-1"></i>{{ $unit->singkatan }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tab content -->
                        <div class="tab-content tab-content-animated" id="unitTabContent-{{ $keyan }}">
                            <!-- Tab All -->
                            <div class="tab-pane fade show active" id="all-{{ $keyan }}" role="tabpanel"
                                aria-labelledby="all-tab-{{ $keyan }}">
                                <div class="table-responsive table-wrapper">
                                    <table id="example2-{{ $keyan }}-all"
                                        class="table table-bordered table-hover table-striped custom-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center fit-col">No</th>
                                                <th class="text-center fit-col">Deadline</th>
                                                <th class="text-center fit-col">No Dokumen</th>

                                                <th class="text-center auto-col">Nama Dokumen</th>

                                                <th class="text-center fit-col">Progress</th>

                                                <th class="text-center workflow-col">Posisi Memo</th>

                                                <th class="text-center fit-col">Status</th>
                                                <th class="text-center fit-col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $documents = $revisi['documents'];
                                                $counterdokumen = 1;
                                            @endphp

                                            @include('newmemo.index.index_loopbody', [
                                                'documents' => $documents,
                                            ])
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tabs for each unit -->
                            @foreach ($units as $unit)
                                <div class="tab-pane fade"
                                    id="{{ str_replace(' ', '_', $unit->singkatan) }}-{{ $keyan }}"
                                    role="tabpanel"
                                    aria-labelledby="{{ str_replace(' ', '_', $unit->name) }}-tab-{{ $keyan }}">
                                    <div class="table-responsive table-wrapper">
                                        <table
                                            id="example2-{{ $keyan }}-{{ str_replace(' ', '_', $unit->singkatan) }}"
                                            class="table table-bordered table-hover table-striped custom-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center fit-col">No</th>
                                                    <th class="text-center fit-col">Deadline</th>
                                                    <th class="text-center fit-col">No Dokumen</th>

                                                    <th class="text-center auto-col">Nama Dokumen</th>

                                                    <th class="text-center fit-col">Progress</th>

                                                    <th class="text-center workflow-col">Posisi Memo</th>

                                                    <th class="text-center fit-col">Status</th>
                                                    <th class="text-center fit-col">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $documents = $revisi['units'][$unit->name];
                                                    $counterdokumen = 1;
                                                @endphp

                                                @include('newmemo.index.index_loopbody', [
                                                    'documents' => $documents,
                                                ])
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Tab for unbound documents -->
                            <div class="tab-pane fade" id="unbound-{{ $keyan }}" role="tabpanel"
                                aria-labelledby="unbound-tab-{{ $keyan }}">
                                <div class="table-responsive table-wrapper">
                                    <table id="example2-{{ $keyan }}-unbound"
                                        class="table table-bordered table-hover table-striped custom-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center fit-col">No</th>
                                                <th class="text-center fit-col">Deadline</th>
                                                <th class="text-center fit-col">No Dokumen</th>
                                                <th class="text-center auto-col">Nama Dokumen</th>
                                                <th class="text-center fit-col">Progress</th>
                                                <th class="text-center workflow-col">Posisi Memo</th>
                                                <th class="text-center fit-col">Status</th>
                                                <th class="text-center fit-col">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $documents = $revisi['units']['unbound'];
                                                $counterdokumen = 1;
                                            @endphp

                                            @include('newmemo.index.index_loopbody', [
                                                'documents' => $documents,
                                            ])
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- CUSTOM CSS -->
    <style>
        :root {
            /* Ganti warna utama di sini */
            --primary-color: #007bff;
            --primary-dark: #0056b3;
            --primary-light: #e7f1ff;
            --focus-ring: rgba(0, 123, 255, 0.25);

            --text-dark: #343a40;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #dee2e6;
        }

        .animated-card {
            animation: slideInUp 0.4s ease-out;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        @keyframes slideInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* HEADER CARD */
        .bg-gradient-danger {
            background: linear-gradient(to right, #a9242d, #8f1e26);
            color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /*TABS (Minimalis)*/
        .nav-tabs-custom {
            border-bottom: 1px solid var(--border-color);
            background: #fff;
            padding: 10px 10px 0 10px;
        }

        .nav-tabs-custom .nav-link {
            color: var(--text-muted);
            font-weight: 500;
            border: none;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
            padding: 10px 20px;
        }

        .nav-tabs-custom .nav-link:hover {
            color: var(--primary-color);
            background: transparent;
        }

        .nav-tabs-custom .nav-link.active {
            color: var(--primary-color);
            background: transparent;
            border-bottom: 3px solid var(--primary-color);
            /* Hanya garis bawah */
            font-weight: 700;
        }

        /* Hilangkan efek garis after yang lama */
        .nav-tabs-custom .nav-link::after {
            display: none;
        }

        /*TABLE (Clean & Professional)*/
        .table-wrapper {
            background: white;
            border-radius: 8px;
            padding: 1;
            border: 1px solid var(--border-color);
            box-shadow: none;
        }

        .custom-table {
            margin-bottom: 0;
        }

        /* Header Tabel*/
        .custom-table thead {
            background: #E2E8F0;
            color: var(--text-dark);
            border-bottom: 2px solid var(--border-color);
        }

        .custom-table thead th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            padding: 12px;
            border: none;
        }

        /* Baris Tabel */
        .custom-table tbody tr {
            transition: background-color 0.2s;
        }

        /* Hover Effect: Soft Grey (Tidak ada Zoom/Scale lagi) */
        .custom-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: none;
            box-shadow: none;
        }

        .custom-table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-top: 1px solid var(--border-color);
            color: #495057;
            font-size: 13px;
        }

        /*BUTTONS & INPUTS*/
        .custom-select-animated {
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-shadow: none;
        }

        .custom-select-animated:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(169, 36, 45, 0.15);
            /* Shadow merah transparan */
        }


        .custom-table {
            width: 100% !important;
            table-layout: auto;
        }

        .custom-table td.fit-content,
        .custom-table th.fit-content {
            width: 1%;
            white-space: nowrap;
            padding-left: 15px;
            padding-right: 15px;
        }

        .custom-table td.fit-col,
        .custom-table th.fit-col {
            width: 1%;
            white-space: nowrap;
            padding-left: 10px;
            padding-right: 10px;
        }

        .custom-table td.auto-col {
            white-space: normal !important;
            max-width: 300px;
        }

        /* 4. Khusus kolom Workflow (Posisi Memo) */
        .custom-table td.workflow-col {
            white-space: nowrap;
            width: 1%;
        }

        .btn-animated {
            border-radius: 4px;
            font-weight: 500;
            box-shadow: none;
            transition: all 0.2s;
        }

        .btn-animated:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Breadcrumb Clean */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 0;
        }

        .breadcrumb-item a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-color) !important;
        }

        /*UTILITIES */
        .text-primary {
            color: var(--primary-color) !important;
        }

        /* Sticky Columns Background (Wajib putih agar tidak transparan) */
        .col-doc-name,
        .sticky-col {
            background-color: #fff !important;
        }

        /* Saat di hover, sticky column juga ikut berubah warnanya */
        .custom-table tbody tr:hover .col-doc-name {
            background-color: #f8f9fa !important;
        }

        .pulse-badge {
            animation: none;
            /* Hilangkan animasi pulse jika mengganggu */
        }

        /* Scrollbar Halus */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        // --- 1. Konfigurasi DataTable Global ---
        var tableConfig = {
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "scrollX": false,
            "responsive": true,
            "language": {
                "search": "Cari:",
                "paginate": {
                    "previous": "‹",
                    "next": "›"
                },
                "emptyTable": "Tidak ada data"
            }
        };

        // --- 2. Fungsi Inisialisasi DataTable (Hanya jika belum aktif) ---
        function initDataTable(tableElement) {
            if (!$.fn.DataTable.isDataTable(tableElement)) {
                $(tableElement).DataTable(tableConfig);
            }
        }

        // --- 3. Logika Lazy Loading ---
        $(function() {
            // A. Inisialisasi tabel yang terlihat saat pertama kali load
            $('.revisi-section:visible .tab-pane.active table.custom-table').each(function() {
                initDataTable(this);
            });

            // B. Event Listener: Inisialisasi saat Tab diklik/dibuka
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var targetId = $(e.target).attr("href"); // ID tab tujuan (#all-KEY, #unit-KEY)
                var table = $(targetId).find('table.custom-table');

                initDataTable(table); // Inisialisasi hanya tabel ini

                // Recalculate layout agar rapi
                $(table).DataTable().columns.adjust().responsive.recalc();
            });
        });

        // --- 4. Fungsi Dropdown Project (Diperbarui) ---
        function showRevisiContent(keyan) {
            // Sembunyikan semua section
            $('.revisi-section').css('opacity', '0');
            setTimeout(() => {
                $('.revisi-section').hide();

                // Tampilkan section yang dipilih
                var selectedSection = $('#revisi-' + keyan);
                if (selectedSection.length) {
                    selectedSection.show();

                    // Init DataTable pada tab aktif di project yang baru dipilih
                    var activeTable = selectedSection.find('.tab-pane.active table.custom-table');
                    initDataTable(activeTable);

                    setTimeout(() => {
                        selectedSection.css('opacity', '1');
                        // Adjust kolom jika sudah ter-init
                        if ($.fn.DataTable.isDataTable(activeTable)) {
                            activeTable.DataTable().columns.adjust().responsive.recalc();
                        }
                    }, 50);
                }
            }, 300);
        }

        // --- 5. Fungsi Delete & Button Animation (Tetap Sama) ---
        document.querySelectorAll('.btn-animated').forEach(button => {
            button.addEventListener('click', function() {
                if (!this.classList.contains('loading')) {
                    const originalText = this.innerHTML;
                    this.classList.add('loading');
                    this.disabled = true;
                    this.innerHTML = '<span class="loading-spinner"></span> Processing...';
                    setTimeout(() => {
                        this.classList.remove('loading');
                        this.disabled = false;
                        this.innerHTML = originalText;
                    }, 2000);
                }
            });
        });

        function handleDeleteMultipleItems() {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin menghapus item yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash mr-2"></i>Ya, hapus!',
                cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectedDocumentIds = [];
                    // Selector ini tetap bekerja walaupun DataTable belum di-init (karena HTML checkbox tetap ada di DOM)
                    document.querySelectorAll('input[name="document_ids[]"]:checked').forEach(function(checkbox) {
                        selectedDocumentIds.push(checkbox.value);
                    });

                    if (selectedDocumentIds.length === 0) {
                        Swal.fire('Info', 'Tidak ada item yang dipilih', 'info');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('document.deleteMultiple') }}", // Pastikan route ini benar
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            document_ids: selectedDocumentIds
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Item dihapus.',
                                icon: 'success'
                            });
                            location.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Gagal menghapus.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush