<?php

/**
 * Admin Controller for Elasticsearch plugin.
 *
 * Provides actions to configure the elasticserach server URL, reindex the site, etc.
 *
 */
class Elasticsearch_AdminController extends Omeka_Controller_AbstractActionController {

    protected function _handlePermissions() {
        if(!Elasticsearch_Utils::hasAdminPermission()) {
            throw new Omeka_Controller_Exception_403;
        }
    }

    public function serverAction() {
        $this->_handlePermissions();
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
        $this->_handlePermissions();
        if ($this->_request->isPost()) {
            try {
                $job_dispatcher = Zend_Registry::get('job_dispatcher');
                $job_dispatcher->setUser($this->getCurrentUser());
                $job_dispatcher->sendLongRunning('Elasticsearch_Job_Reindex');
                $this->_helper->flashMessenger(__('Reindexing started.'), 'success');
            } catch (Exception $err) {
                $this->_helper->flashMessenger($err->getMessage(), 'error');
            }
            $this->redirect('/elasticsearch/admin/reindex');
        } else {
            $jobs = Elasticsearch_Helper_Index::getReindexJobs();
            $this->view->assign("jobs", $jobs);
            $this->view->form = new Elasticsearch_Form_Index();
        }
    }
}
