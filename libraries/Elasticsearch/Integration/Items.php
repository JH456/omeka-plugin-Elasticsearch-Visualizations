<?php

class Elasticsearch_Integration_Items extends Elasticsearch_Integration_BaseIntegration {
    protected $_hooks = array(
        'after_save_item',
        'after_delete_item'
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
        try {
            $elementData = [];
            $elementNames = [];
            foreach ($item->getAllElementTexts() as $elementText) {
                $element = $item->getElementById($elementText->element_id);
                $normalizedName = strtolower(preg_replace('/[^a-zA-Z0-9-_]/', '', $element->name));
                $elementData[$normalizedName] = $elementText->text;
                $elementNames[] = ['displayName' => $element->name, 'name' => $normalizedName];
            }
            $doc->setField('element', $elementData);
            $doc->setField('elements', $elementNames);
        } catch(Omeka_Record_Exception $e) {
            $this->_log("Error loading elements for item {$item->id}. Error: ".$e->getMessage(), Zend_Log::WARN);
        }


        // tags:
        $tags = [];
        foreach ($item->getTags() as $tag) {
            $tags[] = $tag->name;
        }
        $doc->setField('tags', $tags);

        return $doc;
    }

    /**
     * Get array of documents to index.
     *
     * @return array
     */
    public function getItemDocuments() {
        $db = get_db();
        $className = 'Item';
        if(!class_exists($className)) {
            $this->_log("Unable to get documents because $className class does not exist!", Zend_Log::ERR);
            return null;
        }
        $table = $db->getTable($className);
        $select = $table->getSelect();
        $table->applySorting($select, 'id', 'ASC');
        $items = $table->fetchObjects($select);

        $docs = [];
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
}