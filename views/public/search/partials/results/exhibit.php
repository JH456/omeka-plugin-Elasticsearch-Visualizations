<?php $result_img = record_image($record, 'thumbnail', array('class' => 'elasticsearch-result-image')); ?>
<?php if($result_img): ?>
    <?php echo $result_img; ?>
<?php endif; ?>

<ul>
    <li><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>
    <li><b>Description:</b>
        <?php $description = strip_tags($hit['_source']['description'], '<p><br><i><b><em>'); ?>
        <?php echo Elasticsearch_Utils::truncateText($description, $maxTextLength); ?>
    </li>
    <?php if(isset($hit['_source']['tags']) && count($hit['_source']['tags']) > 0): ?>
        <li><b>Tags:</b>  <?php echo implode(", ", $hit['_source']['tags']); ?></li>
    <?php endif; ?>
</ul>

