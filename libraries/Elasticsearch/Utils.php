<?php

class Elasticsearch_Utils {

    public static function nav_li($current, $url, $label)
    {
        $cssClass = ($current ? ' class="current"' : '');
        return "<li$cssClass><a href=\"$url\">$label</a></li>";
    }
}