<?php $result_img = record_image($record, 'thumbnail', array('class' => 'elasticsearch-result-image')); ?>
<?php if($result_img): ?>
    <a href="<?php echo $record_url; ?>"><?php echo $result_img; ?></a>
<?php endif; ?>

<ul>
    <li data-field="resulttype"><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>
    <li data-field="description"><b>Description:</b>
        <?php $description = strip_tags($hit['_source']['description'], '<p><br><i><b><em>'); ?>
        <?php echo Elasticsearch_Utils::truncateText($description, $maxTextLength); ?>
    </li>
    <?php if(isset($hit['_source']['tags']) && count($hit['_source']['tags']) > 0): ?>
        <li data-field="tags"><b>Tags:</b>  <?php echo implode(", ", $hit['_source']['tags']); ?></li>
    <?php endif; ?>
    <li data-field="created"><b>Record Created: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['created']); ?></li>
    <li data-field="updated"><b>Record Updated: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['updated']); ?></li>
</ul>

