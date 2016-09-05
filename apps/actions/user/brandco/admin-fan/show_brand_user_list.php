<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.CacheManager');

class show_brand_user_list extends BrandcoGETActionBase {
    protected $ContainerName = 'show_brand_user_list';

    public $NeedOption = array(BrandOptions::OPTION_FAN_LIST);
    public $NeedAdminLogin = true;

    public function doThisFirst() {
        $this->deleteErrorSession();
        $this->setBrandSession('searchBrandCondition', null);
        $this->setBrandSession('orderBrandCondition', null);

        $this->Data['brand'] = $this->getBrand();
    }

    public function validate() {
        return true;
    }

    function doAction() {
        // このページに来る時のリンク元のユーザ数をリセット
        $cache_manager = new CacheManager();

        $brands_users_count = $cache_manager->getCache("fc", array($this->Data['brand']->id));
        if($brands_users_count) {
            $cache_manager->addCache("fc", $brands_users_count, array($this->Data['brand']->id, $this->getBrandsUsersRelation()->user_id));
        }

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');
        $this->Data['isManager'] = $this->Data['pageStatus']['manager']->id ? $this->Data['pageStatus']['manager']->id : '';
        $this->Data['can_download_brand_user_list'] = $this->Data['isManager'] ||
            $brand_global_setting_service->getBrandGlobalSetting($this->Data['brand']->id, BrandGlobalSettingService::CAN_DOWNLOAD_BRAND_USER_LIST);

        /** @var SocialLikeService $social_like_service */
        $social_like_service = $this->createService('SocialLikeService');
        $this->Data['isSocialLikesEmpty'] = $social_like_service->isEmptyTable();

        /** @var TwitterFollowService $twitter_follow_service */
        $twitter_follow_service = $this->createService('TwitterFollowService');
        $this->Data['isTwitterFollowsEmpty'] = $twitter_follow_service->isEmptyTable();

        return 'user/brandco/admin-fan/show_brand_user_list.php';
    }
}
