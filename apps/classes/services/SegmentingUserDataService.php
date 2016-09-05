<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.SegmentService');

class SegmentingUserDataService extends aafwServiceBase {
    private $default_args = array(array('__NOFETCH__'));

    public function __construct() {
        $this->data_builder = aafwDataBuilder::newBuilder();
        $this->create_sql_service = $this->getService("SegmentCreateSqlService");
    }

    /**
     * @param $page_info
     * @return array
     */
    public function countRemainingUsers($page_info) {
        $this->create_sql_service->resetCurrentParameter();
        $fetching_user_query = $this->create_sql_service->getUserSql($page_info, array(), null, true);
        $fetching_user_query .= " AND ( relate.id NOT IN (" . $this->getTmpSegmentingUsersQuery() . ") )";

        return $this->data_builder->getBySQL($fetching_user_query, array());
    }

    /**
     * @param $page_info
     * @param $cur_provision
     * @param $remaining_flg
     * @return string
     */
    public function getSegmentingUsers($page_info, $cur_provision, $remaining_flg = false) {
        if (!$remaining_flg && $this->isEmptyProvision($cur_provision)) {
            return array();
        }

        $this->create_sql_service->resetCurrentParameter();
        $fetching_user_query = $this->create_sql_service->getUserSql($page_info, $cur_provision);
        $fetching_user_query .= " AND ( relate.id NOT IN (" . $this->getTmpSegmentingUsersQuery() . ") )";

        return $this->data_builder->getBySQL($fetching_user_query, $this->default_args);
    }

    /***********************************************************
     * Temporary table's functions and procedures
     ***********************************************************/

    /**
     * @throws aafwException
     */
    public function createTmpSegmentingUsers() {
        $query = "/* SegmentingUserData CREATE TEMPORARY TABLE tmp_segmenting_users */
                    CREATE TEMPORARY TABLE tmp_segmenting_users (
                        id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        bur_id int(20))";

        $result = $this->data_builder->executeUpdate($query);
        if (!$result) {
            throw new aafwException('SegmentingUserData createTmpSegmentingUsers Failed: ' . $result);
        }
    }

    /**
     * @throws aafwException
     */
    public function dropTmpSegmentingUsers() {
        $query = "/* SegmentingUserData DROP TEMPORARY TABLE tmp_segmenting_users */
                    DROP TEMPORARY TABLE IF EXISTS tmp_segmenting_users;";

        $result = $this->data_builder->executeUpdate($query);
        if (!$result) {
            throw new aafwException('SegmentingUserData dropTmpSegmentingUsers Failed: ' . $result);
        }
    }

    /**
     * @param $segmenting_users
     * @throws aafwException
     */
    public function insertTmpSegmentingUsersByQuery($segmenting_users) {
        $index = 0;
        $tmp_insert_query = "/* SegmentingUserData INSERT INTO tmp_segmenting_users */
                    INSERT INTO tmp_segmenting_users(bur_id) VALUES ";

        foreach ($segmenting_users as $segmenting_user) {
            if (++$index === 1) {
                $query = $tmp_insert_query;
            }

            $query .= "(" . $segmenting_user['brands_users_relations_id'] . "),";

            if ($index >= SegmentService::SQL_EXECUTE_LIMIT) {
                $index = 0;
                $query = trim($query, ",");

                $result = $this->data_builder->executeUpdate($query);
                if (!$result) {
                    throw new aafwException('SegmentingUserData insertTmpSegmentingUsers Failed: ' . $result);
                }
            }
        }

        if ($index !== 0) {
            $query = trim($query, ",");
            $result = $this->data_builder->executeUpdate($query);

            if (!$result) {
                throw new aafwException('SegmentingUserData insertTmpSegmentingUsers Failed: ' . $result);
            }
        }
    }

    /**
     * TODO temporary function
     * @return array
     */
    public function getTmpSegmentingUsers() {
        $query = $this->getTmpSegmentingUsersQuery();

        return $this->data_builder->getBySQL($query, $this->default_args);
    }

    /**
     * @return string
     */
    public function getTmpSegmentingUsersQuery() {
        return "/* SegmentingUserData SELECT tmp_segmenting_users */
                SELECT bur_id FROM tmp_segmenting_users";
    }

    /**
     * @param $provision
     * @return bool
     */
    private function isEmptyProvision($provision) {
        foreach ($provision as $condition) {
            if (is_array($condition) && !empty($condition)) {
                return false;
            }
        }

        return true;
    }
}