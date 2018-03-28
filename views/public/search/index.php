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
<div class='w3-col l4' style="height: inherit; 
                              box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);">

    <div id=elasticsearch-search_block style="background-color: #eeb211">
        <div id="elasticsearch-searchbar" style="padding: 10px;">
            <?php echo $this->partial('search/partials/searchbar.php', array('query' => $query)); ?>
        </div>

        <div id="elasticsearch-help"  style="display:none;">
            <?php echo $this->partial('search/partials/help.php'); ?>
        </div>
    </div>
	
    <?php 
    if ($results):
    ?>
        <div id="elasticsearch-documents" style="overflow-y:scroll; height:70%;">
            <?php 
            if(count($results['hits']['hits']) > 0):
                foreach($results['hits']['hits'] as $hit):
                    echo $this->partial('search/partials/hit.php', array('hit' => $hit));
                endforeach;
            else:
                echo __("Search did not return any results.");
            endif;
            ?>
        </div>
        <div id="elasticsearch-footer" style="border-top: 3px solid #eeb211;">
            <div class='w3-col l7' style="padding:10px; color: #eeb211; font-weight: bold;">
                <?php $totalResults = isset($results['hits']['total']) ? $results['hits']['total'].' '.__('results') : null; ?>
                <?php echo __('Search Total:') . " $totalResults"; ?>
            </div>
            <div class='w3-col l4' style="float:right; padding-left: 10px; padding-top: 10px;">
                 <?php echo pagination_links(); ?>
            </div> 
        </div>
    <?php 
    else: 
    ?>
        <div>
            <h2><?php echo __("Search failed"); ?></h2>
            <p><?php echo __("The search query could not be executed. Please check your search query and try again."); ?></p>
        </div>
    <?php 
    endif; 
    ?>
</div>

<!-- Search Results -->

<div class='w3-col l8' style="height: inherit;">

    <?php 
    if($results): 
    ?>
        <div style="height: inherit;">
            <?php echo $this->partial('search/partials/graph.php'/*, array('graphData' => $graphData)*/); ?>
        </div>
    <?php 
    else: 
    ?>
        <div>
            <h2><?php echo __("Search failed"); ?></h2>
            <p><?php echo __("The search query could not be executed. Please check your search query and try again."); ?></p>
        </div>
    <?php 
    endif;  
    ?>
</div>

<?php echo foot(); ?>

<?php endif ?>
