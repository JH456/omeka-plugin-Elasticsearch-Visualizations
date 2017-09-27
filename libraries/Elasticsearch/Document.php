<?php

/**
 * Class Elasticsearch_Document
 *
 * This class encapsulates a document to be indexed in Elasticsearch. Documents can be indexed individually, or in
 * bulk using the static methods. At a minimum, each document must specify the following fields:
 *
 * - index: the name of the elasticsearch index to store the document
 * - type: the document type
 * - body: the fields that compose the document
 * - id (optional): the document identifier, otherwise one will be generated
 *
 * Depends on Elasticsearch\ClientBuilder to create and submit the REST requests to the elasticsearch cluster.
 */
class Elasticsearch_Document {
    protected $_index;
    protected $_type;
    protected $_body;

    public function __construct($docIndex, $docType, $docId=null) {
        $this->_index = $docIndex;
        $this->_type = $docType;
        $this->_id = "{$docType}_{$docId}";
        $this->_body = [];
    }

    public function setFields(array $params = array()) {
        $this->_body = array_merge($this->_body, $params);
    }

    public function setField($key, $value) {
        $this->_body[$key] = $value;
    }

    public function getParams() {
        $params = [
            'index' => $this->_index,
            'type' => $this->_type,
        ];
        if(isset($this->_id)) {
            $params['id'] = $this->_id;
        }
        if(!empty($this->_body)) {
            $params['body'] = $this->_body;
        }
        return $params;
    }

    public function index() {
        return self::client()->index($this->getParams());
    }

    public static function getBulkParams(array $docs) {
        $params = ['body' => []];
        foreach($docs as $doc) {
            $action_and_metadata = [
                'index' => [
                    '_index' => $doc->_index,
                    '_type'  => $doc->_type,
                ]
            ];
            if(isset($doc->_id)) {
                $action_and_metadata['index']['_id'] = $doc->_id;
            }
            $params['body'][] = $action_and_metadata;
            $params['body'][] = $doc->_body;
        }
        return $params;
    }

    public static function bulkIndex(array $docs) {
        $params = self::getBulkParams($docs);
        return self::client()->bulk($params);
    }

    public static function client() {
        return Elasticsearch\ClientBuilder::create()->build();
    }
}