<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class notification_list extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $NeedAdminLogin = true;
    private $pageLimited = 10;

    public function validate() {
        return true;
    }

    function doAction() {
        if ($this->GET['p']) $this->p = $this->GET['p'];
        /** @var BrandNotificationService $brand_notification_service */
        $brand_notification_service = $this->createService('BrandNotificationService');
        $brand_notification_info = $brand_notification_service->getBrandNotificationInfoBeforeToday($this->p, $this->pageLimited, null, 'publish_at DESC');
        $this->Data['notifications'] = array();
        foreach ($brand_notification_info as $brand_notification) {
            $notification = $brand_notification->toArray();
            $notification['conditions'] = $brand_notification_service->getConditionsByNotificationId($brand_notification->id);
            $notification['icon_information'] = $brand_notification_service->getIconByNotificationId($brand_notification->id);
            $notification['brand_read_mark'] = $brand_notification_service->getBrandMessageReadmarkByNotificationIdAndBrandIdAndUserId($brand_notification->id, $this->getBrandsUsersRelation()->brand_id, $this->getBrandsUsersRelation()->user_id);
            $this->Data['notifications'][] = $notification;
        }
        $this->Data['totalEntriesCount'] = $brand_notification_service->countBrandNotificationBeforeToday();
        $total_page = floor($this->Data['totalEntriesCount'] / $this->pageLimited) + ($this->Data['totalEntriesCount'] % $this->pageLimited > 0);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['pageLimited'] = $this->pageLimited;

        return 'user/brandco/admin-information/notification_list.php';
    }
}