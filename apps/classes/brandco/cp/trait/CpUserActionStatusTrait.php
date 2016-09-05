<?php

AAFW::import('jp.aainc.classes.entities.CpUserActionStatus');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

trait CpUserActionStatusTrait {
    /** @var aafwEntityStoreBase $brands_users_relations  */
    protected $cp_user_action_statuses;

    /**
     * @param $id
     * @return mixed
     */
    public function getCpUserActionStatusById($id) {
        return $this->cp_user_action_statuses->findOne($id);
    }

    public function getCpUserActionStatus($cp_user_id, $cp_action_id, $on_master = false) {
        $filter = array(
            'on_master' => $on_master,
            'conditions' => array(
                "cp_user_id" => $cp_user_id,
                "cp_action_id" => $cp_action_id,
            )
        );

        return $this->cp_user_action_statuses->findOne($filter);
    }

    public function getCpUserActionStatusesByCpActionIdAndCpUserIds($cp_action_id, $cp_user_ids=[]) {
        $filter = array(
            'conditions' => array(
                "cp_user_id" => $cp_user_ids,
                "cp_action_id" => $cp_action_id,
            )
        );

        return $this->cp_user_action_statuses->find($filter);
    }

    /**
     * @param $cp_user_id
     * @return mixed
     */
    public function getCpUserActionStatusesByCpUserId($cp_user_id) {
        $filter = array(
            'conditions' => array(
                "cp_user_id" => $cp_user_id,
            )
        );

        return $this->cp_user_action_statuses->find($filter);
    }

    /**
     * @param $cp_id
     * @return mixed
     */
    public function getCpUserActionStatusesByCpId($cp_id) {
        $filter = array(
            'conditions' => array(
                "cp_id" => $cp_id,
            ),
        );

        return $this->cp_user_action_statuses->find($filter);
    }

    /**
     * @param $cp_user_id
     * @param $page
     * @param $count
     * @param $order
     * @return mixed
     */
    public function getCpUserActionStatusByCpUserIdAndPageAndCountAndOrder($cp_user_id, $page, $count, $order) {
        $filter = array(
            'conditions' => array(
                "cp_user_id" => $cp_user_id,
            ),
            'pager' => array(
                'page' => $page,
                'count' => $count,
            ),
            'order' => array(
                $order
            )
        );

        return $this->cp_user_action_statuses->find($filter);
    }

    /**
     * @param $cp_user_id
     * @param $cp_action_id
     * @param int $status
     * @param null $extern_data
     * @return mixed
     * @throws aafwException
     */
    public function createCpUserActionStatus($cp_user_id, $cp_action_id, $status = 0, $extern_data = null) {
        //カウントキャッシュ削除
        $cacheManager = new CacheManager();
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_FINISH_ACTION, $cp_action_id));
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER, $cp_action_id));


        $cp_user_action_statuses = $this->cp_user_action_statuses->createEmptyObject();
        $cp_user_action_statuses->cp_user_id = $cp_user_id;
        $cp_user_action_statuses->cp_action_id = $cp_action_id;
        $cp_user_action_statuses->status = $status;
        if ($extern_data && is_array($extern_data)) {
            $cp_user_action_statuses->user_agent = $extern_data['user_agent'];
            $cp_user_action_statuses->device_type = $extern_data['device_type'];
        }

        return $this->cp_user_action_statuses->save($cp_user_action_statuses);
    }

    /**
     * @param CpUserActionStatus $cp_user_action_status
     */
    public function updateCpUserActionStatus(CpUserActionStatus $cp_user_action_status) {
        //カウントキャッシュ削除
        $cacheManager = new CacheManager();
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_FINISH_ACTION, $cp_user_action_status->cp_action_id));
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER, $cp_user_action_status->cp_action_id));
        $this->cp_user_action_statuses->save($cp_user_action_status);
    }

    /**
     * @param CpUserActionStatus $cp_user_action_status
     */
    public function deleteCpUserActionStatus(CpUserActionStatus $cp_user_action_status) {
        //カウントキャッシュ削除
        $cacheManager = new CacheManager();
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_FINISH_ACTION, $cp_user_action_status->cp_action_id));
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER, $cp_user_action_status->cp_action_id));
        $this->cp_user_action_statuses->delete($cp_user_action_status);
    }

    public function deleteCpUserActionStatusByCpUser($cp_user_id) {
        $statuses = $this->getCpUserActionStatusesByCpUserId($cp_user_id);
        foreach ($statuses as $status) {
            $this->deleteCpUserActionStatus($status);
        }
    }

    /**
     * @param $cp_user_id
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpUserActionStatusByCpUserIdAndCpActionId($cp_user_id, $cp_action_id) {
        $filter = array(
            'conditions' => array(
                "cp_user_id" => $cp_user_id,
                "cp_action_id" => $cp_action_id,
            ),
        );

        return $this->cp_user_action_statuses->findOne($filter);
    }

    /**
     * @param $cp_user_id
     * @param $cp_action_id
     * @param string $user_agent
     * @param int $device_type
     * @param int $status
     */
    public function joinAction($cp_user_id, $cp_action_id, $user_agent = "", $device_type = CpUserActionStatus::DEVICE_TYPE_OTHERS, $status = CpUserActionStatus::JOIN) {
        //カウントキャッシュ削除
        $cacheManager = new CacheManager();
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_FINISH_ACTION, $cp_action_id));
        $cacheManager->deleteCache("cp_action_member_count", array(CpUserService::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER, $cp_action_id));

        $data_builder = aafwDataBuilder::newBuilder();
        $data_builder->executeUpdate("
            UPDATE cp_user_action_statuses
              SET
                status = '". $data_builder->escape($status) . "',
                user_agent = '" . $data_builder->escape($user_agent) . "',
                device_type = '" . $data_builder->escape($device_type) . "',
                updated_at = NOW()
              WHERE
                cp_user_id = {$cp_user_id}
                AND cp_action_id = {$cp_action_id}
                AND del_flg = 0
        ");
    }

    public function getCpUserActionStatusByCpActionAndStatus($cp_action_id, $status = CpUserActionStatus::NOT_JOIN) {
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_id,
                'status' => $status
            ),
            'order' => array(
                'name' => 'created_at',
                'direction' => "desc"
            )
        );

        return $this->cp_user_action_statuses->find($filter);
    }

    public function countCpUserActionStatusByCpActionAndStatus($cp_action_id, $status = CpUserActionStatus::NOT_JOIN) {
        if (Util::isNullOrEmpty($cp_action_id)) {
            return null;
        }
        if (Util::isNullOrEmpty($status)) {
            return null;
        }
        $builder = aafwDataBuilder::newBuilder();
        $rs = $builder->executeUpdate("
              SELECT
                COUNT(*) FROM cp_user_action_statuses
                WHERE
                  cp_user_action_statuses.del_flg = '0' AND
                  cp_user_action_statuses.cp_action_id = '${cp_action_id}' AND
                  cp_user_action_statuses.status = '${status}'");
        if (!$rs) {
            return null;
        }
        $row = $builder->fetchResultSet($rs);
        return (int) $row['COUNT(*)'];
    }
}
