<?php $pageBlocks = $hit['_source']['blocks']; ?>

<ul>
<?php if(isset($hit['_source']['exhibit'])): ?>
    <li><b>Exhibit:</b> <?php echo $hit['_source']['exhibit']; ?></li>
<?php endif; ?>
</ul>

<?php if($pageBlocks && count($pageBlocks) > 0): ?>
<div class="elasticsearch-result-text">
    <?php $blockText = strip_tags($pageBlocks[0]['text'], '<p><br>'); ?>
    <?php echo Elasticsearch_Utils::truncateText($blockText, $maxTextLength); ?>
</div>
<?php endif; ?>
