<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class ExternalTwStreamService extends aafwServiceBase
{
    protected $db;
    protected $external_tw_streams;

    public function __construct()
    {
        $this->db = aafwDataBuilder::newBuilder();
        $this->external_tw_streams = $this->getModel('ExternalTwStreams');
    }

    /**
     * @return array
     */
    public function getAllStreams(){
        $result = $this->external_tw_streams->findAll();

        return $result;
    }

    /**
     * @param $streamId
     * @param $url
     * @return mixed
     */
    public function updateUrl($streamId, $url){
        $sql = "UPDATE external_tw_streams SET url = '{$url}' WHERE id = {$streamId}";

        return $this->db->executeUpdate($sql);
    }

}