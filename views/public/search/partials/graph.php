<svg id='connections-graph' style='width: 100%; height: inherit;'></svg>

<script>
    var completeGraphData // ewwwwwwwwwwwww global
    (function() {
        graphVisualization.initSimulation();
        graphDataRequester.requestCompleteGraphData()
        .then(function(data) {
            completeGraphData = data
            graphVisualization.renderGraphOnSVG(completeGraphData, graphColors.tagCategoryColors)
            graphVisualization.renderGraphOnSVG(graphFilterer.filterGraphData([], completeGraphData), graphColors.tagCategoryColors)
            var results = <?php echo json_encode($results) ?>;
            filterMenu.generateFilterMenu(results['aggregations']['tags']['buckets'], completeGraphData)
        })
    }())
</script>
