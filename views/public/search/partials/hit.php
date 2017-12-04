<div class="elasticsearch-result">
<!-- <?php echo json_encode($hit, JSON_PRETTY_PRINT); ?> -->
    <?php $record =  Elasticsearch_Utils::getRecord($hit); ?>
    <?php $model_view = Inflector::underscore($hit['_source']['model']).".php"; ?>
    <?php $result_url = record_url($record); ?>
    <?php $result_title = !empty($hit['_source']['title']) ? $hit['_source']['title'] : __('Untitled '.$hit['_source']['resulttype']); ?>

    <h3><a href="<?php echo $result_url; ?>" title="<?php echo htmlspecialchars($result_title); ?>"><?php echo $result_title; ?></a></h3>

    <?php
    try {
        echo $this->partial("search/partials/models/$model_view", array(
            'hit' => $hit,
            'record' => $record,
            'maxTextLength' => 500
        ));
    } catch(Zend_View_Exception $e) {
        echo "<!-- Missing view: $model_view -->";
    }
    ?>

    <div class="elasticsearch-result-footer">
        Result Type: <?php echo $hit['_source']['resulttype']; ?> Score: <?php echo $hit['_score']; ?>
    </div>

</div>