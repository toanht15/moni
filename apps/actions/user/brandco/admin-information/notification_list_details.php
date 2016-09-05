<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class notification_list_details extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->Data['notification_id'] = $this->GET['exts'][0];
    }

    public function validate() {
        /** @var BrandNotificationService $brand_notification_service */
        $this->brand_notification_service = $this->createService('BrandNotificationService');
        $today = date("Y-m-d");
        $this->Data['brand_notification_info'] = $this->brand_notification_service->getBrandNotificationById($this->Data['notification_id']);
        if ($this->Data['brand_notification_info']->conditions == BrandNotificationService::DRAFT) {
            return '404';
        }
        if ($this->Data['brand_notification_info']->publish_at > $today) {
            return '404';
        }
        return true;
    }

    function doAction() {
        /** @var BrandsUsersRelation $brand_user_relation_service */
        $brand_user_relation_service = $this->createService('BrandsUsersRelationService');
        $brand_user_relation = $brand_user_relation_service->getBrandsUsersRelationsByBrandIdAndUserId($this->brand->id, $this->getBrandsUsersRelation()->user_id);
        $this->Data['icon_information'] = $this->brand_notification_service->getIconByNotificationId($this->Data['notification_id']);
        if ($this->isLoginManager() || $brand_user_relation->admin_flg == BrandsUsersRelationService::ADMIN_USER) {
            $brand_message_remark = $this->brand_notification_service->getBrandMessageReadmarkByNotificationIdAndBrandIdAndUserId($this->Data['notification_id'], $this->brand->id, $this->getBrandsUsersRelation()->user_id);
            if (empty($brand_message_remark)) {
                $this->brand_notification_service->saveNotificationReadmark($this->Data['notification_id'], $this->brand->id, $this->getBrandsUsersRelation()->user_id);
            } else {
                $this->brand_notification_service->updateNotificationReadmark($brand_message_remark, $this->Data['notification_id'], $this->brand->id, $this->getBrandsUsersRelation()->user_id);
            }
        }
        return 'user/brandco/admin-information/notification_list_details.php';
    }
}
