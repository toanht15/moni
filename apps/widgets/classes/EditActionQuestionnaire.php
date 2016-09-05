<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.brandco.cp.trait.EditActionTrait');

class EditActionQuestionnaire extends aafwWidgetBase{

    use EditActionTrait;

    private $ActionForm;
    private $ActionError;
    private $cp;

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);
        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        $this->cp = $cp_flow_service->getCpById($params['cp_id']);

        if($this->ActionError) {
            $_SESSION['cpQuestionnaireError'] = $this->ActionError;
        } else {
            $this->assign('saved',1);
        }

        if ($this->cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($this->cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');
        $params['is_opening_flg'] = $params['action']->isOpeningCpAction();

        if ($params['is_opening_flg']) {
            $params = $this->fetchQuestionnaires($params);
        }
        return $params;
    }

    public function getContainerType() {
        return $this->cp ? $this->cp->brand_id : 'common';
    }
}
