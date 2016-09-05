<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpYoutubeChannelActionManager');

class EditActionYoutubeChannel extends aafwWidgetBase{

    public function doService( $params = array() ){
        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        // アクション情報取得
        /** @var CpYoutubeChannelActionManager $action_manager */
        $action_manager = new CpYoutubeChannelActionManager();
        list($cp_action, $concrete_action) =
            $action_manager->getCpActions($params['action_id']);
        $params['action'] = $cp_action;
        $params['concrete_action'] = $concrete_action;

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        // 連携済みYouTubeアカウント一覧取得
        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->getService('BrandSocialAccountService');
        $params['brand_social_accounts'] =
            $brand_social_account_service->getSocialAccountsByBrandId(
                $cp->brand_id,
                SocialApps::PROVIDER_GOOGLE);

        // 各アカウントのentryを取得
        /** @var YoutubeStreamService $yt_stream_service */
        $yt_stream_service = $this->getService('YoutubeStreamService');
        $streams = $yt_stream_service->getAvailableStreamsByBrandId($cp->brand_id);
        $array = array();
        foreach($streams as $stream) {
            $entries = $yt_stream_service->getAllEntriesByStreamId($stream->id);
            $array[0] = '選択して下さい';
            foreach($entries as $entry) {
                $array[$entry->id . ',' . $entry->object_id] = json_decode($entry->extra_data)->snippet->title;
            }
            $params['streams'][$stream->brand_social_account_id] = $array;
            $array = array();
        }

        // 設定済みのYouTubeアカウントを取得
        /** @var CpYoutubeChannelAccountService $cp_yt_channel_account_service */
        $cp_yt_channel_account_service = $this->getService('CpYoutubeChannelAccountService');
        $cp_yt_account = $cp_yt_channel_account_service->getAccount($concrete_action->id);
        if ($cp_yt_account) {
            $params['target_account'] = $brand_social_account_service->getBrandSocialAccountById($cp_yt_account->brand_social_account_id);
            $params['target_entry'] = $yt_stream_service->getEntryById($cp_yt_account->youtube_entry_id);
        }

        // callbackUrlパラメータ生成
        if (preg_match('#edit_action_base#', $_SERVER['REQUEST_URI'])) {
            // 新規作成
            $callback_url = Util::rewriteUrl(
                'admin-cp',
                'edit_action_base',
                array($params['action']->id)
            );
        } else {
            // 編集
            $callback_url = Util::rewriteUrl(
                'admin-cp',
                'edit_action',
                array($params['cp_id'], $params['action']->id));
        }
        $params['callback_url'] = urlencode($callback_url);

        return $params;
    }
}
