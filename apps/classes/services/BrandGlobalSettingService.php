<?php
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.entities.BrandGlobalSetting');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandGlobalSettingService extends aafwServiceBase {
    protected $brand_global_settings;

    // メッセージ送信のマニュアルを表示するか
    const HIDE_FAN_LIST_MESSAGE_MANUAL      = 1;
    const VIEW_FAN_LIST_MESSAGE_MANUAL      = 0;
    const HIDE_FAN_LIST_MESSAGE_MANUAL_KEY         = "hide_fan_list_message_manual";

    // 賞品の発送をもって時のマニュアルを表示するか
    const HIDE_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL     = 1;
    const VIEW_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL     = 0;
    const HIDE_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL_KEY = 'hide_fan_list_announce_delivery_message_manual';

    const LOGIN_PAGE_CONTENTS               = "login_page_contents";

    const SIGNUP_PAGE_CONTENTS              = "signup_page_contents";
    const ORIGINAL_SNS_ACCOUNTS             = "original_sns_accounts";
    // ADEBISを利用する場合、ADEBISから発行されたトークンを設定する
    const ADEBIS_BRAND_TOKEN                = "adebis_brand_token";
    const CAN_GET_FID_REPORT                = "can_get_fid_report";
    // ファン数を表示しない
    const LP_MODE                           = "LP_MODE";
    const CAN_DOWNLOAD_BRAND_USER_LIST      = "can_download_brand_user_list";
    //データダウンロードで参加者情報にfidとリファラを追加する
    const PUBLIC_FID_AND_REFERER            = "public_fid_and_referer";

    const CAN_LOOK_DAILY_PV                 = "can_look_daily_pv";
    // CMSにNEWラベルをつける
    const NEW_PAGE_LABEL                    = 'new_page_label';
    // トップパネルのテキストを省略しないですべてを表示する
    const TOP_PANEL_FULL_TEXT               = "top_panel_full_text";
    // お問い合わせリンクを表示しない
    const HIDDEN_INQUIRY_LINK               = "hidden_inquiry_link";
    // CMSカテゴリー一覧の日付を表示しない
    const CMS_CATEGORY_LIST_DATETIME_HIDDEN = "cms_category_list_datetime_hidden";

    const CAN_SET_NG_WORD                   = "can_set_ng_word";

    const OLYMPUS_CUSTOM_HEADER_FOOTER      = "olympus_custom_header_footer";

    const CAN_USE_FAN_COUNT_MARKDOWN        = "can_use_fan_count_markdown";

    const CAN_SAVE_BRAND_USER_RELATION_NO   = "can_save_brand_user_relation_no";

    const AUTHENTICATION_PAGE               = "authentication_page";

    const WHITEBELG_CUSTOM_HEADER_FOOTER      = "whitebelg_custom_header_footer";

    const KENKEN_CUSTOM_HEADER_FOOTER      = "kenken_custom_header_footer";

    const UQ_CUSTOM_HEADER_FOOTER          = "uq_custom_header_footer";

    const IS_SNS_CONNECTING_DISABLED        = "is_sns_connecting_disabled";

    const SUGAO_CUSTOM_LOGIN_AND_TWITTER_ACTION      = "sugao_custom_login_and_twitter_action";
    
    const CAN_SET_HEADER_TAG_TEXT                  = "can_set_header_tag_text";

    const CAN_SET_SIGN_UP_MAIL                  = "can_set_sign_up_mail";

    // 常設CPで使用可能にする
    const CAN_SET_SHIPPING_ADDRESS_FOR_NON_INCENTIVE_CP = "can_set_shipping_address_for_non_incentive_cp";
    const CAN_SET_COUPON_FOR_NON_INCENTIVE_CP           = "can_set_coupon_for_non_incentive_cp";

    const SHOW_CURRENT_STAMP_RALLY_CP       = "show_current_stamp_rally_cp";

    const CAN_USE_STAMP_RALLY_TEMPLATE      = "can_use_stamp_rally_template";

    const CAN_DOWNLOAD_BRAND_FAN_LIST             = "can_download_brand_fan_list";

    const CAN_ADD_EMBED_PAGE                = "can_add_embed_page";

    const HIDE_BRAND_TOP_PAGE                = "hide_brand_top_page";

    const CAN_UPLOAD_ORIGINAL_VIDEO         = "can_upload_original_video";

    const CAN_USE_CATEGORIES_API            = "can_use_categories_api";

    const MSBC_CUSTOM_LOGIN_PAGE            = "msbc_custom_login_page";

    const CAN_SET_CRM_TEXT_MAIL             = "can_set_crm_text_mail";

    const CAN_SET_MAIL_FROM_ADDRESS         = "can_set_mail_from_address";

    const CAN_USE_SNS_PANELS_API            = "can_use_sns_panels_api";

    const CAN_USE_PAGE_API                  = "can_use_page_api";

    const HIDE_PERSONAL_INFO                = "hide_personal_info";

    const CAN_USE_LOGIN_LIMIT_SETTING       = "can_use_login_limit_setting";

    const CAN_USE_SP_FREE_AREA                = "can_use_sp_free_area";
    
    const CAN_USE_PAYMENT_MODULE            ="can_use_payment_module";

    //ヘッダのログインボタン非表示する
    const HIDE_HEADER_LOGIN_BUTTON          = "hide_header_login_button";

    //ブランドロゴ非表示する
    const HIDE_BRAND_LOGO                  = "hide_brand_logo";

    //問い合わせリンク非表示する
    const HIDE_INQUIRY_LINK               = "hide_inquiry_link";

    // 新モニ新規登録時にオプトインチェックボックスを非表示にする (オプトアウトとなる)
    const HIDE_OPTIN_CHECKBOX               = "hide_optin_checkbox";

    //フッターのメニューを非表示
    const HIDE_FOOTER_MENU               = "hide_footer_menu";

    // 当選発表タグ用
    const CAN_USE_AAID_HASH_TAG             = "can_use_aaid_hash_tag";

    public function __construct() {
        $this->brand_global_settings = $this->getModel("BrandGlobalSettings");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function getBrandGlobalSettingByName($settings, $name) {
        foreach ($settings as $setting) {
            if ($setting->name == $name) {
                return $setting;
            }
        }
        return null;
    }

    public function getBrandGlobalSettingsByBrandId($brand_id) {
        if(!$brand_id) return;
        $filter = array(
            'brand_id' => $brand_id
        );
        return $this->brand_global_settings->find($filter);
    }

	public function getBrandGlobalSetting($brandId, $name) {
        if(!$brandId || !$name) return '';
		$filter = array(
			'brand_id' => $brandId,
			'name' => $name,
		);
		return $this->brand_global_settings->findOne($filter);
	}

    public function createBrandGlobalSetting() {
        return $this->brand_global_settings->createEmptyObject();
    }


    /**
     * ブランドグローバルセッティングの参加者一覧マニュアル非表示フラグを変更する
     * @param $brandId
     * @param $hideManual
     * @return bool
     */
    public function changeHideFanListMessageManual($brandId, $hideManual) {
        if(in_array($hideManual, array(self::HIDE_FAN_LIST_MESSAGE_MANUAL, self::VIEW_FAN_LIST_MESSAGE_MANUAL)) == false){
            return false;
        }
        $brandGlobalSetting = $this->getBrandGlobalSetting($brandId, self::HIDE_FAN_LIST_MESSAGE_MANUAL_KEY);
        if(!$brandGlobalSetting && $hideManual == self::HIDE_FAN_LIST_MESSAGE_MANUAL) {
            //フラグを立てるときは1レコード追加
            $brandGlobalSetting = $this->createBrandGlobalSetting();
            $brandGlobalSetting->brand_id = $brandId;
            $brandGlobalSetting->name = self::HIDE_FAN_LIST_MESSAGE_MANUAL_KEY;
            $brandGlobalSetting->content = $hideManual;
            $this->brand_global_settings->save($brandGlobalSetting);

        }elseif($brandGlobalSetting && $hideManual == self::VIEW_FAN_LIST_MESSAGE_MANUAL){
            //下ろすときは1レコード削除
            $this->brand_global_settings->delete($brandGlobalSetting);
        }

        BrandInfoContainer::getInstance()->clear($brandId);

        return true;
    }

    public function changeAnnounceDeliveryFanListMessageManual($brand_id, $hide_manual) {
        $brand_global_setting = $this->getBrandGlobalSetting($brand_id, self::HIDE_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL_KEY);

        // レコードなしで非表示の時はレコード追加
        if (!$brand_global_setting && $hide_manual == self::HIDE_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL) {
            $brand_global_setting = $this->createBrandGlobalSetting();
            $brand_global_setting->brand_id = $brand_id;
            $brand_global_setting->name = self::HIDE_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL_KEY;
            $brand_global_setting->content = $hide_manual;
            $this->brand_global_settings->save($brand_global_setting);
        } elseif($brand_global_setting && $hide_manual == self::VIEW_FAN_LIST_ANNOUNCE_DELIVERY_MESSAGE_MANUAL) {
            // レコードあり 表示設定の時はレコード削除
            $this->brand_global_settings->delete($brand_global_setting);
        }

        BrandInfoContainer::getInstance()->clear($brand_id);
    }

    /**
     * @param $global_setting
     */
    public function saveGlobalSetting($global_setting){
        $this->brand_global_settings->save($global_setting);
        BrandInfoContainer::getInstance()->clear($global_setting->brand_id);
    }

    /**
     * @param $name
     * @param $brand_id
     * @return mixed
     */
    public function getSettingByNameAndBrandId($name, $brand_id) {
        $filter = array(
            'conditions' => array(
                'name' => $name,
                'brand_id' => $brand_id
            )
        );
        return $this->brand_global_settings->findOne($filter);
    }

    /**
     * @param $name
     * @param $brand_id
     * @return mixed
     */
    public function getSettingsByNameAndBrandId($name, $brand_id) {
        $filter = array(
            'conditions' => array(
                'name' => $name,
                'brand_id' => $brand_id
            )
        );
        return $this->brand_global_settings->find($filter);
    }
}