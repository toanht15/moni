<?php
class RedirectorService extends aafwServiceBase {

    private $redirector;
    private $redirector_log;

    const PC_DEVICE = 1;
    const SMARTPHONE_DEVICE = 2;

    public function __construct() {
        $this->redirector = $this->getModel('Redirectors');
        $this->redirector_log = $this->getModel('RedirectorLogs');
    }

    /**
     * @param $brand_id
     * @param $user_id
     * ログイン情報の保存
     */
    public function setLog($redirector_id, $brand_id, $user_id = null) {

        $redirectorLog = $this->createEmptyRedirectorLog();
        $redirectorLog->redirector_id = $redirector_id;
        $redirectorLog->user_id       = $user_id ? $user_id : 0;
        $redirectorLog->brand_id      = $brand_id;
        $redirectorLog->login_date    = date("Y-m-d H:i:s", time());
        $redirectorLog->user_agent    = $_SERVER['HTTP_USER_AGENT'];
        $redirectorLog->referer_url   = $_SERVER['HTTP_REFERER'];
        $redirectorLog->device        = $this->getDevice();
        $redirectorLog->ip_address    = Util::getIpAddress();

        $this->createRedirectorLogData($redirectorLog);
    }

    public function createRedirectorLogData($redirectorLog) {
        $this->redirector_log->save($redirectorLog);
    }

    public function createEmptyRedirectorLog() {
        return $this->redirector_log->createEmptyObject();
    }

    public function getDevice() {

        if(Util::isSmartPhone()) {
            return self::SMARTPHONE_DEVICE;
        } else {
            return self::PC_DEVICE;
        }
    }

    public function redirectTo($sign, $brand_id, $user_id = null) {
        $redirector = $this->redirector->findOne(array('sign' => $sign, 'brand_id' => $brand_id));
        if($redirector == null) {
            return false;
        }
        $this->setLog($redirector->id, $brand_id, $user_id);
        $url = $redirector->url;
        if($user_id) {

            AAFW::import ( 'jp.aainc.classes.clients.UtilityApiClient' );
            AAFW::import('jp.aainc.services.UserService');

            $service_factory = new aafwServiceFactory();

            /** @var UserService $user_service */
            $user_service = $service_factory->create('UserService');
            $user = $user_service->getUserByBrandcoUserId($user_id);

            $token = UtilityApiClient::getInstance()->getUserToken(UtilityApiClient::TRACKER, $user->monipla_user_id);
            $query  = parse_url($url, PHP_URL_QUERY);
            $token_param_name = '_mp_uid';
            if ($brand_id == 349) {
                //  オピニオンワールド様は、パラメータ名称を変更する
                $token_param_name = 'aff_sub';
            }
            if($query) {
                $url = "${url}&${token_param_name}=${token}";
            } else{
                // https://aainc01.backlog.jp/view/MONIPLA_OP-566
                // 上記の問い合わせで、redirectできないケースが発生したので
                // 末尾の/付与を中止する
                //$url = rtrim($url, '/') . '/';
                $url = "${url}?${token_param_name}=${token}";
            }
        }

        return $url;
    }

    /**
     * @param $brand_id
     * @return aafwEntityContainer|array
     */
    public function getRedirectorsByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id
            ),
            'order' => array(
                'direction' => 'desc',
                'name'      => 'id'
            )
        );
        return $this->redirector->find($filter);
    }

    /**
     * @param $id
     * @return entity
     */
    public function getRedirectorById($id) {
        return $this->redirector->findOne(array('id' => $id));
    }

    /**
     * @param $sign
     * @param $brand_id
     * @return entity
     */
    public function getRedirectorBySignAndBrandId($sign, $brand_id) {
        $filter = array(
            'conditions' => array(
                'sign' => $sign,
                'brand_id' => $brand_id
            )
        );
        return $this->redirector->findOne($filter);
    }

    /**
     * @param $brand_id
     * @param $post
     * @return mixed
     */
    public function createRedirector($brand_id, $post) {
        $redirector = $this->redirector->createEmptyObject();
        $redirector->brand_id = $brand_id;
        $redirector->sign = $post['sign'];
        $redirector->url = $post['url'];
        $redirector->description = $post['description'] ? $post['description'] : '';
        return $this->redirector->save($redirector);
    }

    /**
     * @param $id
     * @param $post
     * @return mixed
     */
    public function updateRedirector($id, $post) {
        $redirector = $this->getRedirectorById($id);
        $redirector->sign = $post['sign'];
        $redirector->url = $post['url'];
        $redirector->description = $post['description'] ? $post['description'] : '';
        return $this->redirector->save($redirector);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRedirector($id) {
        $redirector = $this->getRedirectorById($id);
        if (!$redirector) {
            return null;
        }
        $redirector->del_flg = 1;

        return $this->redirector->save($redirector);
    }
}
