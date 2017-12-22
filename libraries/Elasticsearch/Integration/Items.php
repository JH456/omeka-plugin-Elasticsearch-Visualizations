<?php

class Elasticsearch_Integration_Items extends Elasticsearch_Integration_BaseIntegration {
    protected $_hooks = array(
        'after_save_item',
        'after_save_file',
        'after_delete_item',
        'after_delete_file',
    );

    /**
     * Hook for when an item is being saved.
     * Indexes item in the elasticsearch cluster.
     *
     * @param array $args
     */
    public function hookAfterSaveItem($args) {
        $this->_log("hookAfterSaveItem: {$args['record']->id}");
        $this->indexItem($args['record']);
    }

    /**
     * Hook for when an item is being deleted.
     * Removes item from the elasticsearch index.
     *
     * @param array $args
     */
    public function hookAfterDeleteItem($args) {
        $this->_log("deleting item from index: {$args['record']->id}");
        $this->deleteItem($args['record']);
    }

    /**
     * Hook for when a file is being saved.
     * Update the indexed item document.
     *
     * @param array $args
     */
    public function hookAfterSaveFile($args) {
        $this->_log("hookAfterSaveFile: {$args['record']->id}");
        $file = $args['record'];
        if($item = $file->getItem()) {
            $this->indexItem($item);
        }
    }

    /**
     * Hook for when a file is being deleted.
     * Update the indexed item document.
     *
     * @param array $args
     */
    public function hookAfterDeleteFile($args) {
        $this->_log("hookAfterDeleteFile: {$args['record']->id}");
        $file = $args['record'];
        if($item = $file->getItem()) {
            $this->indexItem($item);
        }
    }

    /**
     * Indexes a single Item record.
     *
     * @param $item
     * @return array
     */
    public function indexItem($item) {
        $doc = $this->getItemDocument($item);
        return $doc->index();
    }

    /**
     * Deletes an item from the index.
     *
     * @param $item
     */
    public function deleteItem($item) {
        $doc = new Elasticsearch_Document($this->_docIndex, "item_{$item->id}");
        return $doc->delete();
    }

    /**
     * Returns an item as a document.
     *
     * @param $item
     * @return Elasticsearch_Document
     */
    public function getItemDocument($item) {
        $doc = new Elasticsearch_Document($this->_docIndex, "item_{$item->id}");
        $doc->setFields([
            'resulttype'=> 'Item',
            'model'     => 'Item',
            'modelid'   => $item->id,
            'featured'  => (bool) $item->featured,
            'public'    => (bool) $item->public,
            'created'   => $this->_getDate($item->added),
            'updated'   => $this->_getDate($item->modified),
            'title'     => metadata($item, array('Dublin Core', 'Title'))
        ]);

        // collection:
        if ($collection = $item->getCollection()) {
            $doc->setField('collection', metadata($collection, array('Dublin Core', 'Title')));
        }

        // item type:
        if ($itemType = $item->getItemType()) {
            $doc->setField('itemtype', $itemType->name);
        }

        // elements:
        $itemElementTexts = $this->_getElementTexts($item);
        $doc->setField('elements', $itemElementTexts['names']);
        $doc->setField('element', $itemElementTexts['data']);

        // tags:
        $tags = [];
        foreach ($item->getTags() as $tag) {
            $tags[] = $tag->name;
        }
        $doc->setField('tags', $tags);

        // files:
        $files = [];
        if($itemFiles = $item->getFiles()) {
            foreach($itemFiles as $itemFile) {
                $fileElementTexts = $this->_getElementTexts($itemFile);
                $files[] = [
                    'id'      => $itemFile->id,
                    'title'   => $itemFile->getProperty('display_title'),
                    'element' => $fileElementTexts['data']
                ];
            }
        }
        $doc->setField('files', $files);

        return $doc;
    }

    /**
     * Get array of documents to index.
     *
     * @return array
     */
    public function getItemDocuments() {
        $docs = [];
        $items = $this->_fetchObjects('Item');
        foreach($items as $item) {
            $docs[] = $this->getItemDocument($item);
        }

        return $docs;
    }

    /**
     * Index all items.
     */
    public function indexAll() {
        $docs = $this->getItemDocuments();
        if(isset($docs)) {
            $this->_log('indexAll items: '.count($docs));
            Elasticsearch_Document::bulkIndex($docs);
        }
    }

    /**
     * Deletes all items from the index.
     */
    public function deleteAll() {
        $this->_deleteByQueryModel('Item');
    }

    /**
     * Helper function to extract element texts from a record.
     *
     * @param $record
     * @return array
     */
    protected function _getElementTexts($record, $options=array()) {
        $normalize = isset($options['normalize']) ? (bool) $options['normalize'] : true;
        $elementData = [];
        $elementNames = [];
        try {
            foreach ($record->getAllElementTexts() as $elementText) {
                $element = $record->getElementById($elementText->element_id);
                if($normalize) {
                    $normalizedName = strtolower(preg_replace('/[^a-zA-Z0-9-_]/', '', $element->name));
                } else {
                    $normalizedName = $element->name;
                }
                $elementData[$normalizedName] = $elementText->text;
                $elementNames[] = ['displayName' => $element->name, 'name' => $normalizedName];
            }
        } catch(Omeka_Record_Exception $e) {
            $this->_log("Error loading elements for record {$record->id}. Error: ".$e->getMessage(), Zend_Log::WARN);
        }
        return array('names' => $elementNames, 'data' => $elementData);
    }
}