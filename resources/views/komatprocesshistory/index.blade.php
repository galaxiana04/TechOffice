@extends('layouts.universal')

@php
    $authuser = auth()->user();
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('komatprocesshistory.index') }}">List KomRev</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="card card-danger card-outline">
        <div class="card-header">
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <h3 class="card-title text-bold">Page Monitoring KomRev </h3>
        </div>
        <div class="card-body">
            @if ($projects->isNotEmpty())
                <div class="mb-3">
                    <label for="projectSelect" class="form-label">Select Project:</label>
                    <div class="project-select-row">
                        <select id="projectSelect" class="form-select" onchange="filterByProject(this.value)">
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $project->title ?? 'Project ' . $project->id }}
                                </option>
                            @endforeach
                        </select>
                        <a href="{{ route('komatprocesshistory.showuploaddoc') }}" class="btn btn-primary btn-sm"
                            id="btn-upload-komrev">
                            <i class="fas fa-upload"></i> Upload Komrev
                        </a>
                    </div>
                </div>
                @foreach ($projects as $project)
                    <div class="project-table" id="project-table-{{ $project->id }}"
                        style="display: {{ $loop->first ? 'block' : 'none' }};">
                        @php
                            $groupedKomatProcesses = $komatProcesses
                                ->groupBy('komat_name')
                                ->map(function ($processes) use ($project) {
                                    return $processes
                                        ->flatMap(function ($process) {
                                            return $process->komatProcessHistories;
                                        })
                                        ->filter(function ($doc) use ($project) {
                                            return $doc->project_type_id == $project->id;
                                        });
                                })
                                ->filter(function ($documents) {
                                    return $documents->isNotEmpty();
                                });
                        @endphp

                        @if ($groupedKomatProcesses->isNotEmpty())
                            <div class="tab-wrapper">
                                <ul class="nav nav-tabs" id="unitTabs-{{ $project->id }}" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="unit-tab-{{ $project->id }}-all" data-bs-toggle="tab"
                                            data-bs-target="#unit-content-{{ $project->id }}-all" type="button" role="tab"
                                            aria-selected="true">All</button>
                                    </li>
                                    @foreach ($units as $unit)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="unit-tab-{{ $project->id }}-{{ $unit->id }}" data-bs-toggle="tab"
                                                data-bs-target="#unit-content-{{ $project->id }}-{{ $unit->id }}" type="button" role="tab"
                                                aria-selected="false">{{ $unit->name }}</button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="tab-content mt-3" id="unitTabContent-{{ $project->id }}">

                                {{-- ══════════════════════════════════════════════
                                TAB ALL
                                ══════════════════════════════════════════════ --}}
                                <div class="tab-pane fade show active" id="unit-content-{{ $project->id }}-all" role="tabpanel">
                                    <div class="table-responsive-wrapper">
                                        <table class="table table-bordered table-hover table-striped komrev-table"
                                            id="table-{{ $project->id }}-all">
                                            <thead>
                                                <tr>
                                                    <th class="col-check"><input type="checkbox" class="selectAll"
                                                            onchange="toggleAllCheckboxes(this, '{{ $project->id }}-all')"></th>
                                                    <th class="col-no">No</th>
                                                    <th class="col-komat">Komat</th>
                                                    <th class="col-material">Material</th>
                                                    <th class="col-supplier">Supplier</th>
                                                    <th class="col-req">Req</th>
                                                    <th class="col-rev">Rev</th>
                                                    <th class="col-disc">Disc No</th>
                                                    <th class="col-status">Status</th>
                                                    <th class="col-posisi">Posisi Komrev</th>
                                                    <th class="col-action">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $counter = 1; @endphp
                                                @foreach ($groupedKomatProcesses as $komat_name => $documents)
                                                    @php
                                                        $supplierGroups = $documents->groupBy(fn($doc) => $doc->supplier->name ?? 'N/A')->values();
                                                    @endphp
                                                    @foreach ($supplierGroups as $spGroup)
                                                        @php
                                                            $firstDocInGroup = $spGroup->first();
                                                            $supplierName = $firstDocInGroup->supplier->name ?? 'N/A';
                                                            $requirementGroups = $spGroup->groupBy(fn($doc) => $doc->requirement->name ?? 'N/A')->values();
                                                        @endphp
                                                        @foreach ($requirementGroups as $reqGroup)
                                                            @php
                                                                $rowspanRequirement = $reqGroup->count();
                                                                $rowspanSupplier = $spGroup->count();
                                                                $firstDocInReqGroup = $reqGroup->first();
                                                                $requirementName = $firstDocInReqGroup->requirement->name ?? 'N/A';
                                                            @endphp
                                                            @foreach ($reqGroup as $index => $document)
                                                                @php
                                                                    $hasLogistikUpload = $hasDiscussion = $hasResume = $hasSMLevel = $hasMTPRReview = $hasLogistikDone = $hasManagerLogistikNeeded = $hasSeniorManagerLogistikNeeded = false;
                                                                    $logistikApproved = $allApproved = $allResumeApproved = $allSMLevelApproved = $allMTPRReviewApproved = $allLogistikDoneApproved = $allManagerLogistikNeededApproved = $allSeniorManagerLogistikNeededApproved = false;

                                                                    if ($document->komatHistReqs) {
                                                                        $hasLogistikUpload = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'logistik_upload'));
                                                                        $logistikApproved = $hasLogistikUpload && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'logistik_upload')->every(fn($p) => $p->status_process === 'done'));

                                                                        $firstReq = $document->komatHistReqs->first();
                                                                        $unitStatuses = [];
                                                                        foreach ($projectpics as $name => $meta) {
                                                                            $uid = $meta['id'];
                                                                            $hd = $firstReq && $firstReq->komatPositions && $firstReq->komatPositions->some(fn($p) => $p->level === 'discussion' && $p->unit_id === $uid);
                                                                            $ap = $hd && $firstReq->komatPositions->filter(fn($p) => $p->level === 'discussion' && $p->unit_id === $uid)->every(fn($p) => $p->status_process === 'done');
                                                                            $unitStatuses[$name] = !$hd ? null : ($ap ? 'Aktif' : 'Ongoing');
                                                                        }

                                                                        $hasDiscussion = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'discussion'));
                                                                        $allApproved = $hasDiscussion && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'discussion')->every(fn($p) => $p->status_process === 'done'));
                                                                        $hasResume = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'resume'));
                                                                        $allResumeApproved = $hasResume && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'resume')->every(fn($p) => $p->status_process === 'done'));
                                                                        $hasSMLevel = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'sm_level'));
                                                                        $allSMLevelApproved = $hasSMLevel && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'sm_level')->every(fn($p) => $p->status_process === 'done'));
                                                                        $hasMTPRReview = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'mtpr_review'));
                                                                        $allMTPRReviewApproved = $hasMTPRReview && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'mtpr_review')->every(fn($p) => $p->status_process === 'done'));
                                                                        $hasLogistikDone = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'logistik_done'));
                                                                        $allLogistikDoneApproved = $hasLogistikDone && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'logistik_done')->every(fn($p) => $p->status_process === 'done'));
                                                                        $hasManagerLogistikNeeded = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'managerlogistikneeded'));
                                                                        $allManagerLogistikNeededApproved = $hasManagerLogistikNeeded && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'managerlogistikneeded')->every(fn($p) => $p->status_process === 'done'));
                                                                        $hasSeniorManagerLogistikNeeded = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'seniormanagerlogistikneeded'));
                                                                        $allSeniorManagerLogistikNeededApproved = $hasSeniorManagerLogistikNeeded && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'seniormanagerlogistikneeded')->every(fn($p) => $p->status_process === 'done'));
                                                                    }

                                                                    $logistikIndicator = !$hasLogistikUpload ? 'red' : ($logistikApproved ? 'green' : 'orange');
                                                                    $discussionIndicator = !$hasDiscussion ? 'red' : ($allApproved ? 'green' : 'orange');
                                                                    $resumeIndicator = !$hasResume ? 'red' : ($allResumeApproved ? 'green' : 'orange');
                                                                    $smLevelIndicator = !$hasSMLevel ? 'red' : ($allSMLevelApproved ? 'green' : 'orange');
                                                                    $mtprReviewIndicator = !$hasMTPRReview ? 'red' : ($allMTPRReviewApproved ? 'green' : 'orange');
                                                                    $logistikDoneIndicator = !$hasLogistikDone ? 'red' : ($allLogistikDoneApproved ? 'green' : 'orange');
                                                                    $managerLogistikNeededIndicator = !$hasManagerLogistikNeeded ? 'red' : ($allManagerLogistikNeededApproved ? 'green' : 'orange');
                                                                    $seniorManagerLogistikNeededIndicator = !$hasSeniorManagerLogistikNeeded ? 'red' : ($allSeniorManagerLogistikNeededApproved ? 'green' : 'orange');

                                                                    $classbox1 = !$hasLogistikUpload ? 'box' : ($logistikApproved ? 'boxblue' : 'boxorange');
                                                                    $classbox2 = !$hasDiscussion ? 'box' : ($allApproved ? 'boxblue' : 'boxorange');
                                                                    $classbox3 = !$hasResume ? 'box' : ($allResumeApproved ? 'boxblue' : 'boxorange');
                                                                    $classbox4 = !$hasSMLevel ? 'box' : ($allSMLevelApproved ? 'boxblue' : 'boxorange');
                                                                    $classbox5 = !$hasMTPRReview ? 'box' : ($allMTPRReviewApproved ? 'boxblue' : 'boxorange');
                                                                    $classbox6 = !$hasLogistikDone ? 'box' : ($allLogistikDoneApproved ? 'boxblue' : 'boxorange');
                                                                    $classbox7 = !$hasManagerLogistikNeeded ? 'box' : ($allManagerLogistikNeededApproved ? 'boxblue' : 'boxorange');
                                                                    $classbox8 = !$hasSeniorManagerLogistikNeeded ? 'box' : ($allSeniorManagerLogistikNeededApproved ? 'boxblue' : 'boxorange');

                                                                    $positionPercentage = match (true) {
                                                                        $document->logisticauthoritylevel == 'seniormanagerneeded' && $allSeniorManagerLogistikNeededApproved => 100,
                                                                        $document->logisticauthoritylevel == 'seniormanagerneeded' && $allManagerLogistikNeededApproved => 87.5,
                                                                        $document->logisticauthoritylevel == 'managerneeded' && $allManagerLogistikNeededApproved => 100,
                                                                        $allLogistikDoneApproved => 75,
                                                                        $allMTPRReviewApproved => 62.5,
                                                                        $allSMLevelApproved => 50,
                                                                        $allResumeApproved => 37.5,
                                                                        $allApproved => 25,
                                                                        $logistikApproved => 12.5,
                                                                        default => 0,
                                                                    };
                                                                @endphp
                                                                <tr>
                                                                    @if ($index === 0 && $spGroup === $supplierGroups->first() && $reqGroup === $requirementGroups->first())
                                                                        <td rowspan="{{ $documents->count() }}">
                                                                            <div class="icheck-primary">
                                                                                <input type="checkbox" value="{{ $document->id }}"
                                                                                    name="document_ids_{{ $document->id }}[]"
                                                                                    id="checkbox{{ $document->id }}">
                                                                                <label for="checkbox{{ $document->id }}"></label>
                                                                            </div>
                                                                        </td>
                                                                        <td rowspan="{{ $documents->count() }}">{{ $counter++ }}</td>
                                                                        <td rowspan="{{ $documents->count() }}">{{ $komat_name ?? 'N/A' }}</td>
                                                                        <td rowspan="{{ $documents->count() }}">
                                                                            {{ $firstDocInGroup->newbomkomat->material ?? 'N/A' }}
                                                                        </td>
                                                                    @endif
                                                                    @if ($index === 0 && $reqGroup === $requirementGroups->first())
                                                                        <td rowspan="{{ $rowspanSupplier }}">{{ $supplierName }}</td>
                                                                    @endif
                                                                    @if ($index === 0)
                                                                        <td rowspan="{{ $rowspanRequirement }}">{{ $requirementName }}</td>
                                                                    @endif
                                                                    <td>{{ $document->revision ?? 'N/A' }}</td>
                                                                    <td>{{ $document->discussion_number ?? 'N/A' }}</td>
                                                                    <td>
                                                                        <div class="d-flex flex-column align-items-center gap-1">
                                                                            <span
                                                                                class="badge {{ ($document->status ?? '') === 'Terbuka' ? 'bg-danger' : (($document->status ?? '') === 'Tertutup' ? 'bg-success' : 'bg-secondary') }} w-100 text-center py-2">
                                                                                {{ $document->status ?? 'N/A' }}
                                                                            </span>
                                                                            <span
                                                                                class="badge bg-info w-100 text-center py-2">{{ $document->documentstatus ?? 'N/A' }}</span>
                                                                            <span
                                                                                class="badge bg-warning w-100 text-center py-2">{{ $document->logisticauthoritylevel ?? 'N/A' }}</span>
                                                                            <span
                                                                                class="badge bg-{{ $positionPercentage === 100 ? 'success' : 'warning' }} w-100 text-center py-2">
                                                                                {{ $positionPercentage }}% Completed
                                                                            </span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="posisi-cell">
                                                                        <div class="flow-h">
                                                                            <span class="arrow-invis">→</span>
                                                                            <a class="{{ $classbox1 }} flow-box" href="#">
                                                                                <div class="indicator {{ $logistikIndicator }}"
                                                                                    title="{{ !$hasLogistikUpload ? 'Logistik upload belum ada' : ($logistikApproved ? 'Logistik upload selesai' : 'Logistik upload sedang berlangsung') }}">
                                                                                </div>
                                                                                <span class="keterangan">Logistik</span>
                                                                            </a>
                                                                            <span class="arrow">→</span>
                                                                            <a class="{{ $classbox2 }} flow-box" href="#">
                                                                                <div class="indicator {{ $discussionIndicator }}"
                                                                                    title="{{ !$hasDiscussion ? 'Diskusi belum ada' : ($allApproved ? 'Semua unit diskusi selesai' : 'Diskusi sedang berlangsung') }}">
                                                                                </div>
                                                                                <span class="keterangan">Diskusi</span>
                                                                            </a>
                                                                            <span class="arrow">→</span>
                                                                            <a class="{{ $classbox3 }} flow-box" href="#">
                                                                                <div class="indicator {{ $resumeIndicator }}"
                                                                                    title="{{ !$hasResume ? 'Resume belum ada' : ($allResumeApproved ? 'Semua resume selesai' : 'Resume sedang berlangsung') }}">
                                                                                </div>
                                                                                <span class="keterangan">Resume</span>
                                                                            </a>
                                                                            <span class="arrow">→</span>
                                                                            <a class="{{ $classbox4 }} flow-box" href="#">
                                                                                <div class="indicator {{ $smLevelIndicator }}"
                                                                                    title="{{ !$hasSMLevel ? 'SM Level belum ada' : ($allSMLevelApproved ? 'Semua SM Level selesai' : 'SM Level sedang berlangsung') }}">
                                                                                </div>
                                                                                <span class="keterangan">SM Level</span>
                                                                            </a>
                                                                            @if ($document->documentstatus !== 'rejectedbysm')
                                                                                <span class="arrow">→</span>
                                                                                <a class="{{ $classbox5 }} flow-box" href="#">
                                                                                    <div class="indicator {{ $mtprReviewIndicator }}"
                                                                                        title="{{ !$hasMTPRReview ? 'MTPR Review belum ada' : ($allMTPRReviewApproved ? 'Semua MTPR Review selesai' : 'MTPR Review sedang berlangsung') }}">
                                                                                    </div>
                                                                                    <span class="keterangan">MTPR Review</span>
                                                                                </a>
                                                                                <span class="arrow">→</span>
                                                                                <a class="{{ $classbox6 }} flow-box" href="#">
                                                                                    <div class="indicator {{ $logistikDoneIndicator }}"
                                                                                        title="{{ !$hasLogistikDone ? 'Logistik belum ada' : ($allLogistikDoneApproved ? 'Logistik selesai' : 'Logistik sedang berlangsung') }}">
                                                                                    </div>
                                                                                    <span class="keterangan">Purchaser</span>
                                                                                </a>
                                                                                @if ($document->logisticauthoritylevel == 'managerneeded' || $document->logisticauthoritylevel == 'seniormanagerneeded')
                                                                                    <span class="arrow">→</span>
                                                                                    <a class="{{ $classbox7 }} flow-box" href="#">
                                                                                        <div class="indicator {{ $managerLogistikNeededIndicator }}"
                                                                                            title="{{ !$hasManagerLogistikNeeded ? 'Manager Logistik belum ada' : ($allManagerLogistikNeededApproved ? 'Manager Logistik selesai' : 'Manager Logistik sedang berlangsung') }}">
                                                                                        </div>
                                                                                        <span class="keterangan">Mgr. Logistik</span>
                                                                                    </a>
                                                                                @endif
                                                                                @if ($document->logisticauthoritylevel == 'seniormanagerneeded')
                                                                                    <span class="arrow">→</span>
                                                                                    <a class="{{ $classbox8 }} flow-box" href="#">
                                                                                        <div class="indicator {{ $seniorManagerLogistikNeededIndicator }}"
                                                                                            title="{{ !$hasSeniorManagerLogistikNeeded ? 'Senior Manager Logistik belum ada' : ($allSeniorManagerLogistikNeededApproved ? 'Senior Manager Logistik selesai' : 'Senior Manager Logistik sedang berlangsung') }}">
                                                                                        </div>
                                                                                        <span class="keterangan">SM. Logistik</span>
                                                                                    </a>
                                                                                @endif
                                                                                <span class="arrow-invis">→</span>
                                                                            @endif
                                                                        </div>
                                                                    </td>
                                                                    <td class="project-actions text-right">
                                                                        <a class="btn btn-primary btn-sm"
                                                                            href="/komatprocesshistory/show/{{ $document->id }}">
                                                                            <i class="fas fa-folder"></i> Detail
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- ══════════════════════════════════════════════
                                TAB UNIT
                                ══════════════════════════════════════════════ --}}
                                @foreach ($units as $unit)
                                    <div class="tab-pane fade" id="unit-content-{{ $project->id }}-{{ $unit->id }}" role="tabpanel">
                                        @php
                                            $unitDocuments = $groupedKomatProcesses
                                                ->flatMap(fn($docs) => $docs->filter(
                                                    fn($doc) =>
                                                    $doc->komatHistReqs->some(
                                                        fn($req) =>
                                                        $req->komatPositions->some(fn($pos) => $pos->unit_id === $unit->id)
                                                    )
                                                ))
                                                ->groupBy(fn($doc) => $doc->komatProcess->komat_name ?? 'N/A')
                                                ->filter(fn($docs) => $docs->isNotEmpty());
                                        @endphp

                                        @if ($unitDocuments->isNotEmpty())
                                            <div class="table-responsive-wrapper">
                                                <table class="table table-bordered table-hover table-striped komrev-table"
                                                    id="table-{{ $project->id }}-{{ $unit->id }}">
                                                    <thead>
                                                        <tr>
                                                            <th class="col-check"><input type="checkbox" class="selectAll"
                                                                    onchange="toggleAllCheckboxes(this, '{{ $project->id }}-{{ $unit->id }}')">
                                                            </th>
                                                            <th class="col-no">No</th>
                                                            <th class="col-komat">Komat</th>
                                                            <th class="col-material">Material</th>
                                                            <th class="col-supplier">Supplier</th>
                                                            <th class="col-req">Req</th>
                                                            <th class="col-rev">Rev</th>
                                                            <th class="col-disc">Disc No</th>
                                                            <th class="col-status">Status</th>
                                                            <th class="col-posisi">Posisi Komrev</th>
                                                            <th class="col-action">Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $counter = 1; @endphp
                                                        @foreach ($unitDocuments as $komat_name => $documents)
                                                            @php
                                                                $supplierGroups = $documents->groupBy(fn($d) => $d->supplier->name ?? 'N/A')->values();
                                                            @endphp
                                                            @foreach ($supplierGroups as $spGroup)
                                                                @php
                                                                    $firstDocInGroup = $spGroup->first();
                                                                    $supplierName = $firstDocInGroup->supplier->name ?? 'N/A';
                                                                    $requirementGroups = $spGroup->groupBy(fn($d) => $d->requirement->name ?? 'N/A')->values();
                                                                @endphp
                                                                @foreach ($requirementGroups as $reqGroup)
                                                                    @php
                                                                        $rowspanRequirement = $reqGroup->count();
                                                                        $rowspanSupplier = $spGroup->count();
                                                                        $firstDocInReqGroup = $reqGroup->first();
                                                                        $requirementName = $firstDocInReqGroup->requirement->name ?? 'N/A';
                                                                    @endphp
                                                                    @foreach ($reqGroup as $index => $document)
                                                                        @php
                                                                            $hasLogistikUpload = $hasDiscussion = $hasResume = $hasSMLevel = $hasMTPRReview = $hasLogistikDone = $hasManagerLogistikNeeded = $hasSeniorManagerLogistikNeeded = false;
                                                                            $logistikApproved = $allApproved = $allResumeApproved = $allSMLevelApproved = $allMTPRReviewApproved = $allLogistikDoneApproved = $allManagerLogistikNeededApproved = $allSeniorManagerLogistikNeededApproved = false;

                                                                            if ($document->komatHistReqs) {
                                                                                $hasLogistikUpload = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'logistik_upload'));
                                                                                $logistikApproved = $hasLogistikUpload && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'logistik_upload')->every(fn($p) => $p->status_process === 'done'));

                                                                                $firstReq = $document->komatHistReqs->first();
                                                                                $unitStatuses = [];
                                                                                foreach ($projectpics as $name => $meta) {
                                                                                    $uid = $meta['id'];
                                                                                    $hd = $firstReq && $firstReq->komatPositions && $firstReq->komatPositions->some(fn($p) => $p->level === 'discussion' && $p->unit_id === $uid);
                                                                                    $ap = $hd && $firstReq->komatPositions->filter(fn($p) => $p->level === 'discussion' && $p->unit_id === $uid)->every(fn($p) => $p->status_process === 'done');
                                                                                    $unitStatuses[$name] = !$hd ? null : ($ap ? 'Aktif' : 'Ongoing');
                                                                                }

                                                                                $hasDiscussion = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'discussion'));
                                                                                $allApproved = $hasDiscussion && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'discussion')->every(fn($p) => $p->status_process === 'done'));
                                                                                $hasResume = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'resume'));
                                                                                $allResumeApproved = $hasResume && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'resume')->every(fn($p) => $p->status_process === 'done'));
                                                                                $hasSMLevel = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'sm_level'));
                                                                                $allSMLevelApproved = $hasSMLevel && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'sm_level')->every(fn($p) => $p->status_process === 'done'));
                                                                                $hasMTPRReview = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'mtpr_review'));
                                                                                $allMTPRReviewApproved = $hasMTPRReview && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'mtpr_review')->every(fn($p) => $p->status_process === 'done'));
                                                                                $hasLogistikDone = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'logistik_done'));
                                                                                $allLogistikDoneApproved = $hasLogistikDone && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'logistik_done')->every(fn($p) => $p->status_process === 'done'));
                                                                                $hasManagerLogistikNeeded = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'managerlogistikneeded'));
                                                                                $allManagerLogistikNeededApproved = $hasManagerLogistikNeeded && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'managerlogistikneeded')->every(fn($p) => $p->status_process === 'done'));
                                                                                $hasSeniorManagerLogistikNeeded = $document->komatHistReqs->some(fn($r) => $r->komatPositions && $r->komatPositions->some(fn($p) => $p->level === 'seniormanagerlogistikneeded'));
                                                                                $allSeniorManagerLogistikNeededApproved = $hasSeniorManagerLogistikNeeded && $document->komatHistReqs->every(fn($r) => $r->komatPositions->filter(fn($p) => $p->level === 'seniormanagerlogistikneeded')->every(fn($p) => $p->status_process === 'done'));
                                                                            }

                                                                            $logistikIndicator = !$hasLogistikUpload ? 'red' : ($logistikApproved ? 'green' : 'orange');
                                                                            $discussionIndicator = !$hasDiscussion ? 'red' : ($allApproved ? 'green' : 'orange');
                                                                            $resumeIndicator = !$hasResume ? 'red' : ($allResumeApproved ? 'green' : 'orange');
                                                                            $smLevelIndicator = !$hasSMLevel ? 'red' : ($allSMLevelApproved ? 'green' : 'orange');
                                                                            $mtprReviewIndicator = !$hasMTPRReview ? 'red' : ($allMTPRReviewApproved ? 'green' : 'orange');
                                                                            $logistikDoneIndicator = !$hasLogistikDone ? 'red' : ($allLogistikDoneApproved ? 'green' : 'orange');
                                                                            $managerLogistikNeededIndicator = !$hasManagerLogistikNeeded ? 'red' : ($allManagerLogistikNeededApproved ? 'green' : 'orange');
                                                                            $seniorManagerLogistikNeededIndicator = !$hasSeniorManagerLogistikNeeded ? 'red' : ($allSeniorManagerLogistikNeededApproved ? 'green' : 'orange');

                                                                            $classbox1 = !$hasLogistikUpload ? 'box' : ($logistikApproved ? 'boxblue' : 'boxorange');
                                                                            $classbox2 = !$hasDiscussion ? 'box' : ($allApproved ? 'boxblue' : 'boxorange');
                                                                            $classbox3 = !$hasResume ? 'box' : ($allResumeApproved ? 'boxblue' : 'boxorange');
                                                                            $classbox4 = !$hasSMLevel ? 'box' : ($allSMLevelApproved ? 'boxblue' : 'boxorange');
                                                                            $classbox5 = !$hasMTPRReview ? 'box' : ($allMTPRReviewApproved ? 'boxblue' : 'boxorange');
                                                                            $classbox6 = !$hasLogistikDone ? 'box' : ($allLogistikDoneApproved ? 'boxblue' : 'boxorange');
                                                                            $classbox7 = !$hasManagerLogistikNeeded ? 'box' : ($allManagerLogistikNeededApproved ? 'boxblue' : 'boxorange');
                                                                            $classbox8 = !$hasSeniorManagerLogistikNeeded ? 'box' : ($allSeniorManagerLogistikNeededApproved ? 'boxblue' : 'boxorange');

                                                                            $positionPercentage = match (true) {
                                                                                $document->logisticauthoritylevel == 'seniormanagerneeded' && $allSeniorManagerLogistikNeededApproved => 100,
                                                                                $document->logisticauthoritylevel == 'seniormanagerneeded' && $allManagerLogistikNeededApproved => 87.5,
                                                                                $document->logisticauthoritylevel == 'managerneeded' && $allManagerLogistikNeededApproved => 100,
                                                                                $allLogistikDoneApproved => 75,
                                                                                $allMTPRReviewApproved => 62.5,
                                                                                $allSMLevelApproved => 50,
                                                                                $allResumeApproved => 37.5,
                                                                                $allApproved => 25,
                                                                                $logistikApproved => 12.5,
                                                                                default => 0,
                                                                            };
                                                                        @endphp
                                                                        <tr>
                                                                            @if ($index === 0 && $spGroup === $supplierGroups->first() && $reqGroup === $requirementGroups->first())
                                                                                <td rowspan="{{ $documents->count() }}">
                                                                                    <div class="icheck-primary">
                                                                                        <input type="checkbox" value="{{ $document->id }}"
                                                                                            name="document_ids_{{ $document->id }}[]"
                                                                                            id="checkbox{{ $document->id }}u{{ $unit->id }}">
                                                                                        <label for="checkbox{{ $document->id }}u{{ $unit->id }}"></label>
                                                                                    </div>
                                                                                </td>
                                                                                <td rowspan="{{ $documents->count() }}">{{ $counter++ }}</td>
                                                                                <td rowspan="{{ $documents->count() }}">{{ $komat_name ?? 'N/A' }}</td>
                                                                                <td rowspan="{{ $documents->count() }}">
                                                                                    {{ $firstDocInGroup->newbomkomat->material ?? 'N/A' }}
                                                                                </td>
                                                                            @endif
                                                                            @if ($index === 0 && $reqGroup === $requirementGroups->first())
                                                                                <td rowspan="{{ $rowspanSupplier }}">{{ $supplierName }}</td>
                                                                            @endif
                                                                            @if ($index === 0)
                                                                                <td rowspan="{{ $rowspanRequirement }}">{{ $requirementName }}</td>
                                                                            @endif
                                                                            <td>{{ $document->revision ?? 'N/A' }}</td>
                                                                            <td>{{ $document->discussion_number ?? 'N/A' }}</td>
                                                                            <td>
                                                                                <div class="d-flex flex-column align-items-center gap-1">
                                                                                    <span
                                                                                        class="badge {{ ($document->status ?? '') === 'Terbuka' ? 'bg-danger' : (($document->status ?? '') === 'Tertutup' ? 'bg-success' : 'bg-secondary') }} w-100 text-center py-2">
                                                                                        {{ $document->status ?? 'N/A' }}
                                                                                    </span>
                                                                                    <span
                                                                                        class="badge bg-info w-100 text-center py-2">{{ $document->documentstatus ?? 'N/A' }}</span>
                                                                                    <span
                                                                                        class="badge bg-warning w-100 text-center py-2">{{ $document->logisticauthoritylevel ?? 'N/A' }}</span>
                                                                                    <span
                                                                                        class="badge bg-{{ $positionPercentage === 100 ? 'success' : 'warning' }} w-100 text-center py-2">
                                                                                        {{ $positionPercentage }}% Completed
                                                                                    </span>
                                                                                </div>
                                                                            </td>
                                                                            <td class="posisi-cell posisi-unit">
                                                                                <div class="flow-unit">
                                                                                    <a class="{{ $classbox1 }} flow-box" href="#">
                                                                                        <div class="indicator {{ $logistikIndicator }}"
                                                                                            title="{{ !$hasLogistikUpload ? 'Logistik upload belum ada' : ($logistikApproved ? 'Logistik upload selesai' : 'Logistik upload sedang berlangsung') }}">
                                                                                        </div>
                                                                                        <span class="keterangan">Logistik</span>
                                                                                    </a>
                                                                                    <span class="arrow unit-arrow">→</span>

                                                                                    <div class="disc-group">
                                                                                        <div class="{{ $classbox2 }} disc-subbox">
                                                                                            <div class="disc-label">Eng</div>
                                                                                            <ul class="disc-list">
                                                                                                @foreach (['Product Engineering', 'Mechanical Engineering System', 'Electrical Engineering System', 'Quality Engineering', 'RAMS'] as $projectpic)
                                                                                                    @php $st = $unitStatuses[$projectpic] ?? null; @endphp
                                                                                                    <li>
                                                                                                        <div class="indicator {{ $st === 'Aktif' ? 'green' : ($st === 'Ongoing' ? 'orange' : ($st === null ? 'black' : 'red')) }}"
                                                                                                            title="{{ $st === 'Aktif' ? $projectpic . ' sudah approve' : ($st === 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve' : ($st === null ? $projectpic . ' tidak terlibat' : $projectpic . ' belum dikerjakan')) }}">
                                                                                                        </div>
                                                                                                        <span
                                                                                                            class="keterangan">{{ $projectpics[$projectpic]['singkatan'] }}</span>
                                                                                                    </li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </div>
                                                                                        <div class="{{ $classbox2 }} disc-subbox">
                                                                                            <div class="disc-label">Des</div>
                                                                                            <ul class="disc-list">
                                                                                                @foreach (['Desain Mekanik & Interior', 'Desain Bogie & Wagon', 'Desain Carbody', 'Desain Elektrik'] as $projectpic)
                                                                                                    @php $st = $unitStatuses[$projectpic] ?? null; @endphp
                                                                                                    <li>
                                                                                                        <div class="indicator {{ $st === 'Aktif' ? 'green' : ($st === 'Ongoing' ? 'orange' : ($st === null ? 'black' : 'red')) }}"
                                                                                                            title="{{ $st === 'Aktif' ? $projectpic . ' sudah approve' : ($st === 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve' : ($st === null ? $projectpic . ' tidak terlibat' : $projectpic . ' belum dikerjakan')) }}">
                                                                                                        </div>
                                                                                                        <span
                                                                                                            class="keterangan">{{ $projectpics[$projectpic]['singkatan'] }}</span>
                                                                                                    </li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </div>
                                                                                        <div class="{{ $classbox2 }} disc-subbox">
                                                                                            <div class="disc-label">TP</div>
                                                                                            <ul class="disc-list">
                                                                                                @foreach (['Preparation & Support', 'Welding Technology', 'Shop Drawing', 'Teknologi Proses'] as $projectpic)
                                                                                                    @php $st = $unitStatuses[$projectpic] ?? null; @endphp
                                                                                                    <li>
                                                                                                        <div class="indicator {{ $st === 'Aktif' ? 'green' : ($st === 'Ongoing' ? 'orange' : ($st === null ? 'black' : 'red')) }}"
                                                                                                            title="{{ $st === 'Aktif' ? $projectpic . ' sudah approve' : ($st === 'Ongoing' ? $projectpic . ' sudah melakukan feedback dan belum approve' : ($st === null ? $projectpic . ' tidak terlibat' : $projectpic . ' belum dikerjakan')) }}">
                                                                                                        </div>
                                                                                                        <span
                                                                                                            class="keterangan">{{ $projectpics[$projectpic]['singkatan'] }}</span>
                                                                                                    </li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        </div>
                                                                                    </div>

                                                                                    <span class="arrow unit-arrow">→</span>

                                                                                    <div class="{{ $classbox3 }} vert-stack">
                                                                                        <div class="vert-row">
                                                                                            <div class="indicator {{ $resumeIndicator }}"
                                                                                                title="{{ !$hasResume ? 'Resume belum ada' : ($allResumeApproved ? 'Semua resume selesai' : 'Resume sedang berlangsung') }}">
                                                                                            </div>
                                                                                            <span class="keterangan">Resume</span>
                                                                                        </div>
                                                                                        <div class="vert-sep">↓</div>
                                                                                        <div class="vert-row">
                                                                                            <div class="indicator {{ $smLevelIndicator }}"
                                                                                                title="{{ !$hasSMLevel ? 'SM Level belum ada' : ($allSMLevelApproved ? 'Semua SM Level selesai' : 'SM Level sedang berlangsung') }}">
                                                                                            </div>
                                                                                            <span class="keterangan">SM Level</span>
                                                                                        </div>
                                                                                        <div class="vert-sep">↓</div>
                                                                                        <div class="vert-row">
                                                                                            <div class="indicator {{ $mtprReviewIndicator }}"
                                                                                                title="{{ !$hasMTPRReview ? 'Review belum ada' : ($allMTPRReviewApproved ? 'Semua Review selesai' : 'Review sedang berlangsung') }}">
                                                                                            </div>
                                                                                            <span class="keterangan">Review</span>
                                                                                        </div>
                                                                                        <div class="vert-sep">↓</div>
                                                                                        <div class="vert-row">
                                                                                            <div class="indicator {{ $logistikDoneIndicator }}"
                                                                                                title="{{ !$hasLogistikDone ? 'Purchaser belum ada' : ($allLogistikDoneApproved ? 'Purchaser selesai' : 'Purchaser sedang berlangsung') }}">
                                                                                            </div>
                                                                                            <span class="keterangan">Purchaser</span>
                                                                                        </div>
                                                                                        <div class="vert-sep">↓</div>
                                                                                        <div class="vert-row">
                                                                                            <div class="indicator {{ $managerLogistikNeededIndicator }}"
                                                                                                title="{{ !$hasManagerLogistikNeeded ? 'M. Logistik belum ada' : ($allManagerLogistikNeededApproved ? 'M. Logistik selesai' : 'M. Logistik sedang berlangsung') }}">
                                                                                            </div>
                                                                                            <span class="keterangan">M. Logistik</span>
                                                                                        </div>
                                                                                        <div class="vert-sep">↓</div>
                                                                                        <div class="vert-row">
                                                                                            <div class="indicator {{ $seniorManagerLogistikNeededIndicator }}"
                                                                                                title="{{ !$hasSeniorManagerLogistikNeeded ? 'SM. Logistik belum ada' : ($allSeniorManagerLogistikNeededApproved ? 'SM. Logistik selesai' : 'SM. Logistik sedang berlangsung') }}">
                                                                                            </div>
                                                                                            <span class="keterangan">SM. Logistik</span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>

                                                                            <td class="project-actions text-right">
                                                                                <a class="btn btn-primary btn-sm"
                                                                                    href="/komatprocesshistory/show/{{ $document->id }}">
                                                                                    <i class="fas fa-folder"></i> Detail
                                                                                </a>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            @endforeach
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                Tidak ada dokumen untuk unit {{ $unit->name }} di proyek
                                                {{ $project->title ?? 'Project ' . $project->id }}.
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                            </div>{{-- /tab-content --}}
                        @else
                            <div class="alert alert-info">Tidak ada dokumen untuk proyek
                                {{ $project->title ?? 'Project ' . $project->id }}.
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">Tidak ada proyek yang tersedia.</div>
            @endif
        </div>
    </div>
