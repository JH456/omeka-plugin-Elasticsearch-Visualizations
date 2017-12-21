<?php

class Elasticsearch_IntegrationManager {
    /**
     * Defines a list of supported plugins that can be integrated with elasticsearch.
     * Maps plugin names to integration class names.
     * @var array
     */
    protected static $_supportedPlugins = array(
        'ExhibitBuilder' => 'Elasticsearch_Integration_Exhibits',
        'SimplePages'    => 'Elasticsearch_Integration_SimplePages',
        'Neatline'       => 'Elasticsearch_Integration_Neatline'
    );

    /**
     * Defines the list of integration classes that will index content.
     * @var array
     */
    protected static $_integrations = array('Elasticsearch_Integration_Items');

    /**
     * Defines whether plugins have been detected yet.
     * @var bool
     */
    protected static $_detected = false;

    /**
     * The name of the elasticsearch index to use.
     * @var string|null
     */
    protected $_docIndex = null;

    /**
     * Elasticsearch_Integration_BaseIntegration constructor.
     *
     * @param $docIndex defines the elasticsearch index to use
     */
    public function __construct() {
        self::_detectIntegrations();
    }

    /**
     * Sets the elasticsearch document index to use.
     *
     * @return $this
     */
    public function setIndex($docIndex) {
        $this->_docIndex = $docIndex;
        return $this;
    }

    /**
     * Invokes the integrate() method in each integration.
     */
    public function integrate() {
        $this->_perform('integrate');
    }

    /**
     * Invokes the indexAll() method in each integration.
     */
    public function indexAll() {
        $this->_perform('indexAll');
    }

    /**
     * Factory function to create an instance of this class.
     *
     * @param $docIndex
     * @return Elasticsearch_IntegrationManager
     */
    public static function create($docIndex) {
        $mgr = new Elasticsearch_IntegrationManager();
        $mgr->setIndex($docIndex);
        return $mgr;
    }

    /**
     * Delegates a method to each integration class.
     *
     * @param $method
     */
    protected function _perform($method) {
        _log("_perform($method)");
        $results = [];
        foreach(self::$_integrations as $integrationClass) {
            if(class_exists($integrationClass)) {
                $integration = new $integrationClass($this->_docIndex);
                $results[] = $integration->$method();
            } else {
                _log("Integration class $integrationClass does not exist!", Zend_Log::WARN);
            }
        }
        return $results;
    }

    /**
     * Detects plugins that are supported and should be integrated with
     * elasticsearch by querying the plugin table and checking for active plugins.
     */
    protected static function _detectIntegrations() {
        if(self::$_detected) {
            return;
        }

        $pluginNames = array_keys(self::$_supportedPlugins);
        sort($pluginNames);
        $table = get_db()->getTable('Plugin');
        $select = $table->getSelect()->where('name IN (?)', $pluginNames);
        $pluginRecords = $table->fetchObjects($select);

        foreach($pluginRecords as $plugin) {
            if ($plugin->active && isset(self::$_supportedPlugins[$plugin->name])) {
                _log("Integration manager detected active plugin {$plugin->name}", Zend_Log::INFO);
                self::$_integrations[] = self::$_supportedPlugins[$plugin->name];
            }
        }

        self::$_detected = true;
    }

}