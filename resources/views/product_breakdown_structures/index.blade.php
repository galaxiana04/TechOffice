@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active text-bold">Product Breakdowns Structure</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-gradient-danger text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-project-diagram"></i> Product Breakdowns Structure Dashboard</h4>
                <button class="btn btn-light btn-sm" id="toggleColumnBtn">
                    <i class="fas fa-eye-slash"></i> Pilih Kolom
                </button>
            </div>
            <div class="card-body bg-light">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Pilih Project Type:</label>
                        <select id="projectTypeSelect" class="form-select shadow-sm">
                            @foreach ($projectTypes as $index => $type)
                            <option value="{{ $type->id }}">{{ $type->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 text-end pt-4">
                        <button type="button" class="btn btn-success shadow-sm me-2" data-toggle="modal" data-target="#uploadExcelModal">
                            <i class="fas fa-file-excel"></i> Upload Excel
                        </button>
                        <button type="button" class="btn btn-danger shadow-sm me-2" id="deleteAllBtn">
                            <i class="fas fa-trash-alt"></i> Delete All
                        </button>
                        <a href="{{ route('product-breakdown-structure.indexallreference') }}" class="btn btn-primary shadow-sm">
                            <i class="fas fa-plus"></i> All Reference
                        </a>
                    </div>
                </div>

                <div class="mb-3 card card-body border-secondary" id="columnToggles" style="display: none;">
                    <div class="d-flex flex-wrap gap-3">
                        @php
                        // Kolom sesuai format pbs.csv
                        $columns = ['No', 'project_type_id', 'level1', 'level2', 'level3', 'level4'];
                        $defaultVisible = ['No', 'project_type_id', 'level1', 'level2', 'level3', 'level4'];
                        @endphp
                        @foreach ($columns as $key => $col)
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input column-toggle" data-col="{{ $key }}" {{ in_array($col, $defaultVisible) ? 'checked' : '' }}>
                            <label class="form-check-label small">{{ $col }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="table-responsive shadow-sm rounded">
                    <table class="table table-bordered table-hover align-middle mb-0" id="allocationTable">
                        <thead class="table-dark text-center">
                            <tr>@foreach ($columns as $col) <th class="small">{{ $col }}</th> @endforeach</tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Attach --}}
<div class="modal fade" id="attachReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Hubungkan Dokumen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="attachReportForm">
                    <input type="hidden" id="reliability_allocation_id">
                    <div class="mb-3">
                        <label class="form-label">Cari Dokumen (Drawing)</label>
                        <select id="newprogressreport_id" class="form-select select2">
                            <option value="">Pilih Dokumen...</option>
                            @foreach ($newprogressreports as $report)
                            <option value="{{ $report->id }}">[{{ $report->nodokumen }}] {{ $report->namadokumen }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-primary" id="attachReportSubmit">Simpan</button></div>
        </div>
    </div>
</div>

{{-- Modal Upload Excel --}}
<div class="modal fade" id="uploadExcelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel"></i> Upload File Excel PBS</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="uploadExcelForm" action="{{ route('product-breakdown-structure.upload-excel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Project Type:</label>
                        <select name="project_type_id" class="form-select" required>
                            @foreach ($projectTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">File Excel/CSV (.xlsx, .xls, .csv):</label>
                        <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Format kolom: project_type_id, level1, level2, level3, level4</small>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian:</strong> Upload file Excel akan mengganti semua data PBS untuk project yang dipilih.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" form="uploadExcelForm" class="btn btn-success"><i class="fas fa-upload"></i> Upload</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let attachReportModal = null;
    let rowCounter = 1;

    $(document).ready(function() {
        attachReportModal = new bootstrap.Modal(document.getElementById('attachReportModal'));
        $('.select2').select2({
            dropdownParent: $('#attachReportModal'),
            width: '100%'
        });

        loadData($('#projectTypeSelect').val());

        $('#projectTypeSelect').change(function() {
            loadData($(this).val());
        });
        $('#toggleColumnBtn').click(function() {
            $('#columnToggles').slideToggle();
        });
        $('.column-toggle').change(applyColumnVisibility);

        $('#attachReportSubmit').click(function() {
            const data = {
                reliability_allocation_id: $('#reliability_allocation_id').val(),
                newprogressreport_id: $('#newprogressreport_id').val(),
                _token: '{{ csrf_token() }}'
            };
            $.post('/product-breakdown-structure/attach-report', data, function() {
                attachReportModal.hide();
                loadData($('#projectTypeSelect').val());
            });
        });

        $('#deleteAllBtn').click(function() {
            const projectId = $('#projectTypeSelect').val();
            const projectTitle = $('#projectTypeSelect option:selected').text();

            if (confirm(`Apakah Anda yakin ingin menghapus SEMUA data PBS untuk project "${projectTitle}"? Tindakan ini tidak dapat dibatalkan.`)) {
                $.post('{{ route("product-breakdown-structure.delete-all") }}', {
                    _token: '{{ csrf_token() }}',
                    project_type_id: projectId
                }, function(response) {
                    if (response.success) {
                        alert(response.message);
                        loadData(projectId);
                    } else {
                        alert('Error: ' + response.message);
                    }
                }).fail(function(xhr) {
                    alert('Gagal menghapus data. Silakan coba lagi.');
                });
            }
        });
    });

    function loadData(projectId) {
        rowCounter = 1;
        $('#tableBody').html('<tr><td colspan="100%" class="text-center">Memproses data...</td></tr>');

        $.get(`/product-breakdown-structure/data/${projectId}`, function(data) {
            $('#tableBody').empty();
            data.forEach(item => {
                const padding = (item.level_depth - 1) * 20;
                const hasChildren = data.some(child => child.parent === item.code);
                const toggle = hasChildren ?
                    `<button class="btn-toggle me-2" onclick="toggleRow('${item.code}', this)"><i class="fas fa-plus-square"></i></button>` :
                    `<span class="me-4"></span>`;

                const row = `
                        <tr data-code="${item.code}" data-parent="${item.parent}" class="pbs-row" style="${item.level_depth === 1 ? '' : 'display:none'}">
                            <td class="text-center">${rowCounter++}</td>
                            <td style="padding-left: ${padding}px; min-width: 250px;">
                                <div class="d-flex align-items-center">${toggle} <span>${item.product || '-'}</span></div>
                            </td>
                            <td class="text-center">${item.level1 || ''}</td>
                            <td class="text-center">${item.level2 || ''}</td>
                            <td class="text-center">${item.level3 || ''}</td>
                            <td class="text-center">${item.level4 || ''}</td>
                        </tr>`;
                $('#tableBody').append(row);
            });
            applyColumnVisibility();
        });
    }

    function toggleRow(code, btn) {
        const icon = $(btn).find('i');
        const isOpening = icon.hasClass('fa-plus-square');

        if (isOpening) {
            icon.replaceWith('<i class="fas fa-minus-square"></i>');
            $(`tr[data-parent="${code}"]`).fadeIn(100);
        } else {
            icon.replaceWith('<i class="fas fa-plus-square"></i>');
            hideChildren(code);
        }
    }

    function hideChildren(parentCode) {
        $(`tr[data-parent="${parentCode}"]`).each(function() {
            const childCode = $(this).data('code');
            $(this).hide();
            $(this).find('.btn-toggle i').replaceWith('<i class="fas fa-plus-square"></i>');
            hideChildren(childCode);
        });
    }

    function applyColumnVisibility() {
        $('.column-toggle').each(function() {
            const idx = $(this).data('col');
            const show = $(this).is(':checked');
            $(`#allocationTable th:eq(${idx}), #allocationTable td:nth-child(${idx + 1})`).toggle(show);
        });
    }

    function openModal(id) {
        $('#reliability_allocation_id').val(id);
        attachReportModal.show();
    }
</script>
@endpush

@push('css')
<style>
    /* Pengaturan dasar tabel agar teks tidak terpotong dan ukuran pas */
    #allocationTable th,
    #allocationTable td {
        white-space: nowrap;
        font-size: 11px;
        vertical-align: middle;
    }

    /* Styling tombol Plus/Minus */
    .btn-toggle {
        border: none;
        background: none;
        color: #ed1c24;
        font-size: 14px;
        padding: 0 5px;
        cursor: pointer;
    }

    /* Efek hover pada baris */
    .pbs-row:hover {
        background-color: rgba(237, 28, 36, 0.05) !important;
    }

    /* TEKNIK UNTUK LEVEL 1 (Sistem Utama: Bogie, Brake, dll) */
    /* Baris yang tidak memiliki data-parent atau parent-nya 'root' */
    tr[data-parent="root"] {
        background-color: #f8f9fa !important;
        /* Abu-abu sangat muda */
        font-weight: bold;
        border-left: 4px solid #ed1c24;
        /* Memberi aksen garis merah di kiri */
    }

    /* Memberi indentasi (jarak ke dalam) berdasarkan level agar terlihat seperti pohon */
    tr[data-level="2"] td:nth-child(2) {
        padding-left: 20px !important;
    }

    tr[data-level="3"] td:nth-child(2) {
        padding-left: 40px !important;
    }

    tr[data-level="4"] td:nth-child(2) {
        padding-left: 60px !important;
    }

    /* Mewarnai Badge Code agar lebih rapi */
    .badge-code {
        font-family: 'Courier New', Courier, monospace;
        color: #555;
        background: #e9ecef;
        padding: 2px 5px;
        border-radius: 3px;
    }
</style>
@endpush