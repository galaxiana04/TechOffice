@extends('layouts.universal')

@section('container2')
    <div class="content-header py-3 py-md-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6 col-12 mb-2 mb-sm-0">
                    <h1 class="m-0 text-dark font-weight-bold header-title">Manajemen Notifikasi</h1>
                    <p class="text-muted small mb-0">Kelola notifikasi harian untuk setiap unit</p>
                </div>
                <div class="col-sm-6 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('newreports.index') }}" class="text-muted hover-primary">
                                <i class="fas fa-home mr-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-danger font-weight-bold">Notifikasi Unit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <style>
        /* --- TEMA MERAH MODERN --- */

        /* Card Layout */
        .card-modern {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            background: #fff;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        /* Header Gradient (Merah) */
        .header-gradient {
            /* Gradien Merah Modern */
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            padding: 1.5rem;
            color: white;
        }

        /* Form Section */
        .form-section {
            background-color: #fff1f2; /* Merah sangat muda */
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #fecaca;
        }

        .form-label-modern {
            color: #7f1d1d; /* Merah gelap */
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
        
        /* Fokus Input (Merah) */
        .form-control-modern:focus {
            border-color: #ef4444; 
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
            outline: none;
        }

        /* Table Styling */
        .table-modern thead th {
            border-top: none;
            border-bottom: 2px solid #fee2e2; /* Garis merah muda */
            background-color: #fff1f2; /* Latar header merah muda */
            color: #991b1b;
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

        .table-modern tbody tr:hover {
            background-color: #fffafa; /* Hover sangat muda */
        }

        /* Select2 Customization (Merah) */
        .select2-container--bootstrap .select2-selection--multiple,
        .select2-container--bootstrap .select2-selection--single {
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
        }

        .select2-container--bootstrap.select2-container--focus .select2-selection--multiple,
        .select2-container--bootstrap.select2-container--focus .select2-selection--single {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
        }

        /* Chips pada Multiple Select (Merah) */
        .select2-container--bootstrap .select2-selection--multiple .select2-selection__choice {
            background-color: #fee2e2 !important;
            border: 1px solid #fca5a5 !important;
            color: #b91c1c !important;
        }
        
        .select2-container--bootstrap .select2-selection--multiple .select2-selection__choice__remove {
            color: #b91c1c !important;
        }

        .btn-gradient-blue {
            background: linear-gradient(to right, #3b82f6, #2563eb); 
            border: none;
            color: white !important;
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.25);
            transition: all 0.2s;
        }
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(220, 38, 38, 0.4);
            filter: brightness(110%);
        }

        /* Badges */
        .badge-soft-info {
            background-color: #eff6ff; 
            padding: 0.35em 0.65em;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.75rem;
            border: 1px solid #dbeafe;
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
    </style>

    <div class="container-fluid pb-5">

        {{-- Alert Notification --}}
        @if(session('success'))
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

        <div class="card card-modern">
            {{-- Header Gradient Merah --}}
            <div class="header-gradient d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between p-3 p-sm-4">

                <div class="d-flex align-items-center mb-3 mb-sm-0">
                    <div class="icon-circle-bg"><i class="fas fa-bell text-white"></i></div>
                    <div>
                        <h5 class="font-weight-bold m-0 text-white" style="font-size: 1.1rem;">Manajemen Notifikasi</h5>
                        <p class="m-0 text-white-50 small">Daftar & Konfigurasi Notifikasi Unit</p>
                    </div>
                </div>

                {{-- TOMBOL TOGGLE UKURAN COMPACT --}}
                <button
                    class="btn btn-sm btn-light text-danger font-weight-bold shadow-none px-3 d-inline-flex align-items-center"
                    type="button" data-toggle="collapse" data-target="#collapseForm" aria-expanded="true"
                    aria-controls="collapseForm">
                    <i class="fas fa-minus fa-fw mr-1" id="icon-toggle"></i>
                    <span>Form Tambah</span>
                </button>
            </div>

            <div class="card-body p-3 p-sm-4">

                {{-- FORM SECTION (Default Tampil) --}}
                <div class="collapse show" id="collapseForm">
                    <div class="form-section">
                        <h6 class="text-dark font-weight-bold mb-4 d-flex align-items-center">
                            <i class="fas fa-plus-circle text-danger mr-2"></i> Tambah Notifikasi Baru
                        </h6>

                        <form action="{{ route('newprogressreports.store-notif-harian-units') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
                                    <label class="form-label-modern">Judul Notifikasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-modern" name="title"
                                        placeholder="Contoh: Produksi Finishing" required>
                                </div>

                                <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
                                    <label class="form-label-modern">Pilih Jenis Dokumen <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="documentkind[]" multiple="multiple"
                                        style="width: 100%;" required>
                                        @foreach ($documentKinds as $documentKind)
                                            <option value="{{ $documentKind->id }}">{{ $documentKind->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
                                    <label class="form-label-modern">Akun Telegram</label>
                                    <select class="form-control select2" name="telegrammessagesaccount_id"
                                        style="width: 100%;">
                                        <option value="">-- Tidak Ada --</option>
                                        @foreach ($telegrammessagesaccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->account }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-right">
                                    <button type="submit" class="btn btn-gradient-blue ">
                                    <i class="fas fa-save mr-2"></i> Simpan Data
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TABLE SECTION --}}
                <div class="table-responsive">
                    <table id="table-notif" class="table table-modern w-100">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%">No</th>
                                <th style="width: 25%">Title</th>
                                <th style="width: 35%">Document Kinds</th>
                                <th style="width: 20%">Telegram Account</th>
                                <th class="text-center" style="width: 15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($notifHarianUnits as $unit)
                                <tr>
                                    <td class="text-center text-muted font-weight-bold">{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold text-dark">{{ $unit->title }}</td>
                                    <td>
                                        @foreach($unit->documentkind_names as $name)
                                            <div class="mb-1">
                                                <span class="badge badge-soft-info"
                                                    style="display: inline-block; white-space: normal; text-align: left;">
                                                    {{ $name }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($unit->telegrammessagesaccount)
                                            <div class="d-flex align-items-center text-primary font-weight-bold">
                                                <i class="fab fa-telegram fa-lg mr-2 text-info"></i>
                                                <span class="text-dark">{{ $unit->telegrammessagesaccount->account }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted small font-italic opacity-75">- Not Set -</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <div class="btn-group shadow-sm" role="group">
                                                <a href="{{ route('newprogressreports.edit-notif-harian-unit', $unit->id) }}"
                                                    class="btn btn-sm btn-white border text-warning" title="Edit"
                                                    style="border-radius: 0.375rem 0 0 0.375rem;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form
                                                    action="{{ route('newprogressreports.delete-notif-harian-unit', $unit->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-white border text-danger"
                                                        onclick="return confirm('Hapus data?')" title="Delete"
                                                        style="border-radius: 0 0.375rem 0.375rem 0;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function () {
            // Select2 dengan tema bootstrap
            $('.select2').select2({ theme: 'bootstrap' });

            // Datatable Configuration
            $('#table-notif').DataTable({
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
                    searchPlaceholder: "Cari data...",
                },
                dom: '<"row mb-3 align-items-center"<"col-md-6"l><"col-md-6"f>>rt<"row mt-4"<"col-md-6"i><"col-md-6"p>>',
                drawCallback: function () {
                    $('.dataTables_paginate > .pagination').addClass('pagination-sm justify-content-end');
                    $('.dataTables_filter input').addClass('form-control form-control-sm').css('border-radius', '20px').css('padding-left', '15px');
                }
            });

            // Toggle Icon Logic
            $('#collapseForm').on('hide.bs.collapse', function () {
                $('#icon-toggle').removeClass('fa-minus').addClass('fa-plus');
            });
            $('#collapseForm').on('show.bs.collapse', function () {
                $('#icon-toggle').removeClass('fa-plus').addClass('fa-minus');
            });
        });
    </script>
@endpush