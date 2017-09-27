<?php

class Elasticsearch_Utils {

    public static function nav_li($current, $url, $label) {
        $cssClass = ($current ? ' class="current"' : '');
        return "<li$cssClass><a href=\"$url\">$label</a></li>";
    }

    public static function getConfig() {
        $config = new Zend_Config_Ini(ELASTICSEARCH_PLUGIN_DIR.'/elasticsearch.ini');
        return $config;
    }
}
