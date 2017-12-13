<?php

class Elasticsearch_Integration_Neatline extends Elasticsearch_Integration_BaseIntegration
{
    protected $_hooks = array(
        'after_save_neatline_record',
        'after_delete_neatline_record',
        'after_save_neatline_exhibit',
        'after_delete_neatline_exhibit'
    );

    /**
 * Hook for when a neatline record is being saved.
 *
 * @param array $args
 */
    public function hookAfterSaveNeatlineRecord($args)
    {
        $this->_log("hookAfterSaveNeatlineRecord: {$args['record']->id}");
        $this->indexNeatlineRecord($args['record']);
    }

    /**
     * Hook for when a neatline record is being deleted.
     *
     * @param array $args
     */
    public function hookAfterDeleteNeatlineRecord($args)
    {
        $this->_log("hookAfterDeleteNeatlineRecord: {$args['record']->id}");
        $this->deleteNeatlineRecord($args['record']);
    }

    /**
     * Hook for when a neatline exhibit is being saved.
     *
     * @param array $args
     */
    public function hookAfterSaveNeatlineExhibit($args)
    {
        $this->_log("hookAfterSaveNeatlineExhibit: {$args['record']->id}");
        $this->indexNeatlineExhibit($args['record']);
    }

    /**
     * Hook for when a neatline exhibit is being deleted.
     *
     * @param array $args
     */
    public function hookAfterDeleteNeatlineExhibit($args)
    {
        $this->_log("hookAfterDeleteNeatlineExhibit: {$args['record']->id}");
        $this->deleteNeatlineExhibit($args['record']);
    }

    /**
     * Indexes a neatline record.
     *
     * @param $neatline
     * @return array
     */
    public function indexNeatlineRecord($neatlineRecord) {
        $exhibit = $neatlineRecord->getExhibit();
        if($exhibit) {
            $this->indexNeatlineExhibit($exhibit);
        }
    }

    /**
     * Deletes a neatline record from the index.
     *
     * @param $exhibit
     */
    public function deleteNeatlineRecord($neatlineRecord) {
        $exhibit = $neatlineRecord->getExhibit();
        if($exhibit) {
            $this->indexNeatlineExhibit($exhibit);
        }
    }

    /**
     * Indexes a neatline exhibit.
     *
     * @param $neatline
     * @return array
     */
    public function indexNeatlineExhibit($neatlineExhibit) {
        $doc = $this->getNeatlineExhibitDocument($neatlineExhibit);
        return $doc->index();
    }

    /**
     * Deletes a neatline from the index.
     *
     * @param $exhibit
     */
    public function deleteNeatlineExhibit($neatlineExhibit) {
        $doc = new Elasticsearch_Document($this->_docIndex, "neatline_exhibit_{$neatlineExhibit->id}");
        return $doc->delete();
    }

    /**
     * Returns a neatline as a document.
     *
     * @param $neatlineRecord
     * @return Elasticsearch_Document
     */
    public function getNeatlineExhibitDocument($neatlineExhibit) {
        $doc = new Elasticsearch_Document($this->_docIndex, "neatline_exhibit_{$neatlineExhibit->id}");
        $doc->setFields([
            'resulttype' => 'Neatline',
            'model'      => 'NeatlineExhibit',
            'modelid'    => $neatlineExhibit->id,
            'public'     => (bool) $neatlineExhibit->public,
            'title'      => $neatlineExhibit->title,
            'text'       => ($neatlineExhibit->private ? '' : $neatlineExhibit->narrative),
            'slug'       => $neatlineExhibit->slug,
            'url'        => 'neatline/show/'.$neatlineExhibit->slug,
            'created'    => $this->_getDate($neatlineExhibit->added),
            'updated'    => $this->_getDate($neatlineExhibit->modified)
        ]);

        $records = array();
        $neatlineRecords = $this->_getRecords($neatlineExhibit->id);
        foreach($neatlineRecords as $neatlineRecord) {
            $records[] = array(
                'id'      => $neatlineRecord->id,
                'title'   => $neatlineRecord->title,
                'body'    => $neatlineRecord->body,
                'created' => $this->_getDate($neatlineRecord->added),
                'updated' => $this->_getDate($neatlineRecord->modified)
            );
        }

        $doc->setField('neatline', array('records' => $records));

        return $doc;
    }

    /**
     * Retrieve records for a neatline exhibit.
     *
     * @return array
     */
    protected function _getRecords($exhibit_id) {
        $table = get_db()->getTable('NeatlineRecord');
        if(!$table) {
            return array();
        }
        $select = $table->getSelect()->where('exhibit_id = ?');
        $table->applySorting($select, 'id', 'ASC');
        $neatlineRecords = $table->fetchObjects($select, array($exhibit_id));
        return $neatlineRecords;
    }

    /**
     * Get array of documents to index.
     *
     * @return array|null
     */
    public function getNeatlineExhibitDocuments() {
        $db = get_db();
        $className = 'NeatlineExhibit';
        if(!class_exists($className)) {
            $this->_log("Unable to get documents because $className class does not exist!", Zend_Log::ERR);
            return null;
        }
        $table = $db->getTable($className);
        $select = $table->getSelect();
        $table->applySorting($select, 'id', 'ASC');
        $neatlineExhibits = $table->fetchObjects($select);

        $docs = [];
        foreach($neatlineExhibits as $neatlineExhibit) {
            $docs[] = $this->getNeatlineExhibitDocument($neatlineExhibit);
        }

        return $docs;
    }

    /**
     * Index all items.
     */
    public function indexAll() {
        $docs = $this->getNeatlineExhibitDocuments();
        if(isset($docs)) {
            $this->_log('indexAll neatline_exhibit: '.count($docs));
            Elasticsearch_Document::bulkIndex($docs);
        }
    }
}