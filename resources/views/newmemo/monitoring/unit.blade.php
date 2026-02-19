@extends('layouts.universal')

@section('title', 'Monitoring Unit')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-chart-line mr-2 text-primary"></i> Monitoring Kinerja Unit
                </h1>
                <p class="text-muted mb-0 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>
                    Dashboard analisis lead time & produktivitas pemrosesan memo
                </p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right bg-white shadow-sm px-3 py-2 mb-0">
                    <li class="breadcrumb-item"><a href="#"><i class="fas fa-home mr-1"></i>Home</a></li>
                    <li class="breadcrumb-item active">Monitoring</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')

    {{-- Enhanced Info Cards with Trends --}}
    <div class="row mb-4">
        @php
            $totalMemos7 = collect($monitoringData['7_days'] ?? [])->sum('memocount');
            $totalMemos30 = collect($monitoringData['30_days'] ?? [])->sum('memocount');
            $totalMemos90 = collect($monitoringData['90_days'] ?? [])->sum('memocount');
            
            $avgLeadTime7 = collect($monitoringData['7_days'] ?? [])->avg('leadtimeaverage');
            $avgLeadTime30 = collect($monitoringData['30_days'] ?? [])->avg('leadtimeaverage');
            $avgLeadTime90 = collect($monitoringData['90_days'] ?? [])->avg('leadtimeaverage');
        @endphp
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="info-card bg-gradient-success">
                <div class="info-card-content">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="info-label">7 Hari Terakhir</p>
                            <h2 class="info-value mb-0">{{ number_format($totalMemos7) }}</h2>
                            <small class="info-sublabel">Total Memo</small>
                        </div>
                        <div class="info-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                    </div>
                    <div class="info-footer">
                        <i class="fas fa-clock mr-1"></i>
                        <span>Avg: {{ \App\Models\NewMemo::formatDuration($avgLeadTime7) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="info-card bg-gradient-info">
                <div class="info-card-content">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="info-label">30 Hari Terakhir</p>
                            <h2 class="info-value mb-0">{{ number_format($totalMemos30) }}</h2>
                            <small class="info-sublabel">Total Memo</small>
                        </div>
                        <div class="info-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="info-footer">
                        <i class="fas fa-clock mr-1"></i>
                        <span>Avg: {{ \App\Models\NewMemo::formatDuration($avgLeadTime30) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="info-card bg-gradient-primary">
                <div class="info-card-content">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="info-label">90 Hari Terakhir</p>
                            <h2 class="info-value mb-0">{{ number_format($totalMemos90) }}</h2>
                            <small class="info-sublabel">Total Memo</small>
                        </div>
                        <div class="info-icon">
                            <i class="fas fa-history"></i>
                        </div>
                    </div>
                    <div class="info-footer">
                        <i class="fas fa-clock mr-1"></i>
                        <span>Avg: {{ \App\Models\NewMemo::formatDuration($avgLeadTime90) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="info-card bg-gradient-warning">
                <div class="info-card-content">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <p class="info-label">Unit Aktif</p>
                            <h2 class="info-value mb-0">{{ count($monitoringData['90_days'] ?? []) }}</h2>
                            <small class="info-sublabel">Unit Kerja</small>
                        </div>
                        <div class="info-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    <div class="info-footer">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span>Periode 90 hari</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Legend with Better Visual Hierarchy --}}
    <div class="card legend-card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-lg-3 mb-3 mb-lg-0">
                    <h5 class="mb-0 font-weight-bold">
                        <i class="fas fa-info-circle text-primary mr-2"></i> Indikator Performa
                    </h5>
                </div>
                <div class="col-lg-9">
                    <div class="legend-items">
                        <div class="legend-item legend-success">
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Cepat</strong>
                                <span>< 24 Jam</span>
                            </div>
                        </div>
                        <div class="legend-item legend-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Normal</strong>
                                <span>1-3 Hari</span>
                            </div>
                        </div>
                        <div class="legend-item legend-danger">
                            <i class="fas fa-times-circle"></i>
                            <div>
                                <strong>Lambat</strong>
                                <span>> 3 Hari</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(empty($monitoringData['90_days']))
        <div class="alert alert-warning alert-modern">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <div>
                <strong>Perhatian!</strong>
                <p class="mb-0 mt-1">Data monitoring kosong. Pastikan ada memo yang dibuat dalam 90 hari terakhir.</p>
            </div>
        </div>
    @endif

    {{-- Tab Navigation for Time Periods --}}
    <div class="card modern-tabs-card">
        <div class="card-header bg-white border-0 pb-0">
            <ul class="nav nav-tabs modern-tabs" id="periodTabs" role="tablist">
                @foreach([
                    '7_days' => ['label' => '7 Hari', 'icon' => 'fa-bolt', 'color' => 'success'],
                    '30_days' => ['label' => '30 Hari', 'icon' => 'fa-calendar-alt', 'color' => 'info'],
                    '90_days' => ['label' => '90 Hari', 'icon' => 'fa-history', 'color' => 'primary']
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
                            {{ count($monitoringData[$key] ?? []) }}
                        </span>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        
        <div class="card-body p-0">
            <div class="tab-content" id="periodTabsContent">
                @foreach([
                    '7_days' => ['label' => 'Mingguan (7 Hari)', 'color' => 'success', 'icon' => 'fa-bolt'],
                    '30_days' => ['label' => 'Bulanan (30 Hari)', 'color' => 'info', 'icon' => 'fa-calendar-alt'],
                    '90_days' => ['label' => 'Triwulan (90 Hari)', 'color' => 'primary', 'icon' => 'fa-history']
                ] as $key => $meta)
                
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                     id="tab-{{ $key }}" 
                     role="tabpanel" 
                     aria-labelledby="{{ $key }}-tab">
                    @php
                        $currentData = $monitoringData[$key] ?? [];
                        $hasData = !empty($currentData);
                    @endphp
                    
                    @if($hasData)
                        {{-- Statistics Summary --}}
                        <div class="stats-summary">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        <i class="fas fa-file-alt text-{{ $meta['color'] }}"></i>
                                        <div>
                                            <div class="stat-label">Total Memo</div>
                                            <div class="stat-value">{{ number_format(collect($currentData)->sum('memocount')) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        <i class="fas fa-clock text-{{ $meta['color'] }}"></i>
                                        <div>
                                            <div class="stat-label">Avg Lead Time</div>
                                            <div class="stat-value">{{ \App\Models\NewMemo::formatDuration(collect($currentData)->avg('leadtimeaverage')) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        @php
                                            $bestUnit = collect($currentData)->sortBy('leadtimeaverage')->first();
                                            $bestUnitName = $bestUnit ? array_search($bestUnit, $currentData) : '-';
                                        @endphp
                                        <i class="fas fa-trophy text-success"></i>
                                        <div>
                                            <div class="stat-label">Unit Tercepat</div>
                                            <div class="stat-value small">{{ Str::limit($bestUnitName, 20) }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="stat-item">
                                        <i class="fas fa-building text-{{ $meta['color'] }}"></i>
                                        <div>
                                            <div class="stat-label">Jumlah Unit</div>
                                            <div class="stat-value">{{ count($currentData) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="table-responsive modern-table-wrapper">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Unit Kerja</th>
                                    <th class="text-center" style="width: 80px;">
                                        <i class="fas fa-file-alt mr-1"></i>Memo
                                    </th>
                                    <th class="text-center" style="width: 80px;">
                                        <i class="fas fa-users mr-1"></i>SDM
                                    </th>
                                    <th class="text-center metric-col">
                                        <div class="metric-header">
                                            <i class="fas fa-tasks"></i>
                                            <span>Beban Volume</span>
                                            <small>memo/org</small>
                                        </div>
                                    </th>
                                    <th class="text-center metric-col">
                                        <div class="metric-header">
                                            <i class="fas fa-hourglass-half"></i>
                                            <span>Beban Waktu</span>
                                            <small>jam/org</small>
                                        </div>
                                    </th>
                                    <th class="text-center metric-col">
                                        <div class="metric-header">
                                            <i class="fas fa-tachometer-alt"></i>
                                            <span>Efisiensi</span>
                                            <small>jam/memo/org</small>
                                        </div>
                                    </th>
                                    <th class="text-center" style="width: 140px;">
                                        <i class="fas fa-stopwatch mr-1"></i>Avg Lead Time
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currentData as $index => $unitData)
                                @php
                                    $unitName = array_search($unitData, $currentData);
                                    $memberCount = $unitMembers[$unitName] ?? 0;
                                    
                                    $totalMemo = $unitData['memocount'];
                                    $avgLeadTime = floatval($unitData['leadtimeaverage']);
                                    
                                    $divider = $memberCount > 0 ? $memberCount : 1;
                                    $memoPerOrang = $totalMemo / $divider;
                                    $totalHoursUnit = $avgLeadTime * $totalMemo;
                                    $leadtimePerOrang = $totalHoursUnit / $divider;
                                    $leadtimePerMemoPerOrang = $avgLeadTime / $divider;
                                    
                                    $val = $avgLeadTime;
                                    $badgeClass = 'secondary';
                                    if($val !== null && $val > 0) {
                                        if($val < 24) $badgeClass = 'success';
                                        elseif($val < 72) $badgeClass = 'warning';
                                        else $badgeClass = 'danger';
                                    }
                                @endphp
                                
                                <tr class="unit-row" onclick="showDetail('{{ $unitName }}', '{{ $key }}', '{{ $meta['label'] }}')">
                                    <td class="text-muted font-weight-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="unit-info">
                                            <i class="fas fa-building text-{{ $meta['color'] }}"></i>
                                            <span class="unit-name">{{ $unitName }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-pill badge-light">{{ $totalMemo }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-pill badge-info">{{ $memberCount }}</span>
                                    </td>
                                    <td class="text-center metric-value">
                                        <div class="metric-display">
                                            <strong>{{ number_format($memoPerOrang, 1) }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center metric-value">
                                        <div class="metric-display">
                                            {{ \App\Models\NewMemo::formatDuration($leadtimePerOrang) }}
                                        </div>
                                    </td>
                                    <td class="text-center metric-value">
                                        <div class="metric-display text-primary">
                                            <strong>{{ \App\Models\NewMemo::formatDuration($leadtimePerMemoPerOrang) }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $badgeClass }} badge-modern">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ \App\Models\NewMemo::formatDuration($val) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>Belum ada data</h5>
                                        <p>Tidak ada memo dalam periode ini</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($hasData)
                    <div class="table-footer">
                        <i class="fas fa-mouse-pointer mr-2"></i>
                        Klik pada baris untuk melihat detail dokumen unit
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Enhanced Modal --}}
    <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content modern-modal">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">
                            <i class="fas fa-list-alt mr-2"></i> Detail Dokumen
                        </h5>
                        <p class="modal-subtitle mb-0">
                            <strong id="modalUnitName"></strong>
                        </p>
                    </div>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                
                <div class="modal-stats" id="modalStats" style="display: none;">
                    <div class="row">
                        <div class="col">
                            <div class="modal-stat-item">
                                <i class="fas fa-file-alt text-primary"></i>
                                <div>
                                    <div class="stat-label">Total Dokumen</div>
                                    <div class="stat-value" id="statTotalDocs">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="modal-stat-item">
                                <i class="fas fa-users text-info"></i>
                                <div>
                                    <div class="stat-label">Anggota Unit</div>
                                    <div class="stat-value" id="statMemberCount">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="modal-stat-item">
                                <i class="fas fa-clock text-warning"></i>
                                <div>
                                    <div class="stat-label">Rata-rata</div>
                                    <div class="stat-value" id="statAvgTime">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="modal-stat-item">
                                <i class="fas fa-rocket text-success"></i>
                                <div>
                                    <div class="stat-label">Tercepat</div>
                                    <div class="stat-value" id="statFastest">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="modal-stat-item">
                                <i class="fas fa-hourglass-end text-danger"></i>
                                <div>
                                    <div class="stat-label">Terlama</div>
                                    <div class="stat-value" id="statSlowest">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-body p-0">
                    <div id="loadingSpinner" class="loading-state">
                        <div class="spinner-border text-primary"></div>
                        <p>Mengambil data memo...</p>
                    </div>
                    
                    <div class="table-responsive" id="tableContainer" style="display: none;">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="width: 150px;">No. Dokumen</th>
                                    <th>Judul Memo</th>
                                    <th style="width: 160px;">Tanggal</th>
                                    <th class="text-center" style="width: 140px;">Lead Time</th>
                                </tr>
                            </thead>
                            <tbody id="modalTableBody"></tbody>
                        </table>
                    </div>
                    
                    <div id="emptyDataMsg" class="empty-state" style="display: none;">
                        <i class="fas fa-folder-open"></i>
                        <h5>Tidak ada data ditemukan</h5>
                        <p>Silakan coba periode lain atau unit kerja lain</p>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <small class="text-muted mr-auto" id="modalPeriod"></small>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    function showDetail(unitName, rangeKey, rangeLabel) {
        $('#modalUnitName').text(unitName);
        $('#modalPeriod').html('<i class="fas fa-calendar mr-1"></i> Periode: ' + rangeLabel);
        $('#modalDetail').modal('show');
        
        $('#loadingSpinner').show();
        $('#tableContainer').hide();
        $('#emptyDataMsg').hide();
        $('#modalStats').hide();
        $('#modalTableBody').empty();

        $.ajax({
            url: "{{ route('monitoring.unit.detail') }}",
            type: "GET",
            data: { unit: unitName, range: rangeKey },
            success: function(response) {
                $('#loadingSpinner').hide();

                if (response.length > 0) {
                    $('#tableContainer').show();
                    $('#modalStats').show();
                    
                    let totalDocs = response.length;
                    let memberCount = response[0].member_count || 0;
                    let leadTimes = response.map(item => parseFloat(item.leadtime_hours || 0));
                    let totalHours = leadTimes.reduce((a, b) => a + b, 0);
                    let avgTime = totalDocs > 0 ? (totalHours / totalDocs) : 0;
                    let fastest = leadTimes.length > 0 ? Math.min(...leadTimes) : 0;
                    let slowest = leadTimes.length > 0 ? Math.max(...leadTimes) : 0;
                    
                    function formatHours(hours) {
                        let h = parseFloat(hours);
                        if (h === 0 || isNaN(h)) return '0 menit';
                        if (h < 1) return Math.round(h * 60) + ' menit';
                        if (h < 24) {
                            let wholeHours = Math.floor(h);
                            let minutes = Math.round((h - wholeHours) * 60);
                            return minutes === 0 ? wholeHours + ' jam' : wholeHours + ' jam ' + minutes + ' menit';
                        }
                        let days = Math.floor(h / 24);
                        let remainingHours = h % 24;
                        let wholeHours = Math.floor(remainingHours);
                        let minutes = Math.round((remainingHours - wholeHours) * 60);
                        let result = days + ' hari';
                        if (wholeHours > 0) result += ' ' + wholeHours + ' jam';
                        if (minutes > 0) result += ' ' + minutes + ' menit';
                        return result;
                    }
                    
                    $('#statTotalDocs').text(totalDocs);
                    $('#statMemberCount').text(memberCount);
                    $('#statAvgTime').text(formatHours(avgTime));
                    $('#statFastest').text(formatHours(fastest));
                    $('#statSlowest').text(formatHours(slowest));
                    
                    $.each(response, function(index, item) {
                        let iconClass = 'fa-file-alt';
                        if (item.badge === 'success') iconClass = 'fa-check-circle';
                        else if (item.badge === 'warning') iconClass = 'fa-exclamation-triangle';
                        else if (item.badge === 'danger') iconClass = 'fa-times-circle';
                        
                        var row = `
                            <tr class="detail-row">
                                <td class="text-muted">${index + 1}</td>
                                <td>
                                    <span class="doc-number">
                                        <i class="fas fa-file mr-1"></i>${item.documentnumber}
                                    </span>
                                </td>
                                <td class="doc-title">${item.documentname}</td>
                                <td class="text-muted">
                                    <i class="far fa-calendar-alt mr-1"></i>${item.created_at}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-${item.badge} badge-modern">
                                        <i class="fas ${iconClass} mr-1"></i>${item.leadtime}
                                    </span>
                                </td>
                            </tr>
                        `;
                        $('#modalTableBody').append(row);
                    });
                } else {
                    $('#emptyDataMsg').show();
                }
            },
            error: function() {
                $('#loadingSpinner').hide();
                $('#emptyDataMsg').html(`
                    <i class="fas fa-exclamation-circle text-danger"></i>
                    <h5>Gagal Mengambil Data</h5>
                    <p>Terjadi kesalahan. Silakan coba lagi.</p>
                `).show();
            }
        });
    }
</script>

<style>
/* Modern Info Cards */
.info-card {
    border-radius: 16px;
    border: none;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

/* Legend Card */
.legend-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.legend-items {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    background: #f8f9fa;
    transition: all 0.2s;
}

.legend-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.legend-item i {
    font-size: 1.5rem;
}

.legend-item strong {
    display: block;
    font-size: 0.875rem;
}

.legend-item span {
    display: block;
    font-size: 0.75rem;
    color: #6c757d;
}

.legend-success i { color: #28a745; }
.legend-warning i { color: #ffc107; }
.legend-danger i { color: #dc3545; }

/* Modern Tabs */
.modern-tabs-card {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
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

/* Statistics Summary */
.stats-summary {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
    border-bottom: 1px solid #dee2e6;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: all 0.2s;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.stat-item i {
    font-size: 2rem;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #212529;
}

/* Modern Table */
.modern-table-wrapper {
    max-height: 600px;
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
}

.metric-col {
    background: #f8f9fa;
    border-left: 1px solid #dee2e6;
    border-right: 1px solid #dee2e6;
}

.metric-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.metric-header i {
    font-size: 1.1rem;
    color: #007bff;
}

.metric-header span {
    font-weight: 600;
    font-size: 0.813rem;
}

.metric-header small {
    font-size: 0.7rem;
    color: #6c757d;
    font-weight: 400;
}

.metric-value {
    background: #f8f9fa;
    font-size: 0.875rem;
}

.metric-display {
    padding: 0.25rem 0;
}

.unit-row {
    border-bottom: 1px solid #f1f3f5;
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

.table-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
    text-align: center;
    color: #6c757d;
    font-size: 0.875rem;
}

/* Empty State */
.empty-state {
    padding: 4rem 2rem;
    text-align: center;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

.empty-state h5 {
    color: #495057;
    margin-bottom: 0.5rem;
}

.empty-state p {
    font-size: 0.875rem;
    margin-bottom: 0;
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
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.modern-modal .modal-subtitle {
    font-size: 0.938rem;
    opacity: 0.9;
}

.modern-modal .close {
    color: white;
    opacity: 0.8;
    text-shadow: none;
    font-size: 2rem;
    font-weight: 300;
}

.modern-modal .close:hover {
    opacity: 1;
}

.modal-stats {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #dee2e6;
}

.modal-stat-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.modal-stat-item i {
    font-size: 1.75rem;
}

.modal-stat-item .stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.modal-stat-item .stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #212529;
}

.loading-state {
    padding: 4rem 2rem;
    text-align: center;
}

.loading-state .spinner-border {
    width: 3rem;
    height: 3rem;
    margin-bottom: 1rem;
}

.loading-state p {
    color: #6c757d;
    margin: 0;
}

.detail-row {
    transition: all 0.2s;
}

.detail-row:hover {
    background-color: #f8f9fa;
}

.doc-number {
    color: #007bff;
    font-weight: 600;
    font-size: 0.875rem;
}

.doc-title {
    font-weight: 500;
    color: #212529;
}

/* Alert Modern */
.alert-modern {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    align-items: start;
    gap: 1rem;
    padding: 1rem 1.5rem;
}

.alert-modern i {
    font-size: 1.5rem;
    margin-top: 0.25rem;
}

/* Scrollbar Styling */
.modern-table-wrapper::-webkit-scrollbar,
.table-responsive::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.modern-table-wrapper::-webkit-scrollbar-track,
.table-responsive::-webkit-scrollbar-track {
    background: #f1f3f5;
    border-radius: 4px;
}

.modern-table-wrapper::-webkit-scrollbar-thumb,
.table-responsive::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.modern-table-wrapper::-webkit-scrollbar-thumb:hover,
.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.tab-pane {
    animation: fadeIn 0.3s ease-in-out;
}

/* Responsive */
@media (max-width: 768px) {
    .info-value {
        font-size: 2rem;
    }
    
    .info-icon {
        font-size: 2rem;
    }
    
    .legend-items {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .modern-tabs .nav-link {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
    }
    
    .stats-summary .row {
        gap: 0.75rem;
    }
    
    .stat-item {
        margin-bottom: 0.75rem;
    }
    
    .table-modern {
        font-size: 0.813rem;
    }
    
    .metric-header span {
        font-size: 0.75rem;
    }
    
    .modal-stats .row {
        gap: 0.5rem;
    }
    
    .modal-stat-item {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    .modern-tabs {
        padding: 0 0.5rem;
    }
    
    .modern-tabs .nav-link {
        padding: 0.75rem 0.5rem;
        font-size: 0.813rem;
    }
    
    .modern-tabs .nav-link i {
        display: none;
    }
}
</style>
@endsection