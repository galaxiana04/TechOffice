@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('weibull.dashboard') }}">Weibull Analysis</a></li>
                        <li class="breadcrumb-item active">Detail Komponen</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <!-- Header Card dengan gradient -->
    <div class="card mb-5 shadow-lg border-0 overflow-hidden">
        <div class="card-header bg-gradient-primary text-white py-4">
            <h5 class="mb-0 text-xl font-semibold">
                {{ $component->component_l1 ?? '' }}
                {{ $component->component_l2 ? ' / ' . $component->component_l2 : '' }}
                {{ $component->component_l3 ? ' / ' . $component->component_l3 : '' }}
                {{ $component->component_l4 ? ' / ' . $component->component_l4 : '' }}
            </h5>
        </div>
        <div class="card-body bg-gray-50">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <p class="mb-2 text-lg"><strong>Jumlah Event Kegagalan:</strong>
                        <span class="badge bg-danger fs-4 ms-2 px-4 py-2">
                            {{ $component->failureRecords->count() }}
                        </span>
                    </p>
                    @if ($component->installed_quantity && $isconsidertotalcomponent)
                        <p class="mb-2 text-lg"><strong>Jumlah Komponen Identik:</strong>
                            <span class="badge bg-danger fs-4 ms-2 px-4 py-2">
                                {{ $component->installed_quantity }}
                            </span>
                        </p>
                    @endif
                    <p class="mb-0 text-lg"><strong>Analisis Terbaru:</strong>
                        <span class="text-primary fw-bold">
                            {{ $latest?->analysis_date ?? 'Belum dianalisis' }}
                        </span>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="fas fa-cogs fa-5x text-primary opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    @if ($latest && $component->failureRecords->count() >= 2)
        <div class="row mb-5">
            <!-- Parameter Weibull -->
            <div class="col-lg-5">
                <div class="card h-100 shadow-lg border-0">
                    <div class="card-header bg-gradient-info text-white">
                        <h6 class="mb-0 fw-bold">Median Rank Method</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-4">
                                <p class="text-muted small">β (Shape Parameter)</p>
                                <h3 class="fw-bold text-info">
                                    {{ number_format($latest->beta, 4, ',', '.') }}
                                </h3>
                            </div>
                            <div class="col-6 mb-4">
                                <p class="text-muted small">η (Characteristic Life)</p>
                                <h4 class="fw-bold">
                                    {{ number_format($latest->eta, 0, ',', '.') }} jam
                                </h4>
                            </div>
                            <div class="col-6">
                                <p class="text-muted small">B10 Life</p>
                                <h4 class="fw-bold text-success">
                                    {{ number_format($latest->b10, 0, ',', '.') }} jam
                                </h4>
                            </div>
                            <div class="col-6">
                                <p class="text-muted small">B25 Life</p>
                                <h4 class="fw-bold text-warning">
                                    {{ number_format($latest->b25, 0, ',', '.') }} jam
                                </h4>
                            </div>
                            <div class="col-6">
                                <p class="text-muted small">{{ strtoupper($latest->meanlifetype) }} Aktual</p>
                                <h4 class="fw-bold">
                                    {{ number_format($latest->meanlife_actual, 0, ',', '.') }} jam
                                </h4>
                            </div>
                            <div class="col-6">
                                <p class="text-muted small">{{ strtoupper($latest->meanlifetype) }} Gamma</p>
                                <h4 class="fw-bold">
                                    {{ number_format($latest->meanlife_gammafunction, 0, ',', '.') }} jam
                                </h4>
                            </div>
                        </div>

                        <!-- Fase Kegagalan – data dari Controller -->
                        @if ($failurePhase)
                            <div class="alert alert-{{ $failurePhase->badge }} text-center mt-4">
                                <h5 class="fw-bold mb-1">{{ $failurePhase->phase }}</h5>
                                <small>{{ $failurePhase->description }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- CDF Plot -->
            <div class="col-lg-7">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-primary text-white">
                        <h6 class="mb-0 fw-bold">Cumulative Distribution Function (CDF)</h6>
                    </div>
                    <div class="card-body p-4 bg-gray-50">
                        <canvas id="cdfChart" height="350"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reliability & Hazard Chart -->
        <div class="row mb-5 g-4">
            <div class="col-12 col-xl-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-success text-white">
                        <h6 class="mb-0 fw-bold">Reliability Function (Survival)</h6>
                    </div>
                    <div class="card-body p-4 bg-gray-50">
                        <canvas id="reliabilityChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-xl-6">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient-danger text-white">
                        <h6 class="mb-0 fw-bold">Hazard Rate λ(t)</h6>
                    </div>
                    <div class="card-body p-4 bg-gray-50">
                        <canvas id="hazardChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Alert jika data kurang -->
        <div class="alert alert-warning rounded-3 shadow-lg text-center py-5 border-0">
            <i class="fas fa-exclamation-triangle fa-5x mb-4 text-warning opacity-75"></i>
            <h3 class="fw-bold">Belum Cukup Data</h3>
            <p class="lead">Minimal <strong>2 event kegagalan</strong> untuk analisis Weibull akurat.</p>
        </div>
    @endif

    <!-- Daftar Event Kegagalan -->
    <h4 class="mt-5 mb-4">Daftar Event Kegagalan</h4>
    <p class="text-muted small mb-4">
        Time to Failure (TTF) dihitung otomatis dari tanggal mulai operasi hingga tanggal & waktu kegagalan.
    </p>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th width="5%">No</th>
                    <th>Tanggal Mulai Operasi</th>
                    <th>Tanggal Kegagalan</th>
                    <th>Waktu Kegagalan</th>
                    <th class="text-end">TTF (jam)</th>
                    <th>Jenis Service</th>
                    <th>Apakah komponen baru/replacement?</th>
                    <th>Trainset</th>
                    <th>No. KA</th>
                    <th>Tipe/ Car</th>
                    <th>Relasi</th>
                    <th>Temuan</th>
                    <th>Solution</th>
                    <th>Klasifikasi Penyebab</th>
                    <th>Batch ID</th>
                    <th>Link</th>
                    <th>Sumber File</th>
                </tr>
            </thead>
            <tbody>
                @forelse($component->failureRecords as $record)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->start_date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->failure_date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->failure_time)->format('H:i') }}</td>
                        <td class="text-end fw-bold">{{ number_format($record->ttf_hours, 1, ',', '.') }}</td>
                        <td>{{ $record->service_type ?? '-' }}</td>
                        <td>{{ $record->is_new ? 'Ya' : 'Tidak' }}</td>
                        <td>{{ $record->trainset ?? '-' }}</td>
                        <td>{{ $record->train_no ?? '-' }}</td>
                        <td>{{ $record->car_type ?? '-' }}</td>
                        <td>{{ $record->relation ?? '-' }}</td>
                        <td>{{ $record->problemdescription ?? '-' }}</td>
                        <td>{{ $record->solution ?? '-' }}</td>
                        <td>{{ $record->cause_classification ?? '-' }}</td>
                        <td>{{ basename($record->import_batch_id ?? '-') }}</td>
                        <td>{{ $record->support_link ?? '-' }}</td>
                        <td><code>{{ basename($record->source_file ?? '-') }}</code></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i><br>
                            Belum ada record kegagalan untuk komponen ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>



    @if ($chartData)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const data = @json($chartData);

                // Format angka Indonesia
                function formatIndo(value, decimals = 4) {
                    if (value === 0) return '0';
                    if (Math.abs(value) < 0.0001) {
                        return value.toExponential(decimals).replace('.', ',');
                    }
                    return value.toLocaleString('id-ID', {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: Math.max(decimals, 6)
                    });
                }

                // CDF Chart
                new Chart(document.getElementById('cdfChart'), {
                    type: 'line',
                    data: {
                        labels: data.t,
                        datasets: [{
                                label: 'Weibull CDF (Teori)',
                                data: data.cdf.map(v => v * 100),
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13, 110, 253, 0.08)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0
                            },
                            {
                                label: 'Data Empiris (Median Rank)',
                                data: data.empirical.map(p => ({
                                    x: p.x,
                                    y: p.y * 100
                                })),
                                type: 'scatter',
                                backgroundColor: '#dc3545',
                                pointRadius: 8,
                                pointHoverRadius: 10
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            annotation: {
                                annotations: {
                                    b10: {
                                        type: 'line',
                                        xMin: data.b10,
                                        xMax: data.b10,
                                        borderColor: '#198754',
                                        borderWidth: 3,
                                        borderDash: [8, 5],
                                        label: {
                                            content: 'B10 Life',
                                            enabled: true,
                                            backgroundColor: '#198754'
                                        }
                                    },
                                    b25: {
                                        type: 'line',
                                        xMin: data.b25,
                                        xMax: data.b25,
                                        borderColor: '#fd7e14',
                                        borderWidth: 3,
                                        borderDash: [8, 5],
                                        label: {
                                            content: 'B25 Life',
                                            enabled: true,
                                            backgroundColor: '#fd7e14'
                                        }
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: context => context.dataset.label + ': ' + context.parsed.y.toFixed(
                                        2) + '%'
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Waktu Operasi (jam)'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Probabilitas Kegagalan (%)'
                                },
                                min: 0,
                                max: 100,
                                ticks: {
                                    callback: v => v + '%'
                                }
                            }
                        }
                    }
                });

                // Reliability Chart
                new Chart(document.getElementById('reliabilityChart'), {
                    type: 'line',
                    data: {
                        labels: data.t,
                        datasets: [{
                            label: 'Reliability R(t) = 1 - CDF',
                            data: data.cdf.map(v => (1 - v) * 100),
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, 0.08)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: context => context.dataset.label + ': ' + context.parsed.y.toFixed(
                                        2) + '%'
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Waktu Operasi (jam)'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Probabilitas Masih Berfungsi (%)'
                                },
                                min: 0,
                                max: 100,
                                ticks: {
                                    callback: v => v + '%'
                                }
                            }
                        }
                    }
                });

                // Hazard Rate Chart
                new Chart(document.getElementById('hazardChart'), {
                    type: 'line',
                    data: {
                        labels: data.t,
                        datasets: [{
                            label: 'Hazard Rate λ(t)',
                            data: data.hazard,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.15)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Hazard Function - Pola Tingkat Kegagalan λ(t)'
                            },
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                titleFont: {
                                    size: 14
                                },
                                bodyFont: {
                                    size: 15
                                },
                                padding: 12,
                                callbacks: {
                                    title: ctx => 'Waktu: ' + formatIndo(ctx[0].parsed.x, 0) + ' jam',
                                    label: ctx => 'λ(t): ' + formatIndo(ctx.parsed.y, 6) + ' per jam'
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Waktu Operasi (jam)'
                                },
                                ticks: {
                                    callback: v => formatIndo(v, 0)
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Tingkat Kegagalan (per jam)'
                                },
                                beginAtZero: true,
                                ticks: {
                                    callback: v => formatIndo(v, 6)
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            });
        </script>
    @endif
@endsection
