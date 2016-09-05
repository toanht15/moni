<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.core.UserSnsAccountManager');

class api_execute_sns_get_data_action extends ExecuteActionBase {

    protected $ContainerName = 'api_execute_sns_get_data_action';
    private $brand_social_account_id;

    public function doThisFirst() {
        $this->brand_social_account_id = $this->POST['brand_social_account_id'];
    }

    public function validate() {
        // TODO: validate user account
        return $this->brand_social_account_id;
    }

    public function doAction() {
        $brand_social_account_service = $this->getService('BrandSocialAccountService');
        $brand_social_account = $brand_social_account_service->getBrandSocialAccountById($this->brand_social_account_id);

        $user_service = $this->getService('UserService');
        $user = $user_service->getUserByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

        $data = array();
        if ($user) {

            /** @var UserApplicationService $user_application_service */
            $user_application_service = $this->getService('UserApplicationService');
            $user_application = $user_application_service->getUserApplicationByUserIdAndAppId($user->id, $this->Data['pageStatus']['brand']->app_id);
            if($user_application->client_id) {
                $user_sns_account_manager =
                    new UserSnsAccountManager($this->Data['pageStatus']['userInfo'], null, $this->Data['pageStatus']['brand']->app_id);
                $social_media_account_id =
                    $this->getSNSAccountId($this->Data['pageStatus']['userInfo'], 'Facebook');
                $sns_account_info = $user_sns_account_manager->getSnsAccountInfo(
                    $social_media_account_id,
                    'Facebook'
                );
                if (count($sns_account_info) > 0) {
                    $data = array(
                        'brand_social_account' => array(
                            'social_media_account_id' => $brand_social_account->social_media_account_id
                        ),
                        'user_info' => array(
                            'social_media_account_id'   => $social_media_account_id,
                            'social_media_access_token' => $sns_account_info['social_media_access_token']
                        )
                    );
                }
            }
        }

        $json_data = $this->createAjaxResponse("ok", $data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    function saveData() {}
}
