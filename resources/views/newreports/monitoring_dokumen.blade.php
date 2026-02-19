@extends('layouts.universal')

@section('title', 'Monitoring Dokumen')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-file-signature mr-2 text-primary"></i> Monitoring Progres Dokumen
                </h1>
                <p class="text-muted mb-0 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Dashboard status, realisasi dokumen per periode, dan statistik unit
                </p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right bg-white shadow-sm px-3 py-2 mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home mr-1"></i>Home</a></li>
                    <li class="breadcrumb-item active">Monitoring Dokumen</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')

    {{-- Filter Section --}}
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('monitoring.dokumen') }}" method="GET" class="d-flex align-items-center">
                <label class="mr-3 font-weight-bold mb-0 text-muted"><i class="fas fa-filter mr-1"></i> Filter Project:</label>
                <select name="project" class="form-control form-control-sm mr-3" style="max-width: 300px; border-radius: 8px;" onchange="this.form.submit()">
                    <option value="All" {{ $selectedProject == 'All' ? 'selected' : '' }}>-- Semua Project --</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->title }}" {{ $selectedProject == $proj->title ? 'selected' : '' }}>
                            {{ $proj->title }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- Info Cards with Gradients (Hanya Total & Released) --}}
    <div class="row mb-4">
        {{-- Total Dokumen --}}
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="info-card bg-gradient-info">
                <div class="info-card-content">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="info-label">Total Dokumen</p>
                            <h2 class="info-value mb-0">{{ $stats['total_docs'] }}</h2>
                            <small class="info-sublabel">Seluruh Status</small>
                        </div>
                        <div class="info-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                    </div>
                    <div class="info-footer">
                        <i class="fas fa-chart-pie mr-1"></i>
                        <span>Project: {{ $selectedProject }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Released --}}
        <div class="col-lg-6 col-md-6 mb-3">
            <div class="info-card bg-gradient-success">
                <div class="info-card-content">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="info-label">Released</p>
                            <h2 class="info-value mb-0">{{ $stats['released'] }}</h2>
                            <small class="info-sublabel">Dokumen Selesai</small>
                        </div>
                        <div class="info-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                    <div class="info-footer">
                        <i class="fas fa-percentage mr-1"></i>
                        <span>Rate: {{ $stats['percentage'] }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Navigation for Time Periods (Realisasi) --}}
    <div class="card modern-tabs-card mb-4">
        <div class="card-header bg-white border-0 pb-0">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0 font-weight-bold text-dark">
                    <i class="fas fa-calendar-check text-primary mr-2"></i> Realisasi Dokumen (Released)
                </h5>
            </div>
            <ul class="nav nav-tabs modern-tabs" id="periodTabs" role="tablist">
                @foreach([
                    'week' => ['label' => '7 Hari Terakhir', 'icon' => 'fa-bolt', 'color' => 'success', 'data' => $stats['list_progress_week']],
                    'month' => ['label' => '30 Hari Terakhir', 'icon' => 'fa-calendar-alt', 'color' => 'info', 'data' => $stats['list_progress_month']],
                    '3month' => ['label' => '90 Hari Terakhir', 'icon' => 'fa-history', 'color' => 'primary', 'data' => $stats['list_progress_3month']]
                ] as $key => $meta)
                <li class="nav-item">
                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" 
                       id="{{ $key }}-tab" 
                       data-toggle="tab" 
                       href="#tab-{{ $key }}" 
                       role="tab"
                       aria-controls="tab-{{ $key }}"
                       aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        <i class="fas {{ $meta['icon'] }} mr-2"></i>
                        {{ $meta['label'] }}
                        <span class="badge badge-{{ $meta['color'] }} ml-2">
                            {{ count($meta['data']) }}
                        </span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        
        <div class="card-body p-0">
            <div class="tab-content" id="periodTabsContent">
                @foreach([
                    'week' => ['label' => 'Mingguan (7 Hari)', 'color' => 'success', 'data' => $stats['list_progress_week']],
                    'month' => ['label' => 'Bulanan (30 Hari)', 'color' => 'info', 'data' => $stats['list_progress_month']],
                    '3month' => ['label' => 'Triwulan (90 Hari)', 'color' => 'primary', 'data' => $stats['list_progress_3month']]
                ] as $key => $meta)
                
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                     id="tab-{{ $key }}" 
                     role="tabpanel" 
                     aria-labelledby="{{ $key }}-tab">
                    
                    @php $currentData = $meta['data']; @endphp

                    <div class="table-responsive modern-table-wrapper">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>No. Dokumen</th>
                                    <th>Nama Dokumen</th>
                                    <th>Unit</th>
                                    <th class="text-center">Tanggal Realisasi</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currentData as $index => $doc)
                                <tr class="unit-row" onclick="showDocDetail('{{ $doc['nodokumen'] }}', '{{ $doc['namadokumen'] }}', '{{ $doc['unit'] }}', '{{ $doc['realisasi'] }}')">
                                    <td class="text-muted font-weight-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="unit-info">
                                            <i class="far fa-file-alt text-{{ $meta['color'] }}"></i>
                                            <span class="unit-name">{{ $doc['nodokumen'] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($doc['namadokumen'], 50) }}</td>
                                    <td>
                                        <span class="badge badge-light border">{{ $doc['unit'] }}</span>
                                    </td>
                                    <td class="text-center font-weight-bold text-dark">
                                        <i class="far fa-calendar-check mr-1 text-success"></i> {{ $doc['realisasi'] }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success badge-modern">
                                            RELEASED
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>Belum ada data realisasi</h5>
                                        <p>Tidak ada dokumen yang dirilis dalam periode ini</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tabel Statistik Unit (Created Documents) --}}
    <div class="card legend-card mb-4 mt-4 border-left-primary">
        <div class="card-header bg-white border-0 pt-3 pb-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-building mr-2 text-primary"></i> Dokumen Masuk per Unit
                    </h5>
                    <small class="text-muted">Jumlah dokumen baru yang masuk ke sistem (Created) berdasarkan waktu</small>
                </div>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive modern-table-wrapper" style="max-height: 400px;">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th class="pl-4">Nama Unit</th>
                            <th class="text-center" style="width: 15%;">
                                <span class="badge badge-success badge-pill px-3">7 Hari</span>
                            </th>
                            <th class="text-center" style="width: 15%;">
                                <span class="badge badge-info badge-pill px-3">30 Hari</span>
                            </th>
                            <th class="text-center" style="width: 15%;">
                                <span class="badge badge-primary badge-pill px-3">90 Hari</span>
                            </th>
                            <th class="text-center font-weight-bold text-dark" style="width: 15%;">Total Accumulative</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats['unit_incoming'] as $unit => $data)
                            @if($data['total'] > 0)
                            <tr>
                                <td class="pl-4 font-weight-bold text-secondary">
                                    <i class="fas fa-layer-group mr-2 text-muted"></i> {{ $unit }}
                                </td>
                                <td class="text-center font-weight-bold text-success">
                                    {{ $data['7_days'] }}
                                </td>
                                <td class="text-center font-weight-bold text-info">
                                    {{ $data['30_days'] }}
                                </td>
                                <td class="text-center font-weight-bold text-primary">
                                    {{ $data['90_days'] }}
                                </td>
                                <td class="text-center font-weight-bold text-dark" style="background-color: #f8f9fa;">
                                    {{ $data['total'] }}
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    Belum ada data dokumen masuk.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Simple Detail Modal --}}
    <div class="modal fade" id="modalDocDetail" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">
                            <i class="fas fa-file-alt mr-2"></i> Detail Dokumen
                        </h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle bg-light mb-2 mx-auto" style="width:60px; height:60px; display:flex; align-items:center; justify-content:center; border-radius:50%;">
                            <i class="fas fa-file-contract text-primary fa-2x"></i>
                        </div>
                        <h5 id="modalNoDoc" class="font-weight-bold mb-1"></h5>
                        <p id="modalNamaDoc" class="text-muted mb-0"></p>
                    </div>
                    
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-building mr-2 text-muted"></i> Unit Kerja</span>
                            <strong id="modalUnit"></strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar-check mr-2 text-muted"></i> Tanggal Realisasi</span>
                            <strong id="modalDate" class="text-success"></strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-info-circle mr-2 text-muted"></i> Status</span>
                            <span class="badge badge-success px-3 py-2">RELEASED</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    function showDocDetail(no, nama, unit, date) {
        $('#modalNoDoc').text(no);
        $('#modalNamaDoc').text(nama);
        $('#modalUnit').text(unit);
        $('#modalDate').text(date);
        $('#modalDocDetail').modal('show');
    }

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<style>
/* Modern Info Cards */
.info-card {
    border-radius: 16px;
    border: none;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
}

