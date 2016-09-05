<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class UserApplicationService extends aafwServiceBase
{
    protected $user;
    protected $user_application;

    public function __construct() {
        $this->user = $this->getModel("Users");
        $this->user_application = $this->getModel("UserApplications");
    }

    public function createUserApplication($userApplication) {
        $this->user_application->save($userApplication);
    }

    public function createEmptyUserApplication() {
        return $this->user_application->createEmptyObject();
    }

    /**
     * @param $monipla_user_id
     * @return mixed
     */
    public function getUserByMoniplaUserId($monipla_user_id) {
        $filter = array(
            'monipla_user_id' => $monipla_user_id,
        );
        return $this->user->findOne($filter);
    }

    /**
     * @param $user_id
     * @param $app_id
     * @return mixed
     */
    public function getUserApplicationByUserIdAndAppId($user_id, $app_id) {
        $filter = array(
            'user_id' => $user_id,
            'app_id' => $app_id,
        );
        return $this->user_application->findOne($filter);
    }

    /**
     * @param $user_id
     * @param $app_id
     * @return mixed
     */
    public function createOrUpdateUserApplication($user_id, $app_id, $access_token, $refresh_token, $client_id) {
        $user_application = $this->getUserApplicationByUserIdAndAppId($user_id, $app_id);

        if(!$user_application) {
            $user_application = $this->createEmptyUserApplication();
            $user_application->user_id = $user_id;
            $user_application->app_id = $app_id;
        }
        $user_application->access_token     = $access_token;
        $user_application->refresh_token    = $refresh_token;
        $user_application->client_id        = $client_id;
        $user_application->token_update_at  = date("Y-m-d H:i:s", time());
        $this->createUserApplication($user_application);

    }




    /**
     * @param $monipla_user_id
     * @param $app_id
     * @return mixed
     */
    public function getUserApplicationByMoniplaUserIdAndAppId($monipla_user_id, $app_id) {
        $user = $this->getUserByMoniplaUserId($monipla_user_id);
        return $this->getUserApplicationByUserIdAndAppId($user->id, $app_id);
    }

    public function UpdateUserApplication($user_application) {
        return $this->user_application->save($user_application);
    }
}