@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('fmeca.index') }}">FMECA Parts</a></li>
                        <li class="breadcrumb-item active">{{ $fmecaPart->name }} Items</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title fw-bold mb-0">Items for {{ $fmecaPart->name }}</h3>
                </div>
                <div class="card-body">
                    @if ($items->isEmpty())
                        <p class="text-muted mb-0">No items found for this part.</p>
                    @else
                        <!-- Tombol Zoom -->
                        <div class="mb-3">
                            <button class="btn btn-sm btn-primary" id="zoom-in-btn"><i class="bi bi-zoom-in"></i> Zoom
                                In</button>
                            <button class="btn btn-sm btn-primary" id="zoom-out-btn"><i class="bi bi-zoom-out"></i> Zoom
                                Out</button>
                            <span id="zoom-level" class="ml-2">Zoom: 100%</span>
                        </div>
                        <div class="table-responsive" tabindex="0">
                            <table class="table table-hover table-striped align-middle" id="fmeca-items-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>ID</th>
                                        <th>Item Reference</th>
                                        <th>Subsystem</th>
                                        <th>Item Name</th>
                                        <th>Function</th>
                                        <th>Operational Mode</th>
                                        <th>Is Safety</th>
                                        <th>Failure Mode</th>
                                        <th>Failure Causes</th>
                                        <th>Failure Base</th>
                                        <th>Ratio</th>
                                        <th>Failure Rate</th>
                                        <th>Items per Train</th>
                                        <th>Data Source</th>
                                        <th>Failure Effect (Item)</th>
                                        <th>Failure Effect (Subsystem)</th>
                                        <th>Failure Effect (System)</th>
                                        <th>Reference</th>
                                        <th>Safety Risk Severity Class</th>
                                        <th>Safety Risk Frequency</th>
                                        <th>Safety Risk Level</th>
                                        <th>Reliability Risk Severity Class</th>
                                        <th>Reliability Risk Frequency</th>
                                        <th>Reliability Risk Level</th>
                                        <th>Failure Detection Means</th>
                                        <th>Available Contingency</th>
                                        <th>Remarks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortable">
                                    @foreach ($items as $item)
                                        <tr data-item-id="{{ $item->id }}" class="view-mode" draggable="true">
                                            <td class="drag-handle">{{ $loop->iteration }}</td>
                                            <td>{{ $item->id }}</td>
                                            <td class="text-wrap">{{ $item->item_ref }}</td>
                                            <td class="text-wrap">{{ $item->subsystem ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->item_name }}</td>
                                            <td class="text-wrap">{{ $item->function ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->operational_mode ?? '-' }}</td>
                                            <td>{{ $item->is_safety ? 'Yes' : 'No' }}</td>
                                            <td class="text-wrap">{{ $item->failure_mode ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->failure_causes ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->failure_base ?? '-' }}</td>
                                            <td>{{ $item->ratio ?? '-' }}</td>
                                            <td>{{ $item->failure_rate ?? '-' }}</td>
                                            <td>{{ $item->items_per_train ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->data_source ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->failure_effect_item ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->failure_effect_subsystem ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->failure_effect_system ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->reference ?? '-' }}</td>
                                            <td>{{ $item->is_safety ? $item->safety_risk_severity_class ?? '-' : '-' }}
                                            </td>
                                            <td>{{ $item->is_safety ? $item->safety_risk_frequency ?? '-' : '-' }}</td>
                                            <td>{{ $item->is_safety ? $item->safety_risk_level ?? '-' : '-' }}</td>
                                            <td>{{ !$item->is_safety ? $item->reliability_risk_severity_class ?? '-' : '-' }}
                                            </td>
                                            <td>{{ !$item->is_safety ? $item->reliability_risk_frequency ?? '-' : '-' }}
                                            </td>
                                            <td>{{ !$item->is_safety ? $item->reliability_risk_level ?? '-' : '-' }}</td>
                                            <td class="text-wrap">{{ $item->failure_detection_means ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->available_contingency ?? '-' }}</td>
                                            <td class="text-wrap">{{ $item->remarks ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary edit-btn"
                                                    data-item-id="{{ $item->id }}">Edit</button>
                                                <a href="#" class="btn btn-sm btn-danger delete-btn"
                                                    onclick="if(confirm('Are you sure you want to delete this item?')) { window.location.href='{{ route('fmeca.delete', $item->id) }}'; }">Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Summary Table -->
                        <div class="table-responsive mt-4" tabindex="0">
                            <table class="table table-hover table-striped align-middle">
                                <thead class="table-light" style="background-color: #007bff; color: white;">
                                    <tr>
                                        <th>Risk Level</th>
                                        <th>Safety Risk</th>
                                        <th>Reliability Risk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Intolerable</td>
                                        <td>{{ $items->where('is_safety', true)->where('safety_risk_level', 'Intolerable')->count() }}
                                        </td>
                                        <td>{{ $items->where('is_safety', false)->where('reliability_risk_level', 'Intolerable')->count() }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Undesirable</td>
                                        <td>{{ $items->where('is_safety', true)->where('safety_risk_level', 'Undesirable')->count() }}
                                        </td>
                                        <td>{{ $items->where('is_safety', false)->where('reliability_risk_level', 'Undesirable')->count() }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Tolerable</td>
                                        <td>{{ $items->where('is_safety', true)->where('safety_risk_level', 'Tolerable')->count() }}
                                        </td>
                                        <td>{{ $items->where('is_safety', false)->where('reliability_risk_level', 'Tolerable')->count() }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Negligible</td>
                                        <td>{{ $items->where('is_safety', true)->where('safety_risk_level', 'Negligible')->count() }}
                                        </td>
                                        <td>{{ $items->where('is_safety', false)->where('reliability_risk_level', 'Negligible')->count() }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sum</td>
                                        <td>{{ $items->where('is_safety', true)->count() }}</td>
                                        <td>{{ $items->where('is_safety', false)->count() }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <a href="{{ route('fmeca.index') }}" class="btn btn-secondary mt-3">Back to FMECA Parts</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root {
            --zoom-level: 1;
        }

        .form-control,
        .form-check-input,
        .form-select {
            font-size: calc(1.2rem * var(--zoom-level));
            padding: calc(0.75rem * var(--zoom-level)) calc(1rem * var(--zoom-level));
            min-width: calc(250px * var(--zoom-level));
            max-width: 100%;
            border-radius: calc(6px * var(--zoom-level));
            transition: border-color 0.2s ease-in-out;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .form-control.textarea {
            min-height: calc(120px * var(--zoom-level));
            resize: vertical;
            width: 100%;
        }

        .table td,
        .table th {
            padding: calc(1rem * var(--zoom-level)) calc(1.25rem * var(--zoom-level));
            vertical-align: middle;
            white-space: normal;
            word-wrap: break-word;
            min-width: calc(150px * var(--zoom-level));
            max-width: calc(350px * var(--zoom-level));
            font-size: calc(1rem * var(--zoom-level));
        }

        .table th {
            white-space: nowrap;
            font-weight: 600;
        }

        .form-check-input {
            margin-top: calc(0.5rem * var(--zoom-level));
            transform: scale(calc(1.5 * var(--zoom-level)));
        }

        .invalid-feedback {
            font-size: calc(0.95rem * var(--zoom-level));
            color: #dc3545;
        }

        .text-wrap {
            white-space: normal !important;
            word-wrap: break-word;
            max-width: calc(350px * var(--zoom-level));
        }

        .btn-sm {
            font-size: calc(1rem * var(--zoom-level));
            padding: calc(0.5rem * var(--zoom-level)) calc(1.25rem * var(--zoom-level));
            border-radius: calc(4px * var(--zoom-level));
        }

        .btn-primary:hover,
        .btn-secondary:hover {
            opacity: 0.9;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table-responsive:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
        }

        .drag-handle {
            cursor: move;
            user-select: none;
        }

        #sortable tr {
            transition: background-color 0.2s;
        }

        #sortable tr.sortable-ghost {
            opacity: 0.5;
            background-color: #e9ecef;
        }

        .swal2-container {
            z-index: 9999;
        }

        .swal2-modal {
            width: 90%;
            max-width: 800px;
        }

        .swal2-content form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .swal2-content .form-group {
            flex: 1 1 45%;
            min-width: 200px;
        }

        .swal2-content .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        @media (max-width: 768px) {

            .form-control,
            .form-select {
                min-width: 100%;
            }

            .table td,
            .table th {
                min-width: calc(120px * var(--zoom-level));
                font-size: calc(0.9rem * var(--zoom-level));
            }

            .swal2-content .form-group {
                flex: 1 1 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi SortableJS
            const sortable = Sortable.create(document.getElementById('sortable'), {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    const rows = document.querySelectorAll('#sortable tr.view-mode');
                    const order = Array.from(rows).map(row => row.getAttribute('data-item-id'));
                    updateOrder(order);
                    updateRowNumbers();
                }
            });

            function updateRowNumbers() {
                const rows = document.querySelectorAll('#sortable tr.view-mode');
                rows.forEach((row, index) => {
                    row.cells[0].textContent = index + 1;
                });
            }

            function updateOrder(order) {
                fetch('{{ route('fmeca.reorder', $fmecaPart->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            order: order
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data.message);
                    })
                    .catch(error => {
                        console.error('Error updating order:', error);
                        Swal.fire('Error', 'Failed to update order. Please try again.', 'error');
                    });
            }

            // Handle Edit Button
            const editButtons = document.querySelectorAll('.edit-btn');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const itemId = this.getAttribute('data-item-id');
                    const row = this.closest('tr');
                    const itemData = {
                        item_ref: row.cells[2].textContent.trim(),
                        subsystem: row.cells[3].textContent.trim() === '-' ? '' : row.cells[3]
                            .textContent.trim(),
                        item_name: row.cells[4].textContent.trim(),
                        function: row.cells[5].textContent.trim() === '-' ? '' : row.cells[5]
                            .textContent.trim(),
                        operational_mode: row.cells[6].textContent.trim() === '-' ? '' : row
                            .cells[6].textContent.trim(),
                        is_safety: row.cells[7].textContent.trim() === 'Yes' ? '1' : '0',
                        failure_mode: row.cells[8].textContent.trim() === '-' ? '' : row.cells[
                            8].textContent.trim(),
                        failure_causes: row.cells[9].textContent.trim() === '-' ? '' : row
                            .cells[9].textContent.trim(),
                        failure_base: row.cells[10].textContent.trim() === '-' ? '' : row.cells[
                            10].textContent.trim(),
                        ratio: row.cells[11].textContent.trim() === '-' ? '' : row.cells[11]
                            .textContent.trim(),
                        failure_rate: row.cells[12].textContent.trim() === '-' ? '' : row.cells[
                            12].textContent.trim(),
                        items_per_train: row.cells[13].textContent.trim() === '-' ? '' : row
                            .cells[13].textContent.trim(),
                        data_source: row.cells[14].textContent.trim() === '-' ? '' : row.cells[
                            14].textContent.trim(),
                        failure_effect_item: row.cells[15].textContent.trim() === '-' ? '' : row
                            .cells[15].textContent.trim(),
                        failure_effect_subsystem: row.cells[16].textContent.trim() === '-' ?
                            '' : row.cells[16].textContent.trim(),
                        failure_effect_system: row.cells[17].textContent.trim() === '-' ? '' :
                            row.cells[17].textContent.trim(),
                        reference: row.cells[18].textContent.trim() === '-' ? '' : row.cells[18]
                            .textContent.trim(),
                        safety_risk_severity_class: row.cells[19].textContent.trim() === '-' ?
                            '' : row.cells[19].textContent.trim(),
                        safety_risk_frequency: row.cells[20].textContent.trim() === '-' ? '' :
                            row.cells[20].textContent.trim(),
                        safety_risk_level: row.cells[21].textContent.trim() === '-' ? '' : row
                            .cells[21].textContent.trim(),
                        reliability_risk_severity_class: row.cells[22].textContent.trim() ===
                            '-' ? '' : row.cells[22].textContent.trim(),
                        reliability_risk_frequency: row.cells[23].textContent.trim() === '-' ?
                            '' : row.cells[23].textContent.trim(),
                        reliability_risk_level: row.cells[24].textContent.trim() === '-' ? '' :
                            row.cells[24].textContent.trim(),
                        failure_detection_means: row.cells[25].textContent.trim() === '-' ? '' :
                            row.cells[25].textContent.trim(),
                        available_contingency: row.cells[26].textContent.trim() === '-' ? '' :
                            row.cells[26].textContent.trim(),
                        remarks: row.cells[27].textContent.trim() === '-' ? '' : row.cells[27]
                            .textContent.trim()
                    };

                    Swal.fire({
                        title: 'Edit Item',
                        html: `
                            <form id="edit-form" class="swal2-content">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">
                                <div class="form-group">
                                    <label>Item Reference</label>
                                    <input type="text" name="item_ref" class="form-control" value="${itemData.item_ref}" required>
                                </div>
                                <div class="form-group">
                                    <label>Subsystem</label>
                                    <input type="text" name="subsystem" class="form-control" value="${itemData.subsystem}">
                                </div>
                                <div class="form-group">
                                    <label>Item Name</label>
                                    <input type="text" name="item_name" class="form-control" value="${itemData.item_name}" required>
                                </div>
                                <div class="form-group">
                                    <label>Function</label>
                                    <textarea name="function" class="form-control textarea">${itemData.function}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Operational Mode</label>
                                    <input type="text" name="operational_mode" class="form-control" value="${itemData.operational_mode}">
                                </div>
                                <div class="form-group">
                                    <label>Is Safety</label>
                                    <select name="is_safety" class="form-control is-safety-toggle">
                                        <option value="1" ${itemData.is_safety === '1' ? 'selected' : ''}>Yes</option>
                                        <option value="0" ${itemData.is_safety === '0' ? 'selected' : ''}>No</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Failure Mode</label>
                                    <input type="text" name="failure_mode" class="form-control" value="${itemData.failure_mode}">
                                </div>
                                <div class="form-group">
                                    <label>Failure Causes</label>
                                    <textarea name="failure_causes" class="form-control textarea">${itemData.failure_causes}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Failure Base</label>
                                    <input type="text" name="failure_base" class="form-control" value="${itemData.failure_base}">
                                </div>
                                <div class="form-group">
                                    <label>Ratio</label>
                                    <input type="number" name="ratio" class="form-control" value="${itemData.ratio}" step="any">
                                </div>
                                <div class="form-group">
                                    <label>Failure Rate</label>
                                    <input type="number" name="failure_rate" class="form-control" value="${itemData.failure_rate}" step="any">
                                </div>
                                <div class="form-group">
                                    <label>Items per Train</label>
                                    <input type="number" name="items_per_train" class="form-control" value="${itemData.items_per_train}">
                                </div>
                                <div class="form-group">
                                    <label>Data Source</label>
                                    <input type="text" name="data_source" class="form-control" value="${itemData.data_source}">
                                </div>
                                <div class="form-group">
                                    <label>Failure Effect (Item)</label>
                                    <textarea name="failure_effect_item" class="form-control textarea">${itemData.failure_effect_item}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Failure Effect (Subsystem)</label>
                                    <textarea name="failure_effect_subsystem" class="form-control textarea">${itemData.failure_effect_subsystem}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Failure Effect (System)</label>
                                    <textarea name="failure_effect_system" class="form-control textarea">${itemData.failure_effect_system}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Reference</label>
                                    <input type="text" name="reference" class="form-control" value="${itemData.reference}">
                                </div>
                                <div class="form-group">
                                    <label>Safety Risk Severity Class</label>
                                    <select name="safety_risk_severity_class" class="form-control safety-risk-field" ${itemData.is_safety === '1' ? '' : 'disabled'}>
                                        <option value="" ${itemData.safety_risk_severity_class === '' ? 'selected' : ''}>-- Select --</option>
                                        <option value="Insignificant" ${itemData.safety_risk_severity_class === 'Insignificant' ? 'selected' : ''}>Insignificant</option>
                                        <option value="Marginal" ${itemData.safety_risk_severity_class === 'Marginal' ? 'selected' : ''}>Marginal</option>
                                        <option value="Critical" ${itemData.safety_risk_severity_class === 'Critical' ? 'selected' : ''}>Critical</option>
                                        <option value="Catastrophic" ${itemData.safety_risk_severity_class === 'Catastrophic' ? 'selected' : ''}>Catastrophic</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Safety Risk Frequency</label>
                                    <input type="text" name="safety_risk_frequency" class="form-control safety-risk-field" value="${itemData.safety_risk_frequency}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Safety Risk Level</label>
                                    <input type="text" name="safety_risk_level" class="form-control safety-risk-field" value="${itemData.safety_risk_level}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Reliability Risk Severity Class</label>
                                    <select name="reliability_risk_severity_class" class="form-control reliability-risk-field" ${itemData.is_safety === '0' ? '' : 'disabled'}>
                                        <option value="" ${itemData.reliability_risk_severity_class === '' ? 'selected' : ''}>-- Select --</option>
                                        <option value="Insignificant" ${itemData.reliability_risk_severity_class === 'Insignificant' ? 'selected' : ''}>Insignificant</option>
                                        <option value="Marginal" ${itemData.reliability_risk_severity_class === 'Marginal' ? 'selected' : ''}>Marginal</option>
                                        <option value="Critical" ${itemData.reliability_risk_severity_class === 'Critical' ? 'selected' : ''}>Critical</option>
                                        <option value="Catastrophic" ${itemData.reliability_risk_severity_class === 'Catastrophic' ? 'selected' : ''}>Catastrophic</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Reliability Risk Frequency</label>
                                    <input type="text" name="reliability_risk_frequency" class="form-control reliability-risk-field" value="${itemData.reliability_risk_frequency}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Reliability Risk Level</label>
                                    <input type="text" name="reliability_risk_level" class="form-control reliability-risk-field" value="${itemData.reliability_risk_level}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Failure Detection Means</label>
                                    <input type="text" name="failure_detection_means" class="form-control" value="${itemData.failure_detection_means}">
                                </div>
                                <div class="form-group">
                                    <label>Available Contingency</label>
                                    <input type="text" name="available_contingency" class="form-control" value="${itemData.available_contingency}">
                                </div>
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea name="remarks" class="form-control textarea">${itemData.remarks}</textarea>
                                </div>
                            </form>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        cancelButtonText: 'Cancel',
                        focusConfirm: false,
                        preConfirm: () => {
                            const form = document.getElementById('edit-form');
                            const formData = new FormData(form);
                            return fetch('{{ route('fmeca.update', '') }}/' + itemId, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: formData
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        return response.json().then(err => {
                                            throw new Error(err.message ||
                                                'Failed to update item');
                                        });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    Swal.fire('Success',
                                            'Item updated successfully!', 'success')
                                        .then(() => {
                                            window.location.reload();
                                        });
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(
                                        `Request failed: ${error.message}`);
                                });
                        },
                        didOpen: () => {
                            const toggles = document.querySelectorAll(
                                '.is-safety-toggle');
                            toggles.forEach(toggle => {
                                toggle.addEventListener('change', function() {
                                    const safetyFields = document
                                        .querySelectorAll(
                                            '.safety-risk-field');
                                    const reliabilityFields = document
                                        .querySelectorAll(
                                            '.reliability-risk-field');
                                    if (this.value === '1') {
                                        safetyFields.forEach(field =>
                                            field.removeAttribute(
                                                'disabled'));
                                        reliabilityFields.forEach(
                                            field => field
                                            .setAttribute(
                                                'disabled',
                                                'disabled'));
                                    } else {
                                        safetyFields.forEach(field =>
                                            field.setAttribute(
                                                'disabled',
                                                'disabled'));
                                        reliabilityFields.forEach(
                                            field => field
                                            .removeAttribute(
                                                'disabled'));
                                    }
                                });
                            });

                            const textareas = document.querySelectorAll('.textarea');
                            textareas.forEach(textarea => {
                                textarea.addEventListener('input', function() {
                                    this.style.height = 'auto';
                                    this.style.height =
                                        `${this.scrollHeight}px`;
                                });
                            });
                        }
                    });
                });
            });

            // Navigasi keyboard untuk table-responsive
            const tableResponsives = document.querySelectorAll('.table-responsive');
            tableResponsives.forEach(table => {
                table.addEventListener('keydown', function(e) {
                    if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                        e.preventDefault();
                        const scrollAmount = 50;
                        if (e.key === 'ArrowRight') {
                            this.scrollLeft += scrollAmount;
                        } else if (e.key === 'ArrowLeft') {
                            this.scrollLeft -= scrollAmount;
                        }
                    }
                });
            });

            // Fitur Zoom In dan Zoom Out
            let zoomLevel = 1;
            const zoomStep = 0.1;
            const minZoom = 0.5;
            const maxZoom = 2;

            const zoomInBtn = document.getElementById('zoom-in-btn');
            const zoomOutBtn = document.getElementById('zoom-out-btn');
            const zoomLevelDisplay = document.getElementById('zoom-level');

            function updateZoom() {
                document.documentElement.style.setProperty('--zoom-level', zoomLevel);
                zoomLevelDisplay.textContent = `Zoom: ${(zoomLevel * 100).toFixed(0)}%`;
            }

            zoomInBtn.addEventListener('click', function() {
                if (zoomLevel < maxZoom) {
                    zoomLevel = Math.min(zoomLevel + zoomStep, maxZoom);
                    updateZoom();
                }
            });

            zoomOutBtn.addEventListener('click', function() {
                if (zoomLevel > minZoom) {
                    zoomLevel = Math.max(zoomLevel - zoomStep, minZoom);
                    updateZoom();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && (e.key === '+' || e.key === '=')) {
                    e.preventDefault();
                    if (zoomLevel < maxZoom) {
                        zoomLevel = Math.min(zoomLevel + zoomStep, maxZoom);
                        updateZoom();
                    }
                } else if (e.ctrlKey && e.key === '-') {
                    e.preventDefault();
                    if (zoomLevel > minZoom) {
                        zoomLevel = Math.max(zoomLevel - zoomStep, minZoom);
                        updateZoom();
                    }
                }
            });

            updateZoom();
        });
    </script>
@endpush
