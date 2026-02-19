@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('newrbd.index') }}">RBD Management</a></li>
                        <li class="breadcrumb-item active">RBD Instances</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
        <div class="col-md-12">
            {{-- Card utama --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title fw-bold mb-0">RBD Management</h3>
                </div>
                <div class="card-body">
                    {{-- Pesan Sukses atau Error --}}
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

                    {{-- Tabs Navigation --}}
                    <ul class="nav nav-tabs" id="rbdTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="instances-tab" data-bs-toggle="tab"
                                data-bs-target="#instances" type="button" role="tab" aria-controls="instances"
                                aria-selected="true">RBD Instances</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="failure-rates-tab" data-bs-toggle="tab"
                                data-bs-target="#failure-rates" type="button" role="tab" aria-controls="failure-rates"
                                aria-selected="false">Failure Rates</button>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="rbdTabsContent">
                        {{-- Tab 1: RBD Instances --}}
                        <div class="tab-pane fade show active" id="instances" role="tabpanel"
                            aria-labelledby="instances-tab">
                            <div class="mt-4">
                                <button class="btn btn-primary mb-3" onclick="showCreateRbdForm()">Create New RBD
                                    Instance</button>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped align-middle" id="rbd-instances-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Component Name</th>
                                                <th>Time Interval</th>
                                                <th>Nodes Count</th>
                                                <th>Links Count</th>
                                                <th>Reliability</th>
                                                <th>Failure Rate</th>
                                                <th>Created by</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($instances as $instance)
                                                <tr>
                                                    <td>{{ $instance->id }}</td>
                                                    <td>{{ $instance->componentname }}</td>
                                                    <td>{{ number_format($instance->time_interval, 2) }} hours</td>
                                                    <td>{{ $instance->nodes_count }}</td>
                                                    <td>{{ $instance->links_count }}</td>
                                                    <td class="text-end font-mono">
                                                        @if ($instance->temporary_reliability_value)
                                                            @if ($instance->temporary_reliability_value < 1e-5)
                                                                <span
                                                                    class="text-danger">{{ sprintf('%.6E', $instance->temporary_reliability_value) }}</span>
                                                            @else
                                                                {{ number_format($instance->temporary_reliability_value, 6) }}
                                                            @endif
                                                        @else
                                                            <span class="text-muted">0.000000</span>
                                                        @endif
                                                    </td>
                                                    </td>
                                                    <td>{{ sprintf('%.2E', $instance->temporary_failure_rate_value) }}</td>
                                                    <td>{{ $instance->user->name }}</td>
                                                    <td>
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            <a href="{{ $instance->diagram_url }}" target="_blank"
                                                                class="btn btn-sm bg-maroon shadow-sm" title="View">
                                                                <i class="bi bi-eye"></i> Concept
                                                            </a>
                                                            <a href="{{ route('newrbd.show', $instance->id) }}"
                                                                class="btn btn-sm bg-teal shadow-sm" title="View">
                                                                <i class="bi bi-eye"></i> View
                                                            </a>
                                                            <form action="{{ route('newrbd.calculate', $instance->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-success calculate-btn"
                                                                    title="Calculate Reliability"
                                                                    data-id="{{ $instance->id }}"
                                                                    data-component="{{ $instance->componentname }}">
                                                                    <i class="bi bi-calculator"></i> Calculate
                                                                </button>
                                                            </form>
                                                            <form class="d-inline expression-form"
                                                                data-id="{{ $instance->id }}"
                                                                data-component="{{ $instance->componentname }}">
                                                                @csrf
                                                                <button type="button"
                                                                    class="btn btn-sm bg-pink shadow-sm expression-btn"
                                                                    title="Calculate Expression"
                                                                    data-id="{{ $instance->id }}">
                                                                    <i class="bi bi-calculator"></i> Expression
                                                                </button>
                                                            </form>
                                                            @if (Auth::id() === 1 || Auth::id() === $instance->user_id)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-danger shadow-sm delete-btn"
                                                                    title="Delete" data-id="{{ $instance->id }}"
                                                                    data-component="{{ $instance->componentname }}"
                                                                    onclick="confirmDelete({{ $instance->id }})">
                                                                    <i class="bi bi-trash"></i> Delete
                                                                </button>
                                                            @endif
                                                            <a href="{{ route('newrbd.nodes.edit', $instance->id) }}"
                                                                class="btn btn-sm btn-outline-primary shadow-sm edit-node-btn"
                                                                title="Edit Node List" data-id="{{ $instance->id }}"
                                                                data-component="{{ $instance->componentname }}">
                                                                <i class="bi bi-pencil"></i> Edit Nodes
                                                            </a>
                                                            <a href="{{ route('newrbd.links.edit', $instance->id) }}"
                                                                class="btn btn-sm btn-outline-primary shadow-sm edit-block-btn"
                                                                title="Edit Link List" data-id="{{ $instance->id }}"
                                                                data-component="{{ $instance->componentname }}">
                                                                <i class="bi bi-layout-text-sidebar-reverse"></i> Edit
                                                                Links
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Failure Rates --}}
                        <div class="tab-pane fade" id="failure-rates" role="tabpanel"
                            aria-labelledby="failure-rates-tab">
                            <div class="mt-4">
                                <button class="btn btn-primary mb-3" onclick="showCreateFailureRateForm()">Add New
                                    Failure Rate</button>

                                <div class="table-responsive">
                                    <table class="table table-hover table-striped align-middle" id="failure-rates-table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Failure Rate (λ)</th>
                                                <th>Source</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($failureRates as $failureRate)
                                                <tr>
                                                    <td>{{ $failureRate->id }}</td>
                                                    <td>{{ $failureRate->name }}</td>
                                                    <td>{{ sprintf('%.2E', $failureRate->failure_rate) }}</td>
                                                    <td>{{ $failureRate->source ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> {{-- card-body --}}
            </div> {{-- card utama --}}
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Event untuk tombol Expression
            $(document).on('click', '.expression-btn', function() {
                const instanceId = $(this).data('id');
                const componentName = $(this).closest('form').data('component');

                Swal.fire({
                    title: `Calculate Expression for "${componentName}"`,
                    html: `
                        <div class="text-start">
                            <label for="ordo" class="form-label">Ordo (default: 2)</label>
                            <input type="number" id="ordo" class="form-control" value="2" min="1" max="10" step="1">
                            <small class="text-muted">Nilai ordo untuk perhitungan hazard rate</small>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Calculate',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const ordo = $('#ordo').val();
                        if (!ordo || ordo < 1) {
                            Swal.showValidationMessage('Ordo harus minimal 1');
                            return false;
                        }
                        return {
                            ordo: parseInt(ordo)
                        };
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        const ordo = result.value.ordo;

                        // Show loading
                        Swal.fire({
                            title: 'Calculating...',
                            text: 'Mengirim ke Python server...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // GUNAKAN AJAX (jQuery)
                        $.ajax({
                            url: "{{ route('newrbd.failureratecalculateAndSendToPython', ':id') }}"
                                .replace(':id', instanceId),
                            method: 'POST',
                            data: JSON.stringify({
                                ordo: ordo
                            }),
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            contentType: 'application/json',
                            timeout: 600000, // 10 menit
                            success: function(data) {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        html: `
                                            <div class="text-start">
                                                <strong>R(t):</strong> <code>${data.R_t_symbolic || 'N/A'}</code><br>
                                                <strong>h(t):</strong> <code>${data.hazard_rate_expression || 'N/A'}</code><br>
                                                <strong>f(t):</strong> <code>${data.frequency_expression || 'N/A'}</code><br>
                                                <strong>t_expr:</strong> <code>${data.t_expression || 'N/A'}</code><br>
                                                <strong>t_value:</strong> ${data.t_value || 'N/A'}<br>
                                                <strong>Ordo:</strong> ${ordo}
                                            </div>
                                        `,
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error', data.error || 'Gagal menghitung',
                                        'error');
                                }
                            },
                            error: function(xhr) {
                                let errorMsg = 'Terjadi kesalahan jaringan atau server';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    errorMsg = xhr.responseJSON.error;
                                } else if (xhr.responseText) {
                                    errorMsg += ': ' + xhr.responseText.substring(0,
                                        200);
                                }
                                Swal.fire('Error', errorMsg, 'error');
                            }
                        });
                    }
                });
            });
        });
        // Function to show SweetAlert form for creating RBD Instance
        // Create Instance (otomatis isi model_id)
        function showCreateRbdForm() {
            Swal.fire({
                title: 'Create Instance for "{{ $model->name }}"',
                html: `
                    <input type="hidden" id="new_rbd_model_id" value="{{ $model->id }}">
                    <div class="form-floating mb-3">
                        <input type="text" id="componentname" class="form-control" required>
                        <label>Component Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" id="time_interval" class="form-control" step="0.01" min="0.01" required>
                        <label>Time Interval (hours)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="url" id="diagram_url" class="form-control">
                        <label>Diagram URL (optional)</label>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    const data = {
                        new_rbd_model_id: {{ $model->id }},
                        componentname: $('#componentname').val(),
                        time_interval: $('#time_interval').val(),
                        diagram_url: $('#diagram_url').val() || null
                    };
                    if (!data.componentname || !data.time_interval) {
                        Swal.showValidationMessage('Fill required fields');
                        return false;
                    }
                    return data;
                }
            }).then(r => {
                if (r.isConfirmed) {
                    fetch("{{ route('newrbd.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(r.value)
                        })
                        .then(res => res.json())
                        .then(d => {
                            if (d.success) {
                                Swal.fire('Success', d.success, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', d.error, 'error');
                            }
                        });
                }
            });
        }

        // Function to show SweetAlert form for creating Failure Rate
        function showCreateFailureRateForm() {
            Swal.fire({
                title: 'Add New Failure Rate',
                html: `
                    <div class="form-floating mb-3">
                        <input type="text" id="name" class="form-control" placeholder="Failure Rate Name" required>
                        <label for="name">Failure Rate Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" id="failure_rate" class="form-control" placeholder="Failure Rate (λ)" required
                            pattern="^([0-9]+(\.[0-9]+)?([eE][-+]?[0-9]+)?)$"
                            title="Enter a number or scientific notation (e.g., 0.000001 or 9.18E-09)">
                        <label for="failure_rate">Failure Rate (λ)</label>
                        <small class="text-muted">Example: <code>0.00000000918</code> or <code>9.18E-09</code></small>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea id="source" class="form-control" placeholder="Source"></textarea>
                        <label for="source">Source (optional)</label>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                preConfirm: () => {
                    const new_rbd_model_id = {{ $model->id }};
                    const name = document.getElementById('name').value;
                    const failure_rate = document.getElementById('failure_rate').value;
                    const source = document.getElementById('source').value;

                    // Client-side validation
                    if (!name || !failure_rate) {
                        Swal.showValidationMessage('Please fill in all required fields');
                        return false;
                    }
                    if (!/^([0-9]+(\.[0-9]+)?([eE][-+]?[0-9]+)?)$/.test(failure_rate)) {
                        Swal.showValidationMessage(
                            'Failure rate must be a valid number or scientific notation');
                        return false;
                    }

                    return {
                        new_rbd_model_id,
                        name,
                        failure_rate,
                        source
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send AJAX request to store failure rate
                    fetch("{{ route('newrbd.failure-rates.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                _token: '{{ csrf_token() }}',
                                ...result.value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Success!', data.success, 'success').then(() => {
                                    location.reload(); // Reload to reflect new failure rate
                                });
                            } else {
                                Swal.fire('Error!', data.error || 'Failed to create failure rate', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred: ' + error.message, 'error');
                        });
                }
            });
        }

        function confirmDelete(id) {
            const componentName = document.querySelector(`.delete-btn[data-id="${id}"]`).getAttribute('data-component');
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete "${componentName}". This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ url('newrbd/newrbdinstances') }}/" + id, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Deleted!', data.success, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', data.error || 'Failed to delete RBD instance', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An error occurred: ' + error.message, 'error');
                        });
                }
            });
        }

        // Helper function to validate URL
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }
    </script>
@endpush
