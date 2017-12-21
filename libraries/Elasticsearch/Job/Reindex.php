<?php

/**
 * This is a job for reindexing content.
 *
 * Note: whether it is async or sync depends on the job dispatcher.
 */

class Elasticsearch_Job_Reindex extends Omeka_Job_AbstractJob {
    /**
     * Main runnable method.
     */
    public function perform() {
        Elasticsearch_Helper_Index::deleteIndex();
        Elasticsearch_Helper_Index::createIndex();
        Elasticsearch_Helper_Index::indexAll();
    }
}
