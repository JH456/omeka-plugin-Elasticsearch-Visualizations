<?php

/**
 * Helper class that does the work of indexing site content.
 */
class Elasticsearch_Helper_Index {

    /**
     * Indexes all items and integrated addons such as exhibits and simple pages.
     *
     * @return void
     */
    public static function indexAll() {
        try {
            $docIndex = Elasticsearch_Config::index();
            $integrationMgr = new Elasticsearch_IntegrationManager($docIndex);
            $integrationMgr->indexAll();
        } catch(Exception $e) {
            error_log($e, Zend_Log::ERR);
        }
    }

    /**
     * Creates an index.
     *
     * Use this to initialize mappings and other settings on the index.
     *
     * @return void
     */
    public static function createIndex() {
        $docIndex = Elasticsearch_Config::index();
        $params = [
            'index' => $docIndex,
            'body' => [
                'settings' => [],
                'mappings' => self::getMappings()
            ]
        ];
        return self::client()->indices()->create($params);
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

    /**
     * Returns the most recent jobs related to reindexing the site.
     *
     * @return array
     */
    public static function getReindexJobs(array $options=array()) {
        $limit = isset($options['limit']) ? $options['limit'] : 25;
        $order = isset($options['order']) ? $options['order'] : 'id desc';
        $table = get_db()->getTable('Process');
        $select = $table->getSelect()->limit($limit)->order($order);
        $job_objects = $table->fetchObjects($select);

        $reindex_jobs = array();
        foreach($job_objects as $job_object) {
            // Because job args are serialized to a string using some combination of PHP serialize() and json_encode(),
            // just do a simple string search rather than try to deal with that.
            if(!empty($job_object->args) && strrpos($job_object->args, 'Elasticsearch_Job_Reindex') !== FALSE) {
                $reindex_jobs[] = $job_object;
            }
        }

        return $reindex_jobs;
    }

    /**
     * This function defines the field mapping used in the elasticsearch index.
     *
     * The mapping defines fields common to all types of documents, as well
     * as fields specific to certain types of integrations (e.g. items, exhibits, etc).
     *
     * Integration-specific fields should be mentioned in the comments below.
     *
     * @return array
     */
    public static function getMappings() {
        $mappings = [
            'doc' => [
                'properties' => [
                    // Common Mappings
                    'resulttype'  => ['type' => 'keyword'],
                    'title'       => ['type' => 'text'],
                    'description' => ['type' => 'text'],
                    'text'        => ['type' => 'text'],
                    'model'       => ['type' => 'keyword'],
                    'modelid'     => ['type' => 'integer'],
                    'featured'    => ['type' => 'boolean'],
                    'public'      => ['type' => 'boolean'],
                    'created'     => ['type' => 'date'],
                    'updated'     => ['type' => 'date'],
                    'tags'        => ['type' => 'keyword'],
                    'slug'        => ['type' => 'keyword'],

                    // Item-Specific
                    'collection' => ['type' => 'text'],
                    'itemtype'   => ['type' => 'keyword'],
                    'elements'   => [
                        'type' => 'nested',
                        'properties' => [
                            'name' => ['type' => 'keyword'],
                            'text' => ['type' => 'text']
                        ]
                    ],

                    // Exhibit-Specific
                    'credits' => ['type' => 'text'],
                    'exhibit' => ['type' => 'text'],
                    'blocks' => [
                        'type' => 'nested',
                        'properties' => [
                            'text'        => ['type' => 'text'],
                            'attachments' => ['type' => 'text']
                        ]
                    ]
                ]
            ]
        ];
        return $mappings;
    }

    /**
     * Returns aggregations that should be returned for every search query.
     *
     * @return array
     */
    public static function getAggregations() {
        $aggregations = [
            'resulttype' => [
                'terms' => [
                    'field' => 'resulttype.keyword',
                    'size' => 10
                ]
            ],
            'itemtype' => [
                'terms' => [
                    'field' => 'itemtype.keyword',
                    'size' => 10
                ]
            ],
            'tags' => [
                'terms' => [
                    'field' => 'tags.keyword',
                    'size' => 10,
                    'missing' => 'N/A'
                ]
            ],
            'collection' => [
                'terms' => [
                    'field' => 'collection.keyword',
                    'size' => 10
                ]
            ],
            'exhibit' => [
                'terms' => [
                    'field' => 'exhibit.keyword',
                    'size' => 10
                ]
            ],
            'featured' => [
                'terms' => [
                    'field' => 'featured',
                ]
            ],
            'public' => [
                'terms' => [
                    'field' => 'public',
                ]
            ]
        ];
        return $aggregations;
    }

    /**
     * Returns display labels for aggregation keys (e.g. "Result Type" for "resulttype").
     *
     * @return array
     */
    public static function getAggregationLabels() {
        $aggregation_labels = array(
            'resulttype' => 'Result Types',
            'itemtype'   => 'Item Types',
            'collection' => 'Collections',
            'exhibit'    => 'Exhibits',
            'tags'       => 'Tags'
        );
        return $aggregation_labels;
    }

    /**
     * Given an array of key/value pairs defining the facets of the search that the
     * user would like to drill down into, this function returns an array of filters
     * that can be used in an elasticsearch query to narrow the search results.
     *
     * @param $facets
     * @return array
     */
    public static function getFacetFilters($facets) {
        $filters = array();
        if(isset($facets['tags'])) {
            $filters[] = ['terms' => ['tags.keyword' => $facets['tags']]];
        }
        if(isset($facets['collection'])) {
            $filters[] = ['term' => ['collection.keyword' => $facets['collection']]];
        }
        if(isset($facets['exhibit'])) {
            $filters[] = ['term' => ['exhibit.keyword' => $facets['exhibit']]];
        }
        if(isset($facets['itemtype'])) {
            $filters[] = ['term' => ['itemtype.keyword' => $facets['itemtype']]];
        }
        if(isset($facets['resulttype'])) {
            $filters[] = ['term' => ['resulttype.keyword' => $facets['resulttype']]];
        }
        if(isset($facets['featured'])) {
            $filters[] = ['term' => ['featured' => $facets['featured']]];
        }
        return $filters;
    }

    /**
     * Executes a search query on an index
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
            'query' => ['bool' => []],
            'aggregations' => self::getAggregations()
        ];

        // Add must query
        if(empty($terms)) {
            $must_query = ['match_all' => new \stdClass()];
        } else {
            $must_query = [
                'query_string' => [
                    'query' => $terms,
                    'default_field' => '_all',
                    'default_operator' => 'OR'
                ]
            ];
        }
        $body['query']['bool']['must'] = $must_query;

        // Add filters
        $filters = self::getFacetFilters($facets);
        if(!$showNotPublic) {
            $filters = array_merge($filters, ['term' => ['public' => true]]);
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
}
