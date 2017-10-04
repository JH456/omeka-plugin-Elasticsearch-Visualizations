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
        $record = self::getRecord($hit);
        if(isset($record)) {
            return record_url($record);
        }
        return '';
    }

    /**
     * Returns a record object from an elasticsearch "hit" document.
     *
     * @param $hit
     * @return false|Omeka_Record_AbstractRecord
     */
    public static function getRecord($hit) {
        $source = $hit['_source'];
        if(isset($source['model']) && isset($source['modelid'])) {
            return get_db()->getTable($hit['_source']['model'])->find($hit['_source']['modelid']);
        }
        return null;
    }

    /**
     * Returns a facet search URL.
     *
     * @return string
     */
    public static function getFacetUrl($querystr, $param, $value) {
        $base_url = get_view()->url('/elasticsearch');
        $param_is_array = in_array($param, array('tags'));
        if($param_is_array) {
            $item = urlencode("facet_{$param}[]")."=".urlencode($value);
        } else {
            $item = "facet_{$param}=".urlencode($value);
        }

        if(strpos($querystr, $item) === FALSE) {
            return "$base_url?$querystr&$item";
        }
        return "$base_url?$querystr";
    }

    /**
     * Returns a query string including search terms and facets.
     *
     * @return string
     */
    public static function getQueryString($query) {
        $terms = isset($query['q']) ? $query['q'] : '';
        $facets = isset($query['facets']) ? $query['facets'] : array();
        $querystr = "q=".urlencode($terms);

        foreach($facets as $facet_name => $facet_values) {
            if(is_array($facet_values)) {
                foreach($facet_values as $k => $v) {
                    $querystr .= '&'.urlencode("facet_{$facet_name}[]").'='.urlencode($v);
                }
            } else {
                $querystr .= '&'.urlencode("facet_{$facet_name}").'='.urlencode($facet_values);
            }
        }

        return $querystr;
    }
}
