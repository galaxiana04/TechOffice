<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>RBD Diagram - Multiple Junctions with Parallel (k-out-of-n)</title>
    <script src="https://unpkg.com/gojs/release/go.js"></script>
    <style>
        body {
            font-family: sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
        }

        #rbdDiagram {
            width: 100%;
            height: 650px;
            border: 1px solid #ccc;
            background: #ffffff;
        }

        #calcButton {
            margin-top: 10px;
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
    </style>
</head>

<body>
    <h1>RBD Diagram (Series + Multiple Parallel via Junctions)</h1>
    <div id="rbdDiagram"></div>
    <button id="calcButton">Calculate System Reliability</button>

    <script>
        const $ = go.GraphObject.make;

        try {
            const diagram = $(go.Diagram, "rbdDiagram", {
                "undoManager.isEnabled": true,
                allowMove: true
            });

            // Node template for components (rectangular)
            diagram.nodeTemplateMap.add("",
                $(go.Node, "Vertical", {
                        locationSpot: go.Spot.Center,
                        location: new go.Binding("location", "",
                            node => new go.Point(node.data.x, node.data.y)).makeTwoWay(
                            (point, node) => ({
                                x: point.x,
                                y: point.y
                            })
                        )
                    },
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
                                alignment: go.Spot.Top
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
                        new go.Binding("text", "source", source => source ? `Source: ${source}` : "")),
                    $(go.TextBlock, {
                            font: "10px monospace",
                            stroke: "#333",
                            margin: new go.Margin(2, 0, 0, 0)
                        },
                        new go.Binding("text", "reliability", r => r ? `R(t) = ${r}` : "")),
                    $(go.TextBlock, {
                            font: "10px monospace",
                            stroke: "#333",
                            margin: new go.Margin(2, 0, 0, 0)
                        },
                        new go.Binding("text", "", node => `X,Y = (${node.data.x}, ${node.data.y})`))
                )
            );

            // Node template for junctions, start, and end (square, blue, no text)
            diagram.nodeTemplateMap.add("junction",
                $(go.Node, "Spot", {
                        locationSpot: go.Spot.Center
                    },
                    $(go.Shape, "Rectangle", {
                        width: 20,
                        height: 20,
                        fill: "#dbeef4",
                        stroke: null
                    })
                )
            );

            // Reuse junction template for start and end nodes
            diagram.nodeTemplateMap.add("start",
                diagram.nodeTemplateMap.get("junction")
            );

            diagram.nodeTemplateMap.add("end",
                diagram.nodeTemplateMap.get("junction")
            );

            // Link template
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

            // Data node & link
            const data = {
                "class": "go.GraphLinksModel",
                "nodeDataArray": [{
                        "key": "start",
                        "category": "start",
                        "x": 0,
                        "y": 150,
                        "reliability": 1
                    },
                    {
                        "key": 1,
                        "code": "R35-KRL-5.1.1",
                        "name": "Pantograph Body",
                        "x": 100,
                        "y": 150,
                        "fr": "1.92E-07",
                        "source": "Manufacturer Data",
                        "reliability": (Math.exp(-1.92e-7 * 9984)).toFixed(6)
                    },
                    {
                        "key": 2,
                        "code": "R35-KRL-5.1.2",
                        "name": "Insulator",
                        "x": 300,
                        "y": 150,
                        "fr": "2.05E-07",
                        "source": "Manufacturer Data",
                        "reliability": (Math.exp(-2.05e-7 * 9984)).toFixed(6)
                    },
                    // First parallel branch
                    {
                        "key": "J_in_1",
                        "category": "junction",
                        "x": 400,
                        "y": 150,
                        "k": 2,
                        "n": 3,
                        "groupId": "parallel_1"
                    },
                    {
                        "key": 3,
                        "code": "R35-KRL-5.1.3",
                        "name": "Collector Head",
                        "x": 500,
                        "y": 80,
                        "fr": "1.55E-07",
                        "source": "Manufacturer Data",
                        "reliability": (Math.exp(-1.55e-7 * 9984)).toFixed(6)
                    },
                    {
                        "key": 4,
                        "code": "R35-KRL-5.1.4",
                        "name": "Parallel Component A",
                        "x": 500,
                        "y": 150,
                        "fr": "1.80E-07",
                        "source": "Manufacturer Data",
                        "reliability": (Math.exp(-1.80e-7 * 9984)).toFixed(6)
                    },
                    {
                        "key": 5,
                        "code": "R35-KRL-5.1.5",
                        "name": "Parallel Component B",
                        "x": 500,
                        "y": 220,
                        "fr": "1.75E-07",
                        "source": "Manufacturer Data",
                        "reliability": (Math.exp(-1.75e-7 * 9984)).toFixed(6)
                    },
                    {
                        "key": "J_out_1",
                        "category": "junction",
                        "x": 600,
                        "y": 150,
                        "k": 1,
                        "n": 3,
                        "groupId": "parallel_1"
                    },
                    // Second parallel branch
                    {
                        "key": "J_in_2",
                        "category": "junction",
                        "x": 700,
                        "y": 150,
                        "k": 1,
                        "n": 2,
                        "groupId": "parallel_2"
                    },
                    {
                        "key": 7,
                        "code": "R35-KRL-5.1.7",
                        "name": "Parallel Component C",
                        "x": 800,
                        "y": 100,
                        "fr": "1.60E-07",
                        "source": "Manufacturer Data",
                        "reliability": (Math.exp(-1.60e-7 * 9984)).toFixed(6)
                    },
                    {
                        "key": 8,
                        "code": "R35-KRL-5.1.8",
                        "name": "Parallel Component D",
                        "x": 800,
                        "y": 200,
                        "fr": "1.70E-07",
                        "source": "Manufacturer Data",
                        "reliability": (Math.exp(-1.70e-7 * 9984)).toFixed(6)
                    },
                    {
                        "key": "J_out_2",
                        "category": "junction",
                        "x": 900,
                        "y": 150,
                        "k": 1,
                        "n": 2,
                        "groupId": "parallel_2"
                    },
                    {
                        "key": 6,
                        "code": "R35-KRL-5.1.6",
                        "name": "Output Component",
                        "x": 1000,
                        "y": 150,
                        "fr": "1.00E-07",
                        "source": "Manufacturer Data",
                        "reliability": (Math.exp(-1.00e-7 * 9984)).toFixed(6)
                    },
                    {
                        "key": "end",
                        "category": "end",
                        "x": 1100,
                        "y": 150,
                        "reliability": 1
                    }
                ],
                "linkDataArray": [{
                        "from": "start",
                        "to": 1
                    },
                    {
                        "from": 1,
                        "to": 2
                    },
                    {
                        "from": 2,
                        "to": "J_in_1"
                    },
                    // First parallel branch
                    {
                        "from": "J_in_1",
                        "to": 3
                    },
                    {
                        "from": "J_in_1",
                        "to": 4
                    },
                    {
                        "from": "J_in_1",
                        "to": 5
                    },
                    {
                        "from": 3,
                        "to": "J_out_1"
                    },
                    {
                        "from": 4,
                        "to": "J_out_1"
                    },
                    {
                        "from": 5,
                        "to": "J_out_1"
                    },
                    {
                        "from": "J_out_1",
                        "to": "J_in_2"
                    },
                    // Second parallel branch
                    {
                        "from": "J_in_2",
                        "to": 7
                    },
                    {
                        "from": "J_in_2",
                        "to": 8
                    },
                    {
                        "from": 7,
                        "to": "J_out_2"
                    },
                    {
                        "from": 8,
                        "to": "J_out_2"
                    },
                    {
                        "from": "J_out_2",
                        "to": 6
                    },
                    {
                        "from": 6,
                        "to": "end"
                    }
                ]
            };

            console.log("Loading diagram model...");
            diagram.model = go.Model.fromJson(data);
            console.log("Diagram model loaded successfully.");

            // Function to calculate binomial coefficient
            function binomialCoefficient(n, k) {
                if (k < 0 || k > n) return 0;
                if (k === 0 || k === n) return 1;
                let result = 1;
                for (let i = 1; i <= k; i++) {
                    result *= (n - i + 1) / i;
                }
                return result;
            }

            // Calculate reliability
            function calculateReliability() {
                console.log("Starting reliability calculation...");
                const t = 9984; // hours
                const nodes = data.nodeDataArray;

                // Calculate individual reliabilities
                const reliabilities = {};
                nodes.forEach(node => {
                    if (node.category === "junction") {
                        reliabilities[node.key] = null;
                        console.log(`Node ${node.key}: Junction, reliability set to null`);
                    } else {
                        const lambda = parseFloat(node.fr) || 0;
                        reliabilities[node.key] = Math.exp(-lambda * t);
                        console.log(`Node ${node.key}: Reliability = ${reliabilities[node.key].toFixed(6)}`);
                    }
                });

                // Map children for each node
                const childrenMap = {};
                data.linkDataArray.forEach(link => {
                    if (!childrenMap[link.from]) childrenMap[link.from] = [];
                    childrenMap[link.from].push(link.to);
                    console.log(`Link: ${link.from} -> ${link.to}`);
                });

                // Group junctions by groupId
                const junctionGroups = {};
                nodes.forEach(node => {
                    if (node.category === "junction" && node.groupId) {
                        if (!junctionGroups[node.groupId]) junctionGroups[node.groupId] = [];
                        junctionGroups[node.groupId].push(node.key);
                        console.log(`Junction ${node.key} added to group ${node.groupId}`);
                    }
                });

                // Validate junction groups
                Object.keys(junctionGroups).forEach(groupId => {
                    if (junctionGroups[groupId].length !== 2) {
                        console.warn(`Warning: Group ${groupId} has ${junctionGroups[groupId].length} junctions, expected 2`);
                    }
                });

                // Recursive reliability calculation
                function getReliability(key) {
                    if (reliabilities[key] !== null && reliabilities[key] !== undefined) {
                        console.log(`Node ${key}: Using cached reliability ${reliabilities[key].toFixed(6)}`);
                        return reliabilities[key];
                    }
                    const node = nodes.find(n => n.key === key);
                    const children = childrenMap[key] || [];
                    console.log(`Node ${key}: Calculating reliability for children ${children}`);
                    const childRs = children.map(childKey => {
                        const r = getReliability(childKey);
                        console.log(`Child ${childKey}: Reliability = ${r.toFixed(6)}`);
                        return r;
                    });

                    if (node.category === "junction") {
                        let k = node.k || 1;
                        let n = node.n || children.length;
                        console.log(`Node ${key}: Junction with k=${k}, n=${n}, children=${children.length}`);
                        let R_parallel = 0;

                        if (k <= n && n === children.length) {
                            for (let i = k; i <= n; i++) {
                                let term = binomialCoefficient(n, i);
                                childRs.forEach((r, idx) => {
                                    const r_i = Math.pow(r, i);
                                    const r_n_i = Math.pow(1 - r, n - i);
                                    term *= (i === n ? 1 : r_i * r_n_i);
                                });
                                R_parallel += term;
                                console.log(`Node ${key}: Term for i=${i} = ${term.toFixed(6)}, R_parallel = ${R_parallel.toFixed(6)}`);
                            }
                        } else {
                            R_parallel = 1 - childRs.reduce((prod, r) => prod * (1 - r), 1);
                            console.log(`Node ${key}: Invalid k/n, using default 1-out-of-n, R_parallel = ${R_parallel.toFixed(6)}`);
                        }
                        reliabilities[key] = R_parallel;
                        return R_parallel;
                    } else {
                        const R_series = childRs.reduce((prod, r) => prod * r, 1);
                        console.log(`Node ${key}: Series reliability = ${R_series.toFixed(6)}`);
                        return R_series;
                    }
                }

                const R_system = getReliability("start");
                console.log(`System Reliability at t = ${t} hours: ${R_system.toFixed(6)}`);
                alert(`System Reliability at t = ${t} hours: ${R_system.toFixed(6)}`);
            }

            document.getElementById("calcButton").addEventListener("click", calculateReliability);
        } catch (error) {
            console.error("Error initializing diagram or calculating reliability:", error);
            alert("An error occurred while loading the diagram or calculating reliability. Please check the console for details.");
        }
    </script>
</body>

</html>