<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class redirect_platform extends BrandcoGETActionBase {

    public $NeedOption = array();
    public $NeedUserLogin = true;

    private $aaidUrlList = array(
        'my_account' => '/my/account' // 会員情報
    );

    public function validate() {
        if(!$this->GET['page'] || !in_array($this->GET['page'], array_keys($this->aaidUrlList))){
            return false;
        }
        return true;
    }

    function doAction() {
        $user_service = $this->createService('UserService');
        $this->brandco_user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

        /** @var UserApplicationService $user_application_service */
        $user_application_service = $this->createService('UserApplicationService');
        $brandco_user_application = $user_application_service->getUserApplicationByUserIdAndAppId($this->brandco_user->id, $this->brand->app_id);

        /** @var RedirectPlatformService $redirect_platform_service */
        $redirect_platform_service = $this->createService('RedirectPlatformService');

        $parameters = array(
            'accessToken'    => $brandco_user_application->access_token,
            'refreshToken'   => $brandco_user_application->refresh_token,
            'platformUserId' => $this->Data['pageStatus']['userInfo']->id
        );

        $options = array(
            'brand'     => $this->brand,
            'returnUrl' => $this->getServer('HTTP_REFERER'),
            'aaidPath'  => $this->aaidUrlList[$this->GET['page']],
            'server'    => $this->SERVER
        );

        $return_url = $redirect_platform_service->getUrl($parameters,$options);
        if(!$return_url)$return_url = '404';

        return 'redirect: '. $return_url;
    }

}
