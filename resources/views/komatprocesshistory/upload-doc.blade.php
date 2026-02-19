@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left shadow-sm rounded">
                        <li class="breadcrumb-item"><a href="{{ route('komatprocesshistory.showuploaddoc') }}"
                                class="text-indigo">Upload Dokumen Komat</a></li>
                        <li class="breadcrumb-item active text-muted">Upload Dokumen</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('container3')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="error-container mb-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>

                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-gradient-indigo text-white p-4 rounded-top">
                        <h3 class="card-title mb-0 fw-bold">Upload Dokumen Komat</h3>
                    </div>
                    <div class="card-body p-5">
                        <form id="uploadLogistikForm" action="{{ route('komatprocesshistory.uploaddoc') }}" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf

                            <div class="form-group mb-4">
                                <label for="kodematerial" class="fw-bold text-dark mb-2">Kode Material
                                    (Kodematerial):</label>
                                <select id="kodematerial" name="kodematerial"
                                    class="form-select form-select-lg rounded-3 shadow-sm select2" required>
                                    <option value="" disabled selected>-- Pilih atau ketik kode material --</option>
                                    @foreach ($komats as $komat)
                                        <option value="{{ $komat->kodematerial }}">
                                            {{ $komat->kodematerial }} - {{ $komat->material }} -
                                            {{ $komat->newbom->unit ?? 'No Unit' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted mt-2">Pilih dari daftar atau ketik kode material
                                    baru.</small>
                            </div>
                            <div class="form-group mb-4">
                                <label for="note" class="fw-bold text-dark mb-2">Catatan (Note):</label>
                                <textarea name="note" id="note" class="form-control rounded-3 shadow-sm" rows="4"
                                    placeholder="Masukkan catatan untuk dokumen ini (opsional)"></textarea>
                                <small class="form-text text-muted mt-2">Masukkan catatan tambahan untuk dokumen ini, jika
                                    ada.</small>
                            </div>
                            <button type="button" class="btn btn-primary btn-lg rounded-3 shadow-sm ms-2"
                                id="searchReferenceBtn">Search Reference</button>
                            <div id="reference-results" class="mb-4">
                                <h6 class="fw-bold text-dark">Reference Results:</h6>
                                <ul class="list-group reference-results-list"></ul>
                            </div>

                            <div class="form-group mb-4">
                                <label for="proyek_type_id" class="fw-bold text-dark mb-2">Pilih Tipe Proyek
                                    (Multiple):</label>
                                <select name="proyek_type_id[]" id="proyek_type_id"
                                    class="form-select form-select-lg rounded-3 shadow-sm select2" multiple required>
                                    <option value="" disabled>-- Pilih proyek --</option>
                                    @foreach ($listproject as $project)
                                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted mt-2">Pilih satu atau lebih tipe proyek.</small>
                                <div id="selected-projects-list" class="mt-3">
                                    <h6 class="fw-bold text-dark">Proyek Terpilih:</h6>
                                    <ul class="list-group selected-projects"></ul>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label for="authority_level" class="fw-bold text-dark mb-2">Pilih Authority
                                    Level:</label>
                                <select name="authority_level" id="authority_level"
                                    class="form-select form-select-lg rounded-3 shadow-sm" required>
                                    <option value="" disabled selected>-- Pilih authority level --</option>
                                    <option value="verifiednotneeded">Purchaser</option>
                                    <option value="managerneeded">Manager</option>
                                    <option value="seniormanagerneeded">Manager + SM</option>
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label for="komat_supplier_id" class="fw-bold text-dark mb-2">Pilih Satu
                                    Supplier:</label>
                                <div class="form-group mb-3 position-relative">
                                    <select name="komat_supplier_id" id="komat_supplier_id" class="form-select select2"
                                        required>
                                        <option value="" disabled selected>-- Pilih supplier --</option>
                                        @foreach ($komatSupplier as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted mt-2">Ketik untuk mencari atau tambahkan
                                        supplier
                                        baru.</small>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label for="requirement_list_id" class="fw-bold text-dark mb-2">Pilih Requirement
                                    (Multiple):</label>
                                <select name="requirement_list_id[]" id="requirement_list_id"
                                    class="form-select form-select-lg rounded-3 shadow-sm select2" multiple required>
                                    <option value="" disabled>-- Pilih requirement --</option>
                                    @foreach ($requirements as $requirement)
                                        <option value="{{ $requirement->id }}">
                                            {{ $requirement->name ?? 'Requirement ' . $requirement->id }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted mt-2">Pilih satu atau lebih requirement untuk
                                    upload
                                    file. Ketik untuk menambahkan requirement baru.</small>
                                <button type="button" class="btn btn-primary btn-sm rounded-3 shadow-sm mt-2"
                                    id="addNewRequirementBtn">
                                    Tambah Requirement Baru
                                </button>
                                <div id="selected-requirements-list" class="mt-3">
                                    <h6 class="fw-bold text-dark">Requirement Terpilih:</h6>
                                    <ul class="list-group selected-requirements"></ul>
                                </div>
                            </div>

                            <input type="hidden" name="category" value="komat">

                            <button type="submit"
                                class="btn btn-success btn-lg w-100 rounded-3 shadow-sm hover-scale">Upload
                                Dokumen</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* General Form Styling */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .bg-gradient-indigo {
            background: linear-gradient(45deg, #4B0082, #6B7280);
        }

        .form-control,
        .form-select {
            border: 1px solid #d1d5db;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4B0082;
            box-shadow: 0 0 0 3px rgba(75, 0, 130, 0.2);
        }

        .btn-success {
            background-color: #10B981;
            border-color: #10B981;
            transition: transform 0.2s ease, background-color 0.3s ease;
        }

        .btn-success:hover {
            background-color: #059669;
            border-color: #059669;
            transform: scale(1.02);
        }

        .hover-scale {
            transition: transform 0.2s ease;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }

        /* Supplier, Project, and Requirement Selection Styling */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--multiple,
        .select2-container--default .select2-selection--single {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background-color: #f9fafb;
            padding: 8px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            min-height: calc(2.75rem + 2px);
        }

        .select2-container--default .select2-selection--multiple:focus-within,
        .select2-container--default .select2-selection--single:focus-within {
            border-color: #4B0082;
            box-shadow: 0 0 0 3px rgba(75, 0, 130, 0.2);
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: none;
        }

        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .select2-results__option {
            padding: 8px 12px;
            transition: background-color 0.2s ease;
        }

        .select2-results__option--highlighted {
            background-color: #4B0082 !important;
            color: white !important;
        }

        .select2-results__option[aria-selected="true"] {
            background-color: #e5e7eb;
        }

        /* Selected Projects and Requirements List Styling */
        .selected-projects,
        .selected-requirements {
            list-style: none;
            padding: 0;
        }

        .selected-projects .list-group-item,
        .selected-requirements .list-group-item {
            background-color: #e5e7eb;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 8px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .selected-projects .list-group-item:hover,
        .selected-requirements .list-group-item:hover {
            background-color: #d1d5db;
            transform: translateY(-2px);
        }

        .selected-projects .delete-project-btn,
        .selected-requirements .delete-requirement-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .selected-projects .delete-project-btn:hover,
        .selected-requirements .delete-requirement-btn:hover {
            background-color: #b02a37;
        }

        .selected-requirements .file-input-wrapper {
            margin-top: 8px;
        }

        .selected-requirements .file-input-wrapper input[type="file"] {
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 4px;
        }

        /* Error Alert */
        .alert-danger {
            border-left: 4px solid #dc3545;
            background-color: #fef2f2;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            const form = document.getElementById('uploadLogistikForm');
            const selectedProjectsList = $('.selected-projects');
            const selectedRequirementsList = $('.selected-requirements');
            const referenceResultsList = $('.reference-results-list');

            // Initialize Select2 for kodematerial
            $('#kodematerial').select2({
                placeholder: "Pilih atau ketik kode material",
                allowClear: true,
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            // Initialize Select2 for project types
            $('#proyek_type_id').select2({
                placeholder: "Pilih satu atau lebih tipe proyek",
                allowClear: true,
                closeOnSelect: false,
                minimumResultsForSearch: 1,
            });

            // Initialize Select2 for requirements
            $('#requirement_list_id').select2({
                placeholder: "Pilih satu atau lebih requirement",
                allowClear: true,
                closeOnSelect: false,
                minimumResultsForSearch: 1,
                tags: true, // Allow adding new requirements via tags
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '' || /\s/.test(term)) { // Check for spaces
                        return null; // Prevent creation if term contains spaces
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                },
                language: {
                    noResults: function() {
                        return ''; // Suppress "No Results Found" message
                    },
                    inputTooShort: function() {
                        return 'Ketik nama requirement tanpa spasi';
                    }
                }
            });

            // Function to add a new requirement via AJAX
            function addNewRequirement(name, description, callback) {
                // Validate no spaces in name
                if (/\s/.test(name)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Nama requirement tidak boleh mengandung spasi.',
                        confirmButtonColor: '#dc3545'
                    });
                    callback(false);
                    return;
                }

                $.ajax({
                    url: '{{ route('komatprocesshistory.addRequirement') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        name,
                        description
                    },
                    success: function(data) {
                        if (data.success) {
                            const newOption = new Option(data.requirement.name, data.requirement.id,
                                true, true);
                            $('#requirement_list_id').append(newOption).trigger('change');
                            updateSelectedRequirementsList();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Requirement berhasil ditambahkan.',
                                confirmButtonColor: '#10B981'
                            });
                            callback(true);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.error ||
                                    'Terjadi kesalahan saat menambahkan requirement.',
                                confirmButtonColor: '#dc3545'
                            });
                            callback(false);
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.error || 'Server error',
                            confirmButtonColor: '#dc3545'
                        });
                        callback(false);
                    }
                });
            }

            // Handle "Add New Requirement" button click
            $('#addNewRequirementBtn').on('click', function() {
                Swal.fire({
                    title: 'Tambah Requirement Baru',
                    html: `
                        <input type="text" id="requirementName" class="swal2-input" placeholder="Nama Requirement (tanpa spasi)" required pattern="[^\\s]+">
                        <textarea id="requirementDescription" class="swal2-textarea" placeholder="Deskripsi Requirement (opsional)"></textarea>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280',
                    preConfirm: () => {
                        const name = Swal.getPopup().querySelector('#requirementName').value;
                        const description = Swal.getPopup().querySelector(
                            '#requirementDescription').value;
                        if (!name) {
                            Swal.showValidationMessage('Nama requirement harus diisi');
                            return false;
                        }
                        if (/\s/.test(name)) {
                            Swal.showValidationMessage(
                                'Nama requirement tidak boleh mengandung spasi');
                            return false;
                        }
                        return {
                            name,
                            description
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const {
                            name,
                            description
                        } = result.value;
                        addNewRequirement(name, description, function(success) {
                            if (success) {
                                // Optionally hide the button, but not necessary since we want it always visible
                                // $('#addNewRequirementBtn').hide();
                            }
                        });
                    }
                });
            });

            // Handle new requirement creation via Select2 tag
            $('#requirement_list_id').on('select2:select', function(e) {
                var data = e.params.data;
                if (data.newTag) {
                    Swal.fire({
                        title: 'Tambah Requirement Baru',
                        html: `
                            <input type="text" id="requirementName" class="swal2-input" value="${data.text}" placeholder="Nama Requirement (tanpa spasi)" required pattern="[^\\s]+">
                            <textarea id="requirementDescription" class="swal2-textarea" placeholder="Deskripsi Requirement (opsional)"></textarea>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#6B7280',
                        preConfirm: () => {
                            const name = Swal.getPopup().querySelector('#requirementName')
                            .value;
                            const description = Swal.getPopup().querySelector(
                                '#requirementDescription').value;
                            if (!name) {
                                Swal.showValidationMessage('Nama requirement harus diisi');
                                return false;
                            }
                            if (/\s/.test(name)) {
                                Swal.showValidationMessage(
                                    'Nama requirement tidak boleh mengandung spasi');
                                return false;
                            }
                            return {
                                name,
                                description
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const {
                                name,
                                description
                            } = result.value;
                            addNewRequirement(name, description, function(success) {
                                if (!success) {
                                    $('#requirement_list_id').val(null).trigger('change');
                                }
                            });
                        } else {
                            $('#requirement_list_id').val(null).trigger('change');
                        }
                    });
                }
            });

            // Function to update the selected projects list
            function updateSelectedProjectsList() {
                const selectedOptions = $('#proyek_type_id').select2('data');
                selectedProjectsList.empty();
                selectedOptions.forEach(option => {
                    const listItem = `
                        <li class="list-group-item" data-id="${option.id}">
                            ${option.text}
                            <button type="button" class="delete-project-btn">Hapus</button>
                        </li>
                    `;
                    selectedProjectsList.append(listItem);
                });
            }

            // Function to update the selected requirements list
            function updateSelectedRequirementsList() {
                const selectedOptions = $('#requirement_list_id').select2('data');
                selectedRequirementsList.empty();
                selectedOptions.forEach(option => {
                    const listItem = `
                        <li class="list-group-item" data-id="${option.id}">
                            ${option.text}
                            <button type="button" class="delete-requirement-btn">Hapus</button>
                            <div class="file-input-wrapper mt-2">
                                <input type="file" name="file_${option.id}[]"
                                    class="form-control rounded-3 shadow-sm" multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png,.jpeg" required />
                                <small class="form-text text-muted">Pilih file(s) untuk requirement ini. Format: pdf, doc, xls, jpg, png</small>
                            </div>
                        </li>
                    `;
                    selectedRequirementsList.append(listItem);
                });
            }

            // Update lists on select/unselect
            $('#proyek_type_id').on('select2:select select2:unselect', function(e) {
                updateSelectedProjectsList();
            });

            $('#requirement_list_id').on('select2:select select2:unselect', function(e) {
                updateSelectedRequirementsList();
            });

            // Handle delete button click for projects
            selectedProjectsList.on('click', '.delete-project-btn', function() {
                const projectId = String($(this).parent().data('id'));
                const currentValues = $('#proyek_type_id').val() || [];
                const updatedValues = currentValues.filter(id => id !== projectId);
                $('#proyek_type_id').val(updatedValues).trigger('change');
                updateSelectedProjectsList();
            });

            // Handle delete button click for requirements
            selectedRequirementsList.on('click', '.delete-requirement-btn', function() {
                const requirementId = String($(this).parent().data('id'));
                const currentValues = $('#requirement_list_id').val() || [];
                const updatedValues = currentValues.filter(id => id !== requirementId);
                $('#requirement_list_id').val(updatedValues).trigger('change');
                updateSelectedRequirementsList();
            });

            // Initialize Select2 for suppliers with AJAX and custom template
            $('#komat_supplier_id').select2({
                placeholder: "Ketik untuk mencari atau tambahkan supplier",
                allowClear: true,
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                },
                ajax: {
                    url: '{{ route('komatprocesshistory.searchSuppliers') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.suppliers.map(function(supplier) {
                                return {
                                    id: supplier.id,
                                    text: supplier.name
                                };
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                templateResult: function(data) {
                    if (data.loading) return data.text;
                    var $result = $('<span>' + data.text + '</span>');
                    if (data.newTag) {
                        $result.append(' <small class="text-muted">(Tambah supplier baru)</small>');
                    }
                    return $result;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            });

            // Handle new supplier creation via Select2 tag
            $('#komat_supplier_id').on('select2:select', function(e) {
                var data = e.params.data;
                if (data.newTag) {
                    Swal.fire({
                        title: 'Tambah Supplier Baru',
                        html: `
                            <input type="text" id="supplierName" class="swal2-input" value="${data.text}" placeholder="Nama Supplier" required>
                            <input type="text" id="supplierDescription" class="swal2-input" placeholder="Deskripsi Supplier (opsional)">
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#6B7280',
                        preConfirm: () => {
                            const name = Swal.getPopup().querySelector('#supplierName').value;
                            const description = Swal.getPopup().querySelector(
                                '#supplierDescription').value;
                            if (!name) {
                                Swal.showValidationMessage('Nama supplier harus diisi');
                                return false;
                            }
                            return {
                                name,
                                description
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const {
                                name,
                                description
                            } = result.value;
                            $.ajax({
                                url: '{{ route('komatprocesshistory.addSupplier') }}',
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                data: {
                                    name,
                                    description
                                },
                                success: function(data) {
                                    if (data.success) {
                                        const newOption = new Option(data.supplier.name,
                                            data.supplier.id, true, true);
                                        $('#komat_supplier_id').append(newOption)
                                            .trigger('change');
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: 'Supplier berhasil ditambahkan.',
                                            confirmButtonColor: '#10B981'
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: data.error ||
                                                'Terjadi kesalahan saat menambahkan supplier.',
                                            confirmButtonColor: '#dc3545'
                                        });
                                        $('#komat_supplier_id').val(null).trigger(
                                            'change');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: xhr.responseJSON?.error ||
                                            'Server error',
                                        confirmButtonColor: '#dc3545'
                                    });
                                    $('#komat_supplier_id').val(null).trigger('change');
                                }
                            });
                        } else {
                            $('#komat_supplier_id').val(null).trigger('change');
                        }
                    });
                }
            });

            // Handle search reference button click
            $('#searchReferenceBtn').on('click', function() {
                const kodematerial = $('#kodematerial').val();
                if (!kodematerial) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Harap pilih kode material terlebih dahulu.',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                // Tampilkan loading indicator
                Swal.fire({
                    title: 'Memuat...',
                    text: 'Sedang mencari referensi requirement.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route('komatprocesshistory.referencerelation') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        kodematerial: kodematerial
                    },
                    success: function(data) {
                        referenceResultsList.empty();
                        if (data.success) {
                            if (data.data && data.data.length > 0) {
                                // Update reference results list
                                data.data.forEach(requirement => {
                                    const listItem = `
                            <li class="list-group-item">${requirement}</li>
                        `;
                                    referenceResultsList.append(listItem);
                                });

                                // Update requirement_list_id select field
                                $('#requirement_list_id').val(null).trigger(
                                    'change'); // Clear existing selections
                                const requirementOptions = $('#requirement_list_id option')
                                    .map(function() {
                                        return $(this).text();
                                    }).get();
                                const validRequirements = data.data.filter(req =>
                                    requirementOptions.includes(req)
                                );
                                validRequirements.forEach(req => {
                                    const option = $('#requirement_list_id option')
                                        .filter(
                                            function() {
                                                return $(this).text() === req;
                                            });
                                    if (option.length) {
                                        const id = option.val();
                                        const currentValues = $('#requirement_list_id')
                                            .val() || [];
                                        $('#requirement_list_id').val([...currentValues,
                                            id
                                        ]).trigger('change');
                                    }
                                });

                                updateSelectedRequirementsList();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Referensi requirement berhasil dimuat.',
                                    confirmButtonColor: '#10B981'
                                });
                            } else {
                                // Tampilkan pesan "Tidak Ada" jika data kosong
                                referenceResultsList.append(
                                    '<li class="list-group-item">Tidak Ada</li>');
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Informasi',
                                    text: 'Tidak ada referensi requirement ditemukan.',
                                    confirmButtonColor: '#6B7280'
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Data tidak ditemukan.',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat mengambil data.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                let validFiles = true;

                const selectedRequirements = $('#requirement_list_id').val() || [];
                selectedRequirements.forEach(requirementId => {
                    const fileInput = document.querySelector(
                        `input[name="file_${requirementId}[]"]`);
                    if (!fileInput || !fileInput.files.length) {
                        validFiles = false;
                    }
                });

                if (!selectedRequirements.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Harap pilih minimal satu requirement dan upload file terkait.',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                if (!validFiles) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'File untuk setiap requirement yang dipilih harus diupload.',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                const selectedProjects = $('#proyek_type_id').val();
                if (!selectedProjects || selectedProjects.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'Harap pilih minimal satu tipe proyek.',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                // Additional validation for requirement names (to catch any edge cases)
                const requirementOptions = $('#requirement_list_id').select2('data');
                const hasSpaces = requirementOptions.some(option => /\s/.test(option.text));
                if (hasSpaces) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Nama requirement tidak boleh mengandung spasi. Silakan periksa kembali.',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Upload',
                    text: "Apakah Anda yakin ingin meng-upload dokumen ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, upload sekarang',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
