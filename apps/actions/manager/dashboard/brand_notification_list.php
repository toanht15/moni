<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class brand_notification_list extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId    = Manager::MENU_INFORMATION;
    private $pageLimited = 20;

    public function validate () {
        return true;
    }

    function doAction() {
        if ($this->GET['p']) $this->p = $this->GET['p'];
        /** @var BrandNotificationService $brand_notification_service */
        $brand_notification_service = $this->createService('BrandNotificationService');

        $brand_notification_info = $brand_notification_service->getBrandNotificationInfo($this->p, $this->pageLimited, null, 'created_at DESC');
        $this->Data['notifications'] = array();
        foreach ($brand_notification_info as $brand_notification) {
            $notification = $brand_notification->toArray();
            $notification['icon_information'] = $brand_notification_service->getIconByNotificationId($brand_notification->id);
            $notification['conditions'] = $brand_notification_service->getConditionsByNotificationId($brand_notification->id);
            $this->Data['notifications'][] = $notification;
        }
        // ページング
        $this->Data['totalEntriesCount'] = $brand_notification_service->countBrandNotification();
        $total_page = floor($this->Data['totalEntriesCount'] / $this->pageLimited) + ($this->Data['totalEntriesCount'] % $this->pageLimited > 0);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['pageLimited'] = $this->pageLimited;

        return 'manager/dashboard/brand_notification_list.php';
    }

}