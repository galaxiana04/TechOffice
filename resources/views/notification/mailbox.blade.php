@extends('layouts.mail')

@section('container2')
    <title>Mailbox - Daftar Notifikasi</title>
@endsection

@section('container3')
    <style>
        /* --- 1. MODERN TABLE STYLING --- */
        .table-modern {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0 8px; /* Jarak antar baris */
        }

        .table-modern thead th {
            border: none;
            background-color: #f1f3f5;
            color: #6c757d;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 12px 15px;
            font-weight: 700;
        }

        .table-modern tbody tr {
            background-color: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            z-index: 10;
            position: relative;
        }

        .table-modern td {
            border: none;
            padding: 15px;
            vertical-align: middle !important; /* Pastikan selalu di tengah vertikal */
            font-size: 0.9rem;
            border-top: 1px solid transparent; /* Fix border glitch */
        }

        /* Radius sudut untuk efek kartu */
        .table-modern td:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .table-modern td:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        /* --- 2. CUSTOM DATATABLES STYLING --- */
        /* Search Box */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 20px;
            padding: 5px 15px;
            outline: none;
            transition: border-color 0.3s;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Pagination Buttons */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 50px !important;
            padding: 4px 12px !important;
            margin: 0 2px !important;
            border: 1px solid transparent !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #007bff !important;
            color: #fff !important;
            border: none;
            box-shadow: 0 2px 4px rgba(0,123,255,0.3);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef !important;
            color: #495057 !important;
            border: 1px solid #dee2e6 !important;
        }

        /* --- 3. BADGES & TYPOGRAPHY --- */
        .badge-soft {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
        }
        .badge-soft-success { background-color: #d1e7dd; color: #0f5132; }
        .badge-soft-secondary { background-color: #e2e3e5; color: #41464b; }
        .badge-soft-warning { background-color: #fff3cd; color: #664d03; }
        .badge-soft-danger { background-color: #f8d7da; color: #842029; }
        
        .meta-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #6c757d;
            margin-right: 5px;
            display: inline-flex;
            align-items: center;
        }

        /* --- 4. RESPONSIVE MOBILE VIEW (Card Layout) --- */
        @media screen and (max-width: 992px) {
            .table-modern thead { display: none; }
            
            .table-modern tbody, .table-modern tr, .table-modern td {
                display: block;
                width: 100%;
            }

            .table-modern tr {
                margin-bottom: 20px;
                border: 1px solid #eee;
                border-radius: 12px;
                padding: 10px;
            }

            .table-modern td {
                padding: 10px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #f1f1f1;
                text-align: right;
            }
            .table-modern td:last-child { border-bottom: none; }
            
            /* Label untuk mode mobile */
            .table-modern td::before {
                content: attr(data-label);
                font-weight: 700;
                color: #adb5bd;
                text-transform: uppercase;
                font-size: 0.7rem;
                margin-right: 15px;
                text-align: left;
            }
            
            /* Tombol full width di mobile */
            .td-actions { flex-direction: column; gap: 5px; }
            .td-actions .btn { width: 100%; display: block; }
            
            /* Penyesuaian Detail Dokumen di Mobile */
            .td-detail div { text-align: right !important; align-items: flex-end !important; }
        }
    </style>

    <div class="card shadow-sm border-0 bg-transparent">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="example2" class="table table-modern">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="10%">Jenis</th>
                            <th width="40%">Detail Dokumen</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                            <th class="text-center">Notif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifs as $notif)
                            {{-- LOGIC EXTRACTION: Membersihkan View --}}
                            @php
                                $doc = $notif->notifmessage;
                                $type = 'N/A';
                                $projectTitle = '-';
                                $docNumber = '-';
                                $docName = 'Dokumen Tidak Tersedia';
                                $docStatus = 'N/A';
                                $detailRoute = '#';

                                // Mapping Tipe Dokumen
                                if ($doc instanceof \App\Models\NewMemo) {
                                    $type = 'Memo';
                                    $projectTitle = optional($notif->memo)->projectType->title ?? '-';
                                    $docNumber = $doc->documentnumber;
                                    $docName = $doc->documentname;
                                    $docStatus = optional($notif->memo)->documentstatus;
                                    $detailRoute = route('new-memo.show', ['memoId' => $notif->notifmessage_id, 'rule' => auth()->user()->rule]);
                                } elseif ($doc instanceof \App\Models\JustiMemo) {
                                    $type = 'Justifikasi';
                                    $projectTitle = optional($notif->justimemo)->projectType->title ?? '-';
                                    $docNumber = $doc->documentnumber;
                                    $docName = $doc->documentname;
                                    $docStatus = optional($notif->justimemo)->documentstatus;
                                    $detailRoute = route('justi-memo.show', ['memoId' => $notif->notifmessage_id, 'rule' => auth()->user()->rule]);
                                } elseif ($doc instanceof \App\Models\MemoSekdiv) {
                                    $type = 'Memo Sekdiv';
                                    $projectTitle = optional($notif->memosekdiv)->projectType->title ?? '-';
                                    $docNumber = $doc->documentnumber;
                                    $docName = $doc->documentname;
                                    $docStatus = optional($notif->memosekdiv)->documentstatus;
                                    $detailRoute = route('memosekdivs.show', ['id' => $notif->notifmessage_id, 'rule' => auth()->user()->rule]);
                                } elseif ($doc instanceof \App\Models\RamsDocument) {
                                    $type = 'RAMS Doc';
                                    $projectTitle = optional($notif->ramsdocument)->proyek_type ?? '-';
                                    $docNumber = $doc->documentnumber;
                                    $docName = $doc->documentname;
                                    $detailRoute = route('ramsdocuments.show', ['document' => $notif->notifmessage_id, 'rule' => auth()->user()->rule]);
                                }

                                // Warna Kategori Notifikasi
                                $notifBadgeClass = 'badge-soft-secondary';
                                if ($notif->notifmessage_type == 'App\Models\NewMemo') $notifBadgeClass = 'badge-soft-warning'; 
                                elseif ($notif->notifmessage_type == 'App\Models\allert1') $notifBadgeClass = 'badge-soft-warning';
                                elseif ($notif->notifmessage_type == 'App\Models\allert2') $notifBadgeClass = 'badge-soft-danger';
                                
                                // Warna Status Dokumen
                                $statusBadgeClass = (in_array(strtolower($docStatus), ['terbuka', 'open'])) ? 'badge-soft-success' : 'badge-soft-secondary';
                            @endphp

                            <tr>
                                <td data-label="No" class="text-center font-weight-bold text-muted">
                                    {{ $loop->iteration }}
                                </td>
                                
                                <td data-label="Jenis">
                                    <span class="font-weight-bold text-primary">{{ $type }}</span>
                                </td>

                                <td data-label="Detail Dokumen" class="td-detail">
                                    <div class="d-flex flex-column justify-content-center text-left">
                                        {{-- Judul Dokumen (Bold & Dark) --}}
                                        <span class="text-dark font-weight-bold mb-2" style="font-size: 0.95rem; line-height: 1.3;">
                                            {{ Str::limit($docName, 70) }}
                                        </span>
                                        
                                        {{-- Metadata (Project & No Surat) dengan Badge --}}
                                        <div class="d-flex flex-wrap" style="gap: 5px;">
                                            <span class="meta-badge" title="Nama Project">
                                                <i class="fas fa-building mr-1"></i> {{ Str::limit($projectTitle, 20) }}
                                            </span>
                                            <span class="meta-badge" title="Nomor Surat">
                                                <i class="fas fa-hashtag mr-1"></i> {{ Str::limit($docNumber, 20) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td data-label="Tanggal" class="text-center">
                                    <span class="text-muted font-weight-bold" style="font-size: 0.85rem;">
                                        {{ $notif->created_at->format('d M Y') }}
                                    </span>
                                </td>

                                <td data-label="Status Dokumen" class="text-center">
                                    <span class="badge {{ $statusBadgeClass }}">
                                        {{ ucfirst($docStatus ?? 'N/A') }}
                                    </span>
                                </td>

                                <td data-label="Aksi" class="td-actions text-center">
                                    <a href="{{ $detailRoute }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm font-weight-bold">
                                        <i class="fas fa-eye mr-1"></i> Buka
                                    </a>
                                </td>

                                <td data-label="Notifikasi" class="text-center">
                                    <div class="d-flex align-items-center justify-content-end justify-content-lg-center">
                                        <div class="mr-2">
                                            @if ($notif->status == 'read')
                                                <i class="fas fa-envelope-open text-success" title="Sudah Dibaca"></i>
                                            @else
                                                <i class="fas fa-envelope text-warning" title="Belum Dibaca"></i>
                                            @endif
                                        </div>
                                        <span class="badge {{ $notifBadgeClass }}">
                                            {{ ucfirst($notif->notificationcategory) }}
                                        </span>
                                    </div>
                                    <small class="d-block text-muted mt-1" style="font-size: 0.65rem;">ID: {{ $notif->id }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        // Logika Dropdown Status
        document.querySelectorAll('select[id^="status-"]').forEach(select => {
            select.addEventListener('change', function() {
                var notifId = this.id.split('-')[1];
                var alasanContainer = document.getElementById('alasan-container-' + notifId);
                if(alasanContainer) {
                    if (this.value === 'Tolak') {
                        alasanContainer.style.display = 'block';
                    } else {
                        alasanContainer.style.display = 'none';
                    }
                }
            });
        });

        function submitAnswer(namaFile, namaProject, idDocument, namaDivisi, notificationcategory, notifId) {
            Swal.fire({
                title: 'Konfirmasi Perubahan',
                text: "Anda yakin ingin mengubah status dokumen ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    var statusEl = document.getElementById('status-' + notifId);
                    var alasanEl = document.getElementById('alasan-' + notifId);
                    
                    var status = statusEl ? statusEl.value : '';
                    var alasan = alasanEl ? alasanEl.value : '';

                    var url = "{{ url('/mail') }}?namafile=" + encodeURIComponent(namaFile) +
                        "&namaproject=" + encodeURIComponent(namaProject) +
                        "&iddocument=" + encodeURIComponent(idDocument) +
                        "&namadivisi=" + encodeURIComponent(namaDivisi) +
                        "&status=" + encodeURIComponent(status) +
                        "&alasan=" + encodeURIComponent(alasan) +
                        "&notificationcategory=" + encodeURIComponent(notificationcategory);

                    window.location.href = url;
                }
            });
        }
    </script>
@endsection