<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class RecruitCpUserList extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $params['cp'] = $cp_flow_service->getCpById($params['list_page']['cp_id']);
        $params['cp_action'] = $cp_flow_service->getCpActionById($params['list_page']['action_id']);
        $params['cp_actions'] = $cp_flow_service->getCpActionsByCpId($params['list_page']['cp_id']);

        // 送信済・送信対象ユーザの取得
        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $this->getService('CpUserListService');

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
        foreach($params['page_user_message'] as $page_user_message) {
            if(!$page_user_message[0]) {
                continue;
            }
            $params['page_sent_user_count'] += 1;
        }
        $params['hasSearchCondition'] = $cp_user_list_service->hasSearchCondition($params['search_condition']);
        $params['page_reservation_target'] = $message_delivery_service->getCurrentMessageReservedTarget($params['current_reservation']->id, $user_ids);

        /** @var SocialLikeService $social_like_service */
        $social_like_service = $this->getService('SocialLikeService');
        $params['isSocialLikesEmpty'] = $social_like_service->isEmptyTable() ? 1 : 0;

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
                    break;
                }
            }
        }

        return $params;
    }
}
