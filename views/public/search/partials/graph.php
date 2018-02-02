<svg id='connections-graph' width='780' height='600'></svg>

<script>
    graphVisualization.renderGraphOnSVG({
        svgID: 'connections-graph',
        data: <?php echo $graphData; ?>
    });
</script>
