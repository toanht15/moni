<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
class edit_notification_details extends BrandcoManagerPOSTActionBase {

    protected $ContainerName = 'edit_notification_details';
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'edit_notification_detail_form/',
    );

    public $NeedManagerLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'subject' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
        ),
        'contents' => array(
            'required' => 1,
            'type' => 'str',
        ),
        'publish_at' => array(
            'required' => true,
            'type' => 'str',
        ),
    );

    public function doThisFirst() {
        $notification_id = $this->GET['exts'][0];
        $this->Form['action'] = 'edit_notification_detail_form/' . $notification_id;
    }

    public function validate() {
        return true;
    }

    function doAction() {
        $notification_id = $this->GET['exts'][0];
        $brand_notification_service = $this->createService('BrandNotificationService');
        $this->Data['BrandNotification'] = array();
        $brand_notification = $brand_notification_service->getBrandNotificationById($notification_id);
        $brand_notification->subject = $this->POST['subject'];
        $brand_notification->contents = $this->POST['contents'];
        $brand_notification->author = $this->Data['managerAccount']->name;
        $brand_notification->message_type = $this->POST['message_type'];
        $brand_notification->publish_at = $this->POST['publish_at'];
        $brand_notification->conditions = $this->POST['conditions'];
        $brand_notification_update = $brand_notification_service->updateBrandNotification($brand_notification);
        $brand_notification_service->deleteReadMark($notification_id);

        $this->Data['saved'] = 1;

        return 'redirect: ' . Util::rewriteUrl('dashboard', 'brand_notification_details', array($brand_notification_update->id), array('mode' => BrandNotificationService::ADD_FINISH), '', true);
    }
}