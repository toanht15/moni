<?php

AAFW::import('jp.aainc.classes.brandco.cp.CpTwitterFollowActionManager');
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class api_execute_twitter_follow_action extends ExecuteActionBase {
    protected $ContainerName = 'api_execute_twitter_follow_action';

    const SOCIAL_TYPE_TWITTER = 'Twitter';

    const API_STATUS_DEFAULT = 1;
    const API_STATUS_SKIP    = 2;
    const API_STATUS_FINISH  = 3;

    public function validate() {
        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);

            return false;
        }

        return true;
    }

    //・ユーザが対象モジュールのSNS連携していない場合は、次のモジュールを自動で呼ぶ
    //  （モジュールでは何も表示しない）
    //・ユーザがすでに「いいね」や「フォロー」していた場合は、次のモジュールを自動で呼ぶ
    //  （モジュールでは何も表示しない）
    //・「フォロー」のアクションはDBに記録しておく
    function saveData() {
        if ($this->status == self::API_STATUS_SKIP) {

            // Twitterフォローをスキップ
            $this->saveFollowActionLog(CpTwitterFollowActionManager::FOLLOW_ACTION_SKIP);
            return;
        } elseif ($this->status == self::API_STATUS_FINISH) {

            // Twitterフォロー済み
            $this->saveFollowActionLog(CpTwitterFollowActionManager::FOLLOW_ACTION_ALREADY);
            return;
        }

        // brand_social_account情報を取得
        $brand_social_account = $this->getBrandSocialAccount();

        // ブランドのTwitterアカウントをフォロー
        $tw_follow_service = $this->getTwitterFollowService();
        $res = $tw_follow_service->postFollow($brand_social_account->screen_name);

        if(!$res) {
            $msg = "Twitter Follow: Cannot Auth";
            throw new aafwException($msg);
        }

        // ログ出力
        $this->saveFollowActionLog(
            CpTwitterFollowActionManager::FOLLOW_ACTION_EXEC
        );
    }

    /**
     * Followアクションのステータスを保存する
     *
     * @param $status
     */
    public function saveFollowActionLog($status) {
        /** @var CpTwitterFollowLogService $follow_log_service */
        $follow_log_service = $this->getService('CpTwitterFollowLogService');
        $follow_log_service->create(
            $this->cp_user_id,
            $this->concrete_action_id,
            $status
        );
    }

    /**
     * Twitterサービスクラスの生成
     *
     * @return CpTwitterFollowService
     */
    public function getTwitterFollowService() {
        $user_sns_account_manager = new UserSnsAccountManager($this->Data['pageStatus']['userInfo'], null, $this->Data['pageStatus']['brand']->app_id);
        $sns_account_info =
            $user_sns_account_manager->getSnsAccountInfo(
                $this->getSNSAccountId($this->Data['pageStatus']['userInfo'], self::SOCIAL_TYPE_TWITTER),
                self::SOCIAL_TYPE_TWITTER
            );
        /** @var  CpTwitterFollowService $tw_follow_service */
        $tw_follow_service = $this->getService('CpTwitterFollowService',
            array(
                $sns_account_info['social_media_access_token'],
                $sns_account_info['social_media_access_refresh_token']
            )
        );

        return $tw_follow_service;
    }

    /**
     * フォロー対象のブランドTWアカウントを取得
     *
     * @return mixed
     */
    public function getBrandSocialAccount() {
        /** @var CpTwitterFollowAccountService $follow_account_service */
        $follow_account_service = $this->createService('CpTwitterFollowAccountService');
        $cp_tw_follow_account =
            $follow_account_service->getFollowTargetSocialAccount(
                $this->concrete_action_id
            );

        return $cp_tw_follow_account->getBrandSocialAccount();
    }
}
