<?php

AAFW::import('jp.aainc.classes.brandco.cp.CpTwitterFollowActionManager');
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.vendor.twitter.Twitter');

class CpTwitterFollowService extends aafwServiceBase {

    private $config;
    private $logger;
    private $twitter;

    /** @var CpTwitterFollowAccountService $cp_tw_follow_account_service */
    protected $cp_tw_follow_account_service;

    public function __construct($twOAuthToken, $twOAuthTokenSecret) {
        $this->config = aafwApplicationConfig::getInstance();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->twitter = new Twitter(
            $this->config->query('@twitter.User.ConsumerKey'),
            $this->config->query('@twitter.User.ConsumerSecret'),
            $twOAuthToken,
            $twOAuthTokenSecret
        );
    }

    /**
     * 対象アカウントのフォロー状態判定
     *
     * @param $source_account_id
     * @param $target_account_id
     * @return bool
     */
    public function isFollowedAccount($source_account_id, $target_account_id) {
        $res = $this->twitter->getFriendshipsShow(
            $source_account_id,
            $target_account_id
        );

        return $res->relationship->source->following;
    }

    /**
     * Followフォームの表示ステータスパラメータ作成
     *
     * @param $brand_social_account
     * @param null $target_user
     * @return array
     */
    public function getFollowFormStatusParams($brand_social_account, $target_user=null) {
        $exec   = CpTwitterFollowActionManager::FOLLOW_ACTION_EXEC;         // exec: 未フォロー
        $finish = CpTwitterFollowActionManager::FOLLOW_ACTION_ALREADY;      // finish: フォロー済み

        // パラメータの初期化
        $params = array();
        foreach (array($exec, $finish) as $status) {
            $params["follow_${status}"] = false;
        }

        // 対象ブランドのTwitterアカウントのフォローチェック
        if (!is_null($target_user) && $this->isFollowedAccount($target_user->socialMediaAccountID, $brand_social_account->social_media_account_id) === true) {
            // フォーロー済み
            $params["follow_${finish}"] = true;
        } else {
            // SNSアカウントなしor未フォロー
            $params["follow_${exec}"] = true;
        }

        return $params;
    }

    /**
     * フォロー対象のTwitterアカウントを取得
     *
     * @param $concrete_action_id 詳細アクションID
     * @return mixed
     */
    public function getTwitterFollowTargeAccount($concrete_action_id) {
        /** @var CpTwitterFollowAccountService $tw_account_service */
        $tw_account_service = $this->getService('CpTwitterFollowAccountService');
        $cp_tw_follow_account = $tw_account_service->getFollowTargetSocialAccount(
            $concrete_action_id
        );

        return $cp_tw_follow_account->getBrandSocialAccount();
    }

    /**
     * フォローする
     *
     * @param $screen_name
     * @return bool
     */
    public function postFollow($screen_name) {
         return $this->twitter->postFollow($screen_name);
    }

    //AccessTokenの期限切れるか判定する
    public function checkCredentials(){
        return $this->twitter->checkCredentials();
    }
}
