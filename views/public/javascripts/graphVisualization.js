'use strict';

var graphVisualization = (function() {
    var simulation = d3.forceSimulation()
    var svgID = 'connections-graph'
    var svgBackgroundID = "svg-background"
    var completeData = {}

    var svg
    var svgWidth
    var svgHeight
    var color

    function initSimulation() {
        svg = d3.select("#" + svgID)
        svgWidth = +svg.attr('svgWidth')
        svgHeight = +svg.attr('svgHeight')
        color = d3.scaleOrdinal(d3.schemeCategory20);

        simulation
            .force("link", d3.forceLink().id(function(d) { return d.id; }))
            .force("charge", d3.forceManyBody())
            .force("center", d3.forceCenter(svgWidth / 2, svgHeight / 2));

    }

    function resetSVG() {
        simulation.nodes([])
            .on("tick", null);
        simulation.force("link").links([])
        svg.html("")
        simulation.restart()
    }

    function renderGraphOnSVG(graphData) {

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
            .attr("r", function(d) {return d.group === 1 ? 4 : 8})
            .attr("fill", function(d) { return color(d.group); })
            .attr("stroke", function(d) { return color(d.group); })
            .call(d3.drag()
                .on("start", dragStarted)
                .on("drag", dragged)
                .on("end", dragEnded));

        node.append("title")
            .text(function(d) {
                var result = d.title || d.id;
                if (d.tags) {
                    for (var i = 0; i < d.tags.length; i++) {
                        result += "; " + d.tags[i];
                    }
                }
                return result;
            });

        node.on("click", function(d) {
            window.open("/items/show/" + d.id.split("_")[1]);
        });
        return node
    }

    function setupLinkBehavior(svg, graph, color) {
        var linkElement = svg.append("g")
            .attr("class", "links")
            .selectAll("line")
            .data(graph.links)
            .enter().append("line")
            .attr("stroke", function(d) { return color(d.group); })
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

    function appendURLParam(url, paramName, paramVal) {
        if (url.indexOf('?') !== -1) {
            return url + '&' + paramName + '=' + paramVal;
        } else {
            return url + '?' + paramName + '=' + paramVal;
        }
    }

    function filterRareTags(graphData, minimumMentionCount) {
        var tagCounts = {}
        for (var i = 0; i < graphData.links.length; i++) {
            var tagName = graphData.links[i].target
            if (tagCounts[tagName]) {
                tagCounts[tagName]++
            } else  {
                tagCounts[tagName] = 1
            }
        }
        var i = 0
        while (i < graphData.links.length) {
            var tagName = graphData.links[i].target
            var tagCount = tagCounts[tagName]
            if (tagCount < minimumMentionCount) {
                graphData.links.splice(i, 1)
            } else {
                i++
            }
        }
        i = 0
        while (i < graphData.nodes.length) {
            var tagName = graphData.nodes[i].id
            var tagCount = tagCounts[tagName] || minimumMentionCount
            if (tagCount < minimumMentionCount) {
                graphData.nodes.splice(i, 1)
            } else {
                i++
            }
        }

        return graphData
    }

    function addChunkToCompleteData(includedNodeSet, dataChunk) {
        var documentsToLinks = {}
        for (var i = 0; i < dataChunk.links.length; i++) {
            var link = dataChunk.links[i]
            var documentName = link.source
            if (!documentsToLinks[documentName]) {
                documentsToLinks[documentName] = [link]
            } else {
                documentsToLinks[documentName].push(link)
            }
        }
        for (var i = 0; i < dataChunk.nodes.length; i++) {
            var nodeId = dataChunk.nodes[i].id
            if (!includedNodeSet.has(nodeId)) {
                includedNodeSet.add(nodeId);
                completeData.nodes.push(dataChunk.nodes[i]);
                var links = documentsToLinks[nodeId]
                if (links) {
                    for (var j = 0; j < links.length; j++) {
                        completeData.links.push(links[j])
                    }
                }
            }
        }
    }

    function getDataAndConstructGraph() {
        jQuery.post(appendURLParam(window.location.href, 'graphData', 0), {}, function(partialData) {
            var totalResults = partialData.totalResults;
            var limit = partialData.limit;

            completeData.nodes = []
            completeData.links = []

            var includedNodeSet = new Set()
            addChunkToCompleteData(includedNodeSet, partialData)
            if (totalResults <= limit) {
                renderGraphOnSVG(filterRareTags(completeData, 2));
            } else  {
                var remainingRequests = Math.ceil((totalResults - limit) / limit);
                var totalRequests = remainingRequests;
                for (var i = 1; i <= totalRequests; i++) {
                    jQuery.post(appendURLParam(window.location.href, 'graphData', i * limit), {}, function(dataChunk) {
                        remainingRequests--;
                        addChunkToCompleteData(includedNodeSet, dataChunk)
                        if (remainingRequests === 0) {
                            renderGraphOnSVG(filterRareTags(completeData, 2));
                        }
                    }, 'json');
                }
            }
        }, 'json');
    }

    function passesFilters(tagName, regexFilters) {
        return !regexFilters.some(function(regex) {
            return regex.test(tagName)
        })
    }

    function filterTagsFromGraphData(exclusionFilterRegexStrings, graphData) {
        var regexFilters = []
        for (var i = 0; i < exclusionFilterRegexStrings.length; i++) {
            regexFilters.push(new RegExp(exclusionFilterRegexStrings[i]))
        }
        return  {
            nodes: graphData.nodes.filter(function(node) {
                    return passesFilters(node.id, regexFilters)
                }),
            links: graphData.links.filter(function(link) {
                    return passesFilters(link.target.id, regexFilters)
                })
        }
    }

    function filterAndReloadGraph(exclusionFilterRegexStrings) {
        var filteredData = filterRareTags(
            filterTagsFromGraphData(exclusionFilterRegexStrings, completeData),
            2)
        renderGraphOnSVG(filteredData)
    }

    return {
        getDataAndConstructGraph,
        filterAndReloadGraph,
        initSimulation
    }
}())

