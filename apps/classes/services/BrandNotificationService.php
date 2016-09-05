<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class BrandNotificationService extends aafwServiceBase {

    protected $manager;

    const ADD_FINISH = 1; // 正常終了
    const ADD_ERROR = 2; // エラー終了

    const PUBLISH = 0; //公開
    const DRAFT = 1; //下書き

    const MESSAGE_READ = 1; //済み

    const NOTIFICATION_RELEASE = 0; //リリース報告
    const NOTIFICATION_SEMINAR = 1; //セミナー告知
    const NOTIFICATION_INTRODUCE = 2; //事例紹介
    const NOTIFICATION_FAILURE = 3; //障害報告
    const NOTIFICATION_URGENT = 4; //緊急のお知らせ
    const NOTIFICATION_MAINTENANCE = 5; //メンテナンス告知

    const NOTIFICATION_CONDITIONS_TYPE_DRAFT = '下書き';
    const NOTIFICATION_CONDITIONS_TYPE_PUBLISH = '公開';

    const NOTIFICATION_TYPE_CAPTION_RELEASE = 'リリース報告';
    const NOTIFICATION_TYPE_CAPTION_SEMINAR = 'セミナーの告知';
    const NOTIFICATION_TYPE_CAPTION_INTRODUCE = '事例紹介';
    const NOTIFICATION_TYPE_CAPTION_FAILURE = '障害報告';
    const NOTIFICATION_TYPE_CAPTION_URGENT = '緊急のお知らせ';
    const NOTIFICATION_TYPE_CAPTION_MAINTENANCE = 'メンテナンス告知';

    const NOTIFICATION_TYPE_ICON_INFO = '/img/icon/iconInfo1.png';
    const NOTIFICATION_TYPE_ICON_ATTENTION = '/img/icon/iconAttention1.png';
    const NOTIFICATION_TYPE_ICON_ALERT = '/img/icon/iconAlert1.png';

    public function __construct() {
        $this->brand_notification = $this->getModel('BrandNotifications');
        $this->brand_message_readmark = $this->getModel('BrandMessageReadmarks');
        $this->brands = $this->getModel("Brands");
    }

    public function getBrandNotificationInfo($page = 1, $limit = 20, $params = array(), $order = null) {
        $filter = array(
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            'order' => $order,
        );

        return $this->brand_notification->find($filter);
    }

    public function getBrandNotificationInfoBeforeToday($page = 1, $limit = 20, $params = array(), $order = null) {
        $today = date("Y-m-d");
        $filter = array(
            'pager' => array(
                'page' => $page,
                'count' => $limit,
            ),
            'order' => $order,
            'publish_at:<=' => $today,
            'conditions' => BrandNotificationService::PUBLISH,
        );

        return $this->brand_notification->find($filter);
    }

    public function countBrandNotification() {
        return $this->brand_notification->count();
    }

    public function countBrandNotificationBeforeToday(){
        $today = date("Y-m-d");
        $filter = array(
            'publish_at:<=' => $today,
            'conditions' => BrandNotificationService::PUBLISH,
        );
        return $this->brand_notification->count($filter);
    }

    public function createBrandNotification($params) {

        $brandNotifications = $this->createEmptyBrandNotification();
        $brandNotifications->subject = $params['subject'];
        $order = array("\r\n", "\n", "\r");
        $replace = '<br />';
        $brandNotifications->contents = str_replace($order, $replace, $params['contents']);
        $brandNotifications->author = $params['author'];
        $brandNotifications->message_type = $params['test_page'];
        $brandNotifications->conditions = $params['conditions'];
        $brandNotifications->publish_at = $params['public_date'];
        return $this->saveBrandNotification($brandNotifications);
    }

    public function saveNotificationReadmark($notification_id, $brand_id, $user_id) {
        $brand_message_readmark = $this->createEmptyBrandMessageReadmark();
        $brand_message_readmark->brand_notification_id = $notification_id;
        $brand_message_readmark->brand_id = $brand_id;
        $brand_message_readmark->user_id = $user_id;
        $this->saveBrandMessageReadmarks($brand_message_readmark);
    }

    public function updateNotificationReadmark($brand_message_remark, $notification_id, $brand_id, $user_id) {
        $brand_message_readmark = $brand_message_remark;
        $brand_message_readmark->brand_notification_id = $notification_id;
        $brand_message_readmark->brand_id = $brand_id;
        $brand_message_readmark->user_id = $user_id;
        $this->saveBrandMessageReadmarks($brand_message_readmark);
    }

    public function saveBrandNotification($brandNotification) {
        return $this->brand_notification->save($brandNotification);
    }

    public function saveBrandMessageReadmarks($brandMessageReadmarks) {
        return $this->brand_message_readmark->save($brandMessageReadmarks);
    }

    public function createEmptyBrandNotification() {
        return $this->brand_notification->createEmptyObject();
    }

    public function createEmptyBrandMessageReadmark() {
        return $this->brand_message_readmark->createEmptyObject();
    }

    public function getBrandNotificationById($id) {
        $filter = array(
            'id' => $id,
        );
        return $this->brand_notification->findOne($filter);
    }

    public function getAllBrandNotificationAfterRegisteredAt($registered_at) {
        $today = date("Y-m-d");
        $filter = array(
            'publish_at:>=' => date("Y-m-d", strtotime($registered_at)),
            'publish_at:<=' => $today,
            'conditions' => BrandNotificationService::PUBLISH,
        );
        return $this->brand_notification->find($filter);
    }

    public function getBrandReadMarkInfoByBrandIdAndUserId($brand_id,$user_id) {
        $filter = array(
            'brand_id' => $brand_id,
            'user_id' => $user_id
        );
        return $this->brand_message_readmark->find($filter);
    }

    public function getBrandReadMarkInfoByNotificationId($id) {
        $filter = array(
            'brand_notification_id' => $id,
        );
        return $this->brand_message_readmark->find($filter);
    }

    public function getBrandMessageReadmarkByNotificationIdAndBrandIdAndUserId($notification_id, $brand_id, $user_id) {
        $filter = array(
            'brand_notification_id' => $notification_id,
            'brand_id' => $brand_id,
            'user_id' => $user_id
        );
        return $this->brand_message_readmark->findOne($filter);
    }

    public function updateBrandNotification($brandNotification) {
        return $this->brand_notification->save($brandNotification);
    }

    public function deleteNotification($id) {
        $brand_notification = $this->getBrandNotificationById($id);
        $this->brand_notification->delete($brand_notification);
    }

    public function deleteReadMark($id) {
        $brand_notification_readmarks = $this->getBrandReadMarkInfoByNotificationId($id);
        foreach ($brand_notification_readmarks as $brand_notification_readmark) {
            $this->brand_message_readmark->delete($brand_notification_readmark);
        }
    }

    public function getInfobyNotificationId($notification_id) {
        $filter = array(
            'id' => $notification_id,
        );
        return $this->brand_notification->findOne($filter);
    }

    public function getIconByNotificationId($notification_id) {
        $brand_notification = $this->getInfobyNotificationId($notification_id);
        if ($brand_notification->message_type == BrandNotificationService::NOTIFICATION_RELEASE) {
            $total_notification_info = array(
                'icon' => BrandNotificationService::NOTIFICATION_TYPE_ICON_INFO,
                'message_type' => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_RELEASE,
            );
        } elseif ($brand_notification->message_type == BrandNotificationService::NOTIFICATION_SEMINAR) {
            $total_notification_info = array(
                'icon' => BrandNotificationService::NOTIFICATION_TYPE_ICON_INFO,
                'message_type' => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_SEMINAR,
            );
        } elseif ($brand_notification->message_type == BrandNotificationService::NOTIFICATION_INTRODUCE) {
            $total_notification_info = array(
                'icon' => BrandNotificationService::NOTIFICATION_TYPE_ICON_INFO,
                'message_type' => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_INTRODUCE,
            );
        } elseif ($brand_notification->message_type == BrandNotificationService::NOTIFICATION_FAILURE) {
            $total_notification_info = array(
                'icon' => BrandNotificationService::NOTIFICATION_TYPE_ICON_ATTENTION,
                'message_type' => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_FAILURE,
            );
        } elseif ($brand_notification->message_type == BrandNotificationService::NOTIFICATION_URGENT) {
            $total_notification_info = array(
                'icon' => BrandNotificationService::NOTIFICATION_TYPE_ICON_ALERT,
                'message_type' => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_URGENT
            );
        } elseif ($brand_notification->message_type == BrandNotificationService::NOTIFICATION_MAINTENANCE) {
            $total_notification_info = array(
                'icon' => BrandNotificationService::NOTIFICATION_TYPE_ICON_ATTENTION,
                'message_type' => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_MAINTENANCE
            );
        }
        return $total_notification_info;
    }

    public static function getMessageTypeArray() {
        $filter = array(
            BrandNotificationService::NOTIFICATION_RELEASE => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_RELEASE,
            BrandNotificationService::NOTIFICATION_SEMINAR  => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_SEMINAR,
            BrandNotificationService::NOTIFICATION_INTRODUCE => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_INTRODUCE,
            BrandNotificationService::NOTIFICATION_FAILURE => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_FAILURE,
            BrandNotificationService::NOTIFICATION_URGENT => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_URGENT,
            BrandNotificationService::NOTIFICATION_MAINTENANCE => BrandNotificationService::NOTIFICATION_TYPE_CAPTION_MAINTENANCE,
        );
        return $filter;
    }

    public function getConditionsByNotificationId($notification_id) {
        $brand_notification = $this->getInfobyNotificationId($notification_id);
        if ($brand_notification->conditions == BrandNotificationService::PUBLISH) {
            $notification_conditions =  BrandNotificationService::NOTIFICATION_CONDITIONS_TYPE_PUBLISH;
        } elseif ($brand_notification->conditions == BrandNotificationService::DRAFT) {
            $notification_conditions =  BrandNotificationService::NOTIFICATION_CONDITIONS_TYPE_DRAFT;
        }
        return $notification_conditions;
    }

    public static function getConditionsTypeArray() {
        $filter = array(
            BrandNotificationService::PUBLISH  => BrandNotificationService::NOTIFICATION_CONDITIONS_TYPE_PUBLISH,
            BrandNotificationService::DRAFT => BrandNotificationService::NOTIFICATION_CONDITIONS_TYPE_DRAFT,
        );
        return $filter;
    }


}