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
}