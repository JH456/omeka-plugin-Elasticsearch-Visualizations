<?php $result_img = record_image($record, 'thumbnail', array('class' => 'elasticsearch-result-image')); ?>
<?php if($result_img): ?>
    <a href="<?php echo $record_url; ?>"><?php echo $result_img; ?></a>
<?php endif; ?>

<ul>
    <li><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>

<?php if(isset($hit['_source']['itemType'])): ?>
    <li><b>Item Type:</b> <?php echo $hit['_source']['itemType']; ?></li>
<?php endif; ?>

<?php if(isset($hit['_source']['collection'])): ?>
    <li><b>Collection:</b> <?php echo $hit['_source']['collection']; ?></li>
<?php endif; ?>

<?php foreach($hit['_source']['elements'] as $element): ?>
    <li><b><?php echo $element['name'];?>:</b> <?php echo $element['text']; ?></li>
<?php endforeach; ?>

<?php if(isset($hit['_source']['tags']) && count($hit['_source']['tags']) > 0): ?>
    <li><b>Tags:</b>  <?php echo implode(", ", $hit['_source']['tags']); ?></li>
<?php endif; ?>
</ul>

