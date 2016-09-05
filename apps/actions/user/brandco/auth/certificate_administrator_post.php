<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class certificate_administrator_post extends BrandcoPOSTActionBase {
    protected $ContainerName = 'certificate_administrator';
    public $NeedOption = array();
    protected $Form = array(
        'package' => 'auth',
        'action' => 'certificate_administrator'
    );

    public $CrsfProtect = true;

    protected $ValidatorDefinition = array(
        'password' => array(
            'required' => 1,
            'type' => 'num',
            'length' => 4
        )
    );

    public function doThisFirst() {
        /** @var AdminInviteTokenService $admin_invite_service */
        $admin_invite_service = $this->createService('AdminInviteTokenService');
        if ($admin_invite_service->getValidInvitedToken($this->getBrand()->id)) {
            $this->NeedPublic = true;
        }
    }

    public function validate () {
        /** @var AdminInviteTokenService $admin_invite_token_service */
        $admin_invite_token_service = $this->createService('AdminInviteTokenService');
        $match_invite_admin = $admin_invite_token_service->matchInviteAdmin($this->getBrand()->id, $this->invite_token, $this->password);

        if (!$match_invite_admin) {
            $this->Validator->setError('invite_certificate_fail', 'INVITE_CERTIFICATE_FAIL');
        }

        return $this->Validator->isValid();
    }

    function doAction() {
        /** @var BrandsUsersRelationService $brands_users_service */
        $brands_users_service = $this->createService('BrandsUsersRelationService');
        $brands_users_service->setAdminFlg($this->getBrand()->id, $this->getBrandsUsersRelation()->user_id);

        // ワンタイムトークンの削除処理
        $invite_token_service = $this->createService('AdminInviteTokenService');
        $invite_token_service->certificatedToken($this->brand->id, $this->invite_token);
        $this->setSession('invite_token'.$this->getBrand()->id, null);
        $this->Data['saved'] = 1;

        $redirectUrl = Util::rewriteUrl('admin-cp', 'public_cps', array(), array('type' => 1, 'mid' => 'confirm-success'));

        return 'redirect: ' . $redirectUrl;
    }
}