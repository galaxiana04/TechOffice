@extends('layouts.universal')

@section('container2')
    {{-- Header Gradient Style (Sama persis dengan Code 1) --}}
    <div class="bg-gradient-to-r from-red-600 to-red-700 border-b border-red-900 py-8 shadow-lg">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="text-3xl font-bold text-white mb-2 tracking-tight flex items-center">
                        <i class="fas fa-boxes mr-3 text-red-200"></i>Katalog Material
                    </h1>
                    <p class="text-red-100 ml-1">Manajemen data stok dan spesifikasi material</p>
                </div>
                <div class="col-sm-6">
                    <ol
                        class="breadcrumb bg-white/10 backdrop-blur-sm px-4 py-2 rounded-lg float-right mb-0 border border-white/20">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-black hover:text-red-200 font-medium transition-colors">
                                <i class="fas fa-home mr-1"></i>Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-red-100 font-semibold">Katalog Komat</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container mx-auto px-4 py-8">
        <div class="row justify-content-center">
            <div class="col-lg-12">

                <div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">

                    {{-- Card Header & Buttons Area --}}
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            
                            {{-- Title --}}
                            <h4 class="text-gray-800 text-lg font-bold flex items-center">
                                <i class="fas fa-clipboard-list text-red-700 mr-3"></i>
                                Data Katalog Material
                            </h4>

                            {{-- Tombol Action dari Code 2 (Didesain ulang ala Tailwind) --}}
                            <div class="flex gap-2">
                                <a href="{{ url('katalogkomat/uploadexcel') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                                    <i class="fas fa-file-excel mr-2"></i> Upload Excel
                                </a>
                                <a href="{{ route('chat.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                                    <i class="fas fa-comments mr-2"></i> Chat Your Komat
                                </a>
                            </div>

                        </div>
                    </div>

                    <div class="card-body p-0">
                        {{-- Error Alert Style (Sama persis dengan Code 1) --}}
                        @if ($errors->any())
                            <div class="m-6 mb-0">
                                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm">
                                    <div class="flex items-start">
                                        <i class="fas fa-exclamation-circle mt-1 mr-3 text-red-600 text-xl"></i>
                                        <div>
                                            <strong class="font-bold block mb-2">Terdapat Kesalahan Input!</strong>
                                            <ul class="list-disc list-inside text-sm mt-1 text-red-700 space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Table Area --}}
                        <div class="p-6">
                            <div class="table-responsive">
                                <table class="table w-100" id="katalogKomatTable">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Kode Material</th>
                                            <th>Deskripsi</th>
                                            <th>Spesifikasi</th>
                                            <th>UoM</th>
                                            <th>Stok UU (Ekspedisi)</th>
                                            <th>Stok UU (Gudang)</th>
                                            <th>Stok Project (Ekspedisi)</th>
                                            <th>Stok Project (Gudang)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-700">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    {{-- Menggunakan CSS yang sama persis dengan Code 1 --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        /* Modern Table Design */
        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0;
            margin-top: 0 !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .dataTables_wrapper {
            padding: 0;
        }

        /* Table Header Styling */
        table.dataTable thead th {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
            color: white !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem 0.75rem !important;
            border-bottom: 3px solid #7f1d1d !important;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        table.dataTable thead th:first-child {
            border-top-left-radius: 0.5rem;
        }

        table.dataTable thead th:last-child {
            border-top-right-radius: 0.5rem;
        }

        /* Search Input */
        .dataTables_filter {
            margin-bottom: 1rem;
        }

        .dataTables_filter label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dataTables_filter input {
            border: 2px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 0.625rem 1rem !important;
            font-size: 0.875rem !important;
            color: #374151 !important;
            background-color: #fff !important;
            transition: all 0.3s ease;
            outline: none;
            width: 300px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .dataTables_filter input:focus {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
            transform: translateY(-1px);
        }

        /* Length Menu Styling */
        .dataTables_length {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dataTables_length label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0 !important;
        }

        .dataTables_length select {
            border: 2px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 2rem 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
            background-color: white !important;
            transition: all 0.3s ease;
            height: 38px;
            line-height: 1.5;
        }

        .dataTables_length select:focus {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
        }

        /* Pagination */
        .dataTables_paginate {
            margin-top: 1.5rem !important;
        }

        .dataTables_paginate .paginate_button {
            border: 2px solid #e5e7eb !important;
            background: white !important;
            color: #374151 !important;
            border-radius: 0.5rem !important;
            margin: 0 0.25rem !important;
            padding: 0.5rem 1rem !important;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .dataTables_paginate .paginate_button:hover {
            background: #fef2f2 !important;
            color: #dc2626 !important;
            border-color: #fca5a5 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dataTables_paginate .paginate_button.current,
        .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
            color: white !important;
            border-color: #dc2626 !important;
            box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3);
            transform: translateY(-2px);
        }

        .dataTables_paginate .paginate_button.disabled {
            color: #9ca3af !important;
            background: #f9fafb !important;
            cursor: not-allowed;
            opacity: 0.5;
        }

        /* Table Row Styles */
        table.dataTable tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #f3f4f6;
        }

        table.dataTable tbody tr:hover {
            background: linear-gradient(90deg, #fef2f2 0%, #ffffff 100%) !important;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.1);
        }

        table.dataTable tbody td {
            padding: 1rem 0.75rem !important;
            vertical-align: middle;
        }

        /* Badge Styles */
        .badge-flat {
            display: inline-block;
            padding: 0.375rem 0.875rem;
            font-size: 0.8125rem;
            font-weight: 700;
            border-radius: 0.375rem;
            text-align: center;
            min-width: 50px;
            transition: all 0.2s ease;
        }

        .badge-flat:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .badge-ekspedisi {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #fca5a5;
        }

        .badge-gudang {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #374151;
            border: 2px solid #d1d5db;
        }

        /* Info Text Styling */
        .dataTables_info {
            color: #6b7280 !important;
            font-size: 0.875rem !important;
            font-weight: 500;
        }

        /* Typography */
        .font-mono-code {
            font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Roboto Mono', Consolas, 'Courier New', monospace;
            letter-spacing: -0.02em;
        }

        /* Responsive Design */
        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
            background-color: #dc2626;
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3);
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            left: 8px !important;
            top: 60% !important;
            transform: translateY(-50%) !important;
            width: 16px !important;
            height: 16px !important;
            line-height: 14px !important;
            font-size: 14px !important;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:hover:before {
            background-color: #b91c1c;
            transform: translateY(-50%) scale(1.1);
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control {
            padding-left: 35px !important;
        }

        /* Loading State */
        .dataTables_processing {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
            color: white !important;
            border-radius: 0.5rem !important;
            padding: 1rem 2rem !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 6px rgba(220, 38, 38, 0.3) !important;
        }

        /* Empty State */
        .dataTables_empty {
            padding: 3rem !important;
            text-align: center;
            color: #9ca3af !important;
            font-size: 1rem !important;
        }

        /* Scrollbar Styling */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f3f4f6;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #dc2626;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #b91c1c;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Konfigurasi DataTable dari Code 1 diterapkan disini
            $('#katalogKomatTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('katalogkomat.getData') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center align-middle',
                        render: function (data) {
                            return '<span class="text-gray-600 font-mono-code font-bold text-sm">' + data + '</span>';
                        }
                    },
                    {
                        data: 'kodematerial',
                        name: 'kodematerial',
                        className: 'align-middle',
                        render: function (data) {
                            return '<span class="text-red-700 font-mono-code font-bold text-sm tracking-tight bg-red-50 px-3 py-1 rounded-md border border-red-200">' + data + '</span>';
                        }
                    },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi',
                        className: 'align-middle',
                        render: function (data) {
                            return '<span class="text-gray-900 font-semibold leading-relaxed block min-w-[200px]">' + data + '</span>';
                        }
                    },
                    {
                        data: 'spesifikasi',
                        name: 'spesifikasi',
                        className: 'align-middle',
                        render: function (data) {
                            return '<span class="text-gray-600 text-xs leading-relaxed">' + data + '</span>';
                        }
                    },
                    {
                        data: 'UoM',
                        name: 'UoM',
                        className: 'text-center align-middle',
                        render: function (data) {
                            return '<span class="text-xs font-bold text-gray-700 bg-gradient-to-r from-gray-100 to-gray-200 px-3 py-1.5 rounded-lg border-2 border-gray-300 shadow-sm">' + data + '</span>';
                        }
                    },
                    {
                        data: 'stokUUekpedisi',
                        name: 'stokUUekpedisi',
                        className: 'text-center align-middle',
                        render: function (data) {
                            return '<span class="badge-flat badge-ekspedisi">' + data + '</span>';
                        }
                    },
                    {
                        data: 'stokUUgudang',
                        name: 'stokUUgudang',
                        className: 'text-center align-middle',
                        render: function (data) {
                            return '<span class="badge-flat badge-gudang">' + data + '</span>';
                        }
                    },
                    {
                        data: 'stokprojectekpedisi',
                        name: 'stokprojectekpedisi',
                        className: 'text-center align-middle',
                        render: function (data) {
                            return '<span class="badge-flat badge-ekspedisi">' + data + '</span>';
                        }
                    },
                    {
                        data: 'stokprojectgudang',
                        name: 'stokprojectgudang',
                        className: 'text-center align-middle',
                        render: function (data) {
                            return '<span class="badge-flat badge-gudang">' + data + '</span>';
                        }
                    }
                ],
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 1, targets: 1 },
                    { responsivePriority: 2, targets: 2 },
                ],
                pageLength: 10,
                language: {
                    search: "",
                    searchPlaceholder: "🔍 Cari data katalog...",
                    paginate: {
                        previous: "← Prev",
                        next: "Next →"
                    },
                    info: "<span class='text-gray-600 text-sm font-medium'>Menampilkan _START_ - _END_ dari _TOTAL_ data</span>",
                    lengthMenu: "<span class='text-gray-600 text-sm font-medium mr-2'>Tampilkan:</span> _MENU_",
                    emptyTable: "📦 Data tidak tersedia",
                    zeroRecords: "🔍 Pencarian tidak ditemukan",
                    processing: "⏳ Memuat data..."
                },
                dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 gap-4"<"flex items-center"l><"w-full md:w-auto"f>>t<"flex flex-col md:flex-row justify-between items-center mt-6 gap-4"ip>',
            });
        });
    </script>
@endpush