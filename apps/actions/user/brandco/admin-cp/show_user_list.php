<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.services.BrandGlobalSettingService');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');
AAFW::import('jp.aainc.classes.services.SegmentService');

class show_user_list extends BrandcoGETActionBase {

    use CpActionGroupTrait;

    protected $ContainerName = 'show_user_list';

    public $NeedOption = array(BrandOptions::OPTION_CP, BrandOptions::OPTION_CRM);
    public $NeedAdminLogin = true;

    public function doThisFirst() {

        $this->Data['cp_id'] = $this->GET['exts'][0];
        $this->Data['action_id'] = $this->GET['exts'][1];
        $this->Data['join_user'] = $this->GET['join_user'] ? $this->GET['join_user'] : '';
        $this->Data['brand'] = $this->getBrand();

        $this->deleteErrorSession();
        $this->setSearchConditionSession($this->Data['cp_id'],null);
        $this->setBrandSession('orderCondition', null);

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService('CpFlowService');

        if (!Util::isNullOrEmpty($this->Data['action_id'])) {
            $action = $cp_flow_service->getCpActionById($this->Data['action_id']);
        }

        // キャンペーンのタイプを取得するためにキャンペーン情報を取得(メッセージでも送信済み・対象の絞り込み実装時に削除する)
        $this->Data['cp'] = $cp_flow_service->getCpById($this->Data['cp_id']);

        $group_actions = $cp_flow_service->getCpActionsByCpActionGroupId($action->cp_action_group_id);

        if ($action->order_no == 1) {
            $this->Data['first_action_id'] = $action->id;
        } else {
            if ($group_actions) {
                $first_action = $group_actions->current();
            }
            $this->Data['first_action_id'] = $first_action->id;
        }

        $this->Data['is_group_fixed'] = true;

        foreach ($group_actions as $group_action) {
            if ($group_action->status == CpAction::STATUS_DRAFT) {
                $this->Data['is_group_fixed'] = false;
                break;
            }
        }

        $all_cp_actions = $cp_flow_service->getCpActionsByCpId($this->Data['cp_id']);
        $this->Data['is_include_type_announce'] = false;
        $this->Data['is_include_type_instagram_hashtag'] = false;
        $this->Data['is_include_type_photo'] = false;
        $this->Data['is_include_type_tweet'] = false;
        foreach ($group_actions as $action) {
            if ($action->type === CpAction::TYPE_ANNOUNCE) {
                $this->Data['is_include_type_announce'] = true;
                continue;
            }
        }
        foreach($all_cp_actions as $cp_action) {
            if ($cp_action->type === CpAction::TYPE_INSTAGRAM_HASHTAG) {
                $this->Data['is_include_type_instagram_hashtag'] = true;
                continue;
            }
            if ($cp_action->type === CpAction::TYPE_PHOTO) {
                $this->Data['is_include_type_photo'] = true;
                continue;
            }
            if ($cp_action->type === CpAction::TYPE_TWEET) {
                $this->Data['is_include_type_tweet'] = true;
            }
        }
        // ステップグループ１に、配送先モジュールが含まれているかチェック
        $this->cp_action_groups = $this->getModel('CpActionGroups');
        $cp_action_groups = $this->getCpActionGroupsByCpId($this->Data['cp_id']);
        $first_cp_action_group = $cp_action_groups->current();
        $group_actions = $cp_flow_service->getCpActionsByCpActionGroupId($first_cp_action_group->id);
        foreach ($group_actions as $action) {
            if ($action->type === CpAction::TYPE_SHIPPING_ADDRESS) {
                $this->Data['is_include_type_shipping_address'] = true;
                break;
            }
        }
    }

    public function validate() {

        if ($this->Data['cp']->status == Cp::STATUS_DRAFT) {
            return "404";
        }

        $cp_validator = new CpValidator($this->Data['brand']->id);
        if (!$cp_validator->isOwner($this->Data['cp_id'])) {
            return false;
        }
        if (!$cp_validator->isOwnerOfAction($this->Data['action_id'])) {
            return false;
        }
        if (!$cp_validator->isFirstActionOfGroup($this->Data['action_id'])) {
            return false;
        }
        if (!$cp_validator->isIncludedInCp($this->Data['cp_id'], $this->Data['action_id'])) {
            return false;
        }

        return true;
    }

    function doAction() {

        // 当選通知を送信したユーザのみで絞り込み
        if($this->GET['sent_target']) {
            $search_condition = $this->getSearchConditionSession($this->Data['cp_id']);
            $search_condition[CpCreateSqlService::SEARCH_QUERY_USER_TYPE] = $this->Data['sent_target'] = CpCreateSqlService::QUERY_USER_SENT.'/'.$this->Data['action_id'];
            $this->setSearchConditionSession($this->Data['cp_id'], $search_condition);
        }

        /** @var CpMessageDeliveryService $message_delivery_service */
        $message_delivery_service = $this->createService('CpMessageDeliveryService');
        $reservation = $message_delivery_service->getOrCreateCurrentReservation($this->getBrand()->id, $this->Data['first_action_id']);

        if ($reservation->isOverScheduled()) {
            return "redirect:" . Util::rewriteUrl('admin-cp', "show_reservation_info", array("action_id" => $this->Data['action_id']), array("mid" => $this->GET['mid']));
        }

        $this->Data["reservation"] = $reservation;

        //当選者を確定するかどうか
        $fixed_target = $message_delivery_service->checkFixedTargetByReservationId($reservation->id);
        $this->Data['fixed_target'] = $fixed_target ? true : false;

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        $this->Data['isManager'] = $this->Data['pageStatus']['manager']->id ? $this->Data['pageStatus']['manager']->id : '';
        $this->Data['can_download_brand_user_list'] = $this->Data['isManager'] ||
            $brand_global_setting_service->getBrandGlobalSetting($this->Data['brand']->id, BrandGlobalSettingService::CAN_DOWNLOAD_BRAND_USER_LIST);

        /** @var SocialLikeService $social_like_service */
        $social_like_service = $this->createService('SocialLikeService');
        $this->Data['isSocialLikesEmpty'] = $social_like_service->isEmptyTable();

        /** @var TwitterFollowService $twitter_follow_service */
        $twitter_follow_service = $this->createService('TwitterFollowService');
        $this->Data['isTwitterFollowsEmpty'] = $twitter_follow_service->isEmptyTable();

        //Segment Message Action
        if($this->Data['cp']->isCpTypeMessage()) {
            $segment_condition_session = $this->getBrandSession(SegmentService::SEGMENT_CONDITION_SESSION_KEY);
            $this->Data['segment_condition_session'] = $segment_condition_session;
        }

        return 'user/brandco/admin-cp/show_user_list.php';
    }
}
