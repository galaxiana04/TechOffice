@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="/">TACK</a></li>
                        <li class="breadcrumb-item active text-bold">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="d-flex justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-gradient-primary text-white text-center">
                    <h3 class="mb-0">TACK System</h3>
                </div>
                <div class="card-body text-center">
                    <div class="form-group mb-4">
                        <label for="revisiSelect" class="font-weight-bold">Pilih Project :</label>
                        <select class="form-control w-50 mx-auto border-primary shadow-sm" id="revisiSelect">
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('tack.upload') }}" class="btn btn-primary btn-sm">
                            Upload TACK
                        </a>
                    </div>
                    <div id="app">
                        <tree-diagram></tree-diagram>
                    </div>
                    <div id="tree-container" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script src="https://d3js.org/d3.v7.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const {
                createApp,
                ref,
                onMounted
            } = Vue;

            createApp({
                setup() {
                    const selectedProjectId = ref(null);
                    const data = ref(null);

                    async function fetchData() {
                        selectedProjectId.value = document.getElementById("revisiSelect").value;
                        if (!selectedProjectId.value) return;

                        // Tampilkan loading SweetAlert
                        Swal.fire({
                            title: "Mengambil data...",
                            text: "Mohon tunggu sebentar.",
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        try {
                            const response = await fetch(
                                `/tack/getprojectdata/${selectedProjectId.value}`);
                            if (!response.ok) throw new Error("Gagal mengambil data");

                            const json = await response.json();
                            data.value = json;
                            processData(data.value);

                            Swal.close(); // Tutup SweetAlert setelah sukses
                        } catch (error) {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Terjadi kesalahan saat mengambil data!"
                            });
                            console.error("Fetch error:", error);
                        }
                    }

                    function processData(data) {
                        const hierarchyData = d3.hierarchy({
                            name: "Project " + selectedProjectId.value,
                            children: data.map(tack => ({
                                name: "TACK " + String(tack
                                    .number), // Konversi integer ke string
                                process: tack.tack_phase.name ?? "Process",
                                children: (tack.subtacks ?? []).map(subtack => ({
                                    name: "TACK " + String(tack.number) +
                                        "." + String(subtack
                                            .number
                                        ), // Konversi integer ke string sebelum digabung
                                    documentnumber: subtack.documentnumber,
                                    children: (subtack.subtack_members ??
                                    []).map(member => ({
                                        name: member.name,

                                        children: (member
                                            .newprogressreports ??
                                            []).map(
                                            newprogressreports =>
                                            ({
                                                name: newprogressreports
                                                    .nodokumen
                                            }))
                                    }))
                                }))
                            }))


                        });

                        hierarchyData.children.forEach(collapse);
                        drawTree(hierarchyData);
                    }

                    function collapse(node) {
                        if (node.children) {
                            node._children = node.children;
                            node.children = null;
                            node._children.forEach(collapse);
                        }
                    }

                    function drawTree(rootData) {
                        const width = 1400,
                            height = 800;
                        const treeLayout = d3.tree().size([height - 100, width - 300]);
                        const root = treeLayout(rootData);

                        d3.select("#tree-container").selectAll("*").remove();

                        const svg = d3.select("#tree-container")
                            .append("svg")
                            .attr("width", width)
                            .attr("height", height)
                            .append("g")
                            .attr("transform", "translate(150,50)");

                        update(root);

                        function update(source) {
                            treeLayout(rootData);

                            const nodes = rootData.descendants();
                            const links = rootData.links();

                            svg.selectAll(".link").remove();
                            svg.selectAll(".node").remove();

                            const link = svg.selectAll(".link")
                                .data(links)
                                .enter().append("path")
                                .attr("class", "link")
                                .attr("d", d3.linkHorizontal()
                                    .x(d => d.y)
                                    .y(d => d.x));

                            const node = svg.selectAll(".node")
                                .data(nodes)
                                .enter().append("g")
                                .attr("class", "node")
                                .attr("transform", d => `translate(${d.y}, ${d.x})`)
                                .on("click", (event, d) => {
                                    toggle(d);
                                    update(d);
                                });

                            node.append("rect")
                                .attr("width", d => d.data.name.length * 7 + 20)
                                .attr("height", 20)
                                .attr("x", d => -((d.data.name.length * 7 + 20) / 2))
                                .attr("y", -10)
                                .attr("fill", d => d._children ? "lightsteelblue" : "#fff");

                            node.append("text")
                                .attr("dy",
                                    "0.4em"
                                ) // Menggeser sedikit ke atas agar tidak bertabrakan dengan documentnumber
                                .attr("class", "node-title")
                                .text(d => d.data.name);

                            node.append("text")
                                .attr("dy", "2.5em") // Atur ulang posisi
                                .attr("x", 0)
                                .attr("class", "node-document")
                                .text(d => d.data.documentnumber ? d.data.documentnumber : "")
                                .style("font-size", "10px")
                                .style("fill", "gray");


                            node.filter(d => d.depth === 1)
                                .append("text")
                                .attr("class", "project-text")
                                .attr("dy", "2.5em")
                                .text(d => `${d.data.process}`);
                        }

                        function toggle(d) {
                            if (d.children) {
                                d._children = d.children;
                                d.children = null;
                            } else {
                                d.children = d._children;
                                d._children = null;
                            }
                        }
                    }

                    onMounted(() => {
                        fetchData();
                        document.getElementById("revisiSelect").addEventListener("change",
                            fetchData);
                    });

                    return {};
                }
            }).mount("#app");
        });
    </script>
@endsection

@push('css')
    <style>
        .node rect {
            fill: #fff;
            stroke: steelblue;
            stroke-width: 2px;
            rx: 5;
            ry: 5;
            cursor: pointer;
        }

        .node text {
            font-size: 12px;
            text-anchor: middle;
            cursor: pointer;
        }

        .node .project-text {
            font-size: 10px;
            fill: gray;
        }

        .link {
            fill: none;
            stroke: #ccc;
            stroke-width: 2px;
        }
    </style>
@endpush
