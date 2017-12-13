<ul>
    <li title="resulttype"><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>

<?php if(isset($hit['_source']['exhibit'])): ?>
    <li title="exhibit"><b>Exhibit:</b> <?php echo $hit['_source']['exhibit']; ?></li>
<?php endif; ?>

<?php $pageBlocks = $hit['_source']['blocks']; ?>
<?php if($pageBlocks && count($pageBlocks) > 0): ?>
    <li title="blocks.text"><b>Text:</b>
        <?php $blockText = strip_tags($pageBlocks[0]['text'], '<p><br>'); ?>
        <?php echo Elasticsearch_Utils::truncateText($blockText, $maxTextLength); ?>
    </li>
<?php endif; ?>

    <li title="created"><b>Record Created: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['created']); ?></li>
    <li title="updated"><b>Record Updated: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['updated']); ?></li>
</ul>
