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
        $index = $this->_options['index'];
        error_log("performing elasticsearch reindex: $index");
        Elasticsearch_Helper_Index::deleteAll($index);
        Elasticsearch_Helper_Index::indexAll($index);
    }
}
