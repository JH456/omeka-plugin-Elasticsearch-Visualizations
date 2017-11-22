<?php

abstract class Elasticsearch_Integration_BaseIntegration {
    protected $_hooks = array();
    protected $_filters = array();

    /**
     * The initialize hook
     */
    public function initialize() {

    }

    /**
     * The install hook
     */
    public function install() {

    }

    /**
     * The uninstall hook
     */
    public function uninstall() {

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
     * Apply all hooks and filters implemented in this integration.
     */
    public function integrate() {
        if ($this->isActive()) {
            $this->initialize();
            $className = get_called_class();
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

    public static function doInstall() {
        $integrationClass = get_called_class(); // requires PHP 5.3.0 or greater
        $integration = new $integrationClass();
        $integration->install();
        return $integration;
    }

    public static function doUninstall() {
        $integrationClass = get_called_class(); // requires PHP 5.3.0 or greater
        $integration = new $integrationClass();
        $integration->uninstall();
        return $integration;
    }
}