<?php

spl_autoload_register(function ($name) {
    if(substr($name, 0, strlen('Elasticsearch_')) == 'Elasticsearch_') {
        $filename = substr($name, strlen('Elasticsearch_'));
        $filename = str_replace('_', DIRECTORY_SEPARATOR, $filename) . ".php";
        $filename = dirname(__FILE__) . '/' . $filename;
        if(file_exists($filename)) {
            include($filename);
            if (class_exists($name)) {
                return TRUE;
            }
        }
    }
    return FALSE;
});