<?php $result_img = record_image($record, 'thumbnail', array('class' => 'elasticsearch-result-image')); ?>
<?php if($result_img): ?>
    <a href="<?php echo $record_url; ?>"><?php echo $result_img; ?></a>
<?php endif; ?>

<ul>
    <li data-field="resulttype"><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>

<?php if(isset($hit['_source']['itemType'])): ?>
    <li data-field="itemType"><b>Item Type:</b> <?php echo $hit['_source']['itemType']; ?></li>
<?php endif; ?>

<?php if(isset($hit['_source']['collection'])): ?>
    <li data-field="collection"><b>Collection:</b> <?php echo $hit['_source']['collection']; ?></li>
<?php endif; ?>

<?php foreach($hit['_source']['elements'] as $elementName): ?>
    <?php if(array_key_exists($elementName, $hit['_source']['element'])): ?>
        <li data-field="<?php echo "element.$elementName"; ?>"><b><?php echo $elementName; ?>:</b> <?php echo $hit['_source']['element'][$elementName]; ?></li>
    <?php endif; ?>
<?php endforeach; ?>

<?php if(isset($hit['_source']['tags']) && count($hit['_source']['tags']) > 0): ?>
    <li data-field="tags"><b>Tags:</b>  <?php echo implode(", ", $hit['_source']['tags']); ?></li>
<?php endif; ?>

    <li data-field="created"><b>Record Created: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['created']); ?></li>
    <li data-field="updated"><b>Record Updated: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['updated']); ?></li>
</ul>

