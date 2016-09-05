<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpFacebookLikeActionManager');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class UserMessageThreadActionFacebookLike extends aafwWidgetBase {

    /**
     * @param array $params
     * @return array
     */
    public function doService($params) {
        /** @var CpFacebookLikeService $fb_like_service */
        $fb_like_service = $this->getService('CpFacebookLikeService');

        // いいね対象のFacebookページ情報を取得
        $params['brand_social_account'] =
            $fb_like_service->getLikeTargeAccount(
                $params['message_info']['concrete_action']->id
            );

        // いいねタイトルの取得
        $params['title'] = $params['message_info']['concrete_action']->title;

        // ユーザのFacebook情報の取得
        $target_user = null;
        $social_accounts = $params['pageStatus']['userInfo']->socialAccounts;
        foreach ($social_accounts as $account) {
            if ($account->socialMediaType ===
                    CpFacebookLikeActionManager::SOCIAL_TYPE_STRING) {
                $target_user = $account;
                break;
            }
        }

        // Likeフォームの表示ステータスパラメータ生成
        //  * ユーザがFacebook連携していない場合は、次のモジュールを自動で呼ぶ
        //      (モジュールでは何も表示しない)
        /** @var CpFacebookLikeLogService $log_service */
        $log_service = $this->getService('CpFacebookLikeLogService');
        $log = $log_service->getLogByCpUserIdAndActionId(
                $params['cp_user']->id,
                $params['message_info']['cp_action']->id
        );
        $params['like_' . CpFacebookLikeLog::LIKE_ACTION_CLOSE . '_1'] = false;
        $params['like_' . CpFacebookLikeLog::LIKE_ACTION_CLOSE . '_2'] = false;

        if (is_null($log)) {
            $form_status_array = $fb_like_service->getLikeFormStatusParams(
                $params['brand_social_account'],
                $target_user
            );
            $statuses = array(
                CpFacebookLikeLog::LIKE_ACTION_EXEC,
                CpFacebookLikeLog::LIKE_ACTION_UNREAD
            );
            foreach ($statuses as $status) {
                $params["like_${status}"] =
                    $form_status_array["like_${status}"];
            }
            // 締め切り日の確認を行う
            $cp_action = $params['message_info']['cp_action'];
            if ($params['like_' . CpFacebookLikeLog::LIKE_ACTION_EXEC] &&
                !$cp_action->isActive()
            ) {
                $params['dead_line'] = true;
            }
        } else {
            $statuses = array(
                CpFacebookLikeLog::LIKE_ACTION_EXEC,
                CpFacebookLikeLog::LIKE_ACTION_UNREAD,
                CpFacebookLikeLog::LIKE_ACTION_CLOSE
            );
            foreach ($statuses as $status) {
                $params["like_${status}"] = false;
            }
            if (is_null($target_user)) {
                $params['like_' . CpFacebookLikeLog::LIKE_ACTION_UNREAD] = true;
            } else {
                $params['like_' . CpFacebookLikeLog::LIKE_ACTION_EXEC] = true;
                $params['like_' . CpFacebookLikeLog::LIKE_ACTION_CLOSE] = true;
                if ($log->isStatusExec()) {
                    $params['like_' . CpFacebookLikeLog::LIKE_ACTION_CLOSE . '_1'] = true;
                }
                if ($log->isStatusAlready()) {
                    $params['like_' . CpFacebookLikeLog::LIKE_ACTION_CLOSE . '_2'] = true;
                }
            }
        }

        return $params;
    }
}
