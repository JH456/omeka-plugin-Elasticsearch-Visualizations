<svg id='connections-graph' width="960" height="600"></svg>

<script>
    graphVisualization.renderGraphOnSVG({
        svgID: 'connections-graph',
        data: <?php echo file_get_contents('/var/www/html/plugins/Elasticsearch/views/public/resources/miserables.json'); ?>
    });
</script>
