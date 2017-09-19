<?php

/**
 * This is a background job for indexing content.
 *
 * @package Elasticsearch
 * @subpackage Job
 */

class Elasticsearch_Job_Index extends Omeka_Job_AbstractJob {
    protected $client;
    /**
     * Elasticsearch_Job_Index constructor.
     * @param array $options
     */
    public function __construct(array $options) {

        $this->client = Elasticsearch\ClientBuilder::create()->build();
    }

    /**
     * Main runnable method.
     */
    public function perform() {
        error_log("performing index action.");


    }

}