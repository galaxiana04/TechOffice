@php
    $userinformations = $document->feedbacks; 
@endphp

{{-- Tutup Paksa Awal --}}
@if ($document->tutuppaksa == 'Aktif' && $document->documentstatus == 'Tertutup')
    @if (in_array($yourrule, [$document->operator, 'MTPR', 'superuser']))
        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-4">

            <div class="card card-danger card-outline shadow-lg border-0 modern-card h-100">

                <div class="card-header gradient-header bg-danger text-white"
                    style="background: linear-gradient(45deg, #dc3545, #ff6b6b);">
                    <h3 class="card-title">
                        <i class="fas fa-ban mr-3"></i>
                        Dokumen Ditutup
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
                        @if ($userinformation->conditionoffile2 == 'tutupterpaksa')

                            <div class="p-2 mb-3 border-bottom">

                                <div class="text-center mb-3 fade-in">
                                    <button class="btn btn-sm btn-outline-danger toggle-info shadow-sm"
                                        style="border-radius: 20px; padding-inline: 20px;">
                                        <i class="fas fa-info-circle me-1"></i> Detail Penutupan
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
                                                    <div class="info-icon text-danger"><i class="fas fa-clipboard-check"></i></div>
                                                    <div class="info-content">
                                                        <span class="info-label">Status Review</span>
                                                        <span class="info-value">{{ $userinformation->review ?: 'Kosong' }}</span>
                                                    </div>
                                                </div>

                                                <div class="info-item">
                                                    <div class="info-icon text-danger"><i class="fas fa-user"></i></div>
                                                    <div class="info-content">
                                                        <span class="info-label">Eksekutor</span>
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

                                                <div class="info-item">
                                                    <div class="info-icon text-danger"><i class="fas fa-tag"></i></div>
                                                    <div class="info-content">
                                                        <span class="info-label">Tipe</span>
                                                        <span
                                                            class="info-value">{{ $userinformation->conditionoffile2 ?: 'Kosong' }}</span>
                                                    </div>
                                                </div>

                                                <div class="info-item">
                                                    <div class="info-icon text-danger"><i class="fas fa-id-card"></i></div>
                                                    <div class="info-content">
                                                        <span class="info-label">ID Log</span>
                                                        <span class="info-value">{{ $userinformation->id ?: 'Kosong' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="info-grid mb-3 mt-3">
                                    @php
                                        $files = $userinformation->files;
                                        $sendtime = $userinformation->created_at;
                                        $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                                    @endphp

                                    <div class="info-item fade-in">
                                        <div class="info-icon text-danger"><i class="fas fa-clock"></i></div>
                                        <div class="info-content">
                                            <span class="info-label">Waktu Penutupan</span>
                                            <span class="info-value fw-bold text-danger">{!! $formattedTime ?? 'Kosong' !!}</span>
                                        </div>
                                    </div>

                                    <div class="info-item fade-in" style="animation-delay: 0.1s;">
                                        <div class="info-icon text-danger"><i class="fas fa-info-circle"></i></div>
                                        <div class="info-content">
                                            <span class="info-label">Status Dokumen</span>
                                            <span class="info-value">{{ $userinformation->hasilreview ?: 'Kosong' }}</span>
                                        </div>
                                    </div>

                                    <div class="info-item fade-in" style="animation-delay: 0.2s;">
                                        <div class="info-icon text-danger"><i class="fas fa-tasks"></i></div>
                                        <div class="info-content">
                                            <span class="info-label">Kondisi Akhir</span>
                                            <span class="info-value">{{ ucfirst($userinformation->conditionoffile ?: 'Kosong') }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if ($yourrule == $document->operator && $sendtime != 'tidakada')
                                    <div class="mb-3 fade-in">
                                        <form id="UnsendForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                            action="{{ route('new-memo.unsenddecision', ['memoId' => $document->id]) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="idfeedback" value="{{ $userinformation->id }}">
                                            <button type="button" class="btn btn-warning btn-sm w-100 shadow-sm"
                                                onclick="confirmDecision('UnsendForm{{ $document->id }}{{ $sendtime }}')">
                                                <i class="fas fa-undo me-2"></i> Batalkan Penutupan (Unsend)
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                @if ($files)
                                    <div class="files-section fade-in mt-3">
                                        <h6 class="section-title text-sm text-danger"><i class="fas fa-paperclip me-2"></i> Lampiran Akhir
                                        </h6>
                                        @foreach ($files as $file)
                                            <div class="file-item p-2 mb-2 bg-white rounded shadow-sm border border-danger">
                                                @include('newmemo.memo.fileinfo', ['file' => $file, 'userinformation' => $userinformation])
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="comment-section mt-3 fade-in">
                                    <div class="alert alert-danger border-start border-4 border-danger bg-soft-danger text-dark">
                                        <strong><i class="fas fa-comment-slash me-2"></i> Alasan Penutupan:</strong><br>
                                        <p class="mb-0 mt-1">
                                            @if (!empty($userinformation->comment))
                                                {{ $userinformation->comment }}
                                                <span class="badge bg-danger text-white">@ {{ $userinformation->pic }}</span>
                                            @else
                                                <em class="text-muted">Tidak ada komentar</em>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endif
{{-- Tutup Paksa Akhir --}}