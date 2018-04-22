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
            graphDataRequester.requestCompleteGraphData(3000)
            .then(function(data) {
                completeGraphData = data
                graphVisualization.renderGraphOnSVG(completeGraphData, graphColors.getTagColor)
                // D3 does weird things to the nodes in complete data the first time it is run, and this
                // makes it not work with the filters, so I need to call this twice because I am a
                // potato
                graphVisualization.renderGraphOnSVG(graphFilterer.filterGraphData([], completeGraphData), graphColors.getTagColor)

                var results = <?php echo json_encode($results) ?>;
                filterMenu.generateFilterMenu(
                    results['aggregations']['tags']['buckets'], 
                    completeGraphData, graphColors.getTagCategoryList()
                );
            })
        }
    }());
</script>
