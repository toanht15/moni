<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionTweet extends aafwWidgetBase{

    private $ActionForm;
    private $ActionError;
    private $cp;

    public function doService( $params = array() ){
        $this->ActionForm   = $params['ActionForm'];
        $this->ActionError  = $params['ActionError'];
        $service_factory            = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service            = $service_factory->create('CpFlowService');

        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);

        $this->cp = $cp_flow_service->getCpById($params['cp_id']);

        if ($this->cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($this->cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        $params['is_last_action'] = $params['action']->isLastCpActionInGroup();

        return $params;
    }
}
