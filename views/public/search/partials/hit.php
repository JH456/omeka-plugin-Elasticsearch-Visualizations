<div class="elasticsearch-result">
    <?php $record =  Elasticsearch_Utils::getRecord($hit); ?>
    <?php $result_img = record_image($record, 'thumbnail'); ?>
    <?php $result_url = record_url($record); ?>
    <?php $result_title = !empty($hit['_source']['title']) ? $hit['_source']['title'] : __('Untitled '.$hit['_source']['resulttype']); ?>

    <h3><a href="<?php echo $result_url; ?>"><?php echo $result_title; ?></a></h3>

    <?php if($result_img): ?>
    <div class="elasticsearch-record-image">
        <?php echo $result_img; ?>
    </div>
    <?php endif; ?>

    <?php if(isset($hit['highlight'])): ?>
        <ul class="elasticsearch-highlight">
        <?php foreach($hit['highlight'] as $hl_key => $hl_val): ?>
            <li>
                <span class="elasticsearch-highlight-field"><?php echo implode(' &gt; ', array_map(function($k) { return ucfirst($k); }, explode('.', $hl_key))); ?>:</span>
                <?php echo strip_tags(implode("...", $hl_val), '<p><a><i><b><em>'); ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>