<?php
AAFW::import('jp.aainc.aafw.net.aafwCurlRequestBase');

class MoniplaCpService extends aafwCurlRequestBase {
    const DEFAULT_N_CPS = 3;

    /**
     * @param array $params
     * @return array|mixed
     */
    public function getCp($params = array()) {
        try {
            $method = 'GET';
            $url = $this->getApiUrl();

            $json_result = $this->request($method, $url, $params);
            if (!$json_result) {
                throw new Exception('Api connection failed | ' . $json_result);
            }

            $res = json_decode($json_result, true);

            return $res;
        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error('MoniplaCpService#getInfo error ' . $e);
            aafwLog4phpLogger::getHipChatLogger()->error('MoniplaCpService#getInfo error ' . $e);
        }

        return array();
    }

    /**
     * @return string
     */
    public function getApiUrl() {
        return 'http://' . config('Domain.media_api') . '/v1/campaigns';
    }
}
