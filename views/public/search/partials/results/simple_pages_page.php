<?php $pageText = strip_tags($hit['_source']['pageText'], '<p><br>'); ?>

<ul>
    <li data-field="resulttype"><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>
    <li data-field="pageText"><b>Page Text:</b>
        <?php echo Elasticsearch_Utils::truncateText(strip_tags($pageText, '<p><br>'), $maxTextLength); ?>
    </li>
    <li data-field="created"><b>Record Created: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['created']); ?></li>
    <li data-field="updated"><b>Record Updated: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['updated']); ?></li>
</ul>
