<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');

class withdraw_brands extends BrandcoManagerPOSTActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId = Manager::MENU_USER_SEARCH;
    protected $ContainerName = 'index';
    protected $Form = array(
        'package' => 'users',
        'action' => 'index',
    );

    protected $ValidatorDefinition = array(
        'relation_id' => array(
            'type' => 'num'
        ),
        'brandco_user_id' => array(
            'type' => 'num'
        ),
    );

    public function validate() {
        if (!$this->POST['relation_id'] && !$this->POST['brandco_user_id']) {
            return false;
        }
        return true;
    }

    function doAction() {
        /** @var BrandsUsersRelationService $brands_users_relation_service */
        $brands_users_relation_service = $this->getService('BrandsUsersRelationService');

        if ($this->POST['brandco_user_id']) {
            $relations = $brands_users_relation_service->getBrandsUsersRelationsByUserId($this->POST['brandco_user_id']);
            foreach ($relations as $relation) {
                if (!$relation->withdraw_flg) {
                    $brands_users_relation_service->withdrawByBrandUserRelation($relation);
                }
            }
        } elseif ($this->POST['relation_id']) {
            $relation = $brands_users_relation_service->getBrandsUsersRelationById($this->POST['relation_id']);
            if (!$relation->withdraw_flg) {
                $brands_users_relation_service->withdrawByBrandUserRelation($relation);
            }
        }
        return 'redirect: ' . urldecode($this->POST['return_url']);
    }
}
