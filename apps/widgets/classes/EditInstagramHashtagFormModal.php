<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditInstagramHashtagFormModal extends aafwWidgetBase {

    public function doService($params = array()) {
        $service_factory = new aafwServiceFactory();
        /** @var InstagramHashtagUserService $instagram_hashtag_user_service */
        $instagram_hashtag_user_service = $service_factory->create('InstagramHashtagUserService');

        $params['instagram_hashtag_user_post'] = $instagram_hashtag_user_service->getInstagramHashtagUserPostById($params['instagram_hashtag_user_post_id']);
        $cp_user = $params['instagram_hashtag_user_post']->getInstagramHashtagUser()->getCpUser();
        $params['instagram_hashtag_user'] = $params['instagram_hashtag_user_post']->getInstagramHashtagUser();
        $params['user'] = $cp_user->getUser();
        $params['cp'] = $cp_user->getCp();

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::HIDE_PERSONAL_INFO);
        $params['is_hide_personal_info'] = !Util::isNullOrEmpty($brand_global_setting) ? true : false;

        $next_instagram_hashtag_user_id = $instagram_hashtag_user_service->getNextInstagramHashtagUserPostId($params['instagram_hashtag_user_post_id'], $params['instagram_hashtag_user_post']->getInstagramHashtagUser()->cp_action_id);
        if ($next_instagram_hashtag_user_id) {
            $params['pageData']['next_id'] = $next_instagram_hashtag_user_id;
        }
        $prev_instagram_hashtag_user_id = $instagram_hashtag_user_service->getPrevInstagramHashtagUserPostId($params['instagram_hashtag_user_post_id'], $params['instagram_hashtag_user_post']->getInstagramHashtagUser()->cp_action_id);
        if ($prev_instagram_hashtag_user_id) {
            $params['pageData']['prev_id'] = $prev_instagram_hashtag_user_id;
        }

        return $params;

    }
}