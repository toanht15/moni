<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class code_auth_list extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_CP);
    public $NeedAdminLogin = true;

    private $page_limited = 20;

    public function doThisFirst() {
        $this->deleteErrorSession();
    }

    public function validate() {
        return true;
    }

    function doAction() {
        $code_auth_service = $this->createService('CodeAuthenticationService');

        $this->Data['total_count'] = $code_auth_service->countCodeAuthsByBrandId($this->getBrand()->id);
        $total_page = floor ( $this->Data['total_count'] / $this->page_limited ) + ( $this->Data['total_count'] % $this->page_limited > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);

        $this->Data['code_auths'] = $code_auth_service->getCodeAuthsByBrandId($this->brand->id, $this->p, $this->page_limited);
        $this->Data['page_limited'] = $this->page_limited;

        return 'user/brandco/admin-code-auth/code_auth_list.php';
    }
}
