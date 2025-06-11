<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Graphes</title>
</head>
<body>
    <div id="chart-container">
        <canvas id="chart-canvas"></canvas>
        <button onclick="graph.resetZoom()">reset view</button>
    </div>

    <!-- Chart.js and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@^2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@^1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script type="text/javascript">

    // --- Configuration ---
    page = new URL (window.location);
    let paramRefreshTime = page.searchParams.get("rTime");
    let paramMaxDataDisplay = page.searchParams.get("maxPoints");
    let paramPointRadius = page.searchParams.get("pointRadius");
    let paramColors = page.searchParams.get("colors");
    const paramFill = false;

    // Change later if needed
    paramRefreshTime = 1000;
    paramMaxDataDisplay = 10;
    paramPointRadius = 2;


    const ctx = document.getElementById("chart-canvas");
    let allowPanFalse = 1;
    let data1 = [];
    let data2 = [];
    let dataTemps1 = [];
    let dataTemps2 = [];
    let time = [];
    let count = 0;
    let graph = null;
    let datasets;
    let colors = ["#EDC240", "#AFD8F8", "#CB4B4B", "#F47B09", "#7736F9", "#FCB40C", "#96989B", "#42B983", "#FF69B4", "#32CD32", "#0F6E84", "#F92A53", "#4BC0C0"];

    if (paramColors != null) {
        paramColors = paramColors.split(",");
        for (let i = 0; i < paramColors.length; i++) {
            if (i < colors.length-1) {
                colors[i] = paramColors[i]
            } else {
                colors.push(paramColors[i]);
            };
        };
    };

    const graphOptions = {
        animation: false,
        responsive: true,
        maintainAspectRatio: false,
        tooltip: {
            mode: "index",
            intersect: false,
        },
        hover: {
            mode: "index",
            intersect: false,
        },
        scales: {
            x: {
                ticks: {
                    maxRotation: 0,
                    minRotation: 0,
                    autoSkipPadding: 20,
                },
                type: "time",
                time: {
                    displayFormats: {
                        millisecond: "HH:mm:ss.S",
                        second: "HH:mm:ss",
                        minute: "HH:mm",
                        hour: "D MMM, HH:mm",
                        day: "D MMM, HH:mm",
                        week: "MMM D",
                        month: "MMM YYYY",
                        quarter: "MMM YYYY",
                        year: "MMM YYYY",
                    },
                    tooltipFormat: "ddd, D MMM YYYY, HH:mm:ss Z",
                },
            },
            y: {
                type: 'linear',
                position: 'left'
            }
        },
        plugins: {
            tooltip: {
                mode: "index",
                intersect: false,
            },
            zoom: {
                pan: {
                    enabled: true,
                    mode: 'xy'
                },
                limits: {
                    x: {
                        min: "original",
                        max: "original",
                    },
                    y: {
                        min: "original",
                        max: "original",
                    },
                    y1: {
                        min: 0,
                        max: 100,
                    },
                },
                zoom: {
                    wheel: { enabled: true },
                    pinch: { enabled: true },
                    mode: 'x'
                }
            }
        }
    };

    const formatOptions = {
        fill: paramFill,
        pointRadius: paramPointRadius,
        order: 0
    };

    const config = {
        graphOptions: graphOptions,
        formatOptions: formatOptions,
    };

    function createDataset(dataset, rawJson, index, yAxisID, data, formatOptions) {
            colorIndex = index;
            while (colorIndex >= colors.length) {
                colorIndex -= colors.length;
            }
            lbl = "";
            colorBorder = colors[colorIndex];
            let a = Math.floor((0.3 + 0.05 * colorIndex)*255);
            colorFill = colors[colorIndex] + a.toString(16);
            dataset.push({
                label: lbl,
                yAxisID: yAxisID,
                data: data,
                fill: formatOptions.fill,
                pointRadius: formatOptions.pointRadius,
                order: formatOptions.order,
                backgroundColor: colorFill,
                borderColor: colorBorder,
                pointBackgroundColor: colorFill,
                pointBorderColor: colorBorder,
            });
        };

    function handleFormat_generic(rawJson) {
        for (let i = 0; i < rawJson["series"].length; i ++) {
            var len = data1.length;
            if (len < rawJson["series"].length) {
                data1.push([]);
            };
            data1[i].push(rawJson["data"]["0"][i]);
        };
        if (graph === null) {
            // graphOptions = config.graphOptions;
            datasets = [];
            // formatOptions = config.formatOptions;
            if (paramPointRadius != null) {
                formatOptions.pointRadius = paramPointRadius;
            };
            if (paramFill != null) {
                formatOptions.fill = paramFill;
            };
            for (let i = 0; i < rawJson["series"].length; i ++) {
                if (paramFill != null) {
                    formatOptions.order = rawJson["series"].length - i;
                    createDataset(datasets, rawJson, i, "y", data1[i], formatOptions);
                } else {
                    createDataset(datasets, rawJson, i, "y", data1[i], formatOptions);
                }
            };
            createGraph();
        };
    };

    function resizeGraph(i = 1) {
        let dHeight = ((Math.max(document.documentElement.scrollHeight, document.body.scrollHeight))*0.9).toString()+"px";
        document.getElementById("chart-container").style.height = dHeight;
        graph.resize();
        if (((Math.max(document.documentElement.scrollHeight, document.body.scrollHeight)) > (Math.max(document.documentElement.clientHeight, document.body.clientHeight))) && (i < 10)) {
            resizeGraph(i+1)
        };
    };

    // --- Simulated DB Query ---
    function fetchDataFromDatabase() {
        const now = Math.floor(Date.now() / 1000);
        return {
            format: "generic",
            ts: now,
            series: ["Temperature", "Humidity"],
            unit: ["°C", "%"],
            data: [[Math.random() * 30 + 10, Math.random() * 40 + 30]]
        };
    }

    // --- Main Update Loop ---
    function update() {
        const rawJson = fetchDataFromDatabase();
        count+=1;
        currentTime = new Date(parseFloat(rawJson["ts"])*1000);
        time.push(currentTime);
        handleFormat_generic(rawJson);
        if (count > paramMaxDataDisplay && allowPanFalse) {
            allowPanFalse = 0;
            graph.options.plugins.zoom.pan.onPanStart = () => {
                graph.config._config.options.scales.x.min = time[0];
                graph.config._config.options.plugins.zoom.limits.x.min = time[0];
            };
            graph.options.plugins.zoom.pan.onPan = () => {
                let vMin = Infinity;
                let vMax = 0;
                indexStart = time.indexOf(time.find(d => Math.floor(d.getTime()/1000) == Math.floor(graph.config._config.options.scales.x.min/1000)));
                indexEnd = time.indexOf(time.find(d => Math.floor(d.getTime()/1000) == Math.floor(graph.config._config.options.scales.x.max/1000)));
                for (let i = 0; i < data1.length; i++) {
                    if (data2.length > 0) {
                        vMin = Math.min(vMin, Math.min.apply(null, data1[i].slice(indexStart, indexEnd)), Math.min.apply(null, data2[i].slice(indexStart, indexEnd)));
                        vMax = Math.max(vMax, Math.max.apply(null, data1[i].slice(indexStart, indexEnd)), Math.max.apply(null, data2[i].slice(indexStart, indexEnd)));
                    } else {
                        vMin = Math.min(vMin, Math.min.apply(null, data1[i].slice(indexStart, indexEnd)));
                        vMax = Math.max(vMax, Math.max.apply(null, data1[i].slice(indexStart, indexEnd)));
                    };
                };
                console.debug("min: ",Math.floor(vMin*10)/10, "max:      ",Math.ceil(vMax*10)/10);
                if (Math.abs(vMin) == Infinity){
                    return;
                }
                graph.zoomScale("y", {min: Math.floor(vMin*10)/10, max: Math.ceil(vMax*10)/10}, "none");
            };
        };
        if ((graph.isZoomedOrPanned() == false)) {
            graph.resetZoom();
            graph.config._config.options.scales.x.min = new Date(currentTime-paramMaxDataDisplay*paramRefreshTime);
            graph.config._config.options.plugins.zoom.limits.x.min = new Date(currentTime-paramMaxDataDisplay*paramRefreshTime);
            graph.config._config.options.scales.x.max = new Date(currentTime);
        };
        graph.update();
    };

    function createGraph() {
        graph = new Chart(ctx, {
            type: "line",
            data: {
                labels: time,
                datasets: datasets,
            },
            options: graphOptions,
        });
        resizeGraph();
    };

    document.addEventListener("DOMContentLoaded", () => {
        console.debug("GraphViewTest loaded");
        update();
        setInterval(update, paramRefreshTime);
        window.addEventListener("resize", () => resizeGraph(1));
    });
    </script>
</body>
</html>
