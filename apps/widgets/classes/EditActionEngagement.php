<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpEngagementActionManager');

class EditActionEngagement extends aafwWidgetBase {
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

        // 連携済みソーシャルアカウント一覧取得
        $params['brand_social_accounts'] = $brand_social_account_service->getBrandSocialAccountByBrandId($cp->brand_id);

        // アクション情報取得
        $cp_engagement_action_manager = new CpEngagementActionManager();
        $actions = $cp_engagement_action_manager->getCpActions($params['action']->id);
        $params['action'] = $actions[0];

        /** @var EngagementSocialAccountService $engagement_social_account_service */
        $engagement_social_account_service = $service_factory->create('EngagementSocialAccountService');

        // ファンゲート設定済取得
        $engagement_social_account = $engagement_social_account_service->getEngagementSocialAccount($actions[1]->id);
        $params['engagement_social_account'] = $engagement_social_account->brand_social_account_id;

        // ファンゲート設定済一覧取得
        $cp_groups = $cp_flow_service->getCpActionGroupsByCpId($params['cp_id']);
        $connected_brand_social_account_ids = array();
        foreach ($cp_groups as $cp_group) {
            $cp_actions = $cp_flow_service->getFixedCpActionsByCpActionGroupIdAndType($cp_group->id, CpAction::TYPE_ENGAGEMENT);
            foreach ($cp_actions as $cp_action) {
                if($params['is_fan_list_page'] && $cp_action->id == $params['action']->id) {
                    continue;
                } else {
                    $engagement_action = $cp_engagement_action_manager->getConcreteAction($cp_action);
                    $engagement_social_acc = $engagement_social_account_service->getEngagementSocialAccount($engagement_action->id);
                    if ($engagement_social_acc) {
                        $connected_brand_social_account_ids[] = $engagement_social_acc->brand_social_account_id;
                    }
                }
            }
        }
        $params['connected_brand_social_account_ids'] = $connected_brand_social_account_ids;
        return $params;
    }
}
