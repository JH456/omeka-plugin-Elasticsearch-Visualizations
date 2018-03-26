<?php if ($_SERVER['REQUEST_METHOD'] === 'GET'): ?>

<?php queue_css_file('elasticsearch-results'); ?>
<?php queue_css_url('https://www.w3schools.com/w3css/4/w3.css'); ?>
<?php queue_css_url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'); ?>
<?php queue_js_file('elasticsearch'); ?>
<?php queue_js_string('ElasticsearchPlugin.setupSearchResults();'); ?>

<?php queue_css_file('graphStyle'); ?>
<?php queue_css_file('fix-w3'); ?>
<?php queue_js_url('https://d3js.org/d3.v4.min.js'); ?>
<?php queue_js_file('graphVisualization'); ?>

<?php echo head(array('title' => __('Elasticsearch'), 'bodyclass' => 'w3Page'));?>




<!-- SEARCH BAR -->
<section class='w3-col l3'>

    <div>
        <?php echo $this->partial('search/partials/searchbar.php', array('query' => $query)); ?>
    </div>

    <div id="elasticsearch-help"  style="display:none;">
        <?php echo $this->partial('search/partials/help.php'); ?>
    </div>

    <?php $totalResults = isset($results['hits']['total']) ? $results['hits']['total'].' '.__('results') : null; ?>
    <h6><?php echo __('Search') . " ($totalResults)"; ?></h6>

    <?php if($results): ?>
        <div style="overflow:scroll; max-height:684px;" >
            <?php if(count($results['hits']['hits']) > 0): ?>
                <?php foreach($results['hits']['hits'] as $hit): ?>
                    <?php echo $this->partial('search/partials/hit.php', array('hit' => $hit)); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <?php echo __("Search did not return any results."); ?>
            <?php endif; ?>

            <?php echo pagination_links(); ?>
        </div>
    <?php else: ?>
        <div>
            <h2><?php echo __("Search failed"); ?></h2>
            <p><?php echo __("The search query could not be executed. Please check your search query and try again."); ?></p>
        </div>
    <?php endif;  ?>

</section>

<!-- RESULTS -->
<?php
//echo "<!--".json_encode($results, JSON_PRETTY_PRINT)."-->";
?>

<!-- Search Results -->

<div class='w3-col l9'>

    <?php if($results): ?>
<!--
        <section class='w3-col l2' style="overflow:scroll; max-height:684px;">
            <?php
            echo $this->partial('search/partials/aggregations.php', array(
                    'query'        => $query,
                    'aggregations' => $results['aggregations'])
            );
            ?>
        </section>
-->
        <div>
            <?php echo $this->partial('search/partials/graph.php'/*, array('graphData' => $graphData)*/); ?>
        </div>
    <?php else: ?>
        <div>
            <h2><?php echo __("Search failed"); ?></h2>
            <p><?php echo __("The search query could not be executed. Please check your search query and try again."); ?></p>
        </div>
    <?php endif;  ?>

</div>

<?php echo foot(); ?>

<?php endif ?>
