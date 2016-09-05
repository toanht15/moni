<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.GiftCardConfig');

class GiftCardConfigService extends aafwServiceBase {
    protected $gift_card_configs;

    public function __construct() {
        $this->gift_card_configs        = $this->getModel('GiftCardConfigs');
    }

    /**
     * CPアクションギフトIDよりグリーティングカード設定情報を取得
     * @param $cp_gift_action_id
     * @return mixed
     */
    public function getGiftCardConfig($cp_gift_action_id) {
        if(!$cp_gift_action_id) return null;
        $filter = array(
            'conditions' => array(
                'cp_gift_action_id' => $cp_gift_action_id,
            ),
        );
        return $this->gift_card_configs->findOne($filter);
    }

    /**
     * グリーティングカードのデフォルト値を設定する
     * @param $cp_gift_action_id
     * @return null
     */
    public function setDefaultGiftCardConfig($cp_gift_action_id) {
        if(!$cp_gift_action_id) return null;
        $gift_card_config = $this->getGiftCardConfig($cp_gift_action_id);
        if ($gift_card_config == null) {
            $gift_card_config =  $this->createDefaultGiftCardConfig($cp_gift_action_id);
        }
        return $gift_card_config;
    }

    /**
     * グリーティングカードを更新する
     * @param $gift_card_config_obj
     * @return mixed
     */
    public function updateGiftCardConfig($gift_card_config_obj) {
        $gift_card_config = $this->getGiftCardConfig($gift_card_config_obj['cp_gift_action_id']);

        if (!$gift_card_config) return null;

        $gift_card_config->text_color           = $gift_card_config_obj['text_color'];

        $gift_card_config->from_x               = $gift_card_config_obj['from_x'];
        $gift_card_config->from_y               = $gift_card_config_obj['from_y'];
        $gift_card_config->from_text_size       = $gift_card_config_obj['from_text_size'];
        $gift_card_config->from_size            = $gift_card_config_obj['from_size'];

        $gift_card_config->to_x                 = $gift_card_config_obj['to_x'];
        $gift_card_config->to_y                 = $gift_card_config_obj['to_y'];
        $gift_card_config->to_text_size         = $gift_card_config_obj['to_text_size'];
        $gift_card_config->to_size              = $gift_card_config_obj['to_size'];

        $gift_card_config->content_x            = $gift_card_config_obj['content_x'];
        $gift_card_config->content_y            = $gift_card_config_obj['content_y'];
        $gift_card_config->content_width        = $gift_card_config_obj['content_width'];
        $gift_card_config->content_height       = $gift_card_config_obj['content_height'];
        $gift_card_config->content_default_text = $gift_card_config_obj['content_default_text'];
        $gift_card_config->content_text_size    = $gift_card_config_obj['content_text_size'];

        return $this->saveGiftCardConfigData($gift_card_config);

    }

    /**
     * グリーティングカードのデフォルト値を作成する
     * @param $cp_gift_action_id
     */
    public function createDefaultGiftCardConfig($cp_gift_action_id) {
        $gift_card_config = $this->createEmptyGiftCardConfigData();
        $gift_card_config->cp_gift_action_id    = $cp_gift_action_id;
        $gift_card_config->card_num             = 1;
        $gift_card_config->required             = 1;
        $gift_card_config->width                = 580;
        $gift_card_config->height               = 348;
        $gift_card_config->text_color           = '#111111';

        $gift_card_config->from_x               = 390;
        $gift_card_config->from_y               = 290;
        $gift_card_config->from_text_size       = 13;
        $gift_card_config->from_size            = 160;

        $gift_card_config->to_x                 = 20;
        $gift_card_config->to_y                 = 20;
        $gift_card_config->to_text_size         = 13;
        $gift_card_config->to_size              = 160;

        $gift_card_config->content_x            = 20;
        $gift_card_config->content_y            = 70;
        $gift_card_config->content_width        = 530;
        $gift_card_config->content_height       = 200;
        $gift_card_config->content_default_text = '';
        $gift_card_config->content_text_size    = 20;
        return $this->saveGiftCardConfigData($gift_card_config);

    }

    public function createEmptyGiftCardConfigData() {
        return $this->gift_card_configs->createEmptyObject();
    }

    public function saveGiftCardConfigData($gift_card_config_data) {
        return $this->gift_card_configs->save($gift_card_config_data);
    }

}

