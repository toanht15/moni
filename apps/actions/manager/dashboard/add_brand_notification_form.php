<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.services.BrandNotificationService');
class add_brand_notification_form extends BrandcoManagerGETActionBase {

    protected $ContainerName = 'add_brand_notifications';

    public $NeedManagerLogin = true;

    public function beforeValidate () {
        $this->resetValidateError();

        if ($this->getActionContainer('Errors')) {
            $this->Data['mode'] = BrandNotificationService::ADD_ERROR;
        }else {
            $this->Data['mode'] = $this->mode == BrandNotificationService::ADD_FINISH;
        }
    }

    public function validate () {
        return true;
    }

    function doAction() {
        /** @var BrandNotificationService $brand_notification_service */
        $brand_notification_service = $this->createService('BrandNotificationService');
        $start_time = strtotime("now");
        $this->Data['BrandNotification']['author'] = $this->Data['managerAccount']->name;
        $this->Data['BrandNotification']['public_date'] = date('Y-m-d', $start_time);
        $this->Data['message_type'] = $brand_notification_service->getMessageTypeArray();
        $this->Data['conditions'] = $brand_notification_service->getConditionsTypeArray();

        $this->assign('ActionForm', $this->Data['BrandNotification']);
        return 'manager/dashboard/add_brand_notification_form.php';
    }
}