<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class login_outer_form extends BrandcoGETActionBase {
    // ActionFormの情報をくわけする
    protected $ContainerName = 'login_outer';
    public $NeedOption = array();
    private $brandOuterToken;

    public function doThisFirst() {
        // Brandのdirectory_nameを取得
        $brand_service = $this->createService('BrandService');
        $brand = $brand_service->getBrandByOuterToken($this->GET['token']);
        if (!$brand) {
            return 403;
        }
        // ドメインマッピングの関係で、directory_nameが重複するパターンがあるようなので
        // チェックを行う
        $directory_name = $brand->directory_name;
        $count = $brand_service->getBrandCountByDirectoryName($directory_name);
        if ($count <= 0 || $count > 1) {
            return 403;
        }
        $this->GET['directory_name'] = $directory_name;
        BrandInfoContainer::getInstance()->initialize($brand);

        // 別のSNSアカウントでのログイン情報が残っている可能性もあるので
        // ログインセッションを破棄する
        $this->setSession('login_outer', null);
    }

    public function validate() {
        // token check
        $tokenService = $this->createService('BrandOuterTokenService');
        $this->brandOuterToken = $tokenService->getBrandOuterTokenByToken($this->GET['token']);
        if (is_null($this->brandOuterToken)) {
            return false;
        }
        $this->NeedPublic = true;

        return true;
    }

    public function doAction() {
        $this->Data['token'] = $this->token;
        $this->Data['brand_outer_token'] = $this->brandOuterToken;
        $this->Data['brand'] = $this->brandOuterToken->getBrands()->toArray()[0];

        $social_app = $this->brandOuterToken->getSocialApps()->toArray()[0];
        $this->Data['social_app'] = $social_app;
        $this->Data['title'] = sprintf('%s連携', $social_app->name);

        return 'user/sns/login_outer_form.php';
    }
}
