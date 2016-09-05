<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class AddActionSkeleton extends aafwWidgetBase {
    public function doService( $params = array() ){

        $cp_action = new CpAction();
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');

        /** @var CpFlowService $cp_flow_service */
        $params['cp_flow_service'] = $this->getService('CpFlowService');
        $cp = $params['cp_flow_service']->getCpById($params['cp_id']);

        $params['CpActionDetail'] = $cp_action->getAvailableActions($cp);
        $params['groups'] = $params['cp_flow_service']->getCpActionGroupsByCpId($params['cp_id']);

        $params['invisible_types'] = array(CpAction::TYPE_ENTRY, CpAction::TYPE_JOIN_FINISH, CpAction::TYPE_INSTANT_WIN);
        if ($cp->shipping_method == Cp::SHIPPING_METHOD_MESSAGE) {
            $params['invisible_types'][] = CpAction::TYPE_ANNOUNCE_DELIVERY;
        }
        //発表方法は「賞品の発送をもって発表」場合は、当選通知を表示しない
        if ($cp->shipping_method == Cp::SHIPPING_METHOD_PRESENT) {
            $params['invisible_types'][] = CpAction::TYPE_ANNOUNCE;
        }
        if ($cp->selection_method == CpNewSkeletonCreator::ANNOUNCE_NON_INCENTIVE) {
            $params['invisible_types'][] = CpAction::TYPE_ANNOUNCE;
            $params['invisible_types'][] = CpAction::TYPE_GIFT;

            //常設キャンペーンに配送先情報設置を許可する
            $can_set_shipping_address_for_non_incentive_cp = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_SET_SHIPPING_ADDRESS_FOR_NON_INCENTIVE_CP);
            $params['can_set_shipping_address_for_non_incentive_cp'] = !Util::isNullOrEmpty($can_set_shipping_address_for_non_incentive_cp);

            //常設キャンペーンにクーポン設置を許可する
            $can_set_coupon_for_non_incentive_cp = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_SET_COUPON_FOR_NON_INCENTIVE_CP);
            $params['can_set_coupon_for_non_incentive_cp'] = !Util::isNullOrEmpty($can_set_coupon_for_non_incentive_cp);

            if (!$params['can_set_shipping_address_for_non_incentive_cp']) {
                $params['invisible_types'][] = CpAction::TYPE_SHIPPING_ADDRESS;
            }
            if (!$params['can_set_coupon_for_non_incentive_cp']) {
                $params['invisible_types'][] =  CpAction::TYPE_COUPON;
            }
        }

        $canUsePaymentModule = $brand_global_setting_service->getBrandGlobalSetting($cp->brand_id, BrandGlobalSettingService::CAN_USE_PAYMENT_MODULE);
        if(!$canUsePaymentModule->id){
            $params['invisible_types'][] = CpAction::TYPE_PAYMENT;
        }

        $not_editable_groups = $params['cp_flow_service']->getNotEditableGroups($params['cp_id']);
        foreach($not_editable_groups as $not_editable_group) {
            $params['not_editable_groups'][] = $not_editable_group['group_id'];
        }
        if($cp->type == Cp::TYPE_MESSAGE) {
            return $params;
        }
        if($cp->status == Cp::STATUS_SCHEDULE || $cp->status == Cp::STATUS_FIX || $cp->status == Cp::STATUS_DEMO) {
            $first_group = $params['cp_flow_service']->getCpActionGroupByCpIdAndOrderNo($params['cp_id'], 1);
            if(!in_array($first_group->id, $params['not_editable_groups'])) {
                $params['not_editable_groups'][] = $first_group->id;
            }
        }

        return $params;
    }
}
