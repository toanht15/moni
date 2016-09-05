<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.BrandOuterTokenService');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class login_outer extends BrandcoPOSTActionBase {

    protected $ContainerName = 'login_outer';
    protected $Form = array();
    public $NeedAdminLogin = false;
    public $CsrfProtect = true;

    private $brandOuterToken;

    protected $ValidatorDefinition = array(
        'password' => array(
            'required' => 1,
            'type' => 'str',
        ),
        //'token' => array(
        //    'type' => 'str',
        //    'length' => BrandOuterTokens::TOKEN_LENGTH,
        //)
    );

    public function doThisFirst() {
        // TODO: tokenは、sessionにて持ち回る？
        $this->Form = array(
            'package' => 'sns',
            'action' => 'login_outer_form?token=' . $this->POST['token'],
        );

        // Brandのdirectory_nameを取得
        $brand_service = $this->createService('BrandService');
        $brand = $brand_service->getBrandByOuterToken($this->POST['token']);
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
    }

    public function validate () {
        $token = $this->POST['token'];
        if (strlen($token) != BrandOuterTokens::TOKEN_LENGTH) {
            $this->Validator->setError('token', 'NOT_OWNER');
            return false;
        }

        $brandOuterTokenService = $this->createService('BrandOuterTokenService');
        $this->brandOuterToken = $brandOuterTokenService->getBrandOuterTokenByTokenAndPassword(
            $this->token, $this->password
        );
        if (is_null($this->brandOuterToken)) {
            $this->Validator->setError('password', 'INVITE_CERTIFICATE_FAIL');
            return false;
        }
        $this->NeedPublic = true;

        return true;
    }

    public function doAction() {
        // Loginセッション発行
        // TODO: tokenをセットするように変更
        $this->setSession('login_outer', 1);

        $package = '';
        $action = '';
        $queryParam = [
            'token' => $this->token
        ];
        if ($this->brandOuterToken->isFacebook()) {
            $package = 'facebook_outer';
            $action = 'connect';
        }
        if ($this->brandOuterToken->isTwitter()) {
            $package = 'twitter_outer';
            $action = 'connect';
            $callback_url = urlencode(Util::rewriteUrl(
                'twitter_outer',
                'connect_finish',
                [],
                ['token' => $this->POST['token']]
            ));
            $queryParam['callback_url'] = $callback_url;
        }
        if ($this->brandOuterToken->isGoogle()) {
            $package = 'google_outer';
            $action = 'connect';
        }
        if ($this->brandOuterToken->isInstagram()) {
            $package = 'instagram_outer';
            $action = 'connect';
        }
        if (!$package || !$action) {
            return false;
        }

        return 'redirect: ' . Util::rewriteUrl($package, $action, [], $queryParam);
    }
}
