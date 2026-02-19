@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <ol class="breadcrumb bg-white px-3 py-2 float-left rounded shadow-sm">
                        <li class="breadcrumb-item"><a href="{{ route('fta.index') }}"
                                class="text-decoration-none text-primary">FTA Identities</a></li>
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
                    <h3 class="card-title fw-bold mb-0">Edit Nodes for {{ $ftaIdentity->componentname }}</h3>
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
                    @if ($ftaIdentity->ftaEvents->isEmpty())
                        <div class="alert alert-warning">
                            No events are available for this FTA Identity. Please
                            <a href="{{ route('fta.events.edit', $ftaIdentity->id) }}" class="text-primary">create an
                                event</a>
                            before adding basic event nodes.
                        </div>
                    @endif
                    @foreach ($nodes as $index => $node)
                        @if (
                            $node->type == 'basic_event' &&
                                $node->fta_event_id &&
                                !$ftaIdentity->ftaEvents->contains('id', $node->fta_event_id))
                            <div class="alert alert-danger">
                                Node {{ $node->id }} references an invalid event (ID: {{ $node->fta_event_id }}).
                                Please select a valid event or remove this node.
                            </div>
                        @endif
                    @endforeach

                    <form method="POST" action="{{ route('fta.nodes.update', $ftaIdentity->id) }}" id="nodes-form">
                        @csrf
                        @method('PUT')

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="nodes-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Node ID</th>
                                        <th>Node Type</th>
                                        <th>Event Name</th>
                                        <th>Parent Node</th>
                                        <th>Associated Event</th>
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
                                                <div class="form-floating">
                                                    <select name="nodes[{{ $index }}][node_type]"
                                                        class="form-select node-type" required
                                                        @if ($ftaIdentity->ftaEvents->isEmpty()) data-event-unavailable="true" @endif>
                                                        @foreach (['and', 'or', 'basic_event'] as $type)
                                                            <option value="{{ $type }}"
                                                                {{ $node->type == $type ? 'selected' : '' }}
                                                                {{ $type == 'basic_event' && $ftaIdentity->ftaEvents->isEmpty() ? 'disabled' : '' }}>
                                                                {{ ucfirst($type) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label>Node Type <span class="text-danger">*</span></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating event-name-field"
                                                    style="{{ $node->type == 'basic_event' ? 'display:none' : '' }}">
                                                    <input type="text" name="nodes[{{ $index }}][event_name]"
                                                        class="form-control"
                                                        value="{{ old('nodes.' . $index . '.event_name', $node->event_name) }}"
                                                        {{ $node->type != 'basic_event' ? 'required' : '' }}
                                                        placeholder="Event Name">
                                                    <label>Event Name <span class="text-danger">*</span></label>
                                                </div>
                                                @if ($node->type == 'basic_event')
                                                    <span class="text-muted">-</span>
                                                @endif
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
                                                                    {{ ucfirst($pnode->type) }} (Node {{ $pnode->id }})
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    <label>Parent Node</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating event-select"
                                                    style="{{ $node->type == 'basic_event' ? '' : 'display:none' }}">
                                                    <select name="nodes[{{ $index }}][fta_event_id]"
                                                        class="form-select event-id"
                                                        {{ $node->type == 'basic_event' ? 'required' : '' }}>
                                                        <option value="">-- Select Event --</option>
                                                        @foreach ($ftaIdentity->ftaEvents as $event)
                                                            <option value="{{ $event->id }}"
                                                                {{ $node->fta_event_id == $event->id ? 'selected' : '' }}>
                                                                {{ $event->fmecaItem->item_name ?? '-' }} |||
                                                                {{ $event->fmecaItem->subsystem ?? '-' }} |||
                                                                {{ $event->name }} |||
                                                                (λ={{ $event->failure_rate }})
                                                                |||
                                                                {{ $event->fmecaItem->failure_effect_system ?? '-' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label>Associated Event <span class="text-danger">*</span></label>
                                                </div>
                                                @if ($node->type != 'basic_event')
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-node">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-outline-primary mb-3" id="add-node"
                            @if ($ftaIdentity->ftaEvents->isEmpty()) disabled @endif>
                            <i class="bi bi-plus-circle"></i> Add Node
                        </button>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg"
                                @if ($ftaIdentity->ftaEvents->isEmpty()) disabled @endif>Save Nodes</button>
                            <a href="{{ route('fta.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
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
            const eventOptions =
                `@foreach ($ftaIdentity->ftaEvents as $event)<option value="{{ $event->id }}">
                                                                {{ $event->fmecaItem->item_name ?? '-' }} |||
                                                                {{ $event->fmecaItem->subsystem ?? '-' }} |||
                                                                {{ $event->name }} |||
                                                                (λ={{ $event->failure_rate }})
                                                                |||
                                                                {{ $event->fmecaItem->failure_effect_system ?? '-' }}</option>@endforeach`;
            const hasEvents = {{ $ftaIdentity->ftaEvents->isNotEmpty() ? 'true' : 'false' }};

            function addNodeRow(index) {
                const rowHtml = `
                    <tr class="node-row" data-index="${index}">
                        <td><input type="hidden" name="nodes[${index}][id]"></td>
                        <td>
                            <div class="form-floating">
                                <select name="nodes[${index}][node_type]" class="form-select node-type" required data-event-unavailable="${hasEvents ? 'false' : 'true'}">
                                    <option value="and">AND</option>
                                    <option value="or">OR</option>
                                    <option value="basic_event" ${hasEvents ? '' : 'disabled'}>Basic Event</option>
                                </select>
                                <label>Node Type <span class="text-danger">*</span></label>
                            </div>
                        </td>
                        <td>
                            <div class="form-floating event-name-field">
                                <input type="text" name="nodes[${index}][event_name]" class="form-control" placeholder="Event Name" required>
                                <label>Event Name <span class="text-danger">*</span></label>
                            </div>
                            <span class="text-muted event-name-placeholder" style="display:none">-</span>
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
                            <div class="form-floating event-select" style="display:none">
                                <select name="nodes[${index}][fta_event_id]" class="form-select event-id">
                                    <option value="">-- Select Event --</option>
                                    ${eventOptions}
                                </select>
                                <label>Associated Event <span class="text-danger">*</span></label>
                            </div>
                            <span class="text-muted">-</span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-node">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        </td>
                    </tr>`;
                $('#nodes-container').append(rowHtml);
                updateParentOptions();
            }

            $('#add-node').click(function() {
                if (!hasEvents) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Events Available',
                        text: 'Please create an event before adding a basic event node.',
                        confirmButtonText: 'Create Event',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary mx-2',
                            cancelButton: 'btn btn-outline-secondary mx-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) window.location.href =
                            '{{ route('fta.events.edit', $ftaIdentity->id) }}';
                    });
                } else {
                    addNodeRow(nodeIndex);
                    nodeIndex++;
                }
            });

            $(document).on('change', '.node-type', function() {
                const row = $(this).closest('.node-row');
                const type = $(this).val();
                const eventCell = row.find('.event-select');
                const eventNameField = row.find('.event-name-field');
                const eventNamePlaceholder = row.find('.event-name-placeholder');

                if (type == 'basic_event' && row.find('.node-type').data('event-unavailable') == true) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Events Available',
                        text: 'Please create an event before selecting a basic event node.',
                        confirmButtonText: 'Create Event',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary mx-2',
                            cancelButton: 'btn btn-outline-secondary mx-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) window.location.href =
                            '{{ route('fta.events.edit', $ftaIdentity->id) }}';
                    });
                    $(this).val('and');
                    return;
                }

                eventCell.toggle(type == 'basic_event');
                eventCell.find('.text-muted').toggle(type != 'basic_event');
                row.find('.event-id').prop('required', type == 'basic_event');

                eventNameField.toggle(type != 'basic_event');
                eventNamePlaceholder.toggle(type == 'basic_event');
                row.find('input[name$="[event_name]"]').prop('required', type != 'basic_event');

                updateParentOptions();
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
                            const displayText = `${type} (Node ${nodeId})`;
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
                    const eventId = row.find('.event-id').val();
                    const eventName = row.find('input[name$="[event_name]"]').val();

                    if (nodeType == 'basic_event' && !eventId) {
                        hasErrors = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: `Associated Event is required for basic event node ${index + 1}.`,
                        });
                        return false;
                    }
                    if (['and', 'or'].includes(nodeType) && !eventName) {
                        hasErrors = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: `Event Name is required for ${nodeType} node ${index + 1}.`,
                        });
                        return false;
                    }
                });

                if (!hasErrors) {
                    Swal.fire({
                        title: 'Save Node List',
                        text: 'Are you sure to save the node list for "{{ $ftaIdentity->componentname }}"?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Save Nodes',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-primary mx-2',
                            cancelButton: 'btn btn-outline-secondary mx-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                }
            });
        });
    </script>
@endpush
