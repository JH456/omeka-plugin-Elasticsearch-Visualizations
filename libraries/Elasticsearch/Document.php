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
    public $id = null;
    public $index = '';
    public $type = '';
    public $body = [];

    public function __construct($docIndex, $docType, $docId=null) {
        $this->index = $docIndex;
        $this->type = $docType;
        if($docId) {
            $this->id = "{$docType}_{$docId}";
        }
    }

    public function setFields(array $params = array()) {
        $this->body = array_merge($this->body, $params);
    }

    public function setField($key, $value) {
        $this->body[$key] = $value;
    }

    /**
     * Returns the params to index a single item.
     *
     * @return array
     */
    public function getParams() {
        $params = [
            'index' => $this->index,
            'type' => $this->type,
        ];
        if(isset($this->id)) {
            $params['id'] = $this->id;
        }
        if(!empty($this->body)) {
            $params['body'] = $this->body;
        }
        return $params;
    }

    /**
     * Indexes the document.
     *
     * @return client response
     */
    public function index() {
        $client = Elasticsearch_Client::create();
        return $client->index($this->getParams());
    }

    /**
     * Returns the params to bulk index an array of documents.
     *
     * @param array $docs
     * @param int $offset
     * @param int|null $length
     * @return array
     * @throws Exception
     */
    public static function getBulkParams(array $docs, int $offset=0, int $length=null) {
        if($offset < 0 || $length < 0) {
            throw new Exception("offset less than zero");
        }

        if(isset($length)) {
            if($offset + $length > count($docs)) {
                $end = count($docs);
            } else {
                $end = $offset + $length;
            }
        } else {
            $end = count($docs);
        }

        $params = ['body' => []];
        for($i = $offset; $i < $end; $i++) {
            $doc = $docs[$i];
            $action_and_metadata = [
                'index' => [
                    '_index' => $doc->index,
                    '_type'  => $doc->type,
                ]
            ];
            if(isset($doc->id)) {
                $action_and_metadata['index']['_id'] = $doc->id;
            }
            $params['body'][] = $action_and_metadata;
            $params['body'][] = $doc->body;
        }
        return $params;
    }

    /**
     * Bulk indexes an array of documents, divided into batches.
     *
     * @param array $docs
     * @param int $batchSize
     * @return array
     */
    public static function bulkIndex(array $docs, $batchSize=500, $timeout=90) {
        $client = Elasticsearch_Client::create(['timeout' => $timeout]);

        $timing_start = microtime(true);
        error_log("Started bulk indexing at $timing_start");

        $responses = array();
        for($offset = 0; $offset < count($docs); $offset += $batchSize) {
            $params = self::getBulkParams($docs, $offset, $batchSize);
            $res = $client->bulk($params);
            $responses[] = $res;
        }

        $timing_end = microtime(true);
        $timing_duration = $timing_end - $timing_start;
        error_log("Finished bulk indexing at $timing_end");
        error_log("Bulk indexing took $timing_duration seconds");

        return $responses;
    }
}