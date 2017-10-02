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

    public static function host() {
        $config = self::load();
        $service = $config->get($config->service, null);
        if(!isset($service)) {
            throw new Exception("elasticsearch.ini misconfiguration: missing [service] section");
        }
        return [
            'host'   => $service->host,
            'port'   => $service->port,
            'scheme' => $service->scheme,
            'user'   => isset($service->user) ? $service->user : null,
            'pass'   => isset($service->pass) ? $service->pass : null
        ];
    }
}
