<?php

use PHPUnit\Framework\TestCase;

class Elasticsearch_Document_Test extends TestCase {

    public function testParams() {
        $docIndex = 'omeka';
        $docType = 'item';
        $docId = 123;
        $fields = ['title' => 'foo', 'tags' => ['x', 'y', 'z']];

        $doc = new Elasticsearch_Document($docIndex, $docType, $docId);
        $doc->setFields($fields);
        $expected = [
            'index' => $docIndex,
            'type' => $docType,
            'id' => $docId,
            'body' => $fields
        ];
        $this->assertEquals($expected, $doc->getParams());
    }
}