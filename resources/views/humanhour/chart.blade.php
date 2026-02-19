@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item">Monitoring Workload</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div id="download-decision-container" data-downloaddecision="{{ $download }}"></div>

    <div align="center">
        <div class="col-9">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <h3 class="card-title text-bold">Monitoring Jam Orang</h3>
                </div>

                <div class="card-body" id="content-wrapper">
                    <h2 class="mb-4">Workload Per Bulan</h2>

                    <!-- Dropdown Tahun -->
                    <div>
                        <label for="year-select">Pilih Tahun:</label>
                        <select id="year-select" class="form-control">
                            @for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Chart Container -->
                    <div id="workloadChart" style="width: 100%; height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function fetchChartData(year) {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching chart data, please wait.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/humanhour/hasil/chart?year=${year}`,
                    type: 'GET',
                    success: function (data) {
                        Swal.close();

                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        const workload = Array(12).fill(0);

                        data.forEach(item => {
                            const monthIndex = new Date(item.date).getMonth();
                            workload[monthIndex] = item.workload;
                        });

                        Highcharts.chart('workloadChart', {
                            chart: {
                                type: 'area'
                            },
                            title: {
                                text: 'Total Workload Per Bulan'
                            },
                            xAxis: {
                                categories: months,
                                title: {
                                    text: 'Bulan'
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'Jam Kerja'
                                },
                                min: 0
                            },
                            series: [{
                                name: 'Workload (Hours)',
                                data: workload,
                                color: 'rgba(54, 162, 235, 0.6)'
                            }],
                            credits: {
                                enabled: false
                            }
                        });
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to fetch chart data.', 'error');
                    }
                });



            }

            fetchChartData(document.getElementById("year-select").value);

            document.getElementById("year-select").addEventListener("change", function () {
                fetchChartData(this.value);
            });
        });
    </script>
@endpush

@push('css')
    <style>
        #workloadChart {
            width: 100%;
            height: 400px;
        }
    </style>
@endpush