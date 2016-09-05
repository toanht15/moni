<?php
AAFW::import('jp.aainc.classes.brandco.auth.BrandcoServiceLoginActionBase');

class service_login extends BrandcoServiceLoginActionBase {

    /**
     * @return string|void
     */
    public function doSubAction() {
        /** @var AdminInviteTokenService $admin_invite_service */
        $admin_invite_service = $this->createService('AdminInviteTokenService');
        if( $this->isLogin() && !$admin_invite_service->getValidInvitedToken($this->getBrand()->id)) {
            return 'redirect: ' . $this->redirectUrl;
        }

        return;
    }
}