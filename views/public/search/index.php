<?php queue_css_file('results'); ?>
<?php echo head(array('title' => __('Elasticsearch')));?>

<h1><?php echo __('Search'); ?></h1>

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
    </form>

</div>

<?php
//echo "<pre>".htmlspecialchars(json_encode($results, JSON_PRETTY_PRINT))."</pre>";
?>

<?php if($results): ?>
    <h2>Found <?php echo $results['hits']['total']; ?> results</h2>

    <section id="elasticsearch-sidebar">
        <?php
        echo $this->partial('search/partials/aggregations.php', array(
                'query'        => $query,
                'aggregations' => $results['aggregations'])
        );
        ?>
    </section>

    <section id="elasticsearch-results">
        <?php foreach($results['hits']['hits'] as $hit): ?>
            <?php echo $this->partial('search/partials/hit.php', array('hit' => $hit)); ?>
        <?php endforeach; ?>

        <div style="margin-bottom:18px;">
            <small>Search query executed in <?php echo $results['took']; ?> milliseconds.</small>
        </div>
        <?php echo pagination_links(); ?>
    </section>

<?php else: ?>
    <section>
        <h2>Search failed</h2>
        <p>An error occurred while executing the search.</p>
    </section>
<?php endif;  ?>


<?php echo foot();