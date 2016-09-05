<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpButtonsActionManager');

class EditActionButtons extends aafwWidgetBase{
    private $ActionForm;
    private $ActionError;

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);

        $action_manager = new CpButtonsActionManager();
        $params['after_actions'] = $action_manager->getAfterActions($params['action'],$params['cp_id']);

        $this->ActionError = $params['ActionError'];

        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        $next_actions_info = $action_manager->getNextActionsInfo($params['action']);

        if ($params['ActionError']) {
            //TODO change value of view
        }

        if ($next_actions_info) {
            $params['cp_next_actions_info'] = $next_actions_info->toArray();
        }

        return $params;
    }
} 