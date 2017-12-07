<ul>
    <li data-field="resulttype"><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>

<?php if(isset($hit['_source']['exhibit'])): ?>
    <li data-field="exhibit"><b>Exhibit:</b> <?php echo $hit['_source']['exhibit']; ?></li>
<?php endif; ?>

<?php $pageBlocks = $hit['_source']['blocks']; ?>
<?php if($pageBlocks && count($pageBlocks) > 0): ?>
    <li data-field="blocks.text"><b>Text:</b>
        <?php $blockText = strip_tags($pageBlocks[0]['text'], '<p><br>'); ?>
        <?php echo Elasticsearch_Utils::truncateText($blockText, $maxTextLength); ?>
    </li>
<?php endif; ?>

    <li data-field="created"><b>Record Created: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['created']); ?></li>
    <li data-field="updated"><b>Record Updated: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['updated']); ?></li>
</ul>
