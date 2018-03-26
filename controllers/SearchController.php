<?php

class Elasticsearch_SearchController extends Omeka_Controller_AbstractActionController {

    public function interceptorAction() {
        $q_string = http_build_query(['q'=> $this->_request->getParam('query')]);
        return $this->_helper->redirector->gotoUrl("/elasticsearch/search/index?$q_string");
    }

    public function indexAction() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $this->_indexGetAction();
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->_indexPostAction();
        }
    }

    private function _indexPostAction() {
        $query = $this->_getSearchParams();
        $user = $this->getCurrentUser();
        $limit = 1000;
        $totalResults = $this->_search(
            [
                'query'             => $query,
                'limit'             => $limit,
                'offset'            => $this->_request->graphData ? $this->_request->graphData : 0,
                'showNotPublic'     => $user && is_allowed('Items', 'showNotPublic'),
                '_source'           => [
                    'include' => ['tags', 'title']
                ]
            ]
        );
        $graphData = $this->_generateGraphData($totalResults, $limit);
        echo $graphData;
    }

    private function _indexGetAction() {
        $limit = get_option('per_page_public');
        $limit = isset($limit) ? $limit : 20;
        $page = $this->_request->page ? $this->_request->page : 1;
        $start = ($page - 1) * $limit;
        $user = $this->getCurrentUser();
        $query = $this->_getSearchParams();
        $sort = $this->_getSortParams();
        $highlight = true;

        // execute query
        $pageResults = $this->_search(
            [
                'query'             => $query,
                'offset'            => $start,
                'limit'             => $limit,
                'sort'              => $sort,
                'showNotPublic'     => $user && is_allowed('Items', 'showNotPublic'),
                'highlight'         => $highlight
            ]
        );

        $paginationParams = [
            'per_page' => $limit,
            'page' => $page,
            'total_results' => $pageResults['hits']['total']
        ];

        if ($pageResults) {
            Zend_Registry::set('pagination', $paginationParams);
        }

        // $graphData = $this->_generateGraphData($totalResults);

        $this->view->assign('query', $query);
        $this->view->assign('results', $pageResults);
        // $this->view->assign('graphData', $graphData);
    }

    private function _search($searchParams) {
        $results = null;
        try {
            $results = Elasticsearch_Helper_Index::search($searchParams);
        } catch(Exception $e) {
            error_log($e->getMessage());
        }
        return $results;
    }

    private function _generateGraphData($results, $limit) {
        ini_set('memory_limit', '256M');
        $nodes = array();
        $links = array();
        $totalResults = $results['hits']['total'];
        if ($results) {
            $hits = $results['hits']['hits'];
            $tagsToDocuments = array();
            foreach($hits as $hit):
                $hitName = $hit['_source']['title'];
                $nodes[] = array(
                    "id" => $hitName,
                    "group" => 1
                );
                $documentHasTags = isset($hit['_source']['tags']);
                if ($documentHasTags) {
                    $tags = $hit['_source']['tags'];
                    foreach($tags as $tagName):
                        if (!isset($tagsToDocuments[$tagName])) {
                            $tagsToDocuments[$tagName] = array($hitName);
                        } else {
                            $tagsToDocuments[$tagName][] = $hitName;
                        }
                     endforeach;
                }
            endforeach;
            foreach($tagsToDocuments as $tagName => $documentsWithTag) {
                $group = base_convert(md5($tagName), 16, 10);
                $nodes[] = array(
                    "id" => $tagName,
                    "name" => $tagName,
                    "group" => $group
                );
                for ($i = 0; $i < count($documentsWithTag); $i++) {
                    $links[] = array(
                        "source" => $documentsWithTag[$i],
                        "target" => $tagName,
                        "value" => 1,
                        "group" => $group
                    );
                }
            }
        }
        $graphData = array("nodes" => $nodes, "links" => $links, "totalResults" => $totalResults, "limit" => $limit);
        return json_encode($graphData);
    }

    private function _getSearchParams() {
        $query = [
            'q'      => $this->_request->q, // search terms
            'facets' => []                  // facets to filter the search results
        ];
        foreach($this->_request->getQuery() as $k => $v) {
            if(strpos($k, 'facet_') === 0) {
                $query['facets'][substr($k, strlen('facet_'))] = $v;
            }
        }
        return $query;
    }

    private function _getSortParams() {
        $sort = [];
        if($this->_request->sort_field) {
            $sort['field'] = $this->_request->sort_field;
            if($this->_request->sort_dir) {
                $sort['dir'] = $this->_request->sort_dir;
            }
        }
        return $sort;
    }
}
