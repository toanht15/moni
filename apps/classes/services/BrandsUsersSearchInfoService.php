<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class BrandsUsersSearchInfoService extends aafwServiceBase {
    protected $brands_users_search_info;
    protected $db;

    public function __construct() {
        $this->brands_users_search_info = $this->getModel("BrandsUsersSearchInfos");
        $this->db = aafwDataBuilder::newBuilder();
    }

    /**
     * 下記のバッチ内でのみ使用されるクラス。
     * UpdateCpEntryCount・UpdateCpAnnounceCount・UpdateMsgDeliveredCount・UpdateMsgReadCount
     * @param $argv
     * @return mixed
     */
    public function getTargetDate($argv) {
        if($argv['from_date']) {
            if(!strptime($argv['from_date'], '%Y-%m-%d')) {
                $from_date = null;
            } else {
                $from_date = date('Y-m-d H:i:s', strtotime($argv['from_date'].' 00:00:00'));
            }
        } else{
            $from_date = date('Y-m-d H:i:s', strtotime('yesterday 00:00:00'));
        }

        if($argv['to_date']) {
            if(!strptime($argv['to_date'], '%Y-%m-%d')) {
                $to_date = null;
            } else {
                $to_date = date('Y-m-d H:i:s', strtotime($argv['to_date'].' 23:59:59'));
            }
        } else{
            $to_date = date('Y-m-d H:i:s', strtotime('yesterday 23:59:59'));
        }
        return array($from_date, $to_date);
    }

    /**
     * エントリーアクションを完了したユーザを一時テーブルに保存
     * @param $from_date, $to_date
     * @return $result
     */
    public function setTempCpEntryActionUsers($from_date, $to_date) {
        $query = "/* setTempCpEntryActionUsers - BrandsUsersSearchInfoService CREATE tmp_entry_action_users */
                    CREATE TEMPORARY TABLE tmp_entry_action_users(id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY)
                    SELECT DISTINCT R.id relation_id
                    FROM (SELECT cp_user_id, cp_action_id FROM cp_user_action_statuses
                           WHERE updated_at BETWEEN '{$from_date}' AND '{$to_date}' AND del_flg = 0 AND status = 1) S
                    INNER JOIN cp_users CU ON S.cp_user_id = CU.id AND CU.del_flg = 0
                    INNER JOIN cps C ON C.id = CU.cp_id AND C.del_flg = 0 AND C.type = 1
                    INNER JOIN brands_users_relations R ON R.brand_id = C.brand_id AND CU.user_id = R.user_id AND R.del_flg = 0 AND R.withdraw_flg = 0
                    INNER JOIN cp_actions A ON A.id = S.cp_action_id AND A.type IN (0, 5) AND A.order_no = 1 AND A.del_flg = 0
                    INNER JOIN cp_action_groups G ON G.id = A.cp_action_group_id AND G.order_no = 1 AND G.del_flg = 0";
        $result = $this->db->executeUpdate($query);
        return $result;
    }

    /**
     * 一時テーブルのidの最大値を取得
     * @return $max_id
     */
    public function getTempCpEntryMaxId() {
        $query = "/* getTempCpEntryMaxId - BrandsUsersSearchInfoService SELECT tmp_entry_action_users */
                    SELECT ifnull(max(id),0) max_id FROM tmp_entry_action_users";
        $max_id = $this->db->getBySQL($query,array())[0]['max_id'];
        return $max_id;
    }

    /**
     * 処理対象となるbrands_users_relation_idを取得
     * @param $i,$maxRange
     * @return $temp_entry_info
     */
    private function getTempCpEntryInfo($i,$maxRange) {
        $query = "/* getTempCpEntryInfo - BrandsUsersSearchInfoService SELECT tmp_entry_action_users */
                    SELECT relation_id FROM tmp_entry_action_users
                    WHERE id between {$i} AND {$maxRange}";
        $temp_entry_info = $this->db->getBySQL($query,array());
        return $temp_entry_info;
    }

    /**
     * 集計テーブルの更新
     * @param $i,$maxRange
     * @return $result
     */
    public function insertCpEntryActionUsers($i,$maxRange) {
        $select_sql;
        $temp_entry_info = $this->getTempCpEntryInfo($i,$maxRange);

        foreach($temp_entry_info as $temp_info) {
            if(isset($select_sql)) {
                $select_sql .= " UNION ALL ";
            }
            $select_sql .= " SELECT R.id as id, COUNT(S.id) AS cnt FROM brands_users_relations R
                INNER JOIN cp_users CU ON CU.user_id = R.user_id AND CU.del_flg = 0
                INNER JOIN cps C ON C.id = CU.cp_id AND R.brand_id = C.brand_id AND C.del_flg = 0 AND C.type = 1
                INNER JOIN cp_user_action_statuses S ON S.cp_user_id = CU.id AND S.del_flg = 0 AND S.status = 1
                INNER JOIN cp_actions A ON A.id = S.cp_action_id AND A.type IN (0, 5) AND A.order_no = 1 AND A.del_flg = 0
                INNER JOIN cp_action_groups G ON G.id = A.cp_action_group_id AND G.order_no = 1 AND G.del_flg = 0
                WHERE R.id = {$temp_info['relation_id']} ";
        }
        if($select_sql) {
            $insert_sql = "/* insertCpEntryActionUsers BrandsUsersSearchInfoService INSERT brands_users_search_info */
            INSERT INTO brands_users_search_info(brands_users_relation_id, cp_entry_count, created_at, updated_at)
            SELECT UNI.id, UNI.cnt, NOW(), NOW() FROM (".$select_sql.") UNI
            ON DUPLICATE KEY UPDATE cp_entry_count = VALUES(cp_entry_count),updated_at = NOW()";
            $result = $this->db->executeUpdate($insert_sql);
            return $result;
        } else {
            return true;
        }
    }

    /**
     * 当選通知アクションを完了したユーザを一時テーブルに保存
     * @param $from_date, $to_date
     * @return $result
     */
    public function setTempCpAnnounceActionUsers($from_date, $to_date) {
        $query = "/* setTempCpAnnounceActionUsers BrandsUsersSearchInfoService CREATE tmp_announce_action_users */
                    CREATE TEMPORARY TABLE tmp_announce_action_users(id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY)
                    SELECT DISTINCT R.id relation_id
                    FROM (SELECT cp_user_id, cp_action_id FROM cp_user_action_messages
                           WHERE created_at BETWEEN '{$from_date}' AND '{$to_date}' AND del_flg = 0) M
                    INNER JOIN cp_users CU ON M.cp_user_id = CU.id AND CU.del_flg = 0
                    INNER JOIN cps C ON C.id = CU.cp_id AND C.del_flg = 0 AND C.type = 1
                    INNER JOIN brands_users_relations R ON R.brand_id = C.brand_id AND CU.user_id = R.user_id AND R.del_flg = 0 AND R.withdraw_flg = 0
                    INNER JOIN cp_actions A ON A.id = M.cp_action_id AND A.type IN (3,25) AND A.del_flg = 0";
        $result = $this->db->executeUpdate($query);
        return $result;
    }

    /**
     * 一時テーブルのidの最大値を取得
     * @return $max_id
     */
    public function getTempCpAnnounceMaxId() {
        $query = "/* getTempCpAnnounceMaxId BrandsUsersSearchInfoService SELECT tmp_announce_action_users */
                    SELECT ifnull(max(id),0) max_id FROM tmp_announce_action_users";
        $max_id = $this->db->getBySQL($query,array())[0]['max_id'];
        return $max_id;
    }

    /**
     * 処理対象となるbrands_users_relation_idを取得
     * @param $i,$maxRange
     * @return $temp_announce_info
     */
    private function getTempCpAnnounceInfo($i,$maxRange) {
        $query = "/* getTempCpAnnounceInfo BrandsUsersSearchInfoService SELECT tmp_announce_action_users */
                    SELECT relation_id FROM tmp_announce_action_users
                    WHERE id between {$i} AND {$maxRange}";
        $temp_entry_info = $this->db->getBySQL($query,array());
        return $temp_entry_info;
    }

    /**
     * 集計テーブルの更新
     * @param $i,$maxRange
     * @return $result
     */
    public function insertCpAnnounceActionUsers($i,$maxRange) {
        $select_sql;
        $temp_announce_info = $this->getTempCpAnnounceInfo($i,$maxRange);

        foreach($temp_announce_info as $temp_info) {
            if(isset($select_sql)) {
                $select_sql .= " UNION ALL ";
            }
            $select_sql .= " SELECT R.id as id, COUNT(M.id) AS cnt FROM brands_users_relations R
                INNER JOIN cp_users CU ON CU.user_id = R.user_id AND CU.del_flg = 0
                INNER JOIN cps C ON C.id = CU.cp_id AND R.brand_id = C.brand_id AND C.del_flg = 0 AND C.type = 1
                INNER JOIN cp_user_action_messages M ON M.cp_user_id = CU.id AND M.del_flg = 0
                INNER JOIN cp_actions A ON A.id = M.cp_action_id AND A.del_flg = 0 AND A.type IN (3,25)
                WHERE R.id = {$temp_info['relation_id']} ";
        }
        if($select_sql) {
            $insert_sql = "/* insertCpAnnounceActionUsers BrandsUsersSearchInfoService INSERT brands_users_search_info */
            INSERT INTO brands_users_search_info(brands_users_relation_id, cp_announce_count, created_at, updated_at)
            SELECT UNI.id, UNI.cnt, NOW(), NOW() FROM (".$select_sql.") UNI
            ON DUPLICATE KEY UPDATE cp_announce_count = VALUES(cp_announce_count),updated_at = NOW()";
            $result = $this->db->executeUpdate($insert_sql);
            return $result;
        } else {
            return true;
        }
    }

    /**
     * 指定した期間中に送信があったメッセージの情報を取得
     * @return $message_information
     */
    public function getDeliveredMessageInfo($from_date, $to_date) {
        $filter = array(
            'from_date' => $from_date,
            'to_date' => $to_date
        );
        $message_information = $this->db->getDeliveredMessageInfo($filter);
        return $message_information;
    }

    /**
     * メッセージを受信したユーザを格納する一時テーブルを作成
     * @return $result
     */
    public function createTempMsgDelivered() {
        $query = "/* createTempMsgDelivered BrandsUsersSearchInfoService CREATE tmp_msg_delivered_users */
                    CREATE TEMPORARY TABLE tmp_msg_delivered_users(
                        id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        relation_id bigint(20)
                    )";
        $result = $this->db->executeUpdate($query);
        return $result;
    }

    /**
     * メッセージを受信したユーザを一時テーブルに保存
     * @param $message_info
     * @return $result
     */
    public function insertTempMsgDeliveredUsers($message_info) {
        $query = "/* insertTempMsgDeliveredUsers BrandsUsersSearchInfoService INSERT tmp_msg_delivered_users */
                    INSERT INTO tmp_msg_delivered_users(relation_id)
                    SELECT DISTINCT R.id relation_id
                    FROM cp_message_delivery_targets T
                    INNER JOIN cp_users CU ON T.user_id = CU.user_id AND CU.del_flg = 0
                    INNER JOIN cp_user_action_messages M ON M.cp_user_id = CU.id AND M.cp_action_id = T.cp_action_id AND M.cp_action_id = {$message_info['cp_action_id']} AND M.del_flg = 0
                    INNER JOIN cp_actions A ON A.id = M.cp_action_id AND A.del_flg = 0 AND A.type != 25
                    INNER JOIN brands_users_relations R ON CU.user_id = R.user_id AND R.brand_id = {$message_info['brand_id']} AND R.del_flg = 0 AND R.withdraw_flg = 0
                    WHERE T.cp_message_delivery_reservation_id = {$message_info['reservation_id']} AND T.del_flg = 0 AND T.status = 1";
        $result = $this->db->executeUpdate($query);
        return $result;
    }

    /**
     * 一時テーブルのidの最大値を取得
     * @return $max_id
     */
    public function getTempMsgDeliveredMaxId() {
        $query = "/* getTempMsgDeliveredMaxId BrandsUsersSearchInfoService SELECT tmp_msg_delivered_users */
                    SELECT ifnull(max(id),0) max_id FROM tmp_msg_delivered_users";
        $max_id = $this->db->getBySQL($query,array())[0]['max_id'];
        return $max_id;
    }

    /**
     * 処理対象となるbrands_users_relation_idを取得
     * @param $i,$maxRange
     * @return $temp_delivered_info
     */
    private function getTempMsgDeliveredInfo($i,$maxRange) {
        $query = "/* getTempMsgDeliveredInfo BrandsUsersSearchInfoService SELECT tmp_msg_delivered_users */
                    SELECT relation_id FROM tmp_msg_delivered_users
                    WHERE id between {$i} AND {$maxRange}";
        $temp_delivered_info = $this->db->getBySQL($query,array());
        return $temp_delivered_info;
    }

    /**
     * 集計テーブルの更新
     * @param $i,$maxRange
     * @return $result
     */
    public function insertMsgDeliveredUsers($i,$maxRange) {
        $select_sql;
        $temp_delivered_info = $this->getTempMsgDeliveredInfo($i,$maxRange);

        foreach($temp_delivered_info as $temp_info) {
            if(isset($select_sql)) {
                $select_sql .= " UNION ALL ";
            }
            $select_sql .= " SELECT R.id as id, COUNT(T.id) AS cnt FROM brands_users_relations R
                INNER JOIN cp_users CU ON CU.user_id = R.user_id AND CU.del_flg = 0
                INNER JOIN cps C ON C.id = CU.cp_id AND R.brand_id = C.brand_id AND C.del_flg = 0
                INNER JOIN cp_user_action_messages M ON M.cp_user_id = CU.id AND M.del_flg = 0
                INNER JOIN cp_actions A ON A.id = M.cp_action_id AND A.del_flg = 0 AND A.type != 25
                INNER JOIN cp_message_delivery_targets T ON T.user_id = R.user_id AND T.cp_action_id = A.id AND T.del_flg = 0 AND T.status = 1
                WHERE R.id = {$temp_info['relation_id']} ";
        }
        if($select_sql) {
            $insert_sql = "/* insertMsgDeliveredUsers BrandsUsersSearchInfoService INSERT brands_users_search_info */
            INSERT INTO brands_users_search_info(brands_users_relation_id, message_delivered_count, created_at, updated_at)
            SELECT UNI.id, UNI.cnt, NOW(), NOW() FROM (".$select_sql.") UNI
            ON DUPLICATE KEY UPDATE message_delivered_count = VALUES(message_delivered_count),updated_at = NOW()";
            $result = $this->db->executeUpdate($insert_sql);
            return $result;
        } else {
            return true;
        }
    }

    /**
     * メッセージを閲覧したユーザを格納する一時テーブルを作成
     * @return $result
     */
    public function createTempMsgRead() {
        $query = "/* createTempMsgRead BrandsUsersSearchInfoService CREATE tmp_msg_read_users */
                    CREATE TEMPORARY TABLE tmp_msg_read_users(
                        id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        relation_id bigint(20)
                    )";
        $result = $this->db->executeUpdate($query);
        return $result;
    }

    /**
     * メッセージを閲覧したユーザを一時テーブルに保存
     * @param $message_info
     * @return $result
     */
    public function insertTempMsgReadUsers($message_info) {
        $query = "/* insertTempMsgReadUsers BrandsUsersSearchInfoService INSERT tmp_msg_delivered_users */
                    INSERT INTO tmp_msg_read_users(relation_id)
                    SELECT DISTINCT R.id relation_id
                    FROM cp_message_delivery_targets T
                    INNER JOIN cp_users CU ON T.user_id = CU.user_id AND CU.del_flg = 0
                    INNER JOIN cp_user_action_messages M ON M.cp_user_id = CU.id AND M.cp_action_id = T.cp_action_id AND M.cp_action_id = {$message_info['cp_action_id']} AND M.del_flg = 0 AND M.read_flg = 1
                    INNER JOIN cp_actions A ON A.id = M.cp_action_id AND A.del_flg = 0 AND A.type != 25
                    INNER JOIN brands_users_relations R ON T.user_id = R.user_id AND R.brand_id = {$message_info['brand_id']} AND R.del_flg = 0 AND R.withdraw_flg = 0
                    WHERE T.cp_message_delivery_reservation_id = {$message_info['reservation_id']} AND T.del_flg = 0 AND T.status = 1";
        $result = $this->db->executeUpdate($query);
        return $result;
    }

    /**
     * 一時テーブルのidの最大値を取得
     * @return $max_id
     */
    public function getTempMsgReadMaxId() {
        $query = "/* getTempMsgReadMaxId BrandsUsersSearchInfoService SELECT tmp_msg_read_users */
                    SELECT ifnull(max(id),0) max_id FROM tmp_msg_read_users";
        $max_id = $this->db->getBySQL($query,array())[0]['max_id'];
        return $max_id;
    }

    /**
     * 処理対象となるbrands_users_relation_idを取得
     * @param $i,$maxRange
     * @return $temp_read_info
     */
    private function getTempMsgReadInfo($i,$maxRange) {
        $query = "/* getTempMsgReadInfo BrandsUsersSearchInfoService SELECT tmp_msg_read_users */
                    SELECT relation_id FROM tmp_msg_read_users
                    WHERE id between {$i} AND {$maxRange}";
        $temp_read_info = $this->db->getBySQL($query,array());
        return $temp_read_info;
    }

    /**
     * 集計テーブルの更新
     * @param $i,$maxRange
     * @return $result
     */
    public function insertMsgReadUsers($i,$maxRange) {
        $select_sql;
        $temp_delivered_info = $this->getTempMsgReadInfo($i,$maxRange);
        foreach($temp_delivered_info as $temp_info) {
            if(isset($select_sql)) {
                $select_sql .= " UNION ALL ";
            }
            $select_sql .= " SELECT R.id as id, COUNT(T.id) AS cnt FROM brands_users_relations R
                    INNER JOIN cp_users CU ON CU.user_id = R.user_id AND CU.del_flg = 0
                    INNER JOIN cps C ON C.id = CU.cp_id AND R.brand_id = C.brand_id AND C.del_flg = 0
                    INNER JOIN cp_user_action_messages M ON M.cp_user_id = CU.id AND M.del_flg = 0 AND M.read_flg = 1
                    INNER JOIN cp_actions A ON A.id = M.cp_action_id AND A.del_flg = 0 AND A.type != 25
                    INNER JOIN cp_message_delivery_targets T ON T.user_id = R.user_id AND T.cp_action_id = A.id AND T.del_flg = 0 AND T.status = 1
                    WHERE R.id = {$temp_info['relation_id']} ";
        }
        if($select_sql) {
            $insert_sql = "/* insertMsgReadUsers BrandsUsersSearchInfoService INSERT brands_users_search_info */
            INSERT INTO brands_users_search_info(brands_users_relation_id, message_read_count, created_at, updated_at)
            SELECT UNI.id, UNI.cnt, NOW(), NOW() FROM (".$select_sql.") UNI
            ON DUPLICATE KEY UPDATE message_read_count = VALUES(message_read_count),updated_at = NOW()";
            $result = $this->db->executeUpdate($insert_sql);
            return $result;
        } else {
            return true;
        }
    }
}