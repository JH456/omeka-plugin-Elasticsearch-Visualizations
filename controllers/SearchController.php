<?php

class Elasticsearch_SearchController extends Omeka_Controller_AbstractActionController {

    public function interceptorAction() {
        $q_string = http_build_query(['q'=> $this->_request->getParam('query')]);
        return $this->_helper->redirector->gotoUrl("/elasticsearch/search/index?$q_string");
    }

    public function indexAction() {
        $limit = get_option('per_page_public');
        $page = $this->_request->page ? $this->_request->page : 1;
        $start = ($page - 1) * $limit;
        $user = $this->getCurrentUser();

        // determine whether we can show "not public" (e.g. private) items
        $can_view_private_items = $user && is_allowed('Items', 'showNotPublic');
        $only_public_items = !$can_view_private_items;

        // execute query
        $results = $this->_search($start, $limit, $only_public_items);

        Zend_Registry::set('pagination', [
            'per_page'      => $limit,
            'page'          => $page,
            'total_results' => $results['hits']['total']
        ]);

        $this->view->assign('results', $results);
    }

    protected function _search($offset, $limit, $only_public_items=true) {
        $client = Elasticsearch_Helper_Index::client();
        $config = Elasticsearch_Utils::getConfig();
        $query = $this->_getSearchQuery($only_public_items);

        // See also: https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html
        $params = [
            'index' => $config->index->name,
            'body' => [
                'query' => [
                    'query_string' => [
                        'default_field' => '_all',
                        'fields' => ['elements.*', 'title', 'collection', 'itemType', 'tags.*'],
                        'query' => $query
                    ]
                ]
            ]
        ];
        error_log("elasticsearch query: ".var_export($params,1));

        return $client->search($params);
    }

    protected function _getSearchQuery($only_public_items) {
        $query = $this->_request->q;
        if(empty($query)) {
            $query = '*';
        }
        if($only_public_items) {
            $query .= ' AND public:true';
        }
        return $query;
    }
}