@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="flex items-center justify-between mb-4">
                <ol class="breadcrumb bg-white px-4 py-2 rounded-lg shadow flex items-center space-x-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('fta.index') }}" class="text-blue-600 hover:underline font-medium">
                            FTA Identities
                        </a>
                    </li>
                    <li class="breadcrumb-item text-gray-700 font-semibold">FTA Visualization</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="p-4 bg-white shadow rounded-xl border border-gray-100">
                <h1 class="text-2xl font-bold text-gray-800 mb-2">FTA Visualization & CFI</h1>
                <p class="text-gray-500">Conditional Failure Intensity Analysis based on Fault Tree</p>
            </div>
            <div class="p-4 bg-white shadow rounded-xl border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Component Name</p>
                <p class="text-lg font-semibold text-gray-800">{{ $ftaIdentity->componentname }}</p>
            </div>
            <div class="p-4 bg-white shadow rounded-xl border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Project Type</p>
                <p class="text-lg font-semibold text-gray-800">{{ $ftaIdentity->projectType->title ?? 'N/A' }}</p>
            </div>
            <div class="p-4 bg-white shadow rounded-xl border border-gray-100">
                <p class="text-sm text-gray-500 mb-1">Conditional Failure Intensity (CFI)</p>
                <p class="text-lg font-semibold text-gray-800">λ = {{ $cfi }}</p>
            </div>
        </div>

        <!-- Fault Tree Diagram -->
        <div class="p-5 bg-white rounded-xl shadow border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Fault Tree Diagram</h2>
            @if ($diagram)
                <div class="relative border border-dashed border-gray-300 rounded-lg bg-gray-50 p-4">

                    <!-- Kontrol Zoom -->
                    <div class="absolute top-2 right-2 flex space-x-2 z-10">
                        <button id="zoom-in" class="btn bg-maroon btn-sm">+</button>
                        <button id="zoom-out" class="btn bg-teal btn-sm">-</button>
                        <button id="reset-zoom" class="btn bg-orange btn-sm">Reset</button>
                    </div>

                    <!-- Container dengan scroll + pan -->
                    <div id="diagram-container" class="overflow-auto border border-gray-200 bg-white rounded-lg"
                        style="width:100%; height:600px; cursor: grab;">
                        <div id="diagram-content" style="transform: scale(1); transform-origin: 0 0;">
                            <pre class="mermaid">
                        graph TD
                        {{ $diagram }}
                    </pre>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    No fault tree available. Please configure the root node in
                    <a href="{{ route('fta.nodes.edit', $ftaIdentity->id) }}" class="text-blue-600 hover:underline">node
                        settings</a>.
                </div>
            @endif
        </div>


        <!-- Node Structure Information -->
        @if ($roots)
            <div class="p-5 bg-white rounded-xl shadow border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Node Structure Information</h2>
                <ul class="space-y-2 text-gray-700">
                    <li><strong>Root Node Type:</strong> {{ ucfirst($roots->type) }}</li>
                    <li><strong>Event Name:</strong>
                        @if ($roots->type === 'basic_event')
                            {{ $roots->ftaEvent->name ?? 'N/A' }}
                            @if (!empty($roots->ftaEvent->source))
                                <span class="keterangan">({{ $roots->ftaEvent->source }})</span>
                            @endif
                        @else
                            {{ $roots->event_name ?? 'N/A' }}
                        @endif
                    </li>
                    @if ($roots->type === 'basic_event')
                        <li><strong>Failure Rate (λ):</strong> {{ number_format($roots->ftaEvent->failure_rate ?? 0, 6) }}
                        </li>
                    @endif
                    @if ($roots->type !== 'basic_event')
                        <li><strong>Number of Children:</strong> {{ $roots->children->count() }}</li>
                    @endif
                    <li><strong>Calculated CFI (λ):</strong> {{ number_format($cfi ?? 0, 6) }}</li>
                </ul>
            </div>
        @else
            <div class="alert alert-warning text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                No root node found. Please configure nodes in
                <a href="{{ route('fta.nodes.edit', $ftaIdentity->id) }}" class="text-blue-600 hover:underline">node
                    settings</a>.
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
                    htmlLabels: true
                },
                securityLevel: 'loose'
            });
            mermaid.contentLoaded();
        } catch (error) {
            console.error('Mermaid initialization failed:', error);
        }

        // Zoom logic
        let scale = 1;
        const zoomStep = 0.1;
        const diagramContent = document.getElementById('diagram-content');

        document.getElementById('zoom-in').addEventListener('click', () => {
            scale += zoomStep;
            diagramContent.style.transform = `scale(${scale})`;
        });

        document.getElementById('zoom-out').addEventListener('click', () => {
            if (scale > zoomStep) {
                scale -= zoomStep;
                diagramContent.style.transform = `scale(${scale})`;
            }
        });

        document.getElementById('reset-zoom').addEventListener('click', () => {
            scale = 1;
            diagramContent.style.transform = `scale(${scale})`;
        });

        // Pan logic (drag scroll)
        const container = document.getElementById('diagram-container');
        let isDown = false;
        let startX, startY, scrollLeft, scrollTop;

        container.addEventListener('mousedown', (e) => {
            isDown = true;
            container.style.cursor = 'grabbing';
            startX = e.pageX - container.offsetLeft;
            startY = e.pageY - container.offsetTop;
            scrollLeft = container.scrollLeft;
            scrollTop = container.scrollTop;
        });

        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.style.cursor = 'grab';
        });

        container.addEventListener('mouseup', () => {
            isDown = false;
            container.style.cursor = 'grab';
        });

        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const y = e.pageY - container.offsetTop;
            const walkX = (x - startX);
            const walkY = (y - startY);
            container.scrollLeft = scrollLeft - walkX;
            container.scrollTop = scrollTop - walkY;
        });
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

        .mermaid {
            min-height: 300px;
            overflow-x: auto;
        }

        .mermaid svg {
            max-width: 100%;
        }
    </style>
@endpush
