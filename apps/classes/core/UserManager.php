<?php
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
AAFW::import('jp.aainc.classes.services.UserService');

/**
 * CoreのUsersを操作するクラス
 * Class UserManager
 */
class UserManager {
    private $userInfo    = null;
    private $moniplaCore = null;
    const SCOPES = 'R_PROFILE';

    const OPT_OUT = 0;
    const OPT_IN = 1;

    public function __construct($userInfo, $moniplaCore = null) {
        $this->userInfo = $userInfo;
        if ( $moniplaCore == null) $moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
        $this->moniplaCore = $moniplaCore;
        $this->UserService = UserService::getInstance();

        $this->initUserInfo();
    }

    /**
     * @param $value
     * @return array
     */
    public function changeName($value) {
        try {
            $this->moniplaCore->changeName(array(
                'class' => 'Thrift_UserQuery',
                'fields' => array('id' => $this->userInfo->id, 'name' => $value)
            ));
            $result = array('mid' => 'changed');
            return $result;
        } catch(Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('UserManager#changeName Error.' . $e);
        }

        return array();
    }

    /**
     * @param $value
     * @return array
     */
    public function changeMailAddress($value) {
        try {
            if (!$this->checkExistMailAddress($value)) {
                $this->moniplaCore->changeMailAddressByUser(array(
                    'class' => 'Thrift_UserQuery',
                    'fields' => array('id' => $this->userInfo->id, 'mailAddress' => $value)
                ));

                return array('mid' => 'changed');
            } else {
                return array('mid' => 'unchanged');
            }
        } catch(Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('UserManager#changeMailAddress Error.' . $e);
        }

        return array();
    }

    /**
     * @param $mail_address
     * @return bool
     */
    public function checkExistMailAddress($mail_address) {
//        if ($mail_address === null) {
//            throw new Exception("mail address is null!");
//        }
        $result = $this->moniplaCore->getUserByQuery(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'mailAddress' => $mail_address,
            )));

        return $result->result->status == Thrift_APIStatus::SUCCESS && $result->id > 0;
    }

    /**
     * @param $value
     * @return array
     */
    public function resetPassword($value) {
        try {
            // TODO:パスワード妥当性チェック
            $this->moniplaCore->resetPassword(array(
                'class' => 'Thrift_ResetPasswordParameter',
                'fields' => array('id' => $this->userInfo->id, 'newPassword' => $value)
            ));
            $result = array('mid' => 'changed');
            return $result;
        } catch(Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('UserManager#resetPassword Error.' . $e);
        }
    }

    /**
     * サインアップの時に、メールアドレスの入力が必要であるかどうか判定するメソッド
     * @param $social_accounts
     */
    public function getMailAddress() {
        try {
            if($this->userInfo->mailAddress) {
                return $this->userInfo->mailAddress;
            }
            if(is_array($this->userInfo->socialAccounts)) {
                foreach ($this->userInfo->socialAccounts as $socialAccount) {
                    if($socialAccount->mailAddress) {
                        return $socialAccount->mailAddress;
                    }
                }
            }

            return false;
        } catch(Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('UserManager#getMailAddress Error.' . $e);
        }
    }

    public function isMailAddressRequired() {
        return Util::isNullOrEmpty($this->userInfo->mailAddress);
    }

    public function getMailAddressCandidate() {
        if(!is_array($this->userInfo->socialAccounts)) {
            return "";
        }
        foreach ($this->userInfo->socialAccounts as $mailAccount) {
            return $mailAccount->mailAddress;
        }
        return "";
    }

    /**
     * @param $platform_user_id
     * @return mixed
     */
    public function createAuthorizationCode() {
        //ここはBRANDCo側限定で叩かれるためclient_idを固定にしている
        return \Monipla\Core\MoniplaCore::getInstance()->createAuthorizationCode(array(
            'class' => 'Thrift_CreateCodeParameter',
            'fields' => array(
                'clientId' => 'brandco',
                'userId' => $this->userInfo->id,
                'scopes' => self::SCOPES
            )
        ));
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getMpAccessToken($code) {
        //ここはBRANDCo側限定で叩かれるためclient_idを固定にしている
        return \Monipla\Core\MoniplaCore::getInstance()->createAccessToken(array(
            'class' => 'Thrift_AuthorizationCodeParameter',
            'fields' => array(
                'clientId' => 'brandco',
                'code' => $code,
            )));
    }

    /**
     * @param $access_token
     * @return mixed
     */
    public function getUser($access_token){
        if ($access_token === null) {
            throw new Exception("access token is null!");
        }
        return \Monipla\Core\MoniplaCore::getInstance()->getUser(array(
            'class' => 'Thrift_AccessTokenParameter',
            'fields' => array(
                'accessToken' => $access_token
            ),
        ));
    }

    public function getUserByQuery($pl_user_id) {
        //if ($pl_user_id === null) {
        //    throw new Exception("pl_user_id is null!");
        //}
        return $this->moniplaCore->getUserByQuery(array(
            'class' => 'Thrift_UserQuery',
            'fields' => array(
                'socialMediaType' => 'Platform',
                'socialMediaAccountID' => $pl_user_id,
            )));
    }

    public function initUserInfo() {
        $this->setUserMailAddress();
    }

    public function setUserMailAddress() {
        if ($this->userInfo->id && !$this->userInfo->mailAddress) {
            $user = $this->getUserByQuery($this->userInfo->id);
            $this->userInfo->mailAddress = $user->mailAddress;
        }
    }
}
