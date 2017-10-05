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

    public static function service() {
        $config = self::load();
        return $config->service;
    }

    public static function host() {
        $config = self::load();
        $section = $config->get($config->service, null);
        if(!isset($section)) {
            throw new Exception("elasticsearch.ini misconfiguration: missing [service] section");
        }
        return [
            'host'   => $section->host,
            'port'   => $section->port,
            'scheme' => $section->scheme,
            'user'   => isset($section->user) ? $section->user : null,
            'pass'   => isset($section->pass) ? $section->pass : null
        ];
    }

    public static function roles() {
        $config = self::load();
        $roles = explode(",", $config->get('roles', 'admin,super'));
        return array_map('trim', $roles);
    }
}
