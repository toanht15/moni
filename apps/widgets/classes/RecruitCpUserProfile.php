<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class RecruitCpUserProfile extends aafwWidgetBase {

    public function doService($params = array()) {
        $service_factory = new aafwServiceFactory();
        /** @var BrandPageSettingService $brand_page_setting_service */
        $brand_page_setting_service = $service_factory->create('BrandPageSettingService');
        $params['page_settings'] = $brand_page_setting_service->getPageSettingsByBrandId($params['brand']->id);
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');
        $params['original_sns_account'] = $brand_global_setting_service->getBrandGlobalSetting($params['brand']->id, BrandGlobalSettingService::ORIGINAL_SNS_ACCOUNTS);

        /** @var $brand_service BrandService */
        $brand_service = $service_factory->create('BrandService');
        $params['definitions'] = $brand_service->getCustomAttributeDefinitions($params['brand']->id);

        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        /** @var CpQuestionnaireService $profile_questionnaire_service */
        $profile_questionnaire_service = $service_factory->create('CpQuestionnaireService', CpQuestionnaireService::TYPE_PROFILE_QUESTION);
        $profile_question_relations = $profile_questionnaire_service->getPublicProfileQuestionRelationByBrandId($params['brand']->id);
        $params['profile_questions'] = array();
        $params['use_profile_questions'] = $profile_questionnaire_service->useProfileQuestion($profile_question_relations);
        foreach($params['use_profile_questions'] as $relation) {
            $params['profile_questions'][$relation->id] = $profile_questionnaire_service->getQuestionById($relation->question_id);
        }

        /** @var ConversionService $conversion_service */
        $conversion_service = $service_factory->create('ConversionService');
        $params['conversions'] = $conversion_service->getConversionsByBrandId($params['brand']->id);


        /** @var SocialLikeService $social_like_service */
        $params['social_like_service'] = $service_factory->create('SocialLikeService');

        if($params['fan_list_users']) {
            $user_ids = array();
            foreach($params['fan_list_users'] as $fan_list_user) {
                $user_ids[] = $fan_list_user->user_id;
            }
            /** @var CpUserListService $cp_user_list_service */
            $cp_user_list_service = $service_factory->create('CpUserListService');
            if($params['isShowDuplicateAddressCpUserList']){
                $params['user_profile'] = $cp_user_list_service->getFanListProfile($user_ids, $params['brand']->id, $params['profile_questions'], $params['conversions'], $params['original_sns_account'], $getSocialLikes, $params['cp_id']);
            } else{
                $params['user_profile'] = $cp_user_list_service->getFanListProfile($user_ids, $params['brand']->id, $params['profile_questions'], $params['conversions'], $params['original_sns_account'], $getSocialLikes);
            }

            //ブランドごとにカスタマイズされたカラム
            if($params['definitions']) {
                $params['user_attributes'] = $cp_user_list_service->getFanlistAttribute($user_ids, $params['definitions']);
            }
        }

        return $params;
    }
}

