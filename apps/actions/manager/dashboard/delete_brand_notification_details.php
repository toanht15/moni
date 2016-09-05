<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');

class delete_brand_notification_details extends BrandcoManagerPOSTActionBase {

    protected $ContainerName = 'delete_brand_notification_details';
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'delete_brand_notification_details',
    );

    public function validate() {

        return true;
    }

    function doAction() {
        $notification_id =$this->notification_id;
        /** @var BrandNotificationService $brand_notification_service */
        $brand_notification_service = $this->createService('BrandNotificationService');
        $notification_delete = $brand_notification_service->deleteNotification($notification_id);
        $read_mark = $brand_notification_service->deleteReadMark($notification_id);

        return 'redirect: ' . Util::rewriteUrl('dashboard', 'brand_notification_list', array(), array(), '', true);
    }
}