<?php

class Elasticsearch_Form_Server extends Omeka_Form {
    public function init() {
        parent::init();

        // Host
        $this->addElement('text', 'elasticsearch_host', array(
            'label'         => __('Server Host'),
            'description'   => __('The elasticsearch host (e.g. localhost)'),
            'value'         => get_option('elasticsearch_host'),
            'required'      => true,
            'size'          => 40
        ));

        // Port
        $this->addElement('text', 'elasticsearch_port', array(
            'label'         => __('Server Port'),
            'description'   => __('The elasticsearch port (e.g. 9200)'),
            'value'         => get_option('elasticsearch_port'),
            'required'      => true,
            'size'          => 10
        ));

        // Scheme
        $this->addElement('text', 'elasticsearch_scheme', array(
            'label'         => __('Scheme'),
            'description'   => __('The elasticsearch scheme (e.g. https OR http)'),
            'value'         => get_option('elasticsearch_scheme'),
            'required'      => true,
            'size'          => 10
        ));

        // User
        $this->addElement('text', 'elasticsearch_user', array(
            'label'         => __('Username'),
            'description'   => __('(optional) The HTTP basic authentication username'),
            'value'         => get_option('elasticsearch_user'),
            'required'      => false,
            'size'          => 20
        ));

        // Pass
        $this->addElement('text', 'elasticsearch_pass', array(
            'label'         => __('Password'),
            'description'   => __('(optional) The HTTP basic authentication password'),
            'value'         => get_option('elasticsearch_pass'),
            'required'      => false,
            'size'          => 20
        ));

        $this->addElement('submit', 'submit', array(
            'label' => __('Save Settings')
        ));

        $this->addDisplayGroup(array(
            'elasticsearch_host',
            'elasticsearch_port',
            'elasticsearch_scheme',
            'elasticsearch_user',
            'elasticsearch_pass'
        ), 'fields');

        $this->addDisplayGroup(array('submit'), 'submit_button');
    }
}