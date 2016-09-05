<?php
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandPageSettingService extends aafwServiceBase {

    private $brand_page_settings;
    private $logger;

    const STATUS_PUBLIC     = 1;
    const STATUS_NON_PUBLIC = 2;

    public static $rdPublicStatus = array(
        self::STATUS_PUBLIC     => '公開',
        self::STATUS_NON_PUBLIC => '非公開',
    );

    const MODE_PRIVACY = 'privacy';
    const MODE_AGREEMENT = 'agreement';

    const AUTHENTICATION_PAGE_DEFAULT_PREVIEW_MODE = 1;
    const AUTHENTICATION_PAGE_SESSION_PREVIEW_MODE = 2;

    const AGE_RESTRICT_MARKDOWN = "/#AGE_RESTRICT#/";

    public function __construct() {
        $this->brand_page_settings = $this->getModel('BrandPageSettings');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @return mixed
     */
    public function getAllPublicPageSettings() {

        $filter = array(
            'conditions' => array(
                'public_flg' => self::STATUS_PUBLIC,
            ),
        );
        $page_settings = $this->brand_page_settings->find($filter);

        return $page_settings;
    }

    /**
     * ブランドIDよりページ設定情報を取得する
     * @param $brandId
     */
    public function getPageSettingsByBrandId($brandId) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brandId,
            ),
        );
        $page_settings = $this->brand_page_settings->findOne($filter);

        return $page_settings;
    }

    /**
     * 必須入力個人情報があるかどうか
     * @param $brandId
     * @return bool
     */
    public function isRequiredPrivacyByBrandId($brandId, $page_settings = null) {
        if ($page_settings === null) {
            $page_settings = $this->getPageSettingsByBrandId($brandId);
        }

        return $page_settings->privacy_required_name ||
        $page_settings->privacy_required_sex ||
        $page_settings->privacy_required_birthday ||
        $page_settings->privacy_required_address ||
        $page_settings->privacy_required_tel ||
        $page_settings->privacy_required_restricted;
    }

    /**
     * ページの公開設定を行う
     * @param $brandId
     * @param $public_settings
     */
    public function setPublicPageSettings($brandId, $public_settings) {

        $page_settings = $this->getPageSettingsByBrandId($brandId);
        if(!$page_settings) {
            $page_settings = $this->createEmptyPageSettings();
            $page_settings->brand_id = $brandId;
            $page_settings->tag_text = '';
            $page_settings->agreement = '';
        }
        $page_settings->public_flg = $public_settings;

        $this->brand_page_settings->save($page_settings);
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    /**
     * @param $brand_id
     * @param $meta_data
     */
    public function setPageMetaSetting($brand_id, $meta_data) {
        $page_setting = $this->getPageSettingsByBrandId($brand_id);

        if (!$page_setting) {
            $page_setting = $this->createEmptyPageSettings();
            $page_setting->brand_id = $brand_id;
        }

        $page_setting->meta_title = $meta_data['meta_title'];
        $page_setting->meta_description = $meta_data['meta_description'];
        $page_setting->meta_keyword = $meta_data['meta_keyword'];
        $page_setting->og_image_url = $meta_data['og_image_url'];

        $this->brand_page_settings->save($page_setting);
        BrandInfoContainer::getInstance()->clear($brand_id);
    }

    public function createEmptyPageSettings() {
        $page_settings = $this->brand_page_settings->createEmptyObject();
        $page_settings->public_flg = self::STATUS_NON_PUBLIC;
        return $page_settings;
    }

    /**
     * ページのタグ管理設定を行う
     * @param $brandId
     * @param $tag_text
     */
    public function setTagPageSettings($brandId, $tag_text) {

        $page_settings = $this->getPageSettingsByBrandId($brandId);
        if(!$page_settings) {
            $page_settings = $this->createEmptyPageSettings();
            $page_settings->brand_id = $brandId;
        }
        $page_settings->tag_text = $tag_text;

        $this->brand_page_settings->save($page_settings);
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    /**
     * ページのヘッダータグ管理設定を行う
     * @param $brandId
     * @param $header_tag_text
     */
    public function setHeaderTagPageSettings($brandId, $header_tag_text) {

        $page_settings = $this->getPageSettingsByBrandId($brandId);
        if(!$page_settings) {
            $page_settings = $this->createEmptyPageSettings();
            $page_settings->brand_id = $brandId;
        }
        $page_settings->header_tag_text = $header_tag_text;

        $this->brand_page_settings->save($page_settings);
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    public function setRequiredPrivacySettings($brandId, $requiredPrivacy, $get_state) {

        $page_settings = $this->getPageSettingsByBrandId($brandId);
        if(!$page_settings) {
            $page_settings = $this->createEmptyPageSettings();
            $page_settings->brand_id = $brandId;
        }

        $page_settings->privacy_required_name       = (int)in_array('privacy_required_name', $requiredPrivacy) ?: $page_settings->privacy_required_name;
        $page_settings->privacy_required_sex        = (int)in_array('privacy_required_sex', $requiredPrivacy);
        $page_settings->privacy_required_birthday   = (int)in_array('privacy_required_birthday', $requiredPrivacy);

        if (in_array('privacy_required_address', $requiredPrivacy)) {
            $page_settings->privacy_required_address = $get_state;
        } else {
            $page_settings->privacy_required_address = $page_settings->privacy_required_address == BrandPageSetting::GET_ALL_ADDRESS ? BrandPageSetting::GET_ALL_ADDRESS : BrandPageSetting::NOT_GET_ADDRESS;
        }

        $page_settings->privacy_required_tel        = (int)in_array('privacy_required_tel', $requiredPrivacy) ?: $page_settings->privacy_required_tel;
        $page_settings->privacy_required_restricted = (int)in_array('privacy_required_restricted', $requiredPrivacy);

        $this->brand_page_settings->save($page_settings);
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    public function setAgreementSettings($brandId, $agreement, $show_agreement_checkbox) {

        $page_settings = $this->getPageSettingsByBrandId($brandId);
        if(!$page_settings) {
            $page_settings = $this->createEmptyPageSettings();
            $page_settings->brand_id = $brandId;
        }
        $page_settings->agreement = $agreement;
        $page_settings->show_agreement_checkbox = $show_agreement_checkbox;

        $this->brand_page_settings->save($page_settings);
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    public function setRestrictedAgeSettings($brandId, $restrictedAge) {

        $page_settings = $this->getPageSettingsByBrandId($brandId);
        if(!$page_settings) {
            $page_settings = $this->createEmptyPageSettings();
            $page_settings->brand_id = $brandId;
        }
        $page_settings->restricted_age = $restrictedAge;

        $this->brand_page_settings->save($page_settings);
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    public function setAgeAuthenticationFlgSettings($brandId, $ageAuthenticationFlg){

        $page_settings = $this->getPageSettingsByBrandId($brandId);
        if(!$page_settings) {
            $page_settings = $this->createEmptyPageSettings();
            $page_settings->brand_id = $brandId;
        }
        $page_settings->age_authentication_flg = $ageAuthenticationFlg;

        $this->brand_page_settings->save($page_settings);
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    public function setCustomMailSettings($brandId, $sendSignupMailFlg){

        $page_settings = $this->getPageSettingsByBrandId($brandId);

        if(!$page_settings) {
            $page_settings = $this->createEmptyPageSettings();
            $page_settings->brand_id = $brandId;
        }

        $page_settings->send_signup_mail_flg = $sendSignupMailFlg;

        $this->brand_page_settings->save($page_settings);
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    public function setNotAuthenticationUrlSettings($brandId, $notAuthenticationUrl){

        $page_settings = $this->getPageSettingsByBrandId($brandId);
        if(!$page_settings) {
            $page_settings = $this->createEmptyPageSettings();
            $page_settings->brand_id = $brandId;
        }
        $page_settings->not_authentication_url = $notAuthenticationUrl;

        $this->brand_page_settings->save($page_settings);
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    public function setAuthenticationPageContentSettings($brandId, $authenticationPageContent){

        $page_settings = $this->getPageSettingsByBrandId($brandId);
        if(!$page_settings) {
            $page_settings = $this->createEmptyPageSettings();
            $page_settings->brand_id = $brandId;
        }
        $page_settings->authentication_page_content = $authenticationPageContent;

        $this->brand_page_settings->save($page_settings);
        BrandInfoContainer::getInstance()->clear($brandId);
    }

    public function isPublic($brandId, $page_settings = null) {
        if ($page_settings === null) {
            $page_settings = $this->getPageSettingsByBrandId($brandId);
        }
        return $page_settings->public_flg == self::STATUS_PUBLIC;
    }

    public function isPublicByEntity($page_setting) {
        return $page_setting->public_flg == self::STATUS_PUBLIC;
    }

    public function updateBrandPageSetting($brand_page_setting) {
        $this->brand_page_settings->begin();

        try {
            $this->brand_page_settings->save($brand_page_setting);
            BrandInfoContainer::getInstance()->clear($brand_page_setting->brand_id);
        } catch (Exception $e) {
            $this->logger->error("BrandPageSettingService#updateBrandPageSetting Error");
            $this->logger->error($e);
            $this->brand_page_settings->rollback();
        }
        $this->brand_page_settings->commit();
    }

    public function genRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strlen($characters);
        $returnStr = '';

        for ($i = 0; $i < $length; $i++) {
            $returnStr .= $characters[rand(0, $charLength - 1)];
        }

        return $returnStr;
    }

    /**
     * top_page_url を更新
     * @param $brand_page_setting
     * @param $top_page_url
     */
    public function updateTopPageUrl($brand_page_setting, $top_page_url) {
        $brand_page_setting->top_page_url = $top_page_url;
        $this->brand_page_settings->save($brand_page_setting);
        BrandInfoContainer::getInstance()->clear($brand_page_setting->brand_id);
    }

    public function evalAgeRestrictMarkdown($content,$ageRestrict){
        return preg_replace(self::AGE_RESTRICT_MARKDOWN,$ageRestrict,$content);
    }

}