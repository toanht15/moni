<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpDataDownloadModal extends aafwWidgetBase {

    public function doService($params = array()) {
        $service_factory = new aafwServiceFactory();
        /** @var CpUserService $cp_user_service */
        $cp_user_service = $service_factory->create('CpUserService');
        /** @var CpMessageDeliveryService $cp_message_delivery_service */
        $cp_message_delivery_service = $service_factory->create('CpMessageDeliveryService');

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $cp_list_service = $service_factory->create('CpListService');

        $params['cp'] = $cp_flow_service->getCpById($params['cp_id']);

        $is_instant_win_cp = $cp_flow_service->checkInstantWinCpByCpId($params['cp']->id);

        $params['gift_actions'] = array();
        $params['fixed_target_actions'] = array();
        $params['shipping_actions'] = array();
        $action_no = 0;
        if(!$params['group_array']) {
            $cp_ids[] = $params['cp']->id;
            $cps = $cp_list_service->getListPublicCp($cp_ids);
            $params['group_array'] = $cps[$params['cp']->id];
        }
        foreach($params['group_array'] as $group) {

            $fixed_target_action_id = null;
            $announce_action_no = null;

            foreach($group as $action_id => $action) {
                if(!is_array($action)) {
                    continue;
                }
                $action_no++;
                if($action['type'] == CpAction::TYPE_GIFT && !$params['first_gift_action_id']) {
                    $params['first_gift_action_id'] = $action_id;
                    $params['is_gift_campaign_with_address'] = $this->getGiftCampaignWithAddress($params['first_gift_action_id']);
                }

                if ($is_instant_win_cp) {
                    // キャッシュより、配送先情報を取得しており、当選発表のメッセージを送信したユーザを取得
                    if ($action['type'] == CpAction::TYPE_ANNOUNCE) {
                        if ($cp_user_service->getSendMessageCount($action_id) > 0) {
                            $params['fixed_target_actions'][$action_id]['order_no'] = $action_no;
                        }
                    }

                } else {
                    if ($action['type'] == CpAction::TYPE_ANNOUNCE_DELIVERY || $action['type'] == CpAction::TYPE_ANNOUNCE) {
                        $announce_action_no = $action_no;
                    }

                    if ($cp_message_delivery_service->checkExistFixedTargetByCpActionId($action_id)) {
                        if ($action['type'] == CpAction::TYPE_ANNOUNCE_DELIVERY || $action['type'] == CpAction::TYPE_ANNOUNCE) {
                            $params['fixed_target_actions'][$action_id]['order_no'] = $action_no;
                        } else {
                            $fixed_target_action_id = $action_id;
                        }
                    }
                }

                if ($action['type'] == CpAction::TYPE_SHIPPING_ADDRESS) {
                    if ($cp_user_service->getFinishActionCount($action_id) > 0) {
                        $params['shipping_actions'][$action_id]['order_no'] = $action_no;
                    }
                }
            }

            if($fixed_target_action_id && $announce_action_no){
                $params['fixed_target_actions'][$fixed_target_action_id]['order_no'] = $announce_action_no;
            }
        }
        $params['can_get_fid_report'] = $this->canGetFidReport($params);

        return $params;
    }

    public function getGiftCampaignWithAddress($cp_action_id) {
        if(!Util::isAcceptRemote()) {
            return false;
        }
        /** @var CpGiftActionService $cp_gift_action_service */
        $cp_gift_action_service = $this->getService('CpGiftActionService');
        $cp_gift_action = $cp_gift_action_service->getCpGiftAction($cp_action_id);
        return $cp_gift_action->incentive_type == CpGiftAction::INCENTIVE_TYPE_PRODUCT;
    }

    public function canGetFidReport($params) {
        if($params['pageStatus']['manager']->canView()) {
            return true;
        }
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');

        return $brand_global_setting_service->getBrandGlobalSetting($params['brand_id'], BrandGlobalSettingService::CAN_GET_FID_REPORT);
    }
}
