@extends('layouts.universal')

@php
    $authuser = auth()->user();

    // Mapping operator DB → label tab
    $operatorTabs = [
        ['label' => 'PE', 'operator' => 'Product Engineering'],
        ['label' => 'DE', 'operator' => 'Desain Elektrik'],
        ['label' => 'DC', 'operator' => 'Desain Carbody'],
        ['label' => 'DMI', 'operator' => 'Desain Mekanik & Interior'],
        ['label' => 'SD', 'operator' => 'Shop Drawing'],
        ['label' => 'TP', 'operator' => 'Teknologi Proses'],
        ['label' => 'DBW', 'operator' => 'Desain Bogie & Wagon'],
        ['label' => 'PANDS', 'operator' => 'Preparation & Support'],
        ['label' => 'WT', 'operator' => 'Welding Technology'],
    ];
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('new-memo.index') }}">List Memo</a></li>
                        <li class="breadcrumb-item active">Memo Tertutup</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')

    <div class="card card-danger card-outline">
        <div class="card-header">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <h3 class="card-title text-bold">Page monitoring memo <span class="badge badge-info ml-1"></span></h3>
        </div>
        <div class="card-body">

            {{-- ===== PILIH PROJECT ===== --}}
            <div class="form-group">
                <label class="font-weight-bold text-secondary">
                    <i class="fas fa-project-diagram mr-1"></i> Pilih Project :
                </label>
                <select class="form-control" id="projectSelect" onchange="filterByProject(this.value)">
                    <option value="">All</option>
                    @foreach ($listproject as $project)
                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                    @endforeach
                </select>
            </div>

            {{-- ===== DOWNLOAD REPORT + TOMBOL AKSI ===== --}}
            <div class="row mb-3">
                <div class="col-lg-4 col-md-5 col-sm-12 mb-2">
                    <form action="{{ route('new-memo.downloadall') }}" method="GET">
                        <div class="input-group">
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

            {{-- ===== TAB FILTER ===== --}}
            <ul class="nav nav-tabs nav-tabs-red mb-2" id="operatorTabs">

                <li class="nav-item">
                    <a class="nav-link active" href="#" onclick="filterByOperator(this, '')">
                        <i class="fas fa-th-large mr-1"></i>All
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="filterByOperator(this, 'unbound')">
                        <i class="fas fa-unlink mr-1"></i>Unbound
                    </a>
                </li>
                @foreach ($operatorTabs as $tab)
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-operator="{{ $tab['operator'] }}"
                            onclick="filterByOperator(this, '{{ $tab['operator'] }}')">
                            <i class="fas fa-folder mr-1"></i>{{ $tab['label'] }}
                        </a>
                    </li>
                @endforeach

            </ul>
            {{-- ===== END TAB FILTER ===== --}}

            <table id="documents-table" class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th><span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span></th>
                        <th>No</th>
                        <th>Deadline</th>
                        <th>Nomor Dokumen</th>
                        <th>Nama Dokumen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data dimuat via AJAX -->
                </tbody>
            </table>

        </div>
    </div>

    <style>
        /* TAB FILTER MERAH */
        .nav-tabs-red {
            border-bottom: 2px solid #dee2e6;
            flex-wrap: wrap;
            gap: 2px;
        }

        .nav-tabs-red .nav-item {
            margin-bottom: -2px;
        }

        .nav-tabs-red .nav-link {
            color: #c0392b;
            font-size: 13px;
            font-weight: 500;
            padding: 7px 14px;
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
            background: transparent;
            white-space: nowrap;
            transition: all 0.15s;
        }

        .nav-tabs-red .nav-link i {
            color: #c0392b;
        }

        .nav-tabs-red .nav-link:hover {
            background: #fdf0f0;
            color: #922b21;
            border-bottom: 3px solid #e57373;
        }

        .nav-tabs-red .nav-link.active {
            color: #922b21;
            font-weight: 700;
            background: #fdf0f0;
            border-bottom: 3px solid #c0392b;
        }
    </style>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        var currentOperator = '';
        var currentProject = '';
        var dataTable;

        $(function () {
            dataTable = $('#documents-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("new-memo.indextertutup") }}',
                    data: function (d) {
                        if (currentOperator !== '') d.operator = currentOperator;
                        if (currentProject !== '') d.project = currentProject;
                    }
                },
                columns: [
                    { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'deadline', name: 'deadline' },
                    { data: 'documentnumber', name: 'documentnumber' },
                    { data: 'documentname', name: 'documentname' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            });
        });

        function filterByOperator(el, operator) {
            event.preventDefault();
            $('#operatorTabs .nav-link').removeClass('active');
            $(el).addClass('active');
            currentOperator = operator;
            dataTable.ajax.reload(null, false);
        }

        function filterByProject(project) {
            currentProject = project;
            dataTable.ajax.reload(null, false);
        }

        function toggleDocumentStatus(button) {
            var documentId = $(button).data('document-id');
            var currentStatus = $(button).data('document-status');
            var newStatus = currentStatus === 'Terbuka' ? 'Tertutup' : 'Terbuka';

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan mengubah status dokumen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, ubah status!',
                html: `
                <label for="fileUpload" style="margin-top: 10px;">Pilih file:</label>
                <input type="file" id="fileUpload" multiple />
            `,
                preConfirm: () => {
                    const fileInput = Swal.getPopup().querySelector('#fileUpload');
                    if (fileInput && fileInput.files.length === 0) {
                        Swal.showValidationMessage('File harus dipilih');
                        return false;
                    }
                    return { files: fileInput ? fileInput.files : [] };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('status', 'Tertutup');

                    if (result.value.files && result.value.files.length > 0) {
                        $.each(result.value.files, function (index, file) {
                            formData.append('file[]', file);
                        });
                    }

                    $.ajax({
                        url: "{{ url('new-memo/show') }}/" + documentId + "/updatedocumentstatus",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            $(button).removeClass('document-status-button-' + currentStatus.toLowerCase())
                                .addClass('document-status-button-' + newStatus.toLowerCase())
                                .data('document-status', newStatus)
                                .attr('title', newStatus)
                                .find('i').removeClass()
                                .addClass(newStatus === 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle')
                                .end().find('span').text(newStatus);

                            if (newStatus === 'Terbuka') {
                                $(button).removeClass('btn-success').addClass('btn-danger');
                            } else {
                                $(button).removeClass('btn-danger').addClass('btn-success');
                            }

                            Swal.fire({
                                title: "Berhasil!",
                                text: "Status dokumen berhasil diubah, dan file telah diunggah.",
                                icon: "success"
                            });

                            dataTable.ajax.reload(null, false);
                        },
                        error: function () {
                            Swal.fire({
                                title: "Gagal!",
                                text: "Gagal mengubah status dokumen.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush