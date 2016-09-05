<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.CpGiftActionService');
AAFW::import('jp.aainc.classes.services.GiftCardConfigService');
AAFW::import('jp.aainc.classes.services.GiftCardUploadService');
AAFW::import('jp.aainc.classes.services.GiftCouponConfigService');

class EditActionGift extends aafwWidgetBase{
    private $ActionForm;
    private $ActionError;
    private $cp;
    private $cp_gift_action;
    const REQUIRE_FLG = 1;

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service            = $service_factory->create('CpFlowService');

        /** @var CpGiftActionService $cp_gift_action_service */
        $cp_gift_action_service     = $service_factory->create('CpGiftActionService');

        /** @var GiftCardConfigService $gift_card_config_service */
        $gift_card_config_service   = $service_factory->create('GiftCardConfigService');

        /** @var GiftCardUploadService $gift_card_upload_service */
        $gift_card_upload_service   = $service_factory->create('GiftCardUploadService');

        /** @var GiftCouponConfigService $gift_coupon_config_service */
        $gift_coupon_config_service = $service_factory->create('GiftCouponConfigService');

        /** @var GiftProductConfigService $gift_product_config_service */
        $gift_product_config_service = $service_factory->create('GiftProductConfigService');

        /** @var CouponService $coupon_service */
        $coupon_service = $service_factory->create('CouponService');

        /** @var PrefectureService $prefectureService */
        $prefectureService = $service_factory->create('PrefectureService');

        $params['prefectures'] = $prefectureService->getPrefecturesKeyValue();

        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);
        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];


        $this->cp = $cp_flow_service->getCpById($params['cp_id']);

        // エントリーアクション情報を取得する
        list(, $entry_concrete_action) = $cp_flow_service->getEntryActionInfoByCpId($params['cp_id']);

        $params['campaign_info']['title']                = $entry_concrete_action->title;
        $params['campaign_info']['image_url']            = $entry_concrete_action->image_url;
        $params['campaign_info']['text']                 = $entry_concrete_action->text;
        $params['campaign_info']['html_content']         = $entry_concrete_action->html_content;

        $this->cp_gift_action = $cp_gift_action_service->getCpGiftAction($params['action_id']);

        $params['gift_card_config'] = $gift_card_config_service->setDefaultGiftCardConfig($this->cp_gift_action->id);

        $params['gift_card_uploads'] = $gift_card_upload_service->getGiftCardUploads($params['gift_card_config']->id);

        $params['coupons'] = $coupon_service->getCpCouponByBrandIdAndWinnerCount($this->cp->brand_id, $this->cp->winner_count);

        $params['gift_coupon_config'] = $gift_coupon_config_service->getGiftCouponConfig($this->cp_gift_action->id);
        if ($params['gift_coupon_config'] != null) {
            $params['current_coupon'] = $coupon_service->getCouponById($params['gift_coupon_config']->coupon_id);
        }

        $params['gift_product_config'] = $gift_product_config_service->getGiftProductConfig($this->cp_gift_action->id);

        //郵送商品の締め切りを設定する為の用意
        $expireTimeHH=[];
        for ($i = 0; $i < 24; $i++) {
            $h = sprintf('%02d', $i);
            $expireTimeHH[$h] = $h;
        }
        $expireTimeMM=[];
        for ($i = 0; $i < 60; $i++) {
            $m = sprintf('%02d', $i);
            $expireTimeMM[$m] = $m;
        }
        $params['expireTimeHH'] = $expireTimeHH;
        $params['expireTimeMM'] = $expireTimeMM;

        if ($params['gift_product_config']->expire_datetime && $params['gift_product_config']->expire_datetime != '0000-00-00 00:00:00') {
            $params['currentDate']      = date('Y/m/d', strtotime($params['gift_product_config']->expire_datetime));
            $params['currentTimeHH']    = date('H', strtotime($params['gift_product_config']->expire_datetime));
            $params['currentTimeMM']    = date('i', strtotime($params['gift_product_config']->expire_datetime));
        }

        $params['postal_name_flg_default']       = $params['gift_product_config'] ? $params['gift_product_config']->postal_name_flg : self::REQUIRE_FLG;
        $params['postal_address_flg_default']    = $params['gift_product_config'] ? $params['gift_product_config']->postal_address_flg : self::REQUIRE_FLG;
        $params['postal_tel_flg_default']        = $params['gift_product_config'] ? $params['gift_product_config']->postal_tel_flg : self::REQUIRE_FLG;

        if($this->ActionError) {
            $_SESSION['cpGiftError'] = $this->ActionError;
        } else {
            $this->assign('saved',1);
        }

        if ($this->cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($this->cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        return $params;
    }

    public function getContainerType() {
        return $this->cp ? $this->cp->brand_id : 'common';
    }
}
