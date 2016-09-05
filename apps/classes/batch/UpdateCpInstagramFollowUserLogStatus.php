<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.vendor.instagram.Instagram');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');
AAFW::import('jp.aainc.classes.services.UserService');
AAFW::import('jp.aainc.classes.services.CpUserService');
AAFW::import('jp.aainc.classes.services.CpInstagramFollowUserLogService');
AAFW::import('jp.aainc.classes.stores.SocialApps');
AAFW::import('jp.aainc.classes.entities.SocialAccount');

class UpdateCpInstagramFollowUserLogStatus {
    public $logger;
    public $service_factory;

    /** @var CpUserService $cp_user_service */
    public $cp_user_service;
    /** @var SocialAccountService $social_account_service */
    public $social_account_service;
    /** @var BrandSocialAccountService $brand_social_account_service */
    public $brand_social_account_service;
    /** @var CpInstagramFollowUserLogService $ig_follow_user_log_service */
    public $ig_follow_user_log_service;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->cp_user_service = $this->service_factory->create('CpUserService');
        $this->social_account_service = $this->service_factory->create('SocialAccountService');
        $this->brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');
        $this->ig_follow_user_log_service = $this->service_factory->create('CpInstagramFollowUserLogService');
    }

    public function doProcess() {
        $instagram = new Instagram();
        $instagram_user_logs = $this->ig_follow_user_log_service->getCpInstargamFollowUserLogsForUpdate();

        foreach ($instagram_user_logs as $instagram_user_log) {
            try {
                $user = $this->cp_user_service->getUserByCpUserId($instagram_user_log->cp_user_id);
                $social_account = $this->social_account_service->getSocialAccountByUserIdAndSocialAppId($user->id, SocialAccount::SOCIAL_MEDIA_INSTAGRAM);

                $brand_social_account = $this->brand_social_account_service->getBrandSocialAccountByAccountId($instagram_user_log->social_media_account_id, SocialApps::PROVIDER_INSTAGRAM);

                $ret = $instagram->getRelationship($social_account->social_media_account_id, $brand_social_account->token);
                $instagram_user_log->follow_status = $ret->data->incoming_status == Instagram::INCOMING_STATUS_FOLLOWS ? CpInstagramFollowUserLog::FOLLOWED : CpInstagramFollowUserLog::NOT_FOLLOWED;
                $instagram_user_log->check_flg = CpInstagramFollowUserLog::CHECKED;
                $this->ig_follow_user_log_service->saveLog($instagram_user_log);
            } catch (Exception $e) {
                $this->logger->error("UpdateCpInstagramFollowUserLogStatus#doProcess() Exception cp_instagram_follow_user_log_id = " . $instagram_user_log->id);
                $this->logger->error($e);
                continue;
            }
        }
    }

}
