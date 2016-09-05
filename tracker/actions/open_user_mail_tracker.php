<?php
require_once(dirname(__FILE__) . '/base/TrackerBase.php');
require_once(dirname(__FILE__) . '/../../apps/classes/Util.php');
require_once dirname(__FILE__) . '/../../apps/config/define.php';
AAFW::import('jp.aainc.classes.services.UserMailTrackingService');

//WelcomeメールとEntryメールに埋め込んだ
// <img>トラッキングタグからのアクセスによって
//メール開封を計測するクラス

class OpenUserMailTracker extends TrackerBase {
    /** @var UserMailTrackingService $user_mail_tracking_service */
    private $user_mail_tracking_service;

    public function run() {
        $service_factory = new aafwServiceFactory();
        $this->user_mail_tracking_service = $service_factory->create('UserMailTrackingService');

        try {
            //GETパラメーターを受けとる
            $params = $this->decodeParams($_GET['params']);
            if (!$params) {
                throw new aafwException('GETパラメータが不正です');
            }

            //重複確認
            if ($this->user_mail_tracking_service->isExistedOpenUserMailTrackingLog($params['user_mail_id'])) {
                throw new aafwException('既に存在しているuser_mail_idです');
            }

            //open_user_mail_tracking_logsに登録
            $this->user_mail_tracking_service->createOpenUserMailTrackingLogs($params['user_mail_id']);

        } catch (Exception $e) {
            aafwLog4phpLogger::getDefaultLogger()->error($e->getMessage());
        }

        $this->createResponseImage();
    }

    public function decodeParams($encoded_params) {
        if (!$encoded_params) {
            return false;
        }

        $decoded_params = json_decode(base64_decode($encoded_params), true);

        if (!is_array($decoded_params)) {
            return false;
        }

        return $decoded_params;
    }
}