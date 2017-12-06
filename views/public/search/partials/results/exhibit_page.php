<?php $pageBlocks = $hit['_source']['blocks']; ?>

<ul>
    <li><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>

<?php if(isset($hit['_source']['exhibit'])): ?>
    <li><b>Exhibit:</b> <?php echo html_escape($hit['_source']['exhibit']); ?></li>
<?php endif; ?>

<?php if($pageBlocks && count($pageBlocks) > 0): ?>
    <li><b>Text:</b>
        <?php $blockText = strip_tags($pageBlocks[0]['text'], '<p><br>'); ?>
        <?php echo html_escape(Elasticsearch_Utils::truncateText($blockText, $maxTextLength)); ?>
    </li>
<?php endif; ?>

    <li><b>Created: </b> <?php echo html_escape(Elasticsearch_Utils::formatDate($hit['_source']['created'])); ?></li>
    <li><b>Updated: </b> <?php echo html_escape(Elasticsearch_Utils::formatDate($hit['_source']['updated'])); ?></li>
</ul>
