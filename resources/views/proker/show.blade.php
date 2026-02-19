@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href='{{ route('proker.index') }}'>Program Kerja dan LPK</a></li>
                        <li class="breadcrumb-item"><a href="#">{{ $unitRecord->name ?? '' }}</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-16">
                <div class="card card-danger card-outline">
                    <div class="card-header">



                        @if ($userId == 37 || $userId == 1)
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="month_year" class="form-label">Pilih Bulan & Tahun</label>
                                    <input type="month" id="month_year" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="unit_id" class="form-label">Pilih Unit</label>
                                    <select id="unit_id" class="form-control">
                                        <option value="">-- Pilih Unit --</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mt-4">
                                    <button class="btn btn-primary" onclick="fetchProker()">Cari</button>
                                    @if ($userId == 37 || $userId == 1)
                                        <button class="btn btn-success" onclick="openCreateModal()">+ Tambah Proker &
                                            LPK</button>
                                    @endif

                                </div>
                            </div>
                        @endif

                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <table id="example2" class="table table-bordered table-hover mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center align-middle" style="width: 5%;">No</th>
                                    <th class="text-center align-middle" style="width: 15%;">Proker Dibuat</th>
                                    <th class="text-center align-middle">Jenis</th>
                                    <th class="text-center align-middle">Nama</th>
                                    <th class="text-center align-middle" style="width: 20%;">Progress Bulan Lalu(%)</th>
                                    <th class="text-center align-middle" style="width: 20%;">Progress Bulan Ini(%)</th>
                                    <th class="text-center align-middle" style="width: 15%;">Evidence</th>
                                    @if ($userId == 37 || $userId == 1)
                                        <th class="text-center align-middle" style="width: 5%;">Status</th>
                                    @endif
                                    <th class="text-center align-middle" style="width: 20%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="prokerTableBody">
                                @if ($prokers->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Silakan pilih bulan & unit
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($prokers as $proker)
                                        <tr>
                                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                            <td class="text-center align-middle">
                                                {{ \Carbon\Carbon::parse($proker->proker_created_at)->format('m/Y') }}
                                            </td>
                                            <td class="align-middle">
                                                {{ $proker->kind == 'proker' ? 'Proker' : ($proker->kind == 'lpk' ? 'LPK' : '-') }}
                                            </td>

                                            <td class="align-middle">{{ $proker->name }}</td>

                                            @php
                                                $currentMonthYear = \Carbon\Carbon::today()->format('Y-m');
                                                $previousMonthYear = \Carbon\Carbon::today()->subMonth()->format('Y-m');
                                                $latestMonthly = $proker->prokerMonthly
                                                    ->where('date', $currentMonthYear)
                                                    ->first();
                                                $latestMonthlyPrevious = $proker->prokerMonthly
                                                    ->where('date', $previousMonthYear)
                                                    ->first();
                                            @endphp
                                            <td class="align-middle">

                                                @if ($latestMonthlyPrevious)
                                                    <div class="progress my-2" style="height: 20px;">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: {{ $latestMonthlyPrevious->percentage }}%;"
                                                            aria-valuenow="{{ $latestMonthlyPrevious->percentage }}"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                            {{ $latestMonthlyPrevious->percentage }}%
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="progress my-2" style="height: 20px;">
                                                        <div class="progress-bar bg-secondary text-dark" role="progressbar"
                                                            style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                                            aria-valuemax="100">
                                                            0%
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="align-middle">

                                                @if ($latestMonthly)
                                                    <div class="progress my-2" style="height: 20px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: {{ $latestMonthly->percentage }}%;"
                                                            aria-valuenow="{{ $latestMonthly->percentage }}"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                            {{ $latestMonthly->percentage }}%
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Belum Ada Progress</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @php
                                                    $currentMonthYear = \Carbon\Carbon::today()->format('Y-m');
                                                    $latestMonthly = $proker->prokerMonthly
                                                        ->where('date', $currentMonthYear)
                                                        ->first();
                                                    $latestMonthlyPrevious = $proker->prokerMonthly
                                                        ->where(
                                                            'date',
                                                            \Carbon\Carbon::today()->subMonth()->format('Y-m'),
                                                        )
                                                        ->first();
                                                @endphp
                                                @if ($latestMonthly)
                                                    @if ($latestMonthly->files->isNotEmpty())
                                                        @foreach ($latestMonthly->files as $file)
                                                            <a href="{{ Storage::url('uploads/' . $file->filename) }}"
                                                                class="d-block text-primary text-decoration-none mt-1"
                                                                target="_blank">
                                                                <i class="fas fa-file-alt me-1"></i> {{ $file->filename }}
                                                            </a>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">Tidak ada file</span>
                                                    @endif
                                                @endif
                                            </td>
                                            @if ($userId == 37 || $userId == 1)
                                                <td class="align-middle text-center">
                                                    <span class="badge {{ $proker->ishide ? 'bg-danger' : 'bg-success' }}">
                                                        {{ $proker->ishide ? 'Tersembunyi' : 'Terlihat' }}
                                                    </span>

                                                    <span
                                                        class="badge {{ $proker->ispercentageflexible ? 'bg-info' : 'bg-secondary' }}">
                                                        {{ $proker->ispercentageflexible ? 'Persentase Bebas' : 'Persentase 0 / 100' }}
                                                    </span>

                                                </td>
                                            @endif
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center gap-2">
                                                    @if ($userId == 37 || $userId == 1)
                                                        <button class="btn btn-info btn-sm rounded-3 px-3"
                                                            onclick="showProkerMonthly({{ $proker->id }})">
                                                            <i class="fas fa-eye me-1"></i> Show
                                                        </button>
                                                        <button class="btn btn-warning btn-sm rounded-3 px-3"
                                                            onclick="sendProkerReminder({{ $proker->id }})">
                                                            <i class="fas fa-bell me-1"></i> Reminder
                                                        </button>
                                                        <button class="btn bg-purple btn-sm rounded-3 px-3"
                                                            onclick="deleteProker({{ $proker->id }})">
                                                            <i class="fas fa-trash me-1"></i> Delete
                                                        </button>
                                                        @php
                                                            $isHidden = $proker->ishide;
                                                            $buttonColorClass = $isHidden ? 'bg-teal' : 'bg-maroon'; // teal jika tersembunyi, maroon jika belum
                                                            $iconClass = $isHidden ? 'fa-eye' : 'fa-eye-slash';
                                                            $buttonText = $isHidden ? 'Tampilkan' : 'Sembunyikan';
                                                        @endphp

                                                        <button class="btn {{ $buttonColorClass }} btn-sm rounded-3 px-3"
                                                            onclick="toggleProker({{ $proker->id }}, {{ $isHidden ? 'true' : 'false' }})">
                                                            <i class="fas {{ $iconClass }} me-1"></i>
                                                            {{ $buttonText }}
                                                        </button>
                                                    @endif
                                                    @if ($unitId && $proker->unit_id == $unitId)
                                                        @if (!$latestMonthly)
                                                            <button class="btn btn-danger btn-sm rounded-3 px-3"
                                                                onclick="addProkerMonthly({{ $proker->id }})">
                                                                <i class="fas fa-plus me-1"></i> Add for this Month
                                                            </button>
                                                        @else
                                                            <button class="btn btn-success btn-sm rounded-3 px-3" disabled>
                                                                <i class="fas fa-check me-1"></i> Progress filled
                                                            </button>
                                                        @endif
                                                        @if (!$latestMonthlyPrevious)
                                                            <button class="btn btn-secondary btn-sm rounded-3 px-3"
                                                                onclick="addProkerMonthlyPrevious({{ $proker->id }})">
                                                                <i class="fas fa-plus me-1"></i> Add Previous Month
                                                            </button>
                                                        @else
                                                            <button class="btn btn-secondary btn-sm rounded-3 px-3"
                                                                disabled>
                                                                <i class="fas fa-check me-1"></i> Previous Month filled
                                                            </button>
                                                        @endif
                                                    @endif

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@push('style')
    <style>
        .large-swal {
            width: 800px !important;
            /* Lebar yang lebih besar */
            height: auto !important;
            /* Tinggi menyesuaikan isi */
            max-height: 90vh !important;
            /* Batasi agar tidak terlalu tinggi */
            overflow-y: auto !important;
            /* Tambahkan scroll jika isinya terlalu banyak */
        }

        .card {
            border-radius: 10px;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: border-color 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 5px rgba(220, 53, 69, 0.2);
        }

        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-primary:hover {
            background-color: #c82333;
            border-color: #c82333;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #138496;
        }

        .table th,
        .table td {
            vertical-align: middle;
            padding: 12px;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .breadcrumb {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 10px 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .alert {
            border-radius: 8px;
            padding: 12px 20px;
        }

        .progress {
            border-radius: 10px;
            overflow: hidden;
        }

        .swal2-popup {
            border-radius: 12px !important;
        }

        .swal2-html-container .form-control {
            margin-bottom: 10px;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script>
        // Teruskan userId dan unitId dari PHP ke JavaScript
        const userId = {{ $userId ?? 'null' }};
        const userUnitId = {{ $unitId ?? 'null' }}; // Unit pengguna dari PHP

        $(document).ready(function() {
            $('#month_year').val('{{ $monthYear }}');
            $('#unit_id').val('{{ $unitId ?? '' }}');
            // Optionally call fetchProker() here if you want to refresh via AJAX on load
        });

        function formatMonthYear(dateString) {
            const date = new Date(dateString);
            const month = String(date.getMonth() + 1).padStart(2, '0'); // getMonth() is zero-based
            const year = date.getFullYear();
            return `${month}/${year}`;
        }


        function fetchProker() {
            let monthYear = $('#month_year').val();
            let unitIdFromSelect = $('#unit_id').val();

            if (!monthYear || !unitIdFromSelect) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Harap pilih bulan & unit terlebih dahulu!'
                });
                return;
            }

            $.ajax({
                url: "{{ route('proker.searchProker') }}",
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                    month_year: monthYear,
                    unit_id: unitIdFromSelect
                },
                beforeSend: function() {
                    console.log('Mengirim data:', {
                        month_year: monthYear,
                        unit_id: unitIdFromSelect
                    });
                },
                success: function(response) {
                    console.log('Respons:', response);
                    let rows = '';
                    const currentMonthYear = monthYear; // Use selected month_year
                    const previousMonthYear = new Date(monthYear + '-01').toISOString().slice(0,
                        7); // Calculate previous month in frontend if needed

                    if (response.length > 0) {
                        let counter = 1;
                        response.forEach(proker => {
                            let currentMonthly = proker.current_monthly;
                            let previousMonthly = proker.previous_monthly;
                            let actionButtons = '<div class="d-flex justify-content-center gap-2">';

                            // Show button for userId 37 or 1
                            if (userId === 37 || userId === 1) {
                                actionButtons += `
                            <button class="btn btn-info btn-sm rounded-3 px-3" onclick="showProkerMonthly(${proker.id})">
                                <i class="fas fa-eye me-1"></i> Show
                            </button>
                            <button class="btn btn-warning btn-sm rounded-3 px-3" onclick="sendProkerReminder(${proker.id})">
                                <i class="fas fa-bell me-1"></i> Reminder
                            </button>
                            <button class="btn bg-purple btn-sm rounded-3 px-3" onclick="deleteProker(${proker.id})">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                            <button class="btn ${proker.ishide ? 'bg-teal' : 'bg-maroon'} text-white btn-sm rounded-3 px-3"
                                    onclick="toggleProker(${proker.id}, ${proker.ishide})">
                                <i class="fas ${proker.ishide ? 'fa-eye' : 'fa-eye-slash'} me-1"></i>
                                ${proker.ishide ? 'Tampilkan' : 'Sembunyikan'}
                            </button>
                        `;
                            }

                            // Add Progress button if no monthly progress and unit matches
                            if (!currentMonthly && userUnitId && proker.unit_id == userUnitId) {
                                actionButtons += `
                            <button class="btn btn-danger btn-sm rounded-3 px-3" onclick="addProkerMonthly(${proker.id})">
                                <i class="fas fa-plus me-1"></i> Add for this Month
                            </button>

                        `;
                            }

                            if (currentMonthly && userUnitId && proker.unit_id == userUnitId) {
                                actionButtons += `
                            <button class="btn btn-success btn-sm rounded-3 px-3" disabled>
                                <i class="fas fa-check me-1"></i> Progress filled
                            </button>
                        `;
                            }


                            actionButtons += '</div>';

                            // Previous month progress
                            let previousProgressCell = `
                        <div class="progress my-2" style="height: 20px;">
                            <div class="progress-bar bg-secondary text-dark" role="progressbar"
                                 style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                 0%
                            </div>
                        </div>
                    `;
                            if (previousMonthly && previousMonthly.percentage !== undefined) {
                                previousProgressCell = `
                            <div class="progress my-2" style="height: 20px;">
                                <div class="progress-bar bg-info" role="progressbar"
                                     style="width: ${previousMonthly.percentage}%;"
                                     aria-valuenow="${previousMonthly.percentage}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                     ${previousMonthly.percentage}%
                                </div>
                            </div>
                        `;
                            }

                            // Current month progress
                            let currentProgressCell =
                                '<span class="text-muted">Belum Ada Progress</span>';
                            let fileCell = '<span class="text-muted">Belum Ada file</span>';

                            if (currentMonthly && currentMonthly.percentage !== undefined) {
                                currentProgressCell = `
                            <div class="progress my-2" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: ${currentMonthly.percentage}%;"
                                     aria-valuenow="${currentMonthly.percentage}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                     ${currentMonthly.percentage}%
                                </div>
                            </div>
                        `;
                            }

                            if (currentMonthly && currentMonthly.files && currentMonthly.files.length >
                                0) {
                                fileCell = currentMonthly.files.map(file =>
                                    `<a href="/storage/uploads/${file.filename}" class="d-block text-primary text-decoration-none mt-1" target="_blank">
                                 <i class="fas fa-file-alt me-1"></i> ${file.filename}
                             </a>`
                                ).join('');
                            }

                            // Status cell for userId 37 or 1
                            let statusCell = '';
                            if (userId === 37 || userId === 1) {
                                statusCell = `
                            <td class="align-middle text-center">
                                <span class="badge ${proker.ishide ? 'bg-danger' : 'bg-success'}">
                                    ${proker.ishide ? 'Tersembunyi' : 'Terlihat'}
                                </span>            <span class="badge ${proker.ispercentageflexible ? 'bg-info' : 'bg-secondary'}">
                ${proker.ispercentageflexible ? 'Persentase Bebas' : 'Persentase 0 / 100'}
            </span>

                            </td>
                        `;
                            }

                            rows += `
                        <tr>
                            <td class="text-center align-middle">${counter}</td>
                            <td class="text-center align-middle">${formatMonthYear(proker.proker_created_at)}</td>
                            <td class="align-middle">${proker.kind === 'proker' ? 'Proker' : (proker.kind === 'lpk' ? 'LPK' : '-')}</td>
                            <td class="align-middle">${proker.name}</td>
                            <td class="align-middle">${previousProgressCell}</td>
                            <td class="align-middle">${currentProgressCell}</td>
                            <td class="align-middle">${fileCell}</td>
                            ${statusCell}
                            <td class="text-center align-middle">${actionButtons}</td>
                        </tr>`;
                            counter++;
                        });
                    } else {
                        rows = '<tr><td colspan="9" class="text-center">Data tidak ditemukan</td></tr>';
                    }
                    $('#prokerTableBody').html(rows);
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mengambil data!'
                    });
                }
            });
        }



        function openCreateModal() {
            Swal.fire({
                title: 'Tambah Program',
                html: `
            <div class="form-group">
                <label for="swal-name">Nama</label>
                <input type="text" id="swal-name" class="form-control mb-2" placeholder="Masukkan nama proker">
            </div>
            <div class="form-group">
                <label for="swal-kind">Jenis</label>
                <select id="swal-kind" class="form-control mb-2">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="proker">Proker</option>
                    <option value="lpk">LPK</option>
                </select>
            </div>
            <div class="form-group">
                <label for="swal-unit">Pilih Unit</label>
                <select id="swal-unit" class="form-control mb-2">
                    <option value="">-- Pilih Unit --</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="swal-month-year">Bulan & Tahun</label>
                <input type="month" id="swal-month-year" class="form-control mb-2">
            </div>
            <div class="form-check">
                <input type="checkbox" id="swal-ispercentageflexible" class="form-check-input" checked>
                <label for="swal-ispercentageflexible" class="form-check-label">Persentase Fleksibel</label>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const name = document.getElementById('swal-name').value;
                    const kind = document.getElementById('swal-kind').value;
                    const unitId = document.getElementById('swal-unit').value;
                    const prokerCreatedAt = document.getElementById('swal-month-year').value;
                    const ispercentageflexible = document.getElementById('swal-ispercentageflexible').checked;

                    if (!name || !kind || !unitId || !prokerCreatedAt) {
                        Swal.showValidationMessage('Harap isi semua kolom!');
                        return false;
                    }

                    return {
                        name,
                        kind,
                        unitId,
                        prokerCreatedAt,
                        ispercentageflexible
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    createProker(
                        result.value.name,
                        result.value.kind,
                        result.value.unitId,
                        result.value.prokerCreatedAt,
                        result.value.ispercentageflexible
                    );
                }
            });
        }


        function toggleProker(prokerId, isCurrentlyHidden) {
            const actionText = isCurrentlyHidden ? 'tampilkan kembali' : 'sembunyikan';
            const confirmText = isCurrentlyHidden ? 'Tampilkan' : 'Sembunyikan';

            Swal.fire({
                title: `${confirmText} Proker?`,
                text: `Apakah Anda yakin ingin ${actionText} proker ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0d9488'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/prokerlpk/toggle-hide/${prokerId}`,
                        type: "PATCH", // GUNAKAN PATCH UNTUK UPDATE SEBAGIAN
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire('Berhasil', response.message, 'success').then(() => {
                                location
                                    .reload(); // reload halaman agar tombol dan ikon berubah
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Gagal memperbarui status proker!', 'error');
                        }
                    });
                }
            });
        }

        function deleteProker(prokerId) {
            Swal.fire({
                title: 'Hapus Proker?',
                text: 'Apakah Anda yakin ingin menghapus proker ini? Tindakan ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/prokerlpk/delete/${prokerId}`,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire('Berhasil', response.message, 'success');
                            fetchProker();
                        },
                        error: function() {
                            Swal.fire('Error', 'Gagal menghapus proker!', 'error');
                        }
                    });
                }
            });
        }



        function createProker(name, kind, unitId, prokerCreatedAt, ispercentageflexible) {
            $.ajax({
                url: "{{ route('proker.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name,
                    kind: kind,
                    unit_id: unitId,
                    proker_created_at: prokerCreatedAt
                },
                success: function(response) {
                    Swal.fire('Berhasil', response.message, 'success');
                    $('#month_year').val(prokerCreatedAt);
                    $('#unit_id').val(unitId);
                    fetchProker();
                },
                error: function() {
                    Swal.fire('Error', 'Gagal menambahkan proker!', 'error');
                }
            });
        }



        function showProkerMonthly(prokerId) {
            $.ajax({
                url: "{{ route('proker.historyProker') }}",
                type: "GET",
                data: {
                    proker_id: prokerId
                },
                success: function(response) {
                    const proker = response.find(p => p.id === prokerId);
                    if (!proker) {
                        Swal.fire('Error', 'Program Kerja tidak ditemukan!', 'error');
                        return;
                    }

                    let monthlyDetails = '<p>Tidak ada data ProkerMonthly.</p>';
                    if (proker.proker_monthly && proker.proker_monthly.length > 0) {
                        monthlyDetails =
                            '<table class="table table-bordered"><thead><tr><th>Bulan/Tahun</th><th>Progress Bulan Ini(%)</th><th>Evidence</th></tr></thead><tbody>';
                        proker.proker_monthly.forEach(monthly => {
                            let evidenceLinks = 'Tidak ada file';
                            if (monthly.files && monthly.files.length > 0) {
                                evidenceLinks = monthly.files.map(file => {
                                    const fileUrl = `/storage/uploads/${file.filename}`;
                                    return `<a href="${fileUrl}" target="_blank">${file.filename}</a>`;
                                }).join('<br>');
                            }

                            monthlyDetails += `
                                <tr>
                                    <td>${monthly.date || 'N/A'}</td>
                                    <td>${monthly.percentage}%</td>
                                    <td>${evidenceLinks}</td>
                                </tr>
                            `;
                        });
                        monthlyDetails += '</tbody></table>';
                    }

                    Swal.fire({
                        title: `Detail Proker: ${proker.name}`,
                        html: monthlyDetails,
                        customClass: {
                            popup: 'large-swal'
                        },
                        width: '70%',
                        showCancelButton: true,
                        confirmButtonText: 'Tutup',
                        cancelButtonText: 'Tambah Progress Proker',
                        cancelButtonColor: '#28a745',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            openCreateProkerMonthlyModal(prokerId, proker.name);
                        }
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil detail ProkerMonthly!', 'error');
                }
            });
        }






        function openCreateProkerMonthlyModal(prokerId, prokerName) {
            Swal.fire({
                title: `Tambah Progress Proker untuk: ${prokerName}`,
                html: `
            <div class="form-group text-left">
                <label for="swal-month-year">Bulan & Tahun</label>
                <input type="month" id="swal-month-year" class="form-control mb-3">
            </div>
            <div class="form-group text-left">
                <label for="swal-percentage">Progress Bulan Ini (%)</label>
                <input type="number" id="swal-percentage" class="form-control mb-3" min="0" max="100" placeholder="Masukkan persentase">
            </div>
            <div class="form-group text-left">
                <label for="fileInput">Upload Evidence</label>
                <label class="btn btn-secondary w-100">
                    Pilih File
                    <input type="file" id="fileInput" name="files[]" multiple hidden>
                </label>
                <small id="file-chosen" class="text-muted d-block mt-2">Belum ada file yang dipilih</small>
            </div>
        `,
                didOpen: () => {
                    const fileInput = Swal.getPopup().querySelector('#fileInput');
                    const fileChosen = Swal.getPopup().querySelector('#file-chosen');

                    fileInput.addEventListener('change', () => {
                        if (fileInput.files.length > 0) {
                            fileChosen.textContent = `${fileInput.files.length} file dipilih`;
                        } else {
                            fileChosen.textContent = 'Belum ada file yang dipilih';
                        }
                    });
                },
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const monthYear = document.getElementById('swal-month-year').value;
                    const percentage = document.getElementById('swal-percentage').value;
                    const files = document.getElementById('fileInput').files;

                    if (!monthYear || !percentage) {
                        Swal.showValidationMessage('Harap isi semua kolom!');
                        return false;
                    }

                    if (percentage < 0 || percentage > 100) {
                        Swal.showValidationMessage('Progress harus antara 0 dan 100!');
                        return false;
                    }

                    return {
                        monthYear,
                        percentage,
                        files
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    createProkerMonthly(prokerId, result.value.monthYear, result.value.percentage, result.value
                        .files);
                }
            });
        }


        function createProkerMonthly(prokerId, date, percentage, files) {
            console.log('Files selected:', files); // Debug jumlah file
            let formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('proker_id', prokerId);
            formData.append('date', date);
            formData.append('percentage', percentage);

            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }

            // Debug isi FormData
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            $.ajax({
                url: "{{ route('proker.store-monthly') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire('Berhasil', 'ProkerMonthly berhasil ditambahkan!', 'success');
                    fetchProker(); // Refresh the table
                    location.reload(); // Ini akan merefresh halaman setelah sukses
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Gagal menambahkan ProkerMonthly!', 'error');
                }
            });
        }

        function sendProkerReminder(prokerId) {
            Swal.fire({
                title: 'Kirim Pengingat?',
                text: 'Apakah Anda yakin ingin mengirim pengingat untuk proker ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Kirim',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/prokerlpk/prokerBroadcast/${prokerId}`,
                        type: "GET",
                        success: function(response) {
                            Swal.fire('Berhasil', 'Pengingat proker telah dikirim!', 'success');
                        },
                        error: function() {
                            Swal.fire('Error', 'Gagal mengirim pengingat!', 'error');
                        }
                    });
                }
            });
        }



        // Add proker monthly with flexible percentage
        function addProkerMonthly(prokerId) {
            const currentDate = new Date();
            const currentMonthYear = $('#month_year').val() || currentDate.toISOString().slice(0,
                7); // Gunakan input month_year jika ada

            $.ajax({
                url: `/prokerlpk/get/${prokerId}`,
                type: "GET",
                success: function(proker) {
                    if (!proker) {
                        Swal.fire('Error', 'Program Kerja tidak ditemukan!', 'error');
                        return;
                    }

                    // Ambil progres bulan lalu (default 0 jika tidak ada)
                    const previousPercentage = proker.previous_monthly && proker.previous_monthly.percentage !==
                        undefined ?
                        proker.previous_monthly.percentage :
                        0;
                    const isPercentageFlexible = proker.ispercentageflexible;

                    // Tentukan input persentase berdasarkan ispercentageflexible
                    let percentageInputHtml = '';
                    if (isPercentageFlexible) {
                        percentageInputHtml = `
                    <input type="number" id="swal-percentage" class="form-control mb-2" min="${previousPercentage}" max="100" placeholder="Masukkan persentase" value="${previousPercentage}">
                    <small class="text-muted">Progres minimal: ${previousPercentage}% (berdasarkan bulan lalu)</small>
                `;
                    } else {
                        percentageInputHtml = `
                    <select id="swal-percentage" class="form-control mb-2">
                        <option value="0">0% (Belum Selesai)</option>
                        <option value="100">100% (Selesai)</option>
                    </select>
                    <small class="text-muted">Pilih progres: 0% (belum selesai) atau 100% (selesai)</small>
                `;
                    }

                    Swal.fire({
                        title: `Tambah Progress Proker untuk: ${proker.name}`,
                        html: `
                    <div class="form-group text-left">
                        <label for="swal-month-year">Bulan & Tahun</label>
                        <input type="month" id="swal-month-year" class="form-control mb-2" value="${currentMonthYear}" disabled>
                    </div>
                    <div class="form-group text-left">
                        <label for="swal-percentage">Progress Bulan Ini (%)</label>
                        ${percentageInputHtml}
                    </div>
                    <div class="form-group text-left">
                        <label for="fileInput">Upload Evidence (PDF, JPG, PNG, DOCX, XLSX)</label>
                        <label class="btn btn-secondary w-100">
                            Pilih File
                            <input type="file" id="fileInput" name="files[]" multiple hidden accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx">
                        </label>
                        <small id="file-chosen" class="text-muted d-block mt-2">Belum ada file yang dipilih</small>
                    </div>
                `,
                        didOpen: () => {
                            const fileInput = Swal.getPopup().querySelector('#fileInput');
                            const fileChosen = Swal.getPopup().querySelector('#file-chosen');

                            fileInput.addEventListener('change', () => {
                                fileChosen.textContent = fileInput.files.length > 0 ?
                                    `${fileInput.files.length} file dipilih` :
                                    'Belum ada file yang dipilih';
                            });
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const percentage = document.getElementById('swal-percentage').value;
                            const files = document.getElementById('fileInput').files;

                            if (!percentage && percentage !== '0') {
                                Swal.showValidationMessage('Harap isi kolom progres!');
                                return false;
                            }

                            const percentageNum = parseInt(percentage);
                            if (isPercentageFlexible) {
                                // Validasi untuk ispercentageflexible = true
                                if (percentageNum < previousPercentage || percentageNum > 100) {
                                    Swal.showValidationMessage(
                                        `Progres harus antara ${previousPercentage}% dan 100%!`);
                                    return false;
                                }
                            } else {
                                // Validasi untuk ispercentageflexible = false
                                if (percentageNum !== 0 && percentageNum !== 100) {
                                    Swal.showValidationMessage('Progres hanya boleh 0% atau 100%!');
                                    return false;
                                }
                            }

                            for (let file of files) {
                                const allowedTypes = [
                                    'application/pdf',
                                    'image/jpeg',
                                    'image/png',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                ];
                                if (!allowedTypes.includes(file.type)) {
                                    Swal.showValidationMessage(
                                        `File ${file.name} memiliki format tidak diizinkan!`);
                                    return false;
                                }
                                if (file.size > 5 * 1024 * 1024) {
                                    Swal.showValidationMessage(
                                        `Ukuran file ${file.name} melebihi 5MB!`);
                                    return false;
                                }
                            }

                            return {
                                monthYear: currentMonthYear,
                                percentage: percentageNum,
                                files
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const {
                                monthYear,
                                percentage,
                                files
                            } = result.value;

                            let formData = new FormData();
                            formData.append('_token', "{{ csrf_token() }}");
                            formData.append('proker_id', prokerId);
                            formData.append('date', monthYear);
                            formData.append('percentage', percentage);

                            for (let i = 0; i < files.length; i++) {
                                formData.append('files[]', files[i]);
                            }

                            $.ajax({
                                url: "{{ route('proker.store-monthly') }}",
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function() {
                                    Swal.fire('Berhasil',
                                        'Progress Proker berhasil ditambahkan!',
                                        'success');
                                    fetchProker(); // Refresh tabel
                                },
                                error: function(xhr) {
                                    const msg = xhr.responseJSON?.message ||
                                        'Gagal menambahkan progress!';
                                    Swal.fire('Error', msg, 'error');
                                }
                            });
                        }
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil detail Proker!', 'error');
                }
            });
        }

        function addProkerMonthlyPrevious(prokerId) {
            const currentDate = new Date();
            console.log('currentDate:', currentDate); // Debugging: cek tanggal saat ini
            // Kurangi satu bulan untuk mendapatkan bulan sebelumnya
            const previousDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 0, 1);
            const previousMonthYear = previousDate.toISOString().slice(0, 7); // Format YYYY-MM untuk bulan sebelumnya
            console.log('previousMonthYear:', previousMonthYear); // Debugging: cek bulan sebelumnya


            $.ajax({
                url: `/prokerlpk/get/${prokerId}`,
                type: "GET",
                success: function(proker) {
                    if (!proker) {
                        Swal.fire('Error', 'Program Kerja tidak ditemukan!', 'error');
                        return;
                    }

                    // Ambil progres bulan lalu (default 0 jika tidak ada)
                    const previousPercentage = proker.previous_monthly && proker.previous_monthly.percentage !==
                        undefined ?
                        proker.previous_monthly.percentage : 0;
                    const isPercentageFlexible = proker.ispercentageflexible;

                    // Tentukan input persentase berdasarkan ispercentageflexible
                    let percentageInputHtml = '';
                    if (isPercentageFlexible) {
                        percentageInputHtml = `
                    <input type="number" id="swal-percentage" class="form-control mb-2" min="${previousPercentage}" max="100" placeholder="Masukkan persentase" value="${previousPercentage}">
                    <small class="text-muted">Progres minimal: ${previousPercentage}% (berdasarkan bulan lalu)</small>
                `;
                    } else {
                        percentageInputHtml = `
                    <select id="swal-percentage" class="form-control mb-2">
                        <option value="0">0% (Belum Selesai)</option>
                        <option value="100">100% (Selesai)</option>
                    </select>
                    <small class="text-muted">Pilih progres: 0% (belum selesai) atau 100% (selesai)</small>
                `;
                    }

                    Swal.fire({
                        title: `Tambah Progress Proker untuk: ${proker.name}`,
                        html: `
                    <div class="form-group text-left">
                        <label for="swal-month-year">Bulan & Tahun</label>
                        <input type="month" id="swal-month-year" class="form-control mb-2" value="${previousMonthYear}" disabled>
                    </div>
                    <div class="form-group text-left">
                        <label for="swal-percentage">Progress Bulan Ini (%)</label>
                        ${percentageInputHtml}
                    </div>
                    <div class="form-group text-left">
                        <label for="fileInput">Upload Evidence (PDF, JPG, PNG, DOCX, XLSX)</label>
                        <label class="btn btn-secondary w-100">
                            Pilih File
                            <input type="file" id="fileInput" name="files[]" multiple hidden accept=".pdf,.jpg,.jpeg,.png,.docx,.xlsx">
                        </label>
                        <small id="file-chosen" class="text-muted d-block mt-2">Belum ada file yang dipilih</small>
                    </div>
                `,
                        didOpen: () => {
                            const fileInput = Swal.getPopup().querySelector('#fileInput');
                            const fileChosen = Swal.getPopup().querySelector('#file-chosen');
                            console.log('swal-month-year value:', document.getElementById(
                                'swal-month-year')?.value); // Debugging: cek nilai input

                            fileInput.addEventListener('change', () => {
                                fileChosen.textContent = fileInput.files.length > 0 ?
                                    `${fileInput.files.length} file dipilih` :
                                    'Belum ada file yang dipilih';
                            });
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const percentage = document.getElementById('swal-percentage').value;
                            const files = document.getElementById('fileInput').files;

                            if (!percentage && percentage !== '0') {
                                Swal.showValidationMessage('Harap isi kolom progres!');
                                return false;
                            }

                            const percentageNum = parseInt(percentage);
                            if (isPercentageFlexible) {
                                // Validasi untuk ispercentageflexible = true
                                if (percentageNum < previousPercentage || percentageNum > 100) {
                                    Swal.showValidationMessage(
                                        `Progres harus antara ${previousPercentage}% dan 100%!`);
                                    return false;
                                }
                            } else {
                                // Validasi untuk ispercentageflexible = false
                                if (percentageNum !== 0 && percentageNum !== 100) {
                                    Swal.showValidationMessage('Progres hanya boleh 0% atau 100%!');
                                    return false;
                                }
                            }

                            for (let file of files) {
                                const allowedTypes = [
                                    'application/pdf',
                                    'image/jpeg',
                                    'image/png',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                ];
                                if (!allowedTypes.includes(file.type)) {
                                    Swal.showValidationMessage(
                                        `File ${file.name} memiliki format tidak diizinkan!`);
                                    return false;
                                }
                                if (file.size > 5 * 1024 * 1024) {
                                    Swal.showValidationMessage(
                                        `Ukuran file ${file.name} melebihi 5MB!`);
                                    return false;
                                }
                            }

                            return {
                                monthYear: previousMonthYear,
                                percentage: percentageNum,
                                files
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const {
                                monthYear,
                                percentage,
                                files
                            } = result.value;

                            let formData = new FormData();
                            formData.append('_token', "{{ csrf_token() }}");
                            formData.append('proker_id', prokerId);
                            formData.append('date', monthYear);
                            formData.append('percentage', percentage);

                            for (let i = 0; i < files.length; i++) {
                                formData.append('files[]', files[i]);
                            }

                            $.ajax({
                                url: "{{ route('proker.store-monthly') }}",
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function() {
                                    Swal.fire('Berhasil',
                                        'Progress Proker berhasil ditambahkan!',
                                        'success');
                                    fetchProker(); // Refresh tabel
                                },
                                error: function(xhr) {
                                    const msg = xhr.responseJSON?.message ||
                                        'Gagal menambahkan progress!';
                                    Swal.fire('Error', msg, 'error');
                                }
                            });
                        }
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil detail Proker!', 'error');
                }
            });
        }
    </script>
@endpush
