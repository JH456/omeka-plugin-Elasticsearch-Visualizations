<?php

class Elasticsearch_Form_Index extends Omeka_Form {
    public function init() {
        parent::init();
        $this->addElement('submit', 'submit', array(
            'label' => __('Clear and Reindex')
        ));
    }
}
