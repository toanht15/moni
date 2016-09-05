<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpEngagementActionManager');

class save_action_engagement extends BrandcoPOSTActionBase {
    protected $ContainerName = 'save_action_engagement';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;
    protected $brand = null;

    public function doThisFirst() {
        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->ValidatorDefinition['title']['required'] = true;
            $this->ValidatorDefinition['brand_social_account_id']['required'] = true;
        }
    }

    protected $ValidatorDefinition = array(
        'title' => array(
            'type' => 'str',
            'length' => 50,
        ),
        'brand_social_account_id' => array(
            'type' => 'str'
        )
    );

    public function validate() {
        $this->brand = $this->getBrand();
        $validatorService = new CpValidator($this->brand->id);

        if (!$validatorService->isOwnerOfAction($this->POST['action_id'])) {
            $this->Validator->setError('auth', 'NOT_OWNER');
            return false;
        }

        // brandのsocial_accountチェック
        if ($this->brand_social_account_id) {
            if (!$this->hasAccount()){
                $this->Validator->setError('social_account', 'INVALID_ACCOUNT');
                return false;
            }
        }

        return true;
    }

    function doAction() {
        $engagement_action_manager = new CpEngagementActionManager();
        $engagement_action = $engagement_action_manager->getCpActions($this->POST['action_id']);

        $engagement_action[0]->status = $this->POST['save_type'];
        $data['title'] = $this->POST['title'];

        $engagement_action_manager->updateCpActions($engagement_action[0], $data);

        $service_factory = new aafwServiceFactory();
        /** @var EngagementSocialAccountService $engagement_social_account_service */
        $engagement_social_account_service = $service_factory->create('EngagementSocialAccountService');

        // ファンゲート設定更新
        if ($this->brand_social_account_id){
            $engagement_social_account_service->deleteByCpEngagementActionId($engagement_action[1]->id);
            $engagement_social_account_service->create($engagement_action[1]->id, $this->brand_social_account_id);
        }

        $this->Data['saved'] = 1;

        if ($this->POST['save_type'] == CpAction::STATUS_FIX) {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-saved';
        } else {
            $this->POST['callback'] = $this->POST['callback'].'?mid=action-draft';
        }

        return 'redirect: ' . $this->POST['callback'];
    }

    private function hasAccount() {
        $brand_social_accounts = $this->brand->getBrandSocialAccounts();
        foreach($brand_social_accounts as $account) {
            if ($this->brand_social_account_id == $account->id) {
                return true;
            }
        }
        return false;
    }
}
