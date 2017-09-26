<?php

class Elasticsearch_Form_Server extends Omeka_Form {
    public function init() {
        parent::init();

        // Host
        $this->addElement('text', 'elasticsearch_endpoint', array(
            'label'         => __('Server URL'),
            'description'   => __('The endpoint of the elasticsearch service (e.g. http://localhost:9200).'),
            'value'         => get_option('elasticsearch_endpoint'),
            'required'      => true,
            'size'          => 40
        ));

        $this->addElement('submit', 'submit', array(
            'label' => __('Save Settings')
        ));

        $this->addDisplayGroup(array(
            'elasticsearch_endpoint'
        ), 'fields');

        $this->addDisplayGroup(array('submit'), 'submit_button');
    }
}