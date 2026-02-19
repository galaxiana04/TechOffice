@extends('layouts.universal')

@section('container2')
    <div class="content-header bg-white shadow-sm py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-gray-100 px-4 py-2 rounded-lg float-left">
                        <li class="breadcrumb-item"><a href="#"
                                class="text-gray-700 hover:text-blue-600 font-medium">Program Kerja dan LPK</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container mx-auto px-4 py-6">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-xl rounded-2xl overflow-hidden bg-white border border-gray-100">
                    <div class="card-header bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-5 px-6">
                        <h3 class="card-title text-3xl font-extrabold tracking-tight">Daftar Unit dan Progres Proker -
                            {{ \Carbon\Carbon::parse($currentMonthYear)->isoFormat('MMMM Y') }}</h3>
                    </div>
                    <div class="card-body p-8">
                        @if (session('success'))
                            <div
                                class="alert alert-success bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-xl mb-6 shadow-sm">
                                {{ session('success') }}
                            </div>
                        @endif

                        <table id="unitTable" class="table table-bordered table-hover w-full mt-4">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-center align-middle w-12 py-4 text-sm font-bold text-gray-800">No</th>
                                    <th class="text-center align-middle py-4 text-sm font-bold text-gray-800">Nama Unit</th>
                                    <th class="text-center align-middle py-4 text-sm font-bold text-gray-800">Progres</th>
                                    <th class="text-center align-middle py-4 text-sm font-bold text-gray-800">Persentase
                                        Selesai</th>
                                    <th class="text-center align-middle w-36 py-4 text-sm font-bold text-gray-800">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="unitTableBody">
                                @if ($units->isEmpty())
                                    <tr>
                                        <td colspan="8" class="text-center py-8 text-gray-600 font-medium">Tidak ada data
                                            unit</td>
                                    </tr>
                                @else
                                    @foreach ($units as $unit)
                                        <tr class="hover:bg-blue-50 transition duration-300">
                                            <td class="text-center align-middle py-5 font-medium text-gray-700">
                                                {{ $loop->iteration }}</td>
                                            <td class="align-middle py-5 font-semibold text-gray-900">{{ $unit->name }}
                                            </td>
                                            <td class="text-center align-middle py-5">
                                                <span
                                                    class="badge bg-yellow-500 text-white px-4 py-2 rounded-full text-lg font-medium mb-2 block shadow-sm">
                                                    Total Proker: {{ $unit->stats['total_prokers'] }}
                                                </span>
                                                <span
                                                    class="badge bg-green-300 text-gray-900 px-4 py-2 rounded-full text-lg font-medium mb-2 block shadow-sm">
                                                    Proker Selesai: {{ $unit->stats['completed_prokers'] }}
                                                </span>
                                                <span
                                                    class="badge bg-red-500 text-white px-4 py-2 rounded-full text-lg font-medium block shadow-sm">
                                                    Proker Belum Selesai: {{ $unit->stats['incomplete_prokers'] }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle py-5">
                                                <span
                                                    class="badge bg-green-500 text-white px-5 py-2 rounded-full text-xl font-bold shadow-md">
                                                    {{ $unit->stats['completed_percentage'] }}%
                                                </span>
                                            </td>
                                            <td class="text-center align-middle py-5">
                                                <button
                                                    class="btn bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white px-5 py-2 rounded-lg shadow-lg transition duration-300 transform hover:scale-105"
                                                    onclick="window.location='{{ route('proker.show', $unit->id) }}'">
                                                    <i class="fas fa-eye mr-2"></i> Lihat Proker
                                                </button>
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

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .large-swal {
            width: 800px !important;
            height: auto !important;
            max-height: 90vh !important;
            overflow-y: auto !important;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .table th,
        .table td {
            padding: 1.25rem;
            vertical-align: middle;
        }

        .table thead th {
            letter-spacing: 0.05rem;
            text-transform: uppercase;
        }

        .breadcrumb {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            transition: background-color 0.3s ease;
        }

        .breadcrumb:hover {
            background-color: #edf2f7;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .swal2-popup {
            border-radius: 1.25rem !important;
        }

        .swal2-html-container .form-control {
            margin-bottom: 1.25rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script>
        const userId = {{ $userId ?? 'null' }};
        const userUnitId = {{ $unitId ?? 'null' }};
        const currentMonthYear = '{{ $currentMonthYear }}';

        $(document).ready(function() {
            $('#unitTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });

        function formatMonthYear(dateString) {
            const date = new Date(dateString);
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${month}/${year}`;
        }

        function viewUnitProkers(unitId) {
            $.ajax({
                url: "{{ route('proker.searchProker') }}",
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                    month_year: currentMonthYear,
                    unit_id: unitId
                },
                success: function(response) {
                    let htmlContent = `
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Proker Dibuat</th>
                                    <th>Jenis</th>
                                    <th>Nama</th>
                                    <th>Progress Bulan Ini (%)</th>
                                    <th>Evidence</th>
                                    ${userId === 37 || userId === 1 ? '<th>Status</th>' : ''}
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    if (response.length > 0) {
                        response.forEach((proker, index) => {
                            let currentMonthly = proker.current_monthly;
                            let actionButtons = '<div class="d-flex justify-content-center gap-2">';
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

                            let currentProgressCell =
                                '<span class="text-muted">Belum Ada Progress</span>';
                            let fileCell = '<span class="text-muted">Tidak ada file</span>';
                            if (currentMonthly && currentMonthly.percentage !== undefined) {
                                currentProgressCell = `
                                    <div class="progress my-2" style="height: 20px;">
                                        <div class="progress-bar ${currentMonthly.percentage == 100 ? 'bg-success' : 'bg-warning'}" 
                                             role="progressbar" 
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

                            let statusCell = '';
                            if (userId === 37 || userId === 1) {
                                statusCell = `
                                    <td class="align-middle text-center">
                                        <span class="badge ${proker.ishide ? 'bg-danger' : 'bg-success'}">
                                            ${proker.ishide ? 'Tersembunyi' : 'Terlihat'}
                                        </span>
                                        <span class="badge ${proker.ispercentageflexible ? 'bg-info' : 'bg-secondary'}">
                                            ${proker.ispercentageflexible ? 'Persentase Bebas' : 'Persentase 0 / 100'}
                                        </span>
                                    </td>
                                `;
                            }

                            htmlContent += `
                                <tr>
                                    <td class="text-center align-middle">${index + 1}</td>
                                    <td class="text-center align-middle">${formatMonthYear(proker.proker_created_at)}</td>
                                    <td class="align-middle">${proker.kind === 'proker' ? 'Proker' : (proker.kind === 'lpk' ? 'LPK' : '-')}</td>
                                    <td class="align-middle">${proker.name}</td>
                                    <td class="align-middle">${currentProgressCell}</td>
                                    <td class="align-middle">${fileCell}</td>
                                    ${statusCell}
                                    <td class="text-center align-middle">${actionButtons}</td>
                                </tr>
                            `;
                        });
                    } else {
                        htmlContent +=
                            '<tr><td colspan="8" class="text-center">Tidak ada proker untuk unit ini</td></tr>';
                    }

                    htmlContent += '</tbody></table>';

                    Swal.fire({
                        title: 'Daftar Proker Unit',
                        html: htmlContent,
                        customClass: {
                            popup: 'large-swal'
                        },
                        width: '90%',
                        showConfirmButton: true,
                        confirmButtonText: 'Tutup'
                    });
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Gagal mengambil data proker!', 'error');
                }
            });
        }

        // Fungsi-fungsi lainnya (openCreateModal, toggleProker, deleteProker, dll.) tetap sama seperti di kode asli
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

        function createProker(name, kind, unitId, prokerCreatedAt, ispercentageflexible) {
            $.ajax({
                url: "{{ route('proker.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name,
                    kind: kind,
                    unit_id: unitId,
                    proker_created_at: prokerCreatedAt,
                    ispercentageflexible: ispercentageflexible
                },
                success: function(response) {
                    Swal.fire('Berhasil', response.message, 'success');
                    location.reload(); // Refresh halaman untuk memperbarui daftar unit
                },
                error: function() {
                    Swal.fire('Error', 'Gagal menambahkan proker!', 'error');
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
                        type: "PATCH",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire('Berhasil', response.message, 'success').then(() => {
                                location.reload();
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
                            location.reload();
                        },
                        error: function() {
                            Swal.fire('Error', 'Gagal menghapus proker!', 'error');
                        }
                    });
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
                        monthlyDetails = `
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Bulan/Tahun</th>
                                        <th>Progress Bulan Ini (%)</th>
                                        <th>Evidence</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
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
            $.ajax({
                url: `/prokerlpk/get/${prokerId}`,
                type: "GET",
                success: function(proker) {
                    if (!proker) {
                        Swal.fire('Error', 'Program Kerja tidak ditemukan!', 'error');
                        return;
                    }

                    const previousPercentage = proker.previous_monthly && proker.previous_monthly.percentage !==
                        undefined ?
                        proker.previous_monthly.percentage :
                        0;
                    const isPercentageFlexible = proker.ispercentageflexible;

                    let percentageInputHtml = isPercentageFlexible ?
                        `
                            <input type="number" id="swal-percentage" class="form-control mb-3" min="${previousPercentage}" max="100" placeholder="Masukkan persentase" value="${previousPercentage}">
                            <small class="text-muted">Progres minimal: ${previousPercentage}% (berdasarkan bulan lalu)</small>
                        ` :
                        `
                            <select id="swal-percentage" class="form-control mb-3">
                                <option value="0">0% (Belum Selesai)</option>
                                <option value="100">100% (Selesai)</option>
                            </select>
                            <small class="text-muted">Pilih progres: 0% (belum selesai) atau 100% (selesai)</small>
                        `;

                    Swal.fire({
                        title: `Tambah Progress Proker untuk: ${prokerName}`,
                        html: `
                            <div class="form-group text-left">
                                <label for="swal-month-year">Bulan & Tahun</label>
                                <input type="month" id="swal-month-year" class="form-control mb-3" value="${currentMonthYear}" disabled>
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
                                if (percentageNum < previousPercentage || percentageNum > 100) {
                                    Swal.showValidationMessage(
                                        `Progres harus antara ${previousPercentage}% dan 100%!`);
                                    return false;
                                }
                            } else {
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
                                    location.reload();
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
    </script>
@endpush
