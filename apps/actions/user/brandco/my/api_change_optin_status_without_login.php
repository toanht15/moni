<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class api_change_optin_status_without_login extends BrandcoPOSTActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_change_optin_status_without_login';
    protected $AllowContent = array('JSON');

    public $CsrfProtect = true;

    private $params;

    public function validate () {
        if (!$this->POST['optin_token']) {
            return '404';
        }

        $this->params = json_decode(base64_decode($this->POST['optin_token']), true);
        if (!$this->params['brand_id'] || !$this->params['user_id']) {
            return '404';
        }

        if ($this->getBrand()->id !== $this->params['brand_id']) {
            return '404';
        }

        if (in_array($this->POST['optin_flg'], array(BrandsUsersRelationService::STATUS_OPTIN, BrandsUsersRelationService::STATUS_OPTOUT)) == false) {
            return false;
        }

        return true;
    }

    function doAction() {
        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->getService('BrandsUsersRelationService');
        if( $brands_users_relation_service->changeOptinFlg($this->getBrand()->id, $this->params['user_id'], $this->POST['optin_flg']) ){
            $json_data = $this->createAjaxResponse("ok");
        } else {
            $json_data = $this->createAjaxResponse("ng");
        }

        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
