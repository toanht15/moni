<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditAction extends aafwWidgetBase{

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $manager_service = $service_factory->create('ManagerService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);
        $params['cp_action_detail'] = $params['action']->getCpActionDetail();
        $params['cp'] = $cp_flow_service->getCpById($params['cp_id']);
        $params['isAgent'] = $manager_service->isAgentLogin();
        return $params;
    }
} 