<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class edit_redirector_form extends BrandcoGETActionBase {
    protected $ContainerName = 'edit_redirector_form';
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->deleteErrorSession();
        $this->Data['redirector_id'] = $this->GET['exts'][0];
    }

    public function validate() {
        /** @var RedirectorService $redirector_service */
        $redirector_service = $this->createService('RedirectorService');
        if ($this->Data['redirector_id'] !== '0') {
            //既存
            $this->Data['redirector'] = $redirector_service->getRedirectorById($this->Data['redirector_id']);
            if (!$this->Data['redirector']) {
                return false;
            }

            //所有者チェック
            if($this->brand->id != $this->Data['redirector']->brand_id) {
                return false;
            }
        }

        return true;
    }

    function doAction() {

        if ($this->Data['redirector']) {
            $this->assign('ActionForm', $this->Data['redirector']->toArray());
        }
        return 'user/brandco/admin-settings/edit_redirector_form.php';
    }
}
