<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionGroupTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpUserActionMessageTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpUserActionStatusTrait');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class CpUserService extends aafwServiceBase {

    use CpActionTrait;
    use CpActionGroupTrait;
    use CpUserActionMessageTrait;
    use CpUserActionStatusTrait;

    protected $logger;
    protected $cp_users;
    private $cache_manager;
    private $data_builder;

    const CACHE_TYPE_SEND_MESSAGE = "send_message";
    const CACHE_TYPE_READ_PAGE = "read_page";
    const CACHE_TYPE_READ_PAGE_NEW_BR_USER = "read_page_new_br_user";
    const CACHE_TYPE_READ_PAGE_PC = "read_page_pc";
    const CACHE_TYPE_READ_PAGE_SP = "read_page_sp";
    const CACHE_TYPE_FINISH_ACTION = "finish_action";
    const CACHE_TYPE_FINISH_ACTION_PC = "finish_action_pc";
    const CACHE_TYPE_FINISH_ACTION_SP = "finish_action_sp";
    const CACHE_TYPE_FINISH_ACTION_NEW_BR_USER = "finish_action_new_br_user";

    /**
     * @param $id
     * @return mixed
     */
    public function getCpUserById($id, $on_master = false) {
        $filter = array(
            'on_master' => $on_master,
            'conditions' => array(
                "id" => $id
            )
        );

        return $this->cp_users->findOne($filter);
    }

    /**
     * @param $cp_id
     * @param $user_id
     */
    public function getCpUserByCpIdAndUserId($cp_id, $user_id) {
        $filter = array(
            'conditions' => array(
                "cp_id" => $cp_id,
                "user_id" => $user_id,
            ),
        );
        return $this->cp_users->findOne($filter);
    }

    /**
     * @param $cp_ids
     * @param $user_id
     * @return mixed
     */
    public function getCpUserByCpIdsAndUserId($cp_ids, $user_id) {
        $filter = array(
            'conditions' => array(
                "cp_id" => $cp_ids,
                "user_id" => $user_id,
            ),
        );
        return $this->cp_users->find($filter);
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function getCpUsersByUserId($user_id) {

        $filter = array(
            'conditions' => array(
                "user_id" => $user_id,
            ),
            'order' => array(
                'name' => 'user_id',
                'direction' => 'desc'
            )
        );
        return $this->cp_users->find($filter);
    }

    /**
     * @param $user_id
     * @param $page
     * @param $count
     * @param $order
     * @return mixed
     */
    public function getCpUsersByUserIdAndPageAndCountAndOrder($user_id, $page, $count, $order) {

        $filter = array(
            'conditions' => array(
                "user_id" => $user_id,
            ),
            'pager' => array(
                'page' => $page,
                'count' => $count,
            ),
            'order' => array(
                $order
            )
        );

        return $this->cp_users->find($filter);
    }

    /**
     * @param $cp_id
     * @param $user_id
     * @param bool $from_admin_flg
     * @param int $beginner_flg
     * @param int $sns_kind
     * @param int $demography_flg
     * @param  $from_id
     * @param  $referrer
     * @return mixed
     */
    public function createCpUser($cp_id, $user_id, $from_admin_flg = false, $beginner_flg = CpUser::NOT_BEGINNER_USER, $sns_kind = 0, $demography_flg = CpUser::DEMOGRAPHY_STATUS_COMPLETE, $from_id = null, $referrer = null) {
        $cp_user = $this->cp_users->createEmptyObject();
        $cp_user->cp_id = $cp_id;
        $cp_user->user_id = $user_id;
        $cp_user->beginner_flg = $beginner_flg;
        $cp_user->join_sns = $sns_kind;
        $cp_user->demography_flg = $demography_flg;

        if ($from_admin_flg == false) {
            $cp_user->from_id = $from_id;
            $cp_user->referrer = $referrer;
        }
        
        return $this->cp_users->save($cp_user);
    }

    /**
     * @param CpUser $cp_user
     */
    public function updateCpUser(CpUser $cp_user) {
        $this->cp_users->save($cp_user);
    }

    /**
     * @param CpUser $cp_user
     */
    public function deleteCpUser(CpUser $cp_user) {
        $this->cp_users->delete($cp_user);
    }

    public function deletePhysicalCpUser(CpUser $cpUser) {
        $this->cp_users->deletePhysical($cpUser);
    }

    public function __construct() {
        $this->cp_users = $this->getModel("CpUsers");
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_user_action_messages = $this->getModel("CpUserActionMessages");
        $this->cp_user_action_statuses = $this->getModel("CpUserActionStatuses");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->cache_manager = new CacheManager();
        $this->data_builder = aafwDataBuilder::newBuilder();
    }


    /**
     * @param $cp_action_id
     * @return array
     */
    public function getCpAndActionsByCpActionId($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        $cp_action_group = $cp_action->getCpActionGroup();

        // 具体的なアクションを取得
        /** @var CpActionManager $action_manager */
        $manager = $cp_action->getActionManagerClass();
        $concrete_action = $manager->getConcreteAction($cp_action);
        return array($cp_action, $concrete_action, CpInfoContainer::getInstance()->getCpById($cp_action_group->cp_id));
    }

    /**
     * @param $cp_id
     * @param $user_id
     * @return mixed
     */
    public function getOrCreateCpUser($cp_id, $user_id) {
        $cp_user = $this->getCpUserByCpIdAndUserId($cp_id, $user_id);
        if ($cp_user->id) {
            return $cp_user;
        } else {
            return $this->createCpUser($cp_id, $user_id);
        }
    }


    public function sendActionMessage($cp_user_id, $cp_action, $concrete_action, $read_flg = false) {
        $message = $this->createCpUserActionMessage($cp_user_id, $cp_action, $concrete_action, $read_flg);
        $action_status = $this->createCpUserActionStatus($cp_user_id, $cp_action->id);
        return array($message, $action_status);
    }

    /**
     * @param $cp_user_id
     * @param $cp_action
     * @param $concrete_action
     * @param bool $read_flg
     * @param null $extern_data
     * @param int $join_status
     * @return array
     */
    public function sendJoinActionMessage($cp_user_id, $cp_action, $concrete_action, $read_flg = false, $extern_data = null, $join_status = CpUserActionStatus::JOIN) {
        $message = $this->createCpUserActionMessage($cp_user_id, $cp_action, $concrete_action, $read_flg);
        $action_status = $this->createCpUserActionStatus($cp_user_id, $cp_action->id, $join_status, $extern_data);
        return array($message, $action_status);
    }

    /**
     * @param $cp_action_id
     * @return array
     */
    public function getActionMemberCounts($cp_action_id) {
        $ret = array();

        //送信済み
        $ret[self::CACHE_TYPE_SEND_MESSAGE] = $this->getSendMessageCount($cp_action_id);

        /**
         * 開封済み
         */
        $ret[self::CACHE_TYPE_READ_PAGE] = $this->getReadPageCount($cp_action_id);

        /**
         * 開封済みPC
         */
        $ret[self::CACHE_TYPE_READ_PAGE_PC] = $this->cache_manager->getCache("cp_action_member_count", array(self::CACHE_TYPE_READ_PAGE_PC, $cp_action_id));

        if ($ret[self::CACHE_TYPE_READ_PAGE_PC] === null) {
            $filter = array();
            $filter['conditions'] = array('cp_action_id' => $cp_action_id, 'read_flg' => 1, 'cus.device_type' => CpUserActionStatus::DEVICE_TYPE_OTHERS);
            $filter['join'][] = array('type' => 'INNER', 'name' => 'cp_users', 'alias' => 'cu', 'key' => array('cp_user_action_messages.cp_user_id' => 'cu.id'));
            $filter['join'][] = array('type' => 'INNER', 'name' => 'cp_user_action_statuses', 'alias' => 'cus', 'key' => array('cus.cp_user_id' => 'cu.id'));

            $cp_user_action_count = $this->cp_user_action_messages->count($filter);
            $ret[self::CACHE_TYPE_READ_PAGE_PC] = $cp_user_action_count;
            $this->cache_manager->addCache("cp_action_member_count", $cp_user_action_count, array(self::CACHE_TYPE_READ_PAGE_PC, $cp_action_id));
        }

        /**
         * 開封済みSP
         */
        $ret[self::CACHE_TYPE_READ_PAGE_SP] = $this->cache_manager->getCache("cp_action_member_count", array(self::CACHE_TYPE_READ_PAGE_SP, $cp_action_id));

        if ($ret[self::CACHE_TYPE_READ_PAGE_SP] === null) {
            $filter = array();
            $filter['conditions'] = array('cp_action_id' => $cp_action_id, 'read_flg' => 1, 'cus.device_type' => CpUserActionStatus::DEVICE_TYPE_SP);
            $filter['join'][] = array('type' => 'INNER', 'name' => 'cp_users', 'alias' => 'cu', 'key' => array('cp_user_action_messages.cp_user_id' => 'cu.id'));
            $filter['join'][] = array('type' => 'INNER', 'name' => 'cp_user_action_statuses', 'alias' => 'cus', 'key' => array('cus.cp_user_id' => 'cu.id'));

            $cp_user_action_count = $this->cp_user_action_messages->count($filter);
            $ret[self::CACHE_TYPE_READ_PAGE_SP] = $cp_user_action_count;
            $this->cache_manager->addCache("cp_action_member_count", $cp_user_action_count, array(self::CACHE_TYPE_READ_PAGE_SP, $cp_action_id));
        }

        /**
         * 開封済み新規
         */
        $ret[self::CACHE_TYPE_READ_PAGE_NEW_BR_USER] = $this->cache_manager->getCache("cp_action_member_count", array(self::CACHE_TYPE_READ_PAGE_NEW_BR_USER, $cp_action_id));

        if ($ret[self::CACHE_TYPE_READ_PAGE_NEW_BR_USER] === null) {
            $filter = array(
                'conditions' => array('cp_action_id' => $cp_action_id, 'read_flg' => 1,),
                'join' => array('type' => 'INNER', 'name' => 'cp_users', 'alias' => 'cu', 'key' => array('cp_user_action_messages.cp_user_id' => 'cu.id', 'cu.beginner_flg' => '1'))
            );

            $cp_user_action_count = $this->cp_user_action_messages->count($filter);
            $ret[self::CACHE_TYPE_READ_PAGE_NEW_BR_USER] = $cp_user_action_count;
            $this->cache_manager->addCache("cp_action_member_count", $cp_user_action_count, array(self::CACHE_TYPE_READ_PAGE_NEW_BR_USER, $cp_action_id));
        }

        /**
         * アクション完了
         */
        $ret[self::CACHE_TYPE_FINISH_ACTION] = $this->getFinishActionCount($cp_action_id);
        
        /**
         * アクション完了PC
         */
        $ret[self::CACHE_TYPE_FINISH_ACTION_PC] = $this->cache_manager->getCache("cp_action_member_count",array(self::CACHE_TYPE_FINISH_ACTION_PC, $cp_action_id));

        if ($ret[self::CACHE_TYPE_FINISH_ACTION_PC] === null) {
            $filter = array();
            $filter['conditions'] = array('cp_action_id' => $cp_action_id, 'status' => 1, 'device_type' => CpUserActionStatus::DEVICE_TYPE_OTHERS);
            $filter['join'] = array('type' => 'INNER', 'name' => 'cp_users', 'alias' => 'cu', 'key' => array('cp_user_action_statuses.cp_user_id' => 'cu.id'));

            $cp_user_action_count = $this->cp_user_action_statuses->count($filter);
            $ret[self::CACHE_TYPE_FINISH_ACTION_PC] = $cp_user_action_count;
            $this->cache_manager->addCache("cp_action_member_count", $cp_user_action_count, array(self::CACHE_TYPE_FINISH_ACTION_PC, $cp_action_id));
        }

        /**
         * アクション完了SP
         */
        $ret[self::CACHE_TYPE_FINISH_ACTION_SP] = $this->cache_manager->getCache("cp_action_member_count",array(self::CACHE_TYPE_FINISH_ACTION_SP, $cp_action_id));

        if ($ret[self::CACHE_TYPE_FINISH_ACTION_SP] === null) {
            $filter = array();
            $filter['conditions'] = array('cp_action_id' => $cp_action_id, 'status' => 1, 'device_type' => CpUserActionStatus::DEVICE_TYPE_SP);
            $filter['join'] = array('type' => 'INNER', 'name' => 'cp_users', 'alias' => 'cu', 'key' => array('cp_user_action_statuses.cp_user_id' => 'cu.id'));

            $cp_user_action_count = $this->cp_user_action_statuses->count($filter);
            $ret[self::CACHE_TYPE_FINISH_ACTION_SP] = $cp_user_action_count;
            $this->cache_manager->addCache("cp_action_member_count", $cp_user_action_count, array(self::CACHE_TYPE_FINISH_ACTION_SP, $cp_action_id));
        }

        /**
         * アクション完了新規
         */
        $ret[self::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER] = $this->cache_manager->getCache("cp_action_member_count",array(self::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER, $cp_action_id));

        if ($ret[self::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER] === null) {
            $filter = array(
                'conditions' => array('cp_action_id' => $cp_action_id, 'status' => 1,),
                'join' => array('type' => 'INNER', 'name' => 'cp_users', 'alias' => 'cu', 'key' => array('cp_user_action_statuses.cp_user_id' => 'cu.id', 'cu.beginner_flg' => '1'))
            );

            $cp_user_action_count = $this->cp_user_action_statuses->count($filter);
            $ret[self::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER] = $cp_user_action_count;
            $this->cache_manager->addCache("cp_action_member_count", $cp_user_action_count, array(self::CACHE_TYPE_FINISH_ACTION_NEW_BR_USER, $cp_action_id));
        }

        return $ret;
    }


    public function getReadPageCount($cp_action_id) {
        if (!$cp_action_id) return false;
        $count = $this->cache_manager->getCache("cp_action_member_count", array(self::CACHE_TYPE_READ_PAGE, $cp_action_id));

        if ($count === null) {
            $filter = array(
                'conditions' => array(
                    'cp_action_id' => $cp_action_id,
                    'read_flg' => 1,
                ),
            );

            $count = $this->cp_user_action_messages->count($filter);
            $this->cache_manager->addCache("cp_action_member_count", $count, array(self::CACHE_TYPE_READ_PAGE, $cp_action_id));
        }

        return $count;
    }

    public function getFinishActionCount($cp_action_id) {
        if (!$cp_action_id) return false;

        $count = $this->cache_manager->getCache("cp_action_member_count",array(self::CACHE_TYPE_FINISH_ACTION, $cp_action_id));

        if ($count === null) {
            $filter = array(
                'conditions' => array(
                    'cp_action_id' => $cp_action_id,
                    'status' => 1,
                ),
            );

            $count = $this->cp_user_action_statuses->count($filter);
            $this->cache_manager->addCache("cp_action_member_count", $count, array(self::CACHE_TYPE_FINISH_ACTION, $cp_action_id));
        }

        return $count;
    }

    public function getSendMessageCount($cp_action_id) {

        if (!$cp_action_id) return false;

        $count = $this->cache_manager->getCache("cp_action_member_count", array(self::CACHE_TYPE_SEND_MESSAGE, $cp_action_id));

        if ($count === null) {
            $filter = array(
                'conditions' => array(
                    'cp_action_id' => $cp_action_id,
                ),
            );

            $count = $this->cp_user_action_messages->count($filter);
            $this->cache_manager->addCache("cp_action_member_count", $count, array(self::CACHE_TYPE_SEND_MESSAGE, $cp_action_id));
        }

        return $count;
    }

    public function getCpUsersByCpId($cp_id) {
        return $this->cp_users->find(array('cp_id' => $cp_id));
    }

    public function getCpUserCountByCpId($cp_id) {
        $filter = array(
            'cp_id' => $cp_id,
        );

        return $this->cp_users->count($filter);
    }

    /**
     * @param $cp_id
     * @param $user_id
     * @return bool
     * @throws Exception
     */
    public function isJoinedCp($cp_id, $user_id, $cp_user = null, $cp = null, $entry_action = null) {
        if ($cp_user === null) {
            $cp_user = $this->getCpUserByCpIdAndUserId($cp_id, $user_id);
        }
        if (!$cp_user) {
            return false;
        }

        if ($cp === null) {
            $cp = $cp_user->getCp();
        }
        if ($cp->type == Cp::TYPE_MESSAGE) {
            return true;
        }

        $data_builder = new aafwDataBuilder();
        if ($entry_action !== null) {
            $result = $data_builder->getBySQL(
                "SELECT 1 FROM cp_user_action_statuses
                  WHERE del_flg = 0 AND cp_user_id = " . $cp_user->id . " AND cp_action_id = " . $entry_action->id, array());
            return $result[0] > 0;
        }

        $result = $data_builder->existJoinedEntryAction(array("CP_ID" => $cp->id, "CP_USER_ID" => $cp_user->id));

        return count($result) > 0;
    }

    /**
     * @param $cp_id
     * @param $user_id
     * @return bool
     */
    public function isJoinFinish($cp_id, $user_id){
        if (Util::isNullOrEmpty($cp_id) || Util::isNullOrEmpty($user_id)) {
            return false;
        }

        /** @var CpFlowService $cpFlowService */
        $cpFlowService = $this->getService('CpFlowService');
        $lastAction = $cpFlowService->getLastActionOfFirstGroupByCpId($cp_id);

        if (!$lastAction) return false;

        $cpUser = $this->getCpUserByCpIdAndUserId($cp_id, $user_id);

        if (Util::isNullOrEmpty($cpUser)) return false;

        $data_builder = aafwDataBuilder::newBuilder();
        $join_finish_query = "SELECT 1 FROM cp_user_action_statuses WHERE del_flg = 0 AND cp_user_id = " . $cpUser->id . " AND cp_action_id = " . $lastAction->id . " AND status = " . CpUserActionStatus::JOIN;

        $result = $data_builder->getBySQL($join_finish_query, array());

        return $result[0] > 0;
    }


    /**
     * @param $cp_user_id
     * @return mixed
     */
    public function getUserByCpUserId($cp_user_id) {

        /** @var UserService $user_service */
        $user_service = $this->getService('UserService');

        $cp_user = $this->getCpUserById($cp_user_id);
        $user = $user_service->getUserByBrandcoUserId($cp_user->user_id);

        return $user;
    }

    public function getBrandByCpUserId($cp_user_id) {

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        /** @var BrandService $brand_service */
        $brand_service = $this->getService('BrandService');

        $cp_user = $this->getCpUserById($cp_user_id);
        $cp = $cp_flow_service->getCpById($cp_user->cp_id);
        $brand = $brand_service->getBrandById($cp->brand_id);

        return $brand;
    }

    public function updateDuplicateAddressCount($ids, $duplicateCount) {

        if(!$ids) return;

        $sql = "
            UPDATE cp_users
            SET duplicate_address_count = ".$duplicateCount."
            WHERE id IN (".implode(',',$ids).")
        ";
        
        $this->data_builder->executeUpdate($sql);
    }
}
