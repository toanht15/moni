<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class CpCreateNewSkeleton extends aafwWidgetBase {
    public function doService( $params = array() ){

        $cp_action = new CpAction();
        $params['CpActionDetail'] = $cp_action->getAvailableCampaignActions();

        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        //常設キャンペーンに配送先情報設置を許可する
        $can_set_shipping_address_for_non_incentive_cp = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_SET_SHIPPING_ADDRESS_FOR_NON_INCENTIVE_CP);
        $params['can_set_shipping_address_for_non_incentive_cp'] = !Util::isNullOrEmpty($can_set_shipping_address_for_non_incentive_cp);
        //常設キャンペーンにクーポン設置を許可する
        $can_set_coupon_for_non_incentive_cp = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_SET_COUPON_FOR_NON_INCENTIVE_CP);
        $params['can_set_coupon_for_non_incentive_cp'] = !Util::isNullOrEmpty($can_set_coupon_for_non_incentive_cp);

        return $params;
    }
}
