<?php
AAFW::import('jp.aainc.aafw.net.aafwCurlRequestBase');

class MoniplaLotteryService extends aafwCurlRequestBase {

    /**
     * @param $user
     * @return array|mixed
     */
    public function getCode($user) {
        if (!config('MoniplaLottery.isBeingHeld')) {
            return array();
        }

        try {
            $method = 'POST';
            $url = $this->getApiUrl();
            $params = array(
                'allied_id' => $user->monipla_user_id,
            );

            $json_result = $this->request($method, $url, $params);
            if (!$json_result) {
                throw new Exception('Api connection failed | ' . $json_result);
            }

            $res = json_decode($json_result, true);

            return $res;
        } catch (Exception $e) {
            aafwLog4phpLogger::getHipChatLogger()->error('MoniplaLotteryService#getCode error ' . $e);
            aafwLog4phpLogger::getDefaultLogger()->error('MoniplaLotteryService#getCode error ' . $e);
            aafwLog4phpLogger::getDefaultLogger()->error(json_encode(debug_backtrace()));
        }

        return array();
    }

    /**
     * @return string
     */
    public function getApiUrl() {
        return 'http://' . config('Domain.media_api') . '/v1/lottery_code';
    }
}
