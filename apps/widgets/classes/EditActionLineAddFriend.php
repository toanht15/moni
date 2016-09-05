<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionLineAddFriend extends aafwWidgetBase{

    private $ActionForm;
    private $ActionError;

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);

        $cp = $cp_flow_service->getCpById($params['cp_id']);

        if ($cp->start_date != '0000-00-00 00:00:00') {
            $params['start_date'] = date_create($cp->start_date)->format('Y/m/d H:i');
        } else {
            $params['start_date'] = date_create()->format('Y/m/d H:i');
        }

        return $params;
    }
} 