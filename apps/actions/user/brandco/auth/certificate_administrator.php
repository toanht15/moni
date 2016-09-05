<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class certificate_administrator extends BrandcoGETActionBase {
    protected $ContainerName = 'certificate_administrator';
    public $NeedOption = array();

    public function doThisFirst() {
        $admin_invite_service = $this->createService('AdminInviteTokenService');
        $this->Data['invite_token'] = $admin_invite_service->getValidInvitedToken($this->getBrand()->id);

        if (!$this->Data['invite_token']) {
            return 404;
        } else {
            $this->NeedPublic = true;
        }
    }

    public function validate() {
        return true;
    }

    function doAction() {
        return 'user/brandco/auth/certificate_administrator.php';
    }
}