<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class ExternalFbEntryService extends aafwServiceBase
{
    protected $externalFbEntries;
    protected $db;

    public function __construct()
    {
        $this->externalFbEntries = $this->getModel('ExternalFbEntries');
        $this->db = aafwDataBuilder::newBuilder();
    }

    /**
     * 新規エントリを作成する
     * @param $entry
     * @return mixed
     */
    public function addEntry($entry)
    {
        $addEntry = $this->externalFbEntries->createEmptyObject();
        $addEntry->stream_id = $entry['stream_id'];
        $addEntry->post_id = $entry['post_id'];
        $addEntry->object_id = $entry['object_id'];
        $addEntry->type = $entry['type'];
        $addEntry->status_type = $entry['status_type'];
        $addEntry->link = $entry['link'];

        return $this->externalFbEntries->save($addEntry);
    }
}