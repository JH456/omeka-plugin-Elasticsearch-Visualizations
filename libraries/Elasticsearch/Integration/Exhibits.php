<?php

class Elasticsearch_Integration_Exhibits extends Elasticsearch_Integration_BaseIntegration
{
    protected $_hooks = array(
        'after_save_exhibit',
        'after_save_exhibit_page',
        'after_delete_exhibit',
        'after_delete_exhibit_page'
    );

    /**
     * Hook for when an exhibit is being saved.
     *
     * @param array $args
     */
    public function hookAfterSaveExhibit($args) {
        $this->_log("hookAfterSaveExhibit: {$args['record']->id}");
        $this->indexExhibit($args['record']);
    }

    /**
     * Hook for when an exhibit is being deleted.
     *
     * @param array $args
     */
    public function hookAfterDeleteExhibit($args) {
        $this->_log("hookAfterDeleteExhibit: {$args['record']->id}");
        $this->deleteExhibit($args['record']);
    }

    /**
     * Hook for when an exhibit is being saved.
     *
     * @param array $args
     */
    public function hookAfterSaveExhibitPage($args) {
        $this->_log("hookAfterSaveExhibitPage: {$args['record']->id}");
        $this->indexExhibitPage($args['record']);
    }

    /**
     * Hook for when an exhibit is being deleted.
     *
     * @param array $args
     */
    public function hookAfterDeleteExhibitPage($args) {
        $this->_log("hookAfterDeleteExhibitPage: {$args['record']->id}");
        $this->deleteExhibitPage($args['record']);
    }

    /**
     * Indexes an exhibit.
     *
     * @param $exhibit
     * @return array
     */
    public function indexExhibit($exhibit) {
        $doc = $this->getExhibitDocument($exhibit);
        return $doc->index();
    }

    /**
     * Deletes an exhibit from the index.
     *
     * @param $exhibit
     */
    public function deleteExhibit($exhibit) {
        $doc = new Elasticsearch_Document($this->_docIndex, "exhibit_{$exhibit->id}");
        return $doc->delete();
    }

    /**
     * Indexes a single exhibit page.
     *
     * @param $exhibitPage
     * @return array
     */
    public function indexExhibitPage($exhibitPage) {
        $doc = $this->getExhibitPageDocument($exhibitPage);
        return $doc->index();
    }


    /**
     * Deletes an exhibit page from the index.
     *
     * @param $exhibitPage
     */
    public function deleteExhibitPage($exhibitPage) {
        $doc = new Elasticsearch_Document($this->_docIndex, "exhibit_page_{$exhibitPage->id}");
        return $doc->delete();
    }

    /**
     * Returns an exhibit as a document.
     *
     * @param $exhibit
     * @return Elasticsearch_Document
     */
    public function getExhibitDocument($exhibit) {
        $doc = new Elasticsearch_Document($this->_docIndex, "exhibit_{$exhibit->id}");
        $doc->setFields([
            'resulttype' => 'Exhibit',
            'model'      => 'Exhibit',
            'modelid'    => $exhibit->id,
            'featured'   => (bool) $exhibit->featured,
            'public'     => (bool) $exhibit->public,
            'title'      => $exhibit->title,
            'description'=> $exhibit->description,
            'credits'    => $exhibit->credits,
            'slug'       => $exhibit->slug
        ]);

        $tags = [];
        foreach ($exhibit->getTags() as $tag) {
            $tags[] = $tag->name;
        }
        $doc->setField('tags', $tags);

        return $doc;
    }

    /**
     * Get array of documents to index.
     *
     * @return array
     */
    public function getExhibitDocuments() {
        $db = get_db();
        $table = $db->getTable('Exhibit');
        $select = $table->getSelect();
        $table->applySorting($select, 'id', 'ASC');
        $exhibits = $table->fetchObjects($select);

        $docs = [];
        foreach($exhibits as $exhibit) {
            $docs[] = $this->getExhibitDocument($exhibit);
        }

        return $docs;
    }

    /**
     * Returns an exhibit page as a document.
     *
     * @param $exhibitPage
     * @return Elasticsearch_Document
     */
    public function getExhibitPageDocument($exhibitPage) {
        //$exhibit = $exhibitPage->getExhibit();
        $doc = new Elasticsearch_Document($this->_docIndex, "exhibit_page_{$exhibitPage->id}");
        $doc->setFields([
            'resulttype' => 'Exhibit',
            'model'      => 'ExhibitPage',
            'modelid'    => $exhibitPage->id,
            'title'      => $exhibitPage->title,
        ]);

        $pageBlocks = [];
        foreach ($exhibitPage->getPageBlocks() as $pageBlock) {
            $block = array();
            $block['text'] = strip_tags($pageBlock->text);
            $block['attachments'] = array();
            foreach ($pageBlock->getAttachments() as $attachment) {
                $block['attachments'][] = strip_tags($attachment->caption);
            }
            $pageBlocks[] = $block;
        }
        $doc->setField('blocks', $pageBlocks);

        return $doc;
    }

    /**
     * Get array of documents to index.
     *
     * @return array
     */
    public function getExhibitPageDocuments() {
        $db = get_db();
        $table = $db->getTable('ExhibitPage');
        $select = $table->getSelect();
        $table->applySorting($select, 'id', 'ASC');
        $exhibitPages = $table->fetchObjects($select);

        $docs = [];
        foreach($exhibitPages as $exhibitPage) {
            $docs[] = $this->getExhibitPageDocument($exhibitPage);
        }

        return $docs;
    }

    /**
     * Index all exhibits.
     */
    public function indexAll() {
        // since this method is intended to be called as a background job, we need to ensure that
        // the exhibit plugin's routes have been loaded so that URLs can be constructed properly
        $router = Zend_Controller_Front::getInstance()->getRouter();
        exhibit_builder_define_routes(array('router' => $router));

        // index exhibits
        $docs = $this->getExhibitDocuments();
        $this->_log('indexAll exhibits: '.count($docs));
        Elasticsearch_Document::bulkIndex($docs);

        // index exhibit pages
        $docs = $this->getExhibitPageDocuments();
        $this->_log('indexAll exhibit pages: '.count($docs));
        Elasticsearch_Document::bulkIndex($docs);
    }
}