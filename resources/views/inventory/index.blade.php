@extends('layouts.universal')

@push('styles')
    {{-- Menggunakan Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        /* Hilangkan spinner pada input number */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }

        /* --- CUSTOM STYLE TOMBOL CLOSE SWEETALERT (FIXED) --- */
        /* Menggunakan selector yang lebih spesifik dan !important */
        .swal2-container .swal2-popup .swal2-close {
            background-color: #ef4444 !important; /* Merah terang */
            color: #ffffff !important; /* Ikon Putih */
            border: 2px solid #b91c1c !important; /* Border merah tua */
            border-radius: 8px !important; /* Sedikit membulat */
            width: 40px !important;
            height: 40px !important;
            opacity: 1 !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2) !important;
            transition: all 0.2s ease !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            top: 15px !important;
            right: 15px !important;
            font-size: 1.5rem !important; /* Ukuran silang */
        }

        /* Efek Hover tombol X */
        .swal2-container .swal2-popup .swal2-close:hover {
            background-color: #dc2626 !important; /* Merah lebih gelap saat hover */
            transform: scale(1.1) !important; /* Sedikit membesar */
            box-shadow: 0 6px 8px rgba(0,0,0,0.3) !important;
        }
        
        .swal2-container .swal2-popup .swal2-close:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.5) !important;
        }
    </style>
@endpush

