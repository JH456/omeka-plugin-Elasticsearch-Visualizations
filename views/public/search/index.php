<?php queue_css_file('results'); ?>
<?php queue_js_file('results'); ?>
<?php echo head(array('title' => __('Elasticsearch')));?>

<h1><?php echo __('Search') . (isset($results['hits']['total']) ? " ({$results['hits']['total']} results)" : ''); ?></h1>

<div id="elasticsearch-search">
    <form id="elasticsearch-search-form">
        <input type="text" title="<?php echo __('Search keywords') ?>" name="q" value="<?php echo htmlspecialchars(array_key_exists('q', $_GET) ? $_GET['q'] : '', ENT_QUOTES); ?>" />
        <?php foreach($query['facets'] as $facet_name => $facet_values): ?>
            <?php if(is_array($facet_values)): ?>
                <?php foreach($facet_values as $facet_value): ?>
                    <input type="hidden" name="<?php echo "facet_{$facet_name}[]"; ?>" value="<?php echo $facet_value; ?>">
                <?php endforeach; ?>
            <?php else: ?>
                <input type="hidden" name="<?php echo "facet_{$facet_name}"; ?>" value="<?php echo $facet_values; ?>">
            <?php endif; ?>
        <?php endforeach; ?>
        <input type="submit" value="Search" />
        <br>
        <a href="javascript:void(0);" id="elasticsearch-help-btn" style="display:block;clear:both;">Search Help</a>
    </form>
</div>

<div id="elasticsearch-help" style="display:none;">
    <?php echo $this->partial('search/partials/help.php'); ?>
</div>

<?php
//echo "<pre>".htmlspecialchars(json_encode($results, JSON_PRETTY_PRINT))."</pre>";
?>

<?php if($results): ?>

    <section id="elasticsearch-sidebar">
        <?php
        echo $this->partial('search/partials/aggregations.php', array(
                'query'        => $query,
                'aggregations' => $results['aggregations'])
        );
        ?>
    </section>

    <section id="elasticsearch-results">
        <?php if(count($results['hits']['hits']) > 0): ?>
            <?php foreach($results['hits']['hits'] as $hit): ?>
                <?php echo $this->partial('search/partials/hit.php', array('hit' => $hit)); ?>
            <?php endforeach; ?>
        <?php else: ?>
            Search did not return any results.
        <?php endif; ?>

        <?php echo pagination_links(); ?>
    </section>

<?php else: ?>
    <section>
        <h2>Search failed</h2>
        <p>The search query could not be executed. Please check your search query and try again.</p>
    </section>
<?php endif;  ?>


<?php echo foot();