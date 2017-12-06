<?php $pageText = strip_tags($hit['_source']['text'], '<p><br>'); ?>

<ul>
    <li><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>
    <li><b>Text:</b>
        <?php echo html_escape(Elasticsearch_Utils::truncateText($pageText, $maxTextLength)); ?>
    </li>
    <li><b>Created: </b> <?php echo html_escape(Elasticsearch_Utils::formatDate($hit['_source']['created'])); ?></li>
    <li><b>Updated: </b> <?php echo html_escape(Elasticsearch_Utils::formatDate($hit['_source']['updated'])); ?></li>
</ul>
