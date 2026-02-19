@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <ol class="breadcrumb bg-white px-3 py-2 float-left rounded shadow-sm">
                        <li class="breadcrumb-item"><a href="{{ route('fta.index') }}"
                                class="text-decoration-none text-primary">FTA Identities</a></li>
                        <li class="breadcrumb-item active">Edit Events</li>
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
                        <h3 class="card-title fw-bold mb-0">Edit Events for {{ $ftaIdentity->componentname }}</h3>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('fta.events.update', $ftaIdentity->id) }}" id="events-form">
                            @csrf
                            @method('PUT')
                            <div id="events-container">
                                @foreach ($events as $index => $event)
                                    <div class="event-row mb-3 d-flex align-items-center gap-2"
                                        data-index="{{ $index }}">
                                        <input type="hidden" name="events[{{ $index }}][id]"
                                            value="{{ $event->id }}">

                                        <div class="form-floating flex-grow-1">
                                            <select name="events[{{ $index }}][fmeca_item_id]"
                                                class="form-control @error('events.' . $index . '.fmeca_item_id') is-invalid @enderror"
                                                required>
                                                @foreach ($availableFmecaItems as $fmeca)
                                                    <option value="{{ $fmeca->id }}"
                                                        {{ $event->fmeca_item_id == $fmeca->id ? 'selected' : '' }}>
                                                        {{ $fmeca->item_name }}|||
                                                        {{ $fmeca->subsystem ?? '-' }}|||
                                                        (λ={{ $fmeca->failure_rate }})
                                                        |||
                                                        {{ $fmeca->failure_effect_system ?? '-' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label>FMECA Item</label>
                                            @error('events.' . $index . '.fmeca_item_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-floating flex-grow-1">
                                            <input type="text" name="events[{{ $index }}][name]"
                                                class="form-control @error('events.' . $index . '.name') is-invalid @enderror"
                                                value="{{ old('events.' . $index . '.name', $event->name) }}"
                                                placeholder="Event Name" required>
                                            <label>Event Name</label>
                                            @error('events.' . $index . '.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-floating flex-grow-1">
                                            <input type="text" name="events[{{ $index }}][source]"
                                                class="form-control @error('events.' . $index . '.source') is-invalid @enderror"
                                                value="{{ old('events.' . $index . '.source', $event->source) }}"
                                                placeholder="Source">
                                            <label>Source</label>
                                            @error('events.' . $index . '.source')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="button" class="btn btn-outline-danger btn-sm remove-event">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-outline-primary mb-3" id="add-event">
                                <i class="bi bi-plus-circle"></i> Add Event
                            </button>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg px-4">Save Events</button>
                                <a href="{{ route('fta.index') }}" class="btn btn-outline-secondary btn-lg px-4">Cancel</a>
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
            let eventIndex = {{ count($events) }};

            function addEventRow(index) {
                const eventHtml = `
                    <div class="event-row mb-3 d-flex align-items-center gap-2" data-index="${index}">
                        <div class="form-floating flex-grow-1">
                            <select name="events[${index}][fmeca_item_id]" class="form-control" required>
                                @foreach ($availableFmecaItems as $fmeca)
                                    <option value="{{ $fmeca->id }}">
                                    {{ $fmeca->item_name }}|||
                                    {{ $fmeca->subsystem ?? '-' }}|||
                                    {{ $fmeca->failure_mode }}|||
                                    (λ={{ $fmeca->failure_rate }})|||
                                    {{ $fmeca->failure_effect_system ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            <label>FMECA Item</label>
                        </div>
                        <div class="form-floating flex-grow-1">
                            <input type="text" name="events[${index}][name]" class="form-control" placeholder="Event Name" required>
                            <label>Event Name</label>
                        </div>
                        <div class="form-floating flex-grow-1">
                            <input type="text" name="events[${index}][source]" class="form-control" placeholder="Source">
                            <label>Source</label>
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-event">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>`;
                $('#events-container').append(eventHtml);
            }

            $('#add-event').on('click', function() {
                addEventRow(eventIndex);
                eventIndex++;
            });

            $(document).on('click', '.remove-event', function() {
                $(this).closest('.event-row').remove();
            });

            $('#events-form').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Save Event List',
                    text: 'Are you sure you want to save the event list for "{{ $ftaIdentity->componentname }}"?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Save Events',
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
