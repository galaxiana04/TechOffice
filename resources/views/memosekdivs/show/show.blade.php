@extends('layouts.split3')


@section('container1')
    {{-- Dokumen informasi Awal --}}

    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <div class="info-box-content">
                <div class="card">
                    <div class="card-header">
                        <h1 class="card-title">Informasi Dokumen:</h1>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><strong>Nomor Dokumen:</strong> {{ $document->documentnumber }}</p>
                        <p class="card-text"><strong>Nama Dokumen:</strong> {{ $document->documentname }}</p>
                        <p class="card-text"><strong>Tipe Proyek:</strong> {{ $projectname }}</p>
                        <p>
                        <p class="card-text"><strong>Jenis Dokumen:</strong> {{ $document->documentkind }}</p>
                        <p>
                        <div class="card-badge mt-2">
                            <span class="status-text"><strong>Status Dokumen:</strong></span>
                            <span class="status-badge" id="statusBadge"></span>
                        </div>






                        </p>



                        <script>
                            const statusBadge = document.getElementById('statusBadge');
                            const documentStatus = '{{ $document->documentstatus }}';

                            // Atur warna dan isi teks pada badge berdasarkan status dokumen
                            if (documentStatus.toLowerCase() === 'terbuka') {
                                statusBadge.textContent = 'Terbuka';
                                statusBadge.classList.add('badge-terbuka');
                            } else {
                                statusBadge.textContent = 'Tertutup';
                                statusBadge.classList.add('badge-tertutup');
                            }
                        </script>

                        @php
                            $dasarinformasi = $document->feedbacks;
                        @endphp
                        @foreach ($dasarinformasi as $i => $userinformation)
                            @if ($userinformation != '' && $document->operatorsignature != 'Aktif')
                                @if ($userinformation->pic == 'MTPR' && $userinformation->level == 'pembukadokumen')
                                    {{-- Bagian Loop File --}}
                                    @php
                                        $files = $userinformation->files;
                                        $jumlahLampiran = count($files); // Menghitung jumlah lampiran
                                    @endphp
                                @endif
                            @else
                                @php
                                    $files = $userinformation->files;
                                @endphp
                                @if ($files)
                                    @php
                                        $jumlahLampiran = count($files); // Menghitung jumlah lampiran
                                    @endphp
                                @endif
                            @endif
                        @endforeach
                        @if (isset($jumlahLampiran))
                            <p class="card-text"><strong>Jumlah Lampiran:</strong> {{ $jumlahLampiran }}</p>
                        @endif
                        @if (isset(json_decode($document->timeline)->documentopened))
                            @php
                                $sendtime = $userinformation->created_at;
                                $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                            @endphp
                            <p class="card-text"><strong>Tanggal Terbit Memo:</strong> {{ $formattedTime }}</p>
                        @else
                            <p class="card-text"><strong>Tanggal Terbit Memo:</strong> Belum Terbit</p>
                        @endif

                        @php
                            $komats = $document->komats;
                        @endphp

                        @php
                            $timeline = json_decode($document->timeline, true);
                        @endphp

                        @if (isset($timeline['documentshared']))
                            @php
                                $sendtime = $userinformation->created_at;
                                $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                            @endphp
                            <p class="card-text"><strong>Waktu Dokumen disebarkan:</strong> {{ $formattedTime }}</p>
                        @else
                            <p class="card-text"><strong>Waktu Dokumen disebarkan:</strong> Belum disebarkan</p>
                        @endif

                        @if (isset($timeline['documentclosed']))
                            @php
                                $sendtime = $userinformation->created_at;
                                $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                            @endphp
                            <p class="card-text"><strong>Waktu Dokumen ditutup:</strong> {{ $formattedTime }}</p>
                        @else
                            <p class="card-text"><strong>Waktu Dokumen ditutup:</strong> Belum ditutup</p>
                        @endif
                        @if ($yourrule === $document->operator)
                            <p class="card-text"><strong>PIC Proyek:</strong>
                                @if (isset($document->project_pic))
                                    @foreach (json_decode($document->project_pic) as $pic)
                                        <a
                                            href="{{ url('/mail') }}?namafile={{ urlencode($document->documentname) }}&namaproject={{ $document->project_type }}&iddocument={{ $document->id }}&namadivisi={{ $pic }}&notificationcategory={{ $document->category }}">{{ $pic }}</a>
                                    @endforeach
                                @else
                                    Tidak ada PIC proyek tersedia
                                @endif
                            </p>
                        @endif
                        @foreach ($dasarinformasi as $userinformation)
                            @if ($userinformation != '')
                                @if ($userinformation->pic == 'MTPR' && $userinformation->level == 'pembukadokumen')
                                    {{-- Bagian Loop File --}}
                                    @php
                                        $uniqueFiles = []; // Array untuk menyimpan file yang unik
                                        $files = $userinformation->files;
                                    @endphp
                                    @if ($files && $document->operatorsignature != 'Aktif')
                                        <p class="card-text"><strong>File dengan Kolom TTD:</strong>
                                            @foreach ($files as $file)
                                                @php
                                                    $newLinkFile = str_replace('uploads/', '', $file->link);
                                                @endphp
                                                <div class="card-text mt-2">
                                                    @include('newmemo.memo.fileinfo', [
                                                        'file' => $file,
                                                        'userinformation' => $userinformation,
                                                    ])

                                                </div>
                                            @endforeach
                                        </p>
                                    @endif
                                    @php
                                        $files = $userinformation->files;
                                    @endphp
                                @endif
                            @endif
                        @endforeach
                        @foreach ($dasarinformasi as $userinformation)
                            @if ($userinformation != '')
                                @if ($userinformation->pic == $document->operator && $userinformation->level == 'signature')
                                    {{-- Bagian Loop File --}}
                                    @php
                                        $uniqueFiles = []; // Array untuk menyimpan file yang unik
                                        $files = $userinformation->files;
                                    @endphp
                                    @if ($files)
                                        <p class="card-text"><strong>File dengan Kolom TTD:</strong>
                                            @foreach ($files as $file)
                                                @php
                                                    $newLinkFile = str_replace('uploads/', '', $file->link);
                                                @endphp
                                                <div class="card-text mt-2">
                                                    @include('newmemo.memo.fileinfo', [
                                                        'file' => $file,
                                                        'userinformation' => $userinformation,
                                                    ])

                                                </div>
                                            @endforeach
                                        </p>
                                    @endif
                                @endif
                            @endif
                        @endforeach



                        <div class="card mb-3">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <strong class="mb-0">Kepemilikan Akses</strong>
                                @if ($hasAccess)
                                    <button class="btn btn-sm btn-primary ml-auto"
                                        onclick="showAddAccessModal({{ $document->id }}, {{ json_encode($users->toArray()) }}, {{ json_encode($document->memoSekdivAccesses->pluck('user_id')->toArray()) }})">
                                        Add Access
                                    </button>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div id="permissionlist" class="mb-2">
                                        {!! $document->memoSekdivAccesses->pluck('user.name')->filter()->implode(', ') ?: '<em>Tidak ada pengguna</em>' !!}
                                    </div>


                                </div>
                            </div>
                        </div>
                        <div>

                            @if (in_array($yourrule, $sminvolved) && $document->documentstatus === 'open')
                                <a href="{{ route('memosekdivs.edit', $document->id) }}"
                                    class="btn btn-warning btn-sm">Edit
                                    Dokumen</a>
                            @endif
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Dokumen informasi Akhir --}}
@endsection

