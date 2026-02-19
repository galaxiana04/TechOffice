@extends('layouts.universal')

@php
    use Carbon\Carbon;
@endphp

@section('container2')
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 shadow-sm py-6">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-1 flex items-center gap-3">
                        <i class="fas fa-folder-open text-blue-600"></i>
                        Hasil Pencarian
                    </h1>
                    <p class="text-gray-500 text-sm mb-0 ml-1">Menampilkan <strong>{{ $results->count() }}</strong> dokumen yang sesuai</p>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb bg-white px-4 py-2 rounded-lg float-right shadow-sm mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('newreports.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('newreports.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">Cari</a>
                        </li>
                        <li class="breadcrumb-item active text-gray-600">Hasil</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container mx-auto px-4 py-8">
        @if (isset($results) && $results->count())
            <div class="space-y-5" id="resultsAccordion">
                @foreach ($results as $result)
                    @php
                        $accordionId = 'result-' . $result->id;
                        $latestRev = $result->getLatestRevAttribute();
                        $status = $latestRev->status ?? ($result->status ?? '—');
                        
                        // Warna Badge Status
                        $badgeColor = 'bg-gray-100 text-gray-600 border-gray-200'; // Default
                        if($status == 'RELEASED' || $status == 'Completed') {
                            $badgeColor = 'bg-green-100 text-green-700 border-green-200';
                        } elseif ($status == 'In Progress') {
                            $badgeColor = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                        } elseif ($status == 'Terbuka') {
                            $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                        }
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden transition duration-300 hover:shadow-md group">
                        
                        <div class="p-0" id="heading-{{ $accordionId }}">
                            <button class="w-full text-left px-6 py-5 focus:outline-none bg-white hover:bg-gray-50 transition duration-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4" 
                                    type="button"
                                    data-toggle="collapse" 
                                    data-target="#{{ $accordionId }}" 
                                    aria-expanded="false"
                                    aria-controls="{{ $accordionId }}">
                                
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                            <i class="fas fa-file-alt text-lg"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition duration-200">
                                            {{ $result->namadokumen }}
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 mt-1">
                                            <span class="font-mono bg-gray-100 px-2 py-0.5 rounded text-gray-700 border border-gray-200 font-medium inline-flex items-center gap-1.5">
                                                {{ $result->nodokumen }}
                                            </span>

                                            <span class="hidden md:inline text-gray-300">|</span>
                                            <span>
                                                <i class="fas fa-code-branch text-xs mr-1"></i> Rev: 
                                                <span class="font-semibold text-gray-700">{{ $latestRev->rev ?? '-' }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 ml-14 md:ml-0">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border {{ $badgeColor }}">
                                        {{ $status }}
                                    </span>
                                    <span class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-blue-100 group-hover:text-blue-600 transition duration-200 transform accordion-icon">
                                        <i class="fas fa-chevron-down text-sm"></i>
                                    </span>
                                </div>
                            </button>
                        </div>

                        <div id="{{ $accordionId }}" class="collapse" aria-labelledby="heading-{{ $accordionId }}" data-parent="#resultsAccordion">
                            <div class="px-6 pb-6 pt-0 bg-gray-50 border-t border-gray-100">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    
                                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                                        <h6 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Informasi Dokumen</h6>
                                        <div class="space-y-3 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Level</span>
                                                <span class="font-medium text-gray-800">{{ $result->level }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Drafter</span>
                                                <span class="font-medium text-gray-800">{{ $result->drafter }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Checker</span>
                                                <span class="font-medium text-gray-800">{{ $result->checker }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Jenis</span>
                                                <span class="font-medium text-blue-600">{{ $result->documentkind->name ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                                        <h6 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Timeline & Proyek</h6>
                                        <div class="space-y-3 text-sm">
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Deadline</span>
                                                <span class="font-medium text-red-500">
                                                    {{ $result->deadlinerelease ? Carbon::parse($result->deadlinerelease)->format('d M Y') : '-' }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Realisasi</span>
                                                <span class="font-medium text-green-600">
                                                    {{ $result->realisasi }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Unit</span>
                                                <span class="font-medium text-gray-800">{{ $result->newreport->unit ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Proyek</span>
                                                <span class="font-medium text-gray-800 text-right truncate w-1/2" title="{{ $result->newreport->projectType->title ?? '-' }}">
                                                    {{ $result->newreport->projectType->title ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    @if ($result->latestHistory && $result->latestHistory->fileid)
                                        @if (config('app.url') !== 'https://inka.goovicess.com')
                                            <a href="http://10.10.0.40/AutodeskTC/10.10.0.40/TekVault_0003_Dec2011/Document/Download?fileId={{ $result->latestHistory->fileid }}&downloadAsInline=true"
                                               class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg hover:from-red-700 hover:to-pink-700 transform hover:-translate-y-0.5 transition duration-200 text-sm" 
                                               target="_blank">
                                                <i class="fas fa-file-pdf mr-2"></i> Buka PDF
                                            </a>
                                        @else
                                            <div class="flex items-center bg-yellow-50 text-yellow-800 px-4 py-2 rounded-lg border border-yellow-200">
                                                <i class="fas fa-exclamation-circle mr-2"></i>
                                                <span class="text-xs font-mono select-all">Downloadfile_{{ $result->latestHistory->fileid }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-400 rounded-lg text-sm cursor-not-allowed">
                                            <i class="fas fa-ban mr-2"></i> PDF Tidak Tersedia
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-3xl shadow-sm border border-gray-100">
                <div class="mb-6 inline-block p-6 rounded-full bg-blue-50">
                    <i class="fas fa-search text-5xl text-blue-200"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Dokumen Tidak Ditemukan</h3>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">
                    Maaf, kami tidak dapat menemukan dokumen yang sesuai dengan kriteria Anda. Coba ubah kata kunci atau filter proyek/unit.
                </p>
                <a href="{{ route('newreports.index') }}" 
                   class="inline-flex items-center px-8 py-3 bg-white border-2 border-blue-600 text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i> Cari Lagi
                </a>
            </div>
        @endif
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Animasi rotasi chevron saat dibuka */
        button[aria-expanded="true"] .accordion-icon {
            transform: rotate(180deg);
            background-color: #dbeafe; /* blue-100 */
            color: #2563eb; /* blue-600 */
        }
    </style>
@endpush

