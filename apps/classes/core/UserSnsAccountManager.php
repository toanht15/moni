<?php
AAFW::import ( 'jp.aainc.vendor.cores.MoniplaCore' );
AAFW::import ( 'jp.aainc.aafw.base.aafwObject' );

/**
 * CoreのUserが連携しているSNSアカウントを操作するクラス
 * Class UserSnsAccountManager
 */
class UserSnsAccountManager extends aafwObject {

    const SCOPES = 'R_PROFILE';
    private $moniplaCore = null;
    private $userInfo;
    private $user;
    private $app_id;

    public function __construct($userInfo, $moniplaCore = null, $app_id = 1) {
        $this->userInfo = $userInfo;
        $this->app_id = $app_id;
        if ( $moniplaCore == null) $moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        $this->moniplaCore = $moniplaCore;

        $user_service = $this->getService('UserService');
        $this->user = $user_service->getUserByMoniplaUserId($this->userInfo->id);

    }

    /**
     * 指定したSNSアカウントでaccess_tokenが格納された配列を取得する。
     * @param $sns_account_id
     * @return array
     */
    public function getSnsAccountInfo($sns_account_id, $socialMedia){
        $data = array();

        if(!$this->user) {
            return $data;
        }

        /** @var UserApplicationService $user_application_service */
        $user_application_service = $this->getService('UserApplicationService');
        $user_application = $user_application_service->getUserApplicationByUserIdAndAppId($this->user->id, $this->app_id);

        if(!$user_application->access_token || !$user_application->refresh_token || !$user_application->client_id) {
            return $data;
        }

        $brandco_auth_service = $this->getService('BrandcoAuthService');
        $refresh_token_result = $brandco_auth_service->refreshAccessToken($user_application->refresh_token, $user_application->client_id);

        if ($refresh_token_result->result->status === Thrift_APIStatus::SUCCESS) {
            $sns_access_token_result = $brandco_auth_service->getSNSAccessToken($refresh_token_result->accessToken,$socialMedia);
            if ($sns_access_token_result->result->status === Thrift_APIStatus::SUCCESS) {
                $data = array(
                    'social_media_account_id' => $sns_account_id,
                    'social_media_access_token' => $sns_access_token_result->socialAccessToken->snsAccessToken,
                    'social_media_access_refresh_token'=>$sns_access_token_result->socialAccessToken->snsRefreshToken
                );
            }
        }
        return $data;
    }

    /**
     * @param $socialMedia
     * @return array
     */
    public function getSocialAccountInfo($socialMedia){
        $data = array();

        if(!$this->user) {
            return $data;
        }

        /** @var UserApplicationService $user_application_service */
        $user_application_service = $this->getService('UserApplicationService');
        $user_application = $user_application_service->getUserApplicationByUserIdAndAppId($this->user->id, $this->app_id);

        if(!$user_application->access_token || !$user_application->refresh_token || !$user_application->client_id) {
            return $data;
        }

        $brandco_auth_service = $this->getService('BrandcoAuthService');
        $refresh_token_result = $brandco_auth_service->refreshAccessToken($user_application->refresh_token, $user_application->client_id);

        if ($refresh_token_result->result->status === Thrift_APIStatus::SUCCESS) {
            $sns_access_token_result = $brandco_auth_service->getSNSAccessToken($refresh_token_result->accessToken,$socialMedia);
            if ($sns_access_token_result->result->status === Thrift_APIStatus::SUCCESS) {
                $data = array(
                    'social_media_access_token' => $sns_access_token_result->socialAccessToken->snsAccessToken,
                    'social_media_access_refresh_token'=>$sns_access_token_result->socialAccessToken->snsRefreshToken
                );
            }
        }
        return $data;
    }

    public function getSNSAccountId($sns_type) {
        foreach ($this->userInfo->socialAccounts as $social_account) {
            if ($social_account->socialMediaType == $sns_type) {
                return $social_account->socialMediaAccountID;
            }
        }
        return false;
    }

}