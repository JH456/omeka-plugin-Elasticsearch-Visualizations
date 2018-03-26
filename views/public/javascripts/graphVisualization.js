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

    return {
        renderGraphOnSVG
    }
}())

