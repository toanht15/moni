<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class brand_login_history extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    private $pageLimited =20;

    public function validate () {
        return true;
    }

    function doAction() {
        if(!$this->p){
            $this->p = 1;
        }
        $brand_id = $this->GET['exts'][0];
        $login_log_admin_data_service = $this->createService('LoginLogAdminDataService');
        $this->Data['login_count'] = $login_log_admin_data_service->getLoginCountByBrandId($brand_id);
        $param = array(
            'conditions' => array(
                'brand_id' => $brand_id,
            )
        );
        $this->Data['pager'] = $login_log_admin_data_service->getPagers($this->p, $this->pageLimited, $param);
        // ページング
        $this->Data['allBrandCount'] =  $this->Data['login_count'];
        $total_page = floor ( $this->Data['allBrandCount'] / $this->pageLimited ) + ( $this->Data['allBrandCount'] % $this->pageLimited > 0 );
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['pageLimited'] = $this->pageLimited;

        return 'manager/dashboard/brand_login_history.php';
    }
}