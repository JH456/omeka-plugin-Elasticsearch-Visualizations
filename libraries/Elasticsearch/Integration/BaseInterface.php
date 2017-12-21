<?php

interface Elasticsearch_Integration_BaseInterface {

    // Initializes the integration before applying hooks and filters
    public function initialize();

    // Returns true if hooks and filters should be applied
    public function isActive();

    // Applies all hooks and filters necessary to integrate elasticsearch
    public function integrate();

    // Indexes all documents related to this integration
    public function indexAll();

    // Delete all documents from the index related to this integration
    public function deleteAll();
}