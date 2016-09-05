<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionBase extends aafwWidgetBase{

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);
        $params['cp_action_detail'] = $params['action']->getCpActionDetail();

        return $params;
    }
} 