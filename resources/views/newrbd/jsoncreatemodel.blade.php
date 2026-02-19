@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('newrbd.index') }}">RBD Management</a></li>
                        <li class="breadcrumb-item active">JSON Model Creator</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- FORM -->
            <div class="card shadow mb-4">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold mb-0">
                        Create RBD Model via JSON
                    </h3>
                    <button type="button" class="btn btn-light btn-sm" id="loadExample">
                        Load Example
                    </button>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">JSON Input <span class="text-danger">*</span></label>
                        <textarea class="form-control font-monospace" id="jsonInput" rows="20" spellcheck="false"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" id="validateJson">
                            Validate
                        </button>
                        <button type="button" class="btn btn-success" id="submitJson">
                            Create Model
                        </button>
                        <button type="button" class="btn btn-danger" id="clearJson">
                            Clear
                        </button>
                    </div>

                    <div id="previewContainer" class="d-none mt-3">
                        <h5>JSON Preview</h5>
                        <pre class="bg-light p-3 rounded border" id="jsonPreview"><code></code></pre>
                    </div>
                </div>
            </div>

            <!-- DAFTAR MODEL -->
            <div class="card shadow">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">Recently Created Models</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="modelsTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Instances</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($models as $model)
                                <tr>
                                    <td>{{ $model->id }}</td>
                                    <td>{{ $model->name }}</td>
                                    <td><span class="badge bg-primary">{{ $model->instances_count }}</span></td>
                                    <td>{{ $model->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <a href="{{ route('newrbd.jsonshowmodel', $model->id) }}"
                                            class="btn btn-sm btn-outline-info" target="_blank">
                                            JSON
                                        </a>
                                        <a href="{{ route('newrbd.newrbdinstances', $model->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/vs2015.min.css">
    <style>
        #jsonInput,
        #jsonPreview code {
            font-size: 0.9rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jsonInput = document.getElementById('jsonInput');
            const jsonPreview = document.getElementById('jsonPreview').querySelector('code');
            const previewContainer = document.getElementById('previewContainer');

            // Load Example
            document.getElementById('loadExample').addEventListener('click', () => {
                const example = {
                    "model_name": "RBD Auxiliary Operating Equipment",
                    "model_description": "Model RBD untuk Auxiliary Operating Equipment (R35-KRL-7), semua komponen seri, T = 5 tahun (31200 jam)",
                    "instances": [{
                            "name": "Auxiliary Power System",
                            "time_interval": 31200,
                            "nodes": [{
                                    "key_value": "start",
                                    "category": "start",
                                    "x": 50,
                                    "y": 100
                                },
                                {
                                    "key_value": "bus_fuse",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Bus Fuse",
                                    "x": 200,
                                    "y": 100,
                                    "failure_rate": 1.000000E-08,
                                    "code": "R35-KRL-7.1.1",
                                    "quantity": 3
                                },
                                {
                                    "key_value": "ahb",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "AHB (APS High Speed Circuit Breaker)",
                                    "x": 350,
                                    "y": 100,
                                    "failure_rate": 3.400000E-07,
                                    "code": "R35-KRL-7.1.2",
                                    "quantity": 3
                                },
                                {
                                    "key_value": "aps_240kva",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "APS 240 kVA",
                                    "x": 500,
                                    "y": 100,
                                    "failure_rate": 9.900000E-10,
                                    "code": "R35-KRL-7.1.3",
                                    "quantity": 3
                                },
                                {
                                    "key_value": "transformer_filter",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "Transformer Filter Set per unit",
                                    "x": 650,
                                    "y": 100,
                                    "failure_rate": 4.118100E-07,
                                    "code": "R35-KRL-7.1.4",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "rectifier_100vdc",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "Rectifier 100 VDC Set per unit",
                                    "x": 800,
                                    "y": 100,
                                    "failure_rate": 5.264143E-06,
                                    "code": "R35-KRL-7.1.5",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "expand_box",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Expand Box",
                                    "x": 950,
                                    "y": 100,
                                    "failure_rate": 3.869800E-08,
                                    "code": "R35-KRL-7.1.6",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "end",
                                    "category": "end",
                                    "x": 1100,
                                    "y": 100
                                }
                            ],
                            "links": [{
                                    "from": "start",
                                    "to": "bus_fuse"
                                },
                                {
                                    "from": "bus_fuse",
                                    "to": "ahb"
                                },
                                {
                                    "from": "ahb",
                                    "to": "aps_240kva"
                                },
                                {
                                    "from": "aps_240kva",
                                    "to": "transformer_filter"
                                },
                                {
                                    "from": "transformer_filter",
                                    "to": "rectifier_100vdc"
                                },
                                {
                                    "from": "rectifier_100vdc",
                                    "to": "expand_box"
                                },
                                {
                                    "from": "expand_box",
                                    "to": "end"
                                }
                            ]
                        },
                        {
                            "name": "Transformer Filter Detail",
                            "time_interval": 31200,
                            "nodes": [{
                                    "key_value": "start",
                                    "category": "start",
                                    "x": 50,
                                    "y": 250
                                },
                                {
                                    "key_value": "transformer",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "Transformer",
                                    "x": 200,
                                    "y": 250,
                                    "failure_rate": 8.061000E-08,
                                    "code": "R35-KRL-7.1.4.1",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "filter",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "Filter",
                                    "x": 350,
                                    "y": 250,
                                    "failure_rate": 2.038000E-07,
                                    "code": "R35-KRL-7.1.4.2",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "kontaktor",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "Kontaktor",
                                    "x": 500,
                                    "y": 250,
                                    "failure_rate": 1.274000E-07,
                                    "code": "R35-KRL-7.1.4.3",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "end",
                                    "category": "end",
                                    "x": 650,
                                    "y": 250
                                }
                            ],
                            "links": [{
                                    "from": "start",
                                    "to": "transformer"
                                },
                                {
                                    "from": "transformer",
                                    "to": "filter"
                                },
                                {
                                    "from": "filter",
                                    "to": "kontaktor"
                                },
                                {
                                    "from": "kontaktor",
                                    "to": "end"
                                }
                            ]
                        },
                        {
                            "name": "Rectifier 100 VDC Detail",
                            "time_interval": 31200,
                            "nodes": [{
                                    "key_value": "start",
                                    "category": "start",
                                    "x": 50,
                                    "y": 400
                                },
                                {
                                    "key_value": "transformer_stepdown",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "Transformer (Step Down)",
                                    "x": 200,
                                    "y": 400,
                                    "failure_rate": 8.061000E-08,
                                    "code": "R35-KRL-7.1.5.1",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "rectifier",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "Rectifier",
                                    "x": 350,
                                    "y": 400,
                                    "failure_rate": 8.353300E-08,
                                    "code": "R35-KRL-7.1.5.2",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "non_fuse_breaker",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "Non Fuse Breaker",
                                    "x": 500,
                                    "y": 400,
                                    "failure_rate": 5.100000E-06,
                                    "code": "R35-KRL-7.1.5.3",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "end",
                                    "category": "end",
                                    "x": 650,
                                    "y": 400
                                }
                            ],
                            "links": [{
                                    "from": "start",
                                    "to": "transformer_stepdown"
                                },
                                {
                                    "from": "transformer_stepdown",
                                    "to": "rectifier"
                                },
                                {
                                    "from": "rectifier",
                                    "to": "non_fuse_breaker"
                                },
                                {
                                    "from": "non_fuse_breaker",
                                    "to": "end"
                                }
                            ]
                        },
                        {
                            "name": "Power Extension",
                            "time_interval": 31200,
                            "nodes": [{
                                    "key_value": "start",
                                    "category": "start",
                                    "x": 50,
                                    "y": 550
                                },
                                {
                                    "key_value": "power_extension",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Power Extension",
                                    "x": 200,
                                    "y": 550,
                                    "failure_rate": 2.000000E-10,
                                    "code": "R35-KRL-7.2.1",
                                    "quantity": 3
                                },
                                {
                                    "key_value": "hgs",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "HGS (High Voltage Ground Switch)",
                                    "x": 350,
                                    "y": 550,
                                    "failure_rate": 1.566809E-06,
                                    "code": "R35-KRL-7.2.2",
                                    "quantity": 3
                                },
                                {
                                    "key_value": "lgs",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "LGS (Low Voltage Ground Switch)",
                                    "x": 500,
                                    "y": 550,
                                    "failure_rate": 1.660192E-06,
                                    "code": "R35-KRL-7.2.3",
                                    "quantity": 3
                                },
                                {
                                    "key_value": "end",
                                    "category": "end",
                                    "x": 650,
                                    "y": 550
                                }
                            ],
                            "links": [{
                                    "from": "start",
                                    "to": "power_extension"
                                },
                                {
                                    "from": "power_extension",
                                    "to": "hgs"
                                },
                                {
                                    "from": "hgs",
                                    "to": "lgs"
                                },
                                {
                                    "from": "lgs",
                                    "to": "end"
                                }
                            ]
                        },
                        {
                            "name": "Battery System",
                            "time_interval": 31200,
                            "nodes": [{
                                    "key_value": "start",
                                    "category": "start",
                                    "x": 50,
                                    "y": 700
                                },
                                {
                                    "key_value": "battery_box",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Battery Box",
                                    "x": 200,
                                    "y": 700,
                                    "failure_rate": 2.236241E-10,
                                    "code": "R35-KRL-7.3.1",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "battery_cell",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Battery Cell",
                                    "x": 350,
                                    "y": 700,
                                    "failure_rate": 1.485948E-08,
                                    "code": "R35-KRL-7.3.2",
                                    "quantity": 72
                                },
                                {
                                    "key_value": "knife_switch",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Knife Switch",
                                    "x": 500,
                                    "y": 700,
                                    "failure_rate": 8.818342E-07,
                                    "code": "R35-KRL-7.3.3",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "end",
                                    "category": "end",
                                    "x": 650,
                                    "y": 700
                                }
                            ],
                            "links": [{
                                    "from": "start",
                                    "to": "battery_box"
                                },
                                {
                                    "from": "battery_box",
                                    "to": "battery_cell"
                                },
                                {
                                    "from": "battery_cell",
                                    "to": "knife_switch"
                                },
                                {
                                    "from": "knife_switch",
                                    "to": "end"
                                }
                            ]
                        },
                        {
                            "name": "Power Converter",
                            "time_interval": 31200,
                            "nodes": [{
                                    "key_value": "start",
                                    "category": "start",
                                    "x": 50,
                                    "y": 850
                                },
                                {
                                    "key_value": "trafo_stepdown",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Trafo Step Down",
                                    "x": 200,
                                    "y": 850,
                                    "failure_rate": 4.000000E-08,
                                    "code": "R35-KRL-7.4.1",
                                    "quantity": 24
                                },
                                {
                                    "key_value": "end",
                                    "category": "end",
                                    "x": 350,
                                    "y": 850
                                }
                            ],
                            "links": [{
                                    "from": "start",
                                    "to": "trafo_stepdown"
                                },
                                {
                                    "from": "trafo_stepdown",
                                    "to": "end"
                                }
                            ]
                        },
                        {
                            "name": "Air Conditioner Unit",
                            "time_interval": 31200,
                            "nodes": [{
                                    "key_value": "start",
                                    "category": "start",
                                    "x": 50,
                                    "y": 1000
                                },
                                {
                                    "key_value": "kompressor",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Kompressor",
                                    "x": 200,
                                    "y": 1000,
                                    "failure_rate": 1.323319E-04,
                                    "code": "R35-KRL-7.5.1",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "kondensor_fan",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Kondensor Fan",
                                    "x": 350,
                                    "y": 1000,
                                    "failure_rate": 1.111309E-05,
                                    "code": "R35-KRL-7.5.2",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "evaporator_fan",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Evaporator Fan",
                                    "x": 500,
                                    "y": 1000,
                                    "failure_rate": 4.228190E-07,
                                    "code": "R35-KRL-7.5.3",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "kondensor_coil",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Kondensor Coil",
                                    "x": 650,
                                    "y": 1000,
                                    "failure_rate": 3.100000E-08,
                                    "code": "R35-KRL-7.5.4",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "evaporator_coil",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Evaporator Coil",
                                    "x": 800,
                                    "y": 1000,
                                    "failure_rate": 3.000000E-08,
                                    "code": "R35-KRL-7.5.5",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "main_frame",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Main Frame and Cover",
                                    "x": 950,
                                    "y": 1000,
                                    "failure_rate": 4.200000E-10,
                                    "code": "R35-KRL-7.5.6",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "filter_dryer",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Filter Dryer",
                                    "x": 1100,
                                    "y": 1000,
                                    "failure_rate": 6.710000E-08,
                                    "code": "R35-KRL-7.5.7",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "tx_valves",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "TX Valves",
                                    "x": 1250,
                                    "y": 1000,
                                    "failure_rate": 9.500000E-09,
                                    "code": "R35-KRL-7.5.8",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "accumulator",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Accumulator",
                                    "x": 1400,
                                    "y": 1000,
                                    "failure_rate": 1.000000E-07,
                                    "code": "R35-KRL-7.5.9",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "ball_valve",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Ball Valve",
                                    "x": 1550,
                                    "y": 1000,
                                    "failure_rate": 1.740000E-07,
                                    "code": "R35-KRL-7.5.10",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "check_valve",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Check Valve",
                                    "x": 1700,
                                    "y": 1000,
                                    "failure_rate": 5.800000E-08,
                                    "code": "R35-KRL-7.5.11",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "strainer",
                                    "category": "component",
                                    "configuration": "series",
                                    "name": "Strainer",
                                    "x": 1850,
                                    "y": 1000,
                                    "failure_rate": 3.480000E-07,
                                    "code": "R35-KRL-7.5.12",
                                    "quantity": 2
                                },
                                {
                                    "key_value": "end",
                                    "category": "end",
                                    "x": 2000,
                                    "y": 1000
                                }
                            ],
                            "links": [{
                                    "from": "start",
                                    "to": "kompressor"
                                },
                                {
                                    "from": "kompressor",
                                    "to": "kondensor_fan"
                                },
                                {
                                    "from": "kondensor_fan",
                                    "to": "evaporator_fan"
                                },
                                {
                                    "from": "evaporator_fan",
                                    "to": "kondensor_coil"
                                },
                                {
                                    "from": "kondensor_coil",
                                    "to": "evaporator_coil"
                                },
                                {
                                    "from": "evaporator_coil",
                                    "to": "main_frame"
                                },
                                {
                                    "from": "main_frame",
                                    "to": "filter_dryer"
                                },
                                {
                                    "from": "filter_dryer",
                                    "to": "tx_valves"
                                },
                                {
                                    "from": "tx_valves",
                                    "to": "accumulator"
                                },
                                {
                                    "from": "accumulator",
                                    "to": "ball_valve"
                                },
                                {
                                    "from": "ball_valve",
                                    "to": "check_valve"
                                },
                                {
                                    "from": "check_valve",
                                    "to": "strainer"
                                },
                                {
                                    "from": "strainer",
                                    "to": "end"
                                }
                            ]
                        },
                        {
                            "name": "Air Conditioner Control",
                            "time_interval": 31200,
                            "nodes": [{
                                    "key_value": "start",
                                    "category": "start",
                                    "x": 50,
                                    "y": 1150
                                },
                                {
                                    "key_value": "hps",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "High Pressure Switch",
                                    "x": 200,
                                    "y": 1150,
                                    "failure_rate": 1.300000E-07,
                                    "code": "R35-KRL-7.6.1",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "lps",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "Low Pressure Switch",
                                    "x": 350,
                                    "y": 1150,
                                    "failure_rate": 1.400000E-07,
                                    "code": "R35-KRL-7.6.2",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "tpev1",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "TPEV1 (Thermal Protection for Evaporator Motor)",
                                    "x": 500,
                                    "y": 1150,
                                    "failure_rate": 1.500000E-07,
                                    "code": "R35-KRL-7.6.3",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "tpev2",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "TPEV2 (Thermal Protection for Evaporator Motor)",
                                    "x": 650,
                                    "y": 1150,
                                    "failure_rate": 1.600000E-07,
                                    "code": "R35-KRL-7.6.4",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "tpcf1",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "TPCF1 (Thermal Protection for Condenser Fan Motor)",
                                    "x": 800,
                                    "y": 1150,
                                    "failure_rate": 1.700000E-07,
                                    "code": "R35-KRL-7.6.5",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "tpcf2",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "TPCF2 (Thermal Protection for Condenser Fan Motor)",
                                    "x": 950,
                                    "y": 1150,
                                    "failure_rate": 1.800000E-07,
                                    "code": "R35-KRL-7.6.6",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "tpcp1",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "TPCP1 (Thermal Protection for Compressor)",
                                    "x": 1100,
                                    "y": 1150,
                                    "failure_rate": 1.900000E-07,
                                    "code": "R35-KRL-7.6.7",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "tpcp2",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "TPCP2 (Thermal Protection for Compressor)",
                                    "x": 1250,
                                    "y": 1150,
                                    "failure_rate": 2.000000E-07,
                                    "code": "R35-KRL-7.6.8",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "cpk1_c3",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "CPK1 (Compressor Contactor 1)– Contactor (C3)",
                                    "x": 1400,
                                    "y": 1150,
                                    "failure_rate": 2.100000E-07,
                                    "code": "R35-KRL-7.6.9",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "cpk2_c3",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "CPK2 (Compressor Contactor 2)– Contactor (C3)",
                                    "x": 1550,
                                    "y": 1150,
                                    "failure_rate": 2.200000E-07,
                                    "code": "R35-KRL-7.6.10",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "cpk1_e6",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "CPK1 (Compressor Contactor 1)– Coil (E6)",
                                    "x": 1700,
                                    "y": 1150,
                                    "failure_rate": 2.300000E-07,
                                    "code": "R35-KRL-7.6.11",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "cpk2_e7",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "CPK2 (Compressor Contactor 2)– Coil (E7)",
                                    "x": 1850,
                                    "y": 1150,
                                    "failure_rate": 2.400000E-07,
                                    "code": "R35-KRL-7.6.12",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "phcr_d10",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "PHCR (Phase Counter Over Voltage Relay 1) – Coil (D10)",
                                    "x": 2000,
                                    "y": 1150,
                                    "failure_rate": 2.500000E-07,
                                    "code": "R35-KRL-7.6.13",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "phcr_c3",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "PHCR (Phase Counter Over Voltage Relay 1) – Contact (C3)",
                                    "x": 2150,
                                    "y": 1150,
                                    "failure_rate": 2.600000E-07,
                                    "code": "R35-KRL-7.6.14",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "cpn1_d11",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "CPN1 (Compressor Motor NFB 1) – Coil (D11)",
                                    "x": 2300,
                                    "y": 1150,
                                    "failure_rate": 2.700000E-07,
                                    "code": "R35-KRL-7.6.15",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "cpn1_d4",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "CPN1 (Compressor Motor NFB 1)– Contact (D4)",
                                    "x": 2450,
                                    "y": 1150,
                                    "failure_rate": 2.800000E-07,
                                    "code": "R35-KRL-7.6.16",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "cpn2_d4",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "CPN2 (Compressor Motor NFB 2)– Contact (D4)",
                                    "x": 2600,
                                    "y": 1150,
                                    "failure_rate": 2.900000E-07,
                                    "code": "R35-KRL-7.6.17",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "hsvr1_e7",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "HSVR1 (Heater Solenoid Valve Relay)– Coil (E7)",
                                    "x": 2750,
                                    "y": 1150,
                                    "failure_rate": 3.000000E-07,
                                    "code": "R35-KRL-7.6.18",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "hsvr2_e7",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "HSVR2 (Heater Solenoid Valve Relay)– Coil (E7)",
                                    "x": 2900,
                                    "y": 1150,
                                    "failure_rate": 3.100000E-07,
                                    "code": "R35-KRL-7.6.19",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "hsvr1_contact_e7",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "HSVR1 (Heater Solenoid Valve Relay)– Contact (E7)",
                                    "x": 3050,
                                    "y": 1150,
                                    "failure_rate": 3.200000E-07,
                                    "code": "R35-KRL-7.6.20",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "hsvr2_contact_e7",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "HSVR2 (Heater Solenoid Valve Relay)– Contact (E7)",
                                    "x": 3200,
                                    "y": 1150,
                                    "failure_rate": 3.300000E-07,
                                    "code": "R35-KRL-7.6.21",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "hsvr1_f8",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "HSVR1 (Heater Solenoid Valve) (F8)",
                                    "x": 3350,
                                    "y": 1150,
                                    "failure_rate": 3.400000E-07,
                                    "code": "R35-KRL-7.6.22",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "hsvr2_f8",
                                    "category": "component",
                                    "configuration": "single",
                                    "name": "HSVR2 (Heater Solenoid Valve) (F8)",
                                    "x": 3500,
                                    "y": 1150,
                                    "failure_rate": 3.500000E-07,
                                    "code": "R35-KRL-7.6.23",
                                    "quantity": 1
                                },
                                {
                                    "key_value": "end",
                                    "category": "end",
                                    "x": 3650,
                                    "y": 1150
                                }
                            ],
                            "links": [{
                                    "from": "start",
                                    "to": "hps"
                                },
                                {
                                    "from": "hps",
                                    "to": "lps"
                                },
                                {
                                    "from": "lps",
                                    "to": "tpev1"
                                },
                                {
                                    "from": "tpev1",
                                    "to": "tpev2"
                                },
                                {
                                    "from": "tpev2",
                                    "to": "tpcf1"
                                },
                                {
                                    "from": "tpcf1",
                                    "to": "tpcf2"
                                },
                                {
                                    "from": "tpcf2",
                                    "to": "tpcp1"
                                },
                                {
                                    "from": "tpcp1",
                                    "to": "tpcp2"
                                },
                                {
                                    "from": "tpcp2",
                                    "to": "cpk1_c3"
                                },
                                {
                                    "from": "cpk1_c3",
                                    "to": "cpk2_c3"
                                },
                                {
                                    "from": "cpk2_c3",
                                    "to": "cpk1_e6"
                                },
                                {
                                    "from": "cpk1_e6",
                                    "to": "cpk2_e7"
                                },
                                {
                                    "from": "cpk2_e7",
                                    "to": "phcr_d10"
                                },
                                {
                                    "from": "phcr_d10",
                                    "to": "phcr_c3"
                                },
                                {
                                    "from": "phcr_c3",
                                    "to": "cpn1_d11"
                                },
                                {
                                    "from": "cpn1_d11",
                                    "to": "cpn1_d4"
                                },
                                {
                                    "from": "cpn1_d4",
                                    "to": "cpn2_d4"
                                },
                                {
                                    "from": "cpn2_d4",
                                    "to": "hsvr1_e7"
                                },
                                {
                                    "from": "hsvr1_e7",
                                    "to": "hsvr2_e7"
                                },
                                {
                                    "from": "hsvr2_e7",
                                    "to": "hsvr1_contact_e7"
                                },
                                {
                                    "from": "hsvr1_contact_e7",
                                    "to": "hsvr2_contact_e7"
                                },
                                {
                                    "from": "hsvr2_contact_e7",
                                    "to": "hsvr1_f8"
                                },
                                {
                                    "from": "hsvr1_f8",
                                    "to": "hsvr2_f8"
                                },
                                {
                                    "from": "hsvr2_f8",
                                    "to": "end"
                                }
                            ]
                        }
                    ]
                };
                jsonInput.value = JSON.stringify(example, null, 4);
                validateAndPreview();
            });

            // Clear
            document.getElementById('clearJson').addEventListener('click', () => {
                jsonInput.value = '';
                previewContainer.classList.add('d-none');
            });

            // Validate & Preview
            document.getElementById('validateJson').addEventListener('click', validateAndPreview);

            function validateAndPreview() {
                const raw = jsonInput.value.trim();
                if (!raw) {
                    Swal.fire('Empty', 'Please enter JSON.', 'warning');
                    previewContainer.classList.add('d-none');
                    return false;
                }

                try {
                    const parsed = JSON.parse(raw);
                    jsonPreview.textContent = JSON.stringify(parsed, null, 2);
                    previewContainer.classList.remove('d-none');
                    hljs.highlightElement(jsonPreview);
                    return parsed;
                } catch (e) {
                    Swal.fire('Invalid JSON', e.message, 'error');
                    previewContainer.classList.add('d-none');
                    return false;
                }
            }

            // SUBMIT VIA FETCH
            document.getElementById('submitJson').addEventListener('click', async function() {
                const parsed = validateAndPreview();
                if (!parsed) return;

                Swal.fire({
                    title: 'Creating...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const response = await fetch('{{ route('newrbd.jsoncreatemodel') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(parsed)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        Swal.fire('Success!', 'Model created!', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        const errorMsg = result.errors ?
                            Object.values(result.errors).flat().join('<br>') :
                            result.message;
                        Swal.fire('Failed', errorMsg, 'error');
                    }
                } catch (err) {
                    Swal.fire('Error', err.message, 'error');
                }
            });

            // DataTable
            $('#modelsTable').DataTable({
                pageLength: 10,
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    targets: 4,
                    orderable: false
                }]
            });

            hljs.highlightAll();
        });
    </script>
@endpush
