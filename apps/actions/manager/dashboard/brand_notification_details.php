<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.services.BrandNotificationService');
class brand_notification_details extends BrandcoManagerGETActionBase {

    protected $ContainerName = 'edit_brand_notification_details';

    private $pageLimited = 20;

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
        /** @var BrandService $brand_service */
        $brand_service = $this->createService('BrandService');
        /** @var BrandNotificationService $brand_message_readmark_service */
        $brand_message_readmark_service = $this->createService('BrandNotificationService');
        /** @var BrandsUsersRelation $brand_user_relation_service */
        $brand_user_relation_service = $this->createService('BrandsUsersRelationService');
        /** @var UserService $user_service */
        $user_service = $this->createService('UserService');

        $brands = $brand_service->getBrands($this->p, $this->pageLimited);
        $this->Data['brandMessageReadmark'] = array();
        foreach ($brands as $brand) {
            $brandMessageReadmark = $brand->toArray();
            $brand_user_relation = $brand_user_relation_service->getBrandsAdminUsersByBrandId($brand->id);
            foreach ($brand_user_relation as $admin_user) {
                $brandMessageReadmark['admin_user_name'] = $user_service->getUserByBrandcoUserId($admin_user->user_id)->name;
                $brand_mark_read = $brand_message_readmark_service->getBrandMessageReadmarkByNotificationIdAndBrandIdAndUserId($this->GET['exts'][0], $brand->id, $admin_user->user_id);
                if ($brand->id == $brand_mark_read->brand_id) {
                    $brandMessageReadmark['readMark'] = BrandNotificationService::MESSAGE_READ;
                } else {
                    $brandMessageReadmark['readMark'] = null;
                }
                $this->Data['brandMessageReadmark'][] = $brandMessageReadmark;
            }
        }
        $this->Data['allManagerCount'] = $brand_service->countBrands();
        $total_page = floor($this->Data['allManagerCount'] / $this->pageLimited) + ($this->Data['allManagerCount'] % $this->pageLimited > 0);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['pageLimited'] = $this->pageLimited;

        $notification_id = $this->GET['exts'][0];
        /** @var BrandNotificationService $brand_notification_service */
        $brand_notification_service = $this->createService('BrandNotificationService');
        $this->Data['brand_notification_info'] = $brand_notification_service->getBrandNotificationById($notification_id);
        $this->Data['notification_icon_info'] = $brand_notification_service->getIconByNotificationId($notification_id);
        $this->Data['conditions'] = $brand_notification_service->getConditionsByNotificationId($notification_id);
        $this->Data['notification_id'] = $notification_id;

        return 'manager/dashboard/brand_notification_details.php';
    }
}