
@extends('layouts.universal')

@section('container2')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="text-gradient-primary font-weight-bold">
                        <i class="fas fa-file-excel mr-2 animate-pulse"></i>Smart Spreadsheet Progress
                    </h1>
                    <p class="text-muted small mb-0">Input massal dengan fitur Drag-to-Fill & Copy-Paste Excel</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('container3')
    <div class="card card-modern">
        <div class="card-header-gradient">
            <h3 class="mb-0"><i class="fas fa-th mr-2"></i>Konfigurasi Penginputan</h3>
            <small class="d-block mt-2 opacity-75">Klik kanan pada tabel untuk menambah/menghapus baris. Gunakan pojok kanan
                bawah sel untuk drag-fill.</small>
        </div>

        <div class="card-body p-4">
            <div class="row mb-4 align-items-center">
                <div class="col-lg-7">
                    <label class="font-weight-bold">Format Struktur Tabel</label>
                    <select id="formatSelector" class="form-control" onchange="initSpreadsheet()">
                        <option value="">-- Pilih Format Tampilan --</option>
                        <option value="formatprogress">📊 Format Progres (Standard)</option>
                        <option value="formatprogresskhusus">⚡ Format Progres Khusus</option>
                        <option value="formatrencana">📝 Format Rencana</option>
                        <option value="formatupdatelink">🔗 Format Update Link</option>
                    </select>
                </div>
                <div class="col-lg-5">
                    <div class="stats-card" style="display: none;" id="statsCard">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted small font-weight-bold">Total Baris Data</div>
                                <div class="h2 mb-0 font-weight-bold text-primary" id="rowCounter">0</div>
                            </div>
                            <i class="fas fa-file-invoice fa-2x text-primary opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div id="tableContainer" style="display:none;">
                <div id="spreadsheet" class="shadow-sm"></div>

                <div
                    class="action-toolbar bg-light p-3 border rounded-bottom d-flex justify-content-between align-items-center mt-3">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary-gradient btn-modern mr-2"
                            onclick="tableInstance.insertRow()">
                            <i class="fas fa-plus-circle mr-2"></i>Tambah Baris
                        </button>
                        <button type="button" class="btn btn-success-gradient btn-modern" onclick="submitData()">
                            <i class="fas fa-save mr-2"></i>Simpan Excel
                        </button>
                    </div>
                    <div class="text-muted small">
                        <i class="fas fa-info-circle mr-1"></i>Tips: Klik kanan untuk opsi baris. <b>Ctrl+V</b> untuk paste
                        dari Excel.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://jsuites.net/v4/jsuites.js"></script>
    <script src="https://bossanova.uk/jspreadsheet/v4/jexcel.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        let tableInstance;
        // Data pendukung dari Laravel
        const projects = @json($newreports).map(u => u.title);
        const units = @json($units).map(u => u.name);
        const users = @json($users).map(u => u.initial);
        const docKinds = @json($documentKinds).map(d => d.name);

        function initSpreadsheet() {
            const format = document.getElementById('formatSelector').value;
            if (!format) {
                $('#tableContainer, #statsCard').fadeOut();
                return;
            }

            $('#tableContainer, #statsCard').fadeIn();
            if (tableInstance) jspreadsheet.destroy(document.getElementById('spreadsheet'));

            let columns = [{
                type: 'text',
                title: 'No',
                width: 50,
            }, {
                type: 'dropdown',
                title: 'Target Proyek *',
                width: 250,
                source: projects,
                name: 'newreport_id',
                autocomplete: true,
                tags: true
            }];

            // Konfigurasi Kolom berdasarkan Format
            if (format === 'formatprogress') {
                columns.push({
                    type: 'text',
                    title: 'No Dokumen *',
                    width: 180,
                    name: 'nodokumen'
                }, {
                    type: 'text',
                    title: 'Nama Dokumen',
                    width: 250,
                    name: 'namadokumen'
                }, {
                    type: 'dropdown',
                    title: 'Unit',
                    width: 100,
                    source: units,
                    name: 'unit',
                    autocomplete: true,
                    tags: true

                }, {
                    type: 'dropdown',
                    title: 'Paper',
                    width: 60,
                    name: 'papersize',
                    source: ['A1', 'A2', 'A3', 'A4'] // hanya opsi ini
                }, {
                    type: 'numeric',
                    title: 'Sheet',
                    width: 60,
                    name: 'sheet'
                }, {
                    type: 'dropdown',
                    title: 'Rev',
                    width: 50,
                    name: 'rev',
                    source: ['0', ...Array.from({
                        length: 26
                    }, (_, i) => String.fromCharCode(65 + i))], // ['0','A'..'Z']
                    autocomplete: true,
                    tags: true,
                    validate: function(el, value) {
                        const valid = /^([0]|[A-Z])$/.test(value); // hanya 0 atau huruf A-Z
                        el.style.backgroundColor = valid ? '' : '#fdd';
                        return valid;
                    }
                }, {
                    type: 'text',
                    title: 'Rev.DD',
                    width: 50,
                    name: 'rev_dd'
                }, {
                    type: 'calendar',
                    title: 'Tgl Drawing',
                    width: 120,
                    name: 'drawing_date',
                    options: {
                        format: 'DD-MM-YYYY'
                    }
                }, {
                    type: 'calendar',
                    title: 'Tgl Release',
                    width: 120,
                    name: 'realisasidate',
                    options: {
                        format: 'DD-MM-YYYY'
                    }
                }, {
                    type: 'text',
                    title: 'Drafter',
                    width: 80,
                    source: users,
                    name: 'drafter',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'text',
                    title: 'Checker',
                    width: 80,
                    source: users,
                    name: 'checker',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'text',
                    title: 'Approval',
                    width: 80,
                    source: users,
                    name: 'approval',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'text',
                    title: 'Welding',
                    width: 80,
                    source: users,
                    name: 'welding',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'dropdown',
                    title: 'Jenis Dok',
                    width: 120,
                    source: docKinds,
                    name: 'jenisdokumen',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'text',
                    title: 'DCR',
                    width: 50,
                    name: 'dcr'
                }, {
                    type: 'dropdown',
                    title: 'Status',
                    width: 110,
                    source: ['RELEASED'],
                    name: 'status',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'text',
                    title: 'File URL',
                    width: 250,
                    name: 'fileurl'
                });
            } else if (format === 'formatprogresskhusus') {
                columns.push({
                    type: 'dropdown',
                    title: 'Unit',
                    width: 100,
                    source: units,
                    name: 'unit',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'text',
                    title: 'No Dokumen *',
                    width: 180,
                    name: 'nodokumen'
                }, {
                    type: 'text',
                    title: 'Nama Dokumen',
                    width: 250,
                    name: 'namadokumen'
                }, {
                    type: 'dropdown',
                    title: 'Rev',
                    width: 50,
                    name: 'rev',
                    source: ['0', ...Array.from({
                        length: 26
                    }, (_, i) => String.fromCharCode(65 + i))], // ['0','A'..'Z']
                    autocomplete: true,
                    tags: true,
                    validate: function(el, value) {
                        const valid = /^([0]|[A-Z])$/.test(value); // hanya 0 atau huruf A-Z
                        el.style.backgroundColor = valid ? '' : '#fdd';
                        return valid;
                    }
                }, {
                    type: 'text',
                    title: 'Level',
                    width: 50,
                    name: 'level'
                }, {
                    type: 'dropdown',
                    title: 'Drafter',
                    width: 80,
                    source: users,
                    name: 'drafter',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'dropdown',
                    title: 'Checker',
                    width: 80,
                    source: users,
                    name: 'checker',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'calendar',
                    title: 'Deadline',
                    width: 120,
                    name: 'deadlinereleasedate',
                    options: {
                        format: 'YYYY-MM-DD'
                    }
                }, {
                    type: 'calendar',
                    title: 'Tgl Release',
                    width: 120,
                    name: 'realisasidate',
                    options: {
                        format: 'YYYY-MM-DD'
                    }
                }, {
                    type: 'dropdown',
                    title: 'Status',
                    width: 100,
                    source: ['RELEASED', 'PROGRESS'],
                    name: 'status',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'dropdown',
                    title: 'Paper',
                    width: 60,
                    name: 'papersize',
                    source: ['A1', 'A2', 'A3', 'A4'] // hanya opsi ini
                }, {
                    type: 'numeric',
                    title: 'Sheet',
                    width: 60,
                    name: 'sheet'
                });
            } else if (format === 'formatrencana') {
                columns.push({
                    type: 'dropdown',
                    title: 'Unit',
                    width: 100,
                    source: units,
                    name: 'unit',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'text',
                    title: 'No Dokumen *',
                    width: 200,
                    name: 'nodokumen'
                }, {
                    type: 'text',
                    title: 'Nama Dokumen',
                    width: 300,
                    name: 'namadokumen'
                });
            } else if (format === 'formatupdatelink') {
                columns.push({
                    type: 'text',
                    title: 'No Dokumen *',
                    width: 200,
                    name: 'nodokumen'
                }, {
                    type: 'dropdown',
                    title: 'Unit',
                    width: 100,
                    source: units,
                    name: 'unit',
                    autocomplete: true,
                    tags: true
                }, {
                    type: 'dropdown',
                    title: 'Rev',
                    width: 50,
                    name: 'rev',
                    source: ['0', ...Array.from({
                        length: 26
                    }, (_, i) => String.fromCharCode(65 + i))], // ['0','A'..'Z']
                    autocomplete: true,
                    tags: true,
                    validate: function(el, value) {
                        const valid = /^([0]|[A-Z])$/.test(value); // hanya 0 atau huruf A-Z
                        el.style.backgroundColor = valid ? '' : '#fdd';
                        return valid;
                    }
                }, {
                    type: 'text',
                    title: 'File URL',
                    width: 350,
                    name: 'fileurl'
                });
            }

            tableInstance = jspreadsheet(document.getElementById('spreadsheet'), {
                data: [
                    []
                ],
                columns: columns,
                minDimensions: [columns.length, 10],
                columnSorting: true,
                onchange: updateRowCounter,
                oninsertrow: updateRowCounter,
                ondeleterow: updateRowCounter,
                allowInsertColumn: false,
                parseFormulas: false,

            });
        }

        function updateRowCounter() {
            const data = tableInstance.getJson();
            // Hitung baris yang kolom pertamanya tidak kosong
            const count = data.filter(row => row[0] !== "" && row[0] !== null).length;
            $('#rowCounter').text(count);
        }

        function submitData() {
            if (!tableInstance) {
                Swal.fire('Error', 'Tabel belum dibuat!', 'error');
                return;
            }

            // Ambil data sebagai array biasa
            const data = tableInstance.getData();

            // Ambil header langsung dari config awal
            const headers = tableInstance.options.columns.map(col => col.title);

            // Filter baris kosong (kolom pertama kosong)
            const filteredData = data.filter(row => row[0] !== "" && row[0] !== null);

            if (filteredData.length === 0) {
                Swal.fire('Info', 'Tidak ada data untuk disimpan.', 'info');
                return;
            }

            const worksheetData = [headers, ...filteredData];

            const ws = XLSX.utils.aoa_to_sheet(worksheetData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Progress");

            XLSX.writeFile(wb, "Smart_Progress.xlsx");
        }
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="https://bossanova.uk/jspreadsheet/v4/jexcel.css" type="text/css" />

    <link rel="stylesheet" href="https://jsuites.net/v4/jsuites.css" type="text/css" />

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .text-gradient-primary {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-modern {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .card-header-gradient {
            background: var(--primary-gradient);
            color: white;
            padding: 25px;
            border: none;
        }

        /* Jspreadsheet Styling Overrides */
        .jexcel_container {
            width: 100% !important;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .jexcel>thead>tr>td {
            background-color: #667eea !important;
            color: white !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            font-size: 11px;
            padding: 10px !important;
            border: 1px solid #5a6fd6 !important;
        }

        .jexcel>tbody>tr>td {
            padding: 8px !important;
        }

        .stats-card {
            background: #f8faff;
            border-radius: 15px;
            padding: 20px;
            border-left: 6px solid #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }

        #formatSelector {
            height: 3.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 15px;
            border: 2px solid #e8e8e8;
            transition: 0.3s;
        }

        .btn-modern {
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            border: none;
            transition: 0.3s;
        }

        .btn-primary-gradient {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-success-gradient {
            background: var(--success-gradient);
            color: white;
        }

        .jexcel_freezethrough {
            z-index: 5;
        }

        .jexcel td.jexcel_error {
            background-color: transparent !important;
            color: inherit !important;
        }
    </style>
@endpush