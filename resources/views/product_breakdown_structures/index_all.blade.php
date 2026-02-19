@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('product-breakdown-structure.index') }}">Product Breakdowns Structure</a></li>
                        <li class="breadcrumb-item active text-bold">All References</li>
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
                    <h4 class="mb-0"><i class="fas fa-list"></i> PBS All Reference List</h4>
                    <button class="btn btn-light btn-sm" id="toggleColumnBtn">
                        <i class="fas fa-eye-slash"></i> Pilih Kolom
                    </button>
                </div>
                <div class="card-body bg-light">
                    <div class="mb-3 card card-body border-secondary" id="columnToggles" style="display: none;">
                        <div class="d-flex flex-wrap gap-3">
                            @php
                                $columns = ['No', 'Project', 'Product', 'Level 1', 'Level 2', 'Level 3', 'Level 4', 'Deskripsi', 'Material', 'Berat (kg)', 'Qty/TS', 'Total Qty', 'Source (Drawing)', 'Action'];
                                $defaultVisible = ['No', 'Project', 'Product', 'Level 1', 'Level 2', 'Level 3', 'Level 4', 'Source (Drawing)', 'Action'];
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
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let attachReportModal = null;
        let rowCounter = 1;

        $(document).ready(function() {
            attachReportModal = new bootstrap.Modal(document.getElementById('attachReportModal'));
            $('.select2').select2({ dropdownParent: $('#attachReportModal'), width: '100%' });

            loadAllData();

            $('#toggleColumnBtn').click(function() { $('#columnToggles').slideToggle(); });
            $('.column-toggle').change(applyColumnVisibility);

            $('#attachReportSubmit').click(function() {
                const data = {
                    reliability_allocation_id: $('#reliability_allocation_id').val(),
                    newprogressreport_id: $('#newprogressreport_id').val(),
                    _token: '{{ csrf_token() }}'
                };
                $.post('/product-breakdown-structure/attach-report', data, function() {
                    attachReportModal.hide();
                    loadAllData();
                });
            });
        });

        function loadAllData() {
            rowCounter = 1;
            $('#tableBody').html('<tr><td colspan="100%" class="text-center">Memproses semua data...</td></tr>');
            
            $.get(`{{ route('product-breakdown-structure.getAllData') }}`, function(data) {
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
                            <td class="small text-bold text-danger">${item.project_title || '-'}</td>
                            <td style="padding-left: ${padding}px; min-width: 200px;">
                                <div class="d-flex align-items-center">${toggle} <span>${item.product || '-'}</span></div>
                            </td>
                            <td class="text-center">${item.level1 || ''}</td>
                            <td class="text-center">${item.level2 || ''}</td>
                            <td class="text-center">${item.level3 || ''}</td>
                            <td class="text-center">${item.level4 || ''}</td>
                            <td><small>${item.deskripsi || '-'}</small></td>
                            <td><small>${item.material || '-'}</small></td>
                            <td class="text-center">${item.berat_kg || '-'}</td>
                            <td class="text-center">${item.qty_per_ts || '-'}</td>
                            <td class="text-center fw-bold">${item.total_qty || '-'}</td>
                            <td><small>${item.source_drawing_names.map(d => d.nodokumen).join(', ') || '-'}</small></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="openModal(${item.id})"><i class="fas fa-link"></i></button>
                            </td>
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
    #allocationTable th, #allocationTable td { white-space: nowrap; font-size: 11px; vertical-align: middle; }
    .btn-toggle { border: none; background: none; color: #ed1c24; font-size: 14px; padding: 0 5px; cursor: pointer; }
    .pbs-row:hover { background-color: rgba(237, 28, 36, 0.05) !important; }
    tr[data-parent="root"] { background-color: #f8f9fa !important; font-weight: bold; border-left: 4px solid #ed1c24; }
</style>
@endpush