<?php

/**
 * Admin Controller for Elasticsearch plugin.
 *
 * Provides actions to configure the elasticserach server URL, reindex the site, etc.
 *
 */
class Elasticsearch_AdminController extends Omeka_Controller_AbstractActionController {

    public function serverAction() {
        $form = new Elasticsearch_Form_Server();

        if($this->_request->isPost() && $form->isValid($_POST)) {
            foreach($form->getValues() as $option => $value) {
                set_option($option, $value);
            }

            try {
                $client = Elasticsearch_Client::create(['timeout' => 2]);
                $res = $client->cat()->health();
                $msg = "Elasticsearch endpoint health check successful. Cluster status is {$res[0]['status']} with {$res[0]['node.total']} total nodes.";
                $this->_helper->flashMessenger($msg, 'success');
            } catch(Exception $e) {
                $msg = "Elasticsearch endpoint health check failed. Error: ".$e->getMessage();
                $this->_helper->flashMessenger($msg, 'error');
            }
        }

        $this->view->form = $form;
    }

    public function reindexAction() {
        $this->view->form = new Elasticsearch_Form_Index();

        if ($this->_request->isPost()) {
            try {
                $job_dispatcher = Zend_Registry::get('job_dispatcher');
                $job_dispatcher->send('Elasticsearch_Job_Reindex', array(
                    'user'     => $this->getCurrentUser(),
                    'db'       => $this->_helper->db
                ));
                $this->_helper->flashMessenger(__('Reindexing started.'), 'success');
            } catch (Exception $err) {
                $this->_helper->flashMessenger($err->getMessage(), 'error');
            }
        }
    }
}
