<?php
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.MailManager');
class ManagerService extends aafwServiceBase {

	protected $manager;

	//ログイン失敗可能回数
	const LOGIN_INVALID_MAX_COUNT = 3;
	//失敗回数リセット期間
	const LOGIN_TRY_INTERVAL = '30 minute';
	//ロックアウト期間
	const LOGIN_LOCKOUT_INTERVAL = '120 minute';
	//パスワード変更期間
	const PASSWORD_CHANGE_INTERVAL = '4 month';

	//管理者登録
	const ADD_FINISH = 1; // 正常終了
	const ADD_ERROR = 2; // エラー終了

    //ログインエラー
    const LOGIN_INVALID = 1;
    const LOGIN_INVALID_MAX = 2;   //アカウントロック中

	//パスワード変更
	const CHANGE_FINISH = 1; // 正常終了
	const CHANGE_ERROR = 2; // エラー終了
	const CHANGE_REQUIRED = 3; // 期限切れで変更必要
    const ACCOUNT_ERROR = 4;    //アカウントエラー終了
    
    const TOKEN_KEY = '_mlt'; // Manager Login Token
    const TOKEN_EXPIRE_TIME = 15;
        
	public function __construct() {
		$this->manager = $this->getModel('Managers');
	}

	/**
	 * @param $username
	 * @param $email
	 * @param $password
	 * 管理者の保存
	 */
	public function setManager($ManagerInfo) {

		$addManager = $this->createEmptyManager();
		$addManager->name = $ManagerInfo['username'];
		$addManager->mail_address = $ManagerInfo['email'];
		$addManager->mail_address_hash = md5($ManagerInfo['email']);
		$addManager->password = md5($ManagerInfo['password']);
		$addManager->pw_register_date = date("Y-m-d H:i:s", time());;
		$addManager->pw_expire_date = date('Y-m-d H:i:s' ,  strtotime( '+' . self::PASSWORD_CHANGE_INTERVAL ));

		$this->saveManagerInfo($addManager);
	}

	public function saveManagerInfo($saveManagerInfo) {
		$this->manager->save($saveManagerInfo);
	}

	public function createEmptyManager() {
		return $this->manager->createEmptyObject();
	}

	/**
	 * @param $email
	 * メールアドレスから管理者のアカウント情報取得
	 */
	public function getManagerAccount($email) {

		$filter = array(
			'conditions' => array(
				'mail_address' => $email,
			),
		);

		$managerAccount = $this->manager->findOne($filter);
		return $managerAccount;
	}

    public function getManagerAccounts($page = 1, $limit = 20, $params = array(), $order = null) {
        $filter = array(
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            'order' => $order,
        );

        return $this->manager->find($filter);
    }

    public function getManagers($order = null) {
        $filter = array();
        if ($order) {
            $filter['order'] = $order;
        }

        return $this->manager->find($filter);
    }

    /**
     * @param $emailhash
     * メールアドレスのハッシュ値から管理者のアカウント情報取得
     */
    public function getManagerFromHash($emailhash) {

        $filter = array(
            'conditions' => array(
                'mail_address_hash' => $emailhash,
            ),
        );

        $managerAccountHash = $this->manager->findOne($filter);
        return $managerAccountHash;
    }

    /**
     * @param $email
     * @param $password
     * 管理者のアカウントが一致するか判定
     * true:一致する false:一致しない
     */
    public function isManager($email, $password) {
    
        $filter = array(
            'conditions' => array(
                'mail_address' => $email,
                'password' => md5($password),
            ),
        );
    
        $matchAccount = $this->manager->findOne($filter) ? true : false;
        return $matchAccount;
    }

    /**
     * アカウントロック情報リセット
     */
    public function resetAccountLock($mailMatchAccount) {
        $mailMatchAccount->login_invalid_count = 0;
        $mailMatchAccount->login_try_reset_date = "0000-00-00 00:00:00";
        $mailMatchAccount->login_lockout_reset_date = "0000-00-00 00:00:00";
        $this->saveManagerInfo($mailMatchAccount);
    }

