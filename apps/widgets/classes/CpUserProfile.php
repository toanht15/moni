<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpUserProfile extends aafwWidgetBase {

    public function doService($params = array()) {
        $service_factory = new aafwServiceFactory();
        /** @var BrandPageSettingService $brand_page_setting_service */
        $brand_page_setting_service = $service_factory->create('BrandPageSettingService');
        $params['page_settings'] = $brand_page_setting_service->getPageSettingsByBrandId($params['brand']->id);
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');
        $original_sns_account = $brand_global_setting_service->getBrandGlobalSetting($params['brand']->id, BrandGlobalSettingService::ORIGINAL_SNS_ACCOUNTS);

        $params['original_sns_account_array'] = array();
        if($original_sns_account){
            $params['original_sns_account_array'] = explode(',', $original_sns_account->content);
        }

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

        //TODO ハードコーディング: カンコーブランドのプロフィールアンケート質問の回答から、「子供の年代」という新しいカラムを追加する
        /** @var ProfileQuestionProcessService $profile_question_process_service */
        $profile_question_process_service = $this->getService("ProfileQuestionProcessService");
        if ($params['use_profile_questions'] && $params['brand']->id == Brand::KANKO) {   //TODO カンコーブランドのハードコーディング
            $params['extend_columns'] = $profile_question_process_service->getExtendColumnForUserList();
        }

        /** @var ConversionService $conversion_service */
        $conversion_service = $service_factory->create('ConversionService');
        $params['conversions'] = $conversion_service->getConversionsByBrandId($params['brand']->id);

        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $service_factory->create('BrandSocialAccountService');
        $params['facebook_accounts'] = $brand_social_account_service->getSocialAccountsByBrandId($params['brand']->id,SocialApps::PROVIDER_FACEBOOK);
        $params['twitter_accounts'] = $brand_social_account_service->getSocialAccountsByBrandId($params['brand']->id, SocialApps::PROVIDER_TWITTER);

        $getSocialLikes = $params['facebook_accounts'] && !$params['isSocialLikesEmpty'];
        $getTwitterFollows = $params['twitter_accounts'] && !$params['isTwitterFollowsEmpty'];

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
                $params['user_profile'] = $cp_user_list_service->getFanListProfile($user_ids, $params['brand']->id, $params['profile_questions'], $params['conversions'], $original_sns_account, $getSocialLikes, $getTwitterFollows, $params['cp_id']);
            } else{
                $params['user_profile'] = $cp_user_list_service->getFanListProfile($user_ids, $params['brand']->id, $params['profile_questions'], $params['conversions'], $original_sns_account, $getSocialLikes, $getTwitterFollows);
            }
            
            //ブランドごとにカスタマイズされたカラム
            if($params['definitions']) {
                $params['user_attributes'] = $cp_user_list_service->getFanlistAttribute($user_ids, $params['definitions']);
            }

            //TODO ハードコーディング: カンコーブランドのプロフィールアンケート質問の回答から、「子供の生まれた年代」という新しいカラムを追加する
            if($params['extend_columns']) {
                foreach ($params['fan_list_users'] as $fan_list_user) {
                    $params['user_profile'][$fan_list_user->user_id]['children_ages'] = $profile_question_process_service->getChildsBirthOfPeriod($fan_list_user->user_id, $params['brand']->id, array_keys($params['extend_columns']));
                }
            }
        }
        return $params;
    }
}
