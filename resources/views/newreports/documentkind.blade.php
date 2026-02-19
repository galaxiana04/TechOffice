@extends('layouts.universal')

@php
    use Carbon\Carbon;
@endphp

@section('container2')
    <div class="content-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.75rem;">Manajemen Dokumen</h1>
                    <p class="text-muted small mb-0">Kelola kategori dan jenis dokumen laporan</p>
                </div>
                <div class="col-sm-6 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('newreports.index') }}" class="text-muted hover-primary">
                                <i class="fas fa-home mr-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-primary font-weight-bold">
                            Jenis Dokumen
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <style>
        /* --- MODERN & TAILWIND-INSPIRED STYLES --- */

        /* Card Layout */
        .card-modern {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            background: #fff;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        /* --- PERUBAHAN WARNA HEADER --- */
        .header-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            padding: 1.5rem;
            color: white;
        }

        /* Form Section */
        .form-section {
            background-color: #f9fafb;
            /* bg-gray-50 */
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #e5e7eb;
        }

        .form-label-modern {
            color: #374151;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control-modern {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.625rem 1rem;
            height: auto;
            font-size: 0.95rem;
            transition: all 0.2s;
            width: 100%;
            background-color: #fff;
        }

        /* Ubah warna fokus input juga agar senada */
        .form-control-modern:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15);
            outline: none;
        }

        /* Table Styling */
        .table-modern thead th {
            border-top: none;
            border-bottom: 2px solid #e5e7eb;
            background-color: #f9fafb;
            color: #4b5563;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
        }

        .table-modern tbody td {
            vertical-align: middle;
            padding: 1rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.9rem;
            color: #1f2937;
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .table-modern tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Badges */
        .badge-soft-success {
            background-color: #ecfdf5;
            color: #047857;
            padding: 0.35em 0.65em;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.75rem;
            border: 1px solid #d1fae5;
        }

        /* --- warna tombol simpan --- */
        .btn-gradient {
            background: linear-gradient(to right, #3b82f6, #2563eb);
            border: none;
            color: white !important;
            border-radius: 0.5rem;
            padding: 0.625rem 1.5rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.5);
            transition: all 0.2s;
            display: block;
            width: 100%;
            text-align: center;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.6);
            filter: brightness(110%);
        }

        .form-control-modern:focus {
            border-color: #3b82f6;
            /* Biru */
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
            outline: none;
        }


        .btn-secondary-modern {
            background-color: #e5e7eb;
            color: #374151;
            border: none;
            border-radius: 0.375rem;
            padding: 0 15px;
            transition: all 0.2s;
        }

        .btn-secondary-modern:hover {
            background-color: #d1d5db;
        }

        /* Icon Container */
        .icon-circle-bg {
            background-color: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }


        /* Wadah Input Select2 */
        .select2-container--bootstrap .select2-selection--single {
            height: 45px !important;
            /* Samakan tinggi dengan form-control-modern */
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
            padding: 8px 12px !important;
            background-color: #fff;
        }

        /* Panah Dropdown */
        .select2-container--bootstrap .select2-selection--single .select2-selection__arrow {
            height: 43px !important;
            right: 10px !important;
        }

        /* Text Rendered */
        .select2-container--bootstrap .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #374151 !important;
            padding-left: 0 !important;
        }

        /* Fokus State (Merah) */
        .select2-container--bootstrap.select2-container--focus .select2-selection--single {
            border-color: #dc2626 !important;
            /* Merah */
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.15) !important;
        }

        /* Dropdown Options Container (Agar bisa di-scroll) */
        .select2-container--bootstrap .select2-dropdown {
            border-color: #dc2626;
            /* Merah */
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Scrollable Area */
        .select2-results__options {
            max-height: 250px !important;
            /* Batasi tinggi agar scroll muncul */
            overflow-y: auto !important;
            padding: 5px;
        }

        /* Option Item */
        .select2-container--bootstrap .select2-results__option {
            padding: 8px 12px;
            font-size: 0.9rem;
            border-radius: 0.375rem;
            margin-bottom: 2px;
        }

        /* Selected / Highlighted Option (Merah) */
        .select2-container--bootstrap .select2-results__option--highlighted[aria-selected] {
            background-color: #dc2626 !important;
            /* Warna Merah modern */
            color: white !important;
        }

        /* Fix Select2 di dalam Input Group (Tabel) */
        .input-group-sm .select2-container--bootstrap .select2-selection--single {
            height: 31px !important;
            padding: 4px 10px !important;
            border-radius: 0.25rem 0 0 0.25rem !important;
            /* Radius kiri saja */
            font-size: 0.875rem;
        }

        .input-group-sm .select2-selection__rendered {
            line-height: 20px !important;
        }

        .input-group-sm .select2-selection__arrow {
            height: 29px !important;
        }
    </style>

    <div class="container-fluid pb-5">
        <div class="card card-modern">

            {{-- Header Gradient --}}
            <div class="header-gradient d-flex align-items-center">
                <div class="icon-circle-bg">
                    <i class="fas fa-folder-open text-white"></i>
                </div>
                <div>
                    <h5 class="font-weight-bold m-0 text-white" style="font-size: 1.1rem;">Daftar Dokumen</h5>
                    <p class="m-0 text-blue-100 small opacity-75">Manajemen Kategori Dokumen Laporan</p>
                </div>
            </div>

            <div class="card-body p-4">

                {{-- Alert Notification --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center"
                        role="alert" style="border-radius: 0.75rem; background-color: #ecfdf5; border-left: 5px solid #10b981;">
                        <i class="fas fa-check-circle fa-lg mr-3 text-success"></i>
                        <div class="text-success">
                            <strong>Berhasil!</strong> {{ session('success') }}
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                {{-- Section: Form Tambah --}}
                <div class="form-section">
                    <h6 class="text-dark font-weight-bold mb-4 d-flex align-items-center">
                        <i class="fas fa-plus-circle text-primary mr-2"></i> Tambah Jenis Dokumen Baru
                    </h6>

                    <form action="{{ route('newprogressreports.document-kindstore') }}" method="POST">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                <label for="name" class="form-label-modern">Nama Dokumen</label>
                                <input type="text" id="name" name="name" class="form-control form-control-modern"
                                    placeholder="Contoh: Welding Procedure" required>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                <label for="unit_id" class="form-label-modern">Unit Teknologi</label>
                                {{-- Gunakan class select2 untuk mengaktifkan fitur scroll & search --}}
                                <select id="unit_id" name="unit_id" class="form-control select2" required
                                    style="width: 100%;">
                                    <option value="" disabled selected>-- Pilih Unit --</option>
                                    @foreach ($techUnits as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-4 col-md-6 mb-3 mb-md-0">
                                <label for="description" class="form-label-modern">Deskripsi</label>
                                <input type="text" id="description" name="description"
                                    class="form-control form-control-modern" placeholder="Keterangan tambahan (Opsional)">
                            </div>

                            <div class="col-lg-2 col-md-6">
                                <button type="submit" class="btn btn-gradient">
                                    <i class="fas fa-save mr-2"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Section: Tabel Data --}}
                <div class="table-responsive">
                    <table id="documentKindTable" class="table table-modern w-100 text-nowrap">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>Nama Jenis Dokumen</th>
                                <th style="width: 30%;">Unit Teknologi</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documentKinds as $index => $documentKind)
                                <tr>
                                    <td class="text-center text-muted font-weight-bold">{{ $index + 1 }}</td>
                                    <td class="font-weight-bold text-dark">
                                        {{ $documentKind->name }}
                                    </td>
                                    <td>
                                        @if ($documentKind->unit)
                                            <span class="badge badge-soft-success">
                                                <i class="fas fa-layer-group mr-1"></i> {{ $documentKind->unit->name }}
                                            </span>
                                        @else
                                            {{-- Form Assign Unit Compact dengan Select2 --}}
                                            <form
                                                action="{{ route('newprogressreports.document-kind-assign-unit', $documentKind->id) }}"
                                                method="POST" class="d-flex align-items-center">
                                                @csrf
                                                <div class="input-group input-group-sm" style="max-width: 280px;">
                                                    {{-- Select2 kecil di dalam tabel --}}
                                                    <select name="unit_id" class="form-control select2-table" required
                                                        style="width: 80%;">
                                                        <option value="" disabled selected>Pilih Unit...</option>
                                                        @foreach ($techUnits as $unit)
                                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button type="submit" class="btn btn-secondary-modern" title="Simpan Unit"
                                                            style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        @if($documentKind->description)
                                            {{Str::limit($documentKind->description, 50)}}
                                        @else
                                            <span class="text-muted font-italic opacity-75">- Tidak ada deskripsi -</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('css')
    {{-- Select2 CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
@endpush

@push('scripts')
    {{-- Select2 JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function () {
            // 1. Inisialisasi Select2 pada Form Utama
            $('.select2').select2({
                theme: 'bootstrap',
                placeholder: '-- Pilih Unit --',
                allowClear: true,
                width: '100%'
            });

            // 2. Inisialisasi Select2 Kecil pada Tabel (jika ada)
            $('.select2-table').select2({
                theme: 'bootstrap',
                placeholder: 'Unit...',
                width: 'resolve',
                dropdownAutoWidth: true
            });

            // 3. Konfigurasi DataTable
            var table = $('#documentKindTable').DataTable({
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                    search: "",
                    searchPlaceholder: "Cari dokumen...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                },
                dom: '<"row mb-3 align-items-center"<"col-md-6"l><"col-md-6"f>>rt<"row mt-4"<"col-md-6"i><"col-md-6"p>>',
                drawCallback: function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm justify-content-end');
                    $('.dataTables_filter input').addClass('form-control form-control-sm').css('border-radius', '20px').css('padding-left', '15px');

                    // Re-init select2 jika tabel di-redraw (pagination/search)
                    $('.select2-table').select2({
                        theme: 'bootstrap',
                        placeholder: 'Unit...',
                        width: 'resolve'
                    });
                }
            });
        });
    </script>
@endpush