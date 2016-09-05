<?php
AAFW::import('jp.aainc.classes.brandco.auth.BrandcoServiceLoginActionBase');


class native_login extends BrandcoServiceLoginActionBase {

    private $brandco_auth_service;

    /**
     * ネィティブアプリでセットしたaccess_tokenとrefresh_tokenがCOOKIEに存在すれば、
     * それを使ってAAIDBackDoorログイン処理を実行する。
     * @return bool|string
     */
    public function doSubAction() {
        if (!$this->GET['cp_id']) return false;

        //iOS負の遺産の暫定対応
        $cp_id_str = explode('?' , $this->GET['cp_id']);
        $this->GET['cp_id'] = $cp_id_str[0];

        if(!$this->COOKIE['platformAccessToken'] || !$this->COOKIE['platformRefreshToken']){
            return 'redirect: ' . Util::rewriteUrl('','campaigns', array("cp_id" => $this->GET['cp_id']));
        }

        $access_token = $this->COOKIE['platformAccessToken'];
        $refresh_token = $this->COOKIE['platformRefreshToken'];
        $user_info = $this->getBrandcoAuthService()->getUserInfo($access_token);

        if($user_info->id<0){
            //access_tokenが失効しているので再発行
            $refresh_token_result = $this->getBrandcoAuthService()->refreshAccessToken($this->COOKIE['platformRefreshToken'], 'platform');
            $access_token = $refresh_token_result->accessToken;
            $refresh_token = $refresh_token_result->refreshToken;
            $user_info = $this->getBrandcoAuthService()->getUserInfo($access_token);
        }

        $this->setSession('accessToken', $access_token);
        $this->setSession('refreshToken', $refresh_token);
        $this->setSession('directoryName',$this->getBrand()->directory_name);
        $this->setSession('authRedirectUrl',Util::rewriteUrl('','campaigns', array("cp_id" => $this->GET['cp_id'])));

        //AAIDのログインSessionタイム間は、BackDoorLoginさせない
        if($this->getSession('nativeAAIDLoginTime')>time()){
            return 'redirect: ' . $this->getSession('authRedirectUrl');
        }

        //AAID BackdoorログインURL生成処理
        /** @var RedirectPlatformService $redirect_platform_service */
        $redirect_platform_service = $this->createService('RedirectPlatformService');

        $aaid_path = '/my/login_form/'.ApplicationService::getClientId($this->getBrand());

        $parameters = array(
            'accessToken'    => $access_token,
            'refreshToken'   => $refresh_token,
            'platformUserId' => $user_info->id
        );

        $options = array(
            'brand'     => $this->getBrand(),
            'returnUrl' => $this->getSession('authRedirectUrl'),
            'aaidPath'  => $aaid_path
        );

        setcookie("platformAccessToken", null);
        setcookie("platformRefreshToken", null);
        $this->setSession('nativeLoginFlg',1);
        $this->setSession('nativeAAIDLoginTime',time()+3600);

        $return_url = $redirect_platform_service->getUrl($parameters,$options);
        if(!$return_url)$return_url = $this->getSession('authRedirectUrl');

        return 'redirect: '. $return_url;

    }

    private function getBrandcoAuthService(){
        if(!$this->brandco_auth_service){
            $this->brandco_auth_service = $this->getService('BrandcoAuthService');
        }
        return $this->brandco_auth_service;
    }

}
