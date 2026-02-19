@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('new-memo.index') }}">List Memo Sekdiv</a></li>
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
            <h3 class="card-title text-bold">Page Monitoring Memo Sekdiv <span class="badge badge-info ml-1"></span></h3>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                @if (in_array($authuser->rule, ['superuser']))
                    <div class="col-md-3 col-sm-6 col-12">
                        <button type="button" class="btn btn-danger btn-sm btn-block"
                            onclick="handleDeleteMultipleItems()">Hapus yang dipilih</button>
                    </div>
                @endif
                <div class="col-md-3 col-sm-6 col-12">
                    <a href="{{ route('memosekdivs.create') }}" class="btn btn-primary btn-sm btn-block">Buat Dokumen</a>
                </div>
            </div>
            <div class="mb-3">
                <a href="{{ route('memosekdivs.create') }}" class="btn btn-primary btn-sm">
                    Upload Memo
                </a>
            </div>
            <table id="example2" class="table table-bordered table-hover">
                <thead>
                    <th>
                        <span class="checkbox-toggle" id="checkAll"><i class="far fa-square"></i></span>
                    </th>
                    <th scope="col">No</th>
                    <th scope="col">Nama Dokumen</th>
                    <th scope="col">No Dokumen</th>
                    <th scope="col">Posisi Dokumen</th>
                    <th scope="col">Status Dokumen</th>
                    <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $counterdokumen = 1;
                    @endphp
                    @foreach ($memosekdivs as $document)
                        @php
                            $unitsingkatan = $document->unitsingkatan ?? [];
                            $projectpics = $document->projectpics ?? [];
                            $unitpicvalidation = $document->unitpicvalidation ?? [];
                            $smunitpicvalidation = $document->smunitpicvalidation ?? [];
                        @endphp
                        <tr>
                            <td>
                                <div class="icheck-primary">
                                    <input type="checkbox" value="{{ $document->id }}" name="document_ids[]"
                                        id="checkbox{{ $document->id }}">
                                    <label for="checkbox{{ $document->id }}"></label>
                                </div>
                            </td>
                            <td>{{ $counterdokumen++ }}</td>
                            <td>{{ $document->documentname }}</td>
                            <td>{{ $document->documentnumber }}
                            </td>
                            <td class="project-actionkus text-right">
                                <div style="position: relative;">
                                    <div class="container">
                                        @php
                                            $positions = [
                                                'posisi1' => $document->posisi1 == true ? 'boxblue' : 'box',
                                                'posisi2' => $document->posisi2 == true ? 'boxblue' : 'box',
                                                'posisi3' => $document->posisi3 == true ? 'boxblue' : 'box',
                                                'posisi4' => $document->posisi4 == true ? 'boxblue' : 'box',
                                                'posisi5' => $document->posisi5 == true ? 'boxblue' : 'box',
                                            ];
                                        @endphp
                                        <a class="{{ $positions['posisi1'] }}" href="#">
                                            <div class="container">
                                                <div class="indicator {{ $document->sekdivvalidation == 'Aktif' ? 'green' : 'red' }}"
                                                    title="{{ $document->sekdivvalidation == 'Aktif' ? 'Dokumen sudah dikirim' : 'Dokumen belum dikirim' }}">
                                                </div>
                                                <span class="keterangan">Sekdiv</span>
                                            </div>
                                        </a>
                                        <span class="arrow">→</span>
                                        <div class="{{ $positions['posisi2'] }}" style="height: 300px;">
                                            <h2>SM</h2>
                                            <ul>
                                                @php
                                                    $allunitundersm = array_keys($smunitpicvalidation);
                                                @endphp
                                                @foreach (['Senior Manager Engineering', 'Senior Manager Teknologi Produksi', 'Senior Manager Desain', 'Manager MTPR'] as $projectpic)
                                                    <li>
                                                        @php
                                                            $unitsminfo =
                                                                $smunitpicvalidation[$projectpic] ?? 'Nonaktif';
                                                        @endphp
                                                        @if (in_array($projectpic, $allunitundersm))
                                                            <div class="indicator 
                                                                    {{ $unitsminfo == 'Aktif'
                                                                        ? 'green'
                                                                        : ($unitsminfo == 'Ongoing'
                                                                            ? 'orange'
                                                                            : ($unitsminfo == 'Belum dibaca'
                                                                                ? 'yellow'
                                                                                : ($unitsminfo == 'Sudah dibaca'
                                                                                    ? 'blue'
                                                                                    : 'red'))) }}"
                                                                title="{{ $unitsminfo == 'Aktif'
                                                                    ? $projectpic . ' sudah approve'
                                                                    : ($unitsminfo == 'Ongoing'
                                                                        ? $projectpic . ' sudah menerima dokumen dan belum memilih unit'
                                                                        : ($unitsminfo == 'Belum dibaca'
                                                                            ? $projectpic . ' belum dibaca'
                                                                            : ($unitsminfo == 'Sudah dibaca'
                                                                                ? $projectpic . ' sudah dibaca'
                                                                                : $projectpic . ' belum dikerjakan'))) }}">
                                                            </div>
                                                        @else
                                                            <div class="indicator black"
                                                                title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                        @endif
                                                        <span
                                                            class="keterangan">{{ $unitsingkatan[$projectpic] ?? $projectpic }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <span class="arrow">→</span>
                                        <div class="{{ $positions['posisi3'] }}" style="height: 300px;">
                                            <h2>Tek</h2>
                                            <ul>
                                                @foreach (['MTPR', 'RAMS'] as $projectpic)
                                                    <li>
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
                                                            <div class="indicator black"
                                                                title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                        @endif
                                                        <span
                                                            class="keterangan">{{ $unitsingkatan[$projectpic] ?? $projectpic }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="{{ $positions['posisi3'] }}" style="height: 300px;">
                                            <h2>Eng</h2>
                                            <ul>
                                                @foreach (['Product Engineering', 'Mechanical Engineering System', 'Electrical Engineering System', 'Quality Engineering'] as $projectpic)
                                                    <li>
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
                                                            <div class="indicator black"
                                                                title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                        @endif
                                                        <span
                                                            class="keterangan">{{ $unitsingkatan[$projectpic] ?? $projectpic }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="{{ $positions['posisi3'] }}" style="height: 300px;">
                                            <h2>Des</h2>
                                            <ul>
                                                @foreach (['Desain Mekanik & Interior', 'Desain Bogie & Wagon', 'Desain Carbody', 'Desain Elektrik'] as $projectpic)
                                                    <li>
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
                                                            <div class="indicator black"
                                                                title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                        @endif
                                                        <span
                                                            class="keterangan">{{ $unitsingkatan[$projectpic] ?? $projectpic }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="{{ $positions['posisi3'] }}" style="height: 300px;">
                                            <h2>TP</h2>
                                            <ul>
                                                @foreach (['Preparation & Support', 'Welding Technology', 'Shop Drawing', 'Teknologi Proses'] as $projectpic)
                                                    <li>
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
                                                            <div class="indicator black"
                                                                title="{{ $projectpic . ' tidak terlibat' }}"></div>
                                                        @endif
                                                        <span
                                                            class="keterangan">{{ $unitsingkatan[$projectpic] ?? $projectpic }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <span class="arrow">→</span>
                                        <a class="{{ $positions['posisi4'] }}" href="#">
                                            <div class="container">
                                                <div class="indicator {{ $document->sekdivfinalvalidation == 'Aktif' ? 'green' : 'red' }}"
                                                    title="{{ $document->sekdivfinalvalidation == 'Aktif' ? 'Dokumen sudah difinalisasi' : 'Dokumen belum difinalisasi' }}">
                                                </div>
                                                <span class="keterangan">Sekdiv</span>
                                            </div>
                                        </a>


                                    </div>
                                </div>
                            </td>
                            <td>
                                <button type="button"
                                    class="btn document-status-button document-status-button-{{ $document->documentopenedclosed == 'Terbuka' ? 'open' : 'closed' }} btn-sm {{ $document->documentopenedclosed == 'Terbuka' ? 'btn-danger' : 'btn-success' }}"
                                    title="{{ $document->documentopenedclosed }}" onclick="toggleDocumentStatus(this)"
                                    data-document-status="{{ $document->documentopenedclosed }}"
                                    data-document-id="{{ $document->id }}">
                                    <i
                                        class="{{ $document->documentopenedclosed == 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle' }}"></i>
                                    <span>{{ $document->documentopenedclosed }}</span>
                                </button>
                            </td>
                            <td class="project-actions text-right">
                                <div class="col-md-12 text-right column-layout">
                                    <a class="btn btn-primary btn-sm"
                                        href="{{ route('memosekdivs.show', $document->id) }}" style="width: 100px;">
                                        <i class="fas fa-folder"></i> Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f0f2f5;
                /* Warna latar belakang yang lembut */
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
                /* Warna biru yang futuristik */
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
                /* Tambahkan sedikit padding */
                background-color: #e1f5fe;
                /* Warna biru muda */
                box-shadow: 0 2px 4px rgba(0, 176, 255, 0.2);
            }

            .box {
                margin-right: 5px;
                border: 1px solid #ccc;
                border-radius: 10px;
                padding: 10px;
                /* Tambahkan sedikit padding */
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
                /* Warna hijau */
            }

            .red {
                background-color: #f44336;
                /* Warna merah */
            }

            .yellow {
                background-color: #ffeb3b;
                /* Warna kuning */
            }

            .blue {
                background-color: #2196f3;
                /* Warna biru */
            }

            .orange {
                background-color: #ff9800;
                /* Warna orange */
            }

            .black {
                background-color: #212121;
                /* Warna hitam */
            }
        </style>
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script>
            function showRevisiContent(keyan) {
                document.querySelectorAll('.revisi-section').forEach(section => {
                    section.style.display = 'none';
                });

                const selectedSection = document.getElementById('revisi-' + keyan);
                if (selectedSection) {
                    selectedSection.style.display = 'block';
                }
            }
        </script>
    @endpush
