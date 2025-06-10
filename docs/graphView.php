<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Graph</title>
    </head>

    <div id="chart-container">
        <canvas id="chart-canvas"></canvas>
        <button onclick="graph.resetZoom()">reset view</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@^2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@^1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script type="text/javascript">
        // -- Parameters -- \\
        page = new URL (window.location);
        let paramURL = page.searchParams.get("src");
        let paramRefreshTime = page.searchParams.get("rTime");
        let paramMaxDataDisplay = page.searchParams.get("maxPoints");
        let paramFormat = page.searchParams.get("format");
        let paramFill = page.searchParams.get("fill");
        let paramPointRadius = page.searchParams.get("pointRadius");
        let paramColors = page.searchParams.get("colors");

        if (paramFill === "1" || paramFill === "true") {
            paramFill = true;
        } else if (paramFill === "0" || paramFill === "false") {
            paramFill = false;
        } else {
            paramFill = null;
        };

        // -- Variables -- \\
        const ctx = document.getElementById("chart-canvas");
        let allowPanFalse = 1;
        let data1 = [];
        let data2 = [];
        let dataTemp1 = [];
        let dataTemp2 = [];
        let time = [];
        let count = 0;
        let graph = null;
        let graphOptions, datasets, formatOptions;
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
        const config = {
            graphOptions: {
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
                },
                plugins: {
                    tooltip: {
                        mode: "index",
                        intersect: false,
                    },
                    zoom: {
                        pan: {
                            enabled: true,
                            mode: "xy",
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
                            wheel: {
                                enabled: true,
                            },
                            pinch: {
                                enabled: true,
                            },
                            mode: "x",
                        },
                    },
                },
            },
            formatOptions: {
                order: 0,
                fill: false,
                pointRadius: 3
            }
        };


        // -- Functions -- \\
        function handleFormat_ping(rawJson) {
            var len = data1.length;
            if (len < rawJson["series"].length) {
                data1.push([]);
                data2.push([]);
            };
            data1[0].push(rawJson["data"]["0"]["0"]);
            data2[0].push(rawJson["data"]["0"]["1"]);
            if (graph === null) {
                colors[1] = "#CB4B4B";
                colors[0] = "#00FF0";
                graphOptions = config.graphOptions;
                graphOptions.scales.y = {
                    type: "linear",
                    display: true,
                    position: "left",
                    ticks: {
                        callback: function(value) {
                            value = Math.floor(1000*value)/1000;
                            if (value*1000 <= 1) {
                                if (value*1000000 <= 1 ) {
                                    return(value*1000000+"µs");
                                };
                                return(value*1000+"ms");
                            };
                            return(value+"s");
                        },
                    },
                },
                graphOptions.scales.y1 = {
                    type: "linear",
                    display: true,
                    position: "right",
                    min: 0,
                    max: 100,
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return(value+"%")
                        }
                    }
                };
                datasets = [];
                formatOptions = config.formatOptions;
                if (paramPointRadius != null) {
                    formatOptions.pointRadius = paramPointRadius;
                };
                if (paramFill != null) {
                    formatOptions.fill = paramFill;
                    createDataset(datasets, rawJson, 0, "y", data1[0], formatOptions);
                } else {
                    createDataset(datasets, rawJson, 0, "y", data1[0], formatOptions);
                    formatOptions.fill = true;
                };
                createDataset(datasets, rawJson, 1, "y1", data2[0], formatOptions);
                createGraph();
            };
        };

        function handleFormat_iface(rawJson) {
            for (let i = 0; i < rawJson["series"].length / 2; i ++) {
                var len = data1.length;
                if (len < rawJson["series"].length / 2) {
                    dataTemp1.push(0);
                    dataTemp2.push(0);
                    data1.push([]);
                    data2.push([]);
                };
                len = data1[i].length;
                if (len > 1) {
                    data1[i].push((rawJson["data"]["0"][i*2] - dataTemp1[i])*1000/paramRefreshTime);
                    data2[i].push((-1*rawJson["data"]["0"][i*2+1] - dataTemp2[i])*1000/paramRefreshTime);
                } else {
                    data1[i].push(0);
                    data2[i].push(0);
                };
                dataTemp1[i] = rawJson["data"]["0"][i*2];
                dataTemp2[i] = -1*rawJson["data"]["0"][i*2+1];
            };
            if (graph === null) {
                graphOptions = config.graphOptions;
                graphOptions.scales.y = {
                    grid: {
                        color: (context) => {
                            const zeroLine = context.tick.value;
                            const gridColor = zeroLine === 0 ? "#black" : "#ccc";
                            return gridColor;
                        },
                        lineWidth: (context) => {
                            const zeroLine = context.tick.value;
                            const lwidth = zeroLine === 0 ? 3 : 0.5;
                            return lwidth;
                        },
                    },
                    ticks: {
                        callback: function (value) {
                            value = Math.abs(value);
                            if (value/1000 >= 1) {
                                if (value/1000000 >= 1 ) {
                                    if (value/1000000000 >= 1) {
                                        return(value/1000000000+" Gb");
                                    };
                                    return(value/1000000+" Mb");
                                };
                                return(value/1000+" Kb");
                            };
                            return(value+" b");
                        },
                    },
                };
                datasets = [];
                formatOptions = config.formatOptions;
                formatOptions.fill = true
                if (paramPointRadius != null) {
                    formatOptions.pointRadius = paramPointRadius;
                };
                if (paramFill != null) {
                    formatOptions.fill = paramFill;
                };
                for (let i = 0; i < rawJson["series"].length / 2; i ++) {
                    createIfaceDataset(datasets, rawJson, i, formatOptions);
                };
                createGraph();
            };
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
                graphOptions = config.graphOptions;
                datasets = [];
                formatOptions = config.formatOptions;
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

        async function update() {
            let rawJson = await (await fetch(paramURL)).json();
            count += 1;
            if (paramFormat === null) {
                var effectiveFormat = rawJson["format"]
                if (effectiveFormat === undefined) {
                    effectiveFormat = (new URL(paramURL)).searchParams.get("type");
                };
            } else {
                var effectiveFormat = paramFormat;
            }
            const fn = "handleFormat_"+effectiveFormat;
            if (window[fn]) {
                window[fn](rawJson);
            } else {
                handleFormat_generic(rawJson);
            };
            currentTime = new Date(parseFloat(rawJson["ts"])*1000);
            time.push(currentTime);
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

        function createDataset(dataset, rawJson, index, yAxisID, data, formatOptions) {
            colorIndex = index;
            while (colorIndex >= colors.length) {
                colorIndex -= colors.length;
            }
            if (rawJson["unit"][index] == "") {
                lbl = rawJson["series"][index];
            } else {
                lbl = rawJson["series"][index]+" ("+rawJson["unit"][index]+")";
            };
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

        function createIfaceDataset(dataset, rawJson, index, formatOptions) {
            colorIndex = index;
            while (colorIndex >= colors.length) {
                colorIndex -= colors.length;
            }
            colorBorder = colors[colorIndex];
            let a = Math.floor((0.3 + 0.05 * colorIndex)*255);
            colorFill = colors[colorIndex] + a.toString(16);
            dataset.push({
                label: rawJson["series"][index*2]+" ("+rawJson["unit"][index*2]+"/s)",
                data: data1[index],
                fill: formatOptions.fill,
                pointRadius: formatOptions.pointRadius,
                backgroundColor: colorFill,
                borderColor: colorBorder,
                pointBackgroundColor: colorFill,
                pointBorderColor: colorBorder,
            },
            {
                label: rawJson["series"][index*2+1]+" ("+rawJson["unit"][index*2+1]+"/s)",
                data: data2[index],
                fill: formatOptions.fill,
                pointRadius: formatOptions.pointRadius,
                backgroundColor: colorFill,
                borderColor: colorBorder,
                pointBackgroundColor: colorFill,
                pointBorderColor: colorBorder,
            });
        };

        function resizeGraph(i = 1) {
            let dHeight = ((Math.max(document.documentElement.scrollHeight, document.body.scrollHeight))*0.9).toString()+"px";
            document.getElementById("chart-container").style.height = dHeight;
            graph.resize();
            if (((Math.max(document.documentElement.scrollHeight, document.body.scrollHeight)) > (Math.max(document.documentElement.clientHeight, document.body.clientHeight))) && (i < 10)) {
                resizeGraph(i+1)
            };
        };

        // -- Main --- \\
        document.addEventListener("DOMContentLoaded", () => {
            update();
            setInterval( update, paramRefreshTime );
            window.addEventListener("resize", () => resizeGraph(1));
        });
    </script>

</html>
