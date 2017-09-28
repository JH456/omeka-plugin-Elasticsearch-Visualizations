<div class="elasticsearch-result">
    <?php $result_url = Elasticsearch_Utils::getDocumentUrl($hit); ?>
    <?php $result_title = !empty($hit['_source']['title']) ? $hit['_source']['title'] : __('Untitled'); ?>

    <h3><a href="<?php echo $result_url; ?>"><?php echo $result_title; ?></a></h3>

    <?php if(isset($hit['highlight'])): ?>
        <ul class="elasticsearch-highlight">
        <?php foreach($hit['highlight'] as $hl_key => $hl_val): ?>
            <li>
                <span class="elasticsearch-highlight-field"><?php echo implode(' &gt; ', array_map(function($k) { return ucfirst($k); }, explode('.', $hl_key))); ?>:</span>
                <?php echo implode("&#8230;", $hl_val); ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>