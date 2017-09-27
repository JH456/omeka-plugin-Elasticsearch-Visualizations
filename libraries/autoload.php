<?php

spl_autoload_register(function ($className) {
    $prefix = 'Elasticsearch_';
    if(substr($className, 0, strlen($prefix)) == $prefix) {
        $filename = str_replace('_', DIRECTORY_SEPARATOR, $className) . ".php";
        $filename = dirname(__FILE__) . '/' . $filename;
        if(file_exists($filename)) {
            include($filename);
            if (class_exists($className)) {
                return TRUE;
            }
        }
    }
    return FALSE;
});
