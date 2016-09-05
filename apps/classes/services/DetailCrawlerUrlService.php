<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class DetailCrawlerUrlService extends aafwServiceBase {
    protected $detailCrawlerUrls;
    protected $db;
    protected $logger;

    public function __construct() {
        $this->detailCrawlerUrls = $this->getModel('DetailCrawlerUrls');
        $this->db = aafwDataBuilder::newBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function getDetailCrawlerUrlById($id) {

        return $this->detailCrawlerUrls->findOne(array('id' => $id));
    }

    public function checkExistFbLike($objectId, $type) {
        $condition = array(
            'object_id' => $objectId,
            'type' => $type,
            'crawler_type' => DetailCrawlerUrl::CRAWLER_TYPE_FACEBOOK,
            'data_type' => DetailCrawlerUrl::DATA_TYPE_LIKE
        );
        $result = $this->detailCrawlerUrls->findOne($condition);

        return $result;
    }

    public function checkExistFbComment($objectId, $type) {
        $condition = array(
            'object_id' => $objectId,
            'type' => $type,
            'crawler_type' => DetailCrawlerUrl::CRAWLER_TYPE_FACEBOOK,
            'data_type' => DetailCrawlerUrl::DATA_TYPE_COMMENT
        );

        return $this->detailCrawlerUrls->findOne($condition);
    }

    /**
     * @param $objectId
     * @param $type
     * @param $crawler_type
     * @param $data_type
     * @param $url
     */
    public function updateDetailCrawlerUrl($objectId, $type, $crawler_type, $data_type, $url) {
        try {
            $this->detailCrawlerUrls->begin();

            $updateUrl = $this->createDataForUpdate($objectId, $type, $crawler_type, $data_type, $url);

            // detail_crawler_urlテブールに更新する
            $sql = "INSERT INTO detail_crawler_urls(object_id,type,crawler_type,data_type,url,created_at,updated_at) VALUES";
            $sql .= "({$updateUrl['object_id']},{$updateUrl['type']},{$updateUrl['crawler_type']},{$updateUrl['data_type']},'{$updateUrl['url']}',NOW(),NOW())";
            $sql .= "ON DUPLICATE KEY UPDATE data_type = VALUES (data_type), url = VALUES (url), updated_at = NOW()";

            $this->db->executeUpdate($sql);

            $this->detailCrawlerUrls->commit();
        } catch (Exception $e) {
            $this->detailCrawlerUrls->rollback();
            $this->logger->error("DetailCrawlerUrlService#updateDetailCrawlerUrl: update detail crawler url error! ");
            $this->logger->error($e);
        }
    }

    private function createDataForUpdate($objectId, $type, $crawler_type, $data_type, $url) {
        $updateUrl = array();
        $updateUrl['object_id'] = $objectId;
        $updateUrl['type'] = $type;
        $updateUrl['crawler_type'] = $crawler_type;
        $updateUrl['data_type'] = $data_type;
        $updateUrl['url'] = $url;

        return $updateUrl;
    }

    public function getDetailCrawlerUrlByObjectId($object_id, $crawler_type, $data_type){
        $filter = array(
            'object_id' => $object_id,
            'crawler_type' => $crawler_type,
            'data_type' => $data_type
        );

        return $this->detailCrawlerUrls->findOne($filter);
    }

}