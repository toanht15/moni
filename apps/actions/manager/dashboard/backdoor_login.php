<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');
AAFW::import('jp.aainc.classes.services.BrandcoAuthService');

class backdoor_login extends BrandcoManagerGETActionBase {

    protected $brandsUsersRelation;

    public function validate() {
        // Managerのログインセッションが不安定のため
        // 一時的にコメントアウトする
        //if (!$this->isLoginManager()) {
        //    return '403';
        //}

        /** @var BrandsUsersRelationService $brandsUsersRelationService */
        $brandsUsersRelationService = $this->getService('BrandsUsersRelationService');
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

        switch ($this->search_type) {
            case UserSearchService::USER_SEARCH_PLATFORM_ID:
                $parameter = ['search_type' => $this->search_type, 'platform_id' => $this->platform_id];
                break;
            case UserSearchService::USER_SEARCH_BRANDCO_ID:
                $parameter = ['search_type' => $this->search_type, 'brandco_uid' => $this->brandco_uid];
                break;
            case UserSearchService::USER_SEARCH_SNS:
                $parameter = ['search_type' => $this->search_type, 'sns' => $this->sns, 'sns_id' => $this->sns_id];
                break;
            case UserSearchService::USER_SEARCH_AA_MAIL:
                $parameter = ['search_type' => $this->search_type, 'allied_mail_address' => urlencode($this->allied_mail_address)];
                break;
            case UserSearchService::USER_SEARCH_BRAND_MAIL:
                $parameter = ['search_type' => $this->search_type, 'brandco_mail_address' => urlencode($this->brandco_mail_address)];
                break;
            case UserSearchService::USER_SEARCH_BRAND:
                $parameter = ['search_type' => $this->search_type, 'brand_id' => $this->search_brand_id, 'member_no' => $this->member_no];
                break;
        }

        $this->Data['return_url'] = Util::rewriteUrl('dashboard', 'user_search', array(), $parameter, '', true);

        return 'manager/dashboard/backdoor_login.php';
    }
}
