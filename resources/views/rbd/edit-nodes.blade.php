@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <ol class="breadcrumb bg-white px-3 py-2 float-left rounded shadow-sm">
                        <li class="breadcrumb-item"><a href="{{ route('rbd.index') }}"
                                class="text-decoration-none text-primary">RBD Identities</a></li>
                        <li class="breadcrumb-item active">Edit Nodes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-3 mb-4">
                    <div class="card-header bg-gradient bg-primary text-white py-3">
                        <h3 class="card-title fw-bold mb-0">Edit Nodes for {{ $rbdIdentity->componentname }}</h3>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if ($rbdIdentity->rbdBlocks->isEmpty())
                            <div class="alert alert-warning">
                                No blocks are available for this RBD Identity. Please
                                <a href="{{ route('rbd.blocks.edit', $rbdIdentity->id) }}" class="text-primary">create a
                                    block</a>
                                before adding block nodes.
                            </div>
                        @endif
                        @foreach ($nodes as $index => $node)
                            @if ($node->type == 'block' && $node->rbd_block_id && !$rbdIdentity->rbdBlocks->contains('id', $node->rbd_block_id))
                                <div class="alert alert-danger">
                                    Node {{ $node->id }} references an invalid block (ID: {{ $node->rbd_block_id }}).
                                    Please select a valid block or remove this node.
                                </div>
                            @endif
                        @endforeach

                        <form method="POST" action="{{ route('rbd.nodes.update', $rbdIdentity->id) }}" id="nodes-form">
                            @csrf
                            @method('PUT')

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="nodes-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Node ID</th>
                                            <th>Node Type</th>
                                            <th>Parent Node</th>
                                            <th>Block Group Type</th>
                                            <th>Associated Block</th>
                                            <th>Block Count</th>
                                            <th>K Value</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="nodes-container">
                                        @foreach ($nodes as $index => $node)
                                            <tr class="node-row" data-index="{{ $index }}">
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="nodes[{{ $index }}][id]" value="{{ $node->id }}"
                                                        readonly>
                                                </td>
                                                <td>
                                                    <input type="hidden" name="nodes[{{ $index }}][id]"
                                                        value="{{ $node->id }}">
                                                    <div class="form-floating">
                                                        <select name="nodes[{{ $index }}][node_type]"
                                                            class="form-select node-type" required
                                                            @if ($rbdIdentity->rbdBlocks->isEmpty()) data-block-unavailable="true" @endif>
                                                            @foreach (['series', 'parallel', 'k-out-of-n', 'block'] as $type)
                                                                <option value="{{ $type }}"
                                                                    {{ $node->type == $type ? 'selected' : '' }}
                                                                    {{ $type == 'block' && $rbdIdentity->rbdBlocks->isEmpty() ? 'disabled' : '' }}>
                                                                    {{ ucfirst($type) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label>Node Type <span class="text-danger">*</span></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating">
                                                        <select name="nodes[{{ $index }}][parent_id]"
                                                            class="form-select parent-select">
                                                            <option value="">-- Select Parent Node --</option>
                                                            @foreach ($nodes as $pnode)
                                                                @if ($pnode->id != $node->id)
                                                                    <option value="{{ $pnode->id }}"
                                                                        {{ $pnode->id == $node->parent_id ? 'selected' : '' }}>
                                                                        {{ ucfirst($pnode->type) }}
                                                                        {{ $pnode->type == 'block' && $pnode->rbdBlock ? '(' . $pnode->rbdBlock->name . ')' : '(Node ' . $pnode->id . ')' }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <label>Parent Node</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating block-group-type-select"
                                                        style="{{ $node->type == 'block' ? '' : 'display:none' }}">
                                                        <select name="nodes[{{ $index }}][block_group_type]"
                                                            class="form-select block-group-type"
                                                            {{ $node->type == 'block' ? 'required' : '' }}>
                                                            <option value="single"
                                                                {{ $node->block_group_type == 'single' ? 'selected' : '' }}>
                                                                Single</option>
                                                            <option value="series"
                                                                {{ $node->block_group_type == 'series' ? 'selected' : '' }}>
                                                                Series Group</option>
                                                            <option value="parallel"
                                                                {{ $node->block_group_type == 'parallel' ? 'selected' : '' }}>
                                                                Parallel Group</option>
                                                            <option value="k-out-of-n"
                                                                {{ $node->block_group_type == 'k-out-of-n' ? 'selected' : '' }}>
                                                                K-out-of-N Group</option>
                                                        </select>
                                                        <label>Block Group Type <span class="text-danger">*</span></label>
                                                    </div>
                                                    @if ($node->type != 'block')
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="form-floating block-select"
                                                        style="{{ $node->type == 'block' ? '' : 'display:none' }}">
                                                        <select name="nodes[{{ $index }}][block_id]"
                                                            class="form-select block-id"
                                                            {{ $node->type == 'block' ? 'required' : '' }}>
                                                            <option value="">-- Select Block --</option>
                                                            @foreach ($rbdIdentity->rbdBlocks as $block)
                                                                <option value="{{ $block->id }}"
                                                                    {{ $node->rbd_block_id == $block->id ? 'selected' : '' }}>
                                                                    {{ $block->name }} (λ={{ $block->lambda }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label>Associated Block <span class="text-danger">*</span></label>
                                                    </div>
                                                    @if ($node->type != 'block')
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="form-floating block-count-input"
                                                        style="{{ $node->type == 'block' && $node->block_group_type && $node->block_group_type != 'single' ? '' : 'display:none' }}">
                                                        <input type="number" min="1" step="1"
                                                            name="nodes[{{ $index }}][block_count]"
                                                            class="form-control block-count"
                                                            value="{{ $node->block_count ?? '' }}"
                                                            {{ $node->type == 'block' && $node->block_group_type && $node->block_group_type != 'single' ? 'required' : '' }}>
                                                        <label>Block Count <span class="text-danger">*</span></label>
                                                    </div>
                                                    @if ($node->type != 'block' || !$node->block_group_type || $node->block_group_type == 'single')
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="form-floating k-input"
                                                        style="{{ $node->type == 'k-out-of-n' || ($node->type == 'block' && $node->block_group_type == 'k-out-of-n') ? '' : 'display:none' }}">
                                                        <input type="number" min="1" step="1"
                                                            name="nodes[{{ $index }}][k_value]"
                                                            class="form-control k-value" value="{{ $node->k_value ?? '' }}"
                                                            {{ $node->type == 'k-out-of-n' || ($node->type == 'block' && $node->block_group_type == 'k-out-of-n') ? 'required' : '' }}>
                                                        <label>K Value <span class="text-danger">*</span></label>
                                                    </div>
                                                    @if ($node->type != 'k-out-of-n' && ($node->type != 'block' || $node->block_group_type != 'k-out-of-n'))
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm remove-node"><i
                                                            class="bi bi-trash"></i> Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" class="btn btn-outline-primary mb-3" id="add-node"
                                @if ($rbdIdentity->rbdBlocks->isEmpty()) disabled @endif>
                                <i class="bi bi-plus-circle"></i> Add Node
                            </button>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg"
                                    @if ($rbdIdentity->rbdBlocks->isEmpty()) disabled @endif>Save Nodes</button>
                                <a href="{{ route('rbd.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let nodeIndex = {{ count($nodes) }};
            const blockOptions =
                `@foreach ($rbdIdentity->rbdBlocks as $block)<option value="{{ $block->id }}">{{ $block->name }} (λ={{ $block->lambda }})</option>@endforeach`;
            const hasBlocks = {{ $rbdIdentity->rbdBlocks->isNotEmpty() ? 'true' : 'false' }};

            function addNodeRow(index) {
                const rowHtml = `
                    <tr class="node-row" data-index="${index}">
                        <td>
                            <input type="hidden" name="nodes[${index}][id]">
                            <div class="form-floating">
                                <select name="nodes[${index}][node_type]" class="form-select node-type" required data-block-unavailable="${hasBlocks ? 'false' : 'true'}">
                                    <option value="series">Series</option>
                                    <option value="parallel">Parallel</option>
                                    <option value="k-out-of-n">K-out-of-N</option>
                                    <option value="block" ${hasBlocks ? '' : 'disabled'}>Block</option>
                                </select>
                                <label>Node Type <span class="text-danger">*</span></label>
                            </div>
                        </td>
                        <td>
                            <div class="form-floating">
                                <select name="nodes[${index}][parent_id]" class="form-select parent-select">
                                    <option value="">-- Select Parent Node --</option>
                                </select>
                                <label>Parent Node</label>
                            </div>
                        </td>
                        <td>
                            <div class="form-floating block-group-type-select" style="display:none">
                                <select name="nodes[${index}][block_group_type]" class="form-select block-group-type">
                                    <option value="single">Single</option>
                                    <option value="series">Series Group</option>
                                    <option value="parallel">Parallel Group</option>
                                    <option value="k-out-of-n">K-out-of-N Group</option>
                                </select>
                                <label>Block Group Type <span class="text-danger">*</span></label>
                            </div>
                            <span class="text-muted">-</span>
                        </td>
                        <td>
                            <div class="form-floating block-select" style="display:none">
                                <select name="nodes[${index}][block_id]" class="form-select block-id">
                                    <option value="">-- Select Block --</option>
                                    ${blockOptions}
                                </select>
                                <label>Associated Block <span class="text-danger">*</span></label>
                            </div>
                            <span class="text-muted">-</span>
                        </td>
                        <td>
                            <div class="form-floating block-count-input" style="display:none">
                                <input type="number" min="1" step="1" name="nodes[${index}][block_count]" class="form-control block-count">
                                <label>Block Count <span class="text-danger">*</span></label>
                            </div>
                            <span class="text-muted">-</span>
                        </td>
                        <td>
                            <div class="form-floating k-input" style="display:none">
                                <input type="number" min="1" step="1" name="nodes[${index}][k_value]" class="form-control k-value">
                                <label>K Value <span class="text-danger">*</span></label>
                            </div>
                            <span class="text-muted">-</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-node"><i class="bi bi-trash"></i> Remove</button>
                        </td>
                    </tr>`;
                $('#nodes-container').append(rowHtml);
                updateParentOptions();
            }

            $('#add-node').click(function() {
                if (!hasBlocks) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Blocks Available',
                        text: 'Please create a block before adding a block node.',
                        confirmButtonText: 'Create Block',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary mx-2',
                            cancelButton: 'btn btn-outline-secondary mx-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href =
                                '{{ route('rbd.blocks.edit', $rbdIdentity->id) }}';
                        }
                    });
                } else {
                    addNodeRow(nodeIndex);
                    nodeIndex++;
                }
            });

            $(document).on('change', '.node-type', function() {
                const row = $(this).closest('.node-row');
                const type = $(this).val();
                const blockGroupTypeCell = row.find('.block-group-type-select');
                const blockCell = row.find('.block-select');
                const blockCountCell = row.find('.block-count-input');
                const kCell = row.find('.k-input');

                if (type == 'block' && row.find('.node-type').data('block-unavailable') == true) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Blocks Available',
                        text: 'Please create a block before selecting a block node.',
                        confirmButtonText: 'Create Block',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary mx-2',
                            cancelButton: 'btn btn-outline-secondary mx-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href =
                                '{{ route('rbd.blocks.edit', $rbdIdentity->id) }}';
                        }
                    });
                    $(this).val('series');
                    return;
                }

                blockGroupTypeCell.toggle(type == 'block');
                blockCell.toggle(type == 'block');
                blockGroupTypeCell.find('.text-muted').toggle(type != 'block');
                blockCell.find('.text-muted').toggle(type != 'block');

                const blockGroupType = row.find('.block-group-type').val() || 'single';
                blockCountCell.toggle(type == 'block' && blockGroupType != 'single');
                blockCountCell.find('.text-muted').toggle(type != 'block' || blockGroupType == 'single');
                kCell.toggle(type == 'k-out-of-n' || (type == 'block' && blockGroupType == 'k-out-of-n'));
                kCell.find('.text-muted').toggle(type != 'k-out-of-n' && (type != 'block' ||
                    blockGroupType != 'k-out-of-n'));

                row.find('.block-group-type').prop('required', type == 'block');
                row.find('.block-id').prop('required', type == 'block');
                row.find('.block-count').prop('required', type == 'block' && blockGroupType != 'single');
                row.find('.k-value').prop('required', type == 'k-out-of-n' || (type == 'block' &&
                    blockGroupType == 'k-out-of-n'));

                updateParentOptions();
            });

            $(document).on('change', '.block-group-type', function() {
                const row = $(this).closest('.node-row');
                const blockGroupType = $(this).val();
                const blockCountCell = row.find('.block-count-input');
                const kCell = row.find('.k-input');

                blockCountCell.toggle(blockGroupType != 'single');
                blockCountCell.find('.text-muted').toggle(blockGroupType == 'single');
                kCell.toggle(blockGroupType == 'k-out-of-n');
                kCell.find('.text-muted').toggle(blockGroupType != 'k-out-of-n');

                row.find('.block-count').prop('required', blockGroupType != 'single');
                row.find('.k-value').prop('required', blockGroupType == 'k-out-of-n');
            });

            $(document).on('click', '.remove-node', function() {
                $(this).closest('.node-row').remove();
                updateParentOptions();
            });

            function updateParentOptions() {
                $('.node-row').each(function(i) {
                    const currentRow = $(this);
                    const currentId = currentRow.find('input[name$="[id]"]').val() || `new-${i}`;
                    const select = currentRow.find('.parent-select');
                    const prevValue = select.val();

                    select.empty().append('<option value="">-- Select Parent Node --</option>');

                    $('.node-row').each(function(j) {
                        const nodeRow = $(this);
                        const nodeId = nodeRow.find('input[name$="[id]"]').val() || `new-${j}`;
                        if (nodeId !== currentId) {
                            const type = nodeRow.find('.node-type').val();
                            const blockSelect = nodeRow.find('.block-id');
                            const blockName = blockSelect.val() ? blockSelect.find(
                                'option:selected').text().split(' (')[0] : '';
                            const displayText = type == 'block' && blockName ?
                                `${type} (${blockName})` : `${type} (Node ${nodeId})`;
                            select.append(`<option value="${nodeId}">${displayText}</option>`);
                        }
                    });

                    if (prevValue && select.find(`option[value="${prevValue}"]`).length) {
                        select.val(prevValue);
                    }
                });
            }

            updateParentOptions();

            $('#nodes-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;

                let hasErrors = false;
                $('.node-row').each(function(index) {
                    const row = $(this);
                    const nodeType = row.find('.node-type').val();
                    const blockGroupType = row.find('.block-group-type').val() || 'single';
                    const blockId = row.find('.block-id').val();
                    const blockCount = row.find('.block-count').val();
                    const kValue = row.find('.k-value').val();

                    if (nodeType == 'block' && !blockId) {
                        hasErrors = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: `Associated Block is required for block node ${index + 1}.`,
                        });
                        return false;
                    }

                    if (nodeType == 'block' && blockGroupType != 'single' && (!blockCount || isNaN(
                            blockCount) || parseInt(blockCount) < 1)) {
                        hasErrors = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: `Block Count must be a positive integer for node ${index + 1} with ${blockGroupType} group.`,
                        });
                        return false;
                    }

                    if ((nodeType == 'k-out-of-n' || (nodeType == 'block' && blockGroupType ==
                            'k-out-of-n')) && (!kValue || isNaN(kValue) || parseInt(kValue) < 1)) {
                        hasErrors = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: `K Value must be a positive integer for node ${index + 1}.`,
                        });
                        return false;
                    }

                    if (blockGroupType == 'k-out-of-n' && blockCount && kValue && parseInt(kValue) >
                        parseInt(blockCount)) {
                        hasErrors = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: `K Value cannot be greater than Block Count for node ${index + 1}.`,
                        });
                        return false;
                    }
                });

                if (!hasErrors) {
                    Swal.fire({
                        title: 'Save Node List',
                        text: 'Are you sure to save the node list for "{{ $rbdIdentity->componentname }}"?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Save Nodes',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary mx-2',
                            cancelButton: 'btn btn-outline-secondary mx-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            });
        });
    </script>
@endpush
