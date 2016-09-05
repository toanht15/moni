<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditCpFooter extends aafwWidgetBase{
    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $action = $cp_flow_service->getCpActionById($params['action_id']);

        $next_action = $action->getCpNextActions();
        if ($next_action) {
            $next_action = $next_action->current();
            $next_action = $cp_flow_service->getCpActionById($next_action->cp_next_action_id);
            $params['next_action_id'] = $next_action->id;
        }

        return $params;
    }
}