<?php echo head(array('title' => __('Elasticsearch')));?>

<h1><?php echo __('Search the Collection'); ?></h1>

<form id="elasticsearch-search-form">
    <input type="submit" value="Search" />
    <span class="float-wrap">
        <input type="text" title="<?php echo __('Search keywords') ?>" name="q" value="<?php echo htmlspecialchars(array_key_exists('q', $_GET) ? $_GET['q'] : '', ENT_QUOTES); ?>" />
    </span>
</form>

<div id="elasticsearch-results">
    <h2 id="num-found"><?php echo $results['hits']['total']; ?></h2>
    <pre><?php var_export($results); ?></pre>
</div>