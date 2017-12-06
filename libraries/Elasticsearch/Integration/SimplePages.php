<?php

class Elasticsearch_Integration_SimplePages extends Elasticsearch_Integration_BaseIntegration
{
    protected $_hooks = array(
        'after_save_simple_pages_page',
        'after_delete_simple_pages_page',
    );

    /**
     * Hook for when a simple page is being saved.
     *
     * @param array $args
     */
    public function hookAfterSaveSimplePagesPage($args)
    {
        $this->_log("hookAfterSaveSimplePagesPage: {$args['record']->id}");
        $this->indexSimplePage($args['record']);
    }

    /**
     * Hook for when a simple page is being deleted.
     *
     * @param array $args
     */
    public function hookAfterDeleteSimplePagesPage($args)
    {
        $this->_log("hookAfterDeleteSimplePagesPage: {$args['record']->id}");
        $this->deleteSimplePage($args['record']);
    }

    /**
     * Indexes a simple page.
     *
     * @param $simplePage
     * @return array
     */
    public function indexSimplePage($simplePage) {
        $doc = $this->getSimplePageDocument($simplePage);
        return $doc->index();
    }

    /**
     * Deletes a simple page from the index.
     *
     * @param $exhibit
     */
    public function deleteSimplePage($simplePage) {
        $doc = new Elasticsearch_Document($this->_docIndex, "simple_page_{$simplePage->id}");
        return $doc->delete();
    }

    /**
     * Returns a simple page as a document.
     *
     * @param $simplePage
     * @return Elasticsearch_Document
     */
    public function getSimplePageDocument($simplePage) {
        $doc = new Elasticsearch_Document($this->_docIndex, "simple_page_{$simplePage->id}");
        $doc->setFields([
            'resulttype' => 'SimplePage',
            'model'      => 'SimplePagesPage',
            'modelid'    => $simplePage->id,
            'public'     => (bool) $simplePage->is_published,
            'title'      => $simplePage->title,
            'text'       => $simplePage->text,
            'slug'       => $simplePage->slug,
            'created'    => $this->_getDate($simplePage->inserted),
            'updated'    => $this->_getDate($simplePage->updated)
        ]);
        return $doc;
    }

    /**
     * Get array of documents to index.
     *
     * @return array
     */
    public function getSimplePageDocuments() {
        $db = get_db();
        $table = $db->getTable('SimplePagesPage');
        $select = $table->getSelect();
        $table->applySorting($select, 'id', 'ASC');
        $simplePages = $table->fetchObjects($select);

        $docs = [];
        foreach($simplePages as $simplePage) {
            $docs[] = $this->getSimplePageDocument($simplePage);
        }

        return $docs;
    }

    /**
     * Index all items.
     */
    public function indexAll() {
        $docs = $this->getSimplePageDocuments();
        $this->_log('indexAll simple pages: '.count($docs));
        Elasticsearch_Document::bulkIndex($docs);
    }
}