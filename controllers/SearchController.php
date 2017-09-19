<?php

class Elasticsearch_SearchController extends Omeka_Controller_AbstractActionController {
    public function init() {

    }

    public function interceptAction() {
        $this->_redirect('elasticsearch?'.http_build_query(array(
            'q' => $this->_request->getParam('query')
        )));
    }

    public function indexAction() {
        $limit = get_option('per_page_public');
        $page = $this->_request->page ? $this->_request->page : 1;
        $start = ($page - 1) * $limit;
    }
}