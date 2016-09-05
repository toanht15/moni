<?php

AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionShare extends aafwWidgetBase{
    private $ActionForm;
    private $ActionError;

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);
        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        /** @var CpShareActionService $cp_share_action_service */
        $cp_share_action_service = $service_factory->create('CpShareActionService');

        $params['cp_share_action'] = $cp_share_action_service->getCpShareActionById($params['action']->id);

        $params['can_share_external_page'] = $params['pageStatus']['isLoginManager'] ? true : false;

        if($params['cp_share_action']->meta_data){
            $params['meta_tags'] = json_decode($params['cp_share_action']->meta_data);
        }

        $params['error_share_url'] = false;
        if($this->ActionError && !$this->ActionError->isValid('share_url')) {
            $params['error_share_url'] = true;
        }

        $params['cp_og_info'] = $cp->getReferenceOpenGraphInfo();

        $params['is_last_action'] = $params['action']->isLastCpActionInGroup();

        return $params;
    }

}