<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_change_optin_status extends BrandcoPOSTActionBase
{
    public $NeedOption = array();
    protected $ContainerName = 'api_change_optin_status';
    protected $AllowContent = array('JSON');

    public $CsrfProtect = true;
    public $NeedUserLogin = true;

    public function validate() {
        if (in_array($this->POST['optin_flg'], array(BrandsUsersRelationService::STATUS_OPTIN, BrandsUsersRelationService::STATUS_OPTOUT)) == false) {
            return false;
        }
        return true;
    }

    function doAction()
    {
        $brand = $this->getBrand();
        
        $brand_user_service = $this->createService('BrandsUsersRelationService');
        $user_service = $this->createService('UserService');
        $user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);
        if( $brand_user_service->changeOptinFlg($brand->id, $user->id, $this->POST['optin_flg']) ){
            $json_data = $this->createAjaxResponse("ok");
        }else{
            $json_data = $this->createAjaxResponse("ng");
        }
        
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
