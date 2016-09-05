<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.TweetMessageService');
AAFW::import('jp.aainc.classes.services.CpTweetMessageService');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class UserMessageThreadActionTweet extends aafwWidgetBase{
    private $is_skip_action;

    const SOCIAL_TYPE_TWITTER = 'Twitter';
    public function doService( $params = array() ){

        $service_factory = new aafwServiceFactory();

        /** @var TweetMessageService $tweet_message_service */
        $tweet_message_service = $service_factory->create('TweetMessageService');

        $params['is_skip_action'] = $this->isSkipAction($params['pageStatus']);
        if (!$tweet_message_service->getTweetMessageByCpUserId($params['cp_user']->id, $params['message_info']['concrete_action']->id)) {
            $params['auto_skip_action'] = $this->isSkipAction($params['pageStatus']);

            $tweet_message_service->createDefaultTweetMessage($params['cp_user']->id, $params['message_info']['concrete_action']->id);
        }

        $params['tweet_message'] = $tweet_message_service->getTweetMessageByCpUserId($params['cp_user']->id, $params['message_info']['concrete_action']->id);
        if ($params['tweet_message']->has_photo) {
            $params['tweet_photos'] = $tweet_message_service->getTweetPhotos($params['tweet_message']->id);
        }

        // ツイーターをログインした後にツイートを投稿する
        if ($params['tweet_message']->tweet_text != '' && $params['tweet_message']->tweet_content_url == '' && $params['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN) {
            $sns_account_info = $this->getSNSAccountInfo($params['pageStatus']['userInfo'], $params['pageStatus']['brand']->app_id);
            if ($sns_account_info) {
                $cp_tweet_message_service   = $service_factory->create('CpTweetMessageService', array(
                    $sns_account_info['social_media_access_token'],
                    $sns_account_info['social_media_access_refresh_token']
                ));

                //ツイート内容を取りだす
                $tweet_status   = $params['tweet_message']->tweet_text . ($params['message_info']['concrete_action']->tweet_fixed_text != '' ? "\r\n" . $params['message_info']['concrete_action']->tweet_fixed_text : '');
                $image_urls     = array();
                foreach ($params['tweet_photos'] as $element) {
                    $image_urls[] = $element->image_url;
                }

                //デモモードで外部SNSにAPIを投げないようにチェックする
                $cp = CpInfoContainer::getInstance()->getCpById($params['cp_user']->cp_id);
                $is_demo_cp = $cp->status == Cp::STATUS_DEMO;
                $result = 'ここにツイートのURLが表示されます。';
                if(!$is_demo_cp) {
                    $result = $cp_tweet_message_service->postTweet($tweet_status, $image_urls);
                }
                if ($result != 'api_error') {
                    $params['tweet_message'] = $tweet_message_service->updateTweetMessage(array('cp_user_id' => $params['cp_user']->id, 'cp_tweet_action_id' => $params['message_info']['concrete_action']->id, 'tweet_content_url' => $result));
                }
            }
        }

        // 最後のアクションかどうか判定する
        $params['is_last_action'] = $params['message_info']['cp_action']->isLastCpActionInGroup();

        return $params;
    }

    private function getSNSAccountInfo($user_info, $app_id) {
        $user_sns_account_manager   = new UserSnsAccountManager($user_info, null, $app_id);
        $user_sns_account_id        = $this->getSNSAccountId($user_info, self::SOCIAL_TYPE_TWITTER);
        if ($user_sns_account_id == -1) return null;

        return $user_sns_account_manager->getSnsAccountInfo($user_sns_account_id, self::SOCIAL_TYPE_TWITTER);
    }

    private function getSNSAccountId($user_info, $sns_type) {
        foreach ($user_info->socialAccounts as $social_account) {
            if ($social_account->socialMediaType == $sns_type) {
                return $social_account->socialMediaAccountID;
            }
        }
        return -1;
    }

    /**
     * @param $params
     * @return bool
     */
    private function isSkipAction($params) {
        if (Util::isNullOrEmpty($this->is_skip_action)) {
            $social_accounts = $params['userInfo']->socialAccounts;

            foreach ($social_accounts as $account) {
                if ($account->socialMediaType === CpTwitterFollowActionManager::SOCIAL_TYPE_STRING) {
                    $target_user = $account;
                    break;
                }
            }

            $this->is_skip_action = $params['is_sugao_brand'] && !$target_user;
        }

        return $this->is_skip_action;
    }
}
