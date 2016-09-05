<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
class add_brand_notifications extends BrandcoManagerPOSTActionBase {

    protected $ContainerName = 'add_brand_notifications';
    protected $Form = array(
        'package' => 'dashboard',
        'action' => 'add_brand_notification_form',
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
        'public_date' => array(
            'required' => true,
            'type' => 'str',
        ),
    );

    public function validate() {
        return true;
    }

    function doAction() {
        /** @var BrandNotificationService $brand_notification_service */
        $brand_notification_service = $this->createService('BrandNotificationService');
        $brand_notification_service->createBrandNotification($this->POST);

        $this->Data['saved'] = 1;

        return 'redirect: ' . Util::rewriteUrl('dashboard', 'brand_notification_list', array(), array('mode' => BrandNotificationService::ADD_FINISH), '', true);
    }
}