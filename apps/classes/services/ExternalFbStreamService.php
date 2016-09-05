<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class ExternalFbStreamService extends aafwServiceBase
{
    protected $externalFbStreams;
    protected $db;

    public function __construct()
    {
        $this->externalFbStreams = $this->getModel('ExternalFbStreams');
        $this->db = aafwDataBuilder::newBuilder();
    }

    /**
     * Next URLをデータベースに更新する
     * @param $streamId
     * @param $url
     */
    public function updateUrl($streamId, $url)
    {
        $sql = "UPDATE external_fb_streams SET url = '{$url}' WHERE id = {$streamId}";
        $this->db->executeUpdate($sql);
    }

    public function getAllStreams()
    {
        $sql = "SELECT id,social_media_account_id,url from external_fb_streams";

        return $this->db->getBySQL($sql, []);
    }
}