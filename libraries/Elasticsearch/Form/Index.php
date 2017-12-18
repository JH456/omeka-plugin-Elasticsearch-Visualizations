<?php

class Elasticsearch_Form_Index extends Omeka_Form {
    public function init() {
        parent::init();

        $this->addElement('text', 'elasticsearch_index', array(
            'label'         => __('Elasticsearch Index'),
            'value'         => get_option('elasticsearch_index'),
            'required'      => true,
            'size'          => 40
        ));

        $this->addElement('submit', 'submit', array(
            'label' => __('Clear and Reindex')
        ));

        $this->addDisplayGroup(array(
            'elasticsearch_index',
        ), 'fields');

        $this->addDisplayGroup(array('submit'), 'submit_button');
    }
}