@endsection


@push('scripts')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        /* ================================================================
               KOMREV — RESPONSIVE FULL LAYOUT
               Desktop/Laptop: tabel penuh, tidak terpotong
               HP: scroll horizontal mulus dengan indikator shadow
               ================================================================ */

        /* ── 0. Reset box-sizing & overflow global ── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        /* ── 0. Font ── */
        body,
        .card,
        th,
        td,
        label,
        select,
        input,
        .nav-link,
        .badge,
        .btn,
        span,
        a {
            font-family: 'Inter', system-ui, -apple-system, sans-serif !important;
        }

        /* ── 1. Card ── */
        .card.card-danger.card-outline {
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .07), 0 6px 20px rgba(0, 0, 0, .06) !important;
            overflow: visible !important;
        }

        .card.card-danger.card-outline>.card-header {
            background: linear-gradient(135deg, #0a1628 0%, #1e3a5f 55%, #2d5986 100%) !important;
            border-bottom: none !important;
            border-radius: 12px 12px 0 0 !important;
            padding: 13px 20px !important;
        }

        .card.card-danger.card-outline>.card-header .card-title {
            color: #fff !important;
            font-size: .875rem !important;
            font-weight: 700 !important;
            letter-spacing: .02em !important;
        }

        .card.card-danger.card-outline>.card-header .btn-tool,
        .card.card-danger.card-outline>.card-header .btn-tool i {
            color: rgba(255, 255, 255, .7) !important;
        }

        .card.card-danger.card-outline>.card-header .btn-tool:hover {
            color: #fff !important;
        }

        .card-body {
            padding: 20px !important;
        }

        /* ── 2. Project Select ── */
        .form-label {
            font-size: .72rem !important;
            font-weight: 600 !important;
            color: #6b7280 !important;
            text-transform: uppercase !important;
            letter-spacing: .07em !important;
            margin-bottom: 5px !important;
            display: block !important;
        }

        .project-select-row {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
        }

        #projectSelect {
            appearance: none !important;
            -webkit-appearance: none !important;
            border: 1.5px solid #d1d5db !important;
            border-radius: 8px !important;
            padding: 8px 36px 8px 12px !important;
            font-size: .85rem !important;
            font-weight: 500 !important;
            color: #111827 !important;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%231e3a5f' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center !important;
            background-size: 14px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, .05) !important;
            transition: border-color .15s, box-shadow .15s !important;
            min-width: 200px !important;
            flex: 1 1 200px !important;
            max-width: 480px !important;
            cursor: pointer !important;
        }

        #projectSelect:focus {
            border-color: #1e3a5f !important;
            box-shadow: 0 0 0 3px rgba(30, 74, 140, .15) !important;
            outline: none !important;
        }

        /* ── 3. Nav Tabs — scroll horizontal ── */
        .tab-wrapper {
            position: relative !important;
            width: 100% !important;
        }

        /* Fade kanan sebagai hint ada konten */
        .tab-wrapper::after {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            right: 0 !important;
            width: 40px !important;
            height: calc(100% - 2px) !important;
            background: linear-gradient(to right, transparent, #fff) !important;
            pointer-events: none !important;
            z-index: 1 !important;
        }

        .nav-tabs {
            border-bottom: 2px solid #b8cceb !important;
            gap: 0 !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            overflow-y: visible !important;
            -webkit-overflow-scrolling: touch !important;
            padding-bottom: 0 !important;
            scrollbar-width: none !important;
            /* Firefox — sembunyikan scrollbar tab */
        }

        .nav-tabs::-webkit-scrollbar {
            display: none !important;
        }

        .nav-tabs .nav-item {
            flex-shrink: 0 !important;
        }

        .nav-tabs .nav-link {
            background: transparent !important;
            border: none !important;
            border-bottom: 2.5px solid transparent !important;
            border-radius: 0 !important;
            margin-bottom: -2px !important;
            padding: 9px 16px !important;
            font-size: .78rem !important;
            font-weight: 600 !important;
            color: #9ca3af !important;
            white-space: nowrap !important;
            transition: color .15s, border-color .15s !important;
            letter-spacing: .01em !important;
        }

        .nav-tabs .nav-link:hover {
            color: #1e3a5f !important;
            border-bottom-color: #7aaad6 !important;
        }

        .nav-tabs .nav-link.active {
            color: #1e3a5f !important;
            border-bottom-color: #1e3a5f !important;
        }

        /* ── 4. Table wrapper — scroll horizontal dengan shadow hint ── */
        .table-responsive-wrapper {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            border: 1.5px solid #b8cceb !important;
            border-radius: 10px !important;
            /* Shadow kiri-kanan sebagai hint ada konten di luar viewport */
            background:
                linear-gradient(to right, white 30%, rgba(255, 255, 255, 0)) left / 40px 100%,
                linear-gradient(to left, white 30%, rgba(255, 255, 255, 0)) right / 40px 100%,
                radial-gradient(farthest-side at 0 50%, rgba(0, 0, 0, .08), rgba(0, 0, 0, 0)) left / 14px 100%,
                radial-gradient(farthest-side at 100% 50%, rgba(0, 0, 0, .08), rgba(0, 0, 0, 0)) right / 14px 100%,
                white !important;
            background-repeat: no-repeat !important;
            background-attachment: local, local, scroll, scroll, local !important;
            /* scrollbar tipis di HP */
            scrollbar-width: thin !important;
            scrollbar-color: #7aaad6 transparent !important;
        }

        .table-responsive-wrapper::-webkit-scrollbar {
            height: 5px !important;
        }

        .table-responsive-wrapper::-webkit-scrollbar-track {
            background: transparent !important;
        }

        .table-responsive-wrapper::-webkit-scrollbar-thumb {
            background: #7aaad6 !important;
            border-radius: 4px !important;
        }

        /* ── 5. Table ── */
        .komrev-table {
            font-size: .85rem !important;
            border-collapse: collapse !important;
            margin-bottom: 0 !important;
            white-space: nowrap !important;
            width: 100% !important;
            min-width: 900px !important;
            /* memastikan tidak terpotong, scroll aktif jika perlu */
            table-layout: auto !important;
        }

        .komrev-table thead th {
            background: #f0f4fb !important;
            color: #0d2645 !important;
            font-size: .75rem !important;
            font-weight: 700 !important;
            letter-spacing: .05em !important;
            text-transform: uppercase !important;
            padding: 10px 14px !important;
            border: 1px solid #b8cceb !important;
            border-bottom: 2.5px solid #7aaad6 !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 2 !important;
            vertical-align: middle !important;
        }

        .komrev-table tbody td {
            padding: 9px 14px !important;
            border: 1px solid #e2e8f0 !important;
            vertical-align: middle !important;
            color: #111827 !important;
            font-size: .85rem !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #fafbfc !important;
        }

        .table-hover tbody tr:hover {
            background-color: #f0f4fb !important;
        }

        .table-bordered {
            border: 2px solid #7aaad6 !important;
        }

        .table-bordered td,
        .table-bordered th {
            border-color: #b8cceb !important;
        }

        /* Lebar kolom spesifik */
        .col-check {
            width: 36px !important;
        }

        .col-no {
            width: 42px !important;
        }

        .col-komat {
            min-width: 110px !important;
            white-space: normal !important;
        }

        .col-material {
            min-width: 130px !important;
            white-space: normal !important;
        }

        .col-supplier {
            min-width: 110px !important;
            white-space: normal !important;
        }

        .col-req {
            min-width: 80px !important;
        }

        .col-rev {
            min-width: 50px !important;
        }

        .col-disc {
            min-width: 80px !important;
        }

        .col-status {
            min-width: 130px !important;
        }

        .col-posisi {
            min-width: 320px !important;
            white-space: nowrap !important;
        }

        .col-action {
            min-width: 90px !important;
        }

        /* ── 6. Badges ── */
        .badge {
            border-radius: 5px !important;
            font-size: .7rem !important;
            font-weight: 600 !important;
            letter-spacing: .02em !important;
            padding: 4px 7px !important;
            margin-bottom: 3px !important;
            display: block !important;
        }

        .badge.bg-danger {
            background: #1e4d8c !important;
            color: #fff !important;
        }

        .badge.bg-success {
            background: #dcfce7 !important;
            color: #15803d !important;
        }

        .badge.bg-secondary {
            background: #f1f5f9 !important;
            color: #475569 !important;
        }

        .badge.bg-info {
            background: #e8eef7 !important;
            color: #0d2645 !important;
        }

        .badge.bg-warning {
            background: #fef9c3 !important;
            color: #a16207 !important;
        }

        /* ── 7. Flow boxes ── */
        body .box,
        body .boxblue,
        body .boxorange {
            display: inline-flex !important;
            align-items: center !important;
            border-radius: 8px !important;
            padding: 4px 7px !important;
            text-decoration: none !important;
            transition: box-shadow .15s, transform .12s !important;
            font-size: .75rem !important;
        }

        body .box {
            border: 1.5px solid #e2e8f0 !important;
            background: #f8fafc !important;
        }

        body .boxblue {
            border: 1.5px solid #7aaad6 !important;
            background: #f0f4fb !important;
            box-shadow: 0 0 0 2px rgba(122, 170, 214, .25) !important;
        }

        body .boxorange {
            border: 1.5px solid #fcd34d !important;
            background: #fffbeb !important;
            box-shadow: 0 0 0 2px rgba(252, 211, 77, .25) !important;
        }

        body .box:hover,
        body .boxblue:hover,
        body .boxorange:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .1) !important;
        }

        /* ── 8. Indicator dots ── */
        body .indicator {
            width: 12px !important;
            height: 12px !important;
            border-radius: 50% !important;
            flex-shrink: 0 !important;
            margin-right: 4px !important;
            cursor: help !important;
        }

        body .indicator.green {
            background: #22c55e !important;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, .22) !important;
        }

        body .indicator.red {
            background: #dc2626 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .22) !important;
        }

        body .indicator.orange {
            background: #f59e0b !important;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, .22) !important;
        }

        body .indicator.black {
            background: #374151 !important;
            box-shadow: 0 0 0 2px rgba(55, 65, 81, .3) !important;
        }

        /* ── 9. Arrow ── */
        body .arrow {
            color: #9ca3af !important;
            font-size: 14px !important;
            margin: 0 2px !important;
            flex-shrink: 0 !important;
        }

        body .arrow-invis {
            color: transparent !important;
            font-size: 14px !important;
            margin: 0 2px !important;
        }

        /* ── 10. Keterangan ── */
        body .keterangan {
            font-size: .75rem !important;
            font-weight: 500 !important;
            color: #111827 !important;
        }

        /* ── 11. Button ── */
        .btn.btn-primary.btn-sm {
            background: linear-gradient(135deg, #1e3a5f, #1e4d8c) !important;
            border: none !important;
            border-radius: 7px !important;
            font-size: .78rem !important;
            font-weight: 600 !important;
            padding: 5px 12px !important;
            letter-spacing: .02em !important;
            box-shadow: 0 1px 3px rgba(20, 58, 120, .35) !important;
            transition: all .15s !important;
            color: #fff !important;
            white-space: nowrap !important;
        }

        .btn.btn-primary.btn-sm:hover {
            background: linear-gradient(135deg, #0d2645, #163660) !important;
            box-shadow: 0 3px 10px rgba(20, 58, 120, .4) !important;
            transform: translateY(-1px) !important;
        }

        /* ── 12. DataTables ── */
        .dataTables_wrapper {
            padding: 0 !important;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 10px !important;
        }

        .dataTables_wrapper .dataTables_filter label,
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_info {
            font-size: .75rem !important;
            color: #6b7280 !important;
            font-weight: 500 !important;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #d1d5db !important;
            border-radius: 7px !important;
            padding: 5px 10px !important;
            font-size: .78rem !important;
            margin-left: 6px !important;
            outline: none !important;
            background: #fff !important;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #1e3a5f !important;
            box-shadow: 0 0 0 3px rgba(30, 74, 140, .1) !important;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid #d1d5db !important;
            border-radius: 7px !important;
            padding: 4px 8px !important;
            font-size: .78rem !important;
            margin: 0 4px !important;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 10px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #e5e7eb !important;
            border-radius: 6px !important;
            font-size: .72rem !important;
            font-weight: 500 !important;
            padding: 4px 9px !important;
            color: #374151 !important;
            background: #fff !important;
            margin: 0 2px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f0f4fb !important;
            border-color: #7aaad6 !important;
            color: #1e3a5f !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #1e4d8c !important;
            border-color: #1e4d8c !important;
            color: #fff !important;
            font-weight: 700 !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            opacity: .4 !important;
            cursor: default !important;
        }

        /* DT controls row */
        .dt-top-row {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            margin-bottom: 10px !important;
        }

        .dt-top-row .dataTables_filter {
            margin-bottom: 0 !important;
            order: 2 !important;
        }

        .dt-top-row .dataTables_length {
            margin-bottom: 0 !important;
            order: 1 !important;
        }

        .dt-upload-slot {
            order: 3 !important;
            display: flex !important;
            align-items: center !important;
        }

        /* ── 13. Breadcrumb ── */
        .breadcrumb {
            border-radius: 8px !important;
            font-size: .78rem !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06) !important;
            padding: 6px 12px !important;
        }

        .breadcrumb-item a {
            color: #1e3a5f !important;
            font-weight: 500 !important;
            text-decoration: none !important;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline !important;
        }

        /* ── 14. Posisi cell — flow layout ── */
        .posisi-cell {
            padding: 8px 10px !important;
            vertical-align: middle !important;
        }

        /* All tab — horizontal flow */
        .flow-h {
            display: flex !important;
            align-items: center !important;
            flex-wrap: nowrap !important;
            gap: 2px !important;
        }

        .flow-box {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        /* Unit tab */
        .posisi-unit {
            white-space: normal !important;
        }

        .flow-unit {
            display: flex !important;
            align-items: flex-start !important;
            gap: 4px !important;
            flex-wrap: nowrap !important;
        }

        .unit-arrow {
            margin-top: 10px !important;
        }

        /* Disc group */
        .disc-group {
            display: flex !important;
            align-items: flex-start !important;
            gap: 4px !important;
        }

        .disc-subbox {
            display: flex !important;
            flex-direction: column !important;
            padding: 5px 7px !important;
            border-radius: 8px !important;
            min-width: 54px !important;
        }

        .disc-label {
            font-size: .62rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: .1em !important;
            color: #0d2645 !important;
            margin-bottom: 4px !important;
            padding-bottom: 3px !important;
            border-bottom: 1px solid rgba(0, 0, 0, .08) !important;
        }

        .disc-list {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .disc-list li {
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding: 2px 0 !important;
        }

        /* Vertical stack */
        .vert-stack {
            display: flex !important;
            flex-direction: column !important;
            padding: 5px 9px !important;
            border-radius: 8px !important;
            min-width: 95px !important;
            gap: 0 !important;
        }

        .vert-row {
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
            padding: 2px 0 !important;
        }

        .vert-sep {
            text-align: center !important;
            color: #9ca3af !important;
            font-size: 11px !important;
            line-height: 1 !important;
            padding: 1px 0 !important;
            margin-left: 2px !important;
        }

        /* ── 15. Tooltip ── */
        #komrev-tooltip {
            position: fixed !important;
            z-index: 99999 !important;
            pointer-events: none !important;
            opacity: 0 !important;
            transition: opacity .15s ease !important;
            max-width: 260px !important;
            background: #1e293b !important;
            color: #f1f5f9 !important;
            font-family: 'Inter', system-ui, sans-serif !important;
            font-size: .73rem !important;
            font-weight: 400 !important;
            line-height: 1.65 !important;
            padding: 9px 13px !important;
            border-radius: 9px !important;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .25) !important;
            white-space: pre-line !important;
            word-break: break-word !important;
        }

        #komrev-tooltip.tip-green {
            border-top: 3px solid #22c55e !important;
        }

        #komrev-tooltip.tip-orange {
            border-top: 3px solid #f59e0b !important;
        }

        #komrev-tooltip.tip-red {
            border-top: 3px solid #dc2626 !important;
        }

        #komrev-tooltip.tip-grey {
            border-top: 3px solid #94a3b8 !important;
        }

        #komrev-tooltip.tip-blue {
            border-top: 3px solid #1e4d8c !important;
        }

        #komrev-tooltip::after {
            content: '' !important;
            position: absolute !important;
            top: 100% !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            border: 6px solid transparent !important;
            border-top-color: #1e293b !important;
        }

        .indicator {
            cursor: help !important;
        }

        .box,
        .boxblue,
        .boxorange {
            cursor: pointer !important;
        }

        td,
        th {
            overflow: visible !important;
        }

        /* ── 16. Responsive breakpoints ── */

        /* Tablet landscape dan layar menengah */
        @media (max-width: 1200px) {
            .col-posisi {
                min-width: 280px !important;
            }

            .komrev-table {
                min-width: 860px !important;
            }
        }

        /* Tablet portrait */
        @media (max-width: 992px) {
            .card-body {
                padding: 16px !important;
            }

            .komrev-table {
                font-size: .8rem !important;
            }

            .komrev-table thead th {
                font-size: .7rem !important;
                padding: 8px 10px !important;
            }

            .komrev-table tbody td {
                padding: 8px 10px !important;
                font-size: .8rem !important;
            }

            .col-posisi {
                min-width: 260px !important;
            }
        }

        /* HP / mobile */
        @media (max-width: 767px) {
            .card-body {
                padding: 12px !important;
            }

            .project-select-row {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            #projectSelect {
                min-width: 100% !important;
                max-width: 100% !important;
                flex: none !important;
            }

            .btn.btn-primary.btn-sm {
                font-size: .72rem !important;
                padding: 5px 10px !important;
            }

            .nav-tabs .nav-link {
                padding: 8px 12px !important;
                font-size: .72rem !important;
            }

            .komrev-table {
                font-size: .75rem !important;
                min-width: 700px !important;
            }

            .komrev-table thead th {
                font-size: .65rem !important;
                padding: 7px 9px !important;
            }

            .komrev-table tbody td {
                padding: 6px 9px !important;
                font-size: .75rem !important;
            }

            .col-posisi {
                min-width: 240px !important;
            }

            .badge {
                font-size: .62rem !important;
                padding: 3px 6px !important;
            }

            .disc-group {
                gap: 3px !important;
            }

            .disc-subbox {
                min-width: 48px !important;
                padding: 4px 5px !important;
            }

            /* Hint scroll HP — label kecil */
            .table-responsive-wrapper::after {
                content: '← geser →' !important;
                display: none !important;
                /* hidden, shadow sudah cukup sebagai hint */
            }
        }

        /* HP kecil */
        @media (max-width: 480px) {
            .komrev-table {
                min-width: 640px !important;
            }

            .col-posisi {
                min-width: 200px !important;
            }

            .vert-stack {
                min-width: 80px !important;
            }

            body .keterangan {
                font-size: .68rem !important;
            }

            body .indicator {
                width: 10px !important;
                height: 10px !important;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        /* ── Tooltip Engine ── */
        (function initTooltipEngine() {
            const dotColorMap = {
                green: { cls: 'tip-green', icon: '🟢', label: 'Hijau', meaning: 'Selesai / Approved' },
                orange: { cls: 'tip-orange', icon: '🟠', label: 'Oranye', meaning: 'Sedang Berlangsung' },
                red: { cls: 'tip-red', icon: '🔴', label: 'Merah', meaning: 'Belum Ada' },
                black: { cls: 'tip-grey', icon: '⚪', label: 'Abu-abu', meaning: 'Tidak Terlibat' },
            };
            const boxColorMap = {
                boxblue: { cls: 'tip-blue', icon: '🔵', label: 'Biru', meaning: 'Tahap Selesai' },
                boxorange: { cls: 'tip-orange', icon: '🟠', label: 'Kuning', meaning: 'Tahap Sedang Berjalan' },
                box: { cls: 'tip-grey', icon: '⬜', label: 'Abu-abu', meaning: 'Belum Ada Proses' },
            };

            const $tip = $('<div id="komrev-tooltip"></div>').appendTo('body');
            let hideTimer;

            function showTip(e, text, colorCls) {
                clearTimeout(hideTimer);
                $tip.text(text)
                    .removeClass('tip-green tip-orange tip-red tip-grey tip-blue')
                    .addClass(colorCls || 'tip-grey')
                    .css('opacity', 0).show();
                positionTip(e);
                $tip.css('opacity', 1);
            }

            function positionTip(e) {
                const W = $(window).width(), tw = $tip.outerWidth(), th = $tip.outerHeight();
                let x = e.clientX - tw / 2;
                let y = e.clientY - th - 14;
                if (x < 6) x = 6;
                if (x + tw > W - 6) x = W - tw - 6;
                if (y < 6) y = e.clientY + 20;
                $tip.css({ left: x, top: y });
            }

            function hideTip() { hideTimer = setTimeout(() => $tip.css('opacity', 0), 80); }

            function setupTooltips() {
                $('div.indicator').each(function () {
                    const $el = $(this);
                    const msg = $el.attr('title') || $el.attr('data-orig-title');
                    if (!msg || !msg.trim()) return;
                    $el.attr('data-orig-title', msg).removeAttr('title');
                    let colorInfo = null;
                    $.each(dotColorMap, (cls, info) => { if ($el.hasClass(cls)) { colorInfo = info; return false; } });
                    const richText = colorInfo
                        ? `${colorInfo.icon} ${colorInfo.label} — ${colorInfo.meaning}\n${msg.trim()}`
                        : msg.trim();
                    const tipCls = colorInfo ? colorInfo.cls : 'tip-grey';
                    $el.off('mouseenter.tip mousemove.tip mouseleave.tip')
                        .on('mouseenter.tip', e => showTip(e, richText, tipCls))
                        .on('mousemove.tip', e => positionTip(e))
                        .on('mouseleave.tip', hideTip);
                });

                $('a.box, a.boxblue, a.boxorange').each(function () {
                    const $el = $(this);
                    const msg = $el.attr('title') || $el.attr('data-orig-title');
                    if (!msg || !msg.trim()) return;
                    $el.attr('data-orig-title', msg).removeAttr('title');
                    let boxInfo = null;
                    $.each(boxColorMap, (cls, info) => { if ($el.hasClass(cls)) { boxInfo = info; return false; } });
                    const richText = boxInfo
                        ? `${boxInfo.icon} ${boxInfo.label} — ${boxInfo.meaning}\n${msg.trim()}`
                        : msg.trim();
                    const tipCls = boxInfo ? boxInfo.cls : 'tip-grey';
                    $el.off('mouseenter.tip mousemove.tip mouseleave.tip')
                        .on('mouseenter.tip', e => showTip(e, richText, tipCls))
                        .on('mousemove.tip', e => positionTip(e))
                        .on('mouseleave.tip', hideTip);
                });
            }

            window.setupKomrevTooltips = setupTooltips;
        })();

        /* ── DataTable ── */
        function initDT(selector) {
            $(selector).each(function () {
                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        paging: true, lengthChange: true, searching: true,
                        ordering: true, info: true, autoWidth: false,
                        responsive: false, destroy: true,
                        language: {
                            search: '', searchPlaceholder: 'Cari...',
                            lengthMenu: 'Tampilkan _MENU_ baris',
                            info: '_START_–_END_ dari _TOTAL_ data',
                            infoEmpty: '0 data',
                            paginate: { previous: '‹', next: '›' }
                        }
                    });

                    /* Inject Upload button sejajar dengan search */
                    const $wrapper = $(this).closest('.dataTables_wrapper');
                    const $filter = $wrapper.find('.dataTables_filter');
                    const $length = $wrapper.find('.dataTables_length');
                    const $uploadBtn = $('#btn-upload-komrev');

                    if ($uploadBtn.length && $filter.length && !$wrapper.find('.dt-top-row').length) {
                        $length.add($filter).wrapAll('<div class="dt-top-row"></div>');
                        $wrapper.find('.dt-top-row').append('<div class="dt-upload-slot"></div>');
                        $wrapper.find('.dt-upload-slot').append($uploadBtn.clone(true).removeClass('d-none'));
                    }
                }
            });
        }

        $(document).ready(function () {
            const firstProjectId = $('#projectSelect').val();
            if (firstProjectId) initDT(`#project-table-${firstProjectId} .komrev-table`);
            setTimeout(window.setupKomrevTooltips, 100);
        });

        function filterByProject(projectId) {
            $('.project-table').hide();
            $(`#project-table-${projectId}`).show();
            initDT(`#project-table-${projectId} .komrev-table`);
            setTimeout(window.setupKomrevTooltips, 100);
        }

        function toggleAllCheckboxes(source, tableId) {
            $(`#table-${tableId} input[type="checkbox"]:not(.selectAll)`).prop('checked', source.checked);
        }

        $(document).on('shown.bs.tab', function () {
            setTimeout(window.setupKomrevTooltips, 80);
        });
    </script>
@endpush