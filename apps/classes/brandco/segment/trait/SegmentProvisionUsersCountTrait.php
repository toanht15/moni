<?php

trait SegmentProvisionUsersCountTrait {

    /**
     * @param $segmenting_users_count
     * @param $segment_provision_id
     * @param $created_date
     * @throws aafwException
     */
    public function updateSegmentProvisionUsersCount($segmenting_users_count, $segment_provision_id, $created_date) {
        $query = "/* SegmentProvisionUsersCountTrait updateSegmentProvisionsUsersCount */
                  INSERT INTO segment_provision_users_count (segment_provision_id, total, created_date, created_at) VALUES ";
        $query .= "(" . $this->escapeForSQL($segment_provision_id)
                . ", " . $this->escapeForSQL($segmenting_users_count)
                . ", " . $this->escapeForSQL($created_date)
                . ", NOW())";

        $result = $this->data_builder->executeUpdate($query);
        if (!$result) {
            throw new aafwException('SegmentingUserData createTmpSegmentingUsers Failed: ' . $result);
        }
    }

    /**
     * @param $segment_provision_id
     * @param null $created_date
     * @return mixed
     */
    public function getSegmentProvisionUsersCountByDate($segment_provision_id, $created_date = null) {
        if (!$created_date) {
            $created_date = strtotime('today');
        }

        $filter = array(
            'conditions' => array(
                'segment_provision_id' => $segment_provision_id,
                'created_date' => $created_date
            )
        );

        return $this->segment_provision_users_counts->findOne($filter);
    }

    /**
     * @param null $created_date
     * @return mixed
     */
    public function getAllSegmentProvisionUsersCountByDate($created_date = null) {
        if (!$created_date) {
            $created_date = strtotime('today');
        }

        $filter = array(
            'conditions' => array(
                'created_date' => $created_date
            )
        );

        return $this->segment_provision_users_counts->find($filter);
    }

    /**
     * @param $segment_provision
     * @return bool
     */
    public function isSegmentedProvision($segment_provision) {
        if (Util::isNullOrEmpty($segment_provision)) {
            return true;
        }

        $user_count = $this->getSegmentProvisionUsersCountByDate($segment_provision->id);

        if (Util::isNullOrEmpty($user_count)) {
            return false;
        }

        return true;
    }

    /**
     * @param $cur_total
     * @param $prev_total
     * @return int
     */
    public static function getUserCountTotalStatus($cur_total, $prev_total) {
        if ($cur_total == 0) {
            return SegmentProvisionUsersCount::USERS_COUNT_PROCESSING;
        }

        if ($cur_total > $prev_total) {
            return SegmentProvisionUsersCount::USERS_COUNT_STATUS_UP;
        }

        return SegmentProvisionUsersCount::USERS_COUNT_STATUS_UNCHANGED;
    }

    /**
     * @param $cur_users_count
     * @param $prev_users_count
     * @return int
     */
    public static function getUsersCountStatus($cur_users_count, $prev_users_count) {
        if ($cur_users_count === null) {
            return SegmentProvisionUsersCount::USERS_COUNT_PROCESSING;
        }

        if ($prev_users_count == null) {
            return SegmentProvisionUsersCount::USERS_COUNT_STATUS_UNCHANGED;
        }

        if ($cur_users_count->total > $prev_users_count->total) {
            return SegmentProvisionUsersCount::USERS_COUNT_STATUS_UP;
        }

        return SegmentProvisionUsersCount::USERS_COUNT_STATUS_UNCHANGED;
    }
}
