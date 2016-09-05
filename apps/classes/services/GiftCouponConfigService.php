<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.GiftCouponConfig');

use Michelf\Markdown;

class GiftCouponConfigService extends aafwServiceBase {
    protected $gift_coupon_configs;

    public function __construct() {
        $this->gift_coupon_configs = $this->getModel('GiftCouponConfigs');
    }

    /**
     * クーポン設定の情報を取得する
     * @param $cp_gift_action_id
     * @return mixed
     */
    public function getGiftCouponConfig($cp_gift_action_id) {
        if (!$cp_gift_action_id) return null;
        $filter = array(
            'conditions' => array(
                'cp_gift_action_id' => $cp_gift_action_id,
            ),
        );
        return $this->gift_coupon_configs->findOne($filter);
    }

    /**
     * クーポンを設定する
     * @param $gift_coupon_config_obj
     * @return mixed
     */
    public function setGiftCouponConfig($gift_coupon_config_obj) {
        if (!$gift_coupon_config_obj['cp_gift_action_id']) return null;
        $gift_coupon_config = $this->getGiftCouponConfig($gift_coupon_config_obj['cp_gift_action_id']);
        if ($gift_coupon_config == null) {
            $gift_coupon_config                     = $this->createEmptyGiftCouponConfigData();
            $gift_coupon_config->cp_gift_action_id  = $gift_coupon_config_obj['cp_gift_action_id'];
        }
        $gift_coupon_config->coupon_id              = $gift_coupon_config_obj['coupon_id'];
        $gift_coupon_config->message                = $gift_coupon_config_obj['message'];
        $gift_coupon_config->html_content           = Markdown::defaultTransform($gift_coupon_config_obj['message']);
        return $this->saveGiftCouponConfigData($gift_coupon_config);

    }

    public function createEmptyGiftCouponConfigData() {
        return $this->gift_coupon_configs->createEmptyObject();
    }

    public function saveGiftCouponConfigData($gift_coupon_config_data) {
        $this->gift_coupon_configs->save($gift_coupon_config_data);
    }
}