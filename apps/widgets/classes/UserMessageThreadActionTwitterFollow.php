<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpTwitterFollowActionManager');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class UserMessageThreadActionTwitterFollow extends aafwWidgetBase {
    private $is_skip_action;

    /**
     * @param array $params
     * @return array
     */
    public function doService($params) {

        /** @var CpTwitterFollowService $tw_follow_service */
        $tw_follow_service =
            $this->getTwitterFollowService($params['pageStatus']);

        // フォロー対象のTwitterアカウントを取得
        $params['brand_social_account'] =
            $tw_follow_service->getTwitterFollowTargeAccount(
                $params['message_info']['concrete_action']->id
            );

        // Twitterフォロータイトルの取得
        $params['title'] = $params['message_info']['concrete_action']->title;

        // スキップフラグの取得
        $params['skip_flg'] = $params['message_info']['concrete_action']->skip_flg;

        // ユーザのTwitter情報の取得
        $target_user = null;
        $social_accounts = $params['pageStatus']['userInfo']->socialAccounts;
        foreach ($social_accounts as $account) {
            if ($account->socialMediaType ===
                    CpTwitterFollowActionManager::SOCIAL_TYPE_STRING) {
                $target_user = $account;
                break;
            }
        }

        //AccessToken期限切れチェック
        $params['access_token_valid'] = $tw_follow_service->checkCredentials();

        // Followフォームの表示ステータスパラメータ生成
        //  * ユーザが対象モジュールのSNS連携していない場合は、次のモジュールを自動で呼ぶ
        //      (モジュールでは何も表示しない)
        //  * ユーザがすでに「フォロー」していた場合は、次のモジュールを自動で呼ぶ
        //      (モジュールでは何も表示しない)
        /** @var CpTwitterFollowLogService $log_service */
        $log_service = $this->getService('CpTwitterFollowLogService');
        $log = $log_service->getLogByCpUserIdAndActionId(
                $params['cp_user']->id,
                $params['message_info']['concrete_action']->id
        );

        $connecting_log = $log_service->getConnectingLogByCpUserIdAndActionId(
            $params['cp_user']->id,
            $params['message_info']['concrete_action']->id
        );
        $is_connecting_log = $connecting_log != null;

        // フォロー状態のチェック
        $form_status_array = $tw_follow_service->getFollowFormStatusParams(
            $params['brand_social_account'],
            $target_user
        );

        $params['is_skip_action'] = $this->isSkipAction($params['pageStatus'], $target_user);

        if (is_null($log)) {
            $params['auto_skip_action'] = $this->isSkipAction($params['pageStatus'], $target_user);

            // 未参加
            $statuses = array(
                CpTwitterFollowActionManager::FOLLOW_ACTION_EXEC,
                CpTwitterFollowActionManager::FOLLOW_ACTION_ALREADY
            );

            foreach ($statuses as $status) {
                $params["follow_${status}"] = $form_status_array["follow_${status}"];
            }

            // 締め切り日の確認を行う
            $cp_action = $params['message_info']['cp_action'];
            if ($params['follow_' . CpTwitterFollowActionManager::FOLLOW_ACTION_EXEC] &&
                !$cp_action->isActive()
            ) {
                // フォローボタンをdisable状態
                // スキップリンクの非表示
                $params['dead_line'] = true;
            } else {
                if (is_null($target_user) || !$params['access_token_valid']) {
                    $params['follow_' . CpTwitterFollowActionManager::FOLLOW_ACTION_CONNECTING] = true;
                } else {
                    $params['follow_' . CpTwitterFollowActionManager::FOLLOW_ACTION_CONNECTING] = false;
                    if ($is_connecting_log) {
                        // 自動フォロー
                        $params['follow_' . CpTwitterFollowActionManager::FOLLOW_ACTION_CLOSE . '_' . CpTwitterFollowActionManager::FOLLOW_ACTION_CONNECTING] = true;
                    }
                }
            }
        } else {
            // 参加済み
            $params['follow_' . CpTwitterFollowActionManager::FOLLOW_ACTION_EXEC] = true;
            if ($log->isStatusUnread()) {
                $params['follow_' . CpTwitterFollowActionManager::FOLLOW_ACTION_EXEC] = false;
            }

            if ($form_status_array['follow_' . CpTwitterFollowActionManager::FOLLOW_ACTION_ALREADY]) {
                // フォロー済み
                $params['follow_' . CpTwitterFollowActionManager::FOLLOW_ACTION_CLOSE . '_' . CpTwitterFollowActionManager::FOLLOW_ACTION_ALREADY] = true;
            } else {
                // 未フォロー
                $params['follow_' . CpTwitterFollowActionManager::FOLLOW_ACTION_CLOSE . '_' . CpTwitterFollowActionManager::FOLLOW_ACTION_EXEC] = true;
            }
        }

        // 最後のアクションかどうか判定する
        $params['is_last_action'] = $params['message_info']['cp_action']->isLastCpActionInGroup();

        return $params;
    }

    /**
     * Twitterサービスクラスの生成
     *
     * @param $pageStatus
     * @return CpTwitterFollowService
     */
    public function getTwitterFollowService($pageStatus) {
        $user_sns_account_manager = new UserSnsAccountManager($pageStatus["userInfo"], null, $pageStatus["brand"]->app_id);
        $type = CpTwitterFollowActionManager::SOCIAL_TYPE_STRING;
        $sns_account_info =
            $user_sns_account_manager->getSnsAccountInfo(
                $this->getSNSAccountId($pageStatus["userInfo"], $type),
                $type
            );
        /** @var CpTwitterFollowService $tw_follow_service */
        $tw_follow_service = $this->getService('CpTwitterFollowService',
            array(
                $sns_account_info['social_media_access_token'],
                $sns_account_info['social_media_access_refresh_token']
            )
        );

        return $tw_follow_service;
    }

    /**
     * @param $user_info
     * @param $sns_type
     * @return int
     */
    public function getSNSAccountId($user_info, $sns_type) {
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
    private function isSkipAction($params, $target_user) {
        if (Util::isNullOrEmpty($this->is_skip_action)) {
            $this->is_skip_action = $params['is_sugao_brand'] && !$target_user;
        }

        return $this->is_skip_action;
    }
}
