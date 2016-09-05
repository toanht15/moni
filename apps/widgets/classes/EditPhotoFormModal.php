<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditPhotoFormModal extends aafwWidgetBase {

    public function doService($params = array()) {
        $service_factory = new aafwServiceFactory();
        $photo_user_service = $service_factory->create('PhotoUserService');
        $manager_service    = $service_factory->create('ManagerService');

        $params['photo_user'] = $photo_user_service->getPhotoUserById($params['photo_user_id']);
        $cp_user = $params['photo_user']->getCpUser();
        $params['user'] = $cp_user->getUser();
        $params['cp'] = $cp_user->getCp();
        $params['isAgent'] = $manager_service->isAgentLogin();

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_PERSONAL_INFO);
        $params['is_hide_personal_info'] = !Util::isNullOrEmpty($brand_global_setting) ? true : false;

        $next_photo_user_id = $photo_user_service->getNextPhotoUserId($params['photo_user_id'], $params['photo_user']->cp_action_id);
        if ($next_photo_user_id) {
            $params['pageData']['next_id'] = $next_photo_user_id;
        }
        $prev_photo_user_id = $photo_user_service->getPrevPhotoUserId($params['photo_user_id'], $params['photo_user']->cp_action_id);
        if ($prev_photo_user_id) {
            $params['pageData']['prev_id'] = $prev_photo_user_id;
        }

        return $params;

    }
}
