<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class UserMessageThreadActionYoutubeChannel extends aafwWidgetBase{

    const VIEW_CONNECT = 1;  //チャンネル登録のボタンが表示されます
    const VIEW_FOLLOWED = 2; //チャンネル登録済のボタンが表示されます
    const VIEW_EXECUTE = 3;  //api_executeが自動で走ります

    public function doService( $params = array() ) {

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $cp_action = $params['message_info']["cp_action"];
        $cp = CpInfoContainer::getInstance()->getCpById($params['cp_user']->cp_id);

        // 対象チャンネルを取得
        /** @var CpYoutubeChannelAccountService $cp_yt_channel_account_service */
        $cp_yt_channel_account_service = $this->getService('CpYoutubeChannelAccountService');
        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->getService('BrandSocialAccountService');

        $cp_yt_channel_account = $cp_yt_channel_account_service->getAccount($params['message_info']['concrete_action']->id);
        $params['target_account'] = $brand_social_account_service->getBrandSocialAccountById($cp_yt_channel_account->brand_social_account_id);
        $params['channel_id'] = json_decode($params['target_account']->store)->channelId;


        // 紹介動画の取得
        /** @var YoutubeStreamService $yt_stream_service */
        $yt_stream_service = $this->getService('YoutubeStreamService');

        if ($params['message_info']['concrete_action']->intro_flg) {
            $params['target_entry'] = $yt_stream_service->getEntryById($cp_yt_channel_account->youtube_entry_id);
        }


        // ユーザの状態を判定します
        $params['view_status'] = self::VIEW_CONNECT; //登録ボタンを表示します

        if ($params['message_info']['action_status']->status == CpUserActionStatus::NOT_JOIN) { //未完了

            if ($_SESSION['cp_action_'.$cp_action->id]['autoFollow'] === 1) { //連携チェック後api_executeを呼んでチャンネル登録

                //セッション戻す
                $_SESSION['cp_action_'.$cp_action->id]['autoFollow'] = 0;

                // ユーザのアカウント情報取得
                $brandco_social_account = $this->getBrandcoSocialAccount($params['cp_user']->id);

                if ($brandco_social_account) {
                    $params['view_status'] = self::VIEW_EXECUTE;
                }
            }

        } elseif ($params['message_info']['action_status']->status == CpUserActionStatus::JOIN) { //完了

            /** @var CpYoutubeChannelUserLogService $cp_yt_channel_user_log_service */
            $cp_yt_channel_user_log_service = $this->getService('CpYoutubeChannelUserLogService');
            $user_log = $cp_yt_channel_user_log_service->getLog($params['message_info']["cp_action"]->id, $params['cp_user']->id);

            if ($_SESSION['cp_action_'.$cp_action->id]['autoFollow'] === 1) { //チャンネル登録します

                //セッション戻す
                $_SESSION['cp_action_'.$cp_action->id]['autoFollow'] = 0;

                // ユーザのアカウント情報取得
                $brandco_social_account = $this->getBrandcoSocialAccount($params['cp_user']->id);

                if ($brandco_social_account) {
                    // チャンネル登録
                    $status = $cp_yt_channel_user_log_service->subscribeYoutubeChannel($brandco_social_account->access_token, $params['channel_id']);

                    if ($status == CpYoutubeChannelUserLog::STATUS_ERROR) {
                        // エラー表示を出す
                        $params['yt_api_error'] = true;
                    } else {
                        // ログを取る
                        $cp_yt_channel_user_log_service->setLog($params['message_info']["cp_action"]->id, $params['cp_user']->id, $status);
                        $params['view_status'] = self::VIEW_FOLLOWED;
                    }
                }

            } elseif ($user_log->status != CpYoutubeChannelUserLog::STATUS_SKIP) { //登録済み画面を返します

                $params['view_status'] = self::VIEW_FOLLOWED;
            }
        }

        $params['callback_url'] = Util::rewriteUrl('messages', 'thread', array($cp->id));

        return $params;
    }

    /**
     * ユーザのYouTubeのアカウント情報
     * @param $cp_user_id
     * @return entity
     */
    private function getBrandcoSocialAccount($cp_user_id) {

         /** @var CpUserService $cp_user_service */
         $cp_user_service = $this->getService('CpUserService');
         $user = $cp_user_service->getUserByCpUserId($cp_user_id);

         /** @var BrandcoSocialAccountService $brandco_social_account_service */
         $brandco_social_account_service = $this->getService('BrandcoSocialAccountService');
         $brandco_social_account = $brandco_social_account_service->getBrandcoSocialAccount($user->id, SocialApps::PROVIDER_GOOGLE);

         return $brandco_social_account;
     }
}
