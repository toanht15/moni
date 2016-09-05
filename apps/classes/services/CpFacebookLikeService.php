<?php

AAFW::import('jp.aainc.classes.brandco.cp.CpFacebookLikeActionManager');
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpFacebookLikeService extends aafwServiceBase {

    private $config;
    private $logger;
    private $twitter;

    public function __construct() {
        $this->config = aafwApplicationConfig::getInstance();
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * Likeフォームの表示ステータスパラメータ作成
     *
     * @param $brand_social_account
     * @param null $target_user
     * @return array
     */
    public function getLikeFormStatusParams($brand_social_account, $target_user=null) {
        // 0: Facebook未連携
        $unread = CpFacebookLikeLog::LIKE_ACTION_UNREAD;
        // 1: Likeしていない
        $exec = CpFacebookLikeLog::LIKE_ACTION_EXEC;

        // パラメータの初期化
        $params = array();
        foreach (array($exec, $unread) as $status) {
            $params["like_${status}"] = false;
        }
        // ユーザの状況によって、パラメータを更新
        if (is_null($target_user)) {
            // Facebook連携なし
            $params["like_${unread}"] = true;
        } else {
            $params["like_${exec}"] = true;
        } 

        return $params;
    }

    /**
     * いいね対象のFacebookページ情報を取得
     *
     * @param $concrete_action_id 詳細アクションID
     * @return mixed
     */
    public function getLikeTargeAccount($concrete_action_id) {
        /** @var CpFacebookLikeAccountService $fb_account_service */
        $fb_account_service = $this->getService('CpFacebookLikeAccountService');
        $cp_fb_like_account = $fb_account_service->getLikeTargetSocialAccount(
            $concrete_action_id
        );

        return $cp_fb_like_account->getBrandSocialAccount();
    }
}
