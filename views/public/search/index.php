<?php queue_css_file('results'); ?>
<?php echo head(array('title' => __('Elasticsearch')));?>

<h1><?php echo __('Search'); ?></h1>

<form id="elasticsearch-search-form">
    <input type="submit" value="Search" />
    <span class="float-wrap">
        <input type="text" title="<?php echo __('Search keywords') ?>" name="q" value="<?php echo htmlspecialchars(array_key_exists('q', $_GET) ? $_GET['q'] : '', ENT_QUOTES); ?>" />
    </span>
</form>

<div id="elasticsearch-results">
    <h2>Found <?php echo $results['hits']['total']; ?> results</h2>

    <?php foreach($results['hits']['hits'] as $hit): ?>
        <?php echo $this->partial('search/partials/hit.php', array('hit' => $hit)); ?>
    <?php endforeach; ?>

    <small>Search query executed in <?php echo $results['took']; ?> milliseconds.</small>
</div>

<?php echo pagination_links(); ?>
<?php echo foot();