<?php

class Elasticsearch_Utils {

    /**
     * Returns an <li> element for the navigation partial view.
     * @param $current
     * @param $url
     * @param $label
     * @return string
     */
    public static function nav_li($current, $url, $label) {
        $cssClass = ($current ? ' class="current"' : '');
        return "<li$cssClass><a href=\"$url\">$label</a></li>";
    }

    /**
     * Returns the URL for a document returned by an elasticsearch query (e.g. a "hit").
     * Depends on the document in question having "model" and "modelid" properties.
     *
     * @param $hit a hit from an elasticsearch query
     * @return string an omeka URL to the object
     */
    public static function getDocumentUrl($hit) {
        $source = $hit['_source'];
        if(isset($source['model']) && isset($source['modelid'])) {
            $record = get_db()->getTable($source['model'])->find($source['modelid']);
            return record_url($record);
        }
        return '';
    }
}
