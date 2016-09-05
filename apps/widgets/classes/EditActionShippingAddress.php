<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionShippingAddress extends aafwWidgetBase{
    private $ActionForm;
    private $ActionError;

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);
        
        /** @var PrefectureService $prefectureService */
        $prefectureService = $service_factory->create('PrefectureService');
        $params['prefectures'] = $prefectureService->getPrefecturesKeyValue();

        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        return $params;
    }
} 