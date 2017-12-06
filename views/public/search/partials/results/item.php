<?php $result_img = record_image($record, 'thumbnail', array('class' => 'elasticsearch-result-image')); ?>
<?php if($result_img): ?>
    <a href="<?php echo $record_url; ?>"><?php echo $result_img; ?></a>
<?php endif; ?>

<ul>
    <li><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>

<?php if(isset($hit['_source']['itemType'])): ?>
    <li><b>Item Type:</b> <?php echo html_escape($hit['_source']['itemType']); ?></li>
<?php endif; ?>

<?php if(isset($hit['_source']['collection'])): ?>
    <li><b>Collection:</b> <?php echo html_escape($hit['_source']['collection']); ?></li>
<?php endif; ?>

<?php foreach($hit['_source']['elements'] as $element): ?>
    <li><b><?php echo html_escape($element['name']);?>:</b> <?php echo html_escape($element['text']); ?></li>
<?php endforeach; ?>

<?php if(isset($hit['_source']['tags']) && count($hit['_source']['tags']) > 0): ?>
    <li><b>Tags:</b>  <?php echo html_escape(implode(", ", $hit['_source']['tags'])); ?></li>
<?php endif; ?>

    <li><b>Created: </b> <?php echo html_escape(Elasticsearch_Utils::formatDate($hit['_source']['created'])); ?></li>
    <li><b>Updated: </b> <?php echo html_escape(Elasticsearch_Utils::formatDate($hit['_source']['updated'])); ?></li>
</ul>

