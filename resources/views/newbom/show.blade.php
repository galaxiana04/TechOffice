@extends('layouts.universal')

@section('container2')
    <div class="content-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.8rem;">Detail Bill of Material</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('newbom.index') }}" class="text-danger font-weight-bold">BOM</a></li>
                        <li class="breadcrumb-item active">List Material</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
     <div class="container-fluid mb-5">
        <div class="row justify-content-center">
            <div class="col-12">
                
                <div class="card border-0 shadow-lg rounded-xl overflow-hidden">
                    
                    {{-- Header Tabs --}}
                    <div class="card-header bg-white border-bottom p-0">
                        <ul class="nav nav-tabs border-0" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active tab-modern" id="progress-tab" data-toggle="tab" href="#progress" role="tab" aria-controls="progress" aria-selected="true">
                                    <i class="fas fa-tasks mr-2 text-primary"></i>
                                    <span class="font-weight-bold">Progress Material</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link tab-modern" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">
                                    <i class="fas fa-history mr-2"></i>
                                    <span class="font-weight-bold">Riwayat Aktivitas</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0">
                        <div class="tab-content" id="myTabContent">
                            
                            {{-- TAB 1: PROGRESS --}}
                            <div class="tab-pane fade show active" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                                <div class="p-4 bg-soft-gradient">
                                    
                                    {{-- Info Cards --}}
                                    <div class="row mb-4">
                                        {{-- Card 1: Nomor BOM --}}
                                        <div class="col-lg-3 col-md-6 col-12 mb-3">
                                            <div class="card border-0 shadow-sm h-100 hover-lift card-border-left-danger">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div class="flex-grow-1">
                                                            <small class="text-label">Nomor BOM</small>
                                                            <h4 class="font-weight-bold text-dark mb-0">{{ $newbom->BOMnumber }}</h4>
                                                        </div>
                                                        <div class="icon-box bg-soft-red text-danger">
                                                            <i class="fas fa-file-alt"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Card 2: Proyek --}}
                                        <div class="col-lg-3 col-md-6 col-12 mb-3">
                                            <div class="card border-0 shadow-sm h-100 hover-lift card-border-left-primary">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div class="flex-grow-1">
                                                            <small class="text-label">Proyek</small>
                                                            <h4 class="font-weight-bold text-dark mb-0 text-truncate" title="{{ $newbom->projectType->title }}">
                                                                {{ $newbom->projectType->title }}
                                                            </h4>
                                                        </div>
                                                        <div class="icon-box bg-soft-blue text-primary">
                                                            <i class="fas fa-building"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Card 3: Unit --}}
                                        <div class="col-lg-3 col-md-6 col-12 mb-3">
                                            <div class="card border-0 shadow-sm h-100 hover-lift card-border-left-success">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div class="flex-grow-1">
                                                            <small class="text-label">Unit</small>
                                                            <h4 class="font-weight-bold text-dark mb-0">{{ $newbom->unit }}</h4>
                                                        </div>
                                                        <div class="icon-box bg-soft-green text-success">
                                                            <i class="fas fa-home"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Card 4: Penyelesaian --}}
                                        <div class="col-lg-3 col-md-6 col-12 mb-3">
                                            <div class="card border-0 shadow-sm h-100 hover-lift card-border-left-warning">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div class="flex-grow-1">
                                                            <small class="text-label">Penyelesaian</small>
                                                            <h3 class="font-weight-bold text-dark mb-0">
                                                                {{ number_format($seniorpercentage, 2) }}<small class="text-muted text-sm">%</small>
                                                            </h3>
                                                        </div>
                                                        <div class="icon-box bg-soft-yellow text-warning">
                                                            <i class="fas fa-chart-bar"></i>
                                                        </div>
                                                    </div>
                                                    <div class="progress" style="height: 8px; border-radius: 10px;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-gradient-warning" 
                                                             role="progressbar" style="width: {{ $seniorpercentage }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row mb-3">
                                        <div class="col-12 text-right">
                                            <button type="button" class="btn btn-danger bg-modern-red border-0 shadow-sm rounded-pill px-4"
                                                onclick="downloadbom('{{ $newbom->id }}')" aria-label="Export data BOM">
                                                <i class="fas fa-file-download mr-2"></i>Download BOM
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive bg-white rounded-lg shadow-sm p-3">
                                        <table id="example2" class="table table-hover w-100 custom-table">
                                            <thead>
                                                <tr>
                                                    <th width="5%" class="text-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="checkAll">
                                                            <label class="custom-control-label" for="checkAll"></label>
                                                        </div>
                                                    </th>
                                                    <th width="5%">No</th>
                                                    <th>Kode Material</th>
                                                    <th>Rev</th>
                                                    <th>Material</th>
                                                    <th>Spesifikasi</th>
                                                    <th>Memo Terkait</th>
                                                    <th>Status</th>
                                                    <th width="20%">Dokumen Pendukung</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $penghitung = 1; @endphp
                                                @foreach ($newbomkomats as $index => $item)
                                                    @php
                                                        $key = key($newbomkomats);
                                                        next($newbomkomats);
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" value="{{ $index }}"
                                                                    name="document_ids[]" id="checkbox{{ $key }}">
                                                                <label class="custom-control-label" for="checkbox{{ $key }}"></label>
                                                            </div>
                                                        </td>
                                                        <td class="font-weight-bold text-muted">{{ $penghitung }}</td>
                                                        <td contenteditable class="editable-cell text-danger font-weight-bold"
                                                            id="kodematerial_{{ $newbom->id }}_{{ $index }}">
                                                            {{ $item['kodematerial'] }}</td>
                                                        <td contenteditable class="editable-cell text-center" id="rev_{{ $newbom->id }}_{{ $index }}">
                                                            {{ $item['rev'] ?? '-' }}</td>
                                                        <td contenteditable class="editable-cell"
                                                            id="material_{{ $newbom->id }}_{{ $index }}">
                                                            {{ $item['material'] }}</td>
                                                        <td>
                                                            @if (isset($item->newprogressreports) && $item->newprogressreports->isNotEmpty())
                                                                @foreach ($item->newprogressreports as $progressReport)
                                                                    @php
                                                                        $documentnumber = $progressReport->nodokumen ?? 'Tidak ada dokumen';
                                                                        $spesifikasipic = $progressReport->drafter ?? 'Tidak ada drafter';
                                                                        $status = $progressReport->status ? 'Released' : 'Proses';
                                                                    @endphp
                                                                    <div class="mb-1 d-flex flex-wrap gap-1">
                                                                        <span class="badge badge-soft-info">{{ $documentnumber }}</span>
                                                                        <span class="badge badge-soft-primary">{{ $spesifikasipic }}</span>
                                                                        <span class="badge {{ $progressReport->released ? 'badge-soft-success' : 'badge-soft-warning' }}">{{ $status }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="d-flex flex-wrap gap-1">
                                                                    <span class="badge badge-soft-warning">Tidak ada dokumen</span>
                                                                    <span class="badge badge-soft-primary">Tidak ada drafter</span>
                                                                    <span class="badge badge-soft-warning">Proses</span>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if (isset($groupedKomats[$item['kodematerial']]))
                                                                @php
                                                                    $sortedKomats = [];
                                                                    $komats = $groupedKomats[$item['kodematerial']];
                                                                    foreach ($komats['memoname'] as $i => $memoname) {
                                                                        $sortedKomats[] = [
                                                                            'memoname' => $memoname,
                                                                            'memoid' => $komats['memoid'][$i],
                                                                            'memostatus' => $komats['memostatus'][$i],
                                                                            'percentage' => $komats['percentage'][$i],
                                                                            'supplier' => strtoupper($komats['supplier'][$i]),
                                                                            'PEcombineworkstatus' => $komats['PEcombineworkstatus'][$i],
                                                                        ];
                                                                    }
                                                                    usort($sortedKomats, fn($a, $b) => strcmp($a['supplier'], $b['supplier']));
                                                                @endphp
                                                                <div class="d-flex flex-column gap-1">
                                                                @foreach ($sortedKomats as $komat)
                                                                    <div class="badge-group-modern shadow-sm">
                                                                        <a href="{{ route('new-memo.show', ['memoId' => $komat['memoid']]) }}"
                                                                            class="badge bg-success text-white">{{ $komat['memoname'] }}</a>
                                                                        <span class="badge bg-light text-dark">{{ $komat['memostatus'] }}</span>
                                                                        <span class="badge bg-primary text-white">{{ $komat['percentage'] }}%</span>
                                                                        <span class="badge bg-warning text-dark">{{ $komat['PEcombineworkstatus'] }}</span>
                                                                        <span class="badge bg-secondary text-white">{{ $komat['supplier'] }}</span>
                                                                    </div>
                                                                @endforeach
                                                                </div>
                                                            @else
                                                                <span class="badge badge-light text-muted border border-light" contenteditable
                                                                    id="yyy_{{ $newbom->id }}_{{ $index }}">Tidak ada Memo</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $totalpercentage = $groupedKomats[$item['kodematerial']]['totalpercentage'] ?? 0;
                                                            @endphp
                                                            <div class="mb-2">
                                                                <span class="badge {{ $totalpercentage == 100 ? 'badge-soft-success' : 'badge-soft-warning' }}">
                                                                    {{ $totalpercentage == 100 ? 'Completed' : 'Incomplete' }}
                                                                </span>
                                                            </div>

                                                            @if ($yourauth->rule == 'Logistik' || $yourauth->rule == 'MTPR' || $yourauth->rule == 'Product Engineering')
                                                                <div class="d-flex align-items-center">
                                                                    <span id="statusDisplay{{ $item->id }}"
                                                                        class="badge mr-1 {{ ($item->status ?? '') == 'SPPH' ? 'badge-warning' : (($item->status ?? '') == 'PO' ? 'badge-success' : 'badge-secondary') }}">
                                                                        {{ $item->status ?? 'No Status' }}
                                                                    </span>
                                                                    <button class="btn btn-tool-icon text-primary" onclick="enableEdit({{ $item->id }})">
                                                                        <i class="fas fa-pencil-alt"></i>
                                                                    </button>
                                                                </div>
                                                                
                                                                <form id="editStatusForm{{ $item->id }}" style="display: none;" class="mt-1">
                                                                    <div class="input-group input-group-sm">
                                                                        <select name="status" id="status{{ $item->id }}" class="custom-select">
                                                                            <option value="SPPH" {{ ($item->status ?? '') == 'SPPH' ? 'selected' : '' }}>SPPH</option>
                                                                            <option value="PO" {{ ($item->status ?? '') == 'PO' ? 'selected' : '' }}>PO</option>
                                                                        </select>
                                                                        <div class="input-group-append">
                                                                            <button type="button" class="btn btn-success" id="saveButton{{ $item->id }}"
                                                                                onclick="updateStatus({{ $item->id }}, {{ $item->newbom_id }}, '{{ $item->status ?? '' }}')"><i class="fas fa-check"></i></button>
                                                                            <button type="button" class="btn btn-secondary" onclick="cancelEdit({{ $item->id }})"><i class="fas fa-times"></i></button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            @else
                                                                <span class="badge {{ ($item->status ?? '') == 'SPPH' ? 'badge-warning' : (($item->status ?? '') == 'PO' ? 'badge-success' : 'badge-secondary') }}"
                                                                    id="status_{{ $item->newbom_id }}_{{ $loop->index }}">
                                                                    {{ $item->status ?? 'No Status' }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div id="requirementList{{ $item->id }}" class="mb-2 d-flex flex-wrap gap-1">
                                                                @if ($item->requirements && $item->requirements->isNotEmpty())
                                                                    @foreach ($item->requirements as $req)
                                                                        <span class="badge badge-info shadow-sm d-flex align-items-center">
                                                                            {{ $req->name }}
                                                                            <a href="javascript:void(0)"
                                                                                onclick="removeRequirement({{ $item->id }}, {{ $req->id }})"
                                                                                class="text-white ml-2 hover-red">&times;</a>
                                                                        </span>
                                                                    @endforeach
                                                                @else
                                                                    <span class="badge badge-light text-muted border border-light">Belum ada dokumen</span>
                                                                @endif
                                                            </div>

                                                            <div class="input-group input-group-sm mb-1">
                                                                <select id="reqSelect{{ $item->id }}" class="custom-select rounded-left">
                                                                    <option value="">+ Pilih Dokumen</option>
                                                                    @foreach ($alldocumentrequirement as $req)
                                                                        <option value="{{ $req->id }}">{{ $req->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-success" onclick="addRequirement({{ $item->id }}, {{ $newbom->id }})"><i class="fas fa-plus"></i></button>
                                                                </div>
                                                            </div>

                                                            <div class="input-group input-group-sm">
                                                                <input type="text" id="newReqName{{ $item->id }}" class="form-control"
                                                                    placeholder="Input Baru...">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-primary"
                                                                        onclick="addNewRequirementType({{ $item->id }}, {{ $newbom->id }})"><i class="fas fa-save"></i></button>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <td class="text-center">
                                                            <button class="btn btn-light text-info btn-sm shadow-sm rounded-circle"
                                                                onclick="showRev({{ $item->id }})" title="Lihat Revisi">
                                                                <i class="fas fa-history"></i> Rev
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @php $penghitung++; @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                                <div class="p-4 bg-light-gray">
                                    <div class="table-responsive bg-white rounded-lg shadow-sm p-3">
                                        @if ($newbom->systemLogs && $newbom->systemLogs->isEmpty())
                                            <div class="alert alert-light text-center">
                                                <i class="fas fa-info-circle mr-2"></i> No history available for project {{ $newbom->id }}.
                                            </div>
                                        @else
                                            <table id="example6" class="table table-striped table-hover w-100">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th scope="col">No</th>
                                                        <th scope="col">Level</th>
                                                        <th scope="col">Nama Uploader</th>
                                                        <th scope="col">Waktu Upload</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col">Persentase Terakhir</th>
                                                        <th scope="col">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $histCount = 1; @endphp
                                                    @foreach ($newbom->systemLogs as $riwayat)
                                                        <tr>
                                                            <td>{{ $histCount++ }}</td>
                                                            <td><span class="badge badge-dark">{{ $riwayat->level }}</span></td>
                                                            <td>{{ $riwayat->user }}</td>
                                                            <td>{{ $riwayat->created_at->format('d/m/Y H:i') }}</td>
                                                            <td>{{ $riwayat->aksi }}</td>
                                                            <td>
                                                                @php
                                                                    $message = json_decode($riwayat->message, true);
                                                                @endphp
                                                                @if (isset($message['persentase']) && is_array($message['persentase']))
                                                                    <div class="d-flex flex-wrap gap-1">
                                                                        @foreach ($message['persentase'] as $key => $value)
                                                                            <div class="badge-group-modern small">
                                                                                <span class="badge bg-danger text-white">{{ $key ?? '' }}</span>
                                                                                <span class="badge bg-primary text-white">{{ $value ?? '' }}</span>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted small">N/A</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($riwayat->aksi == 'progressaddition')
                                                                    <a href="{{ route('newreports.showlog', ['newreport' => $newbom->id, 'logid' => $riwayat->id]) }}"
                                                                        class="btn btn-xs btn-outline-primary rounded-pill px-3">View Log</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>
@endsection

@section('rightsidebar')
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-header bg-modern-red text-white">
            <h5 class="mb-0 font-weight-bold" style="font-size: 1rem;"><i class="fas fa-stream mr-2"></i>Log Aktivitas</h5>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                </ul>
        </div>
    </div>
@endsection

@push('css')
<style>
       CSS VARIABLES & ROOT COLORS
       */
    :root {
        --primary-red: #e72a3a;
        --primary-red-dark: #c41022;
        --soft-red: #ffe5e9;
        --soft-blue: #e3f2fd;
        --soft-green: #e8f5e9;
        --soft-yellow: #fff8e1;
    }

       UTILITY CLASSES
       */
    .text-xs { font-size: 0.7rem; }
    .text-sm { font-size: 0.8rem; }
    .gap-1 { gap: 0.25rem; }

       BACKGROUND & BORDERS
       */
    .bg-modern-red {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%) !important;
        color: white;
    }

    .bg-soft-gradient {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .bg-light-gray {
        background-color: #f8f9fc;
    }

    /* Soft Background Colors for Icons */
    .bg-soft-red { background: linear-gradient(135deg, var(--soft-red) 0%, #ffc1cc 100%); }
    .bg-soft-blue { background: linear-gradient(135deg, var(--soft-blue) 0%, #bbdefb 100%); }
    .bg-soft-green { background: linear-gradient(135deg, var(--soft-green) 0%, #c8e6c9 100%); }
    .bg-soft-yellow { background: linear-gradient(135deg, var(--soft-yellow) 0%, #ffecb3 100%); }

    /* Border Radius */
    .rounded-xl { border-radius: 1rem !important; }
    .rounded-lg { border-radius: 0.75rem !important; }

    /* Border Left Colors */
    .border-left-danger,
    .card-border-left-danger { border-left: 4px solid #dc3545 !important; }
    
    .border-left-primary,
    .card-border-left-primary { border-left: 4px solid #007bff !important; }
    
    .border-left-success,
    .card-border-left-success { border-left: 4px solid #28a745 !important; }
    
    .border-left-warning,
    .card-border-left-warning { border-left: 4px solid #ffc107 !important; }

       CARD STYLES
       */
    .info-card,
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .info-card:hover,
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    }

    /* Icon Box in Cards */
    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: transform 0.3s ease;
    }

    .card:hover .icon-box {
        transform: scale(1.1) rotate(5deg);
    }

    /* Text Label for Cards */
    .text-label {
        text-transform: uppercase;
        font-weight: bold;
        color: #6c757d;
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 0.5rem;
    }

       NAVIGATION TABS
       */
    .nav-modern {
        border-bottom: 2px solid #f1f1f1;
    }

    .nav-modern .nav-link,
    .tab-modern {
        border: none;
        color: #6c757d;
        background: transparent;
        position: relative;
        transition: all 0.3s;
        padding: 1rem 1.5rem;
        border-bottom: 3px solid transparent;
    }

    .nav-modern .nav-link:hover,
    .tab-modern:hover {
        color: var(--primary-red);
        background-color: #f8f9fa;
    }

    .nav-modern .nav-link.active,
    .tab-modern.active {
        color: var(--primary-red) !important;
        background: transparent !important;
        border-bottom-color: var(--primary-red) !important;
    }

    .nav-modern .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--primary-red);
        border-radius: 3px 3px 0 0;
    }

       TABLE STYLING
       */
    .custom-table thead th {
        background-color: #343a40;
        color: #fff;
        border: none;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.8rem;
        padding: 12px;
        vertical-align: middle;
    }

    .custom-table tbody tr {
        transition: all 0.2s;
    }

    .custom-table tbody tr:hover {
        background-color: #fff5f6 !important;
    }

    .custom-table td {
        vertical-align: middle;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f1f1;
        padding: 10px;
    }

    /* Editable Cells */
    .editable-cell {
        padding: 4px 8px;
        border-radius: 4px;
        transition: background 0.2s;
        cursor: pointer;
        border-bottom: 1px dashed #ccc;
    }

    .editable-cell:hover,
    .editable-cell:focus {
        background-color: #fff3cd;
        outline: none;
    }

       BADGES
       */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.7em;
        border-radius: 4px;
    }

    .badge-soft-success {
        background-color: rgba(40, 167, 69, 0.15);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.2);
    }

    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.15);
        color: #856404;
        border: 1px solid rgba(255, 193, 7, 0.2);
    }

    .badge-soft-info {
        background-color: rgba(23, 162, 184, 0.1);
        color: #117a8b;
        border: 1px solid rgba(23, 162, 184, 0.2);
    }

    .badge-soft-primary {
        background-color: rgba(0, 123, 255, 0.1);
        color: #004085;
        border: 1px solid rgba(0, 123, 255, 0.2);
    }

    /* Badge Group */
    .badge-group-modern {
        display: inline-flex;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }

    .badge-group-modern .badge {
        border-radius: 0;
        font-weight: 400;
        padding: 0.5em 0.7em;
    }

       BUTTONS & TOOLS
       */
    .btn-tool-icon {
        padding: 0 5px;
        font-size: 0.9rem;
        background: transparent;
        border: none;
        transition: all 0.2s;
    }

    .btn-tool-icon:hover {
        background-color: #e9ecef;
        border-radius: 50%;
        color: var(--primary-red) !important;
    }

    .hover-red:hover {
        color: #dc3545 !important;
        text-decoration: none;
    }

       FORM CONTROLS
       */
    .custom-select:focus,
    .form-control:focus {
        border-color: var(--primary-red);
        box-shadow: 0 0 0 0.2rem rgba(231, 42, 58, 0.25);
    }

    /* Checkbox */
    .custom-control-input:checked~.custom-control-label::before {
        border-color: var(--primary-red);
        background-color: var(--primary-red);
    }

       ANIMATIONS
       */
    .progress-bar-animated {
        animation: progress-pulse 2s ease-in-out infinite;
    }

    @keyframes progress-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Badge Pulse Animation */
    .badge-danger {
        animation: badge-pulse 2s ease-in-out infinite;
    }

    @keyframes badge-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        50% { box-shadow: 0 0 0 5px rgba(220, 53, 69, 0); }
    }
</style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-5F4Ns+0Ks4bAwW7BDp40FZyKtC95Il7k5zO4A/EoW2I=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <script>
        // --- Semua Logic JS asli tetap dipertahankan ---

        function addNewRequirementType(newbomkomatId, newbomId) {
            let newReqName = document.getElementById('newReqName' + newbomkomatId).value.trim();

            if (!newReqName) {
                Swal.fire('Oops', 'Masukkan nama dokumen terlebih dahulu', 'warning');
                return;
            }

            $.ajax({
                url: '/newboms/add-new-requirement-type', 
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    newbomkomat_id: newbomkomatId,
                    name: newReqName
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil', response.message, 'success');
                        location.reload(); 
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan server', 'error');
                }
            });
        }

        function enableEdit(id) {
            document.getElementById('statusDisplay' + id).style.display = 'none';
            document.getElementById('editStatusForm' + id).style.display = 'inline-block';
        }

        function cancelEdit(id) {
            document.getElementById('statusDisplay' + id).style.display = 'inline';
            document.getElementById('editStatusForm' + id).style.display = 'none';
        }

        function updateStatus(id, newbom_id, statuslama) {
            const status = document.getElementById('status' + id).value;
            const saveButton = document.getElementById('saveButton' + id);
            const originalButtonText = saveButton.innerHTML;

            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            saveButton.disabled = true;

            $.ajax({
                url: '/newboms/update-komatstatus',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                    statuslama: statuslama,
                    newbom_id: newbom_id,
                    id: id
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            confirmButtonColor: '#e72a3a'
                        });
                        document.getElementById('statusDisplay' + id).innerText = status;
                        cancelEdit(id);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message,
                            confirmButtonColor: '#d33'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan.',
                        confirmButtonColor: '#d33'
                    });
                },
                complete: function() {
                    saveButton.innerHTML = originalButtonText;
                    saveButton.disabled = false;
                }
            });
        }

        function addRequirement(newbomkomatId, newbomId) {
            let reqId = document.getElementById('reqSelect' + newbomkomatId).value;
            if (!reqId) {
                Swal.fire('Oops', 'Pilih dokumen dulu', 'warning');
                return;
            }

            $.ajax({
                url: '/newboms/add-requirement',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    newbomkomat_id: newbomkomatId,
                    komat_requirement_id: reqId
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil', response.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                }
            });
        }

        function removeRequirement(newbomkomatId, reqId) {
            $.ajax({
                url: '/newboms/remove-requirement',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    newbomkomat_id: newbomkomatId,
                    komat_requirement_id: reqId
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Dihapus', response.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                    }
                }
            });
        }
    </script>

    <script>
        const groupprogress = @json($newbom);

        function getSpesifikasiOptions(selectedSpesifikasi) {
            let options = '<option value="" ' + (selectedSpesifikasi === '' ? 'selected' : '') + '></option>';
            for (const key in groupprogress) {
                if (groupprogress.hasOwnProperty(key)) {
                    const selected = key === selectedSpesifikasi ? 'selected' : '';
                    options += `<option value="${key}" ${selected}>${key}</option>`;
                }
            }
            return options;
        }

        function showRev(id) {
            Swal.fire({
                title: 'Loading Revision History...',
                text: 'Please wait while we fetch the data.',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('newbomkomat.history', ['id' => ':id']) }}".replace(':id', id),
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.success && response.histories.length > 0) {
                        let historyHtml = '<ul class="list-group list-group-flush text-left">';
                        response.histories.forEach(function(history) {
                            historyHtml += `
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong>Rev: ${history.rev}</strong> 
                                        <small class="text-muted">${history.updated_at}</small>
                                    </div>
                                    <div class="small">
                                        <div><strong>Kode:</strong> ${history.kodematerial}</div>
                                        <div><strong>Mat:</strong> ${history.material}</div>
                                        <div><strong>Status:</strong> ${history.status}</div>
                                    </div>
                                </li>`;
                        });
                        historyHtml += '</ul>';

                        Swal.fire({
                            title: 'Revision History',
                            html: historyHtml,
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#e72a3a'
                        });
                    } else {
                        Swal.fire({
                            title: 'No History Found',
                            text: 'No revision history available for this material.',
                            icon: 'info',
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#e72a3a'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to fetch revision history',
                        icon: 'error',
                        confirmButtonText: 'Close'
                    });
                }
            });
        }

        function downloadbom(idbom) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Anda yakin ingin mengexport item yang dipilih?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#e72a3a',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Export!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({title: 'Memproses...', showConfirmButton: false, didOpen: () => Swal.showLoading()});
                    $.ajax({
                        url: `/newboms/download/${idbom}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        xhrFields: {
                            responseType: 'blob' 
                        },
                        success: function(response, status, xhr) {
                            Swal.close();
                            const disposition = xhr.getResponseHeader('Content-Disposition');
                            const matches = /filename="([^"]*)"/.exec(disposition);
                            const filename = matches != null && matches[1] ? matches[1] : 'export.xlsx';

                            const blob = new Blob([response], {
                                type: xhr.getResponseHeader('Content-Type')
                            });
                            const link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.download = filename;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);

                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'File telah berhasil diunduh.',
                                icon: 'success',
                                confirmButtonColor: '#e72a3a'
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat mengexport item.',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }
    </script>
    <script>
        $(function() {
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "search": "Cari:",
                    "paginate": { "next": ">", "previous": "<" }
                }
            });

             // History Table
             $('#example6').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true
            });
        });
    </script>

    <script>
        $(function() {
            //Enable check and uncheck all functionality
            $('#checkAll').click(function() {
                var clicks = $(this).data('clicks');
                if (clicks) {
                    //Uncheck all checkboxes
                    $('input[name="document_ids[]"]').prop('checked', false);
                    $(this).find('i').removeClass('fa-check-square').addClass('fa-square');
                } else {
                    //Check first 10 checkboxes (Logic asli Anda)
                    $('input[name="document_ids[]"]:lt(10)').prop('checked', true);
                    $(this).find('i').removeClass('fa-square').addClass('fa-check-square');
                }
                $(this).data('clicks', !clicks);
            });
        });
    </script>
@endpush