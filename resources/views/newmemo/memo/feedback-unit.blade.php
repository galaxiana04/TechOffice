@php
    $userinformations = $document->feedbacks; 
@endphp

@if (json_decode($document->project_pic))
    @php
        $project_pic = json_decode($document->project_pic);
        $manager_project_pic = [];
        foreach ($project_pic as $unittunggal) {
            $manager_project_pic[] = 'Manager ' . $unittunggal;
        }
    @endphp

    @if (in_array($yourrule, $manager_project_pic) || in_array($yourrule, $project_pic) || in_array($yourrule, ['superuser', $document->operator, 'MTPR']))

        @foreach ($project_pic as $unit)
            @php
                $managerunit = 'Manager ' . $unit;
            @endphp

            @if (in_array($yourrule, $manager_project_pic) || in_array($yourrule, $project_pic) || $yourrule == 'MTPR' || $yourrule == $document->operator)

                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card card-primary card-outline shadow-lg border-0 modern-card h-100">

                        <div class="card-header gradient-header">
                            <h3 class="card-title">
                                <i class="fas fa-comments mr-3"></i>
                                Feedback {{ $unit }}
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool text-white" data-card-widget="remove" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        @php
                            $userinformations = $document->feedbacks;
                        @endphp

                        <div class="card-body modern-body">

                            {{-- Cek Status Approval --}}
                            @foreach ($userinformations as $userinformation)
                                @if ($userinformation->level == $unit && $userinformation->conditionoffile2 == 'feedback')
                                    @php
                                        $statussetuju = $userinformation->conditionoffile;
                                        if ($statussetuju == 'Approved') {
                                            $statussetujulist[$unit] = $statussetuju;
                                        }
                                    @endphp
                                @endif
                            @endforeach

                            {{-- Loop Data Feedback --}}
                            @foreach ($userinformations as $userinformation)
                                @if ($userinformation->level == $unit && $userinformation->conditionoffile2 == 'feedback')
                                    @php
                                        $files = $userinformation->files;
                                    @endphp

                                    <div class="text-center mb-3 fade-in">
                                        <button class="btn btn-sm btn-info toggle-info shadow-sm"
                                            style="border-radius: 20px; padding-inline: 20px;">
                                            <i class="fas fa-eye me-1"></i> Lihat Detail
                                        </button>
                                    </div>

                                    <div class="info-container mt-2" style="display: none;">
                                        <div class="card bg-light border-0">
                                            <div class="card-body p-3">

                                                <div class="mb-3 text-center">
                                                    @if ($userinformation->level == $yourrule)
                                                        <span class="badge bg-warning text-dark p-2">
                                                            <i class="fas fa-inbox me-1"></i> Penerima:
                                                            {{ $userinformation->level ?? 'Upload Only' }}
                                                        </span>
                                                    @elseif ($userinformation->level == '')
                                                        <span class="badge bg-warning text-dark p-2">
                                                            <i class="fas fa-cloud-upload-alt me-1"></i> Upload Pribadi
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger p-2">
                                                            <i class="fas fa-paper-plane me-1"></i> Terkirim ke:
                                                            {{ $userinformation->level ?? 'Upload Only' }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <div class="info-icon"><i class="fas fa-clipboard-check"></i></div>
                                                        <div class="info-content">
                                                            <span class="info-label">Status Review</span>
                                                            <span class="info-value">{{ $userinformation->review ?: 'Kosong' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="info-item">
                                                        <div class="info-icon"><i class="fas fa-user"></i></div>
                                                        <div class="info-content">
                                                            <span class="info-label">Penulis</span>
                                                            <span class="info-value">{{ $userinformation->author ?: 'Kosong' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="info-item">
                                                        <div class="info-icon"><i class="fas fa-envelope"></i></div>
                                                        <div class="info-content">
                                                            <span class="info-label">Email</span>
                                                            {{-- Tambahkan class 'text-break' --}}
                                                            <span class="info-value text-break">{{ $userinformation->email ?: 'Kosong' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="info-item">
                                                        <div class="info-icon"><i class="fas fa-book-reader"></i></div>
                                                        <div class="info-content">
                                                            <span class="info-label">Dibaca?</span>
                                                            <span class="info-value">{{ $userinformation->sudahdibaca ?: 'Kosong' }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="info-item">
                                                        <div class="info-icon"><i class="fas fa-id-badge"></i></div>
                                                        <div class="info-content">
                                                            <span class="info-label">ID Comment</span>
                                                            <span class="info-value">{{ $userinformation->id ?: 'Kosong' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="info-grid mb-3">
                                        @php
                                            $sendtime = $userinformation->created_at;
                                            $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                                        @endphp

                                        <div class="info-item fade-in">
                                            <div class="info-icon"><i class="fas fa-clock"></i></div>
                                            <div class="info-content">
                                                <span class="info-label">Waktu</span>
                                                <span class="info-value">{!! $formattedTime ?? 'Kosong' !!}</span>
                                            </div>
                                        </div>

                                        <div class="info-item fade-in" style="animation-delay: 0.1s;">
                                            <div class="info-icon"><i class="fas fa-info-circle"></i></div>
                                            <div class="info-content">
                                                <span class="info-label">Status Dokumen</span>
                                                <span class="info-value">{{ $userinformation->hasilreview ?: 'Kosong' }}</span>
                                            </div>
                                        </div>

                                        <div class="info-item fade-in" style="animation-delay: 0.2s;">
                                            <div class="info-icon"><i class="fas fa-tasks"></i></div>
                                            <div class="info-content">
                                                <span class="info-label">Status</span>
                                                <span class="info-value">{{ ucfirst($userinformation->conditionoffile ?: 'Kosong') }}</span>
                                                @php
                                                    $statussetuju = $userinformation->conditionoffile;
                                                @endphp
                                            </div>
                                        </div>
                                    </div>

                                    @if ($yourrule == $document->operator)
                                        <div class="mb-3 fade-in">
                                            <form id="deleteFeedbackForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                                action="{{ route('new-memo.deletedfeedbackdecision', ['memoId' => $document->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="posisi" value="{{ $userinformation->id }}">
                                                <button type="button" class="btn btn-warning btn-sm w-100 shadow-sm"
                                                    onclick="confirmDecision('deleteFeedbackForm{{ $document->id }}{{ $sendtime }}')">
                                                    <i class="fas fa-trash me-2"></i> Delete Feedback
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    @if ($files)
                                        <div class="files-section fade-in mt-3">
                                            <h6 class="section-title text-sm"><i class="fas fa-paperclip me-2"></i> File Lampiran</h6>
                                            @foreach ($userinformation->files as $file)
                                                @php
                                                    $newLinkFile = str_replace('uploads/', '', $file->link);
                                                @endphp
                                                <div class="file-item p-2 mb-2 bg-white rounded shadow-sm border">
                                                    @include('newmemo.memo.fileinfo', ['file' => $file, 'userinformation' => $userinformation])
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="comment-section mt-3 fade-in">
                                        <div class="alert alert-light border-start border-4 border-primary">
                                            <strong><i class="fas fa-comment me-2"></i> Komentar:</strong><br>
                                            <p class="mb-0 mt-1">
                                                @if (!empty($userinformation->comment))
                                                    {{ $userinformation->comment }}
                                                    <span class="badge bg-soft-primary text-primary">@ {{ $userinformation->pic }}</span>
                                                @else
                                                    <em class="text-muted">Kosong</em>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    @if (str_contains($yourrule, 'Manager') && $statussetuju != 'Approved' && $statussetuju != 'Approved by Manager' && $statussetuju != 'Rejected by Manager' && $document->seniormanagervalidation == 'Nonaktif')
                                        <div class="mt-3 p-3 bg-light rounded border fade-in">
                                            <h6 class="text-center font-weight-bold mb-3">Aksi Manager</h6>
                                            <div class="row g-2">

                                                <div class="col-12">
                                                    <form id="approveForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                                        action="{{ route('new-memo.senddecision', ['memoId' => $document->id]) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                        <input type="hidden" name="posisi" value="{{ $userinformation->id }}">
                                                        <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                        <input type="hidden" name="decision" value="Approved by Manager">
                                                        <button type="button" class="btn btn-outline-success w-100 shadow-sm"
                                                            onclick="confirmDecision('approveForm{{ $document->id }}{{ $sendtime }}')"
                                                            title="Disetujui pendapatnya">
                                                            <i class="fas fa-check me-1"></i> Setuju Pendapat
                                                        </button>
                                                    </form>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <form id="approveDirectForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                                        action="{{ route('new-memo.senddecision', ['memoId' => $document->id]) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                        <input type="hidden" name="posisi" value="{{ $userinformation->id }}">
                                                        <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                        <input type="hidden" name="decision" value="Approved">
                                                        <button type="button" class="btn btn-success w-100 shadow-sm"
                                                            onclick="confirmDecision('approveDirectForm{{ $document->id }}{{ $sendtime }}')"
                                                            title="Unit menyatakan selesai">
                                                            <i class="fas fa-check-double me-1"></i> Setuju & Selesai
                                                        </button>
                                                    </form>
                                                </div>

                                                <div class="col-12 mt-2">
                                                    <form id="rejectForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                                        action="{{ route('new-memo.senddecision', ['memoId' => $document->id]) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                        <input type="hidden" name="posisi" value="{{ $userinformation->id }}">
                                                        <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                        <input type="hidden" name="decision" value="Rejected by Manager">
                                                        <button type="button" class="btn btn-danger w-100 shadow-sm"
                                                            onclick="confirmDecision('rejectForm{{ $document->id }}{{ $sendtime }}')"
                                                            title="Tolak Pendapat">
                                                            <i class="fas fa-times me-1"></i> Tolak Pendapat
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    @elseif (str_contains($yourrule, 'Manager') && $statussetuju == 'Approved by Manager' && !isset($statussetujulist[$unit]) && $document->seniormanagervalidation == 'Nonaktif')
                                        <div class="mt-3 p-3 bg-light rounded border fade-in">
                                            <form id="approveDirectForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                                action="{{ route('new-memo.senddecision', ['memoId' => $document->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                                <input type="hidden" name="posisi" value="{{ $userinformation->id }}">
                                                <input type="hidden" name="iddocument" value="{{ $document->id }}">
                                                <input type="hidden" name="decision" value="Approved">
                                                <button type="button" class="btn btn-success w-100 shadow-sm"
                                                    onclick="confirmDecision('approveDirectForm{{ $document->id }}{{ $sendtime }}')"
                                                    title="Unit menyatakan selesai">
                                                    <i class="fas fa-check-double me-1"></i> Finalisasi: Unit Selesai
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                @endif
                            @endforeach

                            {{-- TOMBOL UPLOAD FEEDBACK --}}
                            @if ($document->operator != 'Product Engineering')
                                @if (!empty($document->unitstepverificator[$unit]['status']))
                                    @if ($document->unitstepverificator[$unit]['status'] === 'Access' && ($document->unitpicvalidation[$unit] ?? '') !== 'Aktif' && !in_array($yourrule, ['MTPR']) && ($yourrule == $unit || $yourrule == 'Manager ' . $unit))

                                        <div class="mt-4 pt-3 border-top text-center fade-in">
                                            @if ($yourrule == 'Manager ' . $unit)
                                                <a href="{{ route('new-memo.uploadmanagerfeedback', $document->id) }}"
                                                    class="btn btn-success btn-modern w-100 shadow-sm feedback-upload-btn">
                                                    <i class="fas fa-upload me-2"></i> Upload Feedback Manager {{ $unit }}
                                                </a>
                                            @else
                                                <a href="{{ route('new-memo.uploadfeedback', $document->id) }}"
                                                    class="btn btn-success btn-modern w-100 shadow-sm feedback-upload-btn">
                                                    <i class="fas fa-upload me-2"></i> Upload Feedback {{ $unit }}
                                                </a>
                                            @endif
                                            <div class="mt-2 text-muted small font-weight-bold">STATUS</div>
                                        </div>

                                    @endif
                                @endif
                            @else
                                {{-- Alternatif Logic Upload --}}
                                @if ($yourrule == 'Manager ' . $unit)
                                    <div class="mt-4 pt-3 border-top text-center fade-in">
                                        <a href="{{ route('new-memo.uploadmanagerfeedback', $document->id) }}"
                                            class="btn btn-success btn-modern w-100 shadow-sm feedback-upload-btn">
                                            <i class="fas fa-upload me-2"></i> Upload Feedback Manager {{ $unit }}
                                        </a>
                                        <div class="mt-2 text-muted small font-weight-bold">STATUS</div>
                                    </div>
                                @endif

                                @if ($document->unitpicvalidation[$unit] != 'Aktif' && !in_array($yourrule, ['MTPR']) && $yourrule == $unit)
                                    <div class="mt-4 pt-3 border-top text-center fade-in">
                                        <a href="{{ route('new-memo.uploadfeedback', $document->id) }}"
                                            class="btn btn-success btn-modern w-100 shadow-sm feedback-upload-btn">
                                            <i class="fas fa-upload me-2"></i> Upload Feedback {{ $unit }}
                                        </a>
                                        <div class="mt-2 text-muted small font-weight-bold">STATUS</div>
                                    </div>
                                @endif
                            @endif

                            {{-- SEND FORWARD (Last Step) --}}
                            @php
                                // Re-define variable inside condition to avoid undefined error if loop empty
                                if (isset($userinformation)) {
                                    $sendtime = $userinformation->created_at;
                                }
                            @endphp

                            @if (isset($sendtime) && $document->unitlaststep == $unit && $document->unitvalidation == 'Aktif' && !isset($document->lastunitsendsm))
                                @if ($yourrule == 'Manager ' . $unit)
                                    <div class="mt-3 p-3 bg-light rounded border fade-in">
                                        <form id="sendForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                            action="{{ route('new-memo.sendfoward', ['memoId' => $document->id]) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="sumberinformasi" value="{{ $userinformation }}">
                                            <input type="hidden" name="idfeedback" value="{{ $userinformation->id }}">
                                            <input type="hidden" name="documentname" value="{{ $document->documentname }}">
                                            <input type="hidden" name="project_type" value="{{ $document->project_type }}">
                                            <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                            <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                            <input type="hidden" name="decision" value="Terkirim">
                                            <input type="hidden" name="conditionoffile2" value="">

                                            <div class="form-group mb-2">
                                                <label for="level" class="fw-bold"><i class="fas fa-share me-1"></i> Send SM:</label>
                                                <select name="level" id="level_{{ $document->id }}{{ $sendtime }}"
                                                    class="form-control custom-select">
                                                    <option value="{{ $document->SMname }}">{{ $document->SMname }}</option>
                                                    @if (auth()->user()->id == 1)
                                                        <option value="Senior Manager Engineering">Senior Manager Engineering</option>
                                                        <option value="Senior Manager Desain">Senior Manager Desain</option>
                                                        <option value="Senior Manager Teknologi Produksi">Senior Manager Teknologi Produksi</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <button type="button" class="btn btn-success w-100 shadow-sm"
                                                onclick="confirmDecision('sendForm{{ $document->id }}{{ $sendtime }}')">
                                                <i class="fas fa-paper-plane me-2"></i> Langsung Kirim
                                            </button>
                                        </form>
                                    </div>
                                @elseif($yourrule == $unit)
                                    <div class="alert alert-info mt-3 fade-in">
                                        <i class="fas fa-info-circle me-1"></i> Ubah menjadi manager untuk mengirimkan ke tahap selanjutnya |
                                        Ingatkan manager anda
                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>

            @endif
        @endforeach
    @endif
@endif