<?php

class Elasticsearch_SearchController extends Omeka_Controller_AbstractActionController {

    public function interceptorAction() {
        $q_string = http_build_query(['q'=> $this->_request->getParam('query')]);
        return $this->_helper->redirector->gotoUrl("/elasticsearch/search/index?$q_string");
    }

    public function indexAction() {
        $limit = get_option('per_page_public');
        $limit = isset($limit) ? $limit : 20;
        $page = $this->_request->page ? $this->_request->page : 1;
        $start = ($page - 1) * $limit;
        $user = $this->getCurrentUser();

        // determine whether we can show "not public" (e.g. private) items
        $can_view_private_items = $user && is_allowed('Items', 'showNotPublic');
        $only_public_items = !$can_view_private_items;

        // execute query
        $results = Elasticsearch_Helper_Index::search($this->_request->q, [
            'offset' => $start,
            'limit' => $limit,
            'only_public_items' => $only_public_items
        ]);

        Zend_Registry::set('pagination', [
            'per_page'      => $limit,
            'page'          => $page,
            'total_results' => $results['hits']['total']
        ]);

        $this->view->assign('results', $results);
    }
}