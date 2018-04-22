'use strict';

var graphVisualization = (function() {
    var simulation = d3.forceSimulation()
    var svgID = 'connections-graph'

    var svg
    var svgWidth
    var svgHeight

    function initSimulation() {
        svg = d3.select("#" + svgID)
        var svg_element = document.getElementById(svgID)
        svgWidth = svg_element.width.baseVal.value
        svgHeight = svg_element.height.baseVal.value

        simulation
            .force("link", d3.forceLink().id(function(d) { return d.id; }))
            .force("charge", d3.forceManyBody())
            .force("center", d3.forceCenter(svgWidth / 2, svgHeight / 2));
    }

    /**
     * @param color: a function that takes in a String as input and produces a
     * hexadecimal number color as output.
     */
    function renderGraphOnSVG(graphData, color) {
        function resetSVG() {
            simulation.nodes([])
                .on("tick", null);
            simulation.force("link").links([])
            svg.html("")
            simulation.restart()
        }

        resetSVG()

        var container = svg.append('g')
        var zoom = setupZoom(container)

        svg.call(zoom)

        var linkElement = setupLinkBehavior(container, graphData, color)

        var nodeElement = setupNodeBehavior(container, graphData, color)

        var tooltip = setupTooltipBehavior(container, graphData, color)

        simulation
            .nodes(graphData.nodes)
            .on("tick", ticked);

        simulation.force("link")
            .links(graphData.links);

        //Compute and save the degrees of each vertex
        var edges = [];
        
        var hasDegrees = false;
        for (var i = 0; i < graphData.links.length && !hasDegrees; i++) {
            var cur = graphData.links[i];
            if (cur.source.degree || cur.target.degree) {
                hasDegrees = true;
            }
        }

        if (!hasDegrees) {
            for (var i = 0; i < graphData.links.length; i++) {
                var cur = graphData.links[i];
                cur.source.degree = (cur.source.degree || 0) + 1;
                cur.target.degree = (cur.target.degree || 0) + 1;
            }
        }

        function ticked() {
            linkElement
                .attr("x1", function(d) { return d.source.x; })
                .attr("y1", function(d) { return d.source.y; })
                .attr("x2", function(d) { return d.target.x; })
                .attr("y2", function(d) { return d.target.y; });

            nodeElement
                .attr("cx", function(d) { return d.x; })
                .attr("cy", function(d) { return d.y; });
        }
    }

    function toSparseMatrix(edges) {
        var result = {};
        for (var i = 0; i < edges.length; i++) {
            var cur = edges[i];
            if (result[cur[0]] === undefined || result[cur[1]] === undefined) {
                result[cur[0]] = [];
                result[cur[1]] = [];
            }
            result[cur[0]].push(cur[1]);
            result[cur[1]].push(cur[0]);
        }

        return result;
    }

    function setupZoom(svg) {
        var zoom = d3.zoom()
            .on("zoom", function() {
                svg.attr("transform", d3.event.transform);
            })
        return zoom
    }

    function setupNodeBehavior(svg, graph, color) {
        var node = svg.append("g")
            .attr("class", "nodes")
            .selectAll("circle")
            .data(graph.nodes)
            .enter().append("circle")
            .attr("r", function(d) {
                return d.group === 1 ? 4 : Math.sqrt(d.degree || 0) + 4;
            })
            .attr("fill", function(d) { return color(d.id, 'fill'); })
            .attr("stroke", function(d) { return color(d.id, 'stroke'); })
            .call(d3.drag()
                .on("start", dragStarted)
                .on("drag", dragged)
                .on("end", dragEnded));

        node.append("title")
            .text(function(d) {
                var result = d.title || d.id;
                result = result.split('>');
                if (result.length > 1) {
                    result = result[1];
                } else {
                    result = result[0];
                }
                result = result.split('<')[0].trim();
                if (d.tags) {
                    for (var i = 0; i < d.tags.length; i++) {
                        result += "; " + d.tags[i];
                    }
                }
                return result;
            });

        node.on("click", function(d) {
            if (d.group === 1) {
                window.open('/items/show/' + d.id.split("_")[1]);
            } else {
                var query = d.id;
                var prefix = '';
                if (d.id.indexOf('Box') >= 0 || d.id.indexOf('Folder') >= 0) {
                    prefix = 'tags:';
                }
                if (query.indexOf(':') >= 0) {
                    query = d.id.split(':')[1];
                }
                var url = '/elasticsearch?q=' + prefix +
                    '"' + query.trim() + '"';
                window.open(url);
            }
        });
        return node
    }

    function setupLinkBehavior(svg, graph, color) {
        var linkElement = svg.append("g")
            .attr("class", "links")
            .selectAll("line")
            .data(graph.links)
            .enter().append("line")
            .attr("stroke", function(d) { return color(d.target, 'fill') === '#000000' ? color(d.target.id, 'fill') : color(d.target, 'fill'); })
            .attr("stroke-width", function(d) { return Math.sqrt(d.value); });
        return linkElement
    }

    function setupTooltipBehavior(svg, graph, color) {
        var tooltip = svg.select("body").append("div")
            .attr("class", "tooltip")
            .style("opacity", 0);

        svg.selectAll("circle")
            .on("mouseover", function(d) {
                tooltip.transition()
                .duration(200)
                .style("opacity", 0.9);

                tooltip.html("fffffffffffffff" + "<br>")
                .style("left", d3.event.pageX + "px")
                .style("top", (d3.event.pageY - 28) + "px");
            })
            .on("mouseout", function(d) {
                tooltip.transition()
                .style("opacity", 0);
            });

        return tooltip;
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
        renderGraphOnSVG,
        initSimulation
    }
}())

