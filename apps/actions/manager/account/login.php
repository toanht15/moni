<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
class login extends BrandcoManagerPOSTActionBase {

    protected $manager_account;
    protected $mailMatchAccount;

    protected $ContainerName = 'login';
    protected $Form = array (
        'package' => 'account',
        'action' => 'index',
    );
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'email' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
            'validator' => array('MailAddress')
        ),
        'password' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 32,
            'validator' => array('AlnumSymbol')
        ),
    );

    public function beforeValidate(){
        $this->manager_account = $this->createService('ManagerService');
        if($this->email){
            //メールアドレスが一致する管理者のアカウント情報取得
            $this->mailMatchAccount = $this->manager_account->getManagerAccount($this->email);
        }
        //アカウントロック日付設定
        $this->setAccountLockDateTime();
    }

    public function validate() {
        if (Util::isAcceptRemote() && !Util::isPersonalMachine()) return 404;
        return true;
    }

    public function doAction() {
        $isManager = $this->manager_account->isManager($this->email,$this->password);

        //ロック中の場合はログインエラー
        if( $this->manager_account->isAccountLock($this->mailMatchAccount)) {

            return 'redirect:' . Util::rewriteUrl('account', 'index', array(), array('login_err' => ManagerService::LOGIN_INVALID_MAX), '', true);
        }

        //単純に一致するアカウントがない
        if(!$isManager){
            $this->doAccountLockCheck($this->mailMatchAccount);
            $login_invalid_count = $this->mailMatchAccount->login_invalid_count;

            if ($login_invalid_count >= ManagerService::LOGIN_INVALID_MAX_COUNT) {
                return 'redirect:' . Util::rewriteUrl('account', 'index', array(), array('login_err' => ManagerService::LOGIN_INVALID_MAX), '', true);
            }

            return 'redirect:' . Util::rewriteUrl('account', 'index', array(), array('login_err' => ManagerService::LOGIN_INVALID), '', true);
        }

        //チェックを通ったらセッション登録
        $this->setLoginSession($this->mailMatchAccount->mail_address);

        //ログインOKの場合はリセット
        $this->manager_account->resetAccountLock($this->mailMatchAccount);

        //ログインログの記録
        $login_log_manager_service = $this->createService('LoginLogManagerDataService');
        $login_log_manager_service->setLoginLog($this->mailMatchAccount->mail_address);

        $this->Data['saved'] = 1;

        //パスワード有効期限チェック
        if( date('Y-m-d H:i:s') > $this->mailMatchAccount->pw_expire_date ){
            // パスワード有効期限切れのページへ
            return 'redirect: ' . Util::rewriteUrl('dashboard', 'change_password_form', array(), array('mode' => ManagerService::CHANGE_REQUIRED), '', true);
        }
        return 'redirect: ' . Util::rewriteUrl('dashboard', 'index', array(), array(), '', true);
    }

    /**
     * アカウントロックチェック
     * ロックしていない場合は失敗回数のカウントアップをする
     * @param $mailMatchAccount
     */
    private function doAccountLockCheck($mailMatchAccount) {
        if(!$mailMatchAccount->mail_address) return;

        //インターバルチェック ロックされる前にトライリセット日付を経過していたらリセット
        if(!$this->isNullResetDate($mailMatchAccount->login_try_reset_date) && $this->nowDateTime > $mailMatchAccount->login_try_reset_date && $this->isNullResetDate($mailMatchAccount->login_lockout_reset_date)){
            $this->manager_account->resetAccountLock($mailMatchAccount);
        }

        //ロックアウトリセット日時が未入力の場合は、ロックしていない為カウントアップ
        if($this->isNullResetDate($mailMatchAccount->login_lockout_reset_date)) {
            $this->manager_account->incrementLoginTryCount($mailMatchAccount, $this->loginTryResetDate, $this->loginLockoutResetDate);
        } else {
            //ロックアウトリセット日時が入力されている場合は、ロック中の為日時チェック
            if($this->nowDateTime < $mailMatchAccount->login_lockout_reset_date) {
                return; //まだロックアウト中でこれ以上カウントアップする必要なし
            } else {
                //ロックアウト期間が終了したため、一旦ロック解除する
                $this->manager_account->resetAccountLock($mailMatchAccount);
                $this->manager_account->incrementLoginTryCount($mailMatchAccount, $this->loginTryResetDate, $this->loginLockoutResetDate);
            }
        }
    }

    /**
     * アカウントロック日時設定
     */
    private function setAccountLockDateTime(){
        //日時設定
        $this->nowDateTime = date('Y-m-d H:i:s');
        $this->loginTryResetDate = date('Y-m-d H:i:s' , strtotime( '+' . ManagerService::LOGIN_TRY_INTERVAL ));
        $this->loginLockoutResetDate = date('Y-m-d H:i:s' , strtotime( '+' . ManagerService::LOGIN_LOCKOUT_INTERVAL ));
    }

    /**
     * ログイン情報をのセッションに登録
     * @param $mailAddress
     */
    private function setLoginSession($mailAddress) {

        $this->setSession('managerUserId',md5($mailAddress));
    }

    /**
     * リセット日付に値がセットされていないか判定
     * @param $loginTryResetDate
     */
    private function isNullResetDate($loginTryResetDate) {
        return strtotime($loginTryResetDate) == strtotime("0000-00-00 00:00:00");
    }

}