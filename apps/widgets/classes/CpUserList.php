<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpUserList extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $params['cp'] = $cp_flow_service->getCpById($params['list_page']['cp_id']);
        $params['cp_action'] = $cp_flow_service->getCpActionById($params['list_page']['action_id']);
        $params['cp_actions'] = $cp_flow_service->getCpActionsByCpId($params['list_page']['cp_id']);

        // 送信済・送信対象ユーザの取得
        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $this->getService('CpUserListService');

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        // ニックネーム等の個人情報非表示モード
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_PERSONAL_INFO);
        $params['is_hide_personal_info'] = !Util::isNullOrEmpty($brand_global_setting) ? true : false;

        // 現在の予約情報を取得
        /** @var CpMessageDeliveryService $message_delivery_service */
        $message_delivery_service = $this->getService('CpMessageDeliveryService');
        $params['current_reservation'] = $message_delivery_service->getOrCreateCurrentReservation($params['list_page']['brand_id'], $params['list_page']['action_id']);
        //ページ内の送信済ユーザと、送信対象ユーザ
        foreach($params['fan_list_users'] as $fanList) {
            $user_ids[] = $fanList->user_id;
        }

        $params['page_user_message'] = $cp_user_list_service->getPageUserMessage($user_ids, $params['cp'], $params['cp_action'], $params['current_reservation']);
        $params['page_sent_user_count'] = 0;

        //当選者確定ボタンを追加するかどうかチェックする
        $action_group = $cp_flow_service->getCpActionGroupByAction($params['list_page']['action_id']);
        $cp_actions = $cp_flow_service->getCpActionsByCpActionGroupId($action_group->id);

        $has_announce_module = false;
        $has_instant_win_module = false;
        foreach ($cp_actions as $cp_action) {
            if($cp_action->type == CpAction::TYPE_ANNOUNCE){
                $has_announce_module = true;
            }
            if($cp_action->type == CpAction::TYPE_INSTANT_WIN){
                $has_instant_win_module = true;
            }
        }

        if($action_group->order_no != 1 && $has_announce_module && !$has_instant_win_module){
            $params['has_fix_target_step'] = true;
        }

        //通知メールを送信するかどうか
        $params['delivered_target_message'] = true;

        foreach($params['page_user_message'] as $page_user_message) {
            if(!$page_user_message[0]) {
                continue;
            }

            if ($page_user_message[1] && !$page_user_message[2]){
                $params['delivered_target_message'] = false;
            }

            $params['page_sent_user_count'] += 1;
        }
        $params['hasSearchCondition'] = $cp_user_list_service->hasSearchCondition($params['search_condition']);
        $params['page_reservation_target'] = $message_delivery_service->getCurrentMessageReservedTarget($params['current_reservation']->id, $user_ids);

        $params['selected_target'] = $message_delivery_service->getCurrentMessageReservedTarget($params['current_reservation']->id, null);

        //当選者を確定するかどうか
        $fixed_target = $message_delivery_service->checkFixedTargetByReservationId($params['current_reservation']->id);
        $params['fixed_target'] = $fixed_target ? true : false;

        //マネジャーの権限を取得する
        $manager_account = $this->getManagerAccount();
        $params['manager_full_control'] = $manager_account->full_control_flg;

        /** @var SocialLikeService $social_like_service */
        $social_like_service = $this->getService('SocialLikeService');
        $params['isSocialLikesEmpty'] = $social_like_service->isEmptyTable() ? 1 : 0;

        /** @var TwitterFollowService $twitter_follow_service */
        $twitter_follow_service = $this->getService('TwitterFollowService');
        $params['isTwitterFollowsEmpty'] = $twitter_follow_service->isEmptyTable() ? 1 : 0;

        if(!$params['cp']->isLimitCp() && $cp_flow_service->isExistShippingAddressActionInFirstGroup($params['cp']->id) && $cp_flow_service->isExistAnnounceDeliveryActionFromSecondGroup($params['cp']->id) && $cp_flow_service->isExistAnnounceActionInGroup($params['cp_action']->cp_action_group_id)){
            $params['isShowDuplicateAddressCpUserList'] =  true;
        }else{
            $params['isShowDuplicateAddressCpUserList'] =  false;
        }
        
        // 当選通知モジュールが含まれているかチェック
        // ステップグループ１の場合は、無条件に除外する
        $params['is_include_type_announce'] = false;
        $isFirstGroup = $params['cp_action']->getCpActionGroup()->isFirstGroup();
        if (!$isFirstGroup) {
            $group_actions = $cp_flow_service->getCpActionsByCpActionGroupId($params['cp_action']->cp_action_group_id);
            foreach ($group_actions as $action) {
                if ($action->type === CpAction::TYPE_ANNOUNCE ||
                    $action->type === CpAction::TYPE_ANNOUNCE_DELIVERY) {
                    $params['is_include_type_announce'] = true;
                }

                if($action->type === CpAction::TYPE_ANNOUNCE_DELIVERY) {
                    $params['is_include_type_announce_delivery'] = true;
                }
            }
        }

        //Segment Condition
        if($params['show_segment_condition']) {
            $params['segments'] = $this->getSegmentCondition($params['brand']->id);
        }

        return $params;
    }

    /**
     * @return null
     */
    private function getManagerAccount(){
        /** @var ManagerService $manager_service */
        $manager_service = $this->getService('ManagerService');
        $managerUserId = $_SESSION['managerUserId'];

        if(!$managerUserId) {
            return null;
        }

        $manager_account = $manager_service->getManagerFromHash($managerUserId);

        return $manager_account;
    }
}
