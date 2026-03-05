@extends('layouts.universal')

@section('container2')
    <div class="p-3 bg-white border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="m-0"><strong>{{ $report->progressreportname }}</strong></h4>
                <small class="text-muted">Proyek: {{ $report->proyek_type }} | Status Global: {{ $report->status }}</small>
            </div>
            <div>
                <span class="badge badge-info">Revisi: {{ $report->revisi_ke ?? '0' }}</span>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        .main-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .table-section {
            flex: 0 0 auto;
            max-height: 40%;
            overflow-y: auto;
            background: #fff;
            border-bottom: 2px solid #dee2e6;
            padding: 15px;
        }

        .pdf-section {
            flex: 1;
            background: #525659;
        }

        .fullscreen-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .table-sm td, .table-sm th {
            font-size: 0.85rem;
        }
    </style>

    <div class="main-wrapper">
        <div class="table-section">
            <table class="table table-sm table-bordered table-hover m-0">
                <thead class="thead-light">
                    <tr>
                        <th>No Dokumen</th>
                        <th>Nama Dokumen</th>
                        <th>Drafter</th>
                        <th>Status Revisi</th>
                        <th>Revisi Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Menggunakan method dari model Progressreport
                        $revisiData = $report->revisiData();
                        $timeline = json_decode($report->timeline, true) ?? [];
                    @endphp

                    @forelse($revisiData as $doc)
                        @php
                            $noDoc = $doc['nodokumen'] ?? '';
                            $docTimeline = $timeline[$noDoc] ?? [];
                        @endphp
                        <tr>
                            <td>{{ $noDoc }}</td>
                            <td>{{ $doc['namadokumen'] ?? '-' }}</td>
                            <td>
                                @if($doc['drafter'])
                                    <span class="badge badge-success">{{ $doc['drafter'] }}</span>
                                @else
                                    <span class="badge badge-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($docTimeline['statusrevisi']))
                                    <span class="text-uppercase font-weight-bold {{ $docTimeline['statusrevisi'] == 'dibuka' ? 'text-primary' : 'text-danger' }}">
                                        {{ $docTimeline['statusrevisi'] }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <strong>{{ $docTimeline['revisionlast'] ?? '0' }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Data revisi tidak tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pdf-section">
            @if($report->linkspreadsheet)
                <iframe src="{{ $report->linkspreadsheet }}" class="fullscreen-iframe" loading="lazy"></iframe>
            @elseif($report->linkscript)
                <iframe src="{{ $report->linkscript }}" class="fullscreen-iframe" loading="lazy"></iframe>
            @else
                <div class="h-100 d-flex align-items-center justify-content-center text-white">
                    <div class="text-center">
                        <i class="fas fa-file-excel fa-3x mb-3"></i>
                        <p>Link dokumen (Spreadsheet/Script) belum dikonfigurasi.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection