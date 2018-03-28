
<svg id='connections-graph' style='width: 100%; height: inherit;'></svg>

<script>
    (function() {
        var appendURLParam = function(url, paramName, paramVal) {
            if (url.indexOf('?') !== -1) {
                return url + '&' + paramName + '=' + paramVal;
            } else {
                return url + '?' + paramName + '=' + paramVal;
            }
        };
        jQuery.post(appendURLParam(window.location.href, 'graphData', 0), {}, function(partialData) {
            var totalResults = partialData.totalResults;
            var limit = partialData.limit;
            var compvareData = {
                nodes: partialData.nodes,
                links: partialData.links
            };
            var nodeIDSet = new Set()
            for (var i = 0; i < partialData.nodes.length; i++) {
                nodeIDSet.add(partialData.nodes[i].id);
            }
            if (totalResults <= limit) {
                graphVisualization.renderGraphOnSVG({
                  svgID: 'connections-graph',
                  data: compvareData
                });
            } else  {
                var remainingRequests = Math.ceil((totalResults - limit) / limit);
                var totalRequests = remainingRequests;
                for (var i = 1; i <= totalRequests; i++) {
                    jQuery.post(appendURLParam(window.location.href, 'graphData', i * limit), {}, function(chunk) {
                        remainingRequests--;
                        for (var j = 0; j < chunk.nodes.length; j++) {
                            if (!nodeIDSet.has(chunk.nodes[j].id)) {
                                nodeIDSet.add(chunk.nodes[j].id);
                                compvareData.nodes.push(chunk.nodes[j]);
                            }
                        }
                        compvareData.links = compvareData.links.concat(chunk.links);
                        if (remainingRequests === 0) {
                            graphVisualization.renderGraphOnSVG({
                              svgID: 'connections-graph',
                              data: compvareData
                            });
                        }
                    }, 'json');
                }
            }
        }, 'json');
    }());
</script>
