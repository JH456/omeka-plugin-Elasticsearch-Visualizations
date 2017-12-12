<?php $result_img = record_image($record, 'thumbnail', array('class' => 'elasticsearch-result-image')); ?>
<?php if($result_img): ?>
    <a href="<?php echo $record_url; ?>"><?php echo $result_img; ?></a>
<?php endif; ?>

<ul>
    <li data-field="resulttype"><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>

<?php if(isset($hit['_source']['itemtype'])): ?>
    <li data-field="itemtype"><b>Item Type:</b> <?php echo $hit['_source']['itemtype']; ?></li>
<?php endif; ?>

<?php if(isset($hit['_source']['collection'])): ?>
    <li data-field="collection"><b>Collection:</b> <?php echo $hit['_source']['collection']; ?></li>
<?php endif; ?>

<?php foreach($hit['_source']['elements'] as $element): ?>
        <li data-field="elements.name,elements.text"><b><?php echo $element['name']; ?>:</b> <?php echo $element['text']; ?></li>
<?php endforeach; ?>

<?php if(isset($hit['_source']['tags']) && count($hit['_source']['tags']) > 0): ?>
    <li data-field="tags"><b>Tags:</b>  <?php echo implode(", ", $hit['_source']['tags']); ?></li>
<?php endif; ?>

    <li data-field="created"><b>Record Created: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['created']); ?></li>
    <li data-field="updated"><b>Record Updated: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['updated']); ?></li>
</ul>

