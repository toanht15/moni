<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class connect_finish extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $NeedLogin = true;
    protected $ContainerName = 'login_outer';

    public function doThisFirst() {
        // Brandのdirectory_nameを取得
        $brand_service = $this->createService('BrandService');
        $this->brand = $brand_service->getBrandByOuterToken($this->GET['token']);
        if (!$this->brand) {
            return 403;
        }
        $this->GET['directory_name'] = $this->brand->directory_name;
        BrandInfoContainer::getInstance()->initialize($this->brand);
    }

    public function validate () {
        // 認証チェック
        if ($this->getSession('login_outer') !== 1) {
            return false;
        }

        foreach ($this->getSession('brand_social_account_ids') as $brand_social_account_id) {
            if (!is_numeric($brand_social_account_id)) {
                return false;
            }
        }

        $this->NeedPublic = true;
        return true;
    }

    public function doAction() {
        $brand = $this->getBrand();

        /** @var BrandSocialAccountService $brandSocialAccountService */
        $brandSocialAccountService = $this->createService('BrandSocialAccountService');

        foreach ($this->getSession('brand_social_account_ids') as $brand_social_account_id) {
            $this->Data['accounts'][] =
                $brandSocialAccountService->getBrandSocialAccountById($brand_social_account_id);
        }

        return 'user/facebook_outer/connect_finish.php';
    }
}
