<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.PhotoUserService');
AAFW::import('jp.aainc.classes.services.PhotoStreamService');
AAFW::import('jp.aainc.classes.services.SocialAccountService');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class UserMessageThreadActionPhoto extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var PhotoUserService $photo_user_service */
        $photo_user_service = $this->getService('PhotoUserService');
        /** @var PhotoStreamService $photo_stream_service */
        $photo_stream_service = $this->getService('PhotoStreamService');
        /** @var PhotoUserShareService $photo_user_shares */
        $photo_user_shares = $this->getService('PhotoUserShareService');

        $logger = aafwLog4phpLogger::getDefaultLogger();

        // 写真投稿情報取得
        $params['photo_user'] = $photo_user_service->getPhotoUserByIds($params['message_info']['cp_action']->id, $params['cp_user']->id);
        $params['photo_entry'] = $photo_stream_service->getPhotoEntryByPhotoUserId($params['photo_user']->id);

        // FBシェア状況取得
        if ($photo_user_shares->getPhotoUserSharesByPhotoUserIdAndSnsType($params['photo_user']->id, SocialAccount::SOCIAL_MEDIA_FACEBOOK)) {
            $params['photo_user_fb_share'] = $photo_user_shares->getPhotoUserSharesByPhotoUserIdAndSnsType($params['photo_user']->id, SocialAccount::SOCIAL_MEDIA_FACEBOOK);
        }

        // TWシェア状況取得
        if ($photo_user_shares->getPhotoUserSharesByPhotoUserIdAndSnsType($params['photo_user']->id, SocialAccount::SOCIAL_MEDIA_TWITTER)) {
            $params['photo_user_tw_share'] = $photo_user_shares->getPhotoUserSharesByPhotoUserIdAndSnsType($params['photo_user']->id, SocialAccount::SOCIAL_MEDIA_TWITTER);
        }

        // シェアテキスト取得
        $params['share_text'] = $this->getShareText($params['photo_user_fb_share']->share_text, $params['photo_user_tw_share']->share_text);

        try {
            // photo_userまたはshare_textがなかったらキャッシュチェック
            if (!$params['share_text'] || !$params['photo_user']) {
                $cache_manager = new CacheManager();
                $data = $cache_manager->getCache('ph' . 'a' . $params['message_info']['cp_action']->id . 'u' . $params['cp_user']->id);

                if (!$params['photo_user'] && $data) {
                    $params['photo_user']->cache = 1;
                    $params['photo_user']->photo_title = $data['photo_title'] ? $data['photo_title'] : '';
                    $params['photo_user']->photo_comment = $data['photo_comment'] ? $data['photo_comment'] : '';
                    if ($data['photo_url']) {
                        $params['photo_user']->photo_url = $data['photo_url'];
                    }
                }

                if (!$params['share_text'] && $data) {
                    $params['share_text'] = $data['share_text'] ? $data['share_text'] : '';
                }

                // キャシュ削除
                if ($data) {
                    $cache_manager->deleteCache('ph' . 'a' . $params['message_info']['cp_action']->id . 'u' . $params['cp_user']->id);
                }
            }

            if (count($params['pageStatus']['userInfo']->socialAccounts)) {
                foreach ($params['pageStatus']['userInfo']->socialAccounts as $social_account) {

                    if ($social_account->socialMediaType === SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_FACEBOOK]) {
                        $params['fb_connect'] = 1;

                        // publish_actions permissionチェック
                        if ($this->checkPublishActions($params['pageStatus'], $social_account->socialMediaAccountID)) {
                            $params['fb_has_permission'] = 1;
                        }else {
                            // permissionがない場合は出さない
                            $params['fb_has_permission'] = 0;
                        }
                    }

                    if ($social_account->socialMediaType === SocialAccount::$socialMediaTypeName[SocialAccount::SOCIAL_MEDIA_TWITTER]) {
                        $params['tw_connect'] = 1;
                    }
                }
            }

        }catch(Exception $e) {
            $logger->error('UserMessageThreadActionPhoto#doService cache error.');
            $logger->error($e);
        }

        return $params;
    }

    /**
     * シェアテキスト取得
     * FBかTWどちらかでシェアしていたら取得する
     * @param $fb_share_text
     * @param $tw_share_text
     * @return string
     */
    private function getShareText($fb_share_text, $tw_share_text) {
        if ($fb_share_text) {
            return $fb_share_text;
        }elseif ($tw_share_text) {
            return $tw_share_text;
        }else{
            return false;
        }
    }

    /**
     * @param $pageStatus
     * @param $social_media_account_id
     * @return bool
     */
    private function checkPublishActions($pageStatus, $social_media_account_id){
        $user_sns_account_manager = new UserSnsAccountManager($pageStatus['userInfo'], null, $pageStatus['brand']->app_id);
        // $social_media_account_idは中で特に処理に使われてない
        $sns_account_info = $user_sns_account_manager->getSnsAccountInfo($social_media_account_id, 'Facebook');

        if (!$sns_account_info['social_media_access_token']){
            return false;
        }

        $facebook_api_client = new FacebookApiClient(FacebookApiClient::BRANDCO_MODE_USER);
        $facebook_api_client->setToken($sns_account_info['social_media_access_token']);
        $permission_array = $facebook_api_client->getPermission();

        foreach ($permission_array as $permission){
            if($permission->permission === 'publish_actions' && $permission->status === 'granted'){
                return true;
            }
        }
        return false;
    }
}
