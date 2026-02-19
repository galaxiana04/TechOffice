@extends('layouts.universal')

@section('container2')
    <div class="bg-gradient-to-r from-red-50 to-orange-50 shadow-sm py-8 md:py-10">
        <div class="container-fluid px-4 md:px-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="text-center md:text-left">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 flex items-center justify-center md:justify-start gap-3">
                        <i class="fas fa-boxes text-red-600"></i>
                        Pencarian Material
                    </h1>
                    <p class="text-gray-500 text-sm mb-0 ml-1">Cari komponen material, sparepart, dan kode material BOM.</p>
                </div>
                <div class="flex justify-center md:justify-end">
                    <ol class="breadcrumb bg-white px-4 py-2 rounded-lg shadow-sm mb-0 inline-flex">
                        <li class="breadcrumb-item">
                            <a href="{{ route('newbom.searchkomat') }}" class="text-red-600 hover:text-red-700 font-medium">Home</a>
                        </li>
                        <li class="breadcrumb-item active text-gray-600">Cari Material</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container mx-auto px-4 mt-4 relative z-10 pb-12">
        
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8 md:mb-10">
            <div class="p-4 md:p-8">
                <form action="{{ route('newbom.searchkomat') }}" method="GET" class="relative">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 md:pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 group-focus-within:text-red-500 transition-colors duration-200"></i>
                        </div>
                        <input type="text" 
                               name="query" 
                               class="w-full pl-10 md:pl-12 pr-24 md:pr-32 py-3 md:py-4 bg-gray-50 border border-gray-200 rounded-xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:bg-white focus:border-transparent transition-all duration-200 text-base md:text-lg shadow-inner" 
                               placeholder="Cari nama / kode..." 
                               value="{{ request('query') }}"
                               required 
                               autofocus>
                        <div class="absolute inset-y-0 right-1 md:right-2 flex items-center">
                            <button type="submit" class="px-4 py-1.5 md:px-6 md:py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-md transition-all duration-200 text-sm md:text-base">
                                Cari
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($query))
            <div class="max-w-6xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-2 md:gap-0">
                    <h3 class="text-lg md:text-xl font-bold text-gray-700 text-center md:text-left">
                        Hasil Pencarian: <span class="text-red-600">"{{ $query }}"</span>
                    </h3>
                    <div class="text-center md:text-right">
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs md:text-sm font-medium">
                            {{ $results->count() }} item ditemukan
                        </span>
                    </div>
                </div>

                @if($results->count() > 0)
                    <div class="grid grid-cols-1 gap-4 md:gap-6">
                        @foreach($results as $result)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:p-6 hover:shadow-md hover:border-red-300 transition-all duration-200 group relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-1 h-full bg-red-500"></div>
                                
                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                    <div class="flex-grow">
                                        <div class="flex items-start gap-3 mb-2">
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 flex-shrink-0 mt-1">
                                                <i class="fas fa-cube text-sm"></i>
                                            </span>
                                            <h5 class="text-base md:text-lg font-bold text-gray-800 group-hover:text-red-600 transition-colors leading-snug">
                                                {{ $result->material ?? '-' }}
                                            </h5>
                                        </div>
                                        
                                        <div class="ml-0 md:ml-11 mt-2 md:mt-0">
                                            {{-- BAGIAN TOMBOL COPY --}}
                                            @if($result->kodematerial)
                                                <button onclick="copyToClipboard('{{ $result->kodematerial }}')" 
                                                        class="group/copy inline-flex items-center gap-2 px-3 py-1 bg-gray-50 border border-gray-200 rounded-lg text-xs md:text-sm font-mono text-gray-600 mb-2 md:mb-4 hover:bg-red-50 hover:border-red-200 hover:text-red-700 transition-all duration-200 cursor-pointer"
                                                        title="Klik untuk menyalin">
                                                    <i class="fas fa-barcode text-gray-400 group-hover/copy:text-red-500"></i>
                                                    <span class="font-semibold">{{ $result->kodematerial }}</span>
                                                    <i class="fas fa-copy ml-1 text-gray-300 group-hover/copy:text-red-500"></i>
                                                </button>
                                            @else
                                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 border border-gray-200 rounded-lg text-xs md:text-sm font-mono text-gray-400 mb-2 md:mb-4">
                                                    -
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0 w-full md:w-1/3 border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-6">
                                        <div class="space-y-2 md:space-y-3 text-sm">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 flex items-center gap-2">
                                                    <i class="fas fa-building text-xs w-4 text-center"></i> Unit
                                                </span>
                                                <span class="font-medium text-gray-800">{{ $result->newbom->unit ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 flex items-center gap-2">
                                                    <i class="fas fa-project-diagram text-xs w-4 text-center"></i> Project
                                                </span>
                                                <span class="font-medium text-gray-800 text-right truncate w-32 md:w-40" title="{{ $result->newbom->projectType->title ?? '-' }}">
                                                    {{ $result->newbom->projectType->title ?? '-' }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 flex items-center gap-2">
                                                    <i class="fas fa-file-invoice text-xs w-4 text-center"></i> No BOM
                                                </span>
                                                <span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded text-xs font-bold">
                                                    {{ $result->newbom->BOMnumber ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 md:py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="mb-4 inline-block p-4 rounded-full bg-red-50">
                            <i class="fas fa-search-minus text-3xl md:text-4xl text-red-200"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Material Tidak Ditemukan</h3>
                        <p class="text-gray-500 px-4">Maaf, tidak ada material yang cocok dengan kata kunci <strong>"{{ $query }}"</strong></p>
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- TOAST NOTIFICATION --}}
    <div id="copyToast" class="fixed bottom-6 right-6 md:right-10 transform translate-y-20 opacity-0 transition-all duration-300 z-50">
        <div class="bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
            <i class="fas fa-check-circle text-green-400"></i>
            <span class="font-medium">Kode berhasil disalin!</span>
        </div>
    </div>

@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script>
    // 1. Fungsi untuk menampilkan Toast Notifikasi (Sesuai desain Anda)
    function showCopyToast() {
        const toast = document.getElementById('copyToast');
        if (toast) {
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 2000);
        }
    }

    // 2. Fungsi Fallback (Cara Lama - Agar jalan di HTTP / IP Address)
    function fallbackCopyTextToClipboard(text) {
        var textArea = document.createElement("textarea");
        textArea.value = text;
        
        textArea.style.top = "0";
        textArea.style.left = "0";
        textArea.style.position = "fixed";

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            var successful = document.execCommand('copy');
            if (successful) {
                showCopyToast(); 
            } else {
                alert('Gagal menyalin kode (Fallback error).');
            }
        } catch (err) {
            console.error('Fallback error:', err);
            alert('Browser tidak mendukung copy otomatis.');
        }

        document.body.removeChild(textArea);
    }

    // 3. Fungsi Utama (Dipanggil oleh tombol)
    function copyToClipboard(text) {
        if (!text || text === '-') return;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                showCopyToast(); // Sukses via API Modern
            }, function(err) {
                // Jika API Modern gagal, gunakan cara lama
                console.warn('Modern copy failed, trying fallback...', err);
                fallbackCopyTextToClipboard(text);
            });
        } else {
            // Jika akses via HTTP (IP Address), langsung gunakan cara lama
            fallbackCopyTextToClipboard(text);
        }
    }
</script>
@endpush