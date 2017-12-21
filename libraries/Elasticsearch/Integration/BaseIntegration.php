<?php

abstract class Elasticsearch_Integration_BaseIntegration implements Elasticsearch_Integration_BaseInterface {
    protected $_docIndex = null;
    protected $_hooks = array();
    protected $_filters = array();

    /**
     * Elasticsearch_Integration_BaseIntegration constructor.
     *
     * @param $docIndex defines the elasticsearch index to use
     */
    public function __construct($docIndex) {
        $this->_docIndex = $docIndex;
        if(!isset($this->_docIndex) || $this->_docIndex == "") {
            throw Exception("docIndex parameter must be a non-empty string");
        }
    }

    /**
     * Initializes the integration before adding and hooks or filters.
     */
    public function initialize() {
    }

    /**
     * Returns whether this integration should be applied.
     *
     * @return boolean
     */
    public function isActive() {
        return true;
    }

    /**
     * Applies all hooks and filters defined by this integration.
     */
    public function integrate() {
        $className = get_called_class();
        if ($this->isActive()) {
            $this->_log("Applying hooks and filters for $className");
            $this->initialize();
            foreach ($this->_hooks as $hook) {
                add_plugin_hook($hook, array($this, 'hook' . Inflector::camelize($hook)));
            }
            foreach ($this->_filters as $filter) {
                add_filter($filter, array($this, 'filter' . Inflector::camelize($filter)));
            }
        }
    }

    /**
     * Format a date string as an ISO 8601 date, UTC timezone.
     *
     * @param $date
     * @return string
     */
    protected function _getDate($date) {
        $date = new DateTime($date);
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->format('c');
    }

    /**
     * Retrieve object records.
     *
     * @return array
     */
    protected function _fetchObjects($className) {
        if(!class_exists($className)) {
            $this->_log("Cannot fetch objects for $className because class does not exist!", Zend_Log::ERR);
            return null;
        }
        $db = get_db();
        $table = $db->getTable($className);
        $select = $table->getSelect();
        $table->applySorting($select, 'id', 'ASC');
        return $table->fetchObjects($select);
    }

    /**
     * Deletes all indexed documents by their model keyword.
     *
     * @param string $model
     */
    protected function _deleteByQueryModel($model) {
        $client = $this->_getClient();
        $params = [
            'index' => $this->_docIndex,
            'type' => 'doc',
            'body' => [
                'query' => [
                    'term' => ['model' => $model]
                ]
            ]
        ];
        $this->_log("deleteByQueryModel($model):".var_export($params,1));

        $res = $client->deleteByQuery($params);
        $this->_log("response: ".var_export($res, 1));

        return $res;
    }

    /**
     * Returns the elasticsearch client.
     *
     * @return \Elasticsearch\Client
     */
    protected function _getClient(array $options = array()) {
        return Elasticsearch_Client::create($options);
    }

    /**
     * Logs an elasticsearch message with the given log level (defaults to INFO).
     *
     * @param $msg
     */
    protected function _log($msg, $logLevel=Zend_Log::INFO) {
        _log('Elasticsearch: '.$msg, $logLevel);
    }

    /**
     * Logs a debug message.
     *
     * @param $msg
     */
    protected function _debug($msg) {
        _log('Elasticsearch: '.$msg, Zend_Log::DEBUG);
    }
}