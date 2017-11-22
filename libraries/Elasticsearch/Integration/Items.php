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
        $this->_log("adding item to index: {$args['record']->id}");
        $this->indexRecord($args['record']);
    }

    /**
     * Hook for when an item is being deleted.
     * Removes item from the elasticsearch index.
     *
     * @param array $args
     */
    public function hookAfterDeleteItem($args) {
        $this->_log("deleting item from index: {$args['record']->id}");
        $this->deleteRecord($args['record']);
    }

    /**
     * Indexes a single Item record.
     *
     * @param $item
     * @return array
     */
    public function indexRecord($item) {
        $doc = $this->getDocument($item);
        return $doc->index();
    }

    /**
     * Deletes an item from the index.
     *
     * @param $item
     */
    public function deleteRecord($item) {
        $docIndex = Elasticsearch_Config::index();
        $doc = new Elasticsearch_Document($docIndex, 'item', $item->id);
        return $doc->delete();
    }

    /**
     * Returns an item as a document.
     *
     * @param $item
     * @return Elasticsearch_Document
     */
    public function getDocument($item) {
        $docIndex = Elasticsearch_Config::index();
        $doc = new Elasticsearch_Document($docIndex, 'item', $item->id);
        $doc->setFields([
            'model'     => 'Item',
            'modelid'   => $item->id,
            'featured'  => (bool) $item->featured,
            'public'    => (bool) $item->public,
            'resulttype'=> 'Item'
        ]);


        // title:
        $title = metadata($item, array('Dublin Core', 'Title'));
        $doc->setField('title', $title);

        // collection:
        if ($collection = $item->getCollection()) {
            $doc->setField('collection', metadata($collection, array('Dublin Core', 'Title')));
        }

        // item type:
        if ($itemType = $item->getItemType()) {
            $doc->setField('itemType', $itemType->name);
        }

        // elements:
        $elements = [];
        try {
            foreach ($item->getAllElementTexts() as $elementText) {
                $element = $item->getElementById($elementText->element_id);
                $elements[$element->name] = $elementText->text;
            }
        } catch(Omeka_Record_Exception $e) {
            $this->_log("Error loading elements for item {$item->id}. Error: ".$e->getMessage(), Zend_Log::WARN);
        }
        $doc->setField('elements', $elements);

        // tags:
        $tags = [];
        try {
            foreach ($item->tags as $tag) {
                $tags[] = $tag->name;
            }
        } catch(Omeka_Record_Exception $e) {
            $this->_log("Error loading tags for item {$item->id}. Error: ".$e->getMessage(), Zend_Log::WARN);
        }
        $doc->setField('tags', $tags);

        return $doc;
    }

    /**
     * Get array of documents to index.
     *
     * @return array
     */
    public function getDocuments() {
        $db = get_db();
        $table = $db->getTable('Item');
        $select = $table->getSelect();
        $table->applySorting($select, 'id', 'ASC');
        $items = $table->fetchObjects($select);

        $docs = [];
        foreach($items as $item) {
            $docs[] = $this->getDocument($item);
        }

        return $docs;
    }
}