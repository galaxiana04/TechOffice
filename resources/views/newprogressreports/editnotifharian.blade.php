@extends('layouts.universal')

@section('container2')
    <div class="content-header py-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark font-weight-bold" style="font-size: 1.5rem; line-height: 1.2;">Edit Notifikasi</h1>
                    <p class="text-muted small mb-0">Update konfigurasi notifikasi Telegram</p>
                </div>
                <div class="col-sm-6 d-none d-sm-block">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('newreports.index') }}" class="text-muted hover-primary">
                                <i class="fas fa-home mr-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-danger font-weight-bold">Edit Notif</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <style>
        /* --- UTILITIES (TEMA MERAH) --- */
        .card-modern {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            background: #fff;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        /* Gradient Merah */
        .header-gradient {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            padding: 1.5rem;
            color: white;
        }

        .form-label-modern {
            color: #7f1d1d; /* Merah gelap */
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control-modern {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.75rem 1rem;
            height: auto;
            font-size: 1rem;
            transition: all 0.2s ease-in-out;
        }
        
        /* Fokus Merah */
        .form-control-modern:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
            outline: none;
        }
        
        /* --- SELECT2 CUSTOMIZATION --- */
        .select2-container--bootstrap .select2-selection--multiple {
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            min-height: 48px; 
            padding: 8px 12px !important;
            background-color: #fff;
        }

        /* Fokus Select2 Merah */
        .select2-container--bootstrap.select2-container--focus .select2-selection--multiple,
        .select2-container--bootstrap.select2-container--focus .select2-selection--single {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
        }
        
        /* Sembunyikan Chip Bawaan Select2 */
        .select2-container--bootstrap .select2-selection--multiple .select2-selection__choice {
            display: none !important;
        }

        /* Input text placeholder */
        .select2-search__field {
            margin-top: 0 !important;
            font-size: 1rem !important;
            color: #333 !important;
            width: 100% !important;
        }

        /* Sembunyikan yang sudah dipilih di dropdown */
        .select2-results__option[aria-selected=true] {
            display: none !important;
        }
        
        /* Dropdown Highlight Merah */
        .select2-container--bootstrap .select2-results__option--highlighted[aria-selected] {
            background-color: #ef4444 !important;
        }

        .select2-container--bootstrap .select2-selection--single {
            height: 48px !important;
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
            padding: 10px 12px !important;
        }

        /* --- CUSTOM CHIPS (MERAH MUDA) --- */
        #selected-docs-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .custom-chip {
            background-color: #fef2f2; /* Merah sangat muda */
            border: 1px solid #fecaca;
            color: #b91c1c; /* Merah tua */
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .custom-chip:hover {
            background-color: #fee2e2;
        }

        .custom-chip-remove {
            margin-right: 8px;
            color: #ef4444;
            font-weight: bold;
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            display: flex;
            align-items: center;
        }
        .custom-chip-remove:hover {
            color: #991b1b;
        }

        /* Icon Container */
        .icon-circle-bg {
            background-color: rgba(255, 255, 255, 0.2);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        /* --- TOMBOL BIRU MODERN (Simpan Data) --- */
        .btn-gradient-blue {
            background: linear-gradient(to right, #3b82f6, #2563eb); /* Biru Modern */
            border: none;
            color: white !important;
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.25);
            transition: all 0.2s;
        }
        .btn-gradient-blue:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
            filter: brightness(110%);
        }
        
        .btn-light-custom {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            color: #444;
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        .btn-light-custom:hover {
            background-color: #e2e6ea;
        }
    </style>

    <div class="container-fluid pb-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                
                {{-- Alert Notification --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center" role="alert" style="border-radius: 0.75rem; background-color: #ecfdf5; border-left: 5px solid #10b981;">
                        <i class="fas fa-check-circle fa-lg mr-3 text-success"></i>
                        <div class="text-success">
                            <strong>Berhasil!</strong> {{ session('success') }}
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card card-modern">
                    <div class="header-gradient d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle-bg">
                                <i class="fas fa-edit text-white fa-lg"></i>
                            </div>
                            <div>
                                <h5 class="font-weight-bold m-0 text-white" style="font-size: 1.25rem;">Formulir Edit</h5>
                                <p class="m-0 text-white-50 small">Sesuaikan data notifikasi di bawah ini</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('newprogressreports.update-notif-harian-unit', $notifHarianUnit->id) }}" method="POST">
                            @csrf
                            
                            {{-- Input Title --}}
                            <div class="form-group mb-5">
                                <label for="title" class="form-label-modern">Judul Notifikasi <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0" style="border-color: #d1d5db; border-radius: 0.5rem 0 0 0.5rem;">
                                            <i class="fas fa-heading text-secondary"></i>
                                        </span>
                                    </div>
                                    <input type="text" 
                                           class="form-control form-control-modern border-left-0 @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $notifHarianUnit->title) }}" 
                                           placeholder="Masukkan judul notifikasi..." 
                                           style="border-radius: 0 0.5rem 0.5rem 0;"
                                           required>
                                </div>
                                @error('title')
                                    <div class="small text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Input Document Kinds (Custom Design) --}}
                            <div class="form-group mb-5">
                                <label for="documentkind" class="form-label-modern d-flex justify-content-between align-items-center">
                                    <span>Jenis Dokumen Terkait <span class="text-danger">*</span></span>
                                    <span class="badge badge-light border font-weight-normal text-muted" style="font-size: 0.7rem;">MULTISELECT</span>
                                </label>
                                
                                {{-- Select2 Asli --}}
                                <select class="form-control select2" id="documentkind" name="documentkind[]" multiple="multiple" required style="width: 100%;">
                                    @foreach ($documentKinds as $documentKind)
                                        <option value="{{ $documentKind->id }}" 
                                            @if(in_array($documentKind->id, $selectedDocumentKinds)) selected @endif>
                                            {{ $documentKind->name }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Container Custom Chips --}}
                                <div id="selected-docs-container"></div>
                                
                                <div class="mt-2 text-muted small">
                                    <i class="fas fa-info-circle text-danger mr-1"></i> 
                                    <span>Pilih dokumen di atas. Dokumen yang sudah dipilih akan hilang dari daftar pencarian.</span>
                                </div>
                            </div>

                            {{-- Input Telegram Account --}}
                            <div class="form-group mb-5">
                                <label for="telegrammessagesaccount_id" class="form-label-modern">Akun Telegram Tujuan</label>
                                <select class="form-control select2" id="telegrammessagesaccount_id" name="telegrammessagesaccount_id" style="width: 100%;">
                                    <option value="">-- Nonaktif / Tidak Ada --</option>
                                    @foreach ($telegrammessagesaccounts as $account)
                                        <option value="{{ $account->id }}" 
                                            {{ (old('telegrammessagesaccount_id', $notifHarianUnit->telegrammessagesaccount_id) == $account->id) ? 'selected' : '' }}>
                                            {{ $account->account }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <hr class="my-4 border-top" style="border-color: #f3f4f6;">

                            {{-- Action Buttons --}}
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                                <a href="{{ url()->previous() }}" class="btn btn-light-custom w-100 w-sm-auto text-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                                </a>
                                {{-- Tombol Simpan Biru --}}
                                <button type="submit" class="btn btn-gradient-blue w-100 w-sm-auto">
                                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {
            // 1. Inisialisasi Select2 Dokumen
            var $docSelect = $('#documentkind').select2({
                theme: 'bootstrap',
                placeholder: 'Cari dan pilih jenis dokumen...',
                allowClear: true,
                width: '100%',
                closeOnSelect: false 
            });

            // 2. Inisialisasi Select2 Telegram
            var $teleSelect = $('#telegrammessagesaccount_id').select2({
                theme: 'bootstrap',
                placeholder: 'Pilih akun telegram...',
                allowClear: true,
                width: '100%'
            });

            // 3. Fungsi untuk Render Custom Chips
            function renderCustomChips() {
                var selectedData = $docSelect.select2('data'); 
                var $container = $('#selected-docs-container');
                $container.empty(); 

                selectedData.forEach(function(item) {
                    var chipHtml = `
                        <div class="custom-chip" title="${item.text}">
                            <span class="custom-chip-remove" data-id="${item.id}">&times;</span>
                            <span>${item.text}</span>
                        </div>
                    `;
                    $container.append(chipHtml);
                });
            }

            // 4. Update Chips saat Select2 berubah
            $docSelect.on('change select2:select select2:unselect', function (e) {
                renderCustomChips();
                // Trik: Fokus kembali ke search box select2 agar user bisa langsung ketik lagi
                // setTimeout(function(){ $docSelect.select2('open'); }, 50); 
            });

            // 5. Tombol Hapus (X) pada Chip
            $(document).on('click', '.custom-chip-remove', function() {
                var idToRemove = $(this).data('id').toString();
                var currentValues = $docSelect.val();
                
                if (currentValues) {
                    var newValues = currentValues.filter(function(id) {
                        return id !== idToRemove;
                    });
                    $docSelect.val(newValues).trigger('change');
                }
            });

            // 6. Render awal
            renderCustomChips();
        });
    </script>
@endpush