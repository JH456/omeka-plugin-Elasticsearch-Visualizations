'use strict';

let graphVisualization = (function() {
    let simulation = d3.forceSimulation()

    function renderGraphOnSVG(paramsObject) {
        let svgID = paramsObject.svgID
        let graph = paramsObject.data
        let svgBackgroundID = "svg-background"

        let svg = d3.select("#" + svgID)
        let width = +svg.attr('width')
        let height = +svg.attr('height')

        let color = d3.scaleOrdinal(d3.schemeCategory20);

        simulation
            .force("link", d3.forceLink().id(function(d) { return d.id; }))
            .force("charge", d3.forceManyBody())
            .force("center", d3.forceCenter(width / 2, height / 2));

        let container = svg.append('g')
        let zoom = setupZoom(container)

        svg.call(zoom)

        let link = setupLinkBehavior(container, graph, color)

        let node = setupNodeBehavior(container, graph, color)

        simulation
            .nodes(graph.nodes)
            .on("tick", ticked);

        simulation.force("link")
            .links(graph.links);

        function ticked() {
            link
                .attr("x1", function(d) { return d.source.x; })
                .attr("y1", function(d) { return d.source.y; })
                .attr("x2", function(d) { return d.target.x; })
                .attr("y2", function(d) { return d.target.y; });

            node
                .attr("cx", function(d) { return d.x; })
                .attr("cy", function(d) { return d.y; });
        }
    }

    function setupZoom(svg) {
        let zoom = d3.zoom()
            .on("zoom", () => {
                svg.attr("transform", d3.event.transform);
            })
        return zoom
    }

    function setupNodeBehavior(svg, graph, color) {
        let node = svg.append("g")
            .attr("class", "nodes")
            .selectAll("circle")
            .data(graph.nodes)
            .enter().append("circle")
            .attr("r", function(d) {return d.group === 1 ? 4 : 8})
            .attr("fill", function(d) { return color(d.group); })
            .attr("stroke", function(d) { return color(d.group); })
            .call(d3.drag()
                .on("start", dragStarted)
                .on("drag", dragged)
                .on("end", dragEnded));

        node.append("title")
            .text(function(d) { return d.id; });
        return node
    }

    function setupLinkBehavior(svg, graph, color) {
        let link = svg.append("g")
            .attr("class", "links")
            .selectAll("line")
            .data(graph.links)
            .enter().append("line")
            .attr("stroke", function(d) { return color(d.group); })
            .attr("stroke-width", function(d) { return Math.sqrt(d.value); });
        return link
    }

    function dragStarted(d) {
        if (!d3.event.active) {
            simulation.alphaTarget(0.3).restart();
        }
        d.fx = d.x;
        d.fy = d.y;
    }

    function dragged(d) {
        d.fx = d3.event.x;
        d.fy = d3.event.y;
    }

    function dragEnded(d) {
        if (!d3.event.active) {
            simulation.alphaTarget(0);
        }
        d.fx = null;
        d.fy = null;
    }

    function appendURLParam(url, paramName, paramVal) {
        if (url.indexOf('?') !== -1) {
            return url + '&' + paramName + '=' + paramVal;
        } else {
            return url + '?' + paramName + '=' + paramVal;
        }
    }

    function filterSingletonTags(graphData) {
        let tagCounts = {}
        for (let i = 0; i < graphData.links.length; i++) {
            let tagName = graphData.links[i].target
            if (tagCounts[tagName]) {
                tagCounts[tagName]++
            } else  {
                tagCounts[tagName] = 1
            }
        }
        let i = 0
        while (i < graphData.links.length) {
            let tagName = graphData.links[i].target
            let tagCount = tagCounts[tagName]
            if (tagCount === 1) {
                graphData.links.splice(i, 1)
            } else {
                i++
            }
        }
        i = 0
        while (i < graphData.nodes.length) {
            let tagName = graphData.nodes[i].id
            let tagCount = tagCounts[tagName] || 2 // If not present, the node
                                                   // is not a tag node
            if (tagCount === 1) {
                graphData.nodes.splice(i, 1)
            } else {
                i++
            }
        }

        return graphData
    }

    function getDataAndConstructGraph() {
        jQuery.post(appendURLParam(window.location.href, 'graphData', 0), {}, function(partialData) {
            let totalResults = partialData.totalResults;
            let limit = partialData.limit;
            let completeData = {
                nodes: [],
                links: partialData.links
            };
            let nodeIDSet = new Set()
            for (let i = 0; i < partialData.nodes.length; i++) {
                if (!nodeIDSet.has(partialData.nodes[i].id)) {
                    nodeIDSet.add(partialData.nodes[i].id);
                    completeData.nodes.push(partialData.nodes[i]);
                }
            }
            if (totalResults <= limit) {
                renderGraphOnSVG({
                    svgID: 'connections-graph',
                    data: filterSingletonTags(completeData)
                });
            } else  {
                let remainingRequests = Math.ceil((totalResults - limit) / limit);
                let totalRequests = remainingRequests;
                for (let i = 1; i <= totalRequests; i++) {
                    jQuery.post(appendURLParam(window.location.href, 'graphData', i * limit), {}, function(chunk) {
                        remainingRequests--;
                        for (let j = 0; j < chunk.nodes.length; j++) {
                            if (!nodeIDSet.has(chunk.nodes[j].id)) {
                                nodeIDSet.add(chunk.nodes[j].id);
                                completeData.nodes.push(chunk.nodes[j]);
                            }
                        }
                        completeData.links = completeData.links.concat(chunk.links);
                        if (remainingRequests === 0) {
                            renderGraphOnSVG({
                              svgID: 'connections-graph',
                              data: filterSingletonTags(completeData)
                            });
                        }
                    }, 'json');
                }
            }
        }, 'json');
    }

    return {
        getDataAndConstructGraph
    }
}())

