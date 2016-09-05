<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.ApplicationService');

class BrandcoAuthService extends aafwServiceBase {
    private $monipla_core;

    public function __construct($monipla_core) {
        $this->monipla_core = $monipla_core;
    }

    public function getMoniplaCore() {
        if ( $this->monipla_core == null ) {
            AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
            $this->monipla_core = \Monipla\Core\MoniplaCore::getInstance();
        }

        return $this->monipla_core;
    }

    public function castSocialAccounts($userInfo) {
        if ($userInfo->result->status != Thrift_APIStatus::SUCCESS) return array();
        if ($userInfo->socialAccounts) {
            $userInfo->socialAccounts = array_map (function($elm){ return (object)((array)$elm); }, $userInfo->socialAccounts );
        } else {
            $userInfo->socialAccounts = array();
        }
        return (array) $userInfo;
    }

    public function getRandomString($length = 8) {
        return substr(base_convert(md5(uniqid()), 16, 36), 0, $length);
    }

    public function getUserInfo($accessToken) {
        if ($accessToken === null) {
            throw new Exception("parameter 'accessToken' must not be null!");
        }
        return $this->getMoniplaCore()->getUser(array(
            'class' => 'Thrift_AccessTokenParameter',
            'fields' => array(
                'accessToken' => $accessToken
            )
        ));
    }

    public function getUserInfoByQuery($monipla_user_id) {
        return $this->getMoniplaCore()->getUserByQuery(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'socialMediaType' => 'Platform',
                'socialMediaAccountID' => $monipla_user_id,
            )
        ));
    }

    public function createAccessToken($clientId = ApplicationService::CLIENT_ID_PLATFORM, $code) {
        return $this->getMoniplaCore()->createAccessToken(array(
            'class' => 'Thrift_AuthorizationCodeParameter',
            'fields' => array(
                'clientId' => $clientId,
                'code' => $code
            )
        ));
    }

    /**
     * @param $mail_address
     * @return mixed|string
     */
    public function createNickName($mail_address) {
        $account = preg_replace('#@.+$#', '', $mail_address);
        $params = preg_split('#[-_+.]#', $account);

        $user_name = $account;
        if (count($params) === 1) {
            $user_name = substr($user_name, 2);
        } else {
            $user_name = $params[0] . '_' . $params[count($params) - 1];
        }

        return $user_name;
    }

    public function entryUser($mail_address, $password) {
        $user_name = $this->createNickName($mail_address);
        return $this->getMoniplaCore()->entryUser(array(
            'class'  => 'Thrift_UserEntrySheet',
            'fields' => array(
                'name'        => $user_name,
                'mailAddress' => $mail_address,
                'password'  => $password
            )
        ));
    }

    public function checkAccount($mail_address, $password) {
        return $this->getMoniplaCore()->checkAccount(array (
            'class' => 'Thrift_AccountCheckParameter',
            'fields' => array (
                'mailAddress' => $mail_address,
                'password'  => $password
            )
        ));
    }

    public function createAuthorizationCode($user_id, $client_id = ApplicationService::CLIENT_ID_PLATFORM, $scopes = 'FULL_CONTROL') {
        return $this->getMoniplaCore()->createAuthorizationCode(array(
            'class' => 'Thrift_CreateCodeParameter',
            'fields' => array(
                'clientId' => $client_id,
                'userId' => $user_id,
                'scopes' => $scopes
            )
        ));
    }

    public function getUsersByMailAddress($mail_address) {
        return $this->getMoniplaCore()->getUsersByMailAddress(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'mailAddress' => $mail_address
            )
        ));
    }

    public function getSNSAccessToken($access_token,$socialMediaType = 'Facebook') {
        return $this->getMoniplaCore()->getSNSAccessToken(array(
            'class' => 'Thrift_SocialAccessTokenQuery',
            'fields' => array(
                'accessToken' => $access_token,
                'socialMediaType' => $socialMediaType
            )
        ));
    }

    public function refreshAccessToken($refresh_token, $client_id) {
        return $this->getMoniplaCore()->refreshAccessToken(array(
            'class' => 'Thrift_RefreshTokenParameter',
            'fields' => array(
                'refreshToken' => $refresh_token,
                'clientId' => $client_id
            )
        ));
    }

    public function setOptin($userId, $optin) {
        return $this->getMoniplaCore()->setOptin(array(
            'class' => 'Thrift_Optin',
            'fields' => array(
                'userId' => $userId,
                'optin' => $optin
            )
        ));
    }
}