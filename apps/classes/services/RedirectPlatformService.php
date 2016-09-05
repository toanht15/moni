<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class RedirectPlatformService  extends aafwServiceBase{

    private $accessToken;
    private $refreshToken;
    private $platformUserId;
    private $scope;
    private $moniplaCore;
    private $server;

    const LOGIN_BY_LOGINTOKEN_PATH = "/my/login_by_login_token";

    /**
     * coreへのaccess token
     * @param $access_token
     */
    private function setAccessToken($accessToken){
        $this->accessToken = $accessToken;
    }

    private  function getAccessToken(){
        return $this->accessToken;
    }

    /**
     * coreへのrefresh token
     * @param $refresh_token
     */
    private function setRefreshToken($refreshToken){
        $this->refreshToken = $refreshToken;
    }

    private function getRefreshToken(){
        return $this->refreshToken;
    }

    private function setPlatformUserId($platformUserId){
        $this->platformUserId = $platformUserId;
    }

    private function getPlatformUserId(){
        return $this->platformUserId;
    }

    public function setScope($scope){
        $this->scope = $scope;
    }

    private function getScope(){
        if(!$this->scope){
            return 'FULL_CONTROL';
        }
        return $this->scope;
    }

    /**
     * アクセスするAAIDのURLパス
     * @return string
     */
    private function getAaidUrl($path){
        return Util::getHttpProtocol () . "://" . config('Domain.aaid') . $path;
    }

    private function getClientId($brand){
        if(!$brand){
            return ApplicationService::$ApplicationMaster[ApplicationService::BRANDCO]['client_id'];
        }
        return ApplicationService::getClientId($brand);
    }

    /**
     * 戻り先がない場合はTOPのURLを返す
     */
    private function getReturnUrl($url){
        if(!$url){
            return Util::getBaseUrl();
        }
        return $url;
    }

    private function setServer($server){
        $this->server = $server;
    }

    private function getServerValue($key){
        return $this->server[$key];
    }

    /**
     * @param $parameters
     *  accessToken
     *  refreshToken
     *  platformUserId
     *
     * @param array $options
     *  scope
     *  server
     *  brand
     *  returnUrl:AAIDのヘッダーに表示したいモニプラの戻り先URL
     * @return bool|string
     */
    public function getUrl($parameters,$options=array()){

        if(!$parameters['accessToken'] || !$parameters['refreshToken'] || !$parameters['platformUserId']){
            return false;
        }

        try{
            $this->setAccessToken($parameters['accessToken']);
            $this->setRefreshToken($parameters['refreshToken']);
            $this->setPlatformUserId($parameters['platformUserId']);

            $this->setScope($options['scope']);
            $this->setServer($options['server']);

            $params['returnApp']    = $this->getClientId($options['brand']);
            $params['returnUrl']    = $this->getReturnUrl($options['returnUrl']);
            $params['redirectUrl']  = $this->getAaidUrl($options['aaidPath']);

            $params['redirectUrl']  = $this->appendQueryString($params['redirectUrl']);
            $params['redirectUrl']  = $this->appendBackdoorLogin($params['redirectUrl']);

            return Util::getHttpProtocol() . "://" . config('Domain.aaid') . '/redirect_platform?' . http_build_query($params);
        }catch(Exception $e){
            aafwLog4phpLogger::getDefaultLogger()->error('RedirectPlatformService #getUrl Error.' . $e);
            return false;
        }


    }
    public function getUrlLoginByLoginToken($parameters,$options=array()){
        $options['aaidPath'] = self::LOGIN_BY_LOGINTOKEN_PATH;
        return $this->getUrl($parameters,$options);
    }


    private function appendQueryString($redirectUrl) {
        $queryString =  $this->getServerValue('QUERY_STRING');
        if (!$queryString) return $redirectUrl;
        $querySign = parse_url($redirectUrl, PHP_URL_QUERY) ? '&' : '?';
        return $redirectUrl . $querySign . $queryString;
    }

    /**
     * プラットフォームへのBackdoorログインを実行しTokenを返す
     */
    private function appendBackdoorLogin($redirectUrl) {

        $params = array();
        $params['class'] = 'Thrift_ExchangeAccessTokenParameter';
        $params['fields']['socialMediaType']    = 'Platform';
        $params['fields']['clientId']           = $this->getClientId();
        $params['fields']['snsAccessToken']     = $this->getAccessToken();
        $params['fields']['snsRefreshToken']    = $this->getRefreshToken();
        $params['fields']['snsUserId']          = $this->getPlatformUserId();
        $params['fields']['scopes']             = $this->getScope();

        $tokenResult = $this->getMoniplaCore()->backdoorLogin($params);

        if(!$tokenResult->token){
            throw new Exception('CoreのBackdoorLoginのエラー');
        }

        $querySign = parse_url($redirectUrl, PHP_URL_QUERY) ? '&' : '?';

        return $redirectUrl . $querySign . 'loginToken=' . $tokenResult->token;
    }

    public function getMoniplaCore () {
        if ( $this->moniplaCore == null ) {
            AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
            $this->moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        }
        return $this->moniplaCore;
    }

}