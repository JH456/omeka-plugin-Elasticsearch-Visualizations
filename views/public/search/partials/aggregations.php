<?php
$aggregation_labels = array(
    'itemType'   => 'Item Types',
    'collection' => 'Collections',
    'tags'       => 'Tags'
);
$querystr = Elasticsearch_Utils::getQueryString($query);
$applied_facets = $query['facets'];
?>

<?php if(count($applied_facets) > 0): ?>
<div id="elasticsearch-applied-filters">
<h3>Applied Filters</h3>
<ul>
    <?php foreach($applied_facets as $facet_name => $facet_values): ?>
        <?php $facet_label = $aggregation_labels[$facet_name]; ?>
        <?php $facet_value = is_array($facet_values) ? implode(', ', $facet_values) : $facet_values; ?>
        <li><?php echo htmlspecialchars("$facet_label = <i>$facet_value</i>"); ?></li>
    <?php endforeach ?>
</ul>
</div>
<?php endif; ?>


<div id="elasticsearch-filters">
<h3>Filters</h3>
<?php foreach($aggregation_labels as $agg_name => $agg_label): ?>
    <?php echo $agg_label; ?>
    <ul>
        <?php foreach($aggregations[$agg_name]['buckets'] as $agg): ?>
            <?php $facet_url = Elasticsearch_Utils::getFacetUrl($querystr, $agg_name, $agg['key']); ?>
            <li>
                <a href="<?php echo $facet_url; ?>"><?php echo htmlspecialchars($agg['key']) ." (".$agg['doc_count'].")"; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>
</div>
