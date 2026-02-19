@extends('layouts.universal')

@section('container2')
    <div class="content-header weibull-hero">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="weibull-hero-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold">Weibull Failure Rate Analysis</h3>
                            <div class="text-muted small">Dashboard ringkas untuk monitoring pola kegagalan komponen</div>
                        </div>
                    </div>
                </div>

                <ol class="breadcrumb weibull-breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('weibull.dashboard') }}">Weibull Analysis</a>
                    </li>
                    <li class="breadcrumb-item active">Failure Rate</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    @php
        // hanya hitung tampilan (tidak mengubah alur)
        $totalComponents = $components->count();
        $readyComponents = $components->filter(fn($c) => $c->failure_records_count >= 2)->count();
        $waitingComponents = $totalComponents - $readyComponents;
        $totalFailures = $components->sum('failure_records_count');
        $selectedProject = request('project_id');
    @endphp

    <div class="container-fluid px-3">

        {{-- TOOLBAR (lebih cakep + sticky) --}}
        <div class="card border-0 shadow-sm weibull-toolbar mb-3">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('weibull.create') }}" class="btn btn-primary shadow-sm weibull-btn">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Kegagalan
                        </a>

                        <a href="{{ route('weibull.project-dashboard') }}" class="btn btn-info shadow-sm weibull-btn">
                            <i class="fas fa-building mr-1"></i> Kelola Project
                        </a>

                        <a href="{{ route('weibull.batch.index') }}" class="btn btn-warning shadow-sm text-white weibull-btn">
                            <i class="fas fa-layer-group mr-1"></i> Kelola Batch
                        </a>

                        <a href="{{ route('weibull.showupdateexcel') }}" class="btn btn-outline-success weibull-btn">
                            <i class="fas fa-file-excel mr-1"></i> Upload Excel
                        </a>

                        <a href="{{ route('weibull.calculation-method') }}" class="btn btn-outline-secondary weibull-btn">
                            <i class="fas fa-calculator mr-1"></i> Metode (Median Rank)
                        </a>

                        <a href="https://reliability.readthedocs.io/en/latest/How%20does%20Maximum%20Likelihood%20Estimation%20work.html"
                            class="btn btn-outline-secondary weibull-btn" target="_blank" rel="noopener">
                            <i class="fas fa-calculator mr-1"></i> Metode (MLE)
                        </a>

                        <a href="{{ route('weibull.dashboard.download') }}" class="btn btn-success shadow-sm weibull-btn">
                            <i class="fas fa-download mr-1"></i> Download Laporan
                        </a>
                    </div>

                    <div class="weibull-chip">
                        <i class="fas fa-filter"></i>
                        <span>
                            {{ $selectedProject ? 'Filter aktif: Project ID ' . $selectedProject : 'Tanpa filter project' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER + QUICK STATS --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-bold">Filter Project</div>
                            <span class="badge bg-light text-secondary border">Auto apply</span>
                        </div>

                        <form method="GET" action="{{ route('weibull.dashboard') }}">
                            <select name="project_id" class="form-select weibull-select" onchange="this.form.submit()">
                                <option value="">🔹 Semua Project</option>
                                @foreach ($profilesAll as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->projectType->title ?? 'Project #' . $project->id }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <div class="weibull-mini-note mt-3">
                            <i class="fas fa-lightbulb"></i>
                            <div>
                                <div class="fw-semibold">Tips cepat</div>
                                <div class="small text-muted">Minimal <strong>2</strong> data kegagalan untuk membuka detail analisis.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- stats --}}
            <div class="col-lg-8">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="weibull-stat shadow-sm">
                            <div class="stat-icon bg-primary-soft"><i class="fas fa-cubes"></i></div>
                            <div>
                                <div class="stat-label">Total Komponen</div>
                                <div class="stat-value">{{ $totalComponents }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="weibull-stat shadow-sm">
                            <div class="stat-icon bg-success-soft"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <div class="stat-label">Siap Dianalisis</div>
                                <div class="stat-value">{{ $readyComponents }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="weibull-stat shadow-sm">
                            <div class="stat-icon bg-secondary-soft"><i class="fas fa-hourglass-half"></i></div>
                            <div>
                                <div class="stat-label">Menunggu Data</div>
                                <div class="stat-value">{{ $waitingComponents }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="weibull-stat shadow-sm">
                            <div class="stat-icon bg-danger-soft"><i class="fas fa-bolt"></i></div>
                            <div>
                                <div class="stat-label">Total Kegagalan</div>
                                <div class="stat-value">{{ $totalFailures }}</div>
                            </div>
                        </div>
                    </div>

                    <<div class="col-12">
    <div class="weibull-banner-placeholder"></div>
    
</div>

        </div>

        {{-- INFO CARDS (lebih rapi + lebih “premium”) --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm weibull-card collapsed-card h-100">
                    <div class="card-header weibull-card-header">
                        <div>
                            <div class="fw-bold"><i class="fas fa-info-circle mr-2 text-info"></i> Asumsi Analisis</div>
                            <div class="small text-muted">Repairable vs Non-repairable</div>
                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="weibull-box box-danger">
                                    <div class="box-title">Repairable</div>
                                    <div class="box-sub">As good as new</div>
                                    <ul class="small mb-0">
                                        <li>Hitung untuk <strong>MTBF</strong> dengan Weibull</li>
                                        <li>TBF start 0 setelah perbaikan</li>
                                        <li>Jika tidak bisa diperbaiki → start 0 saat unit baru dipasang</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="weibull-box box-info">
                                    <div class="box-title">Non-Repairable</div>
                                    <div class="box-sub">Ganti baru</div>
                                    <ul class="small mb-0">
                                        <li>Hitung untuk <strong>MTTF</strong> (TTF unit baru)</li>
                                        <li>TTF start 0 hanya untuk unit baru</li>
                                        <li>Tidak ada “perbaikan” (langsung replace)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small mt-3 mb-0">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Semakin lengkap data kegagalan, semakin stabil hasil analisis.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm weibull-card collapsed-card h-100">
                    <div class="card-header weibull-card-header">
                        <div>
                            <div class="fw-bold"><i class="fas fa-wave-square mr-2 text-info"></i> Fase Kegagalan (β)</div>
                            <div class="small text-muted">Panduan interpretasi hazard rate</div>
                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="weibull-phase">
                            <div class="phase-item phase-danger">
                                <div class="phase-head">
                                    <span class="weibull-tag tag-danger">INFANT</span>
                                    <span class="fw-bold">β &lt; 0.9</span>
                                </div>
                                <div class="small text-muted">Cacat manufaktur / instalasi / burn-in.</div>
                            </div>

                            <div class="phase-item phase-primary">
                                <div class="phase-head">
                                    <span class="weibull-tag tag-primary">RANDOM</span>
                                    <span class="fw-bold">0.9–1.1</span>
                                </div>
                                <div class="small text-muted">Acak (konstan). Lebih cocok condition/predictive.</div>
                            </div>

                            <div class="phase-item phase-info">
                                <div class="phase-head">
                                    <span class="weibull-tag tag-info">WEAR-OUT</span>
                                    <span class="fw-bold">β &gt; 1.1</span>
                                </div>
                                <div class="small text-muted">Keausan meningkat. Replacement berbasis umur.</div>
                            </div>
                        </div>

                        <div class="weibull-mini-note mt-3">
                            <i class="fas fa-database"></i>
                            <div>
                                <div class="fw-semibold">Catatan</div>
                                <div class="small text-muted">β dihitung dari data TTF. Data akurat = hasil lebih reliabel.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CHARTS (lebih modern: 2 kolom) --}}
        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm weibull-card collapsed-card h-100">
                    <div class="card-header weibull-card-header danger-accent">
                        <div>
                            <div class="fw-bold text-danger"><i class="fas fa-chart-bar mr-2"></i> Tren Kegagalan Bulanan</div>
                            <div class="small text-muted">Jumlah kegagalan per bulan</div>
                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position:relative;height:350px;">
                            <canvas id="failureChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm weibull-card collapsed-card h-100">
                    <div class="card-header weibull-card-header danger-accent">
                        <div>
                            <div class="fw-bold text-danger"><i class="fas fa-chart-bar mr-2"></i> Kegagalan Tahunan per L1</div>
                            <div class="small text-muted">Stacked: L1 vs tahun</div>
                        </div>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="position:relative;height:350px;">
                            <canvas id="failureChartYearlyL1"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card border-0 shadow-sm weibull-card">
            <div class="card-header weibull-card-header danger-accent">
                <div>
                    <div class="fw-bold text-danger"><i class="fas fa-table mr-2"></i> Tabel Komponen</div>
                    <div class="small text-muted">Cari cepat • buka detail • update jumlah komponen untuk MLE</div>
                </div>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success mx-3 mt-3 mb-0">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mx-3 mt-3 mb-0">{{ $errors->first() }}</div>
            @endif

            <div class="card-body">
                <div class="table-responsive">
                    <table id="weibullTable" class="table table-hover table-bordered align-middle weibull-table">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Project</th>
                                <th>L1</th>
                                <th>L2</th>
                                <th>L3</th>
                                <th>L4</th>
                                <th class="text-center">Data Kegagalan</th>
                                <th class="text-center">Jumlah Komponen</th>
                                <th>Tanggal Analisis</th>
                                <th style="width:280px;">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($components as $c)
                                @php
                                    $analysis = $c->weibull;
                                    $hasEnoughData = $c->failure_records_count >= 2;
                                @endphp
                                <tr class="{{ $hasEnoughData ? 'row-ready' : 'row-wait' }}">
                                    <td></td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $c->operationProfile->projectType->title ?? '-' }}
                                        </div>
                                        <div class="small text-muted">Component ID: {{ $c->id }}</div>
                                    </td>

                                    <td>{{ $c->component_l1 }}</td>
                                    <td>{{ $c->component_l2 }}</td>
                                    <td>{{ $c->component_l3 }}</td>
                                    <td>{{ $c->component_l4 }}</td>

                                    <td class="text-center">
                                        <span class="weibull-badge {{ $hasEnoughData ? 'badge-ready' : 'badge-wait' }}">
                                            <i class="fas {{ $hasEnoughData ? 'fa-check' : 'fa-clock' }} mr-1"></i>
                                            {{ $c->failure_records_count }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <input type="number"
                                            min="{{ $c->failure_records_count ?: 1 }}"
                                            class="form-control form-control-sm text-center quantity-input"
                                            value="{{ $c->installed_quantity }}"
                                            data-id="{{ $c->id }}"
                                            placeholder="—"
                                            style="max-width: 120px; display:inline-block;">
                                        <div class="small text-muted mt-1">untuk MLE</div>
                                    </td>

                                    <td>{{ $analysis?->analysis_date ?? '-' }}</td>

                                    <td>
                                        @if ($hasEnoughData)
                                            <div class="d-flex flex-wrap gap-2">
                                                <a href="{{ route('weibull.detail', $c) }}"
                                                    class="btn btn-sm btn-outline-primary shadow-sm weibull-btn-sm">
                                                    <i class="fas fa-chart-line mr-1"></i> Median-Rank
                                                </a>

                                                @if ($c->installed_quantity)
                                                    <a href="{{ route('weibull.likelihood', $c) }}"
                                                        class="btn btn-sm btn-primary shadow-sm weibull-btn-sm">
                                                        <i class="fas fa-calculator mr-1"></i> MLE
                                                    </a>
                                                @else
                                                    <span class="weibull-pill-warn">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i> Isi jumlah komponen
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="weibull-pill-muted">
                                                <i class="fas fa-hourglass-half mr-1"></i> Menunggu data (min 2)
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- STYLE (lebih modern & menarik) --}}
    <style>
        /* Hero */
        .weibull-hero {
            background: linear-gradient(135deg, rgba(13,110,253,.08), rgba(220,53,69,.08));
            border-bottom: 1px solid rgba(0,0,0,.06);
            padding: 16px 0;
        }
        .weibull-hero-icon{
            width: 44px; height: 44px;
            border-radius: 14px;
            display:flex; align-items:center; justify-content:center;
            background: rgba(13,110,253,.12);
            border: 1px solid rgba(13,110,253,.25);
            color: #0d6efd;
            font-size: 18px;
        }
        .weibull-breadcrumb{
            background: rgba(255,255,255,.75);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(0,0,0,.06);
            border-radius: 999px;
            padding: .5rem 1rem;
        }

        /* Toolbar */
        .weibull-toolbar{
            position: sticky;
            top: 8px;
            z-index: 12;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,.85);
            border: 1px solid rgba(0,0,0,.06);
        }
        .weibull-btn{
            border-radius: 12px;
            padding: .55rem .9rem;
        }
        .weibull-chip{
            display:flex;
            align-items:center;
            gap:.5rem;
            padding:.45rem .75rem;
            border-radius: 999px;
            background: rgba(0,0,0,.03);
            border: 1px solid rgba(0,0,0,.06);
            color: #6c757d;
            font-size: .86rem;
        }

        /* Select */
        .weibull-select{
            border-radius: 14px;
            padding: .7rem .9rem;
        }

        /* Mini note */
        .weibull-mini-note{
            display:flex;
            gap:.75rem;
            align-items:flex-start;
            padding: .75rem .9rem;
            border-radius: 16px;
            background: rgba(13,110,253,.05);
            border: 1px solid rgba(13,110,253,.12);
            color: #0d6efd;
        }
        .weibull-mini-note i{ margin-top: 3px; }

        /* Stats */
        .weibull-stat{
            display:flex;
            gap:.75rem;
            align-items:center;
            padding: 14px 14px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(0,0,0,.06);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .weibull-stat:hover{ transform: translateY(-2px); }
        .stat-icon{
            width: 42px; height: 42px;
            border-radius: 16px;
            display:flex; align-items:center; justify-content:center;
            font-size: 16px;
        }
        .bg-primary-soft{ background: rgba(13,110,253,.12); color:#0d6efd; border:1px solid rgba(13,110,253,.18); }
        .bg-success-soft{ background: rgba(25,135,84,.12); color:#198754; border:1px solid rgba(25,135,84,.18); }
        .bg-secondary-soft{ background: rgba(108,117,125,.12); color:#6c757d; border:1px solid rgba(108,117,125,.18); }
        .bg-danger-soft{ background: rgba(220,53,69,.12); color:#dc3545; border:1px solid rgba(220,53,69,.18); }
        .stat-label{ font-size:.82rem; color:#6c757d; }
        .stat-value{ font-size:1.35rem; font-weight:800; line-height: 1.1; }

        /* Banner */
        .weibull-banner{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid rgba(0,0,0,.06);
            background: linear-gradient(135deg, rgba(220,53,69,.06), rgba(13,110,253,.06));
        }
        .banner-icon{
            width: 44px; height: 44px;
            border-radius: 18px;
            display:flex; align-items:center; justify-content:center;
            background: rgba(220,53,69,.12);
            border: 1px solid rgba(220,53,69,.18);
            color: #dc3545;
        }
        .weibull-tag{
            padding: .35rem .65rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 800;
            border: 1px solid rgba(0,0,0,.06);
        }
        .tag-danger{ background: rgba(220,53,69,.12); color:#dc3545; border-color: rgba(220,53,69,.2); }
        .tag-primary{ background: rgba(13,110,253,.12); color:#0d6efd; border-color: rgba(13,110,253,.2); }
        .tag-info{ background: rgba(13,202,240,.12); color:#0dcaf0; border-color: rgba(13,202,240,.2); }

        /* Cards */
        .weibull-card{ border-radius: 18px; overflow:hidden; }
        .weibull-card-header{
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(0,0,0,.06);
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap: 10px;
            padding: 14px 16px;
        }
        .danger-accent{ border-left: 6px solid rgba(220,53,69,.6); }

        /* Info boxes */
        .weibull-box{
            border-radius: 16px;
            padding: 12px 14px;
            border: 1px solid rgba(0,0,0,.06);
            background: rgba(0,0,0,.01);
        }
        .weibull-box ul{ padding-left: 1rem; margin-top: 6px; }
        .box-title{ font-weight: 900; }
        .box-sub{ font-size:.85rem; color:#6c757d; }
        .box-danger{ background: rgba(220,53,69,.06); border-color: rgba(220,53,69,.18); }
        .box-info{ background: rgba(13,202,240,.06); border-color: rgba(13,202,240,.18); }

        /* Phase */
        .weibull-phase{ display:grid; gap: 10px; }
        .phase-item{
            padding: 12px 14px;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,.06);
            background: #fff;
        }
        .phase-head{ display:flex; align-items:center; justify-content:space-between; gap: 10px; }
        .phase-danger{ border-color: rgba(220,53,69,.18); background: rgba(220,53,69,.04); }
        .phase-primary{ border-color: rgba(13,110,253,.18); background: rgba(13,110,253,.04); }
        .phase-info{ border-color: rgba(13,202,240,.18); background: rgba(13,202,240,.04); }

        /* Table */
        .weibull-table thead th{ white-space: nowrap; }
        .weibull-table tbody tr{ transition: background .12s ease; }
        .weibull-table tbody tr:hover{ background: rgba(0,0,0,.02); }
        .row-ready{ }
        .row-wait{ opacity: .92; }

        .weibull-badge{
            display:inline-flex;
            align-items:center;
            gap: .35rem;
            padding: .4rem .7rem;
            border-radius: 999px;
            font-weight: 800;
            font-size: .85rem;
            border: 1px solid rgba(0,0,0,.06);
        }
        .badge-ready{ background: rgba(25,135,84,.12); color:#198754; border-color: rgba(25,135,84,.2); }
        .badge-wait{ background: rgba(108,117,125,.12); color:#6c757d; border-color: rgba(108,117,125,.2); }

        .weibull-btn-sm{ border-radius: 12px; padding: .45rem .65rem; }
        .weibull-pill-warn{
            display:inline-flex; align-items:center;
            padding: .45rem .7rem;
            border-radius: 999px;
            background: rgba(255,193,7,.18);
            border: 1px solid rgba(255,193,7,.25);
            color: #856404;
            font-size: .82rem;
            font-weight: 700;
        }
        .weibull-pill-muted{
            display:inline-flex; align-items:center;
            padding: .45rem .7rem;
            border-radius: 999px;
            background: rgba(0,0,0,.03);
            border: 1px solid rgba(0,0,0,.06);
            color: #6c757d;
            font-size: .82rem;
            font-weight: 700;
        }

        /* Inputs */
        .quantity-input{
            border-radius: 12px;
        }
    </style>

    {{-- CHART --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let chartMonthly = null;
        let chartYearly = null;

        function initMonthlyChart(data) {
            const ctx = document.getElementById('failureChart').getContext('2d');

            chartMonthly = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Jumlah Kegagalan',
                        data: data.values,
                        backgroundColor: 'rgba(220,53,69,0.6)',
                        borderColor: 'rgba(220,53,69,1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        function initYearlyChart(data) {
            const ctx = document.getElementById('failureChartYearlyL1').getContext('2d');

            chartYearly = new Chart(ctx, {
                type: 'bar',
                data: { labels: data.labels, datasets: data.datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { stacked: true },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah Kegagalan' }
                        }
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                title: function(tooltipItems) { return tooltipItems[0].label; },
                                label: function() { return ''; },
                                afterBody: function(tooltipItems) {
                                    return tooltipItems
                                        .map(item => ({ label: item.dataset.label, value: item.raw || 0 }))
                                        .filter(item => item.value > 0)
                                        .sort((a, b) => b.value - a.value)
                                        .map(item => `${item.label} : ${item.value}`);
                                }
                            }
                        },
                        legend: { position: 'top' }
                    }
                }
            });
        }

        function loadCharts(projectId = '') {
            fetch(`{{ route('weibull.failure-chart') }}?project_id=${projectId}`)
                .then(res => res.json())
                .then(data => {
                    if (!chartMonthly) initMonthlyChart(data);
                    else {
                        chartMonthly.data.labels = data.labels;
                        chartMonthly.data.datasets[0].data = data.values;
                        chartMonthly.update();
                    }
                });

            fetch(`{{ route('weibull.failure-chart-yearly-l1') }}?project_id=${projectId}`)
                .then(res => res.json())
                .then(data => {
                    if (!chartYearly) initYearlyChart(data);
                    else {
                        chartYearly.data = data;
                        chartYearly.update();
                    }
                });
        }

        $(document).ready(function() {
            loadCharts(`{{ request('project_id') }}`);
        });

        $(document).on('shown.lte.cardwidget', '.card', function() {
            setTimeout(() => {
                if (chartMonthly) { chartMonthly.resize(); chartMonthly.update('none'); }
                if (chartYearly) { chartYearly.resize(); chartYearly.update('none'); }
            }, 200);
        });
    </script>

    {{-- DATATABLE --}}
    <script>
        $(function() {
            let table = $('#weibullTable').DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                columnDefs: [{ orderable: false, targets: 'no-sort' }]
            });

            function updateRowNumbers() {
                table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }

            table.on('order.dt search.dt draw.dt', updateRowNumbers);
            updateRowNumbers();
        });
    </script>

    {{-- UPDATE QUANTITY --}}
    <script>
        $(document).on('change', '.quantity-input', function() {
            let input = $(this);
            let value = input.val();
            let componentId = input.data('id');

            $.ajax({
                url: `/failurerate/component/${componentId}/quantity`,
                method: 'PATCH',
                data: {
                    installed_quantity: value,
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    input.addClass('is-valid');
                    setTimeout(() => input.removeClass('is-valid'), 1000);
                },
                error: function() {
                    input.addClass('is-invalid');
                }
            });
        });
    </script>
@endpush
