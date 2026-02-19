@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
                <li class="breadcrumb-item">
                    <a href="{{ route('newrbd.index') }}" class="text-decoration-none text-primary">RBD Instances</a>
                </li>
                <li class="breadcrumb-item active">Edit Links</li>
            </ol>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-3 mb-4">
                    <div class="card-header bg-gradient bg-primary text-white py-3">
                        <h3 class="card-title fw-bold mb-0">Edit Links for {{ $rbdInstance->componentname }}</h3>
                    </div>
                    <div class="card-body p-4">

                        {{-- Success / Error Alerts --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- No Nodes Warning --}}
                        @if ($nodes->isEmpty())
                            <div class="alert alert-warning">
                                No nodes available. Please
                                <a href="{{ route('newrbd.nodes.edit', $rbdInstance->id) }}">create nodes</a> first.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('newrbd.links.update', $rbdInstance->id) }}" id="links-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="instance_id" value="{{ $rbdInstance->id }}">

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="links-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40%">From Node</th>
                                            <th width="40%">To Node</th>
                                            <th width="20%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="links-container">
                                        @foreach ($links as $index => $link)
                                            <tr class="link-row">
                                                <td>
                                                    <div class="form-floating">
                                                        <select name="links[{{ $index }}][from_node_id]"
                                                            class="form-select from-node" required>
                                                            <option value="">-- Select From --</option>
                                                            @foreach ($nodes as $node)
                                                                <option value="{{ $node->id }}"
                                                                    {{ $link['from_node_id'] == $node->id ? 'selected' : '' }}>
                                                                    {{ $node->key_value }} ({{ $node->category }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label>From Node <span class="text-danger">*</span></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating">
                                                        <select name="links[{{ $index }}][to_node_id]"
                                                            class="form-select to-node" required>
                                                            <option value="">-- Select To --</option>
                                                            @foreach ($nodes as $node)
                                                                <option value="{{ $node->id }}"
                                                                    {{ $link['to_node_id'] == $node->id ? 'selected' : '' }}>
                                                                    {{ $node->key_value }} ({{ $node->category }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label>To Node <span class="text-danger">*</span></label>
                                                    </div>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm remove-link">
                                                        Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" class="btn btn-outline-primary mb-3" id="add-link"
                                @if ($nodes->isEmpty()) disabled @endif>
                                Add Link
                            </button>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg"
                                    @if ($nodes->isEmpty()) disabled @endif>
                                    Save Links
                                </button>
                                <a href="{{ route('newrbd.index') }}" class="btn btn-outline-secondary btn-lg">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
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
            let linkIndex = {{ $links->count() }};
            const nodeOptions =
                `@foreach ($nodes as $node)<option value="{{ $node->id }}">{{ $node->key_value }} ({{ $node->category }})</option>@endforeach`;
            const hasNodes = {{ $nodes->isNotEmpty() ? 'true' : 'false' }};

            // Tambah baris link baru
            function addLinkRow(index) {
                const rowHtml = `
        <tr class="link-row">
            <td>
                <div class="form-floating">
                    <select name="links[${index}][from_node_id]" class="form-select from-node" required>
                        <option value="">-- Select From --</option>
                        ${nodeOptions}
                    </select>
                    <label>From Node <span class="text-danger">*</span></label>
                </div>
            </td>
            <td>
                <div class="form-floating">
                    <select name="links[${index}][to_node_id]" class="form-select to-node" required>
                        <option value="">-- Select To --</option>
                        ${nodeOptions}
                    </select>
                    <label>To Node <span class="text-danger">*</span></label>
                </div>
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-outline-danger btn-sm remove-link">Remove</button>
            </td>
        </tr>`;
                $('#links-container').append(rowHtml);
            }

            // Event: Tambah link
            $('#add-link').on('click', function() {
                if (!hasNodes) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Nodes',
                        text: 'Create nodes first.',
                        confirmButtonText: 'Go to Nodes'
                    }).then(r => r.isConfirmed && (window.location =
                        `{{ route('newrbd.nodes.edit', $rbdInstance->id) }}`));
                } else {
                    addLinkRow(linkIndex++);
                }
            });

            // Event: Hapus baris
            $(document).on('click', '.remove-link', function() {
                $(this).closest('tr').remove();
            });

            // Validasi real-time saat pilih node
            $(document).on('change', '.from-node, .to-node', function() {
                const $select = $(this);
                const $row = $select.closest('.link-row');
                const from = $row.find('.from-node').val();
                const to = $row.find('.to-node').val();

                // Reset feedback
                $row.find('.form-select').removeClass('is-invalid');
                $row.find('.invalid-feedback').remove();

                // Cek kosong
                if ($select.val() === '') {
                    markInvalid($select, 'This field is required.');
                    return;
                }

                // Cek self-link & duplikat hanya jika keduanya terpilih
                if (from && to) {
                    if (from === to) {
                        markInvalid($row.find('.to-node'), 'Cannot link a node to itself.');
                    }

                    let isDuplicate = false;
                    $('.link-row').not($row).each(function() {
                        if ($(this).find('.from-node').val() === from && $(this).find('.to-node')
                            .val() === to) {
                            isDuplicate = true;
                            return false;
                        }
                    });
                    if (isDuplicate) {
                        markInvalid($row.find('.to-node'), 'Duplicate link.');
                    }
                }
            });

            function markInvalid($el, message) {
                $el.addClass('is-invalid');
                $el.closest('.form-floating').append(`<div class="invalid-feedback d-block">${message}</div>`);
            }

            // Submit dengan validasi ketat
            $('#links-form').on('submit', function(e) {
                e.preventDefault();

                let hasError = false;

                // Cek minimal 1 link
                if ($('.link-row').length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Links',
                        text: 'Add at least one link.'
                    });
                    return;
                }

                // Cek semua select terpilih
                $('.link-row').each(function() {
                    const $row = $(this);
                    const from = $row.find('.from-node').val();
                    const to = $row.find('.to-node').val();

                    if (!from || !to) {
                        hasError = true;
                        if (!from) markInvalid($row.find('.from-node'), 'This field is required.');
                        if (!to) markInvalid($row.find('.to-node'), 'This field is required.');
                    }
                });

                if (hasError) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Incomplete Links',
                        text: 'Please select both From and To for all links.'
                    });
                    return;
                }

                if ($('.is-invalid').length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fix Errors',
                        text: 'Check self-links and duplicates.'
                    });
                    return;
                }

                // Konfirmasi simpan
                Swal.fire({
                    title: 'Save Links?',
                    text: 'This will replace all existing links.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Save',
                    cancelButtonText: 'Cancel'
                }).then(result => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .is-invalid~.invalid-feedback,
        .invalid-feedback.d-block {
            display: block !important;
        }

        .form-floating>.form-select.is-invalid~label {
            color: #dc3545;
        }
    </style>
@endpush
