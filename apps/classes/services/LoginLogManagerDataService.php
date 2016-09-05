<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class LoginLogManagerDataService extends aafwServiceBase {

	private $login_log_manager_data;

	const PC_DEVICE = 1;
	const SMARTPHONE_DEVICE = 2;

	public function __construct() {
		$this->login_log_manager_data = $this->getModel('LoginLogManagerDatas');
	}

	/**
	 * @param $brand_id
	 * @param $user_id
	 * ログイン情報の保存
	 */
	public function setLoginLog($mailAddress) {

		$date = date("Y-m-d H:i:s", time());

		$loginLogManagerData = $this->createEmptyLoginLogManager();
		$loginLogManagerData->mail_address = $mailAddress;
		$loginLogManagerData->login_date = $date;
		$loginLogManagerData->cookie = json_encode($_COOKIE);
		$loginLogManagerData->user_agent = $_SERVER['HTTP_USER_AGENT'];
		$loginLogManagerData->device = $this->getDevice();
		$loginLogManagerData->ip_address = Util::getIpAddress();

		$this->createLoginLogData($loginLogManagerData);
	}

	public function createLoginLogData($loginLogManagerData) {
		$this->login_log_manager_data->save($loginLogManagerData);
	}

	public function createEmptyLoginLogManager() {
		return $this->login_log_manager_data->createEmptyObject();
	}

	public function getDevice() {

		$device = aafwMobileDispatcher::isMobile($_SERVER);
		if($device['is_mobile'] || $device['is_smart']) {
			return self::SMARTPHONE_DEVICE;
		} else {
			return self::PC_DEVICE;
		}
	}

    public function getLastestLoginDate($mail_address) {
        $filter = array(
            'conditions' => array(
                'mail_address' => $mail_address,
            ),
        );
        $logindate = $this->login_log_manager_data->getMax('login_date',$filter);
        return $logindate;
    }

    public function getLoginCount($mail_address) {
        $filter = array(
            'conditions' => array(
                'mail_address' => $mail_address,
            ),
        );
        $countLogin = $this->login_log_manager_data->find($filter);
        return $countLogin ? $countLogin->total() : 0;
    }
}