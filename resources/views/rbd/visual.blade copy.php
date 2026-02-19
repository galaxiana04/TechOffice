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
                <button id="calcButton" class="mt-4 btn bg-blue-600 text-white btn-sm hover:bg-blue-800">Calculate System
                    Reliability</button>
            </div>
        </div>

        <!-- Diagram -->
        <div class="p-5 bg-white rounded-xl shadow border border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800 mb-3">Block Diagram</h2>
            @if ($diagram)
                <div id="rbdDiagram"
                    class="border border-dashed border-gray-300 rounded-lg bg-gray-50 overflow-x-auto relative"
                    style="width: 100%; height: 650px;"></div>
            @else
                <div class="alert alert-warning text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    Tidak ada diagram yang tersedia. Pastikan node telah dikonfigurasi di
                    <a href="{{ route('rbd.nodes.edit', $rbdIdentity->id) }}"
                        class="text-blue-600 hover:underline">pengaturan node</a>.
                </div>
            @endif
        </div>

        <!-- Informasi Tambahan -->
        @if ($nodeDataArray)
            <div class="p-5 bg-white rounded-xl shadow border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Informasi Struktur Node</h2>
                <table class="w-full text-gray-700">
                    <thead>
                        <tr class="border-b">
                            <th class="p-2 text-left">Code</th>
                            <th class="p-2 text-left">Name</th>
                            <th class="p-2 text-left">Failure Rate</th>
                            <th class="p-2 text-left">Source</th>
                            <th class="p-2 text-left">Reliability</th>
                            <th class="p-2 text-left">Position (X,Y)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($nodeDataArray as $node)
                            <tr class="border-b">
                                <td class="p-2">{{ $node['code'] }}</td>
                                <td class="p-2">{{ $node['name'] }}</td>
                                <td class="p-2">{{ $node['fr'] ?? 'N/A' }}</td>
                                <td class="p-2">{{ $node['source'] }}</td>
                                <td class="p-2">{{ $node['reliability'] }}</td>
                                <td class="p-2">({{ $node['x'] }}, {{ $node['y'] }})</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-warning text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                Tidak ada node ditemukan. Silakan konfigurasikan node di
                <a href="{{ route('rbd.nodes.edit', $rbdIdentity->id) }}" class="text-blue-600 hover:underline">pengaturan
                    node</a>.
            </div>
        @endif
    </div>
@endsection

