<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');
AAFW::import('jp.aainc.classes.services.BrandcoAuthService');

class backdoor_login extends BrandcoManagerGETActionBase {

    protected $brandsUsersRelation;

    public function validate() {
        if (!$this->isLoginManager()) {
            return '403';
        }

        /** @var BrandsUsersRelationService $brandsUsersRelationService */
        $brandsUsersRelationService = $this->getService('BrandsUsersRelationService');
        $this->getService('ManagerUserSearchService');
        $this->Data['brandsUsersRelation'] = $brandsUsersRelationService->getBrandsUsersRelation($this->brand_id, $this->user_id);
        // 代理ログインで使用するtokenのチェック
        $salt = $this->getSession('backdoor_login_salt');
        $this->setSession('backdoor_login_salt', null);
        $token = Util::generateBackdoorLoginToken(
            $this->Data['brandsUsersRelation']->created_at,
            $salt
        );
        if ($this->token !== $token) {
            return false;
        }

        return true;
    }

    function doAction() {
        /** @var UserSearchService $user_search_service */
        $user_search_service = $this->createService('UserSearchService');
        $this->Data['oneTimeBrandUrl'] = $user_search_service->generateOnetimeUrl($this->brand_id, $this->user_id);

        switch ($this->GET['parameter_search_type']) {
            case ManagerUserSearchService::SEARCH_TYPE_PL_UID:
                $parameter = ['search_type' => $this->GET['parameter_search_type'], 'platform_user_id' => $this->GET['parameter_platform_user_id']];
                break;
            case ManagerUserSearchService::SEARCH_TYPE_BRC_UID:
                $parameter = ['search_type' => $this->GET['parameter_search_type'], 'brandco_user_id' => $this->GET['parameter_brandco_user_id']];
                break;
            case ManagerUserSearchService::SEARCH_TYPE_SNS_UID:
                $parameter = ['search_type' => $this->GET['parameter_search_type'], 'social_media_id' => $this->GET['parameter_social_media_id'], 'social_media_account_id' => $this->GET['parameter_social_media_account_id']];
                break;
            case ManagerUserSearchService::SEARCH_TYPE_PL_MAIL:
                $parameter = ['search_type' => $this->GET['parameter_search_type'], 'platform_mail_address' => urlencode($this->GET['parameter_platform_mail_address'])];
                break;
            case ManagerUserSearchService::SEARCH_TYPE_BRC_MAIL:
                $parameter = ['search_type' => $this->GET['parameter_search_type'], 'brandco_mail_address' => urlencode($this->GET['parameter_brandco_mail_address'])];
                break;
            case ManagerUserSearchService::SEARCH_TYPE_BRC_NO:
                $parameter = ['search_type' => $this->GET['parameter_search_type'], 'brand_id' => $this->GET['parameter_brand_id'], 'brand_user_no' => $this->GET['parameter_brand_user_no']];
                break;
        }

        $this->Data['return_url'] = Util::rewriteUrl('users', 'index', array(), $parameter, '', true);

        return 'manager/users/backdoor_login.php';
    }
}
