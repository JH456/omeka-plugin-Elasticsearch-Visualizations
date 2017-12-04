<?php $pageText = strip_tags($hit['_source']['text'], '<p><br>'); ?>

<ul>
    <li><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>
    <li><b>Text:</b>
        <?php echo Elasticsearch_Utils::truncateText($pageText, $maxTextLength); ?>
    </li>
</ul>
