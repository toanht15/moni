<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class optin_status extends BrandcoGETActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_change_optin_status_without_login';

    private $optin_token;
    private $params;

    public function validate () {
        if (!$this->GET['params']) {
            return '404';
        }

        $this->optin_token = $this->GET['params'];
        $this->params = json_decode(base64_decode($this->GET['params']), true);
        if (!$this->params['brand_id'] || !$this->params['user_id']) {
            return '404';
        }

        if ($this->getBrand()->id !== $this->params['brand_id']) {
            return '404';
        }

        return true;
    }

    function doAction() {
        /** @var UserService $user_service */
        $user_service = $this->getService('UserService');
        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->getService('BrandsUsersRelationService');

        $this->Data['user'] = $user_service->getUserByBrandcoUserId($this->params['user_id']);
        $this->Data['brand'] = $this->getBrand();
        $this->Data['brands_users_relation'] = $brands_users_relation_service->getBrandsUsersRelation($this->Data['brand']->id, $this->Data['user']->id);
        $this->Data['optin_token'] = $this->optin_token;

        return 'user/brandco/my/optin_status.php';
    }
}
