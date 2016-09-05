<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class TwEntriesUsersMentionService extends aafwServiceBase {

    protected $tw_entries_users_mentions;
    protected $db;
    protected $logger;

    public function __construct() {
        $this->tw_entries_users_mentions = $this->getModel('TwEntriesUsersMentions');
        $this->db = aafwDataBuilder::newBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $mentioned_uid
     * @return null
     */
    public function getMaxObjectIdByMentionedUid($mentioned_uid) {
        if (!$mentioned_uid) {
            return null;
        }
        $sql = "SELECT max(object_id) max_id FROM tw_entries_users_mentions WHERE mentioned_uid = '{$mentioned_uid}' AND del_flg = 0";
        $result = $this->db->getBySQL($sql, array());

        return $result[0]['max_id'];
    }

    /**
     * @param $mentioned_uid
     * @return null
     */
    public function getMinObjectIdByMentionedUid($mentioned_uid) {
        if (!$mentioned_uid) {
            return null;
        }
        $sql = "SELECT min(object_id) min_id FROM tw_entries_users_mentions WHERE mentioned_uid = '{$mentioned_uid}' AND del_flg = 0";
        $result = $this->db->getBySQL($sql, array());

        return $result[0]['min_id'];
    }

    /**
     * @param $object_id
     * @return null
     */
    public function getMentionByObjectId($object_id) {
        if (!$object_id) {
            return null;
        }
        $filter = array('object_id' => $object_id);

        return $this->tw_entries_users_mentions->findOne($filter);
    }

    /**
     * @param $mentions
     * @return bool
     */
    public function insertMentions($mentions) {
        $count = 0;
        $sql = "INSERT INTO tw_entries_users_mentions (tw_uid, object_id, mentioned_uid, text, created_at, updated_at) VALUES";

        try {
            $this->tw_entries_users_mentions->begin();

            foreach ($mentions as $mention) {
                $mention_text = json_encode(array("text" => $mention['text']));
                $mention_text = $this->tw_entries_users_mentions->escapeForSQL($mention_text);

                $sql .= "('{$mention['tw_uid']}',{$mention['object_id']},{$mention['mentioned_uid']},'$mention_text', NOW(), NOW()),";
                $count++;

                if ($count == 50) {
                    $count = 0;
                    $sql = substr($sql, 0, strlen($sql) - 1);
                    $sql .= "ON DUPLICATE KEY UPDATE updated_at = NOW()";
                    $this->db->executeUpdate($sql);
                    $sql = "INSERT INTO tw_entries_users_mentions (tw_uid, object_id, mentioned_uid, text, created_at, updated_at) VALUES";
                }
            }

            if ($count > 0) {
                $sql = substr($sql, 0, strlen($sql) - 1);
                $sql .= "ON DUPLICATE KEY UPDATE updated_at = NOW()";
                $this->db->executeUpdate($sql);
            }

            $this->tw_entries_users_mentions->commit();

            return true;
        } catch (Exception $e) {
            $this->tw_entries_users_mentions->rollback();
            $this->logger->error("GetTwitterReplyTweets#insertMentions Insert Mentions Failed !");
            $this->logger->error($e);
            return false;
        }
    }
}