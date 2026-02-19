@extends('layouts.split3')

@section('container2')
    <div class="row">

        {{-- Form untuk Informasi Dokumen --}}
        <div class="col-md-3 col-sm-6 col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h1 class="card-title h5 mb-0">Informasi Dokumen</h1>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool text-white" data-card-widget="remove" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body space-y-3">

                    {{-- 📝 Bagian Informasi Umum --}}
                    <h5 class="mb-3"><strong>📌 Informasi Umum</strong></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="card-text mb-1"><strong>No. Dokumen:</strong><br>
                                {{ $document->no_dokumen ?? 'Belum diterbitkan' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="card-text mb-1"><strong>Tipe Proyek:</strong><br>
                                {{ $document->projectType->title }}
                            </p>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <p class="card-text mb-1"><strong>Distributor Dokumen:</strong><br>
                                {{ $document->unit_distributor_id ? $document->unitDistributor->name : 'Belum menentukan distributor dokumen' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="card-text mb-1"><strong>Supplier:</strong><br>
                                <i class="fas fa-industry text-primary"></i> {{ $document->supplier->name }}
                            </p>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <p class="card-text mb-1"><strong>Tanggal Terbit:</strong><br>
                                {{ $document->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="card-text mb-1"><strong>Catatan:</strong><br>
                                {{ $document->note ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- 🟡 Status Dokumen --}}
                    <div class="mt-3">
                        <strong>Status Dokumen:</strong><br>
                        <span class="badge {{ $document->status === 'Terbuka' ? 'bg-danger' : 'bg-success' }}"
                            id="statusBadge">
                            {{ $document->status }}
                        </span>

                        @if (!is_null($document->rejectedreason))
                            <div class="mt-2">
                                <strong>Alasan Tertolak:</strong><br>
                                <span class="badge bg-danger">
                                    {{ $document->rejectedreason }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- 🧾 Identitas Dokumen --}}
                    <h5 class="mt-4"><strong>🆔 Identitas Dokumen</strong></h5>
                    <table class="table table-bordered table-sm mb-0">
                        <tr>
                            <th style="width: 180px;">Nama Komat</th>
                            <td>{{ $document->komatProcess->komat_name }}</td>
                        </tr>
                        <tr>
                            <th>Revisi</th>
                            <td>{{ $document->revision }}</td>
                        </tr>
                        <tr>
                            <th>No. Diskusi</th>
                            <td>{{ $document->discussion_number }}</td>
                        </tr>
                    </table>

                    {{-- 📂 File dengan Kolom TTD --}}
                    <h5 class="mt-4"><strong>📝 File dengan Kolom TTD</strong></h5>
                    @foreach ($document->komatHistReqs as $komatHistReq)
                        <div class="mt-3">
                            <h6 class="fw-bold">
                                • {{ $komatHistReq->komatRequirement->name }}
                            </h6>
                            @foreach ($document->feedbacks->where('komat_requirement_id', $komatHistReq->komatRequirement->id) as $userinformation)
                                @if ($userinformation->files && $userinformation->komatPosition->level === 'logistik_upload')
                                    @foreach ($userinformation->files as $file)
                                        <div class="mt-2">
                                            @include('newmemo.memo.fileinfo', [
                                                'file' => $file,
                                                'userinformation' => $userinformation,
                                            ])
                                        </div>
                                    @endforeach
                                @endif
                            @endforeach
                        </div>
                    @endforeach

                    {{-- 🔧 Aksi --}}
                    <div class="mt-4 d-flex gap-2">
                        @if (
                            ($yourauth->rule === $document->unitDistributor->name || $yourauth->rule === 'MTPR') &&
                                $document->status === 'Terbuka')
                            <a href="{{ route('komatprocesshistory.edit', $document->id) }}"
                                class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit Dokumen
                            </a>
                        @endif

                        <a class="btn btn-primary btn-sm"
                            href="{{ route('document.report', ['id' => $document->id, 'rule' => $yourauth->rule]) }}">
                            <i class="fas fa-folder-open"></i> Progress
                        </a>
                    </div>

                </div>

            </div>
        </div>





        {{-- Form untuk menambahkan feedback diskusi --}}
        @foreach ($document->komatHistReqs as $komatHistReq)
            <div class="col-md-3 col-sm-6 col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h1 class="card-title h5 mb-0">
                            Komat Requirement: {{ $komatHistReq->komatRequirement->name }}
                            @php
                                $allApproved = $komatHistReq->komatPositions
                                    ->where('level', 'discussion')
                                    ->every(fn($position) => $position->status_process === 'done');
                            @endphp
                            <i class="fas {{ $allApproved ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                title="{{ $allApproved ? 'All Units Approved' : 'Not All Units Approved' }}"></i>
                        </h1>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"
                                title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool text-white" data-card-widget="remove" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="unitTabs-{{ $komatHistReq->id }}" role="tablist">
                            @foreach ($units as $unit)
                                @php
                                    $isChecked = $komatHistReq->komatPositions
                                        ->where('unit_id', $unit->id)
                                        ->where('level', 'discussion')
                                        ->isNotEmpty();
                                @endphp
                                @if ($isChecked)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            id="tab-{{ $komatHistReq->id }}-{{ $unit->id }}" data-bs-toggle="tab"
                                            data-bs-target="#content-{{ $komatHistReq->id }}-{{ $unit->id }}"
                                            type="button" role="tab"
                                            aria-controls="content-{{ $komatHistReq->id }}-{{ $unit->id }}"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            {{ $unit->name }}
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="tab-content mt-3" id="unitTabContent-{{ $komatHistReq->id }}">
                            @foreach ($units as $unit)
                                @php
                                    $isChecked = $komatHistReq->komatPositions
                                        ->where('unit_id', $unit->id)
                                        ->where('level', 'discussion')
                                        ->isNotEmpty();
                                    $feedbacks = $isChecked
                                        ? $komatHistReq->komatPositions
                                            ->where('unit_id', $unit->id)
                                            ->where('level', 'discussion')
                                            ->first()->feedbacks
                                        : collect([]);
                                    $komatposition = $isChecked
                                        ? $komatHistReq->komatPositions
                                            ->where('unit_id', $unit->id)
                                            ->where('level', 'discussion')
                                            ->first()
                                        : null;
                                @endphp
                                @if ($isChecked)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="content-{{ $komatHistReq->id }}-{{ $unit->id }}" role="tabpanel"
                                        aria-labelledby="tab-{{ $komatHistReq->id }}-{{ $unit->id }}">
                                        <h5 class="mb-3">Diskusi Status:
                                            {{ $komatposition ? $komatposition->status : 'N/A' }}
                                            <i class="fas {{ $komatposition && $komatposition->status != 'draft' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                                title="{{ $komatposition && $komatposition->status != 'draft' ? 'Unit Approved' : 'Unit Not Approved' }}"></i>
                                        </h5>
                                        @foreach ($feedbacks as $feedback)
                                            <div
                                                class="discussion-item d-flex align-items-start mb-4 p-3 bg-light rounded hover-shadow">
                                                <div class="avatar mr-3">
                                                    <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1, 5) }}.png"
                                                        alt="User Avatar" class="rounded-circle" width="50">
                                                </div>
                                                <div class="discussion-content flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <p class="mb-1 font-weight-bold">{{ $feedback->user_name }}
                                                            </p>
                                                            <p class="mb-1 text-muted">{{ $feedback->user_rule }}</p>
                                                        </div>
                                                        <span
                                                            class="badge {{ $feedback->status === 'last_accepted' ? 'bg-success' : ($feedback->status === 'draft' ? 'bg-warning' : 'bg-primary') }} text-white">
                                                            {{ ucfirst($feedback->status) }}
                                                        </span>
                                                    </div>
                                                    <p class="mb-2 text-muted small">
                                                        {{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                    <p class="mb-2">{{ $feedback->comment }}</p>
                                                    <p class="mb-2"><strong>Status:</strong>
                                                        {{ $feedback->feedback_status }}</p>
                                                    @if ($feedback->files->isNotEmpty())
                                                        <p class="mb-2"><strong>Files:</strong></p>
                                                        <ul class="list-unstyled">
                                                            @foreach ($feedback->files as $file)
                                                                <li class="mb-2">
                                                                    @include(
                                                                        'komatprocesshistory.show.fileinfo',
                                                                        [
                                                                            'file' => $file,
                                                                            'userinformation' => $feedback,
                                                                        ]
                                                                    )
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                    <div class="mt-2">
                                                        @if (
                                                            $feedback->status === 'draft' &&
                                                                $document->status === 'Terbuka' &&
                                                                $komatposition &&
                                                                $komatposition->status === 'draft' &&
                                                                strpos($yourauth->rule, 'Manager') !== false)
                                                            <form
                                                                action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $unit->id, 'feedbackId' => $feedback->id, 'level' => 'discussion']) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit"
                                                                    class="btn btn-warning btn-sm">Reviewed</button>
                                                            </form>
                                                        @elseif (
                                                            $feedback->status === 'reviewed' &&
                                                                strpos($yourauth->rule, 'Manager') !== false &&
                                                                $document->status === 'Terbuka' &&
                                                                $komatposition &&
                                                                $komatposition->status === 'draft')
                                                            <form
                                                                action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $unit->id, 'feedbackId' => $feedback->id, 'level' => 'discussion']) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit"
                                                                    class="btn btn-success btn-sm">Selesai</button>
                                                            </form>
                                                        @endif
                                                        @if ($document->status === 'Terbuka' && $document->unit_distributor_id === $yourauth->unit_id)
                                                            <form
                                                                action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $unit->id, 'feedbackId' => $feedback->id, 'level' => 'discussion']) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')">Delete</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if (
                                            $document->status === 'Terbuka' &&
                                                $komatposition &&
                                                $komatposition->status === 'draft' &&
                                                $yourauth->unit_id === $unit->id)
                                            <form
                                                action="{{ route('komatprocesshistory.addComment', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $unit->id]) }}"
                                                method="POST" enctype="multipart/form-data" class="mt-4">
                                                @csrf
                                                @if (config('app.url') === 'https://inka.goovicess.com')
                                                    <div class="form-group">
                                                        <label for="filecount">Jumlah File (Sementara File Kosong):</label>
                                                        <input type="number" id="filecount" name="filecount"
                                                            class="form-control" min="0" max="100"
                                                            step="1" value="0">
                                                    </div>
                                                @else
                                                    <div class="form-group">
                                                        <label for="files">Choose File:</label>
                                                        <input type="file" id="files" name="file[]"
                                                            class="form-control-file" multiple>
                                                    </div>
                                                @endif
                                                <div class="form-group">
                                                    <label for="feedback_status">Status Feedback</label>
                                                    <select name="feedback_status" id="feedback_status"
                                                        class="form-control" required>
                                                        <option value="approved">Approved</option>
                                                        <option value="notapproved">Not Approved</option>
                                                        <option value="withremarks">Approved with Remark</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="comment">Tambah Komentar</label>
                                                    <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">Kirim
                                                    Komentar</button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Form untuk menambahkan feedback resume --}}
        @if (
            $document->komatHistReqs->every(function ($komatHistReq) {
                $discussions = $komatHistReq->komatPositions->where('level', 'discussion');
                return $discussions->isNotEmpty() && $discussions->every(fn($pos) => $pos->status_process === 'done');
            }))
            <div class="col-md-3 col-sm-6 col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h1 class="card-title h5 mb-0">
                            Resume Feedback
                            @php
                                $allResumeApproved = $komatHistReq->komatPositions
                                    ->where('level', 'resume')
                                    ->every(fn($position) => $position->status_process === 'done');
                                $permissiontosendSM =
                                    $komatHistReq->komatPositions->where('level', 'resume')->isNotEmpty() &&
                                    $komatHistReq->komatPositions
                                        ->where('level', 'resume')
                                        ->every(
                                            fn($position) => $position->feedbacks->contains('status', 'last_accepted'),
                                        );
                                $issmlevelexist = $komatHistReq->komatPositions
                                    ->where('level', 'sm_level')
                                    ->isNotEmpty();
                            @endphp
                            <i class="fas {{ $allResumeApproved ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                title="{{ $allResumeApproved ? 'All Units Approved' : 'Not All Units Approved' }}"></i>
                            @if ($permissiontosendSM && !$issmlevelexist)
                                <form
                                    action="{{ route('komatprocesshistory.copyTo', ['id' => $document->id, 'level' => 'sm_level']) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @if ($document->unit_distributor_id == 2)
                                        <input type="hidden" name="sendto" value="Senior Manager Engineering">
                                    @elseif (
                                        $document->unit_distributor_id == 5 ||
                                            $document->unit_distributor_id == 6 ||
                                            $document->unit_distributor_id == 7 ||
                                            $document->unit_distributor_id == 8)
                                        <input type="hidden" name="sendto" value="Senior Manager Desain">
                                    @else
                                        <input type="hidden" name="sendto" value="Senior Manager Teknologi Produksi">
                                    @endif
                                    <button type="submit" class="btn btn-danger btn-sm ml-2"
                                        onclick="return confirm('Apakah Anda yakin ingin mengirim resume ini ke SM?')">Kirim
                                        ke SM</button>
                                </form>
                            @elseif ($allResumeApproved && $issmlevelexist)
                                <i class="fas fa-envelope text-success" title="Tercopy ke SM"></i>
                            @endif
                        </h1>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"
                                title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool text-white" data-card-widget="remove"
                                title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="resumeFeedbackTabs" role="tablist">
                            @foreach ($document->komatHistReqs as $komatHistReq)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        id="resume-tab-{{ $komatHistReq->id }}" data-bs-toggle="tab"
                                        data-bs-target="#resume-content-{{ $komatHistReq->id }}" type="button"
                                        role="tab" aria-controls="resume-content-{{ $komatHistReq->id }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ $komatHistReq->komatRequirement->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content mt-3" id="resumeFeedbackTabContent">
                            @foreach ($document->komatHistReqs as $komatHistReq)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="resume-content-{{ $komatHistReq->id }}" role="tabpanel"
                                    aria-labelledby="resume-tab-{{ $komatHistReq->id }}">
                                    @php
                                        $resumeFeedback = $komatHistReq->komatPositions
                                            ->where('level', 'resume')
                                            ->first();
                                        $feedbacks = $resumeFeedback ? $resumeFeedback->feedbacks : collect([]);
                                        $isthereonefeedbackandstatusislastaccepted =
                                            $feedbacks->isNotEmpty() && $feedbacks->contains('status', 'last_accepted');
                                    @endphp
                                    <h5 class="mb-3">Resume Status {{ $komatHistReq->komatRequirement->name }}:
                                        <i class="fas {{ $isthereonefeedbackandstatusislastaccepted ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                            title="{{ $isthereonefeedbackandstatusislastaccepted ? 'Unit Approved' : 'Unit Not Approved' }}"></i>
                                    </h5>
                                    <h5 class="mb-3 text-danger">Peringatan: Setiap jenis dokumen wajib memiliki hanya satu
                                        feedback.</h5>
                                    <div class="card-body">
                                        @foreach ($feedbacks as $feedback)
                                            <div
                                                class="discussion-item d-flex align-items-start mb-4 p-3 bg-light rounded hover-shadow">
                                                <div class="avatar mr-3">
                                                    <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1, 5) }}.png"
                                                        alt="User Avatar" class="rounded-circle" width="50">
                                                </div>
                                                <div class="discussion-content flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <p class="mb-1 font-weight-bold">{{ $feedback->user_name }}
                                                            </p>
                                                            <p class="mb-1 text-muted">{{ $feedback->user_rule }}</p>
                                                        </div>
                                                        <span
                                                            class="badge {{ $feedback->status === 'last_accepted' ? 'bg-success' : ($feedback->status === 'draft' ? 'bg-warning' : 'bg-primary') }} text-white">
                                                            {{ ucfirst($feedback->status) }}
                                                        </span>
                                                    </div>
                                                    <p class="mb-2 text-muted small">
                                                        {{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                    <p class="mb-2">{{ $feedback->comment }}</p>
                                                    <p class="mb-2"><strong>Status:</strong>
                                                        {{ $feedback->feedback_status }}</p>
                                                    @if ($feedback->files->isNotEmpty())
                                                        <p class="mb-2"><strong>Files:</strong></p>
                                                        <ul class="list-unstyled">
                                                            @foreach ($feedback->files as $file)
                                                                <li class="mb-2">
                                                                    @include(
                                                                        'komatprocesshistory.show.fileinfo',
                                                                        [
                                                                            'file' => $file,
                                                                            'userinformation' => $feedback,
                                                                        ]
                                                                    )
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                    <div class="mt-2">
                                                        @if (
                                                            $feedback->status === 'draft' &&
                                                                $document->status === 'Terbuka' &&
                                                                $resumeFeedback &&
                                                                $resumeFeedback->status === 'draft' &&
                                                                strpos($yourauth->rule, 'Manager') !== false)
                                                            <form
                                                                action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'resume']) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit"
                                                                    class="btn btn-warning btn-sm">Reviewed</button>
                                                            </form>
                                                        @elseif (
                                                            $feedback->status === 'reviewed' &&
                                                                strpos($yourauth->rule, 'Manager') !== false &&
                                                                $document->status === 'Terbuka' &&
                                                                $resumeFeedback &&
                                                                $resumeFeedback->status === 'draft')
                                                            <form
                                                                action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'resume']) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit"
                                                                    class="btn btn-success btn-sm">Selesai</button>
                                                            </form>
                                                        @endif
                                                        @if ($document->status === 'Terbuka' && $document->unit_distributor_id === $yourauth->unit_id)
                                                            <form
                                                                action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'resume']) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm"
                                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')">Delete</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if ($yourauth->unit_id == $document->unit_distributor_id && !$isthereonefeedbackandstatusislastaccepted)
                                            <form
                                                action="{{ route('komatprocesshistory.addResumeFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id]) }}"
                                                method="POST" enctype="multipart/form-data" class="mt-4">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="files">Choose File:</label>
                                                    <input type="file" id="files" name="file[]"
                                                        class="form-control-file" multiple>
                                                </div>
                                                <div class="form-group">
                                                    <label for="feedback_status">Status Feedback</label>
                                                    <select name="feedback_status" id="feedback_status"
                                                        class="form-control" required>
                                                        <option value="approved">Approved</option>
                                                        <option value="notapproved">Not Approved</option>
                                                        <option value="withremarks">Approved with Remark</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="comment">Tambah Komentar</label>
                                                    <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">Kirim
                                                    Komentar</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form untuk verifikasi sm_level --}}
        @if ($document->komatHistReqs->contains(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'sm_level')->isNotEmpty()))
            <div class="col-md-3 col-sm-6 col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <h1 class="card-title h5 mb-0">
                            Validasi SM Level
                            @php
                                $allSMLevelApproved = $document->komatHistReqs->every(
                                    fn($komatHistReq) => $komatHistReq->komatPositions
                                        ->where('level', 'sm_level')
                                        ->every(fn($position) => $position->status_process === 'done'),
                                );
                                $isLogistikDoneExist = $document->komatHistReqs->contains(
                                    fn($komatHistReq) => $komatHistReq->komatPositions
                                        ->where('level', 'mtpr_review')
                                        ->isNotEmpty(),
                                );
                            @endphp
                            <i class="fas {{ $allSMLevelApproved ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                title="{{ $allSMLevelApproved ? 'All SM Level Positions Approved' : 'Not All SM Level Positions Approved' }}"></i>
                            @if ($allSMLevelApproved && !$isLogistikDoneExist)
                                @if ($document->status == 'Terbuka')
                                    <button type="button" class="btn bg-teal btn-sm reject-sm-btn"
                                        data-form-id="reject-sm-form" style="width: 100px;">
                                        <i class="fas fa-lock"></i> Tolak dan Balik Logistik
                                    </button>
                                    <form id="reject-sm-form"
                                        action="{{ route('komatprocesshistory.close', ['id' => $document->id]) }}"
                                        method="POST" style="display:none;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Tertutup">
                                        <input type="hidden" name="documentstatus" value="rejectedbysm">
                                        <input type="hidden" name="needincreaserevision" value="yes">
                                        <input type="hidden" name="rejectedreason" id="rejectedreason-sm">
                                    </form>
                                    <form
                                        action="{{ route('komatprocesshistory.copyTo', ['id' => $document->id, 'level' => 'mtpr_review']) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="sendto" value="MTPR">
                                        <button type="submit" class="btn btn-danger btn-sm ml-2"
                                            onclick="return confirm('Apakah Anda yakin ingin mengirim ke MTPR?')">Kirim
                                            ke MTPR</button>
                                    </form>
                                @endif
                            @elseif ($allSMLevelApproved && $isLogistikDoneExist)
                                <i class="fas fa-envelope text-success" title="Tercopy ke MTPR"></i>
                            @endif
                        </h1>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"
                                title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool text-white" data-card-widget="remove"
                                title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="smLevelFeedbackTabs" role="tablist">
                            @foreach ($document->komatHistReqs as $komatHistReq)
                                @php
                                    $hasSmLevelPosition = $komatHistReq->komatPositions
                                        ->where('level', 'sm_level')
                                        ->isNotEmpty();
                                @endphp
                                @if ($hasSmLevelPosition)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            id="sm-tab-{{ $komatHistReq->id }}" data-bs-toggle="tab"
                                            data-bs-target="#sm-content-{{ $komatHistReq->id }}" type="button"
                                            role="tab" aria-controls="sm-content-{{ $komatHistReq->id }}"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            {{ $komatHistReq->komatRequirement->name }}
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="tab-content mt-3" id="smLevelFeedbackTabContent">
                            @foreach ($document->komatHistReqs as $komatHistReq)
                                @php
                                    $smLevelPosition = $komatHistReq->komatPositions
                                        ->where('level', 'sm_level')
                                        ->first();
                                    $feedbacks = $smLevelPosition ? $smLevelPosition->feedbacks : collect([]);
                                    $isLastAccepted = $feedbacks->contains('status', 'last_accepted');
                                @endphp
                                @if ($smLevelPosition)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="sm-content-{{ $komatHistReq->id }}" role="tabpanel"
                                        aria-labelledby="sm-tab-{{ $komatHistReq->id }}">
                                        <h5 class="mb-3">SM Level Status {{ $komatHistReq->komatRequirement->name }}:
                                            <i class="fas {{ $isLastAccepted ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                                title="{{ $isLastAccepted ? 'SM Level Approved' : 'SM Level Not Approved' }}"></i>
                                        </h5>
                                        <div class="card-body">
                                            @foreach ($feedbacks as $feedback)
                                                <div
                                                    class="discussion-item d-flex align-items-start mb-4 p-3 bg-light rounded hover-shadow">
                                                    <div class="avatar mr-3">
                                                        <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1, 5) }}.png"
                                                            alt="User Avatar" class="rounded-circle" width="50">
                                                    </div>
                                                    <div class="discussion-content flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <p class="mb-1 font-weight-bold">
                                                                    {{ $feedback->user_name }}</p>
                                                                <p class="mb-1 text-muted">{{ $feedback->user_rule }}</p>
                                                            </div>
                                                            <span
                                                                class="badge {{ $feedback->status === 'last_accepted' ? 'bg-success' : ($feedback->status === 'draft' ? 'bg-warning' : 'bg-primary') }} text-white">
                                                                {{ ucfirst($feedback->status) }}
                                                            </span>
                                                        </div>
                                                        <p class="mb-2 text-muted small">
                                                            {{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                        <p class="mb-2">{{ $feedback->comment }}</p>
                                                        <p class="mb-2"><strong>Status:</strong>
                                                            {{ $feedback->feedback_status }}</p>
                                                        @if ($feedback->files->isNotEmpty())
                                                            <p class="mb-2"><strong>Files:</strong></p>
                                                            <ul class="list-unstyled">
                                                                @foreach ($feedback->files as $file)
                                                                    <li class="mb-2">
                                                                        @include(
                                                                            'komatprocesshistory.show.fileinfo',
                                                                            [
                                                                                'file' => $file,
                                                                                'userinformation' => $feedback,
                                                                            ]
                                                                        )
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                        <div class="mt-2">
                                                            @if (
                                                                $feedback->status === 'draft' &&
                                                                    $document->status === 'Terbuka' &&
                                                                    $smLevelPosition &&
                                                                    $smLevelPosition->status === 'draft' &&
                                                                    strpos($yourauth->rule, 'Manager') !== false)
                                                                <form
                                                                    action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'sm_level']) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="btn btn-warning btn-sm">Promote to
                                                                        Reviewed</button>
                                                                </form>
                                                            @elseif (
                                                                $feedback->status === 'reviewed' &&
                                                                    $document->status === 'Terbuka' &&
                                                                    $smLevelPosition &&
                                                                    $smLevelPosition->status === 'draft' &&
                                                                    strpos($yourauth->rule, 'Manager') !== false)
                                                                <form
                                                                    action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'sm_level']) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="btn btn-success btn-sm">Selesai</button>
                                                                </form>
                                                            @endif
                                                            @if ($document->status === 'Terbuka' && $document->unit_distributor_id === $yourauth->unit_id)
                                                                <form
                                                                    action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'sm_level']) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')">Delete</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form untuk verifikasi mtpr_review --}}
        @if ($document->komatHistReqs->contains(fn($histReq) => $histReq->komatPositions->where('level', 'mtpr_review')->isNotEmpty()))
            <div class="col-md-3 col-sm-6 col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-maroon text-white d-flex justify-content-between align-items-center">
                        <h1 class="card-title h5 mb-0">
                            Validasi MTPR
                            @php
                                $allMtprApproved = $document->komatHistReqs->every(
                                    fn($histReq) => $histReq->komatPositions
                                        ->where('level', 'mtpr_review')
                                        ->every(fn($pos) => $pos->status_process === 'done'),
                                );
                                $isLogistikDoneExist = $document->komatHistReqs->contains(
                                    fn($histReq) => $histReq->komatPositions
                                        ->where('level', 'logistik_done')
                                        ->isNotEmpty(),
                                );
                            @endphp
                            <i class="fas {{ $allMtprApproved ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                title="{{ $allMtprApproved ? 'Semua MTPR Approved' : 'Belum Semua MTPR Approved' }}"></i>
                            @if ($allMtprApproved && !$isLogistikDoneExist)
                                <form
                                    action="{{ route('komatprocesshistory.copyTo', ['id' => $document->id, 'level' => 'logistik_done']) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="sendto" value="Logistik">
                                    <button type="submit" class="btn btn-danger btn-sm ml-2"
                                        onclick="return confirm('Apakah Anda yakin ingin mengirim ke Logistik Done?')">Kirim
                                        ke Logistik Done</button>
                                </form>
                            @elseif ($allMtprApproved && $isLogistikDoneExist)
                                <i class="fas fa-envelope text-success" title="Tercopy ke Logistik Done"></i>
                            @endif
                        </h1>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"
                                title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool text-white" data-card-widget="remove"
                                title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="mtprReviewTabs" role="tablist">
                            @foreach ($document->komatHistReqs as $histReq)
                                @php
                                    $hasMtprReview = $histReq->komatPositions
                                        ->where('level', 'mtpr_review')
                                        ->isNotEmpty();
                                @endphp
                                @if ($hasMtprReview)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            id="mtpr-tab-{{ $histReq->id }}" data-bs-toggle="tab"
                                            data-bs-target="#mtpr-content-{{ $histReq->id }}" type="button"
                                            role="tab" aria-controls="mtpr-content-{{ $histReq->id }}"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            {{ $histReq->komatRequirement->name }}
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="tab-content mt-3" id="mtprReviewTabContent">
                            @foreach ($document->komatHistReqs as $histReq)
                                @php
                                    $mtprReviewPos = $histReq->komatPositions->where('level', 'mtpr_review')->first();
                                    $mtprFeedbacks = $mtprReviewPos ? $mtprReviewPos->feedbacks : collect([]);
                                    $isMtprAccepted = $mtprFeedbacks->contains('status', 'last_accepted');
                                @endphp
                                @if ($mtprReviewPos)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="mtpr-content-{{ $histReq->id }}" role="tabpanel"
                                        aria-labelledby="mtpr-tab-{{ $histReq->id }}">
                                        <h5 class="mb-3">MTPR Status {{ $histReq->komatRequirement->name }}:
                                            <i class="fas {{ $isMtprAccepted ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                                title="{{ $isMtprAccepted ? 'MTPR Approved' : 'MTPR Not Approved' }}"></i>
                                        </h5>
                                        <div class="card-body">
                                            @foreach ($mtprFeedbacks as $mtprFeedback)
                                                <div
                                                    class="discussion-item d-flex align-items-start mb-4 p-3 bg-light rounded hover-shadow">
                                                    <div class="avatar mr-3">
                                                        <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1, 5) }}.png"
                                                            alt="User Avatar" class="rounded-circle" width="50">
                                                    </div>
                                                    <div class="discussion-content flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <p class="mb-1 font-weight-bold">
                                                                    {{ $mtprFeedback->user_name }}</p>
                                                                <p class="mb-1 text-muted">{{ $mtprFeedback->user_rule }}
                                                                </p>
                                                            </div>
                                                            <span
                                                                class="badge {{ $mtprFeedback->status === 'last_accepted' ? 'bg-success' : ($mtprFeedback->status === 'draft' ? 'bg-warning' : 'bg-primary') }} text-white">
                                                                {{ ucfirst($mtprFeedback->status) }}
                                                            </span>
                                                        </div>
                                                        <p class="mb-2 text-muted small">
                                                            {{ $mtprFeedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                        <p class="mb-2">{{ $mtprFeedback->comment }}</p>
                                                        <p class="mb-2"><strong>Status:</strong>
                                                            {{ $mtprFeedback->feedback_status }}</p>
                                                        @if ($mtprFeedback->files->isNotEmpty())
                                                            <p class="mb-2"><strong>Files:</strong></p>
                                                            <ul class="list-unstyled">
                                                                @foreach ($mtprFeedback->files as $mtprFile)
                                                                    <li class="mb-2">
                                                                        @include(
                                                                            'komatprocesshistory.show.fileinfo',
                                                                            [
                                                                                'file' => $mtprFile,
                                                                                'userinformation' => $mtprFeedback,
                                                                            ]
                                                                        )
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                        <div class="mt-2">
                                                            @if (
                                                                $mtprFeedback->status === 'draft' &&
                                                                    $document->status === 'Terbuka' &&
                                                                    $mtprReviewPos &&
                                                                    $mtprReviewPos->status === 'draft' &&
                                                                    strpos($yourauth->rule, 'MTPR') !== false)
                                                                <form
                                                                    action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $histReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $mtprFeedback->id, 'level' => 'mtpr_review']) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="btn btn-warning btn-sm">Promote to
                                                                        Reviewed</button>
                                                                </form>
                                                            @elseif (
                                                                $mtprFeedback->status === 'reviewed' &&
                                                                    $document->status === 'Terbuka' &&
                                                                    $mtprReviewPos &&
                                                                    $mtprReviewPos->status === 'draft' &&
                                                                    strpos($yourauth->rule, 'MTPR') !== false)
                                                                <form
                                                                    action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $histReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $mtprFeedback->id, 'level' => 'mtpr_review']) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="btn btn-success btn-sm">Selesai</button>
                                                                </form>
                                                            @endif
                                                            @if ($document->status === 'Terbuka' && $document->unit_distributor_id === $yourauth->unit_id)
                                                                <form
                                                                    action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $histReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $mtprFeedback->id, 'level' => 'mtpr_review']) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')">Delete</button>
                                                                </form>
                                                            @endif
                                                        </div>

                                                    </div>
                                                </div>
                                            @endforeach
                                            @if ($yourauth->rule == 'MTPR')
                                                <form
                                                    action="{{ route('komatprocesshistory.addMTPRFeedback', ['id' => $document->id, 'komatHistReqId' => $histReq->id, 'unitId' => $yourauth->unit_id]) }}"
                                                    method="POST" enctype="multipart/form-data" class="mt-4">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="files">Choose File:</label>
                                                        <input type="file" id="files" name="file[]"
                                                            class="form-control-file" multiple>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="feedback_status">Status Feedback</label>
                                                        <select name="feedback_status" id="feedback_status"
                                                            class="form-control" required>
                                                            <option value="approved">Approved</option>
                                                            <option value="notapproved">Not Approved</option>
                                                            <option value="withremarks">Approved with Remark
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="comment">Tambah Komentar</label>
                                                        <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-sm">Kirim
                                                        Komentar</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form untuk verifikasi logistik_done --}}
        @if ($document->komatHistReqs->contains(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'logistik_done')->isNotEmpty()))
            <div class="col-md-3 col-sm-6 col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-teal text-white d-flex justify-content-between align-items-center">
                        <h1 class="card-title h5 mb-0">
                            Validasi Logistik
                            @php
                                $allLogistikDoneApproved = $document->komatHistReqs->every(
                                    fn($komatHistReq) => $komatHistReq->komatPositions
                                        ->where('level', 'logistik_done')
                                        ->every(fn($position) => $position->status_process === 'done'),
                                );
                            @endphp
                            <i class="fas {{ $allLogistikDoneApproved ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                title="{{ $allLogistikDoneApproved ? 'All Logistik Done Positions Approved' : 'Not All Logistik Done Positions Approved' }}"></i>
                        </h1>
                        @if ($document->status === 'Terbuka' && $allLogistikDoneApproved)
                            @if ($document->logisticauthoritylevel === 'verifiednotneeded')
                                <form action="{{ route('komatprocesshistory.close', ['id' => $document->id]) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Tertutup">
                                    <input type="hidden" name="documentstatus" value="approved">
                                    <input type="hidden" name="needincreaserevision" value="no">
                                    <button type="submit" class="btn btn-warning btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menutup dokumen ini?')"
                                        style="width: 100px;">
                                        <i class="fas fa-lock"></i> Tutup
                                    </button>
                                </form>
                            @else
                                @php
                                    $issendtoManagerLogistik = $document->komatHistReqs->every(
                                        fn($komatHistReq) => $komatHistReq->komatPositions
                                            ->where('level', 'managerlogistikneeded')
                                            ->isNotEmpty(),
                                    );
                                @endphp
                                @if (!$issendtoManagerLogistik)
                                    <button type="button" class="btn bg-purple btn-sm reject-logistik-btn"
                                        data-form-id="reject-logistik-form">
                                        Balik ke Teknologi
                                    </button>
                                    <form id="reject-logistik-form"
                                        action="{{ route('komatprocesshistory.close', ['id' => $document->id]) }}"
                                        method="POST" style="display:none;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Tertutup">
                                        <input type="hidden" name="documentstatus" value="rejectedbylogistik">
                                        <input type="hidden" name="needincreaserevision" value="yes">
                                        <input type="hidden" name="rejectedreason" id="rejectedreason-logistik">
                                    </form>


                                    <form
                                        action="{{ route('komatprocesshistory.copyTo', ['id' => $document->id, 'level' => 'managerlogistikneeded']) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="sendto" value="Logistik">
                                        <button type="submit" class="btn btn-danger btn-sm ml-2"
                                            onclick="return confirm('Apakah Anda yakin ingin mengirim ke Manager Logistik?')">
                                            Kirim ke Manager Logistik</button>
                                    </form>
                                @endif
                            @endif
                        @elseif ($document->status === 'Tertutup')
                            <i class="fas fa-envelope text-success" title="Dokumen Telah Tertutup"></i>
                        @endif
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"
                                title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool text-white" data-card-widget="remove"
                                title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="logistikDoneFeedbackTabs" role="tablist">
                            @foreach ($document->komatHistReqs as $komatHistReq)
                                @php
                                    $hasLogistikDonePosition = $komatHistReq->komatPositions
                                        ->where('level', 'logistik_done')
                                        ->isNotEmpty();
                                @endphp
                                @if ($hasLogistikDonePosition)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                            id="logistik-done-tab-{{ $komatHistReq->id }}" data-bs-toggle="tab"
                                            data-bs-target="#logistik-done-content-{{ $komatHistReq->id }}"
                                            type="button" role="tab"
                                            aria-controls="logistik-done-content-{{ $komatHistReq->id }}"
                                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                            {{ $komatHistReq->komatRequirement->name }}
                                        </button>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        <div class="tab-content mt-3" id="logistikDoneFeedbackTabContent">
                            @foreach ($document->komatHistReqs as $komatHistReq)
                                @php
                                    $logistikDonePosition = $komatHistReq->komatPositions
                                        ->where('level', 'logistik_done')
                                        ->first();
                                    $feedbacks = $logistikDonePosition ? $logistikDonePosition->feedbacks : collect([]);
                                    $isLastAccepted = $feedbacks->contains('status', 'last_accepted');
                                @endphp
                                @if ($logistikDonePosition)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="logistik-done-content-{{ $komatHistReq->id }}" role="tabpanel"
                                        aria-labelledby="logistik-done-tab-{{ $komatHistReq->id }}">
                                        <h5 class="mb-3">Logistik Status
                                            {{ $komatHistReq->komatRequirement->name }}:
                                            <i class="fas {{ $isLastAccepted ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                                title="{{ $isLastAccepted ? 'Logistik Done Approved' : 'Logistik Done Not Approved' }}"></i>
                                        </h5>
                                        <div class="card-body">
                                            @foreach ($feedbacks as $feedback)
                                                <div
                                                    class="discussion-item d-flex align-items-start mb-4 p-3 bg-light rounded hover-shadow">
                                                    <div class="avatar mr-3">
                                                        <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1, 5) }}.png"
                                                            alt="User Avatar" class="rounded-circle" width="50">
                                                    </div>
                                                    <div class="discussion-content flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <p class="mb-1 font-weight-bold">
                                                                    {{ $feedback->user_name }}</p>
                                                                <p class="mb-1 text-muted">{{ $feedback->user_rule }}</p>
                                                            </div>
                                                            <span
                                                                class="badge {{ $feedback->status === 'last_accepted' ? 'bg-success' : ($feedback->status === 'draft' ? 'bg-warning' : 'bg-primary') }} text-white">
                                                                {{ ucfirst($feedback->status) }}
                                                            </span>
                                                        </div>
                                                        <p class="mb-2 text-muted small">
                                                            {{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                        <p class="mb-2">{{ $feedback->comment }}</p>
                                                        <p class="mb-2"><strong>Status:</strong>
                                                            {{ $feedback->feedback_status }}</p>
                                                        @if ($feedback->files->isNotEmpty())
                                                            <p class="mb-2"><strong>Files:</strong></p>
                                                            <ul class="list-unstyled">
                                                                @foreach ($feedback->files as $file)
                                                                    <li class="mb-2">
                                                                        @include(
                                                                            'komatprocesshistory.show.fileinfo',
                                                                            [
                                                                                'file' => $file,
                                                                                'userinformation' => $feedback,
                                                                            ]
                                                                        )
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                        <div class="mt-2">
                                                            @if (
                                                                $feedback->status === 'draft' &&
                                                                    $document->status === 'Terbuka' &&
                                                                    $logistikDonePosition &&
                                                                    $logistikDonePosition->status === 'draft' &&
                                                                    $yourauth->rule === 'Logistik')
                                                                <form
                                                                    action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'logistik_done']) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="btn btn-warning btn-sm">Promote to
                                                                        Reviewed</button>
                                                                </form>
                                                            @elseif (
                                                                $feedback->status === 'reviewed' &&
                                                                    $yourauth->rule === 'Logistik' &&
                                                                    $document->status === 'Terbuka' &&
                                                                    $logistikDonePosition &&
                                                                    $logistikDonePosition->status === 'draft')
                                                                <form
                                                                    action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'logistik_done']) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit"
                                                                        class="btn btn-success btn-sm">Selesai</button>
                                                                </form>
                                                            @endif
                                                            @if (
                                                                $document->status === 'Terbuka' &&
                                                                    isset($document->unit_distributor_id) &&
                                                                    isset($yourauth->unit_id) &&
                                                                    $document->unit_distributor_id === $yourauth->unit_id)
                                                                <form
                                                                    action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'logistik_done']) }}"
                                                                    method="POST" style="display:inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')">Delete</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif


        @if ($document->logisticauthoritylevel == 'managerneeded' || $document->logisticauthoritylevel == 'seniormanagerneeded')
            {{-- Form untuk verifikasi managerneeded --}}
            @if ($document->komatHistReqs->contains(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'managerlogistikneeded')->isNotEmpty()))
                <div class="col-md-3 col-sm-6 col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-orange text-white d-flex justify-content-between align-items-center">
                            <h1 class="card-title h5 mb-0">
                                Validasi Manager Logistik
                                @php
                                    $allManagerLogistikApproved = $document->komatHistReqs->every(
                                        fn($komatHistReq) => $komatHistReq->komatPositions
                                            ->where('level', 'managerlogistikneeded')
                                            ->every(fn($position) => $position->status_process === 'done'),
                                    );
                                @endphp
                                <i class="fas {{ $allManagerLogistikApproved ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                    title="{{ $allManagerLogistikApproved ? 'All Manager Logistik Needed Positions Approved' : 'Not All Manager Logistik Needed Positions Approved' }}"></i>
                            </h1>
                            @if ($document->status === 'Terbuka' && $allManagerLogistikApproved)
                                @if ($document->logisticauthoritylevel === 'managerneeded')
                                    <form action="{{ route('komatprocesshistory.close', ['id' => $document->id]) }}"
                                        method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Tertutup">
                                        <input type="hidden" name="rejectedreason" value="">
                                        <input type="hidden" name="documentstatus" value="approved">
                                        <input type="hidden" name="needincreaserevision" value="no">
                                        <button type="submit" class="btn btn-warning btn-sm"
                                            onclick="return confirm('Apakah Anda yakin ingin menutup dokumen ini?')"
                                            style="width: 100px;">
                                            <i class="fas fa-lock"></i> Tutup
                                        </button>
                                    </form>
                                @else
                                    @php
                                        $issendtoSeniorManagerLogistik = $document->komatHistReqs->every(
                                            fn($komatHistReq) => $komatHistReq->komatPositions
                                                ->where('level', 'seniormanagerlogistikneeded')
                                                ->isNotEmpty(),
                                        );
                                    @endphp
                                    @if (!$issendtoSeniorManagerLogistik)
                                        <form
                                            action="{{ route('komatprocesshistory.copyTo', ['id' => $document->id, 'level' => 'seniormanagerlogistikneeded']) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="sendto" value="Senior Manager Logistik">
                                            <button type="submit" class="btn btn-danger btn-sm ml-2"
                                                onclick="return confirm('Apakah Anda yakin ingin mengirim ke Senior Manager Logistik?')">Kirim
                                                ke Senior Manager Logistik</button>
                                        </form>
                                    @endif
                                @endif
                            @elseif ($document->status === 'Tertutup')
                                <i class="fas fa-envelope text-success" title="Dokumen Telah Tertutup"></i>
                            @endif
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"
                                    title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool text-white" data-card-widget="remove"
                                    title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="managerLogistikFeedbackTabs" role="tablist">
                                @foreach ($document->komatHistReqs as $komatHistReq)
                                    @php
                                        $hasManagerLogistikPosition = $komatHistReq->komatPositions
                                            ->where('level', 'managerlogistikneeded')
                                            ->isNotEmpty();
                                    @endphp
                                    @if ($hasManagerLogistikPosition)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                id="manager-logistik-tab-{{ $komatHistReq->id }}" data-bs-toggle="tab"
                                                data-bs-target="#manager-logistik-content-{{ $komatHistReq->id }}"
                                                type="button" role="tab"
                                                aria-controls="manager-logistik-content-{{ $komatHistReq->id }}"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                {{ $komatHistReq->komatRequirement->name }}
                                            </button>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                            <div class="tab-content mt-3" id="managerLogistikFeedbackTabContent">
                                @foreach ($document->komatHistReqs as $komatHistReq)
                                    @php
                                        $managerLogistikPosition = $komatHistReq->komatPositions
                                            ->where('level', 'managerlogistikneeded')
                                            ->first();
                                        $managerFeedbacks = $managerLogistikPosition
                                            ? $managerLogistikPosition->feedbacks
                                            : collect([]);
                                        $isManagerLastAccepted = $managerFeedbacks->contains('status', 'last_accepted');
                                    @endphp
                                    @if ($managerLogistikPosition)
                                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                            id="manager-logistik-content-{{ $komatHistReq->id }}" role="tabpanel"
                                            aria-labelledby="manager-logistik-tab-{{ $komatHistReq->id }}">
                                            <h5 class="mb-3">Logistik Status
                                                {{ $komatHistReq->komatRequirement->name }}:
                                                <i class="fas {{ $isManagerLastAccepted ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                                    title="{{ $isManagerLastAccepted ? 'Logistik Done Approved' : 'Logistik Done Not Approved' }}"></i>
                                            </h5>
                                            <div class="card-body">
                                                @foreach ($managerFeedbacks as $feedback)
                                                    <div
                                                        class="discussion-item d-flex align-items-start mb-4 p-3 bg-light rounded hover-shadow">
                                                        <div class="avatar mr-3">
                                                            <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1, 5) }}.png"
                                                                alt="User Avatar" class="rounded-circle" width="50">
                                                        </div>
                                                        <div class="discussion-content flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <p class="mb-1 font-weight-bold">
                                                                        {{ $feedback->user_name }}</p>
                                                                    <p class="mb-1 text-muted">{{ $feedback->user_rule }}
                                                                    </p>
                                                                </div>
                                                                <span
                                                                    class="badge {{ $feedback->status === 'last_accepted' ? 'bg-success' : ($feedback->status === 'draft' ? 'bg-warning' : 'bg-primary') }} text-white">
                                                                    {{ ucfirst($feedback->status) }}
                                                                </span>
                                                            </div>
                                                            <p class="mb-2 text-muted small">
                                                                {{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                            <p class="mb-2">{{ $feedback->comment }}</p>
                                                            <p class="mb-2"><strong>Status:</strong>
                                                                {{ $feedback->feedback_status }}</p>
                                                            @if ($feedback->files->isNotEmpty())
                                                                <p class="mb-2"><strong>Files:</strong></p>
                                                                <ul class="list-unstyled">
                                                                    @foreach ($feedback->files as $file)
                                                                        <li class="mb-2">
                                                                            @include(
                                                                                'komatprocesshistory.show.fileinfo',
                                                                                [
                                                                                    'file' => $file,
                                                                                    'userinformation' => $feedback,
                                                                                ]
                                                                            )
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                            <div class="mt-2">
                                                                @if (
                                                                    $feedback->status === 'draft' &&
                                                                        $document->status === 'Terbuka' &&
                                                                        $managerLogistikPosition &&
                                                                        $managerLogistikPosition->status === 'draft' &&
                                                                        $yourauth->rule === 'Manager Logistik')
                                                                    <form
                                                                        action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'managerlogistikneeded']) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <button type="submit"
                                                                            class="btn btn-warning btn-sm">Promote to
                                                                            Reviewed</button>
                                                                    </form>
                                                                @elseif (
                                                                    $feedback->status === 'reviewed' &&
                                                                        $yourauth->rule === 'Manager Logistik' &&
                                                                        $document->status === 'Terbuka' &&
                                                                        $managerLogistikPosition &&
                                                                        $managerLogistikPosition->status === 'draft')
                                                                    <form
                                                                        action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'managerlogistikneeded']) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-sm">Selesai</button>
                                                                    </form>
                                                                @endif
                                                                @if (
                                                                    $document->status === 'Terbuka' &&
                                                                        isset($document->unit_distributor_id) &&
                                                                        isset($yourauth->unit_id) &&
                                                                        $document->unit_distributor_id === $yourauth->unit_id)
                                                                    <form
                                                                        action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'logistik_done']) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-danger btn-sm"
                                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')">Delete</button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        @if ($document->logisticauthoritylevel == 'seniormanagerneeded')
            {{-- Form untuk verifikasi seniormanagerneeded --}}
            @if ($document->komatHistReqs->contains(fn($komatHistReq) => $komatHistReq->komatPositions->where('level', 'seniormanagerlogistikneeded')->isNotEmpty()))
                <div class="col-md-3 col-sm-6 col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <h1 class="card-title h5 mb-0">
                                Validasi Senior Manager Logistik
                                @php
                                    $allSeniorLogistikApproved = $document->komatHistReqs->every(
                                        fn($komatHistReq) => $komatHistReq->komatPositions
                                            ->where('level', 'seniormanagerlogistikneeded')
                                            ->every(fn($position) => $position->status_process === 'done'),
                                    );
                                @endphp
                                <i class="fas {{ $allSeniorLogistikApproved ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                    title="{{ $allSeniorLogistikApproved ? 'All Logistik Done Positions Approved' : 'Not All Logistik Done Positions Approved' }}"></i>
                            </h1>
                            @if ($document->status === 'Terbuka' && $allSeniorLogistikApproved)
                                <form action="{{ route('komatprocesshistory.close', ['id' => $document->id]) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="Tertutup">
                                    <input type="hidden" name="documentstatus" value="approved">
                                    <input type="hidden" name="needincreaserevision" value="no">
                                    <button type="submit" class="btn btn-warning btn-sm"
                                        onclick="return confirm('Apakah Anda yakin ingin menutup dokumen ini?')"
                                        style="width: 100px;">
                                        <i class="fas fa-lock"></i> Tutup
                                    </button>
                                </form>
                            @elseif ($document->status === 'Tertutup')
                                <i class="fas fa-envelope text-success" title="Dokumen Telah Tertutup"></i>
                            @endif
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse"
                                    title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool text-white" data-card-widget="remove"
                                    title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="seniorLogistikFeedbackTabs" role="tablist">
                                @foreach ($document->komatHistReqs as $komatHistReq)
                                    @php
                                        $hasSeniorLogistikPosition = $komatHistReq->komatPositions
                                            ->where('level', 'seniormanagerlogistikneeded')
                                            ->isNotEmpty();
                                    @endphp
                                    @if ($hasSeniorLogistikPosition)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                id="senior-logistik-tab-{{ $komatHistReq->id }}" data-bs-toggle="tab"
                                                data-bs-target="#senior-logistik-content-{{ $komatHistReq->id }}"
                                                type="button" role="tab"
                                                aria-controls="senior-logistik-content-{{ $komatHistReq->id }}"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                                {{ $komatHistReq->komatRequirement->name }}
                                            </button>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                            <div class="tab-content mt-3" id="seniorLogistikFeedbackTabContent">
                                @foreach ($document->komatHistReqs as $komatHistReq)
                                    @php
                                        $seniorLogistikPosition = $komatHistReq->komatPositions
                                            ->where('level', 'seniormanagerlogistikneeded')
                                            ->first();
                                        $seniorFeedbacks = $seniorLogistikPosition
                                            ? $seniorLogistikPosition->feedbacks
                                            : collect([]);
                                        $isSeniorLastAccepted = $seniorFeedbacks->contains('status', 'last_accepted');
                                    @endphp
                                    @if ($seniorLogistikPosition)
                                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                            id="senior-logistik-content-{{ $komatHistReq->id }}" role="tabpanel"
                                            aria-labelledby="senior-logistik-tab-{{ $komatHistReq->id }}">
                                            <h5 class="mb-3">Logistik Status
                                                {{ $komatHistReq->komatRequirement->name }}:
                                                <i class="fas {{ $isSeniorLastAccepted ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} ml-2"
                                                    title="{{ $isSeniorLastAccepted ? 'Logistik Done Approved' : 'Logistik Done Not Approved' }}"></i>
                                            </h5>
                                            <div class="card-body">
                                                @foreach ($seniorFeedbacks as $feedback)
                                                    <div
                                                        class="discussion-item d-flex align-items-start mb-4 p-3 bg-light rounded hover-shadow">
                                                        <div class="avatar mr-3">
                                                            <img src="https://bootdey.com/img/Content/avatar/avatar{{ rand(1, 5) }}.png"
                                                                alt="User Avatar" class="rounded-circle" width="50">
                                                        </div>
                                                        <div class="discussion-content flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <p class="mb-1 font-weight-bold">
                                                                        {{ $feedback->user_name }}</p>
                                                                    <p class="mb-1 text-muted">{{ $feedback->user_rule }}
                                                                    </p>
                                                                </div>
                                                                <span
                                                                    class="badge {{ $feedback->status === 'last_accepted' ? 'bg-success' : ($feedback->status === 'draft' ? 'bg-warning' : 'bg-primary') }} text-white">
                                                                    {{ ucfirst($feedback->status) }}
                                                                </span>
                                                            </div>
                                                            <p class="mb-2 text-muted small">
                                                                {{ $feedback->created_at->format('d-m-Y H:i:s') }}</p>
                                                            <p class="mb-2">{{ $feedback->comment }}</p>
                                                            <p class="mb-2"><strong>Status:</strong>
                                                                {{ $feedback->feedback_status }}</p>
                                                            @if ($feedback->files->isNotEmpty())
                                                                <p class="mb-2"><strong>Files:</strong></p>
                                                                <ul class="list-unstyled">
                                                                    @foreach ($feedback->files as $file)
                                                                        <li class="mb-2">
                                                                            @include(
                                                                                'komatprocesshistory.show.fileinfo',
                                                                                [
                                                                                    'file' => $file,
                                                                                    'userinformation' => $feedback,
                                                                                ]
                                                                            )
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                            <div class="mt-2">
                                                                @if (
                                                                    $feedback->status === 'draft' &&
                                                                        $document->status === 'Terbuka' &&
                                                                        $seniorLogistikPosition &&
                                                                        $seniorLogistikPosition->status === 'draft' &&
                                                                        $yourauth->rule === 'Senior Manager Logistik')
                                                                    <form
                                                                        action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'seniormanagerlogistikneeded']) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <button type="submit"
                                                                            class="btn btn-warning btn-sm">Promote to
                                                                            Reviewed</button>
                                                                    </form>
                                                                @elseif (
                                                                    $feedback->status === 'reviewed' &&
                                                                        $yourauth->rule === 'Senior Manager Logistik' &&
                                                                        $document->status === 'Terbuka' &&
                                                                        $seniorLogistikPosition &&
                                                                        $seniorLogistikPosition->status === 'draft')
                                                                    <form
                                                                        action="{{ route('komatprocesshistory.promoteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $yourauth->unit_id, 'feedbackId' => $feedback->id, 'level' => 'seniormanagerlogistikneeded']) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-sm">Selesai</button>
                                                                    </form>
                                                                @endif
                                                                @if (
                                                                    $document->status === 'Terbuka' &&
                                                                        isset($document->unit_distributor_id) &&
                                                                        isset($yourauth->unit_id) &&
                                                                        $document->unit_distributor_id === $yourauth->unit_id)
                                                                    <form
                                                                        action="{{ route('komatprocesshistory.deleteFeedback', ['id' => $document->id, 'komatHistReqId' => $komatHistReq->id, 'unitId' => $document->unit_distributor_id, 'feedbackId' => $feedback->id, 'level' => 'logistik_done']) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-danger btn-sm"
                                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')">Delete</button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
