<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.GiftProductConfig');

use Michelf\Markdown;

class GiftProductConfigService extends aafwServiceBase {
    protected $gift_product_configs;

    public function __construct() {
        $this->gift_product_configs = $this->getModel('GiftProductConfigs');
    }

    /**
     * @param $cp_gift_action_id
     * @return mixed
     */
    public function getGiftProductConfig($cp_gift_action_id) {
        if (!$cp_gift_action_id) return null;
        $filter = array(
            'conditions' => array(
                'cp_gift_action_id' => $cp_gift_action_id,
            ),
        );
        return $this->gift_product_configs->findOne($filter);
    }

    /**
     * 商品情報を設定する
     * @param $gift_product_config_data
     * @return mixed
     */
    public function setGiftProductConfig($gift_product_config_data) {
        if (!$gift_product_config_data['cp_gift_action_id']) return null;
        $gift_product_config = $this->getGiftProductConfig($gift_product_config_data['cp_gift_action_id']);
        if ($gift_product_config == null) {
            $gift_product_config                     = $this->createEmptyGiftProductConfigData();
            $gift_product_config->cp_gift_action_id  = $gift_product_config_data['cp_gift_action_id'];
        }
        $gift_product_config->product_text           = $gift_product_config_data['product_text'];
        $gift_product_config->product_html_content   = Markdown::defaultTransform($gift_product_config_data['product_text']);
        $gift_product_config->postal_name_flg        = $gift_product_config_data['postal_name_flg'];
        $gift_product_config->postal_address_flg     = $gift_product_config_data['postal_address_flg'];
        $gift_product_config->postal_tel_flg         = $gift_product_config_data['postal_tel_flg'];
        $gift_product_config->expire_datetime        = $gift_product_config_data['expire_datetime'];
        return $this->saveGiftProductConfigData($gift_product_config);

    }

    public function createEmptyGiftProductConfigData() {
        return $this->gift_product_configs->createEmptyObject();
    }

    public function saveGiftProductConfigData($gift_product_config_data) {
        $this->gift_product_configs->save($gift_product_config_data);
    }
}