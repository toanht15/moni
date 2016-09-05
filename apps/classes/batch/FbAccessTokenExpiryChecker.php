<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import( 'jp.aainc.classes.entities.BrandSocialAccount' );

/**
 * Class FbAccessTokenExpiryCheckerTask
 * Facebook連携のアクセストークンチェックしメールを送る
 */
class FbAccessTokenExpiryChecker {

    protected $logger;
    /** @var MailManager $mail */
    protected $mail;
    protected $settings;
    protected $expiry_date_span = BrandSocialAccount::FB_EXPIRED_DATE;
    protected $service_factory;
    protected $account_service;
    protected $user_service;

    public function __construct() {
        $this->settings = aafwApplicationConfig::getInstance();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->mail = new MailManager();
        $this->service_factory = new aafwServiceFactory();
    }

    public function doProcess() {
        try{
            $this->user_service = $this->service_factory->create('UserService');
            $this->account_service = $this->service_factory->create('BrandSocialAccountService');

            $admin_users = $this->user_service->getAdminUserAll();

            // アラート内容を振り分けを送る企業を振り分け、メール送信
            foreach ($admin_users as $admin_user) {
                // admin userが連携してるFBアカウント一覧取得
                //$brand_social_accounts = $this->account_service->getBrandSocialAccountsByUserId($admin_user->id, SocialApps::PROVIDER_FACEBOOK);
                $brand_social_accounts = $this->account_service->getBrandSocialAccountsByUserId($admin_user->user_id, SocialApps::PROVIDER_FACEBOOK);
                if(count($brand_social_accounts) == 0) continue;

                // トークン期限切れチェック、メール送信
                $this->dispatchNotice($brand_social_accounts);
            }
        }catch (Exception $e){
            $this->logger->error('FbAccessTokenExpiryChecker batch error.' . $e);
        }
    }

    /**
     * @param $brand_social_accounts
     */
    private function dispatchNotice($brand_social_accounts) {
        // expired
        $expiry_info_list = $this->account_service->getFbAccessTokenExpiryInfo($brand_social_accounts,$this->expiry_date_span);
        if (count($expiry_info_list)) {
            $this->sendAlertMail($expiry_info_list);
        }

        // two days before
        $brand_social_accounts_2 = $this->account_service->getFbAccessTokenExpiryInfo($brand_social_accounts,$this->expiry_date_span - 2);
        if (count($brand_social_accounts_2)) {
            $this->sendAlertMail($brand_social_accounts_2, 2);
        }

        // seven days before
        $brand_social_accounts_7 = $this->account_service->getFbAccessTokenExpiryInfo($brand_social_accounts,$this->expiry_date_span - 7);
        if (count($brand_social_accounts_7)) {
            $this->sendAlertMail($brand_social_accounts_7, 7);
        }
        // expireが過去日付になっているデータを取得
        $brand_social_accounts_1 = $this->account_service->getFbAccessTokenExpiryInfo($brand_social_accounts, -1);
        if (count($brand_social_accounts_1)) {
            $this->sendAlertMail($brand_social_accounts_1, -1);
        }

        // expireが過去日付になっているデータを取得
        $brand_social_accounts = $this->account_service->getFbAccessTokenExpiryInfo($brand_social_accounts, -1);
        if (count($brand_social_accounts)) {
            $this->sendAlertMail($brand_social_accounts, -1);
        }
    }

    private function sendAlertMail($expiry_info_list, $date_before = null) {
        if (!count($expiry_info_list)) return;

        foreach ($expiry_info_list as $expiry_info) {
            $items[] = array(
                'FACEBOOK_PAGE' => $expiry_info->facebook_page ,
                'ACCESS_TOKEN_EXPIRY_DATE' => $expiry_info->expired_date ,
                'URL' => $expiry_info->update_url
            );
        }

        //値セット
        $mailParams = array(
            'USER_NAME' => $expiry_info_list[0]->user_name,
            'FACEBOOK_PAGE_INFO' => $items
        );

        $this->mail->loadMailContent('fb_access_token_alert');
        //$this->mail->sendNow('sato.masaki@aainc.co.jp', $mailParams);
        $this->mail->sendNow('bc-dev@aainc.co.jp', $mailParams);
    }
}