    /**
     * ログイントライカウントチェック
     * @param $manager_account
     */
    public function incrementLoginTryCount($mailMatchAccount, $loginTryResetDate, $loginLockoutResetDate) {

        //カウントアップ
        $mailMatchAccount->login_invalid_count += 1;
        $mailMatchAccount->login_try_reset_date = $loginTryResetDate;
        
        //カウントMAXチェック
        if($mailMatchAccount->login_invalid_count >= self::LOGIN_INVALID_MAX_COUNT) {
            $mailMatchAccount->login_lockout_reset_date = $loginLockoutResetDate;

            $token_arry = array('id' => $mailMatchAccount->id, 'date' => $mailMatchAccount->login_try_reset_date);
            $token = json_encode($token_arry);
            $encoded_token = base64_encode($token);

            $mailParams['USER_NAME'] = $mailMatchAccount->name;
            $mailParams['RESET_PASS_URL'] = config('Protocol.Secure')."://".config('Domain.brandco_manager')."/account/reset_password_form?token={$encoded_token}";
            $mail = new MailManager(array('BccSend'=>false));
            $mail->loadMailContent('account_lockout_mail_manager');
            $mail->sendNow($mailMatchAccount->mail_address, $mailParams);
        }
        $this->saveManagerInfo($mailMatchAccount);
    }

    public function countManagers(){

        return $this->manager->count(array());
    }

    /**
     * アカウントロック中かどうか
     * @param $mailMatchAccount
     */
    public function isAccountLock($mailMatchAccount) {
        return ($mailMatchAccount->login_lockout_reset_date != '0000-00-00 00:00:00') && date('Y-m-d H:i:s') < $mailMatchAccount->login_lockout_reset_date ;
    }

    /**
     * アカウントロック中かどうか
     * @param $managerAccount,$newPassword
     */
    public function changeManagerPass($managerAccount, $newPassword) {

        $managerAccount->password = $newPassword;
        $managerAccount->pw_register_date = date('Y-m-d H:i:s');
        $managerAccount->pw_expire_date = date('Y-m-d H:i:s' ,  strtotime( self::PASSWORD_CHANGE_INTERVAL ));
        $managerAccount->pw_expire_mail_send_flg = 0;

        $this->saveManagerInfo($managerAccount);
    }

    public function changeManagerName($managerAccount, $newName) {
        $managerAccount->name = $newName;

        $this->saveManagerInfo($managerAccount);
    }

    public function getManagerById($id) {
        $filter = array(
            'id' => $id,
        );
        return $this->manager->findOne($filter);
    }

    public static function generateOnetimeToken($managerUserId) {
        $token_generator = new TokenWithoutSimilarCharGenerator();
        $token = $token_generator->generateToken(64);

        $redis = CacheManager::getRedis();
        $redis->multi(Redis::PIPELINE);
        $redis->set(self::generateKey($token), $managerUserId);
        $redis->expire(self::generateKey($token), self::TOKEN_EXPIRE_TIME);
        $redis->exec();

        return self::TOKEN_KEY . '=' .$token;

    }

    public static function verifyOnetimeToken($token) {
        $redis = CacheManager::getRedis();
        $redis->multi();
        $redis->get(self::generateKey($token));
        $redis->del(self::generateKey($token));
        $result = $redis->exec();
        $managerUserId = $result[0];

        return $managerUserId;
    }

    private static function generateKey($token) {
        return self::TOKEN_KEY . ':' . $token;
    }

    /**
     * @return bool
     */
    public function isAgentLogin() {
        $managerUserId = $_SESSION['managerUserId'];
        if (!$managerUserId) {
            return false;
        }
        $managerAccount = $this->getManagerFromHash($managerUserId);
        return $managerAccount->authority == Manager::AGENT;
    }
}
