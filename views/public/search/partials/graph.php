
<svg id='connections-graph' style='width: 100%; height: inherit;'></svg>

<script>
    var completeGraphData // ewwwwwwwwwwwww global
    (function() {
        graphVisualization.initSimulation();
        graphDataRequester.requestCompleteGraphData()
        .then(function(data) {
            completeGraphData = data
            graphVisualization.renderGraphOnSVG(completeGraphData)
        })
    }())
</script>
