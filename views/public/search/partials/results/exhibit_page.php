<?php $pageBlocks = $hit['_source']['blocks']; ?>

<ul>
    <li><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>

<?php if(isset($hit['_source']['exhibit'])): ?>
    <li><b>Exhibit:</b> <?php echo $hit['_source']['exhibit']; ?></li>
<?php endif; ?>

<?php if($pageBlocks && count($pageBlocks) > 0): ?>
    <li><b>Text:</b>
        <?php $blockText = strip_tags($pageBlocks[0]['text'], '<p><br>'); ?>
        <?php echo Elasticsearch_Utils::truncateText($blockText, $maxTextLength); ?>
    </li>
<?php endif; ?>
</ul>
