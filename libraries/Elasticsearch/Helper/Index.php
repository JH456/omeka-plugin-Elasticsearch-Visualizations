<?php

/**
 * Helper class that does the work of indexing site content.
 */
class Elasticsearch_Helper_Index {

    /**
     * @var \Elasticsearch\Client|null
     */
    protected $_client = null;

    /**
     * @var string Holds the elasticsearch index name
     */
    protected $_index = null;

    public function __construct($index) {
        $this->_index = $index;
        $this->_client = self::getClient();
    }

	public static function indexAll($index) {
        $helper = new self($index);
    }

    public static function deleteAll($index) {
        $helper = new self($index);
    }

    public static function ping() {
        return self::getClient()->ping();
    }

    public static function getClient() {
	    return Elasticsearch\ClientBuilder::create()->build();
    }
}