@section('container2')

    {{-- Feedback Unit Awal --}}
    @if ($document->projectpics)
        @php
            $project_pic = $document->projectpics;
            $manager_project_pic = [];
            foreach ($project_pic as $unittunggal) {
                $manager_project_pic[] = 'Manager ' . $unittunggal;
            }
        @endphp
        @if (in_array($yourrule, $manager_project_pic) ||
                in_array($yourrule, $project_pic) ||
                in_array($yourrule, ['superuser', $document->operator, 'MTPR']))
            @foreach ($project_pic as $unit)
                @php
                    $managerunit = 'Manager ' . $unit;
                @endphp
                @if (in_array($yourrule, $manager_project_pic) || in_array($yourrule, $project_pic) || $yourrule == 'MTPR')
                    <div class="col-md-3 col-sm-6 col-12">
                        <div class="info-box">
                            <div class="info-box-content">
                                <!-- MULTI CHARTS -->
                                <div class="card">
                                    <div class="card-header">
                                        <h1 class="card-title">Feedback {{ $unit }}</h1>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                                title="Collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove"
                                                title="Remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @php
                                        $userinformations = $document->feedbacks;
                                    @endphp
                                    <div class="card-body">
                                        @foreach ($userinformations as $userinformation)
                                            @if ($userinformation->level == $unit && $userinformation->condition2 == 'feedback')
                                                @php
                                                    $statussetuju = $userinformation->condition1;
                                                    if ($statussetuju == 'Approved') {
                                                        $statussetujulist[$unit] = $statussetuju;
                                                    }

                                                @endphp
                                            @endif
                                        @endforeach

                                        @foreach ($userinformations as $userinformation)
                                            @if ($userinformation->level == $unit && $userinformation->condition2 == 'feedback')
                                                <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                                <p>
                                                <div class="card mt-3">
                                                    <div class="info-container mt-2" style="display: none;">
                                                        <div class="card-body">
                                                            <h5 class="card-title"></h5>
                                                            <ul class="list-group list-group-flush">
                                                                <li class="list-group-item">
                                                                    @if ($userinformation->level == $yourrule)
                                                                        <button class="btn"
                                                                            style="background-color: orange;">
                                                                            <strong>Status: Penerima dari:</strong>
                                                                            {{ $userinformation->level ?? 'hanya upload & tidak dikirim' }}
                                                                        </button>
                                                                    @elseif ($userinformation->level == '')
                                                                        <button class="btn"
                                                                            style="background-color: yellow;">
                                                                            <strong>Upload Pribadi</strong>
                                                                        </button>
                                                                    @else
                                                                        <button class="btn"
                                                                            style="background-color: red;">
                                                                            <strong>Status: Terkirim ke:</strong>
                                                                            {{ $userinformation->level ?? 'hanya upload & tidak dikirim' }}
                                                                        </button>
                                                                    @endif
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Apakah anda sudah melakukan review atas dokumen
                                                                        approval?</strong>
                                                                    {{ $userinformation->review ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Nama Penulis:</strong>
                                                                    {{ $userinformation->author ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Email:</strong>
                                                                    {{ $userinformation->email ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Apakah dokumen sudah dibaca?</strong>
                                                                    {{ $userinformation->isread ? 'Sudah dibaca' : 'Belum dibaca' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>Jenis Comment:</strong>
                                                                    {{ $userinformation->condition2 ?: 'Kosong' }}
                                                                </li>
                                                                <li class="list-group-item">
                                                                    <strong>ID Comment:</strong>
                                                                    {{ $userinformation->id ?: 'Kosong' }}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                </p>
                                                <li class="list-group-item">
                                                    <strong>Waktu:</strong>
                                                    @php
                                                        $sendtime = $userinformation->created_at;
                                                        $formattedTime = $userinformation->created_at->format(
                                                            'Y-m-d H:i:s',
                                                        );
                                                    @endphp
                                                    {!! $formattedTime ?? 'Kosong' !!}
                                                    @if ($yourrule == $document->operator)
                                                        <div class="col-md-6">
                                                            <form
                                                                id="deleteFeedbackForm{{ $document->id }}{{ $sendtime }}"
                                                                method="POST"
                                                                action="{{ route('new-memo.deletedfeedbackdecision', ['id' => $document->id]) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="posisi"
                                                                    value="{{ $userinformation->id }}">
                                                                <button type="button" class="btn btn-warning mt-2"
                                                                    onclick="confirmDecision('deleteFeedbackForm{{ $document->id }}{{ $sendtime }}')">Delete
                                                                    Feedback</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Status Dokumen:</strong>
                                                    {{ $userinformation->reviewresult ?: 'Kosong' }}
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Status:</strong>
                                                    {{ ucfirst($userinformation->condition1 ?: 'Kosong') }}
                                                    @php
                                                        $statussetuju = $userinformation->condition1;
                                                        $files = $userinformation->files;
                                                    @endphp
                                                </li>
                                                @if ($files)
                                                    <div class="card feedback-item">
                                                        <div class="card-text-item">
                                                            <strong>File:</strong>
                                                        </div>
                                                        <p class="card-text"><strong>File:</strong>
                                                            @foreach ($userinformation->files as $file)
                                                                @php
                                                                    $newLinkFile = str_replace(
                                                                        'uploads/',
                                                                        '',
                                                                        $file->link,
                                                                    );
                                                                @endphp
                                                                <div class="card-text mt-2">
                                                                    @include('newmemo.memo.fileinfo', [
                                                                        'file' => $file,
                                                                        'userinformation' => $userinformation,
                                                                    ])
                                                                </div>
                                                            @endforeach
                                                        </p>
                                                    </div>
                                                @endif
                                                <li class="list-group-item">
                                                    <strong>Komentar:</strong>
                                                    @if (!empty($userinformation->comment))
                                                        {{ $userinformation->comment }} <span
                                                            style="color: blue;">@</span><span
                                                            style="color: blue;">{{ $userinformation->pic }}</span>
                                                    @else
                                                        Kosong
                                                    @endif
                                                </li>





                                                @if (str_contains($yourrule, 'Manager') &&
                                                        $statussetuju != 'Approved' &&
                                                        $statussetuju != 'Approved by Manager' &&
                                                        $statussetuju != 'Rejected by Manager' &&
                                                        $document->unitvalidation == 'Nonaktif')
                                                    <div class="card-text">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <form
                                                                    id="approveForm{{ $document->id }}{{ $sendtime }}"
                                                                    method="POST"
                                                                    action="{{ route('memosekdivs.senddecision', ['id' => $document->id]) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="sumberinformasi"
                                                                        value="{{ $userinformation }}">
                                                                    <input type="hidden" name="posisi"
                                                                        value="{{ $userinformation->id }}">
                                                                    <input type="hidden" name="iddocument"
                                                                        value="{{ $document->id }}">
                                                                    <input type="hidden" name="decision"
                                                                        value="Approved by Manager">
                                                                    <button type="button" class="btn btn-success mt-2"
                                                                        onclick="confirmDecision('approveForm{{ $document->id }}{{ $sendtime }}')"
                                                                        title="Disetujui pendapatnya, tidak menyatakan unit menyatakan mengakhiri feedback">
                                                                        Setuju pendapat
                                                                    </button>
                                                                </form>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <form
                                                                    id="approveDirectForm{{ $document->id }}{{ $sendtime }}"
                                                                    method="POST"
                                                                    action="{{ route('memosekdivs.senddecision', ['id' => $document->id]) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="sumberinformasi"
                                                                        value="{{ $userinformation }}">
                                                                    <input type="hidden" name="posisi"
                                                                        value="{{ $userinformation->id }}">
                                                                    <input type="hidden" name="iddocument"
                                                                        value="{{ $document->id }}">
                                                                    <input type="hidden" name="decision"
                                                                        value="Approved">
                                                                    <button type="button" class="btn btn-success mt-2"
                                                                        onclick="confirmDecision('approveDirectForm{{ $document->id }}{{ $sendtime }}')"
                                                                        title="Unit menyatakan mengakhiri feedback selesai, silakan lanjut ke proses atau unit berikutnya">
                                                                        Setuju Pendapat & Unit menyatakan selesai
                                                                    </button>
                                                                </form>
                                                            </div>

                                                            <div class="col-md-6">
                                                                <form
                                                                    id="rejectForm{{ $document->id }}{{ $sendtime }}"
                                                                    method="POST"
                                                                    action="{{ route('memosekdivs.senddecision', ['id' => $document->id]) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="sumberinformasi"
                                                                        value="{{ $userinformation }}">
                                                                    <input type="hidden" name="posisi"
                                                                        value="{{ $userinformation->id }}">
                                                                    <input type="hidden" name="iddocument"
                                                                        value="{{ $document->id }}">
                                                                    <input type="hidden" name="decision"
                                                                        value="Rejected by Manager">
                                                                    <button type="button" class="btn btn-danger mt-2"
                                                                        onclick="confirmDecision('rejectForm{{ $document->id }}{{ $sendtime }}')"
                                                                        title="Pendapat/feedback ditolak karena kurang atau salah atau hal-hal yang membuat pendapat/feedback ditolak.">
                                                                        Tolak Pendapat
                                                                    </button>
                                                                </form>
                                                            </div>

                                                        </div>
                                                    </div>
                                                @elseif (str_contains($yourrule, 'Manager') &&
                                                        $statussetuju == 'Approved by Manager' &&
                                                        !isset($statussetujulist[$unit]) &&
                                                        $document->seniormanagervalidation == 'Nonaktif')
                                                    <div class="card-text">
                                                        <div class="row">


                                                            <div class="col-md-6">
                                                                <form
                                                                    id="approveDirectForm{{ $document->id }}{{ $sendtime }}"
                                                                    method="POST"
                                                                    action="{{ route('memosekdivs.senddecision', ['id' => $document->id]) }}">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="sumberinformasi"
                                                                        value="{{ $userinformation }}">
                                                                    <input type="hidden" name="posisi"
                                                                        value="{{ $userinformation->id }}">
                                                                    <input type="hidden" name="iddocument"
                                                                        value="{{ $document->id }}">
                                                                    <input type="hidden" name="decision"
                                                                        value="Approved">
                                                                    <button type="button" class="btn btn-success mt-2"
                                                                        onclick="confirmDecision('approveDirectForm{{ $document->id }}{{ $sendtime }}')"
                                                                        title="Unit menyatakan mengakhiri feedback selesai, silakan lanjut ke proses atau unit berikutnya">
                                                                        Unit menyatakan selesai
                                                                    </button>
                                                                </form>
                                                            </div>


                                                        </div>

                                                    </div>
                                    </div>
                @endif
            @endif
        @endforeach
        @if (!empty($document->unitstepverificator[$unit]['status']))
            @if (
                $document->unitstepverificator[$unit]['status'] === 'Access' &&
                    ($document->unitpicvalidation[$unit] ?? '') !== 'Aktif' &&
                    !in_array($yourrule, ['MTPR']) &&
                    ($yourrule == $unit || $yourrule == 'Manager ' . $unit))
                @if ($yourrule == 'Manager ' . $unit)
                    <p class="mt-3">
                        <a href="{{ route('memosekdivs.uploadmanagerfeedback', $document->id) }}"
                            class="btn btn-success btn-sm feedback-upload-btn">Upload Feedback Manager
                            {{ $unit }}</a>
                    </p>
                    <p class="mt-2"><strong>STATUS</strong></p>
                @else
                    <p class="mt-3">
                        <a href="{{ route('memosekdivs.uploadfeedback', $document->id) }}"
                            class="btn btn-success btn-sm feedback-upload-btn">Upload Feedback {{ $unit }}</a>
                    </p>
                    <p class="mt-2"><strong>STATUS</strong></p>
                @endif
            @endif

            @php
                $sendtime = $userinformation->created_at;
                $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
            @endphp
            @if (
                $document->unitlaststep == $unit &&
                    $document->unitvalidation == 'Aktif' &&
                    !isset($document->sekdivfinalvalidation))
                @if ($yourrule == 'Manager ' . $unit)
                    <div class="card-text">
                        <form id="sendForm{{ $document->id }}{{ $sendtime }}" method="POST"
                            action="{{ route('new-memo.sendfoward', ['id' => $document->id]) }}">
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
                            <div class="form-group">
                                <label for="level">Send SM:</label>
                                <select name="level" id="level_{{ $document->id }}{{ $sendtime }}"
                                    class="form-control">
                                    <option value="{{ $document->SMname }}">{{ $document->SMname }}</option>
                                    @if (auth()->user()->id == 1)
                                        <option value="Senior Manager Engineering">Senior Manager Engineering</option>
                                        <option value="Senior Manager Desain">Senior Manager Desain</option>
                                        <option value="Senior Manager Teknologi Produksi">Senior Manager Teknologi Produksi
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <button type="button" class="btn btn-success mt-2"
                                onclick="confirmDecision('sendForm{{ $document->id }}{{ $sendtime }}')">Langsung
                                Kirim</button>
                        </form>
                    </div>
                @elseif($yourrule == $unit)
                    <div class="card-text">Ubah menjadi manager untuk mengirimkan ke tahap selanjutnya | Ingatkan
                        manager anda</div>
                @endif
            @endif
        @endif

        </div>
        </div>
        </div>
        </div>
        </div>
    @endif
    @endforeach
    @endif
    @endif
    {{-- Feedback Unit Akhir --}}








    {{-- Validasi Awal --}}
    @if (in_array($yourrule, ['MTPR', 'superuser']))
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <div class="info-box-content">
                    {{-- Awalan --}}
                    {{-- VALIDASI AWAL --}}
                    <!-- MULTI CHARTS -->
                    <div class="card">
                        <div class="card-header">
                            <h1 class="card-title">Validasi:</h1>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                    title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $userinformations = $document->feedbacks;
                            @endphp
                            @foreach ($userinformations as $userinformation)
                                @if ($userinformation != '')
                                    @if (in_array($userinformation->level, ['MTPR', 'superuser']) && $userinformation->condition2 != 'feedback')
                                        <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                        <p>
                                        <div class="card mt-3">
                                            <div class="info-container mt-2" style="display: none;">
                                                <div class="card-body">
                                                    <h5 class="card-title"></h5>

                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item">
                                                            @if ($userinformation->level == $yourrule)
                                                                <button class="btn" style="background-color: orange;">
                                                                    <strong>Status: Penerima dari:</strong>
                                                                    {{ $userinformation->level ?? 'hanya upload & tidak dikirim' }}
                                                                </button>
                                                            @elseif ($userinformation->level == '')
                                                                <button class="btn" style="background-color: yellow;">
                                                                    <strong>Upload Pribadi</strong>
                                                                </button>
                                                            @else
                                                                <button class="btn" style="background-color: red;">
                                                                    <strong>Status: Terkirim ke:</strong>
                                                                    {{ $userinformation->level ?? 'hanya upload & tidak dikirim' }}
                                                                </button>
                                                            @endif
                                                        </li>
                                                        <li class="list-group-item">
                                                            <strong>Apakah anda sudah melakukan review atas dokumen
                                                                approval?</strong>
                                                            {{ $userinformation->review ?: 'Kosong' }}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <strong>Nama Penulis:</strong>
                                                            {{ $userinformation->author ?: 'Kosong' }}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <strong>Email:</strong>
                                                            {{ $userinformation->email ?: 'Kosong' }}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <strong>Apakah dokumen sudah dibaca?</strong>
                                                            {{ $userinformation->isread ? 'Sudah dibaca' : 'Belum dibaca' }}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <strong>Jenis Comment:</strong>
                                                            {{ $userinformation->condition2 ?: 'Kosong' }}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <strong>ID Comment:</strong>
                                                            {{ $userinformation->id ?: 'Kosong' }}
                                                        </li>
                                                    </ul>

                                                </div>
                                            </div>
                                        </div>
                                        </p>


                                        <li class="list-group-item">
                                            <strong>Waktu:</strong>
                                            @php
                                                $sendtime = $userinformation->created_at;
                                                $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                                            @endphp
                                            {!! $formattedTime ?? 'Kosong' !!}
                                            @if ($yourrule == $document->operator && $sendtime != 'tidakada')
                                                <div class="col-md-6">
                                                    <form id="UnsendForm{{ $document->id }}{{ $sendtime }}"
                                                        method="POST"
                                                        action="{{ route('new-memo.unsenddecision', ['id' => $document->id]) }}">
                                                        @csrf
                                                        @method('PUT') <!-- Menyertakan metode PUT -->
                                                        <input type="hidden" name="_method" value="PUT">
                                                        <!-- Menambahkan input _method untuk menyatakan PUT -->
                                                        <input type="hidden" name="idfeedback"
                                                            value="{{ $userinformation->id }}">
                                                        <button type="button" class="btn btn-warning mt-2"
                                                            onclick="confirmDecision('UnsendForm{{ $document->id }}{{ $sendtime }}')">Unsend
                                                            Semua</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Status Dokumen:</strong>
                                            {{ $userinformation->reviewresult ?: 'Kosong' }}
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Status:</strong>
                                            {{ ucfirst($userinformation->condition1 ?: 'Kosong') }}
                                        </li>
                                        @php
                                            $statussetuju = $userinformation->condition1;
                                            $files = $userinformation->files;
                                        @endphp
                                        @if ($files)
                                            <div class="card feedback-item">
                                                <div class="card-text-item">
                                                    <strong>File:</strong>
                                                </div>

                                                @foreach ($files as $file)
                                                    @php
                                                        $newLinkFile = str_replace('uploads/', '', $file->link);
                                                    @endphp
                                                    <div class="card-text mt-2">
                                                        @include('newmemo.memo.fileinfo', [
                                                            'file' => $file,
                                                            'userinformation' => $userinformation,
                                                        ])
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <li class="list-group-item">
                                            <strong>Komentar:</strong>
                                            @if (!empty($userinformation->comment))
                                                {{ $userinformation->comment }} <span style="color: blue;">@</span><span
                                                    style="color: blue;">{{ $userinformation->pic }}</span>
                                            @else
                                                Kosong
                                            @endif
                                        </li>
                                        @if ($document->unitvalidation == 'Aktif' && $document->sekdivfinalvalidation != 'Aktif')
                                            <div class="col-md-4">

                                                <form id="sendForm{{ $document->id }}{{ $sendtime }}"
                                                    method="POST"
                                                    action="{{ route('memosekdivs.sendfoward', ['id' => $document->id]) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <!-- Menyertakan _method untuk metode PUT -->
                                                    <input type="hidden" name="idfeedback"
                                                        value="{{ $userinformation->id }}">
                                                    <input type="hidden" name="documentname"
                                                        value="{{ $document->documentname }}">
                                                    <input type="hidden" name="project_type"
                                                        value="{{ $document->project_type }}">
                                                    <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                                    <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                    <input type="hidden" name="decision" value="Dokumen ditutup">
                                                    <div class="form-group">
                                                        <label for="level">Send:</label>
                                                        <select name="level" id="level" class="form-control">
                                                            <option value="selesai">selesai</option>
                                                        </select>
                                                    </div>
                                                    <button type="button" class="btn btn-success mt-2"
                                                        onclick="confirmDecision('sendForm{{ $document->id }}{{ $sendtime }}')">Langsung
                                                        Kirim</button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            @endforeach
                        </div>

                    </div>
                    <!-- END MULTI CHARTS -->

                    {{-- VALIDASI Penutup --}}
                    {{-- Akhiran --}}
                </div>
            </div>
        </div>
    @endif
    {{-- Validasi Akhir --}}

    {{-- Balasan Awal --}}
    @if (in_array($yourrule, ['MTPR', 'superuser']))
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <div class="info-box-content">
                    {{-- Awalan --}}
                    {{-- VALIDASI AWAL --}}
                    <!-- MULTI CHARTS -->
                    <div class="card">
                        <div class="card-header">
                            <h1 class="card-title">Balasan (Optional):</h1>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                    title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            @php
                                $userinformations = $document->feedbacks;
                                $hasValidReply = false;
                            @endphp

                            @foreach ($userinformations as $userinformation)
                                @if ($userinformation->condition2 == 'reply')
                                    @php $hasValidReply = true; @endphp

                                    {{-- Konten balasan seperti sebelumnya --}}
                                    <button class="btn btn-sm btn-info toggle-info">Selengkapnya</button>
                                    <p>
                                    <div class="card mt-3">
                                        <div class="info-container mt-2" style="display: none;">
                                            <div class="card-body">
                                                <h5 class="card-title"></h5>

                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item">
                                                        @if ($userinformation->level == $yourrule)
                                                            <button class="btn" style="background-color: orange;">
                                                                <strong>Status: Penerima dari:</strong>
                                                                {{ $userinformation->level ?? 'hanya upload & tidak dikirim' }}
                                                            </button>
                                                        @elseif ($userinformation->level == '')
                                                            <button class="btn" style="background-color: yellow;">
                                                                <strong>Upload Pribadi</strong>
                                                            </button>
                                                        @else
                                                            <button class="btn" style="background-color: red;">
                                                                <strong>Status: Terkirim ke:</strong>
                                                                {{ $userinformation->level ?? 'hanya upload & tidak dikirim' }}
                                                            </button>
                                                        @endif
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>Apakah anda sudah melakukan review atas dokumen
                                                            approval?</strong>
                                                        {{ $userinformation->review ?: 'Kosong' }}
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>Nama Penulis:</strong>
                                                        {{ $userinformation->author ?: 'Kosong' }}
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>Email:</strong>
                                                        {{ $userinformation->email ?: 'Kosong' }}
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>Apakah dokumen sudah dibaca?</strong>
                                                        {{ $userinformation->isread ? 'Sudah dibaca' : 'Belum dibaca' }}
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>Jenis Comment:</strong>
                                                        {{ $userinformation->condition2 ?: 'Kosong' }}
                                                    </li>
                                                    <li class="list-group-item">
                                                        <strong>ID Comment:</strong>
                                                        {{ $userinformation->id ?: 'Kosong' }}
                                                    </li>
                                                </ul>

                                            </div>
                                        </div>
                                    </div>
                                    </p>


                                    <li class="list-group-item">
                                        <strong>Waktu:</strong>
                                        @php
                                            $sendtime = $userinformation->created_at;
                                            $formattedTime = $userinformation->created_at->format('Y-m-d H:i:s');
                                        @endphp
                                        {!! $formattedTime ?? 'Kosong' !!}
                                        @if ($yourrule == $document->operator && $sendtime != 'tidakada')
                                            <div class="col-md-6">
                                                <form id="UnsendForm{{ $document->id }}{{ $sendtime }}"
                                                    method="POST"
                                                    action="{{ route('new-memo.unsenddecision', ['id' => $document->id]) }}">
                                                    @csrf
                                                    @method('PUT') <!-- Menyertakan metode PUT -->
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <!-- Menambahkan input _method untuk menyatakan PUT -->
                                                    <input type="hidden" name="idfeedback"
                                                        value="{{ $userinformation->id }}">
                                                    <button type="button" class="btn btn-warning mt-2"
                                                        onclick="confirmDecision('UnsendForm{{ $document->id }}{{ $sendtime }}')">Unsend
                                                        Semua</button>
                                                </form>
                                            </div>
                                        @endif
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Status Dokumen:</strong>
                                        {{ $userinformation->reviewresult ?: 'Kosong' }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Status:</strong>
                                        {{ ucfirst($userinformation->condition1 ?: 'Kosong') }}
                                    </li>
                                    @php
                                        $statussetuju = $userinformation->condition1;
                                        $files = $userinformation->files;
                                    @endphp
                                    @if ($files)
                                        <div class="card feedback-item">
                                            <div class="card-text-item">
                                                <strong>File:</strong>
                                            </div>

                                            @foreach ($files as $file)
                                                @php
                                                    $newLinkFile = str_replace('uploads/', '', $file->link);
                                                @endphp
                                                <div class="card-text mt-2">
                                                    @include('newmemo.memo.fileinfo', [
                                                        'file' => $file,
                                                        'userinformation' => $userinformation,
                                                    ])
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <li class="list-group-item">
                                        <strong>Komentar:</strong>
                                        @if (!empty($userinformation->comment))
                                            {{ $userinformation->comment }} <span style="color: blue;">@</span><span
                                                style="color: blue;">{{ $userinformation->pic }}</span>
                                        @else
                                            Kosong
                                        @endif
                                    </li>
                                    @if ($document->unitvalidation == 'Aktif' && $document->sekdivfinalvalidation != 'Aktif')
                                        <div class="col-md-4">

                                            <form id="sendForm{{ $document->id }}{{ $sendtime }}" method="POST"
                                                action="{{ route('memosekdivs.sendfoward', ['id' => $document->id]) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="_method" value="PUT">
                                                <!-- Menyertakan _method untuk metode PUT -->
                                                <input type="hidden" name="idfeedback"
                                                    value="{{ $userinformation->id }}">
                                                <input type="hidden" name="documentname"
                                                    value="{{ $document->documentname }}">
                                                <input type="hidden" name="project_type"
                                                    value="{{ $document->project_type }}">
                                                <input type="hidden" name="picunit" value="{{ $yourrule }}">
                                                <input type="hidden" name="posisi" value="{{ $sendtime }}">
                                                <input type="hidden" name="decision" value="Dokumen ditutup">
                                                <div class="form-group">
                                                    <label for="level">Send:</label>
                                                    <select name="level" id="level" class="form-control">
                                                        <option value="selesai">selesai</option>
                                                    </select>
                                                </div>
                                                <button type="button" class="btn btn-success mt-2"
                                                    onclick="confirmDecision('sendForm{{ $document->id }}{{ $sendtime }}')">Langsung
                                                    Kirim</button>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            @endforeach

                            {{-- Hanya tampilkan jika tidak ada satu pun yang memenuhi --}}
                            @if (!$hasValidReply)
                                <p class="mt-3">
                                    <a href="{{ route('memosekdivs.uploadreply', $document->id) }}"
                                        class="btn btn-success btn-sm feedback-upload-btn">Upload Balasan (Optional)</a>
                                </p>
                            @endif
                        </div>

                    </div>
                    <!-- END MULTI CHARTS -->

                    {{-- VALIDASI Penutup --}}
                    {{-- Akhiran --}}
                </div>
            </div>
        </div>
    @endif
    {{-- Balasan Akhir --}}


@endsection







@push('scripts')
    <!-- SweetAlert2 and JavaScript for the modal -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function showAddAccessModal(documentId, users, existingUserIds) {
            // Create dropdown options, disabling users who already have access
            const userOptions = users.map(user => `
            <option value="${user.id}" ${existingUserIds.includes(user.id) ? 'disabled' : ''}>
                ${user.name} ${existingUserIds.includes(user.id) ? '(Sudah memiliki akses)' : ''}
            </option>
        `).join('');

            // Show SweetAlert modal
            Swal.fire({
                title: 'Tambah Akses Pengguna',
                html: `
                <select id="user_id" class="swal2-select" style="width: 100%;">
                    <option value="">Pilih pengguna</option>
                    ${userOptions}
                </select>
            `,
                showCancelButton: true,
                confirmButtonText: 'Tambah',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const userId = document.getElementById('user_id').value;
                    if (!userId) {
                        Swal.showValidationMessage('Silakan pilih pengguna');
                    }
                    return {
                        user_id: userId
                    };
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch('/memosekdivs/access/store', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                memo_sekdiv_id: documentId,
                                user_id: result.value.user_id
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            Swal.fire('Sukses', 'Akses pengguna berhasil ditambahkan!', 'success').then(() => {
                                // Refresh the access list
                                fetchAccessList(documentId);
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Gagal menambahkan akses', 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
                    }
                }
            });
        }

        async function fetchAccessList(documentId) {
            const response = await fetch(`/memosekdivs/access/${documentId}/list`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            const statusBadge = document.getElementById('permissionlist');

            // ✅ Tampilkan nama-nama pengguna dengan benar
            statusBadge.textContent = data.accesses.length ?
                data.accesses.map(access => access.name).join(', ') :
                'Tidak ada pengguna';

            // ✅ Update tombol dengan user_id terbaru agar yang sudah ada tidak bisa dipilih lagi
            const button = document.querySelector('.btn-primary[onclick*="showAddAccessModal"]');
            if (button) {
                const newExistingUserIds = data.accesses.map(access => access.user_id);
                button.setAttribute('onclick',
                    `showAddAccessModal(${documentId}, ${JSON.stringify(users)}, ${JSON.stringify(newExistingUserIds)})`
                );
            }
        }
    </script>
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Teks berhasil disalin ke clipboard');
            }, function(err) {
                alert('Gagal menyalin teks');
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        function confirmDecision(formId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Anda akan mengambil keputusan ini.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, lanjutkan!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Updated!",
                        text: "Your information has been uploaded.",
                        icon: "success"
                    });
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.toggle-info').click(function() {
                $(this).closest('.info-box-content').find('.info-container').toggle();
            });
        });
    </script>
@endpush


@push('css')
    <style>
        .feedback-container {
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .feedback-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .feedback-item a {
            color: #007bff;
            text-decoration: none;
        }

        .feedback-section {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 20px;
        }

        .btn-warning,
        .btn-success {
            color: #fff;
        }

        .card-text {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .feedback-info {
            margin-bottom: 10px;
        }

        .timestamp-badge {
            background-color: #007bff;
            /* Warna latar belakang badge */
            color: #fff;
            /* Warna teks badge */
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 80%;
            /* Ukuran font yang lebih kecil */
            margin-top: 5px;
            /* Jarak antara teks dan waktu */
        }

        .card-badge {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-text {
            margin-right: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .badge-terbuka {
            background-color: #dc3545;
            color: #fff;
        }

        .badge-tertutup {
            background-color: #28a745;
            color: #fff;
        }
    </style>
@endpush
