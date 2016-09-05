<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class edit_sns_form extends BrandcoManagerGETActionBase {

    protected $ContainerName = 'edit_sns_form';
    public $NeedManagerLogin = true;

    public function validate () {
        $brandId = $this->GET['exts'][0];
        if (!is_numeric($brandId)) {
            return false;
        }

        return true;
    }

    function doAction() {
        $brandId = $this->GET['exts'][0];

        /** @var BrandService  $brand_service */
        $brand_service = $this->createService('BrandService');
        $brand = $brand_service->getBrandById($brandId);
        $this->Data['brand'] = $brand;

        // 各Brandの連携情報を取得
        $params = [
            'fb' => [
                $brandId,
                SocialApps::PROVIDER_FACEBOOK
            ],
            'tw' => [
                $brandId,
                SocialApps::PROVIDER_TWITTER
            ],
            'yt' => [
                $brandId,
                SocialApps::PROVIDER_GOOGLE
            ],
            'ig' => [
                $brandId,
                SocialApps::PROVIDER_INSTAGRAM
            ],
        ];
        $dataList = [];
        /** @var BrandSocialAccountService $brandSocialAccountService */
        $brandSocialAccountService = $this->createService('BrandSocialAccountService');
        foreach ($params as $key => $value) {
            $dataList[$key] = $brandSocialAccountService->getSocialAccountsByBrandId(
                $value[0],
                $value[1]
            );
        }
        $this->Data['data_list'] = $dataList;

        // 管理者情報を取得
        /** @var UserService $userService */
        $userService = $this->createService('UserService');
        $adminUsers = $userService->getAdminUsers($brand->id);
        $adminUserParams = [];
        foreach ($adminUsers as $adminUser) {
            $adminUserParams[$adminUser->id] = $adminUser->name ?: '名無しさん';
        }
        $this->Data['admin_user_params'] = $adminUserParams;

        $this->Data['sns_outer_url'] = Util::rewriteUrl(
            'api',
            'create_sns_outer_url',
            [],
            [],
            config('Protocol.Secure') . '://' . config('Domain.brandco_manager') . '/',
            true
        ) . '.json';

        $this->Data['brand_id'] = $brand->id;

        $this->Data['types'] = [
            'fb' => SocialApps::PROVIDER_FACEBOOK,
            'tw' => SocialApps::PROVIDER_TWITTER,
            'yt' => SocialApps::PROVIDER_GOOGLE,
            'ig' => SocialApps::PROVIDER_INSTAGRAM,
        ];

        $this->Data['titles'] = [
            'fb' => 'Facebook',
            'tw' => 'Twitter',
            'yt' => 'Google',
            'ig' => 'Instagram',
        ];

        return 'manager/brands/edit_sns_form.php';
    }
}
