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
     * Adds a facet to a query string.
     *
     * Handles logic around facets that are arrays of values such as tags.
     *
     * @param $querystr
     * @param $param The name of the facet parameter (e.g. itemType)
     * @param $value The value that should associated with the parameter (e.g. "Still Image")
     * @return string The new query string with the facet added.
     */
    public static function addFacetToQuery($querystr, $param, $value) {
        if(in_array($param, array('tags'))) {
            $item = urlencode("facet_{$param}[]")."=".urlencode($value);
        } else {
            $item = "facet_{$param}=".urlencode($value);
        }

        if(strpos($querystr, $item) === FALSE) {
            return "$querystr&$item";
        }
        return $querystr;
    }

    /**
     * Removes a facet from a given query string and returns the new query string.
     *
     * @param $querystr The query string to manipulate.
     * @param $paramToRemove The name of the facet parameter to remove (e.g. itemType).
     * @return string The new query string with the facet removed.
     */
    public static function removeFacetFromQuery($querystr, $paramToRemove) {
        $old_items = explode('&', $querystr);
        $filtered = array();
        foreach($old_items as $item) {
            if(strpos($item, $paramToRemove) === FALSE) {
                $filtered[] = $item;
            }
        }
        return implode('&', $filtered);
    }

    /**
     * Returns a facet value as a string.
     *
     * When the value is an array, returns the value as a comma-separated string.
     *
     * @return string
     */
    public static function facetVal2Str($value, $glue=", ") {
        return is_array($value) ? implode($glue, $value) : $value;
    }

    /**
     * Returns a query string including search terms and facets.
     *
     * @param array $query
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

    /**
     * Truncates a string for display.
     *
     * @param string $text the text to truncate
     * @param int $length the length to truncate to
     * @param boolean $ellipsis show an ellipsis or not
     * @return string
     */
    public static function truncateText($text, $length, $ellipsis=true) {
        $truncated = substr($text, 0, $length);
        if($ellipsis && strlen($truncated) > $length) {
            return "$truncated...";
        }
        return $truncated;
    }

    /**
     * Formats an ISO8601 date for display.
     */
    public static function formatDate($iso8601date) {
        return date_format(date_create($iso8601date), "F j, Y");
    }

    /**
     * Returns true if the user is allowed to access the admin functionality.
     *
     * Super users always have permission, but other roles must be explicitly
     * allowed.
     *
     * @return boolean
     */
    public static function hasAdminPermission() {
        $user = Zend_Registry::get('bootstrap')->getResource('CurrentUser');
        if(!$user) {
            return false;
        }
        $roles = array_unique(array_merge(Elasticsearch_Config::roles(), array('super')));
        return in_array($user->role, $roles);
    }
}
