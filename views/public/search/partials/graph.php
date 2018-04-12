<div style='width: 100%; height: inherit; display: flex; justify-content: space-around; align-items: center;'>
    <svg display='none' id='connections-graph' style='width: 100%; height: inherit;'></svg>
    <input type='submit' value='Load Graph' id='show-graph-button' onclick='showGraph()'>
</div>

<script>
    var showGraph = function() {
        window.location.href = graphDataRequester.setURLParam('showGraph', 'true');
    };

    (function() {
        var displayGraph = graphDataRequester.getURLParam('showGraph');
        if (displayGraph === 'true') {
            jQuery('#connections-graph').show();
            jQuery('#show-graph-button').hide();
            var completeGraphData
            graphVisualization.initSimulation();
            graphDataRequester.requestCompleteGraphData()
            .then(function(data) {
                completeGraphData = data
                graphVisualization.renderGraphOnSVG(completeGraphData, graphColors.tagCategoryColors)
                graphVisualization.renderGraphOnSVG(graphFilterer.filterGraphData([], completeGraphData), graphColors.tagCategoryColors)
                var results = <?php echo json_encode($results) ?>;
                filterMenu.generateFilterMenu(results['aggregations']['tags']['buckets'], completeGraphData)
            })
        }
    }());
</script>
