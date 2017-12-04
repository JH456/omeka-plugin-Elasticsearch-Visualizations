<?php queue_css_file('results'); ?>
<?php queue_js_file('results'); ?>
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
        <br>
        <a href="javascript:void(0);" id="elasticsearch-help-btn" style="display:block;clear:both;">Search Help</a>
    </form>
</div>

<div id="elasticsearch-help" style="display:none;">
    <table>
        <thead>
            <tr>
                <th>Topic</th>
                <th>Examples</th>
            </tr>
        </thead>
        <tr>
            <td>
                Search by field:
            </td>
            <td>
                Possible fields you can search by include:
                    <i>title</i>, <i>description</i>, <i>collection</i>, <i>exhibit</i>, <i>itemType</i>, <i>resulttype</i>,
                    <i>featured</i>, and <i>tags</i>. Examples:
                <br><br>
                <code>title:"Inhabited Spaces"</code><br>
                <code>collection:Map*</code>
                <code>itemType:("Historical Map" OR "Still Image")</code><br>
                <code>resulttype:Exhibit</code>
                <code>featured:true</code><br>
                <code>tags:forts</code><br>
            </td>
        </tr>
        <tr>
            <td>Search using boolean operators and wildcards:</td>
            <td>
                <code>paris AND fortifications</code><br>
                <code>title:paris AND itemType:Text</code><br>
                <code>featured:true</code>
                <code>184?s OR 185?s</code>
            </td>
        </tr>
        <tr>
            <td>Boost searches:</td>
            <td>Use the boost operator ^ to make one term more relevant than another. For example if we wanted to boost the term "paris" (the default boost is 1):<br><br>
                <code>paris^2 western</code>
            </td>
        </tr>
    </table>
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