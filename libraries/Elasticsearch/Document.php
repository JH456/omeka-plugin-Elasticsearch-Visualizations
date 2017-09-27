<?php

class Elasticsearch_Document {
    protected $_index;
    protected $_type;
    protected $_body;

    public function __construct($index, $type, $id=null) {
        $this->_index = $index;
        $this->_type = $type;
        $this->_id = $id;
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
            'body' => $this->_body
        ];
        if(isset($this->_id)) {
            $params['id'] = $this->_id;
        }
        return $params;
    }

    public function index() {
        return self::client()->index($this->getParams());
    }

    public static function bulkIndex(array $docs) {
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

        return self::client()->bulk($params);
    }

    public static function client() {
        return Elasticsearch\ClientBuilder::create()->build();
    }
}