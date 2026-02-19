@php
    $counterdokumen = 1;
@endphp

@foreach ($documents as $document)
    @php
        $key = key($documents);
        next($documents);
        $projectpics = json_decode($document->project_pic, true);
        $unitpicvalidation = $document->unitpicvalidation;

        // DEFINISIKAN $deadline DI AWAL
        $timeline = collect($document->timelines)->where('infostatus', 'documentopened')->first();
        if ($timeline) {
            $deadline = \Carbon\Carbon::parse($timeline->entertime)->addDays(5);
        } else {
            $deadline = null;
        }
    @endphp

    <tr>
        <!-- Nomor Urut -->
        <td class="text-center fit-col">
            <span class="badge badge-light">{{ $counterdokumen++ }}</span>
        </td>

        <td class="text-center align-middle fit-col">
            @php
                $timelines = collect($document->timelines);
                $badgeClass = '';
                $statusMessage = '';
                $isLate = false;

                if ($deadline) {
                    if ($document->documentstatus == 'Terbuka') {
                        $now = \Carbon\Carbon::now();
                        $differenceInDays = $now->diffInDays($deadline, false);

                        if ($differenceInDays < 0) {
                            $badgeClass = 'badge-danger badge-late';
                            $statusMessage = 'Telat ' . abs($differenceInDays) . ' hari';
                            $isLate = true;
                        } else {
                            $badgeClass = 'badge-success';
                            $statusMessage = 'Sisa ' . abs($differenceInDays) . ' hari';
                        }
                    } elseif ($document->documentstatus == 'Tertutup') {
                        $documentclosed = $timelines->firstWhere('infostatus', 'documentclosed');

                        if ($documentclosed) {
                            $closed = \Carbon\Carbon::parse($documentclosed->createdat);
                            $differenceInDays = $closed->diffInDays($deadline, false);

                            if ($differenceInDays < 0) {
                                $badgeClass = 'badge-danger badge-late';
                                $statusMessage = 'Telat ' . abs($differenceInDays) . ' hari';
                                $isLate = true;
                            } else {
                                $badgeClass = 'badge-success';
                                $statusMessage = 'Tepat Waktu';
                            }
                        } else {
                            $badgeClass = 'badge-warning';
                            $statusMessage = 'Tidak Normal';
                        }
                    }
                } else {
                    $badgeClass = 'badge-secondary';
                    $statusMessage = 'Tidak ada deadline';
                }
            @endphp

            <div style="display: flex; flex-direction: column; gap: 6px; align-items: center;">

                {{-- Deadline Date --}}
                @if ($deadline)
                    <span style="font-size: 15px; font-weight: 600; color: {{ $isLate ? '#dc3545' : '#495057' }};">
                        {{ $deadline->format('d/m/Y') }}
                    </span>
                @endif

                {{-- Status Badge --}}
                <span class="badge {{ $badgeClass }}"
                    style="padding: 8px 12px; font-size: 16px; white-space: nowrap; width: 100%; text-align: center; display: flex; align-items: center; justify-content: center;">
                    @if ($isLate)
                        <i class="fas fa-exclamation-triangle mr-2 blink-icon"></i>
                    @endif
                    {{ $statusMessage }}
                </span>

                {{-- Estimation --}}
                <span class="badge bg-info" style="padding: 5px 10px; font-size: 13px;">
                    Estimasi: 5 hari
                </span>
            </div>
        </td>

        <!-- Nomor Dokumen -->
        <td class="text-center fit-col">
            <div class="d-flex align-items-center"
                style="line-height: 1.4; font-size: 20px; font-weight: bold; color: #212529;">
                <i class="fas fa-file-alt text-primary mr-2"></i>
                <span class="font-weight-bold">{{ $document->documentnumber }}</span>
            </div>
        </td>

        <!-- Nama Dokumen -->
        <td class="auto-col"">
            <div style="
            line-height: 1.4; font-size: 30px;color: #212529;">
                {{ $document->documentname }}
            </div>
        </td>

        <!-- Progress -->
        <td class="text-center fit-col">
            <div class="progress-wrapper">
                <div class="progress" style="height: 12px;">
                    <div class="progress-bar bg-{{ $document->positionPercentage == 100 ? 'success' : 'warning' }}"
                        role="progressbar" style="width: {{ $document->positionPercentage }}%"
                        aria-valuenow="{{ $document->positionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>

                <span class="badge badge-{{ $document->positionPercentage == 100 ? 'success' : 'warning' }}">
                    {{ $document->positionPercentage }}% Selesai
                </span>
            </div>
        </td>

        <!-- POSISI MEMO (WORKFLOW VISUALIZATION) -->
        <td class="text-center workflow-col">
            <div class="container-box" style="width: fit-content;">

                @php
                    $classbox1 = $document->posisi1 == 'on' || $document->MTPRvalidation == 'Aktif' ? 'boxblue' : 'box';
                    $classbox2 = $document->posisi2 == 'on' || $document->MTPRvalidation == 'Aktif' ? 'boxblue' : 'box';
                    $classbox3 = $document->posisi3 == 'on' || $document->MTPRvalidation == 'Aktif' ? 'boxblue' : 'box';
                @endphp

                <a class="{{ $classbox1 }}" href="#" style="text-decoration: none;">

                    {{-- BAGIAN LOGISTIK --}}
                    @if ($document->is_expand_to_logistic == true)
                        {{-- Gunakan workflow-row agar sejajar kiri --}}
                        <div class="workflow-row">
                            <div class="indicator 
                                                                                                                                                                                                {{ $document->Logistiksend == 'Aktif' ? 'green' : 'red' }}"
                                title="{{ $document->Logistiksend == 'Aktif' ? 'Dokumen sudah dikirim' : 'Dokumen belum dikirim' }}">

                                <span class="keterangan">Logistik</span>
                            </div>

                            {{-- Arrow dibuat center terpisah --}}
                            <div class="d-flex justify-content-center w-100">
                                <span class="arrow">↓</span>
                            </div>
                    @endif
                    @if ($document->withMTPR == 'Yes')
                        <div class="workflow-row">
                            <div class="indicator 
                                                                                                                                                {{ $document->MTPRsend == 'Aktif' ? 'green' : 'red' }}"
                                title="{{ $document->MTPRsend == 'Aktif' ? 'Dokumen sudah dikirim' : 'Dokumen belum dikirim' }}">
                            </div>
                            <span class="keterangan">MTPR</span>
                        </div>

                        <div class="d-flex justify-content-center w-100">
                            <span class="arrow">↓</span>
                        </div>
                    @endif
                    <div class="workflow-row">
                        <div class="indicator 
                                                                                    {{ $document->operatorshare == 'Aktif'
                                                                                        ? 'green'
                                                                                        : ($document->operatorshare == 'Ongoing'
                                                                                            ? 'orange'
                                                                                            : ($document->operatorshare == 'Belum dibaca'
                                                                                                ? 'yellow'
                                                                                                : 'red')) }}"
                            title="{{ $document->operatorshare == 'Aktif'
                                ? 'Dokumen sudah dibagikan ke unit'
                                : ($document->operatorshare == 'Ongoing'
                                    ? 'Dokumen sedang dibagikan ke unit'
                                    : ($document->operatorshare == 'Belum dibaca'
                                        ? 'Dokumen belum dibaca oleh unit'
                                        : 'Dokumen belum dibagikan ke unit')) }}">
                        </div>
                        @if ($document->operator == null)
                            <span class="keterangan">Unchoosen Operator</span>
                        @else
                            <span class="keterangan">{{ $unitsingkatan[$document->operator] }}</span>
                        @endif
                    </div>

                </a>

                <span class="arrow">→</span>

                <div class="{{ $classbox2 }}" style="height: 300px;">
                    <h2>Eng</h2>
                    <ul class="pl-2 m-0 list-unstyled">
                        @foreach (['Product Engineering', 'Mechanical Engineering System', 'Electrical Engineering System', 'Quality Engineering', 'RAMS'] as $projectpic)
                            <li class="mb-2">
                                <div class="workflow-row">
                                    @if (isset($projectpics))
                                        @if (in_array($projectpic, $projectpics))
                                            <div class="indicator 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ $unitpicvalidation[$projectpic] == 'Aktif'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ? 'green'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            : ($unitpicvalidation[$projectpic] == 'Ongoing'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ? 'orange'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                : ($unitpicvalidation[$projectpic] == 'Belum dibaca'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ? 'yellow'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    : ($unitpicvalidation[$projectpic] == 'Sudah dibaca'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ? 'blue'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        : 'red'))) }}"
                                                title="{{ $unitpicvalidation[$projectpic] == 'Aktif'
                                                    ? $projectpic . ' sudah approve'
                                                    : ($unitpicvalidation[$projectpic] == 'Ongoing'
                                                        ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                        : ($unitpicvalidation[$projectpic] == 'Belum dibaca'
                                                            ? $projectpic . ' belum dibaca'
                                                            : ($unitpicvalidation[$projectpic] == 'Sudah dibaca'
                                                                ? $projectpic . ' sudah dibaca'
                                                                : $projectpic . ' belum dikerjakan'))) }}">
                                            </div>
                                        @else
                                            <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}">
                                            </div>
                                        @endif
                                    @else
                                        <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}">
                                        </div>
                                    @endif
                                    @if ($projectpic != 'RAMS')
                                        <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                    @else
                                        <span class="keterangan">{{ $projectpic }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="{{ $classbox2 }}" style="height: 300px;">
                    <h2>Des</h2>
                    <ul class="pl-2 m-0 list-unstyled">
                        @foreach (['Desain Mekanik & Interior', 'Desain Bogie & Wagon', 'Desain Carbody', 'Desain Elektrik'] as $projectpic)
                            <li class="mb-2">
                                <div class="workflow-row">
                                    @if (isset($projectpics))
                                        @if (in_array($projectpic, $projectpics))
                                            <div class="indicator 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            {{ $unitpicvalidation[$projectpic] == 'Aktif'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ? 'green'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                : ($unitpicvalidation[$projectpic] == 'Ongoing'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ? 'orange'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    : ($unitpicvalidation[$projectpic] == 'Belum dibaca'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ? 'yellow'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        : ($unitpicvalidation[$projectpic] == 'Sudah dibaca'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ? 'blue'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            : 'red'))) }}"
                                                title="{{ $unitpicvalidation[$projectpic] == 'Aktif'
                                                    ? $projectpic . ' sudah approve'
                                                    : ($unitpicvalidation[$projectpic] == 'Ongoing'
                                                        ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                        : ($unitpicvalidation[$projectpic] == 'Belum dibaca'
                                                            ? $projectpic . ' belum dibaca'
                                                            : ($unitpicvalidation[$projectpic] == 'Sudah dibaca'
                                                                ? $projectpic . ' sudah dibaca'
                                                                : $projectpic . ' belum dikerjakan'))) }}">
                                            </div>
                                        @else
                                            <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}">
                                            </div>
                                        @endif
                                    @else
                                        <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}">
                                        </div>
                                    @endif
                                    <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="{{ $classbox2 }}" style="height: 300px;">
                    <h2>TP</h2>
                    <ul class="pl-2 m-0 list-unstyled">
                        @foreach (['Preparation & Support', 'Welding Technology', 'Shop Drawing', 'Teknologi Proses'] as $projectpic)
                            <li class="mb-2">
                                <div class="workflow-row">
                                    @if (isset($projectpics))
                                        @if (in_array($projectpic, $projectpics))
                                            <div class="indicator 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                {{ $unitpicvalidation[$projectpic] == 'Aktif'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ? 'green'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    : ($unitpicvalidation[$projectpic] == 'Ongoing'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ? 'orange'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        : ($unitpicvalidation[$projectpic] == 'Belum dibaca'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ? 'yellow'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            : ($unitpicvalidation[$projectpic] == 'Sudah dibaca'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ? 'blue'
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                : 'red'))) }}"
                                                title="{{ $unitpicvalidation[$projectpic] == 'Aktif'
                                                    ? $projectpic . ' sudah approve'
                                                    : ($unitpicvalidation[$projectpic] == 'Ongoing'
                                                        ? $projectpic . ' sudah melakukan feedback dan belum approve'
                                                        : ($unitpicvalidation[$projectpic] == 'Belum dibaca'
                                                            ? $projectpic . ' belum dibaca'
                                                            : ($unitpicvalidation[$projectpic] == 'Sudah dibaca'
                                                                ? $projectpic . ' sudah dibaca'
                                                                : $projectpic . ' belum dikerjakan'))) }}">
                                            </div>
                                        @else
                                            <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}">
                                            </div>
                                        @endif
                                    @else
                                        <div class="indicator black" title="{{ $projectpic . ' tidak terlibat' }}">
                                        </div>
                                    @endif
                                    <span class="keterangan">{{ $unitsingkatan[$projectpic] }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <span class="arrow">→</span>

                <a class="{{ $classbox3 }}" href="#" style="text-decoration: none;">
                    @if ($document->configuration == 'parallel')
                        <div class="workflow-row">
                            <div class="indicator 
                                                                                                                                                                                                                                {{ $document->operatorcombinevalidation == 'Aktif'
                                                                                                                                                                                                                                    ? 'green'
                                                                                                                                                                                                                                    : ($document->operatorcombinevalidation == 'Ongoing'
                                                                                                                                                                                                                                        ? 'orange'
                                                                                                                                                                                                                                        : ($document->operatorcombinevalidation == 'Sudah dibaca'
                                                                                                                                                                                                                                            ? 'blue'
                                                                                                                                                                                                                                            : ($document->operatorcombinevalidation == 'Belum dibaca'
                                                                                                                                                                                                                                                ? 'yellow'
                                                                                                                                                                                                                                                : 'red'))) }}"
                                title="{{ $document->operatorcombinevalidation == 'Aktif'
                                    ? 'PE sudah melakukan review dan penggabungan'
                                    : ($document->operatorcombinevalidation == 'Ongoing'
                                        ? 'PE sedang melakukan review dan penggabungan'
                                        : ($document->operatorcombinevalidation == 'Sudah dibaca'
                                            ? 'PE sudah dibaca'
                                            : ($document->operatorcombinevalidation == 'Belum dibaca'
                                                ? 'PE belum dibaca'
                                                : 'PE belum melakukan review dan penggabungan'))) }}">
                            </div>
                            <span
                                class="keterangan">{{ $unitsingkatan[$document->operator] ?? $document->operator }}</span>
                        </div>

                        <div class="d-flex justify-content-center w-100">
                            <span class="arrow">↓</span>
                        </div>
                        @if ($document->manageroperatorvalidation != 'Tidak Terlibat')
                            <div class="workflow-row">
                                <div class="indicator 
                                                                                                                                                                                                                                                                                                    {{ $document->manageroperatorvalidation == 'Aktif'
                                                                                                                                                                                                                                                                                                        ? 'green'
                                                                                                                                                                                                                                                                                                        : ($document->manageroperatorvalidation == 'Ongoing'
                                                                                                                                                                                                                                                                                                            ? 'orange'
                                                                                                                                                                                                                                                                                                            : ($document->manageroperatorvalidation == 'Sudah dibaca'
                                                                                                                                                                                                                                                                                                                ? 'blue'
                                                                                                                                                                                                                                                                                                                : ($document->manageroperatorvalidation == 'Belum dibaca'
                                                                                                                                                                                                                                                                                                                    ? 'yellow'
                                                                                                                                                                                                                                                                                                                    : 'red'))) }}"
                                    title="{{ $document->manageroperatorvalidation == 'Aktif'
                                        ? 'Manager PE sudah melakukan review dan penggabungan'
                                        : ($document->manageroperatorvalidation == 'Ongoing'
                                            ? 'PE sedang melakukan review dan penggabungan'
                                            : ($document->manageroperatorvalidation == 'Sudah dibaca'
                                                ? 'PE sudah dibaca'
                                                : ($document->manageroperatorvalidation == 'Belum dibaca'
                                                    ? 'PE belum dibaca'
                                                    : 'Manager PE belum melakukan review dan penggabungan'))) }}">
                                </div>
                                <span class="keterangan">Manager
                                    {{ $unitsingkatan[$document->operator] ?? $document->operator }}</span>
                            </div>

                            <div class="d-flex justify-content-center w-100">
                                <span class="arrow">↓</span>
                            </div>
                        @endif
                    @endif
                    <div class="workflow-row">
                        <div class="indicator 
                                                                                {{ $document->seniormanagervalidation == 'Aktif'
                                                                                    ? 'green'
                                                                                    : ($document->seniormanagervalidation == 'Ongoing'
                                                                                        ? 'orange'
                                                                                        : ($document->seniormanagervalidation == 'Sudah dibaca'
                                                                                            ? 'blue'
                                                                                            : ($document->seniormanagervalidation == 'Belum dibaca'
                                                                                                ? 'yellow'
                                                                                                : 'red'))) }}"
                            title="{{ $document->seniormanagervalidation == 'Aktif'
                                ? 'Senior manager sudah melakukan review'
                                : ($document->seniormanagervalidation == 'Ongoing'
                                    ? 'Senior manager sedang melakukan review'
                                    : ($document->seniormanagervalidation == 'Sudah dibaca'
                                        ? 'Senior manager sudah membaca'
                                        : ($document->seniormanagervalidation == 'Belum dibaca'
                                            ? 'Senior manager belum membaca'
                                            : 'Senior manager belum melakukan review'))) }}">
                        </div>
                        @if ($document->SMname == 'Belum ditentukan')
                            <span class="keterangan">SM</span>
                        @else
                            <span class="keterangan">{{ $unitsingkatan[$document->SMname] }}</span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-center w-100">
                        <span class="arrow">↓</span>
                    </div>
                    <div class="workflow-row">
                        <div class="indicator 
                                                                                {{ $document->MTPRbeforeLogistik == 'Aktif'
                                                                                    ? 'green'
                                                                                    : ($document->MTPRbeforeLogistik == 'Ongoing'
                                                                                        ? 'orange'
                                                                                        : ($document->MTPRbeforeLogistik == 'Sudah dibaca'
                                                                                            ? 'blue'
                                                                                            : ($document->MTPRbeforeLogistik == 'Belum dibaca'
                                                                                                ? 'yellow'
                                                                                                : 'red'))) }}"
                            title="{{ $document->MTPRbeforeLogistik == 'Aktif'
                                ? 'MTPR sudah mengirim ke logistik'
                                : ($document->MTPRbeforeLogistik == 'Ongoing'
                                    ? 'MTPR sedang memproses dokumen'
                                    : ($document->MTPRbeforeLogistik == 'Sudah dibaca'
                                        ? 'MTPR sudah dibaca'
                                        : ($document->MTPRbeforeLogistik == 'Belum dibaca'
                                            ? 'MTPR belum dibaca'
                                            : 'MTPR belum mengirim ke logistik'))) }}">
                        </div>
                        <span class="keterangan">MTPR</span>
                    </div>
                    @if ($document->is_expand_to_logistic == true)
                        <div class="d-flex justify-content-center w-100">
                            <span class="arrow">↓</span>
                        </div>

                        <div class="workflow-row">
                            <div class="indicator 
                                                                                                                                        {{ $document->MTPRbeforeLogistik == 'Aktif' ? 'orange' : 'red' }}"
                                title="{{ $document->MTPRbeforeLogistik == 'Aktif' ? 'Dokumen menunggu diproses' : 'Dokumen belum ditutup' }}">
                            </div>
                            <span class="keterangan">Logistik</span>
                        </div>
                    @endif

                </a>

            </div>

        </td>
        <!-- STATUS -->
        <td class="text-center" style="width: 120px;">
            @if (
                $authuser->rule == $document->operator ||
                    $authuser->rule == 'superuser' ||
                    $authuser->id == 9 ||
                    $authuser->id == 1 ||
                    $authuser->rule == 'MTPR')
                <button type="button"
                    class="btn document-status-button document-status-button-{{ $document->documentstatus == 'Terbuka' ? 'open' : 'closed' }} btn-sm {{ $document->documentstatus == 'Terbuka' ? 'btn-danger' : 'btn-success' }}"
                    title="{{ $document->documentstatus }}" onclick="toggleDocumentStatus(this)"
                    data-document-status="{{ $document->documentstatus }}" data-document-id="{{ $document->id }}"
                    data-url="{{ route('new-memo.updatedocumentstatus', ['memoId' => $document->id]) }}">
                    <i
                        class="{{ $document->documentstatus == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle' }}"></i>
                    <span>{{ $document->documentstatus }}</span>
                </button>
            @else
                <span class="badge badge-{{ $document->documentstatus == 'Terbuka' ? 'danger' : 'success' }}"
                    style="padding: 8px 12px; font-size: 12px;">
                    <i
                        class="{{ $document->documentstatus == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle' }} mr-1"></i>
                    {{ $document->documentstatus }}
                </span>
            @endif
        </td>

        <!-- AKSI -->
        <td class="text-center" style="min-width: 120px;">
            @if (auth()->user()->rule != 'Logistik')
                <div class="btn-group-vertical btn-group-sm" role="group">

                    <a class="btn btn-primary btn-sm mb-1 text-left"
                        href="{{ route('new-memo.show', ['memoId' => $document->id, 'rule' => auth()->user()->rule]) }}"
                        title="Detail">
                        <i class="fas fa-eye mr-1"></i> Detail
                    </a>

                    <a class="btn btn-info btn-sm mb-1 text-left"
                        href="{{ route('new-memo.timelinetracking', ['memoId' => $document->id]) }}"
                        title="Milestone">
                        <i class="fas fa-flag mr-1"></i> Milestone
                    </a>

                    @if (auth()->user()->rule == 'superuser')
                        <button type="button" class="btn btn-danger btn-sm text-left"
                            onclick="confirmDelete('{{ $document->id }}')" title="Hapus">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    @endif
                </div>
            @endif
        </td>
    </tr>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function toggleDocumentStatus(button) {
        const targetUrl = button.getAttribute('data-url');
        const currentStatus = button.getAttribute('data-document-status');
        const newStatus = currentStatus === 'Terbuka' ? 'Tertutup' : 'Terbuka';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let swalConfig = {};

        if (newStatus === 'Tertutup') {
            swalConfig = {
                title: 'Upload Memo Penutupan',
                html: `
                    <div class="text-left">
                        <p class="mb-2">Dokumen akan <strong>DITUTUP</strong>.</p>
                        <p class="mb-3 text-muted small">Silakan upload file memo (PDF/Gambar) sebagai bukti penutupan.</p>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="memoFile" accept="application/pdf, image/jpeg, image/png" aria-label="Upload file memo">
                            <label class="custom-file-label text-left" for="memoFile">Pilih file...</label>
                        </div>
                        <div id="fileNameDisplay" class="mt-2 text-info small font-weight-bold"></div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-upload mr-1"></i> Upload & Tutup',
                cancelButtonText: 'Batal',
                didOpen: () => {
                    const fileInput = Swal.getPopup().querySelector('#memoFile');
                    const fileNameDisplay = Swal.getPopup().querySelector('#fileNameDisplay');
                    const fileLabel = Swal.getPopup().querySelector('.custom-file-label');

                    fileInput.addEventListener('change', (e) => {
                        const file = e.target.files[0];
                        if (file) {
                            fileLabel.textContent = file.name;
                            fileNameDisplay.textContent = 'File terpilih: ' + file.name;
                        } else {
                            fileLabel.textContent = 'Pilih file...';
                            fileNameDisplay.textContent = '';
                        }
                    });
                },
                preConfirm: () => {
                    const fileInput = Swal.getPopup().querySelector('#memoFile');
                    const file = fileInput.files[0];
                    if (!file) {
                        Swal.showValidationMessage('Anda wajib mengupload file memo!');
                    }
                    return file;
                }
            };
        } else {
            swalConfig = {
                title: 'Konfirmasi Perubahan',
                html: 'Dokumen akan <strong>DIBUKA KEMBALI</strong>.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Buka Kembali',
                cancelButtonText: 'Batal'
            };
        }

        Swal.fire(swalConfig).then((result) => {
            if (result.isConfirmed) {
                const originalHTML = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ...';

                const formData = new FormData();
                formData.append('status', newStatus);

                if (newStatus === 'Tertutup' && result.value) {
                    formData.append('file[]', result.value);
                }

                fetch(targetUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Gagal menghubungi server');
                        return res.json();
                    })
                    .then(data => {
                        if (data.new_status) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: `Status berhasil diubah menjadi ${data.new_status}`,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            if (data.new_status === 'Tertutup') {

                                var row = $(button).closest('tr');
                                var table = $(button).closest('table');

                                row.css('background-color', '#d4edda').fadeOut(600, function() {
                                    if ($.fn.DataTable.isDataTable(table)) {
                                        table.DataTable().row(row).remove().draw(false);
                                    } else {
                                        $(this).remove();
                                    }
                                });
                                updateButtonUI(button, data.new_status);
                            } else {
                                updateButtonUI(button, data.new_status);
                            }
                        } else {
                            Swal.fire('Info', 'Status mungkin telah berubah, silakan refresh.', 'info');
                            location.reload();
                        }
                    })
                    .catch(err => {
                        button.disabled = false;
                        button.innerHTML = originalHTML;
                        console.error(err);
                        Swal.fire('Error', 'Terjadi kesalahan saat memproses permintaan.', 'error');
                    });
            }
        });
    }

    function updateButtonUI(button, status) {
        button.disabled = false;
        button.setAttribute('data-document-status', status);

        // Reset class
        button.className = 'btn btn-sm document-status-button';

        if (status === 'Terbuka') {
            button.classList.add('btn-danger', 'document-status-button-open');
            button.innerHTML = '<i class="fas fa-times-circle"></i> <span>Terbuka</span>';
            button.title = 'Terbuka';
        } else {
            button.classList.add('btn-success', 'document-status-button-closed');
            button.innerHTML = '<i class="fas fa-check-circle"></i> <span>Tertutup</span>';
            button.title = 'Tertutup';
        }
    }
</script>

<style>
    /* Gaya untuk tombol status dokumen */
    .document-status-button {
        padding: 6px 10px;
        border-radius: 5px;
        font-size: 12px;
        transition: all 0.3s ease;
    }

    .document-status-button-open {
        background-color: #dc3545;
        color: #fff;
    }

    .document-status-button-closed {
        background-color: #28a745;
        color: #fff;
    }

    .document-status-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Progress wrapper */
    .progress-wrapper {
        padding: 5px 10px;
    }

    /* Document name ellipsis */
    .document-name {
        max-height: 3em;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    /* Button group vertical */
    .btn-group-vertical .btn {
        border-radius: 4px !important;
        margin-bottom: 3px;
    }

    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }

    /* Gaya untuk workflow visualization */
    .project-actionkus>div {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }


    .arrow {
        margin: 0 5px;
        font-size: 24px;
        color: #00b0ff;
    }

    .container-box {
        display: flex;
        align-items: center;
    }


    .boxblue {
        margin-right: 5px;
        border: 1px solid #00b0ff;
        border-radius: 10px;
        padding: 10px;
        background-color: #e1f5fe;
        box-shadow: 0 2px 4px rgba(0, 176, 255, 0.2);
    }

    .box {
        margin-right: 5px;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 10px;
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    h2 {
        font-size: 20px;
        margin-bottom: 10px;
        color: #333;
    }

    ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }

    li {
        margin-bottom: 10px;
    }

    .keterangan {
        margin-left: 5px;
        font-size: 16px;
        color: #555;
    }

    .indicator {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        margin-right: 5px;
    }

    .green {
        background-color: #4caf50;
    }

    .red {
        background-color: #f44336;
    }

    .yellow {
        background-color: #ffeb3b;
    }

    .blue {
        background-color: #2196f3;
    }

    .orange {
        background-color: #ff9800;
    }

    .black {
        background-color: #212121;
    }

    /* RESPONSIVE DESIGN */
    @media screen and (max-width: 768px) {
        .project-actionkus {
            padding: 10px;
            transform: scale(0.85);
            transform-origin: left center;
        }

        .boxblue,
        .box {
            padding: 5px;
            margin-right: 3px;
        }

        h2 {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .keterangan {
            font-size: 12px;
            margin-left: 3px;
        }

        .indicator {
            width: 15px;
            height: 15px;
        }

        .arrow {
            font-size: 18px;
            margin: 0 3px;
        }

        li {
            margin-bottom: 5px;
        }

        .boxblue[style*="height: 300px"],
        .box[style*="height: 300px"] {
            height: 200px !important;
        }

        .btn-group-vertical .btn {
            font-size: 10px;
            padding: 4px 8px;
        }

        .document-status-button {
            font-size: 10px;
            padding: 4px 6px;
        }
    }

    .workflow-row {
        display: flex;
        align-items: center;
        /* KUNCI: Sejajar Vertikal Tengah */
        width: 100%;
    }

    @media screen and (min-width: 769px) and (max-width: 1024px) {
        .project-actionkus {
            padding: 15px;
        }

        .boxblue,
        .box {
            padding: 8px;
        }

        h2 {
            font-size: 16px;
        }

        .keterangan {
            font-size: 14px;
        }

        .indicator {
            width: 18px;
            height: 18px;
        }

        .arrow {
            font-size: 20px;
        }

        .boxblue[style*="height: 300px"],
        .box[style*="height: 300px"] {
            height: 250px !important;
        }
    }

    @media screen and (min-width: 1400px) {
        .project-actionkus {
            padding: 25px;
        }

        .keterangan {
            font-size: 18px;
        }

        h2 {
            font-size: 22px;
        }
    }

    @media (hover: hover) {

        .boxblue:hover,
        .box:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease;
        }
    }

    @media print {

        .project-actions,
        .document-status-button,
        .btn-group-vertical {
            display: none;
        }

        .project-actionkus {
            transform: scale(0.7);
        }
    }

    /* Bar Styling */
    /* 1. Wrapper Container */
    .progress-wrapper {
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    /* 2. Track (Latar Belakang Progress) */
    .progress {
        background-color: #e9ecef;
        border-radius: 50px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        height: 12px !important;
        width: 100%;
        margin-bottom: 8px !important;
    }

    /* 3. Bar (Isian Progress) */
    .progress-bar {
        border-radius: 50px;
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;

        background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
    }

    /* Styling Khusus jika 100% (Success) */
    .progress-bar.bg-success {
        background-color: #28a745;
        background-image: linear-gradient(to right, #28a745, #00d25b);
        box-shadow: 0 2px 5px rgba(40, 167, 69, 0.4);
    }

    /* Styling Khusus jika < 100% (Warning) */
    .progress-bar.bg-warning {
        background-color: #ffc107;
        background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent), linear-gradient(to right, #ffc107, #ffdb4d);
        animation: progress-bar-stripes 1s linear infinite;
        box-shadow: 0 2px 5px rgba(255, 193, 7, 0.4);
    }

    /* 4. Badge Percentage Text */
    .progress-wrapper .badge {
        font-weight: 600;
        letter-spacing: 0.5px;
        padding: 5px 12px;
        border-radius: 12px;
        font-size: 11px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .progress-wrapper .badge-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .progress-wrapper .badge-warning {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    /* Hover Effect pada Wrapper */
    .progress-wrapper:hover .badge {
        transform: translateY(-2px);
    }

    @keyframes progress-bar-stripes {
        from {
            background-position: 1rem 0;
        }

        to {
            background-position: 0 0;
        }
    }
</style>
