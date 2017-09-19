<?php

class Elasticsearch_AdminController extends Omeka_Controller_AbstractActionController {

    public function serverAction() {
        $form = new Elasticsearch_Form_Server();

        if($this->_request->isPost() && $form->isValid($_POST)) {
            foreach($form->getValues() as $option => $value) {
                set_option($option, $value);
            }
        }

        $this->view->form = $form;
    }

    public function reindexAction() {
        $form = new Elasticsearch_Form_Index();

        if ($this->_request->isPost()) {
            // dispatch background job to index content
            // install php client library for elasticsearch
            // try to index something
            try {
                $this->_helper->flashMessenger(__('Reindexing started.'), 'success');
                $job = new Elasticsearch_Job_Index( array() );
                $job->perform();
            } catch (Exception $err) {
                $this->_helper->flashMessenger($err->getMessage(), 'error');
            }
        }

        $this->view->form = $form;
    }
}
