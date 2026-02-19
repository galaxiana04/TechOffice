@extends('layouts.universal')

@section('container2')
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 shadow-sm py-6">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-book-open text-blue-600 mr-2"></i>Library 
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb bg-white px-4 py-2 rounded-lg float-right shadow-sm mb-0">
                        <li class="breadcrumb-item">
                            <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-home mr-1"></i>Home
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-gray-600">Library</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container mx-auto px-4 py-6">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card border-0 shadow-lg rounded-3xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white py-6 px-8">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="text-2xl font-bold mb-1">Library Dokumen</h3>
                                <p class="text-blue-100 text-sm mb-0">Total: {{ $files->sum->count() }} dokumen</p>
                            </div>
                            <button type="button" class="btn btn-sm bg-white bg-opacity-20 hover:bg-opacity-30 text-white border-0 rounded-lg transition"
                                data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if (session('success'))
                            <div class="m-6 mb-0">
                                <div class="alert alert-success bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 p-4 rounded-xl shadow-sm d-flex align-items-center">
                                    <i class="fas fa-check-circle text-2xl mr-3"></i>
                                    <span class="font-medium">{{ session('success') }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-8 py-6 border-b border-gray-200">
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <a href="{{ route('library.create') }}"
                                        class="btn bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-6 py-3 rounded-xl shadow-lg transition duration-300 transform hover:scale-105 hover:shadow-xl border-0">
                                        <i class="fas fa-upload mr-2"></i>
                                        <span class="font-semibold">Unggah Dokumen Baru</span>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="projectDropdown" class="block text-gray-700 font-semibold mb-2 text-sm">
                                            <i class="fas fa-filter mr-1 text-blue-600"></i>Filter Kategori
                                        </label>
                                        <select id="projectDropdown" class="form-control form-control-lg border-2 border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition">
                                            <option value="">Semua Kategori</option>
                                            @foreach ($libraryProjects as $proj)
                                                <option value="{{ $proj->slug }}">
                                                {{ $proj->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="projectTables" class="p-4 md:p-8">
                            @forelse($files as $projectTitle => $projectFiles)
                                @php
                                    $slug = $projectTitle === 'Tanpa Kategori' ? 'tanpa-kategori' : Str::slug($projectTitle);
                                @endphp
                                <div class="project-table bg-white rounded-2xl shadow-md p-4 md:p-6 mb-6 border border-gray-100" id="table-{{ $slug }}" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-6 pb-4 border-b-2 border-blue-100">
                                        <div>
                                            <h4 class="text-xl md:text-2xl font-bold text-gray-800 mb-1">
                                                <i class="fas fa-folder-open text-blue-600 mr-2"></i>{{ $projectTitle }}
                                            </h4>
                                            <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
                                                <i class="fas fa-file-alt mr-1"></i>{{ $projectFiles->count() }} Dokumen
                                            </span>
                                        </div>
                                    </div>

                                    <div class="table-responsive" style="overflow-x: hidden;">
                                        <table class="table table-hover w-100 dt-responsive" id="datatable-{{ $slug }}">
                                            <thead>
                                                <tr class="bg-gradient-to-r from-blue-50 to-indigo-50">
                                                    <th class="text-center py-4 px-2 font-bold text-gray-700 text-sm align-middle" style="width: 5%;">
                                                        <i class="fas fa-hashtag text-blue-600"></i>
                                                    </th>
                                                    <th class="py-4 px-2 font-bold text-gray-700 text-sm align-middle" style="width: 35%;">
                                                        <i class="fas fa-file-signature text-blue-600 mr-2"></i>Nama Dokumen
                                                    </th>
                                                    <th class="py-4 px-2 font-bold text-gray-700 text-sm align-middle" style="width: 15%;">
                                                        <i class="fas fa-barcode text-blue-600 mr-2"></i>Nomor Dokumen
                                                    </th>
                                                    <th class="py-4 px-2 font-bold text-gray-700 text-sm align-middle" style="width: 20%;">
                                                        <i class="fas fa-paperclip text-blue-600 mr-2"></i>File
                                                    </th>
                                                    <th class="py-4 px-2 font-bold text-gray-700 text-sm align-middle" style="width: 15%;">
                                                        <i class="fas fa-link text-blue-600 mr-2"></i>Link
                                                    </th>
                                                    <th class="text-center py-4 px-2 font-bold text-gray-700 text-sm align-middle" style="width: 10%;">
                                                        <i class="fas fa-cog text-blue-600 mr-2"></i>Aksi
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($projectFiles as $index => $file)
                                                    <tr class="border-b border-gray-100 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition duration-200">
                                                        <td class="text-center py-3 px-2 align-middle">
                                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold text-sm">
                                                                {{ $index + 1 }}
                                                            </span>
                                                        </td>
                                                        <td class="py-3 px-2 align-middle break-word-force">
                                                            <span class="font-semibold text-gray-900 leading-snug block">
                                                                {{ $file->file_name }}
                                                            </span>
                                                        </td>
                                                        <td class="py-3 px-2 align-middle">
                                                            <span class="inline-block bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs md:text-sm font-medium">
                                                                {{ $file->file_code }}
                                                            </span>
                                                        </td>
                                                        <td class="py-3 px-2 align-middle">
                                                            @if ($file->files->count() > 0)
                                                                <div class="space-y-2">
                                                                    @foreach ($file->files as $f)
                                                                        @include('library.fileinfo', ['file' => $f])
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <span class="text-gray-400 text-sm italic">
                                                                    <i class="fas fa-minus-circle mr-1"></i>Nihil
                                                                </span>
                                                            @endif
                                                        </td>
                                                         </td>
                                                           <td class="py-4">
                                                            @if ($file->file_link)
                                                                <a href="{{ $file->file_link }}" target="_blank"
                                                                    class="text-blue-600 hover:underline text-sm">
                                                                    {{ Str::limit($file->file_link, 50) }}
                                                                </a>
                                                            @else
                                                                <span class="text-gray-400 text-sm">Tidak ada link</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center py-3 px-2 align-middle">
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <a href="{{ route('library.edit', $file->id) }}"
                                                                    class="btn btn-sm bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 md:px-3 md:py-2 rounded-lg font-medium shadow-sm transition transform hover:scale-105 border-0"
                                                                    data-toggle="tooltip" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                
                                                                <form action="{{ route('library.destroy', $file->id) }}"
                                                                    method="POST" class="d-inline"
                                                                    onsubmit="return confirm('⚠️ Yakin ingin menghapus dokumen ini?');">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-sm bg-red-600 hover:bg-red-700 text-white px-2 py-1 md:px-3 md:py-2 rounded-lg font-medium shadow-sm transition transform hover:scale-105 border-0"
                                                                        data-toggle="tooltip" title="Hapus">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-20">
                                    <div class="mb-6">
                                        <i class="fas fa-folder-open text-9xl text-gray-300"></i>
                                    </div>
                                    <h4 class="text-2xl font-bold text-gray-700 mb-2">Belum Ada Dokumen</h4>
                                    <p class="text-gray-500 mb-6">Mulai unggah dokumen pertama Anda untuk membangun library</p>
                                    <a href="{{ route('library.create') }}"
                                        class="btn bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-xl shadow-lg hover:shadow-xl transition border-0">
                                        <i class="fas fa-plus-circle mr-2"></i>Unggah Dokumen Pertama
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        /* CSS KHUSUS UNTUK RESPONSIVE */
        
        /* Memaksa text turun ke bawah */
        .break-word-force {
            white-space: normal !important;
            word-wrap: break-word;
            word-break: break-word;
            min-width: 150px;
        }

        /* Memaksa link panjang agar tidak merusak layout */
        .break-all {
            word-break: break-all;
        }

        .project-table {
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(10px);
        }

        .project-table.show {
            display: block !important;
            opacity: 1;
            transform: translateY(0);
        }

        /* Styling Search Input agar rapi */
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
            width: 100%;
            max-width: 250px;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        /* Pagination Styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem;
            margin: 0 2px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(to right, #3b82f6, #6366f1) !important;
            border: none !important;
            color: white !important;
        }

        .gap-2 { gap: 0.5rem; }
        
        /* Responsive Table Overrides */
        table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before {
            background-color: #3b82f6;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            @foreach ($files as $projectTitle => $projectFiles)
                @php
                    $slug = $projectTitle === 'Tanpa Kategori' ? 'tanpa-kategori' : Str::slug($projectTitle);
                @endphp
                if ($('#datatable-{{ $slug }}').length) {
                    $('#datatable-{{ $slug }}').DataTable({
                        paging: true,
                        lengthChange: false,
                        searching: true,
                        ordering: true,
                        info: true,
                        autoWidth: false, 
                        responsive: true, 
                        pageLength: 10,
                        columnDefs: [
                            
                            { responsivePriority: 1, targets: 0 }, 
                            { responsivePriority: 1, targets: 1 }, 
                            { responsivePriority: 2, targets: -1 }, 
                            { responsivePriority: 3, targets: 2 }, 
                            { responsivePriority: 4, targets: 3 }, 
                            { responsivePriority: 5, targets: 4 }, 
                        ],
                        language: {
                            search: "",
                            searchPlaceholder: "Cari dokumen...",
                            paginate: {
                                previous: "‹",
                                next: "›"
                            },
                            info: "Hal _PAGE_ dari _PAGES_",
                            infoEmpty: "Tidak ada data",
                            emptyTable: "Data kosong",
                            zeroRecords: "Tidak ditemukan"
                        }
                    });
                }
            @endforeach

            // Dropdown change handler
            $('#projectDropdown').on('change', function() {
                const selected = $(this).val();
                
                $('.project-table').fadeOut(200, function() {
                    $(this).removeClass('show');
                });

                setTimeout(function() {
                    if (selected) {
                        $('#table-' + selected).fadeIn(300).addClass('show');
                        // Redraw table agar responsive calculation ulang saat muncul
                        $('#table-' + selected).find('table').DataTable().columns.adjust().responsive.recalc();
                    } else {
                        $('.project-table').fadeIn(300).addClass('show');
                        // Redraw semua table
                        $('.project-table').find('table').DataTable().columns.adjust().responsive.recalc();
                    }
                }, 250);
            });

            // Show first project
            @if ($files->count() > 0)
                @php
                    $firstTitle = $files->keys()->first();
                    $firstSlug = $firstTitle === 'Tanpa Kategori' ? 'tanpa-kategori' : Str::slug($firstTitle);
                @endphp
                const firstSlug = '{{ $firstSlug }}';
                if (firstSlug) {
                    $('#projectDropdown').val(firstSlug);
                    setTimeout(function() {
                        $('#table-' + firstSlug).fadeIn(400).addClass('show');
                        $('#table-' + firstSlug).find('table').DataTable().columns.adjust().responsive.recalc();
                    }, 100);
                }
            @endif
        });
    </script>
@endpush