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
        $brand = $brand_service->getBrandByOuterToken($this->GET['token']);
        if (!$brand) {
            return 403;
        }
        $this->GET['directory_name'] = $brand->directory_name;
        BrandInfoContainer::getInstance()->initialize($brand);
    }

    public function validate () {
        // 認証チェック
        if ($this->getSession('login_outer') !== 1) {
            return false;
        }

        if (!is_numeric($this->getSession('brand_social_account_id'))) {
            return false;
        }

        $this->NeedPublic = true;
        return true;
    }

    public function doAction() {
        /** @var BrandSocialAccountService $service */
        $brandSocialAccountService = $this->createService('BrandSocialAccountService');
        $this->Data['account'] =
            $brandSocialAccountService->getBrandSocialAccountById(
                $this->getSession('brand_social_account_id')
            );

        return 'user/instagram_outer/connect_finish.php';
    }
}
