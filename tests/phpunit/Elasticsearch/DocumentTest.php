<?php

use PHPUnit\Framework\TestCase;

class Elasticsearch_Document_Test extends TestCase {

    public function testParams() {
        $docIndex = 'omeka';
        $docId = 123;
        $fields = [
            'title' => 'foo',
            'description' => 'bar',
            'tags' => ['x', 'y', 'z']
        ];

        $doc = new Elasticsearch_Document($docIndex, $docId);
        $doc->setFields($fields);
        $expected = [
            'index' => $docIndex,
            'type' => 'doc',
            'id' => $docId,
            'body' => $fields
        ];
        $this->assertEquals($expected, $doc->getParams());
    }
}