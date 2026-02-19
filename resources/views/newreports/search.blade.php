@extends('layouts.universal')

@section('container2')
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 shadow-sm py-6">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-search text-blue-600 mr-2"></i>Pencarian Dokumen
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb bg-white px-4 py-2 rounded-lg float-right shadow-sm mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('newreports.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-list mr-1"></i>List Unit
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-gray-600">Search</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container mx-auto px-4 py-10">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div
                    class="card border-0 shadow-2xl rounded-3xl overflow-hidden transform transition duration-500 hover:scale-[1.01]">

                    <div
                        class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-center py-10 px-8 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full -mt-10 -ml-10"></div>
                        <div class="absolute bottom-0 right-0 w-40 h-40 bg-white opacity-10 rounded-full -mb-10 -mr-10">
                        </div>

                        <h2 class="text-3xl font-bold text-white mb-2 relative z-10">Temukan Dokumen Anda</h2>
                        <p class="text-blue-100 relative z-10">Cari berdasarkan Nomor Dokumen, Nama, atau Drafter dengan
                            cepat.</p>
                    </div>

                    <div class="bg-white p-8 md:p-10">
                        <form action="{{ route('newprogressreports.search') }}" method="GET">
                            <div class="form-group mb-6">
                                <label for="query" class="block text-gray-700 font-bold mb-3 text-lg pl-1">
                                    Kata Kunci Pencarian
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400 text-xl"></i>
                                    </div>

                                    <input type="text" name="query" id="query"
                                        class="w-full pl-16 pr-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition duration-300 text-gray-700 placeholder-gray-400 shadow-sm"
                                        placeholder="No Dokumen / Nama / Drafter...">
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit"
                                    class="btn w-full md:w-auto px-8 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-lg shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-indigo-700 transform hover:-translate-y-1 transition duration-300 border-0">
                                    <i class="fas fa-paper-plane mr-2"></i>Lakukan Pencarian
                                </button>
                            </div>
                        </form>

                        <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                            <p class="text-gray-500 text-sm mb-3">Tips Pencarian:</p>
                            <div class="flex flex-wrap justify-center gap-2">
                                <span
                                    class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-medium border border-blue-100">
                                    <i class="fas fa-tag mr-1"></i>No. Dokumen
                                </span>
                                <span
                                    class="px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-xs font-medium border border-purple-100">
                                    <i class="fas fa-file-alt mr-1"></i>Nama Dokumen
                                </span>
                                <span
                                    class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs font-medium border border-green-100">
                                    <i class="fas fa-user mr-1"></i>Drafter/Checker
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom styles untuk mempercantik input focus */
        input:focus {
            outline: none;
        }

        .btn:focus {
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);
        }
    </style>
@endpush