<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.services.BrandNotificationService');
class edit_notification_detail_form extends BrandcoManagerGETActionBase {

    protected $ContainerName = 'edit_notification_details';

    public $NeedManagerLogin = true;

    public function beforeValidate () {
        $this->resetValidateError();

        if ( !$this->getActionContainer('Errors') ) {
            $this->Data['mode'] = $this->mode == BrandNotificationService::ADD_FINISH ? BrandNotificationService::ADD_FINISH : $this->mode;
        } else {
            $this->Data['mode'] = BrandNotificationService::ADD_ERROR;
        }
    }

    public function validate () {
        return true;
    }

    function doAction() {

        $notification_id = $this->GET['exts'][0];
        /** @var BrandNotificationService $brand_notification_service */
        $brand_notification_service = $this->createService('BrandNotificationService');
        $brand_notification_info = $brand_notification_service->getBrandNotificationById($notification_id);
        $this->Data['conditions'] = $brand_notification_service->getConditionsTypeArray();
        $this->Data['message_type'] = $brand_notification_service->getMessageTypeArray();
        $this->assign('ActionForm', $brand_notification_info->toArray());
        $this->Data['notification_id'] = $notification_id;

        return 'manager/dashboard/edit_notification_detail_form.php';
    }
}