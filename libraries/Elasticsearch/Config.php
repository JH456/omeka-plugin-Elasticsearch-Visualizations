<?php

class Elasticsearch_Config {
    protected static $_config;

    public static function load() {
        if(self::$_config) {
            return self::$_config;
        }
        self::$_config = new Zend_Config_Ini(ELASTICSEARCH_PLUGIN_DIR.'/elasticsearch.ini');
        return self::$_config;
    }

    public static function index() {
        $config = self::load();
        return $config->get('index', 'omeka');
    }

    public static function hosts() {
        $config = self::load();
        return $config->get('hosts', array());
    }
}
