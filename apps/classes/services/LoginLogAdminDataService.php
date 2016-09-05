<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class LoginLogAdminDataService extends aafwServiceBase {

    private $login_log_admin_data;

    const PC_DEVICE = 1;
    const SMARTPHONE_DEVICE = 2;

    const LOGIN_PLATFORM = -1;
    const LOGIN_FACEBOOK = 1;
    const LOGIN_TWITTER = 3;
    const LOGIN_GOOGLE = 4;
    const LOGIN_YAHOO = 5;
    const LOGIN_GDO = 6;

    public function __construct() {
        $this->login_log_admin_data = $this->getModel('LoginLogAdminDatas');
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
        $this->login_log_admin_data->save($loginLogData);
    }

    public function createEmptyLoginLog() {
        return $this->login_log_admin_data->createEmptyObject();
    }

    public function getLoginRefererSession() {

        $loginReferer = $_SESSION['loginReferer'];

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
        if($_SESSION['clientId'] == 'fb') {
            return self::LOGIN_FACEBOOK;
        }
        if($_SESSION['clientId'] == 'tw') {
            return self::LOGIN_TWITTER;
        }
        if($_SESSION['clientId'] == 'ggl') {
            return self::LOGIN_GOOGLE;
        }
        if($_SESSION['clientId'] == 'yh') {
            return self::LOGIN_YAHOO;
        }
        if($_SESSION['clientId'] == 'gdo') {
            return self::LOGIN_GDO;
        }
        if($_SESSION['clientId'] == 'platform') {
            return self::LOGIN_PLATFORM;
        }
    }
    public function getLoginCountByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
            ),
        );
        $countBrandLogin = $this->login_log_admin_data->find($filter);
        if(!$countBrandLogin){
            return 0;
        }
        $countLoginBrandCount = $countBrandLogin->total();
        return $countLoginBrandCount;
    }

    public function getPagers($page = 1, $limit = 20, $params = array(), $order = 'login_date DESC') {
        $filter = array(
            'conditions' => $params,
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            'order' => $order,
        );
        $filter = array_merge($filter, $params);

        return $this->login_log_admin_data->find($filter);
    }

    public function getLastLoginByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
            ),
        );

        return $this->login_log_admin_data->getMax('login_date',$filter);
    }

}
