@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <ol class="breadcrumb bg-white px-3 py-2 float-left rounded shadow-sm">
                        <li class="breadcrumb-item"><a href="{{ route('rbd.index') }}"
                                class="text-decoration-none text-primary">RBD Identities</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Blocks</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-3 mb-4">
                    <div class="card-header bg-gradient bg-primary text-white py-3">
                        <h3 class="card-title fw-bold mb-0">Edit Blocks for {{ $rbdIdentity->componentname }}</h3>
                    </div>
                    <div class="card-body p-4">

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('rbd.blocks.update', $rbdIdentity->id) }}" id="blocks-form">
                            @csrf
                            @method('PUT')
                            <div id="blocks-container">
                                @foreach ($blocks as $index => $block)
                                    <div class="block-row mb-3 d-flex align-items-center gap-2" data-index="{{ $index }}">
                                        <input type="hidden" name="blocks[{{ $index }}][id]" value="{{ $block->id }}">

                                        <div class="form-floating flex-grow-1">
                                            <input type="text" name="blocks[{{ $index }}][block_name]"
                                                class="form-control @error('blocks.' . $index . '.block_name') is-invalid @enderror"
                                                value="{{ old('blocks.' . $index . '.block_name', $block->name) }}"
                                                placeholder="Block Name" required>
                                            <label>Block Name</label>
                                            @error('blocks.' . $index . '.block_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-floating flex-grow-1">
                                            <input type="text" name="blocks[{{ $index }}][lambda]"
                                                class="form-control lambda-input @error('blocks.' . $index . '.lambda') is-invalid @enderror"
                                                value="{{ old('blocks.' . $index . '.lambda', $block->lambda) }}"
                                                placeholder="Lambda" required>
                                            <label>Lambda</label>
                                            @error('blocks.' . $index . '.lambda')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-floating flex-grow-1">
                                            <input type="text" name="blocks[{{ $index }}][source]"
                                                class="form-control @error('blocks.' . $index . '.source') is-invalid @enderror"
                                                value="{{ old('blocks.' . $index . '.source', $block->source) }}"
                                                placeholder="Source">
                                            <label>Source</label>
                                            @error('blocks.' . $index . '.source')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="button" class="btn btn-outline-danger btn-sm remove-block">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-outline-primary mb-3" id="add-block">
                                <i class="bi bi-plus-circle"></i> Add Block
                            </button>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg px-4">Save Blocks</button>
                                <a href="{{ route('rbd.index') }}" class="btn btn-outline-secondary btn-lg px-4">Cancel</a>
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
        $(document).ready(function () {
            let blockIndex = {{ count($blocks) }};

            function addBlockRow(index) {
                const blockHtml = `
            <div class="block-row mb-3 d-flex align-items-center gap-2" data-index="${index}">
                <div class="form-floating flex-grow-1">
                    <input type="text" name="blocks[${index}][block_name]"
                           class="form-control" placeholder="Block Name" required>
                    <label>Block Name</label>
                </div>
                <div class="form-floating flex-grow-1">
                    <input type="text" name="blocks[${index}][lambda]"
                           class="form-control lambda-input" placeholder="Lambda" required>
                    <label>Lambda</label>
                </div>
                <div class="form-floating flex-grow-1">
                    <input type="text" name="blocks[${index}][source]"
                           class="form-control" placeholder="Source">
                    <label>Source</label>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm remove-block">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>`;
                $('#blocks-container').append(blockHtml);
            }

            $('#add-block').on('click', function () {
                addBlockRow(blockIndex);
                blockIndex++;
            });

            $(document).on('click', '.remove-block', function () {
                $(this).closest('.block-row').remove();
            });

            $('#blocks-form').on('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Save Block List',
                    text: 'Are you sure you want to save the block list for "{{ $rbdIdentity->componentname }}"?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Save Blocks',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
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