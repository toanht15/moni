<?php
/**
 * User: t-yokoyama
 * Date: 15/03/10
 * Time: 13:35
 */

AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpTwitterFollowActionManager');

class EditActionTwitterFollow extends aafwWidgetBase {
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

        // キャンペーン終了日
        if ($cp->end_date != '0000-00-00 00:00:00') {
            $cp_end_date = date_create($cp->end_date);
            $params['cp_end_date'] = $cp_end_date->format('Y/m/d H:i');
        } else {
            $params['cp_end_date'] = '未確定';
        }

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $service_factory->create('BrandSocialAccountService');

        // 連携済みTWソーシャルアカウント一覧取得
        $params['brand_social_accounts'] =
            $brand_social_account_service->getTwitterSocialAccountsByBrandId($cp->brand_id);

        // アクション情報取得
        $action_manager = new CpTwitterFollowActionManager();
        list($cp_action, $concrete_action) =
            $action_manager->getCpActions($params['action']->id);
        $params['action'] = $cp_action;

        // TWフォロータイトルの取得
        $params['titles'] = array();
        foreach ($params['brand_social_accounts'] as $account) {
            $params['titles'][$account->id] =
                $action_manager->makeFollowActionTitle($account->name);
        }

        // 設定済みのTWフォローアカウントを取得
        /** @var CpTwitterFollowAccountService $cp_tw_follow_account_service */
        $cp_tw_follow_account_service = $service_factory->create('CpTwitterFollowAccountService');
        $tw_follow_social_account =
            $cp_tw_follow_account_service->getFollowTargetSocialAccount(
                $concrete_action->id
            );
        $params['tw_follow_social_account'] = $tw_follow_social_account->brand_social_account_id;

        // callbackUrlパラメータ生成
        $callback_url;
        if (preg_match('#edit_action_base#', $_SERVER['REQUEST_URI'])) {
            // 新規作成
            $callback_url = Util::rewriteUrl(
                'admin-cp',
                'edit_action_base',
                array($params['action']->id),
                array('connect' => 'tw')
            );
        } else {
            // 編集
            $callback_url = Util::rewriteUrl(
                'admin-cp',
                'edit_action',
                array($params['cp_id'], $params['action']->id),
                array('connect' => 'tw')
            );
        }
        $params['callback_url'] = urlencode($callback_url);

        $params['is_last_action'] = $params['action']->isLastCpActionInGroup();

        return $params;
    }
}
