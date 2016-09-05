<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class TwitterFollowService extends aafwServiceBase {

    protected $twitter_follows;
    protected $db;
    protected $logger;

    public function __construct() {
        $this->twitter_follows = $this->getModel('TwitterFollows');
        $this->db = aafwDataBuilder::newBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function insertTwitterFollows($follower_ids, $stream_id) {
        $count = 0;
        $sql = "INSERT INTO twitter_follows(stream_id, follower_id , created_at, updated_at) VALUES ";

        try {
            $this->twitter_follows->begin();

            foreach ($follower_ids as $follower_id) {
                $count++;
                $sql .= "({$stream_id}, {$follower_id}, NOW(),NOW()),";

                if ($count == 100) {
                    $sql = substr($sql, 0, strlen($sql) - 1);
                    $sql .= " ON DUPLICATE KEY UPDATE updated_at = NOW()";
                    $this->db->executeUpdate($sql);
                    $sql = "INSERT INTO twitter_follows(stream_id, follower_id , created_at, updated_at) VALUES ";
                    $count = 0;
                }
            }

            if ($count > 0) {
                $sql = substr($sql, 0, strlen($sql) - 1);
                $sql .= " ON DUPLICATE KEY UPDATE updated_at = NOW()";
                $this->db->executeUpdate($sql);
            }

            $this->twitter_follows->commit();
        } catch (Exception $e) {
            $this->twitter_follows->rollback();
            $this->logger->error('Insert TwitterFollows error!');
            $this->logger->error($e);
        }
    }

    public function getTwitterFollowByStreamIdAndFollowerId($stream_id, $follower_id){
        $filter = array(
            'stream_id' => $stream_id,
            'follower_id' => $follower_id
        );

        return $this->twitter_follows->findOne($filter);
    }

    public function isEmptyTable() {
        $twitter_follows = $this->db->getBySQL('SELECT id FROM twitter_follows LIMIT 1', array());
        if(!$twitter_follows[0]) {
            return true;
        }
        return false;
    }
}