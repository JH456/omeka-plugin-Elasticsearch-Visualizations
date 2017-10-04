<?php queue_css_file('results'); ?>
<?php echo head(array('title' => __('Elasticsearch')));?>

<h1><?php echo __('Search'); ?></h1>

<div id="elasticsearch-search">
    <form id="elasticsearch-search-form">
        <input type="text" title="<?php echo __('Search keywords') ?>" name="q" value="<?php echo htmlspecialchars(array_key_exists('q', $_GET) ? $_GET['q'] : '', ENT_QUOTES); ?>" />
        <input type="submit" value="Search" />
    </form>
</div>

<?php
//echo "<pre>".htmlspecialchars(json_encode($results, JSON_PRETTY_PRINT))."</pre>";
?>

<?php if($results): ?>
    <h2>Found <?php echo $results['hits']['total']; ?> results</h2>

    <section id="elasticsearch-aggregations">
        <?php echo $this->partial('search/partials/aggregations.php', array('querystr' => $querystr, 'aggregations' => $results['aggregations'])); ?>
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