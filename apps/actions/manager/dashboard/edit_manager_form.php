<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class edit_manager_form extends BrandcoManagerGETActionBase {

    protected $ContainerName = 'edit_manager';
    public $NeedManagerLogin = true;

    public function beforeValidate () {
        $this->resetValidateError();

        if (!$this->getActionContainer('Errors')) {
            $this->Data['mode'] = $this->mode == ManagerService::ADD_FINISH ? ManagerService::ADD_FINISH : $this->mode;
        } else {
            $this->Data['mode'] = ManagerService::ADD_ERROR;
        }
    }

    public function validate () {
        if (!$this->GET['exts'][0]) {
            return '404';
        }
        return true;
    }

    function doAction() {
        $manager_service = $this->createService('ManagerService');
        $this->Data['manager'] = $manager_service->getManagerById($this->GET['exts'][0]);

        $actionForm = array(
            'username' => $this->Data['manager']->name,
        );
        $this->assign('ActionForm', $actionForm);

        return 'manager/dashboard/edit_manager_form.php';
    }
}
