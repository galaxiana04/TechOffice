@php
    use Carbon\Carbon; // Import Carbon class
@endphp

<table id="example2" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Dibuat</th>
            <th>No Dokumen</th>
            <th>Nama Dokumen</th>
            <th>Detail Project</th>
            <th>Pihak Terlibat</th>
            <th>Dokumen Pendukung (Vault) & Catatan</th>
            <th>Posisi</th>
            <th>Waktu Mulai & Countup</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $urutan = 1;
        @endphp
        @foreach ($jobtickets as $index => $jobticket)
            @php
                $documenref = $jobticket->getDocumentSupport();
                $startcolor = 'bg-gray';
                $continuecolor = '';
                $donecolor = '';
                $pausecolor = '';
                $draftername = $jobticket->drafter_name ?? null;
                $checkername = $jobticket->checker_name ?? null;
                $approvername = $jobticket->approver_name ?? null;
                $jobticket_started = $jobticket->jobticketStarted;
                $utc_time = ($jobticket_started && is_object($jobticket_started)) ? ($jobticket_started->start_time_first ?? 'Belum Ada') : 'Belum Ada';

                // Convert UTC time to Asia/Jakarta timezone
                $waktuindo = $utc_time !== 'Belum Ada'
                    ? Carbon::parse($utc_time, 'UTC')->setTimezone('Asia/Jakarta')->format('d/m/Y')
                    : 'Belum Ada';

                // Calculate total time
                $elapsedSeconds = $jobticket_started->total_elapsed_seconds ?? 0;
                $startTime = $jobticket_started->start_time_run ?? null;
                $pauseTime = $jobticket_started->pause_time_run ?? null;
                $currentTime = Carbon::now();
                $totalTime = 0;

                if ($startTime !== null) {
                    $startTime = Carbon::parse($startTime);
                    if ($pauseTime !== null) {
                        $pauseTime = Carbon::parse($pauseTime);
                    }
                    $totalTime = $pauseTime === null
                        ? $currentTime->diffInSeconds($startTime) + $elapsedSeconds
                        : $pauseTime->diffInSeconds($startTime) + $elapsedSeconds;
                }
            @endphp

            <tr>
                <td>{{ $urutan++ }}</td>
                <td>{{ Carbon::parse($jobticket->created_at)->format('d/m/Y') }}</td>
                <td>{{ $jobticket->jobticketIdentity->documentnumber }}</td>
                <td>{{ $jobticket->documentname }}</td>
                <td>
                    <p><strong>Proyek:</strong></p>
                    <p><span class="badge bg-info">{{ $jobticket->jobticketIdentity->jobticketPart->projectType->title }}</span></p>
                    <strong>Jenis Dokumen:</strong>
                    <p><span class="badge bg-info">{{ $jobticket->jobticketIdentity->jobticketDocumentkind->name ?? 'Tidak ada' }}</span></p>
                    <p><strong>Rev:</strong><span class="badge bg-info">{{ $jobticket->rev }}</span></p>
                    <p><strong>Level:</strong><span class="badge bg-info">{{ $jobticket->level }}</span></p>
                    <p><strong>Status:</strong>
                        @if ($jobticket->status === 'closed')
                            <span class="badge bg-success">Closed</span>
                        @else
                            <span class="badge bg-info">{{ $jobticket->status ?? 'Open' }}</span>
                        @endif
                    </p>
                </td>
                <td>
                    <div class="user-info">
                        <p id="drafter_{{ $jobticket->id }}"><strong>Drafter:</strong>
                            <span class="badge badge-secondary">{{ $jobticket->drafter_name ?? 'Belum ada' }}</span>
                        </p>
                        <p id="checker_{{ $jobticket->id }}"><strong>Checker:</strong>
                            <span class="badge badge-secondary">{{ $jobticket->checker_name ?? 'Belum ada' }}</span>
                        </p>
                        <p id="approver_{{ $jobticket->id }}"><strong>Approver:</strong>
                            <span class="badge badge-secondary">{{ $jobticket->approver_name ?? 'Belum ada' }}</span>
                        </p>
                    </div>
                </td>
                <td id="document-support-container-{{ $urutan }}">
                    @if ($documenref)
                        @foreach ($documenref as $document)
                            @php
                                $text = "{$document->namadokumen} - {$document->nodokumen} - {$document->rev}";
                                $hasFile = !empty($document->fileid);
                                $badgeClass = $hasFile ? 'badge bg-success' : 'badge bg-danger';
                            @endphp
                            @if ($hasFile)
                                @if (config('app.url') !== 'https://inka.goovicess.com')
                                    <a href="http://10.10.0.40/AutodeskTC/10.10.0.40/TekVault_0003_Dec2011/Document/Download?fileId={{ $document->fileid }}&downloadAsInline=true"
                                        class="d-inline-block mb-1" target="_blank" rel="noopener noreferrer">
                                        <span class="{{ $badgeClass }}">
                                            {{ $text }} – <strong>Lihat</strong>
                                        </span>
                                    </a><br>
                                @else
                                    <span class="{{ $badgeClass }}">
                                        <strong>Perhatian:</strong> Ketik
                                        <code>Downloadfile_{{ $document->fileid }}</code>
                                    </span><br>
                                @endif
                            @else
                                <span class="{{ $badgeClass }}">{{ $text }}</span><br>
                            @endif
                        @endforeach
                    @else
                        <span class="badge bg-secondary">Tidak ada dokumen pendukung</span><br>
                    @endif
                    <p><strong>Catatan</strong></p>
                    <p id="note_{{ $jobticket->id }}" style="white-space: normal; word-wrap: break-word;">
                        {{ $jobticket->note ?? 'Belum ada catatan' }}
                    </p>
                </td>
                <td class="project-actionkus text-right">
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 0;
                            background-color: #f0f2f5;
                        }
                        .project-actionkus {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            padding: 20px;
                        }
                        .action-group {
                            display: flex;
                            align-items: center;
                            margin: 0 10px;
                        }
                        .arrow {
                            margin: 0 5px;
                            font-size: 24px;
                            color: #00b0ff;
                        }
                        .container {
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
                        .green { background-color: #4caf50; }
                        .red { background-color: #f44336; }
                        .yellow { background-color: #ffeb3b; }
                        .blue { background-color: #2196f3; }
                        .orange { background-color: #ff9800; }
                        .black { background-color: #212121; }
                    </style>
                    @php
                        $classbox1 = 'boxblue';
                        $drafterstatus = isset($jobticket->allrule->drafter_status) ? $jobticket->allrule->drafter_status : 'Not Approve';
                        $checkerstatus = isset($jobticket->allrule->checker_status) ? $jobticket->allrule->checker_status : 'Not Approve';
                        $approverstatus = isset($jobticket->allrule->approver_status) ? $jobticket->allrule->approver_status : 'Not Approve';
                    @endphp
                    <a class="{{ $classbox1 }}" href="#">
                        <div class="container">
                            <div class="indicator {{ $drafterstatus == 'Approve' || ($jobticket && $jobticket->status === 'closed') ? 'green' : ($drafterstatus == 'Ongoing' ? 'orange' : ($drafterstatus == 'Belum dibaca' ? 'yellow' : 'red')) }}"
                                title="{{ $drafterstatus == 'Approve' ? 'Drafter telah upload dokumen' : ($drafterstatus == 'Ongoing' ? 'Drafter sudah menyelesaikan dokumen tetapi belum upload dokumen' : ($drafterstatus == 'Belum dibaca' ? 'Dokumen belum dibaca oleh unit' : 'Belum ada tindakan yang bisa diambil')) }}">
                            </div>
                            <span class="keterangan">Drafter</span>
                        </div>
                    </a>
                    <span class="arrow">→</span>
                    <a class="{{ $classbox1 }}" href="#">
                        <div class="container">
                            <div class="indicator {{ $checkerstatus == 'Approve' || ($jobticket && $jobticket->status === 'closed') ? 'green' : ($checkerstatus == 'Ongoing' ? 'orange' : ($checkerstatus == 'Belum dibaca' ? 'yellow' : 'red')) }}"
                                title="{{ $checkerstatus == 'Approve' ? 'Checker telah menyetujui' : ($checkerstatus == 'Ongoing' ? 'Checker belum menyetujui' : ($checkerstatus == 'Belum dibaca' ? 'Dokumen belum dibaca oleh unit' : 'Belum ada tindakan yang bisa diambil')) }}">
                            </div>
                            <span class="keterangan">Checker</span>
                        </div>
                    </a>
                    <span class="arrow">→</span>
                    <a class="{{ $classbox1 }}" href="#">
                        <div class="container">
                            <div class="indicator {{ $approverstatus == 'Approve' || ($jobticket && $jobticket->status === 'closed') ? 'green' : ($approverstatus == 'Ongoing' ? 'orange' : ($approverstatus == 'Belum dibaca' ? 'yellow' : 'red')) }}"
                                title="{{ $approverstatus == 'Approve' ? 'Approver telah menyetujui' : ($approverstatus == 'Ongoing' ? 'Approver belum menyetujui' : ($approverstatus == 'Belum dibaca' ? 'Dokumen belum dibaca oleh unit' : 'Belum ada tindakan yang bisa diambil')) }}">
                            </div>
                            <span class="keterangan">Approver</span>
                        </div>
                    </a>
                </td>
                <td>
                    <strong>Start:</strong>
                    <span class="badge badge-secondary">{{ $waktuindo }}</span><br>
                    <strong>Count Up:</strong>
                    <div id="elapsed_time_{{ $jobticket->id }}" style="position: relative;">
                        <span class="time-display badge {{ $pauseTime ? 'badge-warning' : 'badge-info' }}">
                            @if ($pauseTime === null)
                                {{ gmdate('H:i:s', $totalTime) }}
                            @else
                                Paused
                            @endif
                        </span>
                    </div>
                </td>
                <td>
                    @php
                        if (isset($jobticket->jobticketStarted->revisions) && count($jobticket->jobticketStarted->revisions) > 0) {
                            $detailcolor = 'bg-maroon';
                        } else {
                            $detailcolor = 'bg-gray';
                        }
                    @endphp
                    <a href="#" class="btn btn-default {{ $detailcolor }} d-block mb-1"
                        id="buttondetailtugas_{{ $jobticket->id }}_{{ $index }}"
                        onclick="detailtugas('{{ $jobticket->jobticketIdentity->jobticket_part_id }}', '{{ $jobticket->jobticket_identity_id }}', '{{ $jobticket->id }}')">
                        <i class="fas fa-info-circle"></i> Detail
                    </a>
                    @if ($useronly->rule == 'superuser')
                        <a href="#" class="btn btn-default bg-maroon d-block mb-1"
                            id="buttondeletetugas_{{ $jobticket->id }}_{{ $index }}"
                            onclick="deletetugas('{{ $jobticket->jobticketIdentity->jobticket_part_id }}','{{ $jobticket->jobticket_identity_id }}','{{ $jobticket->id }}')">
                            <i class="fas fa-info-circle"></i> Delete Jobticket
                        </a>
                    @endif
                    @if ($jobticket->status == null && $jobticket->publicstatus != 'drafted' && $statusposition == 'drafter')
                        @if ($jobticket->checker_id == null || $jobticket->approver_id == null)
                            <a href="#" class="btn btn-default bg-pink d-block mb-1"
                                id="selectuserbutton_{{ $jobticket->id }}_{{ $index }}"
                                onclick="pickposition('{{ $jobticket->id }}', {{ json_encode($jobticket) }}, '{{ $index }}', '{{ $useronly->rule }}')">
                                <i class="fas fa-hand-pointer"></i> Select Pic
                            </a>
                        @endif
                        @if (!isset($jobticket_started->start_time_first))
                            @php
                                $statusrevisi = $jobticket_started->statusrevisi ?? 'dibuka';
                            @endphp
                            @if ($draftername == '-' || $draftername == '' || $draftername == null)
                                @if (!isset($indukan[strval($jobticket->id)]['persen']))
                                    <a href="#" class="btn btn-success btn-sm d-block mb-1"
                                        id="button_{{ $jobticket->id }}_{{ $index }}"
                                        onclick="picktugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}','drafter')">
                                        <i class="fas fa-hand-pointer"></i> Pick Drafter
                                    </a>
                                @elseif(isset($indukan[strval($jobticket->id)]['persen']) &&
                                    $indukan[strval($jobticket->id)]['persen']['count'] !=
                                    $indukan[strval($jobticket->id)]['persen']['countrelease'])
                                    <a href="#" class="btn btn-default bg-pink d-block mb-1" id="button">
                                        <i class="fas fa-hand-pointer"></i> Dokumen Pendukung Belum Release
                                    </a>
                                @elseif(isset($indukan[strval($jobticket->id)]['persen']) &&
                                    $indukan[strval($jobticket->id)]['persen']['count'] ==
                                    $indukan[strval($jobticket->id)]['persen']['countrelease'])
                                    <a href="#" class="btn btn-success btn-sm d-block mb-1"
                                        id="button_{{ $jobticket->id }}_{{ $index }}"
                                        onclick="picktugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                        <i class="fas fa-hand-pointer"></i> Pick Tugas
                                    </a>
                                @endif
                            @else
                                @if (!isset($jobticket_started) || $jobticket_started->statusrevisi != 'ditutup')
                                    @if ($draftername == $useronly->name)
                                        @php
                                            $startcolor = ($jobticket->documentsupport !== null || $jobticket->note !== null) ? 'btn-warning' : 'bg-gray';
                                        @endphp
                                        <a href="#" class="btn {{ $startcolor }} btn-sm d-block mb-1"
                                            id="button_{{ $jobticket->id }}_{{ $index }}"
                                            onclick="starttugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                            <i class="fas fa-rocket"></i> Start Tugas
                                        </a>
                                    @else
                                        <a href="#" class="btn btn-default bg-white d-block mb-1" id="button">
                                            <i class="fas fa-hand-pointer"></i> Tugas Milik Orang
                                        </a>
                                    @endif
                                @endif
                            @endif
                        @else
                            @if ($useronly->name == $jobticket->drafter_name)
                                @if ($jobticket_started->pause_time_run == null)
                                    @php
                                        $pausecolor = 'btn-secondary';
                                    @endphp
                                    <a href="#" class="btn btn-secondary btn-sm d-block mb-1"
                                        id="button_{{ $jobticket->id }}_{{ $index }}"
                                        onclick="pausetugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                        <i class="fas fa-pause-circle"></i> Jeda
                                    </a>
                                @else
                                    @php
                                        $continuecolor = 'btn-primary';
                                    @endphp
                                    <a href="#" class="btn {{ $continuecolor }} btn-sm d-block mb-1"
                                        id="button_{{ $jobticket->id }}_{{ $index }}"
                                        onclick="resumetugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                        <i class="fas fa-play-circle"></i> Lanjutkan
                                    </a>
                                @endif
                                @php
                                    if ($continuecolor == 'btn-primary') {
                                        $donecolor = 'btn-danger';
                                    } elseif ($detailcolor == 'bg-maroon') {
                                        $donecolor = 'bg-gray';
                                    } else {
                                        $donecolor = 'btn-danger';
                                    }
                                @endphp
                                <a href="#" class="btn {{ $donecolor }} btn-sm d-block mb-1"
                                    id="button_{{ $jobticket->id }}_{{ $index }}"
                                    onclick="selesaitugas('{{ $jobticket->id }}', '{{ $index }}', '{{ $useronly->name }}')">
                                    <i class="fas fa-check-circle"></i> Selesai
                                </a>
                            @endif
                        @endif
                        @php
                            if ($draftername == '-' || $draftername == '' || $draftername == null) {
                                $documentcolor = 'bg-gray';
                                $notecolor = 'bg-gray';
                            } elseif ($detailcolor == 'bg-maroon' || $continuecolor == 'btn-primary' || $pausecolor == 'btn-secondary' || $startcolor == 'btn-warning') {
                                $documentcolor = 'bg-gray';
                                $notecolor = 'bg-gray';
                            } else {
                                $documentcolor = 'bg-purple';
                                $notecolor = 'bg-orange';
                            }
                        @endphp
                        <a href="#" class="btn btn-default {{ $documentcolor }} d-block mb-1"
                            id="documentpickerbutton_{{ $jobticket->id }}_{{ $index }}"
                            onclick="pickdocument('{{ $jobticket->id }}','{{ json_encode($documenref) }}','{{ $index }}', '{{ $useronly->name }}')">
                            <i class="fas fa-hand-pointer"></i> Pick Dokumen
                        </a>
                        <a href="#" class="btn btn-default {{ $notecolor }} d-block mb-1"
                            id="notebutton_{{ $jobticket->id }}_{{ $index }}"
                            onclick="picknote('{{ $jobticket->id }}','{{ json_encode($documenref) }}','{{ $index }}', '{{ $useronly->name }}')">
                            <i class="fas fa-hand-pointer"></i> Tambahkan Note
                        </a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>