@section('container2')
    <div class="content-header" style="background: white; padding: 1rem 0; border-bottom: 1px solid #e5e7eb;">
        <div class="container-fluid">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                <a href="#" style="color: #4b5563; font-weight: 500; text-decoration: none;">Inventory</a>
                <svg style="width: 1rem; height: 1rem; color: #ef4444;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span style="color: #ef4444; font-weight: 600;">List Item</span>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container-fluid" style="padding: 2rem 1rem;">
        
        {{-- HEADER CARD: Warna Merah Solid #ef4444 --}}
        <div style="background-color: #ef4444; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 2rem; margin-bottom: 2rem; color: white; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            
            {{-- Judul & Ikon --}}
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="background-color: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 0.75rem;">
                    <svg style="width: 2.5rem; height: 2.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h1 style="font-size: 1.875rem; font-weight: 700; line-height: 1.25;">Inventory Management</h1>
                    <p style="color: #fee2e2; font-size: 1rem; margin-top: 0.25rem;">Kelola inventaris dengan mudah dan efisien</p>
                </div>
            </div>

            {{-- Tombol Aksi Header --}}
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                {{-- Tombol Tambah Barang (Putih, Teks Merah) --}}
                <button onclick="showCreateModal()" 
                        style="background: white; color: #ef4444; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Barang</span>
                </button>
                
                {{-- Tombol Tambah Jenis (Merah Gelap/Maroon) --}}
                <button onclick="showCreateKindModal()" 
                        style="background: #991b1b; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span>Tambah Jenis</span>
                </button>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div style="background: white; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); overflow: hidden; border: 1px solid #e5e7eb;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;" id="inventory-table">
                    <thead>
                        {{-- Header Tabel: Merah Solid #ef4444 --}}
                        <tr style="background-color: #ef4444; color: white;">
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; width: 50px;">ID</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; width: 100px;">Gambar</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600;">Kode Asset</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600;">Kode Mesin</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600;">Nama</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600;">Jenis</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; text-align: center;">Total</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; text-align: center;">Tersedia</th>
                            <th style="padding: 1rem; font-size: 0.875rem; font-weight: 600; min-width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @foreach ($inventories as $item)
                            <tr style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.2s;" data-id="{{ $item->id }}" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                <td style="padding: 1rem; color: #6b7280; font-weight: 500;">{{ $loop->iteration }}</td>
                                <td style="padding: 1rem;">
                                    @if ($item->files->isNotEmpty())
                                        @foreach ($item->files as $file)
                                            <div style="width: 4rem; height: 4rem; border-radius: 0.5rem; overflow: hidden; border: 1px solid #e5e7eb; background: #f9fafb; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                {{-- PERUBAHAN: object-fit: contain agar gambar full --}}
                                                <img src="{{ asset('storage/uploads/' . rawurlencode(str_replace('uploads/', '', $file->link))) }}"
                                                     style="width: 100%; height: 100%; object-fit: contain;">
                                            </div>
                                        @endforeach
                                    @else
                                        <div style="width: 4rem; height: 4rem; background: #f3f4f6; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #9ca3af; border: 1px solid #e5e7eb;">
                                            <i class="fas fa-image fa-lg"></i>
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    <span id="assetcodeDisplay{{ $item->id }}" style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-family: monospace; color: #374151; font-weight: 500;">{{ $item->assetcode }}</span>
                                    <form id="editAssetcodeForm{{ $item->id }}" style="display: none; gap: 0.25rem; align-items: center;">
                                        <input type="text" id="assetcode{{ $item->id }}" value="{{ $item->assetcode }}" style="width: 100px; padding: 0.25rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                        <button type="button" onclick="updateAssetcode({{ $item->id }})" style="background: #10b981; color: white; border: none; padding: 0.35rem 0.5rem; border-radius: 0.375rem; border-bottom: 2px solid #059669; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" title="Simpan"><i class="fas fa-check"></i></button>
                                        <button type="button" onclick="cancelEditAssetcode({{ $item->id }})" style="background: #ef4444; color: white; border: none; padding: 0.35rem 0.5rem; border-radius: 0.375rem; border-bottom: 2px solid #b91c1c; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" title="Batal"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                                <td style="padding: 1rem;">
                                    <span id="machinecodeDisplay{{ $item->id }}" style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-family: monospace; color: #374151; font-weight: 500;">{{ $item->machinecode }}</span>
                                    <form id="editMachinecodeForm{{ $item->id }}" style="display: none; gap: 0.25rem; align-items: center;">
                                        <input type="text" id="machinecode{{ $item->id }}" value="{{ $item->machinecode }}" style="width: 100px; padding: 0.25rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                        <button type="button" onclick="updateMachinecode({{ $item->id }})" style="background: #10b981; color: white; border: none; padding: 0.35rem 0.5rem; border-radius: 0.375rem; border-bottom: 2px solid #059669; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" title="Simpan"><i class="fas fa-check"></i></button>
                                        <button type="button" onclick="cancelEditMachinecode({{ $item->id }})" style="background: #ef4444; color: white; border: none; padding: 0.35rem 0.5rem; border-radius: 0.375rem; border-bottom: 2px solid #b91c1c; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" title="Batal"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                                <td style="padding: 1rem; font-weight: 600; color: #111827;">{{ $item->name }}</td>
                                <td style="padding: 1rem;">
                                    <span style="background: #e5e7eb; color: #374151; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">{{ $item->kind->name }}</span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #f3f4f6; color: #374151; min-width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.375rem; font-weight: 700;">{{ $item->quantity_total }}</span>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #d1fae5; color: #047857; min-width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.375rem; font-weight: 700;">{{ $item->quantity_available }}</span>
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        
                                        {{-- 1. Form Pinjam --}}
                                        <form class="form-borrow" data-id="{{ $item->id }}" style="display: flex; gap: 0.25rem;">
                                            @csrf
                                            <input type="number" name="quantity" min="1" max="{{ $item->quantity_available }}" required 
                                                style="width: 3.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem; text-align: center; font-size: 0.875rem;">
                                            <button type="button" class="btn-borrow" 
                                                style="flex: 1; background: gray; color: white; border: none; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: background 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                Pinjam
                                            </button>
                                        </form>

                                        {{-- 2. Tombol Peminjam --}}
                                        <button class="btn-show-loans" data-id="{{ $item->id }}" 
                                            style="width: 100%; background: #3b82f6; color: white; border: none; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: background 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <i class="fas fa-users" style="font-size: 0.875rem;"></i> Peminjam
                                        </button>

                                        {{-- 3. Edit Asset --}}
                                        <button onclick="enableEditAssetcode({{ $item->id }})" 
                                            style="width: 100%; background: #22c55e; color: white; border: none; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: background 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            Edit Kode Asset
                                        </button>

                                        {{-- 4. Edit Mesin --}}
                                        <button onclick="enableEditMachinecode({{ $item->id }})" 
                                            style="width: 100%; background: #22c55e; color: white; border: none; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: background 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            Edit Kode Mesin
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Scripts External --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        $(document).ready(function () {
            attachEventListeners();
        });

        // Function untuk Refresh Tabel via AJAX
        function updateInventoryTable() {
            $.ajax({
                url: "{{ route('inventories.data') }}",
                type: 'GET',
                success: function (data) {
                    if (data.success) {
                        const tbody = $('#inventory-table tbody');
                        tbody.empty();
                        data.inventories.forEach((item, index) => {
                            const row = `
                                <tr style="border-bottom: 1px solid #f3f4f6; transition: background-color 0.2s;" data-id="${item.id}" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                                    <td style="padding: 1rem; color: #6b7280; font-weight: 500;">${index + 1}</td>
                                    <td style="padding: 1rem;">
                                        ${item.files.length > 0 ? 
                                            `<div style="width: 4rem; height: 4rem; border-radius: 0.5rem; overflow: hidden; border: 1px solid #e5e7eb; background: #f9fafb; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                                {{-- PERUBAHAN JS: object-fit: contain --}}
                                                <img src="${item.files[0].link}" style="width: 100%; height: 100%; object-fit: contain;">
                                             </div>` : 
                                            `<div style="width: 4rem; height: 4rem; background: #f3f4f6; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; color: #9ca3af; border: 1px solid #e5e7eb;">
                                                <i class="fas fa-image fa-lg"></i>
                                             </div>`
                                        }
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span id="assetcodeDisplay${item.id}" style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-family: monospace; color: #374151; font-weight: 500;">${item.assetcode || ''}</span>
                                        <form id="editAssetcodeForm${item.id}" style="display: none; gap: 0.25rem; align-items: center;">
                                            <input type="text" id="assetcode${item.id}" value="${item.assetcode || ''}" style="width: 100px; padding: 0.25rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                            <button type="button" onclick="updateAssetcode(${item.id})" style="background: #10b981; color: white; border: none; padding: 0.35rem 0.5rem; border-radius: 0.375rem; border-bottom: 2px solid #059669; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" title="Simpan"><i class="fas fa-check"></i></button>
                                            <button type="button" onclick="cancelEditAssetcode(${item.id})" style="background: #ef4444; color: white; border: none; padding: 0.35rem 0.5rem; border-radius: 0.375rem; border-bottom: 2px solid #b91c1c; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" title="Batal"><i class="fas fa-times"></i></button>
                                        </form>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <span id="machinecodeDisplay${item.id}" style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-family: monospace; color: #374151; font-weight: 500;">${item.machinecode || ''}</span>
                                        <form id="editMachinecodeForm${item.id}" style="display: none; gap: 0.25rem; align-items: center;">
                                            <input type="text" id="machinecode${item.id}" value="${item.machinecode || ''}" style="width: 100px; padding: 0.25rem 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                            <button type="button" onclick="updateMachinecode(${item.id})" style="background: #10b981; color: white; border: none; padding: 0.35rem 0.5rem; border-radius: 0.375rem; border-bottom: 2px solid #059669; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" title="Simpan"><i class="fas fa-check"></i></button>
                                            <button type="button" onclick="cancelEditMachinecode(${item.id})" style="background: #ef4444; color: white; border: none; padding: 0.35rem 0.5rem; border-radius: 0.375rem; border-bottom: 2px solid #b91c1c; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 2px rgba(0,0,0,0.1);" title="Batal"><i class="fas fa-times"></i></button>
                                        </form>
                                    </td>
                                    <td style="padding: 1rem; font-weight: 600; color: #111827;">${item.name}</td>
                                    <td style="padding: 1rem;">
                                        <span style="background: #e5e7eb; color: #374151; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">${item.kind_name}</span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <span style="background: #f3f4f6; color: #374151; min-width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.375rem; font-weight: 700;">${item.quantity_total}</span>
                                    </td>
                                    <td style="padding: 1rem; text-align: center;">
                                        <span style="background: #d1fae5; color: #047857; min-width: 2rem; height: 2rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 0.375rem; font-weight: 700;">${item.quantity_available}</span>
                                    </td>
                                    <td style="padding: 1rem;">
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                            <form class="form-borrow" data-id="${item.id}" style="display: flex; gap: 0.25rem;">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <input type="number" name="quantity" min="1" max="${item.quantity_available}" required 
                                                    style="width: 3.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.25rem; text-align: center; font-size: 0.875rem;">
                                                <button type="button" class="btn-borrow" 
                                                    style="flex: 1; background: #1f2937; color: white; border: none; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: background 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                    Pinjam
                                                </button>
                                            </form>
                                            <button class="btn-show-loans" data-id="${item.id}" 
                                                style="width: 100%; background: #3b82f6; color: white; border: none; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: background 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <i class="fas fa-users" style="font-size: 0.875rem;"></i> Peminjam
                                            </button>
                                            <button onclick="enableEditAssetcode(${item.id})" 
                                                style="width: 100%; background: #22c55e; color: white; border: none; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: background 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                Edit Kode Asset
                                            </button>
                                            <button onclick="enableEditMachinecode(${item.id})" 
                                                style="width: 100%; background: #22c55e; color: white; border: none; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer; transition: background 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                Edit Kode Mesin
                                            </button>
                                        </div>
                                    </td>
                                </tr>`;
                            tbody.append(row);
                        });
                        attachEventListeners();
                    } else {
                        Swal.fire('Gagal', data.message || 'Gagal memuat data inventaris', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menghubungi server', 'error');
                }
            });
        }

        // --- EDIT ASSET CODE ---
        function enableEditAssetcode(id) {
            document.getElementById('assetcodeDisplay' + id).style.display = 'none';
            document.getElementById('editAssetcodeForm' + id).style.display = 'flex';
        }

        function cancelEditAssetcode(id) {
            document.getElementById('assetcodeDisplay' + id).style.display = 'inline';
            document.getElementById('editAssetcodeForm' + id).style.display = 'none';
        }

        function updateAssetcode(id) {
            const assetcode = document.getElementById('assetcode' + id).value;
            if(!assetcode) return;
            $.ajax({
                url: `/inventories/${id}/update-assetcode`,
                method: 'POST',
                data: { assetcode: assetcode, id: id },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 1500, showConfirmButton: false });
                        updateInventoryTable();
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Gagal update asset code', 'error');
                }
            });
        }

        // --- EDIT MACHINE CODE ---
        function enableEditMachinecode(id) {
            document.getElementById('machinecodeDisplay' + id).style.display = 'none';
            document.getElementById('editMachinecodeForm' + id).style.display = 'flex';
        }

        function cancelEditMachinecode(id) {
            document.getElementById('machinecodeDisplay' + id).style.display = 'inline';
            document.getElementById('editMachinecodeForm' + id).style.display = 'none';
        }

        function updateMachinecode(id) {
            const machinecode = document.getElementById('machinecode' + id).value;
            if(!machinecode) return;
            $.ajax({
                url: `/inventories/${id}/update-machinecode`,
                method: 'POST',
                data: { machinecode: machinecode, id: id },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 1500, showConfirmButton: false });
                        updateInventoryTable();
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Gagal update machine code', 'error');
                }
            });
        }

        // --- EVENT LISTENERS ---
        function attachEventListeners() {
            $('.btn-borrow').off('click').on('click', function () {
                const form = $(this).closest('.form-borrow');
                const id = form.data('id');
                const quantity = form.find('[name=quantity]').val();
                
                if (quantity < 1) {
                    Swal.fire('Gagal', 'Jumlah harus lebih dari 0', 'warning');
                    return;
                }

                if ({{ auth()->id() }} === 78 || {{ auth()->id() }} === 9 || {{ auth()->id() }} === 1) {
                    Swal.fire({
                        title: 'Pilih Peminjam',
                        html: `
                            <select id="user-select" class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:border-gray-500 focus:ring focus:ring-gray-200">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->username }}</option>
                                @endforeach
                            </select>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Pinjam',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#4b5563',
                        preConfirm: () => {
                            const user_id = $('#user-select').val();
                            if (!user_id) Swal.showValidationMessage('Peminjam harus dipilih');
                            return user_id;
                        }
                    }).then(result => {
                        if (result.isConfirmed) {
                            processBorrow(id, quantity, result.value);
                        }
                    });
                } else {
                    Swal.fire('Akses Ditolak', 'Anda tidak memiliki izin untuk meminjam barang secara langsung. Hubungi admin.', 'error');
                }
            });

            $('.btn-show-loans').off('click').on('click', function () {
                const id = $(this).data('id');
                showLoansModal(id);
            });
        }

        function processBorrow(id, quantity, user_id) {
            Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading() });
            $.ajax({
                url: `/inventories/${id}/borrow`,
                type: 'POST',
                data: { user_id: user_id, quantity: quantity },
                success: function (data) {
                    Swal.close();
                    if (data.success) {
                        Swal.fire('Sukses', data.message || 'Peminjaman berhasil', 'success').then(() => updateInventoryTable());
                    } else {
                        Swal.fire('Gagal', data.message || 'Gagal meminjam barang', 'error');
                    }
                },
                error: function () {
                    Swal.close();
                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                }
            });
        }

        function showCreateModal() {
            Swal.fire({
                title: 'Tambah Barang',
                html: `
                    <form id="form-create" class="text-left space-y-4">
                        <div><label class="block text-gray-700 font-semibold mb-2">Nama Barang</label><input type="text" name="name" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-red-500 focus:ring focus:ring-red-200 transition-all"></div>
                        <div><label class="block text-gray-700 font-semibold mb-2">Jenis Barang</label><select name="inventory_kind_id" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-red-500 focus:ring focus:ring-red-200 transition-all">
                            @foreach ($kinds as $kind)<option value="{{ $kind->id }}">{{ $kind->name }}</option>@endforeach
                        </select></div>
                        <div><label class="block text-gray-700 font-semibold mb-2">Jumlah Total</label><input type="number" name="quantity_total" min="1" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-red-500 focus:ring focus:ring-red-200 transition-all"></div>
                        <div><label class="block text-gray-700 font-semibold mb-2">Upload Gambar (Multiple)</label><input type="file" id="create-files" name="files[]" multiple accept=".jpg,.jpeg,.png" class="w-full border border-gray-300 rounded-lg p-2"></div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
                width: '600px',
                preConfirm: () => {
                    const form = $('#form-create');
                    const files = $('#create-files')[0].files;
                    const formData = new FormData();
                    formData.append('name', form.find('[name=name]').val());
                    formData.append('inventory_kind_id', form.find('[name=inventory_kind_id]').val());
                    formData.append('quantity_total', form.find('[name=quantity_total]').val());
                    for (let i = 0; i < files.length; i++) { formData.append('files[]', files[i]); }
                    return $.ajax({ url: `/inventories/store`, type: 'POST', processData: false, contentType: false, data: formData }).then(response => { if (!response.success) throw new Error(response.message); return response; }).catch(error => { Swal.showValidationMessage(error.message || 'Gagal menyimpan data'); });
                }
            }).then(result => { if (result.isConfirmed) { Swal.fire('Sukses', 'Barang berhasil ditambahkan', 'success').then(() => updateInventoryTable()); } });
        }

        function showCreateKindModal() {
            Swal.fire({
                title: 'Tambah Jenis Barang',
                html: `
                    <form id="form-create-kind" class="text-left space-y-4">
                        <div><label class="block text-gray-700 font-semibold mb-2">Nama Jenis</label><input type="text" name="name" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-red-500 focus:ring focus:ring-red-200 transition-all"></div>
                        <div><label class="block text-gray-700 font-semibold mb-2">Deskripsi</label><input type="text" name="description" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-red-500 focus:ring focus:ring-red-200 transition-all"></div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#991b1b',
                preConfirm: () => {
                    const form = $('#form-create-kind');
                    return $.ajax({ url: `/inventories/kinds/store`, type: 'POST', data: { name: form.find('[name=name]').val(), description: form.find('[name=description]').val() } }).then(response => { if (!response.success) throw new Error(response.message); return response; }).catch(error => { Swal.showValidationMessage(error.message || 'Gagal menyimpan jenis'); });
                }
            }).then(result => { if (result.isConfirmed) { Swal.fire('Sukses', 'Jenis barang berhasil ditambahkan', 'success').then(() => location.reload()); } });
        }

        function showLoansModal(id) {
            Swal.fire({ title: 'Memuat Data Peminjam...', didOpen: () => Swal.showLoading() });
            $.ajax({
                url: `/inventories/${id}/loans`,
                type: 'GET',
                success: function (data) {
                    Swal.close();
                    if (data.success) {
                        let html = `
                            <div class="overflow-x-auto">
                                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                                    <thead>
                                        <tr style="background-color: #ef4444; color: white;">
                                            <th style="padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem;">Nama Peminjam</th>
                                            <th style="padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem; text-align: center;">Jumlah</th>
                                            <th style="padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem; text-align: center;">Status</th>
                                            <th style="padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem;">Tanggal Pinjam</th>
                                            <th style="padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem;">Tanggal Kembali</th>
                                            <th style="padding: 0.75rem 1rem; font-weight: 600; font-size: 0.875rem; text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">`;
                        data.loans.forEach(loan => {
                            const statusColor = loan.status === 'dipinjam' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                            html += `
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td class="px-4 py-3 font-medium text-sm">${loan.user_name}</td>
                                    <td class="px-4 py-3 text-center text-sm">${loan.quantity}</td>
                                    <td class="px-4 py-3 text-center"><span class="${statusColor} px-2 py-1 rounded-full text-xs font-semibold uppercase">${loan.status}</span></td>
                                    <td class="px-4 py-3 text-sm text-gray-600">${loan.borrowed_at}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">${loan.returned_at || '-'}</td>
                                    <td class="px-4 py-3 text-center">
                                        ${loan.status === 'dipinjam' && ({{ auth()->id() }} === 78 || {{ auth()->id() }} === 9 || {{ auth()->id() }} === 1) ? `<button class="btn-return bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors shadow-sm" data-loan-id="${loan.id}"><i class="fas fa-undo-alt mr-1"></i> Kembali</button>` : '-'}
                                    </td>
                                </tr>`;
                        });
                        html += `</tbody></table></div>`;
                        Swal.fire({
                            title: '<span style="color: #ef4444">Riwayat Peminjaman</span>',
                            html: html,
                            width: '900px',
                            showConfirmButton: false,
                            showCloseButton: true,
                            didOpen: () => {
                                $('.btn-return').click(function() { returnLoan($(this).data('loan-id'), id); });
                            }
                        });
                    } else { Swal.fire('Info', 'Belum ada riwayat peminjaman untuk barang ini', 'info'); }
                },
                error: function() { Swal.close(); Swal.fire('Error', 'Gagal memuat data', 'error'); }
            });
        }

        function returnLoan(loanId, inventoryId) {
            Swal.fire({ title: 'Konfirmasi', text: 'Tandai barang ini sudah dikembalikan?', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya', cancelButtonText: 'Batal' }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/inventories/loans/${loanId}/return`,
                        type: 'POST',
                        success: function(data) {
                            if (data.success) { Swal.fire('Sukses', 'Barang dikembalikan', 'success').then(() => { showLoansModal(inventoryId); updateInventoryTable(); }); } else { Swal.fire('Gagal', data.message, 'error'); }
                        }
                    });
                }
            });
        }
    </script>
@endpush