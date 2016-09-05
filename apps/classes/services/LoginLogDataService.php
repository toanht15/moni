<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class LoginLogDataService extends aafwServiceBase {

	private $login_log_data;

	const PC_DEVICE = 1;
	const SMARTPHONE_DEVICE = 2;

    const LOGIN_PLATFORM  = -1;
    const LOGIN_FACEBOOK  = 1;
    const LOGIN_TWITTER   = 3;
    const LOGIN_GOOGLE    = 4;
    const LOGIN_YAHOO     = 5;
    const LOGIN_GDO       = 6;
    const LOGIN_INSTAGRAM = 7;
    const LOGIN_LINE      = 8;

    private $login_types = array(
        'platform' => self::LOGIN_PLATFORM,
        'fb'       => self::LOGIN_FACEBOOK,
        'tw'       => self::LOGIN_TWITTER,
        'ggl'      => self::LOGIN_GOOGLE,
        'yh'       => self::LOGIN_YAHOO,
        'gdo'      => self::LOGIN_GDO,
        'insta'    => self::LOGIN_INSTAGRAM,
        'line'     => self::LOGIN_LINE
    );

	public function __construct() {
		$this->login_log_data = $this->getModel('LoginLogDatas');
	}

	/**
	 * @param $brand_id
	 * @param $user_id
	 * ログイン情報の保存
	 */
	public function setLoginLog($brand_id, $user_id) {

		$date = date("Y-m-d H:i:s", time());

		$loginLogData = $this->createEmptyLoginLog();
		$loginLogData->user_id = $user_id;
		$loginLogData->brand_id = $brand_id;
		$loginLogData->login_date = $date;
		$loginLogData->cookie = json_encode($_COOKIE);
		$loginLogData->user_agent = $_SERVER['HTTP_USER_AGENT'];
		$loginLogData->referer_url = $this->getLoginRefererSession();
		$loginLogData->device = $this->getDevice();
		$loginLogData->login_type = $this->getLoginType();
		$loginLogData->ip_address = Util::getIpAddress();

		$this->createLoginLogData($loginLogData);
	}

	public function createLoginLogData($loginLogData) {
		$this->login_log_data->save($loginLogData);
	}

	public function createEmptyLoginLog() {
		return $this->login_log_data->createEmptyObject();
	}

	public function getLoginRefererSession() {

		$loginReferer = $_SESSION['loginReferer'];
		unset($_SESSION['loginReferer']);

		return $loginReferer;
	}

	public function getDevice() {

		$device = aafwMobileDispatcher::isMobile($_SERVER);
		if($device['is_mobile'] || $device['is_smart']) {
			return self::SMARTPHONE_DEVICE;
		} else {
			return self::PC_DEVICE;
		}
	}

    public function getLoginType() {
        return $this->login_types[$_SESSION['clientId']];
    }

}