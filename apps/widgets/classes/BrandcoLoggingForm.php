<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.BrandInfoContainer');
AAFW::import('jp.aainc.classes.services.SocialAccountService');

class BrandcoLoggingForm extends aafwWidgetBase {

    private $default_available_sns_accounts = array(
        SocialAccount::SOCIAL_MEDIA_FACEBOOK,
        SocialAccount::SOCIAL_MEDIA_TWITTER,
        SocialAccount::SOCIAL_MEDIA_LINE,
        SocialAccount::SOCIAL_MEDIA_INSTAGRAM,
        SocialAccount::SOCIAL_MEDIA_GOOGLE,
        SocialAccount::SOCIAL_MEDIA_YAHOO,
        SocialAccount::SOCIAL_MEDIA_LINKEDIN
    );

    // TODO ▼▼ 特別対応 MSBC
    private $msbc_available_sns_accounts = array(
        SocialAccount::SOCIAL_MEDIA_FACEBOOK,
        SocialAccount::SOCIAL_MEDIA_TWITTER,
        SocialAccount::SOCIAL_MEDIA_LINKEDIN,
        SocialAccount::SOCIAL_MEDIA_INSTAGRAM,
        SocialAccount::SOCIAL_MEDIA_GOOGLE,
        SocialAccount::SOCIAL_MEDIA_YAHOO,
        SocialAccount::SOCIAL_MEDIA_LINE
    );
    // TODO ▲▲ 特別対応 MSBC

    public function doService( $params = array() ) {
        $redirectUrl = $params['pageInfo']['loginRedirectUrl'] ? $params['pageInfo']['loginRedirectUrl'] : Util::getBaseUrl();

        // BrandGlobalSetting: LoginPageContentの取得
        $login_page_content = $this->getBrandGlobalSettingByName(BrandGlobalSettingService::LOGIN_PAGE_CONTENTS);

        if ($login_page_content) {
            $settingParams = array(
                '<#REDIRECT_URL>' => urlencode($redirectUrl)
            );
            $content = Util::applyParameter($login_page_content->content, $settingParams);
            $params['pageContents'] = $content;
        }

        $params['available_sns_accounts'] = $this->getAvailableSnsAccounts($params['loggingFormInfo']['available_sns_accounts'], $params['pageStatus']['brand']->id);

        $params['cp_id'] = $params['pageInfo']['cp_id'] ? $params['pageInfo']['cp_id'] : null;
        $params['ActionForm'] = array('mail_address' => $params['ActionForm']['mail_address']);
        $params['loginRedirectUrl'] = $redirectUrl;

        $params['template_file'] = $params['loggingFormInfo']['template_file'];
        $params['page_type'] = $params['loggingFormInfo']['page_type'];

        $params['sns_limited'] = $params['loggingFormInfo']['sns_limited'];
        $params['preset_mail_address'] = $params['loggingFormInfo']['preset_mail_address'] ?: '';

        return $params;
    }

    /**
     * @param $name
     * @return null
     */
    public function getBrandGlobalSettingByName($name) {
        /** @var  BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        return $brand_global_setting_service->getBrandGlobalSettingByName(
            BrandInfoContainer::getInstance()->getBrandGlobalSettings(), $name);
    }

    /**
     * @param $available_sns_accounts
     * @return array
     */
    public function getAvailableSnsAccounts($available_sns_accounts, $brand_id) {
        if (is_null($available_sns_accounts)) {
            $available_sns_accounts = $this->default_available_sns_accounts;
        }

        /** @var BrandLoginSettingService $brand_login_setting_service */
        $brand_login_setting_service = $this->getService('BrandLoginSettingService', array($brand_id));
        $brand_login_sns_list = $brand_login_setting_service->getBrandLoginSnsList();

        if (!empty($brand_login_sns_list)) {
            foreach ($available_sns_accounts as $key => $available_sns_account) {
                if (!in_array($available_sns_account, $brand_login_sns_list)) {
                    unset($available_sns_accounts[$key]);
                }
            }
        }

        // BrandGlobalSetting: OriginalSnsAccountの取得
        $original_sns_account = $this->getBrandGlobalSettingByName(BrandGlobalSettingService::ORIGINAL_SNS_ACCOUNTS);
        if ($original_sns_account) {
            $original_sns_account_array = explode(',', $original_sns_account->content);
            if (!in_array(SocialAccountService::SOCIAL_MEDIA_LINKEDIN, $original_sns_account_array)) {
                if (($key = array_search(SocialAccount::SOCIAL_MEDIA_LINKEDIN, $available_sns_accounts)) !== false) {
                    unset($available_sns_accounts[$key]);
                }
            }
        } else {
            if (($key = array_search(SocialAccount::SOCIAL_MEDIA_LINKEDIN, $available_sns_accounts)) !== false) {
                unset($available_sns_accounts[$key]);
            }
        }

        // TODO ▼▼ 特別対応 SUGAO
        $sugao_custom_login = $this->getBrandGlobalSettingByName(BrandGlobalSettingService::SUGAO_CUSTOM_LOGIN_AND_TWITTER_ACTION);
        if ($sugao_custom_login) {
            $unavailable_sns_accounts = array(
                SocialAccount::SOCIAL_MEDIA_INSTAGRAM, SocialAccount::SOCIAL_MEDIA_GOOGLE, SocialAccount::SOCIAL_MEDIA_YAHOO
            );
            foreach ($unavailable_sns_accounts as $unavailable_sns_account) {
                if (($key = array_search($unavailable_sns_account, $available_sns_accounts)) !== false) {
                    unset($available_sns_accounts[$key]);
                }
            }
        }
        // TODO ▲▲ 特別対応 SUGAO

        $available_sns_accounts = $this->changeOrderOfSnsAccounts($available_sns_accounts);

        // Limit email login
        if (empty($brand_login_sns_list) || in_array(SocialAccountService::SOCIAL_MEDIA_PLATFORM, $brand_login_sns_list)) {
            $available_sns_accounts[] = SocialAccountService::SOCIAL_MEDIA_PLATFORM;
        }

        return $available_sns_accounts;
    }

    /**
     * @param $available_sns_accounts
     * @return array
     */
    public function changeOrderOfSnsAccounts($available_sns_accounts) {
        $order_of_sns_accounts = $this->default_available_sns_accounts;

        // TODO ▼▼ 特別対応 MSBC
        $msbc_custom_login_page = $this->getBrandGlobalSettingByName(BrandGlobalSettingService::MSBC_CUSTOM_LOGIN_PAGE);
        if (!Util::isNullOrEmpty($msbc_custom_login_page)) {
            $order_of_sns_accounts = $this->msbc_available_sns_accounts;
        }
        // TODO ▲▲ 特別対応 MSBC

        $sns_accounts = array();
        foreach ($order_of_sns_accounts as $sns_account) {
            if (in_array($sns_account, $available_sns_accounts)) {
                $sns_accounts[] = $sns_account;
            }
        }

        return $sns_accounts;
    }

    public function fetchSNSQuery($sns_type, $data) {
        $query = array(
            'platform' => $sns_type,
            'redirect_url' => urlencode($data['loginRedirectUrl'])
        );

        $default_query = $this->fetchQuery($data);
        return array_merge($query, $default_query);
    }

    public function fetchQuery($data) {
        return $data['cp_id'] ? array('cp_id' => $data['cp_id']) : array();
    }
}