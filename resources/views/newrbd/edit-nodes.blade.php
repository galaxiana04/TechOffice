@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
                <li class="breadcrumb-item"><a href="{{ route('newrbd.index') }}" class="text-decoration-none text-primary">RBD
                        Instances</a></li>
                <li class="breadcrumb-item active">Edit Nodes</li>
            </ol>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-header bg-gradient bg-primary text-white py-3">
                    <h3 class="card-title fw-bold mb-0">Edit Nodes for {{ $rbdInstance->componentname }}</h3>
                </div>
                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($failureRates->isEmpty() && $otherInstances->isEmpty())
                        <div class="alert alert-warning">
                            No failure rates or other RBD instances available. Please create a failure rate or another RBD
                            instance before adding component nodes.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('newrbd.nodes.update', $rbdInstance->id) }}" id="nodes-form">
                        @csrf
                        @method('PUT')
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="nodes-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Key</th>
                                        <th>Category</th>
                                        <th>Configuration</th>
                                        <th>Quantity</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Failure Rate</th>
                                        <th>Foreign Instance</th>
                                        <th>X</th>
                                        <th>Y</th>
                                        <th>K</th>
                                        <th>N</th>
                                        <th>t_initial (hrs)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="nodes-container">
                                    @foreach ($nodes as $index => $node)
                                        <tr class="node-row" data-index="{{ $index }}">
                                            <td>
                                                <input type="hidden" name="nodes[{{ $index }}][id]"
                                                    value="{{ $node->id }}">
                                                <div class="form-floating">
                                                    <input type="text" name="nodes[{{ $index }}][key_value]"
                                                        class="form-control key-value"
                                                        value="{{ old('nodes.' . $index . '.key_value', $node->key_value) }}"
                                                        required>
                                                    <label>Key <span class="text-danger">*</span></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating">
                                                    <select name="nodes[{{ $index }}][category]"
                                                        class="form-select node-category" required>
                                                        @foreach (['start', 'end', 'junction', 'component'] as $category)
                                                            <option value="{{ $category }}"
                                                                {{ $node->category == $category ? 'selected' : '' }}>
                                                                {{ ucfirst($category) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label>Category <span class="text-danger">*</span></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating">
                                                    <select name="nodes[{{ $index }}][configuration]"
                                                        class="form-select configuration"
                                                        {{ $node->category != 'component' ? 'disabled' : '' }}>
                                                        @foreach (['single', 'series', 'parallel', 'k-out-of-n'] as $config)
                                                            <option value="{{ $config }}"
                                                                {{ $node->configuration == $config ? 'selected' : '' }}>
                                                                {{ ucfirst($config) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label>Configuration <span class="text-danger">*</span></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating quantity-input"
                                                    style="{{ $node->category == 'component' && $node->configuration != 'single' ? '' : 'display:none' }}">
                                                    <input type="number" min="1"
                                                        name="nodes[{{ $index }}][quantity]"
                                                        class="form-control quantity-value"
                                                        value="{{ old('nodes.' . $index . '.quantity', $node->quantity) }}"
                                                        {{ $node->category == 'component' && $node->configuration != 'single' ? 'required' : '' }}>
                                                    <label>Quantity <span class="text-danger">*</span></label>
                                                </div>
                                                @if ($node->category != 'component' || $node->configuration == 'single')
                                                    <span class="text-muted">1</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-floating">
                                                    <input type="text" name="nodes[{{ $index }}][code]"
                                                        class="form-control"
                                                        value="{{ old('nodes.' . $index . '.code', $node->code) }}">
                                                    <label>Code</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating">
                                                    <input type="text" name="nodes[{{ $index }}][name]"
                                                        class="form-control"
                                                        value="{{ old('nodes.' . $index . '.name', $node->name) }}">
                                                    <label>Name</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating failure-rate-select"
                                                    style="{{ $node->category == 'component' ? '' : 'display:none' }}">
                                                    <select name="nodes[{{ $index }}][failure_rate_id]"
                                                        class="form-select failure-rate-id">
                                                        <option value="">-- Select Failure Rate --</option>
                                                        @foreach ($failureRates as $fr)
                                                            <option value="{{ $fr->id }}"
                                                                {{ $node->failure_rate_id == $fr->id ? 'selected' : '' }}>
                                                                {{ $fr->name }} (λ={{ $fr->failure_rate }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label>Failure Rate</label>
                                                </div>
                                                @if ($node->category != 'component')
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-floating foreign-instance-select"
                                                    style="{{ $node->category == 'component' ? '' : 'display:none' }}">
                                                    <select name="nodes[{{ $index }}][foreign_instance_id]"
                                                        class="form-select foreign-instance-id">
                                                        <option value="">-- Select Foreign Instance --</option>
                                                        @foreach ($otherInstances as $instance)
                                                            <option value="{{ $instance->id }}"
                                                                {{ $node->foreign_instance_id == $instance->id ? 'selected' : '' }}>
                                                                {{ $instance->componentname }} (ID: {{ $instance->id }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <label>Foreign Instance</label>
                                                </div>
                                                @if ($node->category != 'component' || $node->configuration != 'single')
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-floating">
                                                    <input type="number" name="nodes[{{ $index }}][x]"
                                                        class="form-control"
                                                        value="{{ old('nodes.' . $index . '.x', $node->x) }}">
                                                    <label>X</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating">
                                                    <input type="number" name="nodes[{{ $index }}][y]"
                                                        class="form-control"
                                                        value="{{ old('nodes.' . $index . '.y', $node->y) }}">
                                                    <label>Y</label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-floating k-input"
                                                    style="{{ $node->category == 'junction' || ($node->category == 'component' && $node->configuration == 'k-out-of-n') ? '' : 'display:none' }}">
                                                    <input type="number" min="1"
                                                        name="nodes[{{ $index }}][k]" class="form-control k-value"
                                                        value="{{ old('nodes.' . $index . '.k', $node->k) }}"
                                                        {{ $node->category == 'junction' || ($node->category == 'component' && $node->configuration == 'k-out-of-n') ? 'required' : '' }}>
                                                    <label>K <span class="text-danger">*</span></label>
                                                </div>
                                                @if ($node->category != 'junction' && ($node->category != 'component' || $node->configuration != 'k-out-of-n'))
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-floating n-input"
                                                    style="{{ $node->category == 'junction' || ($node->category == 'component' && $node->configuration == 'k-out-of-n') ? '' : 'display:none' }}">
                                                    <input type="number" min="1"
                                                        name="nodes[{{ $index }}][n]" class="form-control n-value"
                                                        value="{{ old('nodes.' . $index . '.n', $node->n) }}"
                                                        {{ $node->category == 'junction' || ($node->category == 'component' && $node->configuration == 'k-out-of-n') ? 'required' : '' }}>
                                                    <label>N <span class="text-danger">*</span></label>
                                                </div>
                                                @if ($node->category != 'junction' && ($node->category != 'component' || $node->configuration != 'k-out-of-n'))
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($node->category === 'component')
                                                    <div class="form-floating">
                                                        <input type="number"
                                                            name="nodes[{{ $index }}][t_initial]"
                                                            class="form-control t-initial-value" step="1"
                                                            value="{{ old('nodes.' . $index . '.t_initial', $node->t_initial ?? 0) }}"
                                                            required>
                                                        <label>t_initial (hrs)</label>
                                                        <small class="text-muted d-block">0 = new, bisa negatif (umur
                                                            awal)</small>
                                                    </div>
                                                @else
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

                        <button type="button" class="btn btn-outline-primary mb-3" id="add-node">
                            <i class="bi bi-plus-circle"></i> Add Node
                        </button>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Save Nodes</button>
                            <a href="{{ route('newrbd.index') }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
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
            const failureRateOptions =
                `@foreach ($failureRates as $fr)<option value="{{ $fr->id }}">{{ $fr->name }} (λ={{ $fr->failure_rate }})</option>@endforeach`;
            const foreignInstanceOptions =
                `<option value="">-- Select Foreign Instance --</option>@foreach ($otherInstances as $instance)<option value="{{ $instance->id }}">{{ $instance->componentname }} (ID: {{ $instance->id }})</option>@endforeach`;

            function addNodeRow(index) {
                const rowHtml = `
                    <tr class="node-row" data-index="${index}">
                     <td>
                        <div class="form-floating">
                            <input type="text" name="nodes[${index}][key_value]" class="form-control key-value" required>
                            <label>Key <span class="text-danger">*</span></label>
                        </div>
                    </td>
                     <td>
                         <div class="form-floating">
                                <select name="nodes[${index}][category]" class="form-select node-category" required>
                                    <option value="start">Start</option>
                                    <option value="end">End</option>
                                    <option value="junction">Junction</option>
                                    <option value="component">Component</option>
                                </select>
                                <label>Category <span class="text-danger">*</span></label>
                            </div>
                        </td>
                        <td>
                            <div class="form-floating">
                                <select name="nodes[${index}][configuration]" class="form-select configuration" disabled>
                                    <option value="single">Single</option>
                                    <option value="series">Series</option>
                                    <option value="parallel">Parallel</option>
                                    <option value="k-out-of-n">k-out-of-n</option>
                                </select>
                                <label>Configuration <span class="text-danger">*</span></label>
                            </div>
                        </td>
                        <td>
                            <div class="form-floating quantity-input" style="display:none">
                                <input type="number" min="1" name="nodes[${index}][quantity]" class="form-control quantity-value">
                                <label>Quantity <span class="text-danger">*</span></label>
                            </div>
                            <span class="text-muted quantity-text">1</span>
                        </td>
                        <td>
                            <div class="form-floating">
                                <input type="text" name="nodes[${index}][code]" class="form-control">
                                <label>Code</label>
                            </div>
                        </td>
                        <td>
                            <div class="form-floating">
                                <input type="text" name="nodes[${index}][name]" class="form-control">
                                <label>Name</label>
                            </div>
                        </td>
                        <td>
                            <div class="form-floating failure-rate-select" style="display:none">
                                <select name="nodes[${index}][failure_rate_id]" class="form-select failure-rate-id">
                                    <option value="">-- Select Failure Rate --</option>
                                    ${failureRateOptions}
                                </select>
                                <label>Failure Rate</label>
                            </div>
                            <span class="text-muted">-</span>
                        </td>
                        <td>
                            <div class="form-floating foreign-instance-select" style="display:none">
                                <select name="nodes[${index}][foreign_instance_id]" class="form-select foreign-instance-id">
                                    ${foreignInstanceOptions}
                                </select>
                                <label>Foreign Instance</label>
                            </div>
                            <span class="text-muted">-</span>
                        </td>
                        <td>
                            <div class="form-floating">
                                <input type="number" name="nodes[${index}][x]" class="form-control">
                                <label>X</label>
                            </div>
                        </td>
                        <td>
                            <div class="form-floating">
                                <input type="number" name="nodes[${index}][y]" class="form-control">
                                <label>Y</label>
                            </div>
                        </td>
                        <td>
                            <div class="form-floating k-input" style="display:none">
                                <input type="number" min="1" name="nodes[${index}][k]" class="form-control k-value">
                                <label>K <span class="text-danger">*</span></label>
                            </div>
                            <span class="text-muted">-</span>
                        </td>
                        <td>
                            <div class="form-floating n-input" style="display:none">
                                <input type="number" min="1" name="nodes[${index}][n]" class="form-control n-value">
                                <label>N <span class="text-danger">*</span></label>
                            </div>
                            <span class="text-muted">-</span>
                        </td>
                        // Di dalam addNodeRow(), ganti kolom t_initial:
                        <td>
                            <div class="form-floating t-initial-input" style="display:none">
                                <input type="number" 
                                    name="nodes[${index}][t_initial]" 
                                    class="form-control t-initial-value" 
                                    step="1" 
                                    value="0">
                                <label>t_initial (hrs)</label>
                                <small class="text-muted d-block">0 = new, bisa negatif (umur awal)</small>
                            </div>
                            <span class="text-muted t-initial-text">-</span>
                        </td>

                        <td>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-node"><i class="bi bi-trash"></i> Remove</button>
                        </td>
                    </tr>`;
                $('#nodes-container').append(rowHtml);
            }

            $('#add-node').click(function() {
                addNodeRow(nodeIndex);
                nodeIndex++;
            });

            $(document).on('click', '.remove-node', function() {
                $(this).closest('.node-row').remove();
            });

            $(document).on('change', '.node-category', function() {
                const row = $(this).closest('.node-row');
                const category = $(this).val();
                const configurationSelect = row.find('.configuration');
                const quantityInput = row.find('.quantity-input');
                const quantityText = row.find('.quantity-text');
                const failureRateCell = row.find('.failure-rate-select');
                const foreignInstanceCell = row.find('.foreign-instance-select');
                const kCell = row.find('.k-input');
                const nCell = row.find('.n-input');
                // --- TAMBAHAN: t_initial ---
                const tInitialInput = row.find('.t-initial-input');
                const tInitialText = row.find('.t-initial-text');

                if (category === 'component') {
                    tInitialInput.show();
                    tInitialText.hide();
                    row.find('.t-initial-value').prop('required', true);
                } else {
                    tInitialInput.hide();
                    tInitialText.show();
                    // JANGAN hapus name! Biarkan tetap ada
                    row.find('.t-initial-value').prop('required', false);
                    // Hapus name agar tidak terkirim
                    row.find('.t-initial-value').removeAttr('name');
                }

                // Toggle visibility
                configurationSelect.prop('disabled', category !== 'component');
                failureRateCell.toggle(category === 'component');
                failureRateCell.find('.text-muted').toggle(category !== 'component');
                foreignInstanceCell.toggle(category === 'component' && configurationSelect.val() ===
                    'single');
                foreignInstanceCell.find('.text-muted').toggle(category !== 'component' ||
                    configurationSelect.val() !== 'single');
                kCell.toggle(category === 'junction' || (category === 'component' && configurationSelect
                    .val() === 'k-out-of-n'));
                kCell.find('.text-muted').toggle(category !== 'junction' && (category !== 'component' ||
                    configurationSelect.val() !== 'k-out-of-n'));
                nCell.toggle(category === 'junction' || (category === 'component' && configurationSelect
                    .val() === 'k-out-of-n'));
                nCell.find('.text-muted').toggle(category !== 'junction' && (category !== 'component' ||
                    configurationSelect.val() !== 'k-out-of-n'));

                // Reset required attributes and values
                row.find('.failure-rate-id').val('').prop('required', false);
                row.find('.foreign-instance-id').val('').prop('required', false);
                row.find('.quantity-value').prop('required', category === 'component' && configurationSelect
                    .val() !== 'single');
                row.find('.k-value').prop('required', category === 'junction' || (category ===
                    'component' && configurationSelect.val() === 'k-out-of-n'));
                row.find('.n-value').prop('required', category === 'junction' || (category ===
                    'component' && configurationSelect.val() === 'k-out-of-n'));

                // Update quantity visibility
                quantityInput.toggle(category === 'component' && configurationSelect.val() !== 'single');
                quantityText.toggle(category !== 'component' || configurationSelect.val() === 'single');
                row.find('.quantity-value').prop('required', category === 'component' && configurationSelect
                    .val() !== 'single');

                // Set required for component nodes with single configuration
                if (category === 'component') {
                    const failureRateVal = row.find('.failure-rate-id').val();
                    const foreignInstanceVal = row.find('.foreign-instance-id').val();
                    row.find('.failure-rate-id').prop('required', !foreignInstanceVal);
                    row.find('.foreign-instance-id').prop('required', !failureRateVal);
                }

                row.find('.is-invalid').removeClass('is-invalid');
                row.find('.invalid-feedback').remove();
            });

            $(document).on('change', '.configuration', function() {
                const row = $(this).closest('.node-row');
                const configuration = $(this).val();
                const quantityInput = row.find('.quantity-input');
                const quantityText = row.find('.quantity-text');
                const kInput = row.find('.k-input');
                const nInput = row.find('.n-input');
                const foreignInstanceCell = row.find('.foreign-instance-select');
                const failureRateCell = row.find('.failure-rate-select');

                // Update quantity visibility
                quantityInput.toggle(configuration !== 'single');
                quantityText.toggle(configuration === 'single');
                row.find('.quantity-value').prop('required', configuration !== 'single');
                if (configuration === 'single') {
                    row.find('.quantity-value').val(1);
                }

                // Update k and n visibility
                kInput.toggle(configuration === 'k-out-of-n');
                kInput.find('.text-muted').toggle(configuration !== 'k-out-of-n');
                nInput.toggle(configuration === 'k-out-of-n');
                nInput.find('.text-muted').toggle(configuration !== 'k-out-of-n');
                row.find('.k-value').prop('required', configuration === 'k-out-of-n');
                row.find('.n-value').prop('required', configuration === 'k-out-of-n');

                // Update foreign instance visibility
                foreignInstanceCell.toggle(configuration === 'single' || configuration === 'parallel' ||
                    configuration === 'series' || configuration === 'k-out-of-n');


                // Update failure rate visibility
                failureRateCell.toggle(true); // Always show for component nodes
                row.find('.failure-rate-id').prop('required', configuration === 'single' && !row.find(
                    '.foreign-instance-id').val());

                row.find('.is-invalid').removeClass('is-invalid');
                row.find('.invalid-feedback').remove();
            });

            $(document).on('change', '.failure-rate-id, .foreign-instance-id', function() {
                const row = $(this).closest('.node-row');
                const category = row.find('.node-category').val();
                const configuration = row.find('.configuration').val();
                if (category !== 'component') return;

                const failureRateSelect = row.find('.failure-rate-id');
                const foreignInstanceSelect = row.find('.foreign-instance-id');
                const failureRateVal = failureRateSelect.val();
                const foreignInstanceVal = foreignInstanceSelect.val();

                // Reset validation states
                failureRateSelect.removeClass('is-invalid');
                foreignInstanceSelect.removeClass('is-invalid');
                row.find('.invalid-feedback').remove();

                // Validate: only one of failure rate or foreign instance can be selected
                if (failureRateVal && foreignInstanceVal) {
                    failureRateSelect.addClass('is-invalid');
                    foreignInstanceSelect.addClass('is-invalid');
                    row.find('.failure-rate-select').append(
                        '<div class="invalid-feedback">Please select either a Failure Rate or a Foreign Instance, not both.</div>'
                    );
                } else if (!failureRateVal && !foreignInstanceVal && configuration === 'single') {
                    failureRateSelect.addClass('is-invalid');
                    foreignInstanceSelect.addClass('is-invalid');
                    row.find('.failure-rate-select').append(
                        '<div class="invalid-feedback">Please select either a Failure Rate or a Foreign Instance for single configuration.</div>'
                    );
                } else {
                    // Update required attributes based on current selection
                    failureRateSelect.prop('required', configuration === 'single' && !foreignInstanceVal);
                    foreignInstanceSelect.prop('required', configuration === 'single' && !failureRateVal);
                }
            });

            $(document).on('input', '.key-value', function() {
                const currentRow = $(this).closest('.node-row');
                const currentKey = $(this).val();
                let hasDuplicate = false;

                $('.key-value').not(this).each(function() {
                    if ($(this).val() === currentKey) {
                        hasDuplicate = true;
                        return false;
                    }
                });

                if (hasDuplicate) {
                    $(this).addClass('is-invalid');
                    currentRow.find('.invalid-feedback').remove();
                    currentRow.find('.form-floating').first().append(
                        '<div class="invalid-feedback">This key is already used in this instance.</div>'
                    );
                } else {
                    $(this).removeClass('is-invalid');
                    currentRow.find('.invalid-feedback').remove();
                }
            });

            $(document).on('input', '.k-value, .n-value', function() {
                const row = $(this).closest('.node-row');
                const kInput = row.find('.k-value');
                const nInput = row.find('.n-value');
                const kVal = parseInt(kInput.val()) || 0;
                const nVal = parseInt(nInput.val()) || 0;

                kInput.removeClass('is-invalid');
                nInput.removeClass('is-invalid');
                row.find('.invalid-feedback').remove();

                if (kVal > nVal && kVal > 0 && nVal > 0) {
                    kInput.addClass('is-invalid');
                    row.find('.k-input').append(
                        '<div class="invalid-feedback">K must not be greater than N.</div>'
                    );
                }
            });

            $('#nodes-form').on('submit', function(e) {
                e.preventDefault();
                const hasDuplicates = $('.key-value.is-invalid').length > 0;
                const hasInvalidComponents = $(
                    '.failure-rate-id.is-invalid, .foreign-instance-id.is-invalid').length > 0;
                const hasInvalidKn = $('.k-value.is-invalid').length > 0;


                if (hasDuplicates || hasInvalidComponents || hasInvalidKn) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fix duplicate keys, invalid component selections, or invalid K/N values.',
                    });
                    return;
                }

                Swal.fire({
                    title: 'Save Node List',
                    text: 'Are you sure you want to save the node list for "{{ $rbdInstance->componentname }}"?',
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
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush
