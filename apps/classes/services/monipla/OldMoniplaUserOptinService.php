<?php
AAFW::import('jp.aainc.aafw.net.aafwCurlRequestBase');

class OldMoniplaUserOptinService extends aafwCurlRequestBase {
    const CLASS_GET_OR_CREATE = 'get_or_create_opt_in';
    const CLASS_UPDATE = 'update_opt_in';

    const FROM_ID_SIGN_UP_BRAND = 1; // ブランドから新規登録時のチェックボックス
    const FROM_ID_SIGN_UP_CP    = 2; // CPから新規登録時のチェックボックス
    const FROM_ID_JOINED_CP     = 3; // CP参加完了後の導線テンプレート
    const FROM_ID_MGR           = 4; // マネージャ画面のユーザ管理

    protected $logger;
    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function get_or_create($monipla_user_id, $opt_in) {
        try {
            $method = 'GET';
            $url = $this->getApiUrl(self::CLASS_GET_OR_CREATE);
            $params = array(
                'allied_id' => $monipla_user_id,
                'default_optin' => $opt_in
            );
            $json_result = $this->request($method, $url, $params);
            $res = json_decode($json_result);

            if ($res->data->status != 'success') {
                throw new Exception('API connection failed | ' . $json_result, 500);
            }
            return $res;
        } catch (Exception $e) {
            $this->logger->error('#OldMoniplaUserOptinService Error ' . $e );
        }
    }

    public function update($monipla_user_id, $opt_in, $from_id = null, $free_item = null) {
        try {
            $method = 'GET';
            $url = $this->getApiUrl(self::CLASS_UPDATE);
            $params = array(
                'allied_id' => $monipla_user_id,
                'opt_in' => $opt_in,
            );
            if ($from_id) $params['from_id'] = $from_id;
            if ($free_item) $params['free_item'] = $free_item;
            $json_result = $this->request($method, $url, $params);
            $res = json_decode($json_result);

            if ($res->data->status != 'success') {
                throw new Exception('API connection failed | ' . $json_result, 500);
            }
        } catch (Exception $e) {
            $this->logger->error('#OldMoniplaUserOptinService Error ' . $e );
            return null;
        }
        return $res;
    }

    /**
     * @param $class_name
     * @return string
     */
    public function getApiUrl($class_name) {
        return 'http://' . config('Domain.old_monipla_api') . '/private_api/' . $class_name;
    }
}