.info-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.12);
}

.info-card-content {
    padding: 1.5rem;
    color: white;
}

.info-label {
    margin: 0;
    font-size: 0.875rem;
    opacity: 0.9;
    font-weight: 500;
}

.info-value {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1.2;
}

.info-sublabel {
    opacity: 0.85;
    font-size: 0.813rem;
}

.info-icon {
    font-size: 3rem;
    opacity: 0.2;
}

.info-footer {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255,255,255,0.2);
    font-size: 0.875rem;
    opacity: 0.95;
}

/* Gradients */
.bg-gradient-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
.bg-gradient-info { background: linear-gradient(135deg, #17a2b8 0%, #3ca7d6 100%); }

/* Modern Tabs */
.modern-tabs-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

.modern-tabs {
    border-bottom: 2px solid #e9ecef;
    padding: 0 1.5rem;
}

.modern-tabs .nav-item {
    margin-bottom: -2px;
}

.modern-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    padding: 1rem 1.5rem;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}

.modern-tabs .nav-link:hover {
    color: #495057;
    border-bottom-color: #dee2e6;
}

.modern-tabs .nav-link.active {
    color: #007bff;
    background: transparent;
    border-bottom-color: #007bff;
}

.modern-tabs .nav-link .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

/* Modern Table */
.modern-table-wrapper {
    max-height: 500px;
    overflow-y: auto;
}

.table-modern {
    font-size: 0.9rem;
}

.table-modern thead {
    position: sticky;
    top: 0;
    z-index: 10;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.table-modern thead th {
    border-top: none;
    border-bottom: 2px solid #dee2e6;
    padding: 1rem 0.75rem;
    font-weight: 600;
    color: #495057;
    vertical-align: middle;
}

.table-modern tbody tr {
    transition: all 0.2s;
}

.table-modern tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
    transform: scale(1.002);
}

.unit-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.unit-info i {
    font-size: 1.25rem;
}

.unit-name {
    font-weight: 600;
    color: #212529;
}

.badge-modern {
    padding: 0.5rem 0.75rem;
    font-weight: 500;
    font-size: 0.813rem;
    border-radius: 6px;
}

/* Empty State */
.empty-state {
    padding: 3rem 2rem;
    text-align: center;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

/* Modern Modal */
.modern-modal {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modern-modal .modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem 2rem;
    border-radius: 12px 12px 0 0;
}

.modern-modal .modal-title {
    font-size: 1.15rem;
    font-weight: 600;
}

.modern-modal .close {
    color: white;
    opacity: 0.8;
    text-shadow: none;
    font-size: 1.5rem;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.tab-pane {
    animation: fadeIn 0.3s ease-in-out;
}

/* Legend/Warning Cards */
.legend-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    overflow: hidden;
}
.border-left-primary { border-left: 4px solid #007bff; }

/* Scrollbar Styling */
.modern-table-wrapper::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.modern-table-wrapper::-webkit-scrollbar-track {
    background: #f1f3f5;
}
.modern-table-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}
</style>
@endsection