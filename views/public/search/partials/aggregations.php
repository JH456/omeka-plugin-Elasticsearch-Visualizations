<?php
$aggregation_names = array(
    'itemType'   => 'Item Types',
    'collection' => 'Collections',
    'tags'       => 'Tags'
);
?>

<?php foreach($aggregation_names as $agg_name => $agg_display_name): ?>
    <h3><?php echo $agg_display_name; ?></h3>
    <ul>
        <?php foreach($aggregations[$agg_name]['buckets'] as $agg): ?>
            <li>
                <a href="<?php echo Elasticsearch_Utils::getFacetUrl($querystr, $agg_name, $agg['key']); ?>">
                    <?php echo $agg['key'] ." (".$agg['doc_count'].")"; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endforeach; ?>

