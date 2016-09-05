<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class code_auth_codes extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    private $page_limit = 20;

    private $code_auth_service;

    public function doThisFirst() {
        $this->Data['code_auth_id'] = $this->GET['exts'][0];
    }

    public function validate() {

        if (!$this->Data['code_auth_id']) {
            return '404';
        }

        $this->code_auth_service = $this->createService('CodeAuthenticationService');
        $this->Data['code_auth'] = $this->code_auth_service->getCodeAuthById($this->Data['code_auth_id']);
        if (!$this->Data['code_auth'] || $this->Data['code_auth']->brand_id != $this->brand->id) {
            return '404';
        }

        return true;
    }

    function doAction() {

        $this->Data['total_count'] = $this->code_auth_service->countCodesByCodeAuthId($this->Data['code_auth_id']);
        $total_page = floor ( $this->Data['total_count'] / $this->page_limit ) + ( $this->Data['total_count'] % $this->page_limit > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);

        $order = array(
            'name' => 'id',
            'direction' => "asc"
        );
        $this->Data['code_auth_codes'] = $this->code_auth_service->getCodesByCodeAuthId($this->Data['code_auth_id'], $this->p, $this->page_limit, $order);
        $this->Data['page_limited'] = $this->page_limit;

        list($reserved_num, $total_num) = $this->code_auth_service->getCodeAuthStatisticByCodeAuthId($this->Data['code_auth_id']);
        $this->Data['code_auth_limit'] = $total_num;
        $this->Data['code_auth_reserved'] = $reserved_num;

        return 'user/brandco/admin-code-auth/code_auth_codes.php';
    }
}
