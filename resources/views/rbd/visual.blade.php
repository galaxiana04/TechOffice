@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="flex items-center justify-between mb-4">
                <ol class="breadcrumb bg-white px-4 py-2 rounded-lg shadow flex items-center space-x-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('rbd.index') }}" class="text-blue-600 hover:underline font-medium">
                            RBD Identities
                        </a>
                    </li>
                    <li class="breadcrumb-item text-gray-700 font-semibold">Visualisasi RBD</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="space-y-6">
        <!-- Identity Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="p-4 bg-white shadow rounded-xl border border-gray-100">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">RBD Visual & Reliability Result</h1>
                <p class="text-gray-500">Analisis reliabilitas sistem berdasarkan diagram blok</p>
            </div>
            <div class="p-4 bg-white shadow rounded-xl border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Component Name</p>
                <p class="text-lg font-semibold text-gray-800">{{ $rbdIdentity->componentname }}</p>
            </div>
            <div class="p-4 bg-white shadow rounded-xl border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Project Type</p>
                <p class="text-lg font-semibold text-gray-800">{{ $rbdIdentity->projectType->title ?? 'N/A' }}</p>
            </div>
            <div class="p-4 bg-white shadow rounded-xl border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Nilai T dan Reliabilitas</p>
                <ul class="space-y-2 text-gray-700">
                    <li><strong>Time (t):</strong> {{ $t }} jam</li>
                    <li><strong>System Reliability R(t):</strong> {{ number_format($reliability, 6) }}</li>
                </ul>
                <form action="{{ route('rbd.project', $rbdIdentity->id) }}" method="GET" class="mt-4">
                    <label for="t" class="text-sm text-gray-500">Ubah Nilai T (jam):</label>
                    <div class="flex items-center space-x-2">
                        <input type="number" name="t" id="t" value="{{ $t }}" min="0"
                            step="0.1"
                            class="border border-gray-300 rounded-lg p-2 w-32 focus:ring-2 focus:ring-blue-600">
                        <button type="submit" class="btn bg-maroon btn-sm">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Diagram -->
        <div class="p-5 bg-white rounded-xl shadow border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Block Diagram</h2>
            @if ($diagram)
                <div class="border border-dashed border-gray-300 rounded-lg bg-gray-50 p-4 overflow-x-auto relative">
                    <pre class="mermaid">
                                                                graph LR
                                                                {{ $diagram }}
                                                                                    </pre>
                </div>
            @else
                <div class="alert alert-warning text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    Tidak ada diagram yang tersedia. Pastikan root node telah dikonfigurasi di
                    <a href="{{ route('rbd.nodes.edit', $rbdIdentity->id) }}"
                        class="text-blue-600 hover:underline">pengaturan
                        node</a>.
                </div>
            @endif
        </div>

        <!-- Informasi Tambahan -->
        @if ($roots)
            <div class="p-5 bg-white rounded-xl shadow border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Informasi Struktur Node</h2>
                <ul class="space-y-2 text-gray-700">
                    <li><strong>Root Node Type:</strong> {{ ucfirst($roots->type) }}</li>
                    @if ($roots->type === 'block')
                        <li><strong>Block Group Type:</strong> {{ ucfirst($roots->block_group_type ?? 'single') }}</li>
                        @if ($roots->block_group_type !== 'single' && $roots->block_group_type)
                            <li><strong>Number of Components:</strong> {{ $roots->block_count ?? 'N/A' }}</li>
                            @if ($roots->block_group_type === 'k-out-of-n')
                                <li><strong>K Value:</strong> {{ $roots->k_value ?? 'N/A' }}</li>
                            @endif
                        @endif
                        <li><strong>Block Name:</strong> {{ $roots->rbdBlock->name ?? 'N/A' }}</li>
                        @if (!empty($roots->rbdBlock->source))
                            <span class="keterangan">({{ $roots->rbdBlock->source }})</span>
                        @endif
                        <li><strong>Lambda:</strong> {{ $roots->rbdBlock->lambda ?? 'N/A' }}</li>
                    @elseif ($roots->type === 'k-out-of-n')
                        <li><strong>K Value:</strong> {{ $roots->k_value ?? 'N/A' }}</li>
                        <li><strong>Number of Children:</strong> {{ $roots->children->count() }}</li>
                    @else
                        <li><strong>Number of Children:</strong> {{ $roots->children->count() }}</li>
                    @endif
                </ul>
            </div>
        @else
            <div class="alert alert-warning text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                Tidak ada root node ditemukan. Silakan konfigurasikan node di
                <a href="{{ route('rbd.nodes.edit', $rbdIdentity->id) }}" class="text-blue-600 hover:underline">pengaturan
                    node</a>.
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script type="module">
        import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
        try {
            mermaid.initialize({
                startOnLoad: true,
                theme: 'neutral',
                flowchart: {
                    useMaxWidth: true,
                    htmlLabels: true,
                },
            });
            console.log('Mermaid initialized');
        } catch (error) {
            console.error('Mermaid initialization failed:', error);
        }
    </script>
@endpush

@push('css')
    <style>
        .keterangan {
            font-size: 0.85rem;
            color: #6c757d;
            font-style: italic;
            margin-left: 4px;
        }

        .nodeTooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            pointer-events: none;
            white-space: nowrap;
            z-index: 1000;
            display: none;
        }
    </style>
@endpush
