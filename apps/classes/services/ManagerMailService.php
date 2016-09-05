<?php
AAFW::import('jp.aainc.classes.services.monipla.MoniplaCpService');
AAFW::import('jp.aainc.aafw.parsers.PHPParser');

/**
 * マネージャに通知メールを送るサービス
 * Class ManagerMailService
 */
class ManagerMailService extends aafwServiceBase {
    const TEMPLATE_ID_TOKEN_EXPIRED = 'token_expired_notification_mail';

    /** @var BrandService */
    private $brand_service;
    /** @var BrandSocialAccountService */
    private $brand_social_account_service;
    /** @var  InquiryMailService */
    private $inquiry_mail_service;

    private $logger;
    private $hipchat_logger;
    private $mail_manager;
    private $bc_dev_mail;

    public function __construct() {
        $this->brand_service = $this->getService("BrandService");
        $this->brand_social_account_service = $this->getService("BrandSocialAccountService");
        $this->inquiry_mail_service = $this->getService("InquiryMailService");

        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->hipchat_logger = aafwLog4phpLogger::getHipChatLogger();

        $this->mail_manager = new MailManager();
        $this->bc_dev_mail = aafwApplicationConfig::getInstance()->query('Mail.ALERT.CcAddress');
    }

    /**
     * トークン切れの通知メールを送信する
     * @param $brand_social_account_id
     */
    public function sendExpiredTokenNotificationMail($brand_social_account_id) {
        $brand_social_account = $this->brand_social_account_service->getBrandSocialAccountById($brand_social_account_id);
        $brand = $this->brand_service->getBrandById($brand_social_account->brand_id);

        //メールを送信する対象を取得する
        $consultants_manager = $this->inquiry_mail_service->getConsultantsManager($brand->id);
        $sales_manager = $this->inquiry_mail_service->getSalesManager($brand->id);

        try {
            $mail_params = $this->buildMailParams($brand, $brand_social_account);

            //通知メールのCCアドレス
            $cc_addresses = $sales_manager->mail_address ? $sales_manager->mail_address .", ".$this->bc_dev_mail : $this->bc_dev_mail;

            //メール送信
            $this->send($consultants_manager->mail_address, self::TEMPLATE_ID_TOKEN_EXPIRED, $mail_params, $cc_addresses);

        } catch (Exception $e) {
            $this->hipchat_logger->error("ERROR: ManagerMailService#senTokenExpiredNotificationMail failed! brand_social_account_id=".$brand_social_account_id);
            $this->logger->error("ERROR: ManagerMailService#sendTokenExpiredNotificationMail failed! brand_social_account_id=".$brand_social_account_id);
            $this->logger->error($e);
        }
    }

    /**
     * メール送信の実行
     * @param $to_address
     * @param $template_name
     * @param array $params
     * @param $cc_address
     * @throws aafwException
     */
    private function send($to_address, $template_name, $params = array(), $cc_address){
        $this->mail_manager->loadMailContent($template_name);

        $this->mail_manager->sendNow($to_address, $params, $cc_address);
    }

    /**
     * brandモデルとbrand_social_accountモデルから
     * メールの置換パラメーターを生成します
     * @param $brand
     * @param $brand_social_account
     * @return array
     */
    private function buildMailParams($brand, $brand_social_account) {
        return array(
            "BRAND_NAME"            => $brand->name,
            "BRAND_URL"             => $brand->getUrl(),
            "BRAND_SOCIAL_ACCOUNT_TYPE" => SocialApps::$social_media_page_og_title[$brand_social_account->social_app_id],
            "BRAND_SOCIAL_ACCOUNT_NAME" => $brand_social_account->name
        );
    }
}