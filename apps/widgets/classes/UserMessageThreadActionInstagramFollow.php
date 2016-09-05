<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class UserMessageThreadActionInstagramFollow extends aafwWidgetBase{

    public function doService( $params = array() ){

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $params['is_last_action'] = $cp_flow_service->isLastCpActionInGroup($params['message_info']['cp_action']->id);

        // フォロー対象のInstagramアカウントとエントリーを取得
        /** @var InstagramStreamService $ig_stream_service */
        $ig_stream_service = $this->getService('InstagramStreamService');
        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->getService('BrandSocialAccountService');
        /** @var CpInstagramFollowEntryService $ig_follow_entry_service */
        $ig_follow_entry_service = $this->getService('CpInstagramFollowEntryService');
        $params['ig_follow_entry'] = $ig_follow_entry_service->getTargetAccount($params['message_info']['concrete_action']->id);
        $params['brand_social_account'] = $brand_social_account_service->getBrandSocialAccountById($params['ig_follow_entry']->brand_social_account_id);
        $params['tgt_entry'] = $ig_stream_service->getEntryById($params['ig_follow_entry']->instagram_entry_id);

        // 埋め込みのHTMLを取得
        $instagram = new Instagram();
        $response = $instagram->getEmbedMedia($params['tgt_entry']->link);
        $params['response_html'] = $response->html;

        // ユーザのInstagram情報の取得
        $target_user = $this->getInstagramUser($params['pageStatus']['userInfo']);

        // 連携状態を判定（連携ログ）
        /** @var CpInstagramFollowActionLogService $ig_follow_action_log_service */
        $ig_follow_action_log_service = $this->getService('CpInstagramFollowActionLogService');
        $ig_follow_action_log_service->createOnceLog(
            $params['message_info']['cp_action']->id,
            $params['cp_user']->id,
            $target_user ? CpInstagramFollowActionLog::STATUS_COOPERATING : CpInstagramFollowActionLog::STATUS_NOT_COOPERATED
        );

        // フォローログ
        if ($target_user) {

            /** @var CpInstagramFollowUserLogService $ig_follow_user_log_service */
            $ig_follow_user_log_service = $this->getService('CpInstagramFollowUserLogService');
            $igFollowUserLog = $ig_follow_user_log_service->getLog($params['message_info']['cp_action']->id, $params['cp_user']->id);

            if (!$igFollowUserLog) {

                $igFollowUserLog = $ig_follow_user_log_service->createEmptyLog();

                // フォロー状態取得
                $ret = $instagram->getRelationship($target_user->socialMediaAccountID, $params['brand_social_account']->token);

                // フォローログ残す
                $igFollowUserLog->cp_action_id = $params['message_info']['cp_action']->id;
                $igFollowUserLog->cp_user_id = $params['cp_user']->id;
                $igFollowUserLog->social_media_account_id = $params['brand_social_account']->social_media_account_id;
                $igFollowUserLog->follow_status = ($ret->data->incoming_status == Instagram::INCOMING_STATUS_FOLLOWS ? CpInstagramFollowUserLog::FOLLOWING : CpInstagramFollowUserLog::NOT_FOLLOWED);
                $ig_follow_user_log_service->saveLog($igFollowUserLog);
            }
        }

        return $params;
    }

    private function getInstagramUser($userInfo) {
        $user = null;
        $social_accounts = $userInfo->socialAccounts;
        foreach ($social_accounts as $account) {
            if ($account->socialMediaType === 'Instagram') {
                $user = $account;
                break;
            }
        }
        return $user;
    }
}
