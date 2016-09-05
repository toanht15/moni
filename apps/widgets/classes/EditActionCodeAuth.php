<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionCodeAuth extends aafwWidgetBase {
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
        $start_date = $cp->start_date != '0000-00-00 00:00:00' ? date_create($cp->start_date) : date_create();
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        /** @var CodeAuthenticationService $code_auth_service */
        $code_auth_service = $service_factory->create('CodeAuthenticationService');
        $code_auth_manager = new CpCodeAuthActionManager();
        $cp_code_auth_action = $code_auth_manager->getConcreteAction($params['action']);
        if ($cp_code_auth_action && $cp_code_auth_action->code_auth_id) {
            $params['current_code_auth'] = $code_auth_service->getCodeAuthById($cp_code_auth_action->code_auth_id);
        }

        $params['code_auths'] = $code_auth_service->getAllCodeAuthsByBrandId($cp->brand_id);

        return $params;
    }
}
