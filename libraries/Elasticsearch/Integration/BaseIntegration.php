<?php

abstract class Elasticsearch_Integration_BaseIntegration {
    protected $_active = true;
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
        return $this->_active;
    }

    /**
     * Alias for applyHooksAndFilters method.
     */
    public function integrate() {
        $this->applyHooksAndFilters();
    }

    /**
     * Apply all hooks and filters implemented in this integration.
     */
    public function applyHooksAndFilters() {
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
     * Returns the elasticsearch client.
     *
     * @return \Elasticsearch\Client
     */
    public function client(array $options = array()) {
        return Elasticsearch_Client::create($options);
    }

    /**
     * Logs an elasticsearch message.
     *
     * @param $msg
     */
    protected function _log($msg, $logLevel=Zend_Log::INFO) {
        _log('Elasticsearch: '.$msg, $logLevel);
    }

}