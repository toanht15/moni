<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');

class create_sns_outer_url extends BrandcoManagerPOSTActionBase {
    protected $AllowContent = array('JSON');
    protected $ContainerName = 'create_sns_outer_url';
    public $NeedManagerLogin = true;
    public $CsrfProtect = false;

    public function validate () {
        if (!is_numeric($this->POST['brand_id']) ||
            !is_numeric($this->POST['social_app_id']) ||
            !is_numeric($this->POST['user_id'])) {
            $error = 'Parameter Validation Error!';
            $json_data = $this->createAjaxResponse('ng', $error);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function doAction() {
        $tokenService = $this->createService('BrandOuterTokenService');
        $brandService = $this->createService('BrandService');

        $brand = $brandService->getBrandById($this->brand_id);

        $brandOuterToken = $tokenService->create(
            $brand,
            $this->POST['social_app_id'],
            $this->POST['user_id']
        );

        $baseUrl = config('Protocol.Secure') . '://' . config('Domain.brandco') . '/';
        $url = Util::rewriteUrl(
            'sns',
            'login_outer_form',
            [],
            ['token' => $brandOuterToken->token],
            $baseUrl,
            true
        );

        $json_data = $this->createAjaxResponse(
            'ok',
            [
                'url' => $url,
                'password' => $brandOuterToken->password
            ]
        );
        $this->assign('json_data', $json_data);

        return 'dummy.php';
    }
}
