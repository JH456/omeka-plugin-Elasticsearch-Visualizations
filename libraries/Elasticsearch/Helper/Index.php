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
     * @return void
     */
    public static function indexAll() {
        $docs = self::getItemDocuments();
        Elasticsearch_Document::bulkIndex($docs);
    }

    /**
     * Indexes a single Item record.
     *
     * @param $item
     * @return array
     */
    public static function indexItem($item) {
	    $doc = self::getItemDocument($item);
	    return $doc->index();
    }

    /**
     * Creates an index and initializes mappings.
     *
     * @return void
     */
    public static function createIndex() {
        $docIndex = Elasticsearch_Config::index();
        $params = [
            'index' => $docIndex,
            'body' => [
                'mappings' => [
                    'item' => self::getItemMapping()
                ]
            ]
        ];
        return self::client()->indices()->create($params);
    }


    /**
     * Get array of documents to index.
     *
     * @return array of Elasticsearch_Document objects
     */
    public static function getItemDocuments() {
        $db = get_db();
        $table = $db->getTable('Item');
        $select = $table->getSelect();
        $table->applySorting($select, 'id', 'ASC');
        $items = $table->fetchObjects($select);

        $docs = [];
        foreach($items as $item) {
            $docs[] = self::getItemDocument($item);
        }

        return $docs;
    }

    /**
     * Returns an item as a document.
     *
     * @param $item
     * @return Elasticsearch_Document
     */
    public static function getItemDocument($item) {
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
     * Returns the mapping for item type documents.
     *
     * @return array
     */
    public static function getItemMapping() {
        return [
            'properties' => [
                'title' => [
                    'type' => 'text'
                ],
                'collection' => [
                    'type' => 'text'
                ],
                'elements' => [
                    'type' => 'text'
                ],
                'tags' => [
                    'type' => 'text',
                    'position_increment_gap' => 100,
                    'fields' => [
                        'raw' => [
                            'type' => 'keyword'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Executes a search query on an index.
     *
     * TODO: aggregations/faceting
     *
     * @param $query
     * @param $options
     * @return array
     */
    public static function search($options) {
        $docIndex = Elasticsearch_Config::index();
        if(!isset($options['query']) || !is_array($options['query'])) {
            throw new Exception("Query parameter is required to execute elasticsearch query.");
        }
        $offset = isset($options['offset']) ? $options['offset'] : 0;
        $limit = isset($options['limit']) ? $options['limit'] : 20;
        $showNotPublic = isset($options['showNotPublic']) ? $options['showNotPublic'] : false;
        $terms = isset($options['query']['q']) ? $options['query']['q'] : '';
        $facets = isset($options['query']['facets']) ? $options['query']['facets'] : [];

        // Main body of query
        $body = [
            'query' => [
                'bool' => [],
            ],
            'highlight' => [
                'pre_tags' => ['<em>'],
                'post_tags' => ['</em>'],
                'order' => 'score',
                'fields' => [
                    'collection' => new \stdClass(),
                    'elements.*' => ['fragment_size' => 400, 'number_of_fragments' => 3],
                    'tags.*'     => new \stdClass()
                ]
            ],
            'aggregations' => [
                'tags' => [
                    'terms' => [
                        'field' => 'tags.keyword'
                    ]
                ],
                'collection' => [
                    'terms' => [
                        'field' => 'collection.keyword'
                    ]
                ],
                'itemType' => [
                    'terms' => [
                        'field' => 'itemType.keyword'
                    ]
                ]
            ]
        ];

        // Add must query
        if(empty($terms)) {
            $must_query = [
                'match_all' => new \stdClass()
            ];
        } else {
            $must_query = [
                'multi_match' => [
                    'query' => $terms,
                    'fields' => ['title', 'collection', 'itemType', 'elements*', 'tags*'],
                    'type' => 'cross_fields',
                    'operator' => 'and'
                ]
            ];
        }
        $body['query']['bool']['must'] = $must_query;

            // Add filters
        $filters = [];
        if(!$showNotPublic) {
            $filters[] = ['term' => ['public' => true]];
        }
        if(isset($facets['tags'])) {
            $filters[] = ['terms' => ['tags.keyword' => $facets['tags']]];
        }
        if(isset($facets['collection'])) {
            $filters[] = ['term' => ['collection.keyword' => $facets['collection']]];
        }
        if(isset($facets['itemType'])) {
            $filters[] = ['term' => ['itemType.keyword' => $facets['itemType']]];
        }
        if(count($filters) > 0) {
            $body['query']['bool']['filter'] = $filters;
        }

        $params = [
            'index' => $docIndex,
            'from' => $offset,
            'size' => $limit,
            'body' => $body
        ];
        error_log("elasticsearch search params: ".var_export($params['body']['query'],1));

        return self::client()->search($params);
    }

    /**
     * Deletes all items in the elasticsearch index.
     *
     * Assumes that index auto-creation is enabled so that when items are re-indexed,
     * the index will be created automatically.
     */
    public static function deleteAll() {
        $docIndex = Elasticsearch_Config::index();
        $params = ['index' => $docIndex];
        if(self::client(['nobody' => true])->indices()->exists($params)) {
            self::client()->indices()->delete($params);
        }
    }

    /**
     * Deletes an item from the index.
     *
     * @param $item
     */
    public static function deleteItem($item) {
        $docIndex = Elasticsearch_Config::index();
        $doc = new Elasticsearch_Document($docIndex, 'item', $item->id);
        self::client()->delete($doc->getParams());
    }

    /**
     * Pings the elasticsearch server to see if it is available or not.
     *
     * @return bool True if the server responded to the ping, false otherwise.
     */
    public static function ping() {
        return self::client(['nobody' => true])->ping();
    }

    /**
     * Returns the elasticsearch client.
     *
     * @return \Elasticsearch\Client
     */
    public static function client(array $options = array()) {
        return Elasticsearch_Client::create($options);
    }
}
