<?php
AAFW::import('jp.aainc.classes.brandco.auth.BrandcoServiceLoginActionBase');
AAFW::import('jp.aainc.classes.entities.CpUser');

class campaign_login extends BrandcoServiceLoginActionBase {

    /**
     * @return string|void
     */
    function doSubAction() {
        if (!$this->isLogin()) return;

        if (!$this->GET['cp_id']) return 'redirect: ' . $this->redirectUrl;

        $brands_users_relation = $this->getBrandsUsersRelation();
        if ($brands_users_relation == null) return;

        if ($brands_users_relation->user_id && $this->checkValidUserSNSAccountType($this->getSession('pl_monipla_userInfo'), $this->getSNSAccountType($this->platform))) {
            $this->setSession('clientId', $this->platform);

            $this->Data['beginner_flg'] = CpUser::NOT_BEGINNER_USER;
            $this->Data['cp_id'] = $this->GET['cp_id'];

            /** @var CpUserService $cp_user_service */
            $cp_user_service = $this->createService('CpUserService');

            if ($cp_user_service->isJoinedCp($this->Data['cp_id'], $brands_users_relation->user_id)) {
                return 'redirect: ' . Util::rewriteUrl('messages', 'thread', array("cp_id" => $this->Data['cp_id']));
            }

            return 'user/brandco/auth/signup_redirect.php';
        }

        // Set Logout
        $this->setLogout($brands_users_relation);
        $this->resetActionContainerByType();

        return;
    }
}