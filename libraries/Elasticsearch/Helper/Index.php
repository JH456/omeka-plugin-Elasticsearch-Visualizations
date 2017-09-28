<?php

/**
 * Helper class that does the work of indexing site content.
 */
class Elasticsearch_Helper_Index {

    /**
     * Indexes all content.
     *
     * Types of documents to index include things that can be uniquely referenced via URL. That includes:
     *      - Items
     *      - TODO: Collections
     *      - TODO: Exhibits (addon)
     *      - TODO: Simplepages (addon)
     *
     * @param $docIndex
     * @return void
     */
	public static function indexAll($docIndex) {
        $docs = self::getItemDocuments($docIndex);
        Elasticsearch_Document::bulkIndex($docs);
    }

    /**
     * Indexes a single Item record.
     *
     * @param $docIndex
     * @param $item
     * @return array
     */
    public static function indexItem($docIndex, $item) {
	    $doc = self::getItemDocument($docIndex, $item);
	    return $doc->index();
    }

    /**
     * Get array of documents to index.
     *
     * @param $docIndex
     * @return array of Elasticsearch_Document objects
     */
    public static function getItemDocuments($docIndex) {
        $db = get_db();
        $table = $db->getTable('Item');
        $select = $table->getSelect();
        $table->applySorting($select, 'id', 'ASC');
        $items = $table->fetchObjects($select);

        $docs = [];
        foreach($items as $item) {
            $docs[] = self::getItemDocument($docIndex, $item);
        }

        return $docs;
    }

    /**
     * Returns an item as a document.
     *
     * @param $docIndex
     * @param $item
     * @return Elasticsearch_Document
     */
    public static function getItemDocument($docIndex, $item) {
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
        foreach($item->getAllElementTexts() as $elementText) {
            $element = $item->getElementById($elementText->element_id);
            $elements[$element->name] = $elementText->text;
        }
        $doc->setField('elements', $elements);

        // tags:
        $tags = [];
        foreach ($item->getTags() as $tag) {
            $tags[] = $tag->name;
        }
        $doc->setField('tags', $tags);

        return $doc;
    }

    /**
     * Deletes all items in the elasticsearch index.
     */
    public static function deleteAll($docIndex) {
        // Just delete the whole index... assumes that index auto-creation is enabled
        if(self::client()->indices()->exists(['index' => $docIndex])) {
            self::client()->indices()->delete(['index' => $docIndex]);
        }
    }

    /**
     * Deletes an item from the index.
     *
     * @param $docIndex
     * @param $item
     */
    public static function deleteItem($docIndex, $item) {
        $doc = new Elasticsearch_Document($docIndex, 'item', $item->id);
        self::client()->delete($doc->getParams());
    }

    /**
     * Pings the elasticsearch server to see if it is available or not.
     *
     * @return bool True if the server responded to the ping, false otherwise.
     */
    public static function ping() {
        return self::client()->ping();
    }

    /**
     * Returns the elasticsearch client.
     *
     * @return \Elasticsearch\Client
     */
    public static function client() {
	    return Elasticsearch\ClientBuilder::create()->build();
    }
}
