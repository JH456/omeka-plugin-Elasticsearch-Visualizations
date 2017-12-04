<?php $pageText = strip_tags($hit['_source']['text'], '<p><br>'); ?>
<div class="elasticsearch-result-text">
    <?php echo Elasticsearch_Utils::truncateText($pageText, $maxTextLength); ?>
</div>