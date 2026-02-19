@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="flex items-center justify-between mb-4">
                <ol class="breadcrumb bg-white px-4 py-2 rounded-lg shadow flex items-center space-x-2">
                    <li class="breadcrumb-item">
                        <a href="{{ route('newrbd.index') }}" class="text-blue-600 hover:underline font-medium">
                            RBD Diagram
                        </a>
                    </li>
                    <li class="breadcrumb-item text-gray-700 font-semibold">Visualisasi RBD</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="showNodeDetails" checked>
            <label class="form-check-label" for="showNodeDetails">
                Tampilkan Detail Node (FR, t₀, t)
            </label>
        </div>
    </div>
    <div id="rbdDiagram"></div>
    <p class="mb-2">
        <strong>At t =</strong>
        <span class="font-mono text-primary">
            {{ number_format($timeInterval, 2) }}
        </span>
        <small class="text-muted">hours</small>,
        <strong class="ms-2">Reliability:</strong>
        <span
            class="font-mono ms-1
        @if ($systemReliability < 1e-6) text-danger
        @elseif($systemReliability < 1e-3) text-warning
        @elseif($systemReliability < 0.9) text-info
        @else text-success @endif"
            title="R(t) = {{ $systemReliability }}">
            @if ($systemReliability < 1e-5)
                <span class="text-danger">{{ sprintf('%.6E', $systemReliability) }}</span>
            @else
                {{ number_format($systemReliability, 6) }}
            @endif
        </span>
    </p>

    <p class="mb-1">
        <strong>Failure Rate:</strong>
        <span
            class="font-mono
        @if ($failureRate > 1e-3) text-danger
        @elseif($failureRate > 1e-5) text-warning
        @else text-success @endif"
            title="Nilai eksak: {{ $failureRate }}">
            {{ $failureRate > 0 ? sprintf('%.2E', $failureRate) : '0.00E+0' }}
        </span>
        <small class="text-muted">per jam</small>
    </p>

    {{-- === INFORMASI BARU DARI PYTHON === --}}
    <div class="mt-3 p-3 border rounded bg-light small">
        <p class="mb-2"><strong>Symbolic Expressions:</strong></p>

        <div class="row g-2">
            <div class="col-md-6">
                <strong>R(t):</strong>
                <code class="d-block text-monospace text-wrap bg-white p-1 rounded">
                    {{ $r_t_symbolic ?? '—' }}
                </code>
            </div>

            <div class="col-md-6">
                <strong>h(t) (Hazard):</strong>
                <code class="d-block text-monospace text-wrap bg-white p-1 rounded">
                    {{ $hazard_rate_expression ?? '—' }}
                </code>
            </div>

            <div class="col-md-6">
                <strong>f(t) (Frequency):</strong>
                <code class="d-block text-monospace text-wrap bg-white p-1 rounded">
                    {{ $frequency_expression ?? '—' }}
                </code>
            </div>

            <div class="col-md-6">
                <strong>t (symbolic):</strong>
                <code class="d-block text-monospace bg-white p-1 rounded">
                    {{ $t_expression ?? 't' }}
                </code>
            </div>
        </div>

        <hr class="my-2">

        <p class="mb-0">
            <strong>t (numerik):</strong>
            <span class="font-mono text-primary">
                {{ $t_value ? number_format($t_value, 2) : '—' }}
            </span>
            <small class="text-muted">jam</small>
        </p>
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

        #systemReliability {
            margin-top: 10px;
            font-weight: bold;
            font-size: 18px;
        }



        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- jQuery dulu -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- GoJS -->
    <script src="https://unpkg.com/gojs/release/go.js"></script>
    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Gunakan jQuery untuk DOM, GoJS untuk diagram
        jQuery(document).ready(function($) { // <-- $ adalah jQuery di sini
            if (typeof go === 'undefined') {
                console.error("GoJS belum dimuat!");
                return;
            }

            // Gunakan go.GraphObject.make, jangan pakai $
            const myDiagram = go.GraphObject.make;

            const diagram = myDiagram(go.Diagram, "rbdDiagram", {
                "undoManager.isEnabled": true,
                allowMove: true,
                initialContentAlignment: go.Spot.Left,
                "ModelChanged": function(e) {
                    console.log("Model changed:", e);
                }
            });

            // === NODE TEMPLATE ===
            diagram.nodeTemplateMap.add("", myDiagram(go.Node, "Vertical", {
                    locationSpot: go.Spot.Center,
                    location: new go.Point(0, 0)
                },
                new go.Binding("location", "", d => new go.Point(d.x || 0, d.y || 0)).makeTwoWay(),
                myDiagram(go.Panel, "Auto",
                    myDiagram(go.Shape, "Rectangle", {
                        fill: "#dbeef4",
                        stroke: null,
                        width: 160,
                        height: 70
                    }),
                    myDiagram(go.Panel, "Table", {
                            width: 160,
                            margin: 0,
                            stretch: go.GraphObject.Fill
                        },
                        myDiagram(go.TextBlock, {
                                row: 0,
                                font: "bold 11px sans-serif",
                                stroke: "black",
                                textAlign: "center",
                                alignment: go.Spot.Center,
                                margin: new go.Margin(2, 0, 8, 0),
                                maxLines: 1,
                                overflow: go.TextBlock.OverflowEllipsis
                            },
                            new go.Binding("text", "code")
                        ),
                        myDiagram(go.TextBlock, {
                                row: 0,
                                alignment: go.Spot.TopRight,
                                margin: new go.Margin(0, 5, 0, 0),
                                font: "bold 10px sans-serif",
                                stroke: "black",
                                visible: false
                            },
                            new go.Binding("text", "shownumber"),
                            new go.Binding("visible", "configuration", c => c && c !== "single")
                        ),
                        myDiagram(go.Shape, "LineH", {
                            row: 1,
                            stroke: "black",
                            strokeWidth: 1,
                            stretch: go.GraphObject.Horizontal,
                            height: 1,
                            alignment: go.Spot.Top
                        }),
                        myDiagram(go.TextBlock, {
                                row: 2,
                                font: "11px sans-serif",
                                stroke: "black",
                                textAlign: "center",
                                wrap: go.TextBlock.WrapFit,
                                alignment: go.Spot.Center,
                                margin: new go.Margin(2, 0, 2, 0)
                            },
                            new go.Binding("text", "name")
                        )
                    ),
                    myDiagram(go.Shape, "LineH", {
                        alignment: go.Spot.Bottom,
                        stroke: "black",
                        strokeWidth: 1,
                        width: 160,
                        height: 5
                    }),
                    myDiagram(go.Shape, "LineV", {
                        alignment: go.Spot.Right,
                        stroke: "black",
                        strokeWidth: 1,
                        width: 1,
                        height: 70
                    })
                ),

                // FR
                myDiagram(go.TextBlock, {
                        font: "10px monospace",
                        visible: false,
                        name: "FR_TEXT"
                    },
                    new go.Binding("text", "", d => d.failure_rate ?
                        `FR: ${parseFloat(d.failure_rate).toExponential(5)}/hr` :
                        (d.foreign_r ? `R: ${d.foreign_r}` : "")
                    ),
                    new go.Binding("visible", "", () => $("#showNodeDetails").is(":checked")).ofObject()
                ),

                // t₀
                myDiagram(go.TextBlock, {
                        font: "10px monospace",
                        margin: new go.Margin(2, 0, 0, 0),
                        visible: false,
                        name: "T0_TEXT"
                    },
                    new go.Binding("text", "", d => d.t_initial != null ?
                        `t₀: ${Number(d.t_initial).toFixed(2)} hr` : ""),
                    new go.Binding("visible", "", () => $("#showNodeDetails").is(":checked")).ofObject()
                ),

                // t
                myDiagram(go.TextBlock, {
                        font: "10px monospace",
                        margin: new go.Margin(2, 0, 0, 0),
                        visible: false,
                        name: "T_TEXT"
                    },
                    new go.Binding("text", "", d => d.time_interval != null ?
                        `t: ${Number(d.time_interval).toFixed(2)} hr` : ""),
                    new go.Binding("visible", "", () => $("#showNodeDetails").is(":checked")).ofObject()
                )
            ));

            // === JUNCTION ===
            const junctionTemplate = myDiagram(go.Node, "Spot", {
                    locationSpot: go.Spot.Center
                },
                new go.Binding("location", "", d => new go.Point(d.x || 0, d.y || 0)).makeTwoWay(),
                myDiagram(go.Shape, "Rectangle", {
                    width: 20,
                    height: 20,
                    fill: "#dbeef4",
                    stroke: null
                }),
                myDiagram(go.TextBlock, {
                        font: "10px sans-serif"
                    },
                    new go.Binding("text", r => r.k && r.n ? `${r.k}-of-${r.n}` : "")
                )
            );
            diagram.nodeTemplateMap.add("junction", junctionTemplate);
            diagram.nodeTemplateMap.add("start", junctionTemplate);
            diagram.nodeTemplateMap.add("end", junctionTemplate);

            // === LINK ===
            diagram.linkTemplate = myDiagram(go.Link, {
                    routing: go.Link.Orthogonal,
                    corner: 5,
                    toEndSegmentLength: 20
                },
                myDiagram(go.Shape, {
                    strokeWidth: 2,
                    stroke: "#4343a8"
                }),
                myDiagram(go.Shape, {
                    toArrow: "Standard",
                    fill: "#00008b",
                    stroke: "#00008b"
                })
            );

            // === DATA ===
            const model = myDiagram(go.GraphLinksModel, {
                nodeKeyProperty: "key_value",
                nodeDataArray: @json($data['nodeDataArray']),
                linkDataArray: @json($data['linkDataArray'])
            });
            diagram.model = model;

            // === CHECKBOX CONTROL ===
            $("#showNodeDetails").on("change", function() {
                const show = this.checked;
                diagram.nodes.each(node => {
                    ["FR_TEXT", "T0_TEXT", "T_TEXT"].forEach(name => {
                        const tb = node.findObject(name);
                        if (tb && tb.text) {
                            tb.visible = show && tb.text.trim() !== "";
                        }
                    });
                });
                diagram.requestUpdate();
            });

            // Terapkan saat load
            $("#showNodeDetails").trigger("change");

        }); // end jQuery(document).ready
    </script>
@endpush
