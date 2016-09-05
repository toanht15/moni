<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_change_questionnaire_action_panel_hidden_flg extends BrandcoPOSTActionBase {
    protected $ContainerName = 'questionnaires';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    private $cp_questionnaire_action_service;
    private $cp_questionnaire_action;

    public function validate() {
        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);

        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            return false;
        }

        $this->cp_questionnaire_action_service = $this->getService('CpQuestionnaireActionService');
        $this->cp_questionnaire_action = $this->cp_questionnaire_action_service->getCpQuestionnaireAction($this->POST['action_id']);

        if (!$this->cp_questionnaire_action) {
            return false;
        }

        return true;
    }

    public function doAction() {
        if ($this->cp_questionnaire_action->panel_hidden_flg != $this->POST['panel_hidden_flg']) {
            $this->cp_questionnaire_action->panel_hidden_flg = $this->POST['panel_hidden_flg'];
            $this->cp_questionnaire_action_service->updateCpQuestionnaireAction($this->cp_questionnaire_action);
        }

        $json_data = $this->createAjaxResponse('ok');
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}