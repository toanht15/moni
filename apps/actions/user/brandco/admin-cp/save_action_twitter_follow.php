<?php
AAFW::import('jp.aainc.classes.brandco.cp.SaveActionBase');

class save_action_twitter_follow extends SaveActionBase {

    protected $ContainerName = 'save_action_twitter_follow';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => '{path}?mid=action-not-filled',
    );

    protected $brand = null;

    protected $ValidatorDefinition = [
        'brand_social_account_id' => [
            'type' => 'num'
        ],
    ];

    public function doThisFirst() {
        $this->ValidatorDefinition['brand_social_account_id']['required'] = true;

        $this->fetchDeadLineValidator();
    }

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
        } else {
            $this->Validator->setError('social_account', 'NOT_EXIST_CHOICE');
            return false;
        }

        if (!$this->validateDeadLine()) return false;

        return true;
    }

    function doAction() {

        $this->getCpAction()->status = $this->POST['save_type'];
        $this->renewDeadLineData();

        // キャンペーン基本情報とアクションの更新
        $social_account_service = $this->getService('BrandSocialAccountService');
        $social_account =
            $social_account_service->getBrandSocialAccountById($this->brand_social_account_id);
        $data['title'] =
            $this->getActionManager()->makeFollowActionTitle($social_account->name);
        $data['skip_flg'] = $this->skip_flg ? $this->skip_flg : Cp::FLAG_HIDE_VALUE;
        $this->getActionManager()->updateCpActions($this->getCpAction(), $data);

        // フォロー対象のTWアカウント更新
        /** @var CpTwitterFollowAccountService $tw_social_account_service */
        $tw_social_account_service = $this->getService('CpTwitterFollowAccountService');
        if ($this->brand_social_account_id){
            $tw_social_account_service->deleteByCpTwitterFollowActionId($this->getConcreteAction()->id);
            $tw_social_account_service->create($this->getConcreteAction()->id, $this->brand_social_account_id);
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

    protected function setActionManager() {
        $this->cp_action_manager = $this->getService('CpTwitterFollowActionManager');
    }
}
