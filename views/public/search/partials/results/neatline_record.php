<?php $text = strip_tags($hit['_source']['text'], '<p><br>'); ?>

<ul>
    <li data-field="resulttype"><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>
    <li data-field="resulttype"><b>Title: </b>: <?php echo $hit['_source']['title']; ?></li>
    <?php if($text): ?>
        <li data-field="text"><b>Body:</b>
            <?php echo Elasticsearch_Utils::truncateText($text, $maxTextLength); ?>
        </li>
    <?php endif; ?>
    <li data-field="created"><b>Record Created: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['created']); ?></li>
    <li data-field="updated"><b>Record Updated: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['updated']); ?></li>
</ul>
