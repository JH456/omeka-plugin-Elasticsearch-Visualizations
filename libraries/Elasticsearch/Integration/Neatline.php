<?php

class Elasticsearch_Integration_Neatline extends Elasticsearch_Integration_BaseIntegration
{
    protected $_hooks = array(
        'after_save_neatline_record',
        'after_delete_neatline_record',
        'after_save_neatline_exhibit',
        'before_delete_neatline_exhibit',
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
        $neatlineRecords = $this->_getRecords($args['record']->id);
        $this->indexNeatlineExhibit($args['record']);
        $this->indexNeatlineRecords($neatlineRecords); // ensure records reflect latest public status
    }

    /**
     * Hook for when a neatline exhibit is about to be deleted.
     *
     * This hook should delete an exhibit's child records.
     *
     * NOTE: unfortunately, NeatlineExhibit's beforeDelete() method deletes all child records before
     * this plugin/hook method is called, so it's not possible to simply query the database for the records
     * and then issue the delete request to elasticsearch. Instead, we query elasticsearch for the exhibit document,
     * which contains the list of the associated records that need to be deleted.
     *
     * @param array $args
     */
    public function hookBeforeDeleteNeatlineExhibit($args)
    {
        $neatlineExhibit = $args['record'];
        $this->_log("hookBeforeDeleteNeatlineExhibit: {$neatlineExhibit->id}");

        $doc = new Elasticsearch_Document($this->_docIndex, "neatline_exhibit_{$neatlineExhibit->id}");
        $res = $doc->get();
        $this->_log(var_export($res,1), Zend_Log::DEBUG);
        $has_records = $res && isset($res['_source']) && isset($res['_source']['neatlineRecords']);

        if($has_records) {
            foreach($res['_source']['neatlineRecords'] as $neatlineRecordId) {
                $this->deleteNeatlineRecordById($neatlineRecordId);
            }
        }
    }

    /**
     * Hook for when a neatline exhibit is being deleted.
     *
     * @param array $args
     */
    public function hookAfterDeleteNeatlineExhibit($args)
    {
        $this->deleteNeatlineExhibit($args['record']);
    }

    /**
     * Indexes a neatline record.
     *
     * @param $neatline
     * @return array
     */
    public function indexNeatlineRecord($neatlineRecord) {
        $doc = $this->getNeatlineRecordDocument($neatlineRecord);
        return $doc->index();
    }

    /**
     * Indexes an array of neatline records.
     *
     * @param $neatline
     */
    public function indexNeatlineRecords($neatlineRecords) {
        foreach($neatlineRecords as $neatlineRecord) {
            $this->indexNeatlineRecord($neatlineRecord);
        }
    }

    /**
     * Deletes a neatline record from the index.
     *
     * @param $exhibit
     */
    public function deleteNeatlineRecord($neatlineRecord) {
        $this->deleteNeatlineRecordById($neatlineRecord->id);
    }

    /**
     * Deletes a neatline record from the index.
     *
     * @param $exhibit
     */
    public function deleteNeatlineRecordById($neatlineRecordId) {
        $this->_log("deleting neatline record $neatlineRecordId");
        $doc = new Elasticsearch_Document($this->_docIndex, "neatline_record_{$neatlineRecordId}");
        $doc->delete();
    }

    /**
     * Deletes an array of neatline records.
     *
     * @param $neatline
     * @return array
     */
    public function deleteNeatlineRecords($neatlineRecords) {
        foreach($neatlineRecords as $neatlineRecord) {
            $this->deleteNeatlineRecord($neatlineRecord);
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
        $this->_log("deleting neatline exhibit {$neatlineExhibit->id}");
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
            'resulttype' => 'NeatlineExhibit',
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

        $recordIds = [];
        $neatlineRecords = $this->_getRecords($neatlineExhibit->id);
        foreach($neatlineRecords as $r) {
            $recordIds[] = $r->id;
        }
        $doc->setField('neatlineRecords', $recordIds);

        return $doc;
    }

    /**
     * Get array of documents to index.
     *
     * @return array|null
     */
    public function getNeatlineExhibitDocuments() {
        $docs = [];
        $neatlineExhibits = $this->_fetchObjects('NeatlineExhibit');
        foreach($neatlineExhibits as $neatlineExhibit) {
            $docs[] = $this->getNeatlineExhibitDocument($neatlineExhibit);
        }
        return $docs;
    }

    /**
     * Returns a neatline as a document.
     *
     * @param $neatlineRecord
     * @return Elasticsearch_Document
     */
    public function getNeatlineRecordDocument($neatlineRecord) {
        $doc = new Elasticsearch_Document($this->_docIndex, "neatline_record_{$neatlineRecord->id}");
        $doc->setFields([
            'resulttype' => 'NeatlineRecord',
            'model'      => 'NeatlineRecord',
            'modelid'    => $neatlineRecord->id,
            'title'      => $neatlineRecord->title,
            'text'       => $neatlineRecord->body,
            'slug'       => $neatlineRecord->slug,
            'public'     => false,
            'created'    => $this->_getDate($neatlineRecord->added),
            'updated'    => $this->_getDate($neatlineRecord->modified)
        ]);

        $neatlineExhibit = $neatlineRecord->getExhibit();
        if($neatlineExhibit) {
            $doc->setField('public', (bool) $neatlineExhibit->public);
            $doc->setField('neatline', $neatlineExhibit->title);
            $doc->setField('url', "neatline/show/{$neatlineExhibit->slug}#records/{$neatlineRecord->id}");
        }

        return $doc;
    }

    /**
     * Get array of documents to index.
     *
     * @return array|null
     */
    public function getNeatlineRecordDocuments() {
        $docs = [];
        $neatlineRecords = $this->_fetchObjects('NeatlineRecord');
        foreach($neatlineRecords as $neatlineRecord) {
            $docs[] = $this->getNeatlineRecordDocument($neatlineRecord);
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

        $docs = $this->getNeatlineRecordDocuments();
        if(isset($docs)) {
            $this->_log('indexAll neatline_record: '.count($docs));
            Elasticsearch_Document::bulkIndex($docs);
        }
    }

    /**
     * Deletes all items from the index.
     */
    public function deleteAll() {
        $this->_deleteByQueryModel('NeatlineRecord');
        $this->_deleteByQueryModel('NeatlineExhibit');
    }

    /**
     * Retrieve records for a neatline exhibit.
     *
     * @return array
     */
    protected function _getRecords($exhibitId) {
        $table = get_db()->getTable('NeatlineRecord');
        if(!$table) {
            return array();
        }
        $select = $table->getSelect()->where('exhibit_id = ?');
        $table->applySorting($select, 'id', 'ASC');
        $neatlineRecords = $table->fetchObjects($select, array($exhibitId));
        return $neatlineRecords;
    }
}