@endsection

@section('container3')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('komatprocesshistory.index') }}"
                                class="text-decoration-none">List KomRev</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><a
                                href="{{ route('komatprocesshistory.show', [$document->id]) }}"
                                class="text-decoration-none">KOMREV/{{ $document->komatProcess->komat_name }}/{{ $document->revision }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Show
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .discussion-item {
            transition: background-color 0.2s;
        }

        .discussion-item:hover {
            background-color: #f8f9fa !important;
        }

        .hover-shadow {
            transition: box-shadow 0.2s;
        }

        .hover-shadow:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.5em 1em;
        }
    </style>
@endpush

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle rejection buttons
            document.querySelectorAll('.reject-sm-btn, .reject-logistik-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const formId = this.getAttribute('data-form-id');
                    const form = document.getElementById(formId);
                    const inputId = formId === 'reject-sm-form' ? 'rejectedreason-sm' :
                        'rejectedreason-logistik';
                    const title = formId === 'reject-sm-form' ? 'Tolak dan Balik Logistik' :
                        'Balik ke Teknologi';

                    Swal.fire({
                        title: title,
                        text: 'Masukkan alasan penolakan:',
                        input: 'textarea',
                        inputPlaceholder: 'Ketik alasan penolakan di sini...',
                        inputAttributes: {
                            'aria-label': 'Alasan penolakan'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Kirim',
                        cancelButtonText: 'Batal',
                        preConfirm: (reason) => {
                            if (!reason) {
                                Swal.showValidationMessage(
                                    'Alasan penolakan wajib diisi');
                            }
                            return reason;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(inputId).value = result.value;
                            form.submit();
                        }
                    });
                });
            });
        });

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Teks berhasil disalin ke clipboard',
                    timer: 1500,
                    showConfirmButton: false
                });
            }, function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal menyalin teks',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }
    </script>
@endpush
