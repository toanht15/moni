<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');

class set_optin extends BrandcoManagerPOSTActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId = Manager::MENU_USER_SEARCH;
    protected $ContainerName = 'index';
    protected $Form = array(
        'package' => 'users',
        'action' => 'index',
    );

    protected $ValidatorDefinition = array(
        'relation_id' => array(
            'type' => 'num',
            'required' => 1
        ),
        'new_optin_flg' => array(
            'type' => 'num',
            'range' => array(
                '>=' => BrandsUsersRelationService::STATUS_OPTIN,
                '<=' => BrandsUsersRelationService::STATUS_OPTOUT
            ),
            'required' => 1
        ),
    );

    protected $brands_users_relation;
    /** @var BrandsUsersRelationService $brands_users_relation_service */
    protected $brands_users_relation_service;

    public function doThisFirst() {
        $this->brands_users_relation_service = $this->getService('BrandsUsersRelationService');
    }

    public function validate() {
        if (!$this->POST['relation_id'] || !$this->POST['new_optin_flg']) {
            return false;
        }

        $this->brands_users_relation = $this->brands_users_relation_service->getBrandsUsersRelationById($this->POST['relation_id']);
        if ($this->brands_users_relation->optin_flg == $this->POST['new_optin_flg']) {
            return false;
        }
        return true;
    }

    function doAction() {
        $this->brands_users_relation_service->setOptinFlg($this->brands_users_relation, $this->POST['new_optin_flg']);
        return 'redirect: ' . urldecode($this->POST['return_url']);
    }
}
