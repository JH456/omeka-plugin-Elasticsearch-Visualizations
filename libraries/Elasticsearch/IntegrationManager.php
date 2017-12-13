<?php

class Elasticsearch_IntegrationManager {
    protected $_integrations = array('items','exhibits', 'simple_pages', 'neatline');
    protected $_docIndex = null;

    /**
     * Elasticsearch_Integration_BaseIntegration constructor.
     *
     * @param $docIndex defines the elasticsearch index to use
     */
    public function __construct($docIndex) {
        $this->_docIndex = $docIndex;
    }

    public function applyHooksAndFilters() {
        $this->_perform('applyHooksAndFilters');
    }

    public function indexAll() {
        $this->_perform('indexAll');
    }

    protected function _perform($method) {
        _log("_perform($method)");
        foreach($this->_integrations as $name) {
            $integrationClass = "Elasticsearch_Integration_".Inflector::camelize($name);
            $integration = new $integrationClass($this->_docIndex);
            $integration->$method();
        }
    }
}