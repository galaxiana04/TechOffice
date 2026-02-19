@extends('layouts.universal')

@section('container2')
    <div class="content-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.8rem;">Bill of Material</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('newbom.index') }}"
                                class="text-danger font-weight-bold">Home</a></li>
                        <li class="breadcrumb-item active">Monitoring</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container-fluid mb-5">
        <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div
                class="card-header border-0 py-3 d-flex align-items-center justify-content-between bg-modern-red text-white">
                <h3 class="card-title font-weight-bold mb-0" style="font-size: 1.2rem; letter-spacing: 0.5px;">
                    <i class="fas fa-layer-group mr-2"></i> BOM Monitoring Data
                </h3>
                <div class="card-tools ml-auto">
                    <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool text-white" data-card-widget="maximize">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
            </div>

            <div class="card-body p-4 bg-white">
                <div class="row mb-4 align-items-end">
                    <div class="col-md-4 col-sm-12 mb-3 mb-md-0">
                        <label class="text-muted font-weight-bold small text-uppercase mb-2">Filter Proyek</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0 rounded-left-pill pl-3">
                                    <i class="fas fa-filter text-danger"></i>
                                </span>
                            </div>
                            <select id="projectTypeDropdown"
                                class="form-control custom-select border-left-0 rounded-right-pill shadow-sm"
                                style="height: 45px;">
                                <option value="">Semua Proyek</option>
                                @foreach ($projects as $index => $project)
                                    <option value="{{ $project->id }}" {{ $index === 0 ? 'selected' : '' }}>
                                        {{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if (in_array($authuser->rule, ['superuser', 'MTPR']))
                        <div class="col-md-8 col-sm-12 text-md-right">
                            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                <button type="button"
                                    class="btn btn-outline-danger btn-modern rounded-pill px-4 mr-2 mb-2 shadow-sm"
                                    onclick="handleDeleteMultipleItems()">
                                    <i class="fas fa-trash-alt mr-2"></i>Hapus
                                </button>
                                <a href="{{ url('newboms/uploadexcel') }}"
                                    class="btn btn-danger bg-modern-red border-0 btn-modern rounded-pill px-4 mr-2 mb-2 shadow-lg">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload BOM
                                </a>
                                <a href="{{ url('newboms/search') }}"
                                    class="btn btn-danger bg-modern-red border-0 btn-modern rounded-pill px-4 mr-2 mb-2 shadow-lg">
                                    <i class="fas fa-cloud-upload-alt mr-2"></i>Cari Komat
                                </a>
                                <a href="{{ url('newboms/logpercentage') }}"
                                    class="btn btn-success btn-modern rounded-pill px-4 mb-2 shadow-sm"
                                    style="background: #00b09b; background: linear-gradient(to right, #00b09b, #96c93d); border:none;">
                                    <i class="fas fa-history mr-2"></i>History
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="table-responsive rounded-lg">
                    <table id="newbomTable" class="table table-hover w-100 custom-table">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="checkAll">
                                        <label class="custom-control-label" for="checkAll"></label>
                                    </div>
                                </th>
                                <th width="5%">No</th>
                                <th>Nomor BOM</th>
                                <th>Tipe Proyek</th>
                                <th>Unit</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        /* Modern Red Gradient Definition */
        .bg-modern-red {
            background: linear-gradient(135deg, #e72a3a 0%, #c41022 100%) !important;
        }

        /* Card Styling */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Dropdown Styling */
        .custom-select:focus {
            box-shadow: none;
            border-color: #e72a3a;
        }

        /* Button Modern Effects */
        .btn-modern {
            transition: all 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            filter: brightness(110%);
        }

        /* Table Styling */
        .custom-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-table thead th {
            background-color: #f8f9fa;
            color: #444;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            border-bottom: 2px solid #e72a3a;
            padding: 15px;
            vertical-align: middle;
        }

        .custom-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #fff5f6 !important;
            /* Very light pink/red tint on hover */
            transform: scale(1.001);
        }

        .custom-table td {
            vertical-align: middle;
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #555;
            font-size: 0.95rem;
        }

        /* DataTables Customization */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #e72a3a 0%, #c41022 100%) !important;
            border: none !important;
            color: white !important;
            border-radius: 50px !important;
            box-shadow: 0 4px 6px rgba(231, 42, 58, 0.3);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #eee !important;
            border: none !important;
            color: #333 !important;
            border-radius: 50px !important;
        }

        /* Custom Checkbox Red */
        .custom-control-input:checked~.custom-control-label::before {
            border-color: #e72a3a;
            background-color: #e72a3a;
        }

        /* Badge/Select Styling in Table */
        table.dataTable.no-footer {
            border-bottom: 1px solid #eee;
        }

        /* Helper for gap */
        .gap-2 {
            gap: 0.5rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {
            let loadingSwal;

            // Initialize DataTable with Modern DOM positioning
            var table = $('#newbomTable').DataTable({
                processing: true,
                serverSide: true,
                dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
                ajax: {
                    url: "{{ route('newbom.data') }}",
                    data: function(d) {
                        d.project_type_id = $('#projectTypeDropdown').val();
                    },
                    beforeSend: function() {
                        // Tidak perlu menampilkan loading swal tiap draw agar UX lebih smooth
                        // Jika ingin loading baris, gunakan processing indicator datatable
                    },
                    error: function(xhr, error, code) {
                        console.log(xhr);
                    }
                },
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center font-weight-bold'
                    },
                    {
                        data: 'BOMnumber',
                        name: 'BOMnumber',
                        className: 'text-danger font-weight-bold'
                    },
                    {
                        data: 'project_type',
                        name: 'projectType.title'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                responsive: true,
                autoWidth: false,
                language: {
                    processing: '<div class="spinner-border text-danger" role="status"><span class="sr-only">Loading...</span></div>',
                    search: "_INPUT_",
                    searchPlaceholder: "Cari Data BOM...",
                    paginate: {
                        next: '<i class="fas fa-chevron-right"></i>',
                        previous: '<i class="fas fa-chevron-left"></i>'
                    }
                },
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                }
            });

            // Styling Search Box DataTables secara Manual
            $('.dataTables_filter input').addClass('form-control rounded-pill border-0 bg-light pl-4').css('width',
                '250px');

            // Reload table on dropdown change
            $('#projectTypeDropdown').change(function() {
                table.draw(); // Menggunakan draw() lebih smooth daripada ajax.reload() full
            });

            // Handle Check All dengan event delegasi yang lebih aman
            $(document).on('change', '#checkAll', function() {
                var isChecked = $(this).is(':checked');
                $('input[name="document_ids[]"]').prop('checked', isChecked);
            });

            // Handle individual checkbox to update Check All state
            $(document).on('change', 'input[name="document_ids[]"]', function() {
                if (false == $(this).prop("checked")) {
                    $("#checkAll").prop('checked', false);
                }
                if ($('input[name="document_ids[]"]:checked').length == $('input[name="document_ids[]"]')
                    .length) {
                    $("#checkAll").prop('checked', true);
                }
            });

            window.handleDeleteMultipleItems = function() {
                var selectedDocumentIds = [];
                $('input[name="document_ids[]"]:checked').each(function() {
                    selectedDocumentIds.push($(this).val());
                });

                if (selectedDocumentIds.length === 0) {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Pilih minimal satu data untuk dihapus.',
                        icon: 'warning',
                        confirmButtonColor: '#e72a3a',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e72a3a',
                    cancelButtonColor: '#adb5bd',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    background: '#fff',
                    customClass: {
                        popup: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            html: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });

                        $.ajax({
                            url: "{{ route('newbom.delete.multiple') }}",
                            type: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                document_ids: selectedDocumentIds
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Data BOM berhasil dihapus.',
                                    icon: 'success',
                                    confirmButtonColor: '#e72a3a'
                                });
                                table.ajax.reload();
                                $("#checkAll").prop('checked', false);
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
                            }
                        });
                    }
                });
            };
        });
    </script>
@endpush
