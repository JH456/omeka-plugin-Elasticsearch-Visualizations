
<svg id='connections-graph' style='width: 100%; height: 600px';></svg>

<script>
    (function() {
        var appendURLParam = (url, paramName, paramVal) => {
            if (url.indexOf('?') !== -1) {
                return url + '&' + paramName + '=' + paramVal
            } else {
                return url + '?' + paramName + '=' + paramVal
            }
        }
        jQuery.post(appendURLParam(window.location.href, 'graphData', 0), {}, (partialData) => {
            var totalResults = partialData.totalResults
            var completeData = {
                nodes: partialData.nodes,
                links: partialData.links
            }
            var nodeIDSet = new Set()
            for (var i = 0; i < partialData.nodes.length; i++) {
                nodeIDSet.add(partialData.nodes[i].id)
            }
            if (totalResults <= 1000) {
                graphVisualization.renderGraphOnSVG({
                  svgID: 'connections-graph',
                  data: completeData
                });
            } else  {
                var remainingRequests = Math.ceil((totalResults - 1000) / 1000)
                var totalRequests = remainingRequests
                for (var i = 1; i <= totalRequests; i++) {
                    jQuery.post(appendURLParam(window.location.href, 'graphData', i * 1000), {}, (chunk) => {
                        remainingRequests--;
                        for (var j = 0; j < chunk.nodes.length; j++) {
                            if (!nodeIDSet.has(chunk.nodes[j].id)) {
                                nodeIDSet.add(chunk.nodes[j].id)
                                completeData.nodes.push(chunk.nodes[j])
                            }
                        }
                        completeData.links = completeData.links.concat(chunk.links)
                        if (remainingRequests === 0) {
                            graphVisualization.renderGraphOnSVG({
                              svgID: 'connections-graph',
                              data: completeData
                            });
                        }
                    }, 'json')
                }
            }
        }, 'json')
    }())
</script>
