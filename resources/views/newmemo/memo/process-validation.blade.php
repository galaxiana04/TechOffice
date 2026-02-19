@php
    $userinformations = $document->feedbacks; 
@endphp

{{-- ============================================================ --}}
{{-- BAGIAN 1: VALIDASI (MTPR/Superuser) --}}
{{-- ============================================================ --}}
@if (in_array($yourrule, ['MTPR', 'superuser']))
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">

        <div class="card card-primary card-outline shadow-lg border-0 modern-card h-100">

            <div class="card-header gradient-header">
                <h3 class="card-title">
                    <i class="fas fa-check-double mr-3"></i>
                    Validasi MTPR
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

            <div class="card-body modern-body">
                @foreach ($userinformations as $userinformation)
                    @if ($userinformation != '')
                        @if (in_array($userinformation->level, ['MTPR', 'superuser']))
                            @php
                                $files = $userinformation->files;
                            @endphp

                            <div class="mb-4 border-bottom pb-3">

                                <div class="text-center mb-3 fade-in">
                                    <button class="btn btn-sm btn-info toggle-info shadow-sm"
                                        style="border-radius: 20px; padding-inline: 20px;">
                                        <i class="fas fa-eye me-1"></i> Detail Validasi
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
                                                        <span class="info-value">{{ $userinformation->email ?: 'Kosong' }}</span>
                                                    </div>
                                                </div>
                                                <div class="info-item">
                                                    <div class="info-icon"><i class="fas fa-comment-dots"></i></div>
                                                    <div class="info-content">
                                                        <span class="info-label">Jenis</span>
                                                        <span
                                                            class="info-value">{{ $userinformation->conditionoffile2 ?: 'Kosong' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-grid mb-3 mt-2">
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
                                        <div class="info-icon"><i class="fas fa-tasks"></i></div>
                                        <div class="info-content">
                                            <span class="info-label">Status</span>
                                            <span class="info-value">{{ ucfirst($userinformation->conditionoffile ?: 'Kosong') }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if ($files)
                                    <div class="files-section fade-in mt-3">
                                        <h6 class="section-title text-sm"><i class="fas fa-paperclip me-2"></i> File Validasi</h6>
                                        @foreach ($files as $file)
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

                                @if ($yourrule == $document->operator && $sendtime != 'tidakada')
                                    <div class="mt-3 fade-in">
                                        <form id="UnsendForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                            action="{{ route('new-memo.unsenddecision', ['memoId' => $document->id]) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="idfeedback" value="{{ $userinformation->id }}">
                                            <button type="button" class="btn btn-warning btn-block w-100 shadow-sm"
                                                onclick="confirmDecision('UnsendForm{{ $document->id }}{{ $sendtime }}')">
                                                <i class="fas fa-undo me-2"></i> Unsend Semua
                                            </button>
                                        </form>
                                    </div>
                                @endif

                            </div>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif


{{-- ============================================================ --}}
{{-- BAGIAN 2: LOGISTIK (MTPR/Superuser/Logistik) --}}
{{-- ============================================================ --}}
@if (in_array($yourrule, ['MTPR', 'superuser', 'Logistik']))
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">

        <div class="card card-info card-outline shadow-lg border-0 modern-card h-100">

            <div class="card-header gradient-header bg-info text-white"
                style="background: linear-gradient(45deg, #17a2b8, #36b9cc);">
                <h3 class="card-title">
                    <i class="fas fa-truck mr-3"></i>
                    Logistik
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

            <div class="card-body modern-body">
                @php
                    $userinformations = $document->feedbacks;
                @endphp

                @foreach ($userinformations as $userinformation)
                    @if ($userinformation != '')
                        @if (in_array($userinformation->level, ['Logistik']))
                            @php
                                $files = $userinformation->files;
                            @endphp

                            <div class="mb-4 border-bottom pb-3">

                                <div class="text-center mb-3 fade-in">
                                    <button class="btn btn-sm btn-info toggle-info shadow-sm"
                                        style="border-radius: 20px; padding-inline: 20px;">
                                        <i class="fas fa-eye me-1"></i> Detail Logistik
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
                                                        <span class="info-label">Review</span>
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
                                                        <span
                                                            class="info-value text-break">{{ $userinformation->email ?: 'Kosong' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="info-grid mb-3 mt-2">
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
                                        <div class="info-icon"><i class="fas fa-tasks"></i></div>
                                        <div class="info-content">
                                            <span class="info-label">Status</span>
                                            <span class="info-value">{{ ucfirst($userinformation->conditionoffile ?: 'Kosong') }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if ($files)
                                    <div class="files-section fade-in mt-3">
                                        <h6 class="section-title text-sm"><i class="fas fa-paperclip me-2"></i> File Logistik</h6>
                                        @foreach ($files as $file)
                                            <div class="file-item p-2 mb-2 bg-white rounded shadow-sm border">
                                                @include('newmemo.memo.fileinfo', ['file' => $file, 'userinformation' => $userinformation])
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="comment-section mt-3 fade-in">
                                    <div class="alert alert-light border-start border-4 border-info">
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

                                @if ($yourrule == $document->operator && $sendtime != 'tidakada')
                                    <div class="mt-3 fade-in">
                                        <form id="UnsendForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                            action="{{ route('new-memo.unsenddecision', ['memoId' => $document->id]) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="idfeedback" value="{{ $userinformation->id }}">
                                            <button type="button" class="btn btn-warning btn-block w-100 shadow-sm"
                                                onclick="confirmDecision('UnsendForm{{ $document->id }}{{ $sendtime }}')">
                                                <i class="fas fa-undo me-2"></i> Unsend Semua
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                @if ($document->seniormanagervalidation == 'Aktif' && $document->MTPRvalidation != 'Aktif')
                                    @if ($document->MTPRbeforeLogistik != 'Aktif')
                                        <div class="mt-3 p-3 bg-light rounded border fade-in">
                                            <h6 class="text-sm font-weight-bold mb-2"><i class="fas fa-paper-plane me-1"></i> Tindakan Lanjutan:
                                            </h6>

                                            <form id="sendForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                                action="{{ route('new-memo.sendfoward', ['memoId' => $document->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="idfeedback" value="{{ $userinformation->id }}">
                                                <input type="hidden" name="documentname" value="{{ $document->documentname }}">
                                                <input type="hidden" name="project_type" value="{{ $document->project_type }}">
                                                <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                                <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                <input type="hidden" name="decision" value="Dokumen ditutup">

                                                <div class="form-group mb-2">
                                                    <select name="level" id="level" class="form-control custom-select">
                                                        @if ($document->is_expand_to_logistic)
                                                            <option value="Logistik">Logistik</option>
                                                        @else
                                                            <option value="selesai">Selesai (Tutup Dokumen)</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <button type="button" class="btn btn-success w-100 shadow-sm"
                                                    onclick="confirmDecision('sendForm{{ $document->id }}{{ $sendtime }}')">
                                                    <i class="fas fa-check-circle me-2"></i> Proses
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endif

                            </div>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endif
{{-- Logistik Akhir --}}