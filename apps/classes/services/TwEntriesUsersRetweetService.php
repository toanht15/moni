<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class TwEntriesUsersRetweetService extends aafwServiceBase {
    protected $tw_entries_users_retweets;
    protected $db;
    protected $logger;

    public function __construct() {
        $this->tw_entries_users_retweets = $this->getModel('TwEntriesUsersRetweets');
        $this->db = aafwDataBuilder::newBuilder();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $retweets
     * @param $entry_object_id
     */
    public function insertRetweets($retweets, $entry_object_id){
        $count = 0;
        $sql = "INSERT INTO tw_entries_users_retweets(tw_uid, object_id, entry_object_id, text, created_at, updated_at) VALUES";
        $tw_entries_users_retweets = aafwEntityStoreFactory::create('TwEntriesUsersRetweets');

        try {
            $this->tw_entries_users_retweets->begin();

            foreach ($retweets as $retweet) {
                if (!is_array($retweet)) {
                    continue;
                }

                //escape for Description
                $retweet_text = json_encode(array('description' => $retweet['user']['description']));
                $retweet_text = $tw_entries_users_retweets->escapeForSQL($retweet_text);

                $sql .= " ('{$retweet['user']['id']}', {$retweet['id']}, {$entry_object_id}, '{$retweet_text}',NOW(),NOW()),";
                $count++;

                if ($count == 50) {
                    $count = 0;
                    $sql = substr($sql, 0, strlen($sql) - 1);
                    $sql .= " ON DUPLICATE KEY UPDATE updated_at = NOW()";
                    $this->db->executeUpdate($sql);
                    $sql = "INSERT INTO tw_entries_users_retweets(tw_uid, object_id, entry_object_id, text, created_at, updated_at) VALUES";
                }
            }

            if ($count > 0) {
                $sql = substr($sql, 0, strlen($sql) - 1);
                $sql .= " ON DUPLICATE KEY UPDATE updated_at = NOW()";
                $this->db->executeUpdate($sql);
            }

            $this->tw_entries_users_retweets->commit();
        } catch (Exception $e) {
            $this->tw_entries_users_retweets->rollback();
            $this->logger->error("TwEntriesUsersRetweetService#updateRetweets failed! $entry_object_id = {$entry_object_id}");
            $this->logger->error($e);
        }
    }
}