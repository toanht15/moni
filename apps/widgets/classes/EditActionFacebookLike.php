<?php
/**
 * User: t-yokoyama
 * Date: 15/03/24
 * Time: 13:35
 */

AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpFacebookLikeActionManager');

class EditActionFacebookLike extends aafwWidgetBase {
    private $ActionForm;
    private $ActionError;

    public function doService($params = array()) {
        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);

        $cp = $cp_flow_service->getCpById($params['cp_id']);

        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $service_factory->create('BrandSocialAccountService');

        // 連携済みFBページ一覧取得
        $params['brand_social_accounts'] =
            $brand_social_account_service->getSocialAccountsByBrandId(
                $cp->brand_id,
                SocialApps::PROVIDER_FACEBOOK
        );

        // Likeフォームタイトル生成
        $action_manager = new CpFacebookLikeActionManager();
        $params['titles'] = array();
        foreach ($params['brand_social_accounts'] as $account) {
            $params['titles'][$account->id] =
                $action_manager->makeLikeActionTitle($account->name);
        }

        // アクション情報取得
        list($cp_action, $concrete_action) =
            $action_manager->getCpActions($params['action']->id);
        $params['action'] = $cp_action;

        // 設定済みのFBページを取得
        /** @var CpTwitterFollowAccountService $cp_fb_like_service */
        $cp_fb_like_service = $service_factory->create('CpFacebookLikeAccountService');
        $fb_like_account =
            $cp_fb_like_service->getLikeTargetSocialAccount(
                $concrete_action->id
            );
        $params['fb_like_account'] = $fb_like_account->brand_social_account_id;

        // callbackUrlパラメータ生成
        $callback_url;
        if (preg_match('#edit_action_base#', $_SERVER['REQUEST_URI'])) {
            // 新規作成
            $callback_url = Util::rewriteUrl(
                'admin-cp',
                'edit_action_base',
                array($params['action']->id),
                array('connect' => 'fb')
            );
        } else {
            // 編集
            $callback_url = Util::rewriteUrl(
                'admin-cp',
                'edit_action',
                array($params['cp_id'], $params['action']->id),
                array('connect' => 'fb')
            );
        }
        $params['callback_url'] = urlencode($callback_url);

        return $params;
    }
}
