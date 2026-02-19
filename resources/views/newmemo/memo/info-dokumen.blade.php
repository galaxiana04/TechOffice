<div class="card card-primary card-outline shadow-lg border-0 modern-card">
    <div class="card">
        <div class="card-header gradient-header">
            <h1 class="card-title">
                <i class="fas fa-file-alt mr-3"></i>
                Informasi Dokumen
            </h1>
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
            <!-- Document Info Grid -->
            <div class="info-grid">
                <div class="info-item fade-in">
                    <div class="info-icon">
                        <i class="fas fa-hashtag"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Nomor Dokumen</span>
                        <span class="info-value">{{ $document->documentnumber }}</span>
                    </div>
                </div>

                <div class="info-item fade-in" style="animation-delay: 0.1s;">
                    <div class="info-icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Nama Dokumen</span>
                        <span class="info-value">{{ $document->documentname }}</span>
                    </div>
                </div>

                <div class="info-item fade-in" style="animation-delay: 0.2s;">
                    <div class="info-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Kategori</span>
                        <span class="info-value">{{ $document->category }}</span>
                    </div>
                </div>

                <div class="info-item fade-in" style="animation-delay: 0.3s;">
                    <div class="info-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Tipe Proyek</span>
                        <span class="info-value">{{ $projectname }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="status-container fade-in" style="animation-delay: 0.4s;">
                <span class="status-label">
                    <i class="fas fa-circle-info mr-3"></i>
                    Status Dokumen
                </span>
                <span class="status-badge-modern" id="statusBadge"></span>
            </div>

            <script>
                const statusBadge = document.getElementById('statusBadge');
                const documentStatus = '{{ $document->documentstatus }}';

                if (documentStatus.toLowerCase() === 'terbuka') {
                    statusBadge.innerHTML = '<i class="fas fa-lock-open me-1"></i> Terbuka';
                    statusBadge.classList.add('badge-terbuka-modern');
                } else {
                    statusBadge.innerHTML = '<i class="fas fa-lock me-1"></i> Tertutup';
                    statusBadge.classList.add('badge-tertutup-modern');
                }
            </script>

            <!-- Additional Info -->
            <div class="additional-info fade-in" style="animation-delay: 0.5s;">
                @if ($document->memoorigin)
                    <div class="info-row">
                        <i class="fas fa-paper-plane"></i>
                        <span><strong>Asal Memo:</strong> {{ $document->memoorigin }}</span>
                    </div>
                @else
                    <div class="info-row text-muted">
                        <i class="fas fa-paper-plane"></i>
                        <span><strong>Asal Memo:</strong> MTPR belum menentukan asal memo</span>
                    </div>
                @endif

                @if ($document->operator)
                    <div class="info-row">
                        <i class="fas fa-user-tie"></i>
                        <span><strong>Distributor Dokumen:</strong> {{ $document->operator }}</span>
                    </div>
                @else
                    <div class="info-row text-muted">
                        <i class="fas fa-user-tie"></i>
                        <span><strong>Distributor Dokumen:</strong> Belum menentukan distributor dokumen</span>
                    </div>
                @endif

                @php
                    $dasarinformasi = $document->feedbacks;
                @endphp
                @foreach ($dasarinformasi as $i => $userinformation)
                    @if ($userinformation != '' && $document->operatorsignature != 'Aktif')
                        @if ($userinformation->pic == 'MTPR' && $userinformation->level == 'pembukadokumen')
                            @php
                                $files = $userinformation->files;
                                $jumlahLampiran = count($files);
                            @endphp
                        @endif
                    @else
                        @php
                            $files = $userinformation->files;
                        @endphp
                        @if ($files)
                            @php
                                $jumlahLampiran = count($files);
                            @endphp
                        @endif
                    @endif
                @endforeach

                @if (isset($jumlahLampiran))
                    <div class="info-row">
                        <i class="fas fa-paperclip"></i>
                        <span><strong>Jumlah Lampiran:</strong> 
                            <span class="badge bg-info">{{ $jumlahLampiran }}</span>
                        </span>
                    </div>
                @endif

                @if (isset(json_decode($document->timeline)->documentopened))
                    @php
                        $sendtime = $userinformation->created_at;
                        $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                    @endphp
                    <div class="info-row">
                        <i class="fas fa-calendar-check"></i>
                        <span><strong>Tanggal Terbit Memo:</strong> {{ $formattedTime }}</span>
                    </div>
                @else
                    <div class="info-row text-muted">
                        <i class="fas fa-calendar-check"></i>
                        <span><strong>Tanggal Terbit Memo:</strong> Belum Terbit</span>
                    </div>
                @endif

                @if ($document->memokind)
                    <div class="info-row">
                        <i class="fas fa-bookmark"></i>
                        <span><strong>Kategori Memo:</strong> {{ $document->memokind }}</span>
                    </div>
                @else
                    <div class="info-row text-muted">
                        <i class="fas fa-bookmark"></i>
                        <span><strong>Kategori Memo:</strong> {{ $document->operator }} belum menentukan kategori memo</span>
                    </div>
                @endif
            </div>

            <!-- Komat Table -->
            @php
                $komats = $document->komats;
            @endphp
            @if (isset($komats))
                <div class="komat-section fade-in" style="animation-delay: 0.6s;">
                    <h5 class="section-title">
                        <i class="fas fa-boxes mr-3"></i>
                        Informasi Komat
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover modern-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-cube me-1"></i> Komponen</th>
                                    <th><i class="fas fa-barcode me-1"></i> Kode Material</th>
                                    <th><i class="fas fa-truck me-1"></i> Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($komats as $komat)
                                    @php
                                        $komponen = $komat->material;
                                        $kodematerial = $komat->kodematerial;
                                        $supplier = $komat->supplier;
                                    @endphp
                                    <tr>
                                        <td>{{ $komponen }}</td>
                                        <td><code>{{ $kodematerial }}</code></td>
                                        <td>{{ $supplier }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Timeline Info -->
            @php
                $timeline = json_decode($document->timeline, true);
            @endphp
            <div class="timeline-section fade-in" style="animation-delay: 0.7s;">
                <h5 class="section-title">
                    <i class="fas fa-history mr-3"></i>
                    Timeline Dokumen
                </h5>
                
                @if (isset($timeline['documentshared']))
                    @php
                        $sendtime = $userinformation->created_at;
                        $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                    @endphp
                    <div class="timeline-item">
                        <i class="fas fa-share-alt"></i>
                        <span><strong>Disebarkan:</strong> {{ $formattedTime }}</span>
                    </div>
                @else
                    <div class="timeline-item text-muted">
                        <i class="fas fa-share-alt"></i>
                        <span><strong>Disebarkan:</strong> Belum disebarkan</span>
                    </div>
                @endif

                @if (isset($timeline['documentclosed']))
                    @php
                        $sendtime = $userinformation->created_at;
                        $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                    @endphp
                    <div class="timeline-item">
                        <i class="fas fa-check-circle"></i>
                        <span><strong>Ditutup:</strong> {{ $formattedTime }}</span>
                    </div>
                @else
                    <div class="timeline-item text-muted">
                        <i class="fas fa-check-circle"></i>
                        <span><strong>Ditutup:</strong> Belum ditutup</span>
                    </div>
                @endif
            </div>

            <!-- PIC Project -->
            @if ($yourrule === $document->operator)
                <div class="pic-section fade-in" style="animation-delay: 0.8s;">
                    <h5 class="section-title">
                        <i class="fas fa-users mr-3"></i>
                        PIC Proyek
                    </h5>
                    <div class="pic-links">
                        @if (isset($document->project_pic))
                            @foreach (json_decode($document->project_pic) as $pic)
                                <a href="{{ url('/mail') }}?namafile={{ urlencode($document->documentname) }}&namaproject={{ $document->project_type }}&iddocument={{ $document->id }}&namadivisi={{ $pic }}&notificationcategory={{ $document->category }}" 
                                   class="pic-badge">
                                    <i class="fas fa-user me-1"></i>
                                    {{ $pic }}
                                </a>
                            @endforeach
                        @else
                            <span class="text-muted">Tidak ada PIC proyek tersedia</span>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Files Section -->
            @foreach ($dasarinformasi as $userinformation)
                @if ($userinformation != '')
                    @if ($userinformation->pic == 'MTPR' && $userinformation->level == 'pembukadokumen')
                        @php
                            $files = $userinformation->files;
                        @endphp
                        @if ($files && $document->operatorsignature != 'Aktif')
                            <div class="files-section fade-in" style="animation-delay: 0.9s;">
                                <h5 class="section-title">
                                    <i class="fas fa-file-contract mr-3"></i>
                                    File dengan Kolom TTD
                                </h5>
                                @foreach ($files as $file)
                                    <div class="file-item">
                                        @include('newmemo.memo.fileinfo', [
                                            'file' => $file,
                                            'userinformation' => $userinformation,
                                        ])
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                @endif
            @endforeach

            @foreach ($dasarinformasi as $userinformation)
                @if ($userinformation != '')
                    @if ($userinformation->pic == $document->operator && $userinformation->level == 'signature')
                        @php
                            $files = $userinformation->files;
                        @endphp
                        @if ($files)
                            <div class="files-section fade-in" style="animation-delay: 0.9s;">
                                <h5 class="section-title">
                                    <i class="fas fa-file-contract mr-3"></i>
                                    File dengan Kolom TTD
                                </h5>
                                @foreach ($files as $file)
                                    <div class="file-item">
                                        @include('newmemo.memo.fileinfo', [
                                            'file' => $file,
                                            'userinformation' => $userinformation,
                                        ])
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                @endif
            @endforeach

            <!-- Action Buttons -->
            <div class="action-buttons fade-in" style="animation-delay: 1s;">
                @if (($yourrule === $document->operator || $yourrule === 'MTPR') && $document->documentstatus === 'Terbuka')
                    @if ($yourrule === $document->operator && $document->operatorsignature == 'Aktif')
                        <a href="{{ route('new-memo.edit', $document->id) }}" class="btn btn-modern btn-warning">
                            <i class="fas fa-edit me-2"></i>
                            Edit Dokumen
                        </a>
                    @else
                        @if ($document->MTPRsend == 'Aktif' && $document->operator == null)
                            <a href="{{ route('new-memo.chooseoperator', $document->id) }}" class="btn btn-modern btn-primary">
                                <i class="fas fa-user-check me-2"></i>
                                Pilih Operator
                            </a>
                        @else
                            @if ($yourrule === $document->operator)
                                <a href="{{ route('new-memo.uploadsignature', $document->id) }}" class="btn btn-modern btn-success">
                                    <i class="fas fa-signature me-2"></i>
                                    Upload Signature
                                </a>
                            @endif
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

