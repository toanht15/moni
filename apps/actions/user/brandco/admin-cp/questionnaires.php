<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class questionnaires extends BrandcoGETActionBase {
    protected $ContainerName = 'questionnaires';

    public $NeedOption = array(BrandOptions::OPTION_CP, BrandOptions::OPTION_CRM);
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->Data['action_id'] = $this->GET['exts'][0];
    }

    public function validate() {
        if (!$this->isLoginManager()) {
            return '403';
        }

        $questionnaire_validator = new CpDataManagerValidator($this->getBrand()->id, $this->Data['action_id'], CpAction::TYPE_QUESTIONNAIRE);

        if (!$questionnaire_validator->validate()) {
            return '404';
        } else {
            $this->Data['pageData'] = $questionnaire_validator->getCpActionInfo();
            $this->Data['pageData']['brand_id'] = $this->brand->id;
        }
        return true;
    }

    public function doAction() {
        $this->Data['pageData']['target_all'] = true;
        return 'user/brandco/admin-cp/questionnaires.php';
    }
}