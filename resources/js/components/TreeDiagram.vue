<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Tree Diagram</title>
        <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
        <script src="https://d3js.org/d3.v7.min.js"></script>
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
    </head>

    <body>
        <div id="app">
            <tree-diagram></tree-diagram>
        </div>

        <script>
            const { createApp, ref, onMounted } = Vue;

            createApp({
                components: {
                    "tree-diagram": {
                        template: `
                        <div>
                            <select v-model="selectedProject" @change="fetchTacks">
                                <option v-for="project in projects" :value="project.id">
                                    {{ project . title }}
                                </option>
                            </select>
                            <div id="tree-container"></div>
                        </div>
                    `,
                        setup() {
                            const projects = ref([]);
                            const selectedProject = ref(null);
                            const data = ref([]);

                            onMounted(async () => {
                                try {
                                    const response = await fetch("/projects");
                                    projects.value = await response.json();
                                } catch (error) {
                                    console.error(
                                        "Gagal mengambil daftar proyek:",
                                        error
                                    );
                                }
                            });

                            async function fetchTacks() {
                                if (!selectedProject.value) return;
                                try {
                                    const response = await fetch(
                                        `/tacks/${selectedProject.value}`
                                    );
                                    data.value = await response.json();
                                    console.log("Data dari API:", data.value);
                                    processData(data.value);
                                } catch (error) {
                                    console.error(
                                        "Gagal mengambil data tasks:",
                                        error
                                    );
                                }
                            }

                            function processData(rawData) {
                                if (!rawData || rawData.length === 0) {
                                    d3.select("#tree-container").html(
                                        "<p>No data available</p>"
                                    );
                                    return;
                                }

                                const hierarchyData = d3.hierarchy({
                                    name: "Special Process",
                                    children: rawData.map((tack) => ({
                                        name: tack.name || "No Name",
                                        project:
                                            tack.project_type?.title ||
                                            "Unknown Project",
                                        children:
                                            tack.subtacks?.map((subtack) => ({
                                                name:
                                                    subtack.name ||
                                                    "No Subtask",
                                                children:
                                                    subtack.subtack_members?.map(
                                                        (member) => ({
                                                            name:
                                                                member.name ||
                                                                "No Member",
                                                        })
                                                    ) || [],
                                            })) || [],
                                    })),
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
                                d3.select("#tree-container").html(""); // Clear SVG before redraw
                                const width = 1400,
                                    height = 800;
                                const treeLayout = d3
                                    .tree()
                                    .size([height - 100, width - 300]);
                                const root = treeLayout(rootData);

                                const svg = d3
                                    .select("#tree-container")
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

                                    svg.selectAll(".link")
                                        .data(links)
                                        .enter()
                                        .append("path")
                                        .attr("class", "link")
                                        .attr(
                                            "d",
                                            d3
                                                .linkHorizontal()
                                                .x((d) => d.y)
                                                .y((d) => d.x)
                                        );

                                    const node = svg
                                        .selectAll(".node")
                                        .data(nodes)
                                        .enter()
                                        .append("g")
                                        .attr("class", "node")
                                        .attr(
                                            "transform",
                                            (d) => `translate(${d.y},${d.x})`
                                        )
                                        .on("click", (event, d) => {
                                            toggle(d);
                                            update(d);
                                        });

                                    node.append("rect")
                                        .attr(
                                            "width",
                                            (d) => d.data.name.length * 7 + 20
                                        )
                                        .attr("height", 20)
                                        .attr(
                                            "x",
                                            (d) =>
                                                -(
                                                    (d.data.name.length * 7 +
                                                        20) /
                                                    2
                                                )
                                        )
                                        .attr("y", -10)
                                        .attr("fill", (d) =>
                                            d._children
                                                ? "lightsteelblue"
                                                : "#fff"
                                        );

                                    node.append("text")
                                        .attr("dy", ".35em")
                                        .text((d) => d.data.name);

                                    node.filter((d) => d.depth === 1)
                                        .append("text")
                                        .attr("class", "project-text")
                                        .attr("dy", "1.5em")
                                        .text((d) => `(${d.data.project})`);
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

                            return { projects, selectedProject, fetchTacks };
                        },
                    },
                },
            }).mount("#app");
        </script>
    </body>
</html>
