<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpInstagramFollowActionManager');
AAFW::import('jp.aainc.vendor.instagram.Instagram');

class EditActionInstagramFollow extends aafwWidgetBase{

    public function doService( $params = array() ) {

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $params['is_last_action'] = $cp_flow_service->isLastCpActionInGroup($params['action_id']);
        $action_manager = new CpInstagramFollowActionManager();
        list($cp_action, $concrete_action) =
            $action_manager->getCpActions($params['action_id']);
        $params['action'] = $cp_action;

        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->getService('BrandSocialAccountService');

        // 連携済みInstagramアカウント一覧取得
        $params['brand_social_accounts'] =
            $brand_social_account_service->getSocialAccountsByBrandId(
                $cp->brand_id,
                SocialApps::PROVIDER_INSTAGRAM);

        // 設定済みのInstagramアカウントを取得
        /** @var CpInstagramFollowEntryService $cp_ig_follow_entry_service */
        $cp_ig_follow_entry_service = $this->getService('CpInstagramFollowEntryService');
        /** @var InstagramStreamService $ig_stream_service */
        $ig_stream_service = $this->getService('InstagramStreamService');
        if ($this->current_entry_id) {
            $params['tgt_account'] = $brand_social_account_service->getBrandSocialAccountByEntryId($this->current_entry_id);
            $params['tgt_entry'] = $ig_stream_service->getEntryById($this->current_entry_id);
        } else {
            $ig_follow_social_account = $cp_ig_follow_entry_service->getTargetAccount($concrete_action->id);
            $params['tgt_account'] = $brand_social_account_service->getBrandSocialAccountById($ig_follow_social_account->brand_social_account_id);
            $params['tgt_entry'] = $ig_stream_service->getEntryById($ig_follow_social_account->instagram_entry_id);
        }

        if (!$params['tgt_account']) {
            $params['tgt_account']->id = CpInstagramFollowAction::NO_TARGET_ACCOUNT;
        }

        // 埋め込みのHTMLを取得
        if ($params['tgt_entry']) {
            $instagram = new Instagram();
            $response = $instagram->getEmbedMedia($params['tgt_entry']->link);
            $params['response_html'] = $response->html;
        }

        // callbackUrlパラメータ生成
        if (preg_match('#edit_action_base#', $_SERVER['REQUEST_URI'])) {
            // 新規作成
            $callback_url = Util::rewriteUrl(
                'admin-cp',
                'edit_action_base',
                array($params['action']->id),
                array('connect' => 'ig')
            );
        } else {
            // 編集
            $callback_url = Util::rewriteUrl(
                'admin-cp',
                'edit_action',
                array($params['cp_id'], $params['action']->id),
                array('connect' => 'ig')
            );
        }
        $params['callback_url'] = urlencode($callback_url);

        return $params;
    }
}