@push('css')
    <style>
        #rbdDiagram {
            width: 100%;
            height: 650px;
            border: 1px solid #ccc;
            background: #ffffff;
        }

        #calcButton {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4343a8;
            color: white;
            border: none;
            border-radius: 5px;
        }

        #calcButton:hover {
            background-color: #00008b;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/gojs/release/go.js"></script>
    <script>
        const $ = go.GraphObject.make;

        const diagram = $(go.Diagram, "rbdDiagram", {
            "undoManager.isEnabled": true,
            allowMove: true
        });

        diagram.nodeTemplate =
            $(go.Node, "Vertical", {
                    locationSpot: go.Spot.Center
                },
                new go.Binding("location", "", node => new go.Point(node.data.x, node.data.y)).makeTwoWay(
                    (point, node) => ({
                        x: point.x,
                        y: point.y
                    })
                ),
                $(go.Panel, "Auto",
                    $(go.Shape, "Rectangle", {
                        fill: "#dbeef4",
                        stroke: null,
                        width: 160,
                        height: 70
                    }),
                    $(go.Panel, "Table", {
                            width: 160,
                            margin: 0,
                            stretch: go.GraphObject.Fill
                        },
                        $(go.TextBlock, {
                                row: 0,
                                font: "bold 11px sans-serif",
                                stroke: "black",
                                textAlign: "center",
                                alignment: go.Spot.Center,
                                margin: new go.Margin(2, 0, 8, 0),
                                maxLines: 1,
                                overflow: go.TextBlock.OverflowEllipsis
                            },
                            new go.Binding("text", "code")),
                        $(go.Shape, "LineH", {
                            row: 1,
                            stroke: "black",
                            strokeWidth: 1,
                            stretch: go.GraphObject.Horizontal,
                            height: 1,
                            alignment: go.Spot.Top,
                            margin: new go.Margin(0, 0, 0, 0)
                        }),
                        $(go.TextBlock, {
                                row: 2,
                                font: "11px sans-serif",
                                stroke: "black",
                                textAlign: "center",
                                wrap: go.TextBlock.WrapFit,
                                alignment: go.Spot.Center,
                                margin: new go.Margin(2, 0, 2, 0)
                            },
                            new go.Binding("text", "name"))
                    ),
                    $(go.Shape, "LineH", {
                        alignment: go.Spot.Bottom,
                        stroke: "black",
                        strokeWidth: 1,
                        width: 160,
                        height: 1
                    }),
                    $(go.Shape, "LineV", {
                        alignment: go.Spot.Right,
                        stroke: "black",
                        strokeWidth: 1,
                        width: 1,
                        height: 70
                    })
                ),
                $(go.TextBlock, {
                        font: "10px monospace",
                        stroke: "#333",
                        margin: new go.Margin(4, 0, 0, 0)
                    },
                    new go.Binding("text", "fr", fr => fr ? `FR = ${fr}` : "")),
                $(go.TextBlock, {
                        font: "10px monospace",
                        stroke: "#333",
                        margin: new go.Margin(2, 0, 0, 0)
                    },
                    new go.Binding("text", "source", source => `Source: ${source}`)),
                $(go.TextBlock, {
                        font: "10px monospace",
                        stroke: "#333",
                        margin: new go.Margin(2, 0, 0, 0)
                    },
                    new go.Binding("text", "reliability", r => `R(t) = ${r}`)),
                $(go.TextBlock, {
                        font: "10px monospace",
                        stroke: "#333",
                        margin: new go.Margin(2, 0, 0, 0)
                    },
                    new go.Binding("text", "", node => `X,Y = (${node.data.x}, ${node.data.y})`))
            );

        diagram.linkTemplate =
            $(go.Link, {
                    routing: go.Link.Orthogonal,
                    corner: 5,
                    toEndSegmentLength: 20
                },
                $(go.Shape, {
                    strokeWidth: 2,
                    stroke: "#4343a8"
                }),
                $(go.Shape, {
                    toArrow: "Standard",
                    fill: "#00008b",
                    stroke: "#00008b"
                })
            );

        // Load diagram data
        const data = {
            "class": "go.GraphLinksModel",
            "nodeDataArray": @json($nodeDataArray),
            "linkDataArray": @json($linkDataArray)
        };

        diagram.model = go.Model.fromJson(data);

        // Reliability calculation
        function calculateReliability() {
            const t = {{ $t }};
            const nodes = @json($nodeDataArray);

            // Calculate individual reliabilities
            const reliabilities = nodes.map(node => {
                const lambda = parseFloat(node.fr) || 0;
                const reliability = lambda ? Math.exp(-lambda * t) : 1;
                return {
                    id: node.key,
                    reliability: reliability
                };
            }).reduce((acc, node) => {
                acc[node.id] = node.reliability;
                return acc;
            }, {});

            // Find output junction (node with multiple incoming links)
            const links = @json($linkDataArray);
            const outputNodeId = links.reduce((acc, link) => {
                acc[link.to] = (acc[link.to] || 0) + 1;
                return acc;
            }, {});
            const outputKey = Object.keys(outputNodeId).find(key => outputNodeId[key] > 1);
            const parallelNodes = nodes.filter(node => node.source === "Parallel").map(node => node.key);

            // Calculate series reliability
            let seriesReliability = 1;
            let currentNode = nodes.find(node => !links.some(link => link.to === node.key));
            while (currentNode && currentNode.key != outputKey && !parallelNodes.includes(currentNode.key)) {
                seriesReliability *= reliabilities[currentNode.key];
                const nextLink = links.find(link => link.from === currentNode.key);
                currentNode = nextLink ? nodes.find(node => node.key === nextLink.to) : null;
            }

            // Calculate parallel reliability
            let parallelReliability = 1;
            parallelNodes.forEach(nodeId => {
                parallelReliability *= (1 - reliabilities[nodeId]);
            });
            parallelReliability = 1 - parallelReliability;

            // Calculate output reliability
            const outputReliability = outputKey ? reliabilities[outputKey] : 1;

            const R_system = seriesReliability * parallelReliability * outputReliability;
            alert(`System Reliability at t = ${t} hours: ${R_system.toFixed(6)}`);
        }

        document.getElementById("calcButton").addEventListener("click", calculateReliability);
    </script>
@endpush
