<?php $text = strip_tags($hit['_source']['text'], '<p><br>'); ?>

<ul>
    <li title="resulttype"><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>
    <?php if(isset($hit['_source']['neatline'])): ?>
        <li title="neatline"><b>Neatline Exhibit:</b> <?php echo $hit['_source']['neatline']; ?></li>
    <?php endif; ?>
    <li title="title"><b>Title: </b> <?php echo $hit['_source']['title']; ?></li>
    <?php if($text): ?>
        <li title="text"><b>Narrative:</b>
            <?php echo Elasticsearch_Utils::truncateText($text, $maxTextLength); ?>
        </li>
    <?php endif; ?>
    <li title="created"><b>Record Created: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['created']); ?></li>
    <li title="updated"><b>Record Updated: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['updated']); ?></li>
</ul>
