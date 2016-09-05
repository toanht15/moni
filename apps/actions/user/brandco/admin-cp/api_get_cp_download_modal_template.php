<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_get_cp_download_modal_template extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;


    public function validate() {
        return true;
    }

    public function doAction() {
        /** @var CpListService $cp_list_service */
        $cp_list_service = $this->createService('CpListService');
        $cp_ids[] = $this->GET['cp_id'];
        $cp = $cp_list_service->getListPublicCp($cp_ids);
        $this->Data['cp'] = $cp;
        return 'user/brandco/admin-cp/api_get_cp_download_modal_template.php';
    }
}