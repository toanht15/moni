<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.RetweetMessageService');
AAFW::import('jp.aainc.classes.services.CpRetweetMessageService');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class UserMessageThreadActionRetweet extends aafwWidgetBase{

    public function doService( $params = array() ){

        $service_factory = new aafwServiceFactory();

        /** @var RetweetMessageService $retweet_message_service */
        $retweet_message_service = $service_factory->create('RetweetMessageService');

        /** @var CpRetweetActionService $cp_retweet_action_service */
        $cp_retweet_action_service = $service_factory->create('CpRetweetActionService');

        if ($params['message_info']['concrete_action']->tweet_has_photo) {
            $params['tweet_photos'] = $cp_retweet_action_service->getRetweetPhotos($params['message_info']['concrete_action']->id);
        }

        $params['retweet_message'] = $retweet_message_service->getRetweetMessageByCpUserId($params['cp_user']->id, $params['message_info']['concrete_action']->id);
        if (!$params['retweet_message']) {
            $params['retweet_message'] = $retweet_message_service->createDefaultRetweetMessage($params['cp_user']->id, $params['message_info']['concrete_action']->id);
        }

        /**
         * retweeted = 1 かつ join_status = 0 かつ TW未連携 のユーザ
         * （つまり、リツイートボタンは押したがTW連携で離脱した人）
         * に対してはretweeted = 0 に戻さないと、延々と自動でTW認証画面に飛ばされてしまう
         */
        if ($params['retweet_message']->retweeted == CpRetweetAction::POST_RETWEET
            && $params['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN
            && !$this->hasTwitterAccountWithAccessToken($params['pageStatus'])) {
            $params['retweet_message']->retweeted = CpRetweetAction::NOT_POST_RETWEET;
            $retweet_message_service->saveRetweetMessageData($params['retweet_message']);
        }

        return $params;
    }

    public function hasTwitterAccountWithAccessToken($pageStatus) {
        $user_sns_account_manager   = new UserSnsAccountManager($pageStatus['userInfo'], null, $pageStatus['brand']->app_id);
        $user_sns_account_id        = $this->getTwitterAccountId($pageStatus['userInfo']);
        if ($user_sns_account_id == -1) return false;

        $sns_account_info = $user_sns_account_manager->getSnsAccountInfo($user_sns_account_id, 'Twitter');
        if (!$sns_account_info['social_media_access_token']) return false;

        return true;
    }

    public function getTwitterAccountId($user_info) {
        foreach ($user_info->socialAccounts as $social_account) {
            if ($social_account->socialMediaType == 'Twitter') {
                return $social_account->socialMediaAccountID;
            }
        }
        return -1;
    }
}
