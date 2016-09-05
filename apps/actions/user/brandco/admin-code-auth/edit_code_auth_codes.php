<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class edit_code_auth_codes extends BrandcoGETActionBase {
    protected $ContainerName = 'update_code_auth';

    public $NeedOption = array(BrandOptions::OPTION_CP);
    public $NeedAdminLogin = true;
    private $page_limit = 20;

    private $code_auth_service;

    public function doThisFirst() {
        $this->Data['code_auth_id'] = $this->GET['exts'][0];
        $this->deleteErrorSession();
    }

    public function validate() {

        if (!$this->Data['code_auth_id']) {
            return '404';
        }

        $this->code_auth_service = $this->createService('CodeAuthenticationService');
        $this->Data['code_auth'] = $this->code_auth_service->getCodeAuthById($this->Data['code_auth_id']);
        if (!$this->Data['code_auth'] || $this->Data['code_auth']->brand_id != $this->getBrand()->id) {
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

        if (!$this->getActionContainer('Errors')) {
            $data['name'] = $this->Data['code_auth']->name;
            $data['description'] = $this->Data['code_auth']->description;

            foreach($this->Data['code_auth_codes'] as $code_auth_code) {
                $data['max_num/' . $code_auth_code->id] = $code_auth_code->max_num;
                if ($code_auth_code->expire_date == '0000-00-00 00:00:00') {
                    $data['non_expire_date/' . $code_auth_code->id] = '1';
                } else {
                    $data['expire_date/' . $code_auth_code->id] = date_create($code_auth_code->expire_date)->format('Y/m/d');
                }
            }

            $this->assign('ActionForm', $data);
        }

        $code_auth_action_manager = $this->createService('CpCodeAuthActionManager');
        $code_auth_action = $code_auth_action_manager->getCpConcreteActionByCodeAuthId($this->Data['code_auth_id']);
        $this->Data['can_delete'] = $code_auth_action ? false : true;

        list($reserved_num, $total_num) = $this->code_auth_service->getCodeAuthStatisticByCodeAuthId($this->Data['code_auth_id']);
        $this->Data['code_auth_limit'] = $total_num;
        $this->Data['code_auth_reserved'] = $reserved_num;

        return 'user/brandco/admin-code-auth/edit_code_auth_codes.php';
    }
}
