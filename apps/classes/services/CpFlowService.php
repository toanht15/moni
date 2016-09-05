<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionGroupTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpNextActionTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpUserTrait');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class CpFlowService extends aafwServiceBase {

    use CpTrait;
    use CpActionTrait;
    use CpActionGroupTrait;
    use CpNextActionTrait;

    const ENTRY_WITH_INFO = 1;
    const ENTRY_WITHOUT_INFO = 2;

    protected $logger;
    protected $cache_manager;
    protected $service_factory;

    public function __construct() {
        $this->cps = $this->getModel("Cps");
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_action_groups = $this->getModel("CpActionGroups");
        $this->cp_next_actions = $this->getModel("CpNextActions");
        $this->cp_users = $this->getModel("CpUsers");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function getCacheManager() {
        if (!$this->cache_manager) {
            $this->cache_manager = new CacheManager();
        }
        return $this->cache_manager;
    }

    /**
     * @param $cp_id
     * @return array
     */
    public function getEntryActionInfoByCpId($cp_id) {
        $cp_action_group = $this->getCpActionGroupsByCpId($cp_id)->current();
        $cp_action = $this->getFirstActionInGroupByGroupId($cp_action_group->id);
        if ($cp_action === null) {
            return array();
        }
        $manager = $cp_action->getActionManagerClass();
        $entry_action = $manager->getConcreteAction($cp_action);
        return array($cp_action, $entry_action);
    }

    public function getFirstActionTypeByCpId($cp_id) {
        if (Util::isNullOrEmpty($cp_id)) {
            return null;
        }
        $builder = aafwDataBuilder::newBuilder();
        $rs = $builder->executeUpdate("
            SELECT a.type FROM cp_actions a
              INNER JOIN (SELECT id FROM cp_action_groups WHERE cp_id = {$cp_id} AND del_flg = 0 ORDER BY order_no LIMIT 1) g
              ON a.cp_action_group_id = g.id
              WHERE a.del_flg = 0
              ORDER BY a.order_no LIMIT 1
        ");
        if (!$rs) {
            return null;
        }
        $row = $builder->fetchResultSet($rs);

        return $row['type'];
    }

    /**
     * @param $cp_id
     * @return bool
     */
    public function canPublicCp($cp_id) {

        $cp_status = $this->isFixedCpInfo($cp_id);
        if (!$cp_status) {
            return false;
        }

        $cp_action_group = $this->getCpActionGroupsByCpId($cp_id)->current();
        $cp_action_group_status = $this->isFixedCpActions($cp_action_group->id);
        if (!$cp_action_group_status) {
            return false;
        }

        return true;
    }

    /**
     * @param $cp_id
     */
    public function scheduleCp($cp_id) {
        $cp = $this->getCpById($cp_id);
        $cp->status = Cp::STATUS_SCHEDULE;
        $this->updateCp($cp);
    }

    /**
     * @param $cp_id
     */
    public function cancelScheduleCp($cp_id) {
        $cp = $this->getCpById($cp_id);
        $cp->status = Cp::STATUS_DRAFT;
        $this->updateCp($cp);
    }

    public function demoCp($cp_id) {
        if (!$cp_id) {
            return;
        }
        $cp = $this->getCpById($cp_id);
        if (!$cp) {
            return;
        }
        $cp->status = Cp::STATUS_DEMO;
        $this->updateCp($cp);
    }

    /**
     * @param $cp_id
     * @return mixed|null
     */
    public function getFirstActionOfCp($cp_id) {
        $first_group = $this->getCpActionGroupByCpIdAndOrderNo($cp_id, 1);
        if (!$first_group) {
            return null;
        }
        $actions = $this->getCpActionByGroupIdAndOrderNo($first_group->id, 1);
        if (!$actions) {
            return null;
        }
        return $actions;
    }

    public function getFirstActionInGroupByAction(CpAction $action) {
        $actions = $this->getCpActionsByCpActionGroupId($action->cp_action_group_id);
        return $actions->current();
    }

    public function getFirstActionInGroupByGroupId($action_group_id) {
        $actions = $this->getCpActionsByCpActionGroupId($action_group_id);
        return $actions->current();
    }

    public function getLastActionInGroupByGroupId($action_group_id) {
        return $this->getMaxStepNo($action_group_id);
    }

    public function getLastActionOfFirstGroupByCpId($cp_id){
        $first_group = $this->getCpActionGroupByCpIdAndOrderNo($cp_id, 1);
        if (!$first_group) {
            return null;
        }
        $lastAction = $this->getLastActionInGroupByGroupId($first_group->id);
        return $lastAction;
    }

    /**
     * @param $cp_action_id
     * @return bool
     */
    public function isLastCpActionInGroup($cp_action_id) {
        $action_group_id = $this->getCpActionById($cp_action_id)->getCpActionGroup()->id;
        return $cp_action_id == $this->getMaxStepNo($action_group_id)->id;
    }

    /**
     * @param $cp_action_id
     * @return bool
     */
    public function isLastCpActionInFirstGroup($cp_action_id) {
        $cp_action_group = $this->getCpActionById($cp_action_id)->getCpActionGroup();

        if ($cp_action_group->order_no === '1') {
            return $cp_action_id == $this->getMaxStepNo($cp_action_group->id)->id;
        }

        return false;
    }

    /**
     * @param $cp_id
     * @return array
     */
    public function getCpActionsByCpId($cp_id) {
        $cp_action_groups = $this->getCpActionGroupsByCpId($cp_id);
        $actions = array();
        foreach ($cp_action_groups as $action_group) {
            $cp_actions = $this->getCpActionsByCpActionGroupId($action_group->id);

            if ($cp_actions != null) {
                $actions = array_merge($actions, $cp_actions->toArray());
            }
        }
        return $actions;
    }

    /**
     * @param $cp_id
     * @return array
     */
    public function getCpActionsInFirstGroupByCpId($cp_id) {
        $cp_action_groups = $this->getCpActionGroupsByCpId($cp_id)->toArray();
        $cp_actions = $this->getCpActionsByCpActionGroupId($cp_action_groups[0]->id);

        $actions = array();
        if ($cp_actions) {
            $actions = $cp_actions->toArray();
        }

        return $actions;
    }

    /**
     * @param $cp_id
     * @param $cp_action_type
     * @return array
     */
    public function getCpActionsByCpIdAndActionType($cp_id, $cp_action_type) {
        $cp_action_group_ids = array();
        $cp_action_groups = $this->getCpActionGroupsByCpId($cp_id);

        foreach ($cp_action_groups as $cp_action_group) {
            $cp_action_group_ids[] = $cp_action_group->id;
        }

        $cp_actions = $this->getCpActionsByCpActionGroupIdAndType($cp_action_group_ids, $cp_action_type);
        return $cp_actions ? $cp_actions->toArray() : array();
    }

    public function getPhotoActionsByCpId($cp_id) {
        $groups = $this->getCpActionGroupsByCpId($cp_id);

        foreach ($groups as $group) {
            $groupIds[] = $group->id;
        }

        $cp_actions = $this->getCpActionsByCpActionGroupIdAndType($groupIds, CpAction::TYPE_PHOTO);
        foreach ($cp_actions as $cp_action) {
            $cpActionIds[] = $cp_action->id;
        }

        return $cpActionIds;
    }

    public function getInstagramHashtagActionsByCpId($cp_id) {
        $groups = $this->getCpActionGroupsByCpId($cp_id);

        foreach ($groups as $group) {
            $groupIds[] = $group->id;
        }

        $cp_actions = $this->getCpActionsByCpActionGroupIdAndType($groupIds, CpAction::TYPE_INSTAGRAM_HASHTAG);
        foreach ($cp_actions as $cp_action) {
            $cpActionIds[] = $cp_action->id;
        }

        return $cpActionIds;
    }

    public function getPopularVoteActionsByCpId($cp_id) {
        $groups = $this->getCpActionGroupsByCpId($cp_id);

        foreach ($groups as $group) {
            $groupIds[] = $group->id;
        }

        $cp_actions = $this->getCpActionsByCpActionGroupIdAndType($groupIds, CpAction::TYPE_POPULAR_VOTE);
        foreach ($cp_actions as $cp_action) {
            $cpActionIds[] = $cp_action->id;
        }

        return $cpActionIds;
    }

    public function getInstantWinActionByCpId($cp_id) {
        $groups = $this->getCpActionGroupsByCpId($cp_id);
        foreach ($groups as $group) {
            $groupIds[] = $group->id;
        }
        $cp_actions = $this->getCpActionByCpActionGroupIdAndType($groupIds, CpAction::TYPE_INSTANT_WIN);
        if ($cp_actions) {
            $cp_action = $cp_actions->toArray();
        }
        return $cp_action;
    }

    public function getCpActionIdsByCpIdAndType($cp_id, $cp_type) {
        $groups = $this->getCpActionGroupsByCpId($cp_id);
        foreach ($groups as $group) {
            $group_ids[] = $group->id;
        }

        $cp_actions = $this->getCpActionsByCpActionGroupIdAndType($group_ids, $cp_type);
        foreach ($cp_actions as $cp_action) {
            $cp_action_ids[] = $cp_action->id;
        }

        return $cp_action_ids;
    }

    public function getEntryActionByCpId($cp_id) {
        $actions = $this->getCpActionsByCpId($cp_id);
        foreach ($actions as $action) {
            if ($action->isOpeningCpAction()) {
                $result = $action;
            }
        }
        return $result;
    }

    public function getAllCpsBySameAnnounceDate($announce_date){
        $filter = array(
            'announce_date:<' => date('Y-m-d', strtotime($announce_date . '+1 day')),
            'announce_date:>=' => date('Y-m-d', strtotime($announce_date)),
            'status' => Cp::CAMPAIGN_STATUS_OPEN,
            'permanent_flg' => Cp::PERMANENT_FLG_OFF
        );

        return $this->cps->find($filter);
    }

    public function getCpPageClosingCps() {
        $filter = array(
            'type'                  => Cp::TYPE_CAMPAIGN,
            'cp_page_close_date:<'  => date('Y-m-d H:i:s'),
            'status:!='             => Cp::STATUS_CLOSE,
            'use_cp_page_close_flg' => Cp::CLOSE_DATE_ON,
            'permanent_flg' => Cp::PERMANENT_FLG_OFF
        );

        return $this->cps->find($filter);
    }

    public function getJoinFinishActionByCpId($cp_id) {
        $actions = $this->getCpActionsByCpId($cp_id);
        foreach ($actions as $action) {
            if ($action->type == CpAction::TYPE_JOIN_FINISH) {
                $result = $action;
            }
        }
        return $result;
    }

    /**
     * $this->Data['cp_info']にセットする内容取得
     * @param $cp
     * @return array
     */
    public function getCampaignInfo($cp,$brand, $first_concrete_action = null, $cp_status = null){
        $cp_info = [
            "cp" => [
                "id" => $cp->id,
                "created_at" => $cp->created_at,
                "can_entry" => $cp->canEntry($cp_status),
                "sponsor" => ($brand->enterprise_name) ? $brand->enterprise_name : $brand->name,
                "url" => $cp->getUrl(true, $brand),
                "shipping_method" => $cp->shipping_method,
                "winner_count" => $cp->winner_count,
                "show_winner_label" => $cp->show_winner_label,
                "winner_label" => $cp->winner_label,
                "show_recruitment_note" => $cp->show_recruitment_note,
                "recruitment_note" => $cp->recruitment_note,
                "back_monipla_flg" => $cp->back_monipla_flg,
                "extend_tag" => $cp->use_extend_tag ? $cp->extend_tag : "",
                "start_date" => Util::getFormatDateString($cp->start_date),
                "start_datetime"=> Util::getFormatDateTimeString($cp->start_date),
                "end_date" => Util::getFormatDateString($cp->end_date),
                "end_datetime" => Util::getFormatDateTimeString($cp->end_date),
                "announce_date" => Util::getFormatDateString($cp->announce_date),
                "status" => $cp->status,
                "title" => $cp->getTitle(),
                "announce_display_label_use_flg" => $cp->announce_display_label_use_flg,
                "announce_display_label" => $cp->announce_display_label
            ],

            "tweet_share_text" => $cp->getTitle($first_concrete_action) . ' / ' . $brand->name
        ];
        return $cp_info;
    }

    public function getCpModel() {
        return $this->cps;
    }

    public function getShippingUserCountByCpId($cp_id) {
        $data_builder = new aafwDataBuilder();
        $result = $data_builder->getBySQL("SELECT COUNT(*) FROM cp_users cpusr
                    INNER JOIN shipping_address_users ship ON ship.cp_user_id = cpusr.id AND ship.del_flg = 0
                    WHERE cpusr.cp_id = " . $cp_id . " AND cpusr.del_flg = 0", array());
        $shipping_count = (int)$result[0]['COUNT(*)'];
        return $shipping_count;
    }

    public function getExpireAnnounceDateCps() {
        $today = date("Y-m-d");
        $filter = array(
            'announce_date:<' => date('Y-m-d', strtotime($today)),
            'type' => Cp::TYPE_CAMPAIGN,
            'status' => Cp::CAMPAIGN_STATUS_OPEN,
            'shipping_method' => Cp::SHIPPING_METHOD_MESSAGE,
            'archive_flg' => Cp::ARCHIVE_OFF,
            'permanent_flg' => Cp::PERMANENT_FLG_OFF
        );

        return $this->cps->find($filter);
    }

    /**
     * @param $cp_id
     */
    public function cancelDemoByCpId($cp_id) {
        if (!$cp_id) {
            return;
        }
        $this->resetDemoUserDataByCpId($cp_id);

        //update campaign status
        $cp = $this->getCpById($cp_id);
        $cp->status = Cp::STATUS_DRAFT;
        $this->cps->save($cp);
    }

    /**
     * @param $cp_id
     */
    public function deletePhysicalCpUsersByCpId($cp_id) {

        if (!$cp_id) {
            return;
        }

        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->getService('CpUserService');
        $cp_users = $cp_user_service->getCpUsersByCpId($cp_id);
        $cp = $this->getCpById($cp_id);
        if (!$cp || !$cp_users) {
            return;
        }
        foreach ($cp_users as $cp_user) {
            $cp_user_service->deletePhysicalCpUser($cp_user);
            $this->getCacheManager()->resetNotificationCount($cp->brand_id, $cp_user->user_id);
        }
    }

    /**
     * @param $cp_id
     */
    public function resetDemoUserDataByCpId($cp_id) {
        if (!$cp_id) {
            return;
        }

        $cp_actions = $this->getCpActionsByCpId($cp_id);
        if (!$cp_actions) {
            return;
        }

        /** @var CpUserActionStatusService $cp_user_action_status_service */
        $cp_user_action_status_service = $this->getService('CpUserActionStatusService');

        /** @var CpMessageDeliveryService $cp_message_delivery_service */
        $cp_message_delivery_service = $this->getService("CpMessageDeliveryService");

        foreach ($cp_actions as $cp_action) {
            if ($cp_action->status == CpAction::STATUS_DRAFT) {
                continue;
            }

            $manager_class = $cp_action->getActionManagerClass();

            // remove user's data
            $manager_class->deletePhysicalRelatedCpActionData($cp_action);

            // remove action status and message + cache
            $cp_user_action_status_service->deleteCpUserActionMessagesByCpActionId($cp_action->id);
            $cp_user_action_status_service->deleteCpUserActionStatusByCpActionId($cp_action->id);

            // remove delivery reservation and target + cache
            $cp_message_delivery_service->deletePhysicalDeliveryReservationAndTargetsByCpActionId($cp_action->id);
        }

        $this->deletePhysicalCpUsersByCpId($cp_id);
    }

    public function resetDemoUserDataByCpUser(CpUser $cp_user) {
        if (!$cp_user) {
            return;
        }

        $cp_actions = $this->getCpActionsByCpId($cp_user->cp_id);
        if (!$cp_actions) {
            return;
        }

        /** @var CpUserActionStatusService $cp_user_action_status_service */
        $cp_user_action_status_service = $this->getService('CpUserActionStatusService');

        /** @var CpMessageDeliveryService $cp_message_delivery_service */
        $cp_message_delivery_service = $this->getService("CpMessageDeliveryService");

        foreach ($cp_actions as $cp_action) {
            if ($cp_action->status == CpAction::STATUS_DRAFT) {
                continue;
            }

            $manager_class = $cp_action->getActionManagerClass();

            // remove user's data
            $manager_class->deletePhysicalRelatedCpActionDataByCpUser($cp_action, $cp_user);

            // remove action status and message + cache
            $cp_user_action_status_service->deleteCpUserActionMessagesByCpActionIdAndCpUserId($cp_action->id, $cp_user->id);
            $cp_user_action_status_service->deleteCpUserActionStatusByCpActionIdAndCpUserId($cp_action->id, $cp_user->id);

            // remove delivery reservation and target + cache
            $cp_message_delivery_service->deletePhysicalDeliveryTargetsByCpActionIdAndUserId($cp_action->id, $cp_user->user_id);
        }

        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->getService("CpUserService");
        $cp_user_service->deletePhysicalCpUser($cp_user);
    }

    /**
     * @param $cp_action_id
     * @return bool
     */
    public function isDemoCpByCpActionId($cp_action_id) {
        if (!$cp_action_id) {
            throw new Exception ("CpFlowService#isDemoCpByCpActionId cp_action_id null");
        }
        $action = CpInfoContainer::getInstance()->getCpActionById($cp_action_id);
        if (!$action) {
            throw new Exception ("CpFlowService#isDemoCpByCpActionId action null");
        }
        $cp = $this->getCpByCpAction($action);
        if (!$cp) {
            throw new Exception ("CpFlowService#isDemoCpByCpActionId cp null");
        }
        if ($cp->status == Cp::STATUS_DEMO) {
            return true;
        }
        return false;
    }

    public function checkCpActionTypesInCp($cp_id, $cp_action_types) {
        if (!$cp_id || !$cp_action_types) {
            return array();
        }

        $data_builder = aafwDataBuilder::newBuilder();
        $result = $data_builder->getBySQL("SELECT ca.type FROM cp_actions ca
                      INNER JOIN cp_action_groups cag ON ca.cp_action_group_id = cag.id
                      WHERE cag.cp_id = " . $cp_id . " AND
                          ca.type IN(". join(",", $cp_action_types) . ") AND ca.del_flg = 0 AND cag.del_flg = 0", array("__NOFETCH__"));
        $cp_action_type_set = array();
        while ($row = $data_builder->fetch($result)) {
            $cp_action_type_set[$row['type']] = true;
        }
        return $cp_action_type_set;
    }

    public function getStepNoByCpIdAndActionId($cp_id, $cp_action_id) {
        $data_builder = aafwDataBuilder::newBuilder();
        $result = $data_builder->getBySQL("SELECT COUNT(*) FROM cp_actions A
                    INNER JOIN cp_action_groups G ON A.cp_action_group_id = G.id AND G.cp_id = " . $cp_id . " AND G.del_flg = 0
                    WHERE A.id <= " . $cp_action_id . " AND A.del_flg = 0", array());
        $stepNo = (int)$result[0]['COUNT(*)'];
        return $stepNo;
    }

    /**
     * step_noをkeyにたcpに紐づくcp_actionsを返す
     * @param $cp_id
     * @return array
     */
    public function getCpActionsOrderByStepNoByCpId($cp_id) {
        $data_builder = aafwDataBuilder::newBuilder();
        $condition['cp_id'] = $cp_id;
        $cp_actions = $data_builder->getCpActionsOrderByStepNo($condition, array());
        $cp_actions_array = array();
        $step_no = 1;
        foreach ($cp_actions as $cp_action) {
            $cp_actions_array[$step_no++] = $cp_action;
        }
        return $cp_actions_array;
    }

    /**
     * 特定ユーザーのプロフィール・アンケートの表示が必要かどうかを判定します。
     *
     * @param $msg_count ユーザーの特定のキャンペーンにおけるメッセージ数
     * @param $first_cp_action キャンペーンの最初のCpAction
     * @return bool
     */
    public function isEntryActionWithProfileQuestionnaires($relation, $msg_count, $first_cp_action) {
        if ($relation === null || Util::isNullOrEmpty($msg_count) || $first_cp_action === null) {
            throw new aafwException("$relation, $msg_count and $first_cp_action mustn't be null or empty!");
        }
        return $relation->personal_info_flg == BrandsUsersRelation::SIGNUP_WITH_INFO && $first_cp_action->isLegalOpeningCpAction() && $msg_count === 1;
    }

    /**
     * 特定ユーザーのプロフィール・アンケートの表示が必要かどうかをクエリで判定します。
     *
     * @param $cp_user
     * @param $cp_action_id
     * @return string|bool プロフィール・アンケートを表示する必要がある場合はpersonal_info_flgの値。
     *                     ENTRY_WITH_INFOはエントリー・アクション部分ののみの再取得。
     *                     ENTRY_WITHOUT_INFOは全て表示。
     */
    public function isEntryActionWithProfileQuestionnairesByQuery($brand_id, $cp_action_id, $cp_user) {
        if (Util::existNullOrEmpty($brand_id, $cp_action_id, $cp_user)) {
            throw new aafwException("$brand_id, $cp_action_id and $cp_user mustn't be null or empty!");
        }
        $data_builder = new aafwDataBuilder();
        $result = $data_builder->isEntryActionWithProfileQuestionnairesByQuery(array(
            'BRAND_ID' => $brand_id,
            'CP_ACTION_ID' => $cp_action_id,
            'CP_USER_ID' => $cp_user->id,
            'USER_ID' => $cp_user->user_id
        ));
        if (count($result) !== 1) {
            return false;
        }
        $first_row = $result[0];

        if ($first_row['msg_count'] !== '1' || $first_row['opening_action_count'] !== '1') {
            return false;
        }

        if ($first_row['personal_info_flg'] == BrandsUsersRelation::SIGNUP_WITHOUT_INFO || $first_row['personal_info_flg'] == BrandsUsersRelation::FORCE_WITH_INFO) {
            // Because there are no user answers, send all of questionnaires.
            return self::ENTRY_WITHOUT_INFO;
        } else if ($first_row['personal_info_flg'] == BrandsUsersRelation::SIGNUP_WITH_INFO && $first_row['questionnaire_count'] > 0) {
            // Send entry action's questionnaires if they exist.
            return self::ENTRY_WITH_INFO;
        } else {
            return false;
        }
    }

    public function getCpActionMapByIds($cp_action_ids) {
        if ($cp_action_ids === null || count($cp_action_ids) === 0) {
            return array();
        }
        $filter = array('where' => 'del_flg = 0 AND id IN(' . join(',', $cp_action_ids). ')');
        $result = $this->cp_actions->find($filter);
        $map = array();
        foreach ($result as $row) {
            $map[$row->id] = $row;
        }
        return $map;
    }

    public function canShiftAction(Cp $cp, CpActionGroup $group, CpAction $action) {
        if ($action->type === CpAction::TYPE_ENTRY) {
            return false;
        }
        if ($cp->type == Cp::TYPE_CAMPAIGN && $group->order_no == 1 && $action->order_no == 1 && $action->type == CpAction::TYPE_QUESTIONNAIRE) {
            return false;
        }
        return true;
    }

    public function canSortAction(Cp $cp, CpActionGroup $group, CpAction $action) {
        if($action->type == CpAction::TYPE_ENTRY || $action->type == CpAction::TYPE_JOIN_FINISH || $action->type == CpAction::TYPE_INSTANT_WIN) {
            return false;
        }
        if($cp->type == Cp::TYPE_CAMPAIGN && $action->type == CpAction::TYPE_QUESTIONNAIRE && $action->order_no == 1 && $group->order_no == 1) {
            return false;
        }
        if($group->order_no == 2 && $action->type == CpAction::TYPE_ANNOUNCE_DELIVERY) {
            return false;
        }
        if($action->type == CpAction::TYPE_ANNOUNCE) {
            if(($cp->selection_method == CpCreator::ANNOUNCE_SELECTION && $group->order_no == 2) ||
            (($cp->selection_method == CpCreator::ANNOUNCE_FIRST || $cp->selection_method == CpCreator::ANNOUNCE_LOTTERY) && $group->order_no == 1)) {
                $announce_actions = $this->getCpActionsByCpActionGroupIdAndType($group->id, CpAction::TYPE_ANNOUNCE);
                if($announce_actions && $announce_actions->total() == 1) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getNotEditableGroups($cp_id) {
        if(!$cp_id) {
            return false;
        }
        $data_builder = aafwDataBuilder::newBuilder();
        $condition = array(
            'cp_id' => $cp_id
        );
        $not_editable_groups = $data_builder->getNotEditableGroups($condition, array());
        return $not_editable_groups;
    }

    public function isExistAnnounceActionInGroup($groupId) {
        $cpActions = $this->getCpActionsByCpActionGroupId($groupId);
        foreach($cpActions as $cpAction){
            if($cpAction->isAnnounceAction()){
                return true;
            }
        }
        return false;
    }

    public function isExistShippingAddressActionInGroup($groupId) {
        $cpActions = $this->getCpActionsByCpActionGroupId($groupId);
        foreach($cpActions as $cpAction){
            if($cpAction->isShippingAddress()){
                return true;
            }
        }
        return false;
    }

    public function isExistShippingAddressActionInFirstGroup($campaignId) {
        $firstGroup = $this->getCpActionGroupByCpIdAndOrderNo($campaignId,1);
        $actions = $this->getCpActionsByCpActionGroupId($firstGroup->id);
        foreach($actions as $action){
            if($action->type == CpAction::TYPE_SHIPPING_ADDRESS){
                return true;
            }
        }
        return false;
    }

    public function isExistAnnounceDeliveryActionFromSecondGroup($campaignId) {

        $cpActionGroups = $this->getCpActionGroupsByCpId($campaignId);
        foreach($cpActionGroups as $key => $cpActionGroup){
            if(!$key) continue;
            if($this->isExistAnnounceActionInGroup($cpActionGroup->id)) {
                return true;
            }
        }
        return false;
    }

    public function isNeedUpdateDuplicateAddressCountCpUser($campaignId) {
        $cpActionGroups = $this->getCpActionGroupsByCpId($campaignId);
        foreach($cpActionGroups as $key => $cpActionGroup){
            if(!$key){
                if(!$this->isExistShippingAddressActionInGroup($cpActionGroup->id)){
                    return false;
                }
            }
            if($this->isExistAnnounceActionInGroup($cpActionGroup->id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Cp $cp
     * @return bool
     */
    public function isAnnounced(Cp $cp) {
        if ($cp->isPermanent()) {
            return false;
        }

        // 当選発表モジュール || 配送を持って発表モジュールを取得
        if ($cp->shipping_method == Cp::SHIPPING_METHOD_MESSAGE) {
            $cp_action_ids = $this->getCpActionIdsByCpIdAndType($cp->id, CpAction::TYPE_ANNOUNCE);
        } else if ($cp->shipping_method == Cp::SHIPPING_METHOD_PRESENT) {
            $cp_action_ids = $this->getCpActionIdsByCpIdAndType($cp->id, CpAction::TYPE_ANNOUNCE_DELIVERY);
        } else {
            return false;
        }

        if (!$cp_action_ids) {
            return false;
        }

        /** @var CpUserActionStatusService $cp_user_action_status_service */
        $cp_user_action_status_service = $cp->getService('CpUserActionStatusService');
        return $cp_user_action_status_service->isExistedStatusByCpActionId($cp_action_ids[0]);
    }

    /**
     * @param $brand_id
     * @param $start_date
     * @param $end_date
     */
    public function getCpsByBrandIdAndPeriod($brand_id, $start_date, $end_date){
        $condition = array(
            "brand_id" => $brand_id,
            "end_date:>=" => $start_date,
            "end_date:<=" => $end_date,
            "status" => Cp::STATUS_FIX,
            "type" => Cp::TYPE_CAMPAIGN
        );

        return $this->cps->find($condition);
    }

    /**
     * @param $brand_id
     * @param $start_date
     * @param $end_date
     * @param $status
     * @return mixed
     */
    public function getCpsByBrandIdAndUpdatedPeriod($brand_id, $start_date, $end_date, $status) {
        $filter = array(
            "brand_id" => $brand_id,
            "updated_at:>=" => $start_date,
            "updated_at:<=" => $end_date,
            "status" => $status,
            "type" => Cp::TYPE_CAMPAIGN
        );

        return $this->cps->find($filter);
    }

    /**
     * @param $cp_id
     * @return string
     */
    public function getCpTitleByCpId($cp_id) {
        $first_action = $this->getFirstActionOfCp($cp_id);

        if ($first_action) {
            $first_action = $first_action->getCpActionData();
        }

        return $first_action->title ? $first_action->title : '名称未設定のキャンペーン';
    }

    /**
     * @param $cp_id
     * @return bool
     */
    public function checkInstantWinCpByCpId($cp_id) {
        $action_groups = $this->getCpActionGroupsByCpId($cp_id);

        foreach ($action_groups as $action_group){
            $cp_actions = $this->getCpActionsByCpActionGroupId($action_group->id);

            foreach ($cp_actions as $action) {
                if ($action->type == CpAction::TYPE_INSTANT_WIN) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $cp_id
     * @return CpAction|null
     */
    public function searchInstantWinCpActionByCpId($cp_id) {
        $cp_actions = $this->getCpActionsInFirstGroupByCpId($cp_id);

        foreach ($cp_actions as $cp_action) {
            if ($cp_action->type == CpAction::TYPE_INSTANT_WIN) {
                return $cp_action;
            }
        }
        return null;
    }
}
