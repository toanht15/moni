<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpEngagementActionManager');

class EditActionPhoto extends aafwWidgetBase {
    private $ActionForm;
    private $ActionError;

    public function doService($params = array()) {
        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);

        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        // アクション情報取得
        $action_manager = new CpPhotoActionManager();
        $actions = $action_manager->getCpActions($params['action']->id);
        $params['action'] = $actions[0];

        return $params;
    }
}
