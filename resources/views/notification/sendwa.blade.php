@extends('layouts.universal')

@php
    use Carbon\Carbon;
@endphp

{{-- BAGIAN HEADER --}}
@section('container2')
    <div class="content-header mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="m-0 text-dark font-weight-bold">Broadcast WhatsApp</h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right bg-transparent p-0 m-0">
                        <li class="breadcrumb-item"><a href="{{ url('') }}" class="text-secondary">Home</a></li>
                        <li class="breadcrumb-item active text-success">Kirim Pesan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- BAGIAN KONTEN UTAMA --}}
@section('container3')
    <div class="container-fluid pb-5">
        <div class="row justify-content-center">
            {{-- PERUBAHAN 1: Memperbesar lebar kolom (dari col-lg-8 ke col-lg-10) --}}
            <div class="col-lg-10 col-md-12">
                <div class="card shadow-lg border-0 rounded-lg">
                    
                    {{-- Card Header --}}
                    <div class="card-header bg-white py-4 border-0">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape bg-success text-white rounded-circle p-3 mr-3 shadow-sm">
                                <i class="fab fa-whatsapp fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 font-weight-bold text-dark">Formulir Pesan Baru</h5>
                                <p class="mb-0 text-muted small">Kirim pesan ke individu atau grup dengan mudah.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body px-4 pt-0">
                        {{-- FORM UTAMA --}}
                        <form id="waBroadcastForm" action="{{ route('notification.sendwa') }}" method="POST">
                            @csrf

                            {{-- 1. Pilihan Jenis Penerima --}}
                            <div class="form-group mb-4">
                                <label class="text-uppercase text-secondary text-xs font-weight-bold mb-2">Target Penerima</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-users-cog text-muted"></i></span>
                                    </div>
                                    <select id="kindreceiver" name="kindreceiver" class="form-control bg-light border-0 shadow-none h-auto py-2" style="font-size: 1rem;" required>
                                        <option value="individual">👤 Nomor Individu</option>
                                        <option value="group">👥 Grup WhatsApp</option>
                                    </select>
                                </div>
                            </div>

                            {{-- 2. Section Individu --}}
                            <div id="individual-section" class="animate-fade">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="text-uppercase text-secondary text-xs font-weight-bold mb-0">Daftar Kontak</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="select_all_individual">
                                        <label class="custom-control-label text-sm text-success" for="select_all_individual">Pilih Semua</label>
                                    </div>
                                </div>
                                
                                {{-- Search Box Individu --}}
                                <div class="form-group mb-2">
                                    <input type="text" id="searchIndividual" class="form-control form-control-sm border rounded-pill px-3" placeholder="🔍 Cari nama kontak...">
                                </div>

                                {{-- List Scrollable Individu --}}
                                <div class="contact-list-container border rounded p-2 bg-light">
                                    <div class="row mx-0" id="individual-list">
                                        @foreach ($userphonebook as $user)
                                            {{-- PERUBAHAN 2: Grid menjadi 3 kolom (col-lg-4) agar lebih pas di layar lebar --}}
                                            <div class="col-lg-4 col-md-6 px-1 mb-2 contact-item">
                                                <div class="custom-control custom-checkbox image-checkbox h-100">
                                                    <input type="checkbox" class="custom-control-input checkbox-phonenumber" 
                                                           id="phonenumber_{{ $loop->index }}" 
                                                           name="phonenumbers[]" 
                                                           value="{{ $user->waphonenumber }}">
                                                    
                                                    {{-- Label Checkbox --}}
                                                    <label class="custom-control-label d-flex align-items-center w-100 p-2 bg-white rounded shadow-sm border border-light h-100" for="phonenumber_{{ $loop->index }}">
                                                        <div class="avatar-sm bg-success-light text-success rounded-circle mr-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                                            <span class="font-weight-bold" style="font-size: 1.1rem;">{{ substr($user->name, 0, 1) }}</span>
                                                        </div>
                                                        <div class="text-truncate">
                                                            {{-- Nama Kontak (Nomor HP dihapus dari tampilan) --}}
                                                            <span class="d-block font-weight-bold text-dark user-name" style="font-size: 0.9rem;">{{ $user->name }}</span>
                                                            {{-- PERUBAHAN 3: Nomor HP dihilangkan visualnya --}}
                                                            <span class="d-block text-muted" style="font-size: 0.7rem;">WhatsApp Contact</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 3. Section Group --}}
                            <div id="group-section" class="animate-fade" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="text-uppercase text-secondary text-xs font-weight-bold mb-0">Daftar Grup</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="select_all_groups">
                                        <label class="custom-control-label text-sm text-success" for="select_all_groups">Pilih Semua</label>
                                    </div>
                                </div>

                                {{-- Search Box Group --}}
                                <div class="form-group mb-2">
                                    <input type="text" id="searchGroup" class="form-control form-control-sm border rounded-pill px-3" placeholder="🔍 Cari nama grup...">
                                </div>

                                {{-- List Scrollable Group --}}
                                <div class="contact-list-container border rounded p-2 bg-light">
                                    <div class="row mx-0" id="group-list">
                                        @foreach ($wagrouplist as $group)
                                            <div class="col-lg-4 col-md-6 px-1 mb-2 group-item">
                                                <div class="custom-control custom-checkbox image-checkbox h-100">
                                                    <input type="checkbox" class="custom-control-input checkbox-group" 
                                                           id="group_{{ $loop->index }}" 
                                                           name="listunit[]" 
                                                           value="{{ $group->name }}">
                                                    <label class="custom-control-label d-flex align-items-center w-100 p-2 bg-white rounded shadow-sm border border-light h-100" for="group_{{ $loop->index }}">
                                                        <div class="avatar-sm bg-info-light text-info rounded-circle mr-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-users"></i>
                                                        </div>
                                                        <span class="font-weight-bold text-dark group-name" style="font-size: 0.9rem;">{{ $group->name }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- 4. Pesan Area --}}
                            <div class="form-group mt-4">
                                <label for="pesan" class="text-uppercase text-secondary text-xs font-weight-bold">Isi Pesan</label>
                                <textarea id="pesan" name="pesan" class="form-control bg-light border-0 shadow-inner p-3" rows="6" required placeholder="Ketik pesan Anda di sini..."></textarea>
                            </div>

                            {{-- 5. Sender & Action --}}
                            <div class="row align-items-end mt-4">
                                <div class="col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="sender_name" class="text-uppercase text-secondary text-xs font-weight-bold">Dikirim Oleh</label>
                                        <select id="sender_name" name="sender_name" class="form-control custom-select shadow-none">
                                            <option value="{{ $senderName }}">{{ $senderName }}</option>
                                            <option value="TechBot">TechBot (System)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right mt-3 mt-md-0">
                                    <button type="submit" id="btnSubmit" class="btn btn-success btn-block btn-lg shadow-lg rounded-pill hover-lift">
                                        <i class="fab fa-whatsapp mr-2"></i> Kirim Sekarang
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- BAGIAN CSS TAMBAHAN --}}
@push('css')
    <style>
        /* Styling Container Scroll */
        .contact-list-container {
            max-height: 350px; /* Sedikit dipertinggi */
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #28a745 #f1f1f1;
        }
        
        /* Custom Scrollbar Webkit */
        .contact-list-container::-webkit-scrollbar { width: 6px; }
        .contact-list-container::-webkit-scrollbar-track { background: #f1f1f1; }
        .contact-list-container::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
        .contact-list-container::-webkit-scrollbar-thumb:hover { background: #28a745; }

        /* Checkbox Card Styling */
        .image-checkbox .custom-control-label { cursor: pointer; transition: all 0.2s; }
        .image-checkbox .custom-control-label:hover { background-color: #f8fff9 !important; border-color: #28a745 !important; }
        .image-checkbox .custom-control-input:checked ~ .custom-control-label { background-color: #e6ffea !important; border-color: #28a745 !important; position: relative; }
        
        /* Typography Helpers */
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .bg-success-light { background-color: rgba(40, 167, 69, 0.1); }
        .bg-info-light { background-color: rgba(23, 162, 184, 0.1); }
        
        /* Button Animation */
        .hover-lift { transition: transform 0.2s; }
        .hover-lift:hover { transform: translateY(-3px); }

        /* Animation Fade */
        .animate-fade { animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

{{-- BAGIAN JAVASCRIPT --}}
@push('scripts')
    {{-- Load SweetAlert dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // --- 1. LOGIKA UTAMA AJAX FORM SUBMIT ---
            const form = document.getElementById('waBroadcastForm');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); 

                    const kind = document.getElementById('kindreceiver').value;
                    let checked = false;
                    if(kind === 'individual') {
                        if(document.querySelectorAll('.checkbox-phonenumber:checked').length > 0) checked = true;
                    } else {
                        if(document.querySelectorAll('.checkbox-group:checked').length > 0) checked = true;
                    }

                    if(!checked) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Harap pilih minimal satu penerima!',
                            confirmButtonColor: '#ffc107'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Sedang Mengirim...',
                        text: 'Mohon tunggu, proses sedang berjalan.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData(this);

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload(); 
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan pada server.',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Sistem',
                            text: 'Gagal menghubungi server. Cek koneksi atau Console Log.',
                            confirmButtonColor: '#d33'
                        });
                    });
                });
            }


            // --- 2. LOGIKA TAMPILAN (TOGGLE & SEARCH) ---

            const kindSelector = document.getElementById('kindreceiver');
            if(kindSelector) {
                kindSelector.addEventListener('change', function(e) {
                    const individualSection = document.getElementById('individual-section');
                    const groupSection = document.getElementById('group-section');

                    if (e.target.value === 'individual') {
                        individualSection.style.display = 'block';
                        groupSection.style.display = 'none';
                        document.querySelectorAll('.checkbox-group').forEach(cb => cb.checked = false);
                    } else {
                        individualSection.style.display = 'none';
                        groupSection.style.display = 'block';
                        document.querySelectorAll('.checkbox-phonenumber').forEach(cb => cb.checked = false);
                    }
                });
            }

            // Select All Logic
            const selectAllInd = document.getElementById('select_all_individual');
            if(selectAllInd) {
                selectAllInd.addEventListener('change', function(e) {
                    const visibleCheckboxes = document.querySelectorAll('#individual-list .contact-item:not([style*="display: none"]) .checkbox-phonenumber');
                    visibleCheckboxes.forEach(checkbox => checkbox.checked = e.target.checked);
                });
            }

            const selectAllGrp = document.getElementById('select_all_groups');
            if(selectAllGrp) {
                selectAllGrp.addEventListener('change', function(e) {
                    const visibleCheckboxes = document.querySelectorAll('#group-list .group-item:not([style*="display: none"]) .checkbox-group');
                    visibleCheckboxes.forEach(checkbox => checkbox.checked = e.target.checked);
                });
            }

            // Search Filter
            function filterList(inputId, listId, itemClass, nameClass) {
                const inputEl = document.getElementById(inputId);
                if(inputEl) {
                    inputEl.addEventListener('keyup', function() {
                        let filter = this.value.toLowerCase();
                        let items = document.querySelectorAll(`#${listId} .${itemClass}`);

                        items.forEach(function(item) {
                            let name = item.querySelector(`.${nameClass}`).textContent.toLowerCase();
                            if (name.includes(filter)) {
                                item.style.display = ""; 
                            } else {
                                item.style.display = "none"; 
                            }
                        });
                    });
                }
            }

            filterList('searchIndividual', 'individual-list', 'contact-item', 'user-name');
            filterList('searchGroup', 'group-list', 'group-item', 'group-name');

        });
    </script>
@endpush