<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class TwEntriesUsersReplyService extends aafwServiceBase {

    protected $tw_entries_users_replies;
    protected $db;
    protected $logger;

    public function __construct() {
        $this->tw_entries_users_replies = $this->getModel('TwEntriesUsersReplies');
        $this->db = aafwDataBuilder::newBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $replies
     * @return bool
     */
    public function insertReplies($replies) {
        $count = 0;
        $sql = "INSERT INTO tw_entries_users_replies (mention_id, object_id, entry_object_id, created_at, updated_at) VALUES";

        try {
            $this->tw_entries_users_replies->begin();

            foreach ($replies as $reply) {
                $sql .= "({$reply['mention_id']},{$reply['object_id']}, {$reply['entry_object_id']}, NOW(), NOW()),";
                $count++;

                if ($count == 50) {
                    $count = 0;
                    $sql = substr($sql, 0, strlen($sql) - 1);
                    $sql .= "ON DUPLICATE KEY UPDATE updated_at = NOW()";
                    $this->db->executeUpdate($sql);
                    $sql = "INSERT INTO tw_entries_users_replies (mention_id, object_id, entry_object_id, created_at, updated_at) VALUES";
                }
            }

            if ($count > 0) {
                $sql = substr($sql, 0, strlen($sql) - 1);
                $sql .= "ON DUPLICATE KEY UPDATE updated_at = NOW()";
                $this->db->executeUpdate($sql);
            }

            $this->tw_entries_users_replies->commit();
            return true;
        } catch (Exception $e) {
            $this->tw_entries_users_replies->rollback();
            $this->logger->error('TwEntriesUsersReplyService#insertReplies Insert Replies Failed !');
            $this->logger->error($e);
            return false;
        }
    }
}