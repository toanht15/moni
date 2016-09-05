<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.CouponService');
AAFW::import('jp.aainc.classes.services.GiftCardConfigService');
AAFW::import('jp.aainc.classes.services.GiftCardUploadService');
AAFW::import('jp.aainc.classes.services.GiftCouponConfigService');
AAFW::import('jp.aainc.classes.services.GiftMessageService');

class UserMessageThreadActionGift extends aafwWidgetBase{

    protected $service_factory;
    public function doService( $params = array() ){
        $this->service_factory = new aafwServiceFactory();

        /** @var GiftCardConfigService $gift_card_config_service */
        $gift_card_config_service   = $this->service_factory->create('GiftCardConfigService');

        /** @var GiftCardUploadService $gift_card_upload_service */
        $gift_card_upload_service   = $this->service_factory->create('GiftCardUploadService');

        /** @var GiftMessageService $gift_message_service */
        $gift_message_service       = $this->service_factory->create('GiftMessageService');

        //ユーザのギフトの基本的なデータを作る
        if (!$gift_message_service->getGiftMessageByCpUserIdAndCpGiftActionId($params['cp_user']->id, $params['message_info']['concrete_action']->id)) {
            $coupon_code_id = $this->getCouponCodeId($params['message_info']['concrete_action']);
            $gift_message_service->createDefaultGiftMessage(
                $params['cp_user']->id,
                $params['message_info']['concrete_action']->id,
                $coupon_code_id
            );
        }

        if ($params['message_info']['concrete_action']->card_required) {
            $params['gift_card_config'] = $gift_card_config_service->getGiftCardConfig($params['message_info']['concrete_action']->id);
            $params['gift_card_upload'] = $gift_card_upload_service->getGiftCardUploads($params['gift_card_config']->id);
        }

        $params['gift_message'] = $gift_message_service->getGiftMessageByCpUserIdAndCpGiftActionId($params['cp_user']->id, $params['message_info']['concrete_action']->id);

        return $params;
    }
    private function getCouponCodeId($cp_gift_action) {

        if ($cp_gift_action->incentive_type == CpGiftAction::INCENTIVE_TYPE_PRODUCT) return GiftMessage::PRODUCT_CODE_ID;

        /** @var CouponService $coupon_service */
        $coupon_service             = $this->service_factory->create('CouponService');

        /** @var GiftCouponConfigService $gift_coupon_config_service */
        $gift_coupon_config_service = $this->service_factory->create('GiftCouponConfigService');

        $gift_coupon_config     = $gift_coupon_config_service->getGiftCouponConfig($cp_gift_action->id);
        $coupon_code_id         = $coupon_service->getCouponCodeForDistribute($gift_coupon_config->coupon_id);
        $coupon_service->incrementReservedNum($coupon_code_id);

        return $coupon_code_id;
    }
}
