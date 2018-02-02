<?php $result_img = record_image($record, 'thumbnail', array('class' => 'elasticsearch-result-image')); ?>
<?php if($result_img): ?>
    <a href="<?php echo $record_url; ?>"><?php echo $result_img; ?></a>
<?php endif; ?>

<ul>
    <li title="resulttype"><b>Result Type:</b> <?php echo $hit['_source']['resulttype']; ?></li>

<?php if(isset($hit['_source']['itemtype'])): ?>
    <li title="itemtype"><b>Item Type:</b> <?php echo $hit['_source']['itemtype']; ?></li>
<?php endif; ?>

<?php if(isset($hit['_source']['collection'])): ?>
    <li title="collection"><b>Collection:</b> <?php echo $hit['_source']['collection']; ?></li>
<?php endif; ?>

<?php if(isset($hit['_source']['elements']) && isset($hit['_source']['element'])): ?>
    <?php $elementText = $hit['_source']['element']; ?>
    <?php $elementNames = $hit['_source']['elements']; ?>
    <?php foreach($elementNames as $elementName): ?>
        <?php if(isset($elementText[$elementName['name']])): ?>
            <li title="element.<?php echo $elementName['name']; ?>">
                <b><?php echo $elementName['displayName']; ?>:</b> 
		<?php 
                    $text = $elementText[$elementName['name']];
                    $truncated = Elasticsearch_Utils::truncateText($text, $maxTextLength);
                ?>
                <pre><?php echo $truncated?><pre>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php if(isset($hit['_source']['tags']) && count($hit['_source']['tags']) > 0): ?>
    <li title="tags"><b>Tags:</b>  <?php echo implode(", ", $hit['_source']['tags']); ?></li>
<?php endif; ?>

    <li title="created"><b>Record Created: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['created']); ?></li>
    <li title="updated"><b>Record Updated: </b> <?php echo Elasticsearch_Utils::formatDate($hit['_source']['updated']); ?></li>
</ul>

