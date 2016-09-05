<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class administrator_settings extends BrandcoPOSTActionBase {
    protected $ContainerName = 'administrator_settings';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'administrator_settings_form',
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'mail_address' => array(
            'required' => 1,
            'type' => 'str',
            'length' => 255,
            'validator' => array('MailAddress')
        ),
    );

    public function validate () {

        // 代理店は管理者追加できたらダメ
        $manager = $this->getManager();
        if ($manager && $manager->authority == Manager::AGENT) {
            return false;
        }

        /** @var UserService $user_service */
        $user_service = $this->createService('UserService');
        $user = $user_service->getUserByEmail($this->POST['mail_address']);
        if ($user) {
            /** @var BrandsUsersRelationService $brand_user_relation_service */
            $brand_user_relation_service = $this->createService('BrandsUsersRelationService');
            $filter = array(
                'conditions' => array(
                    'brand_id' => $this->brand->id,
                    'user_id' => $user->id,
                    'admin_flg' => 1
                )
            );
            if ($brand_user_relation_service->getBrandsUsersRelationsByConditions($filter)) {
                $this->Validator->setError('mail_address', 'INVITED_EMAIL');
            }
        }

        if ($this->Validator->getErrorCount() > 0) {
            return false;
        }
        return true;
    }

    function doAction() {

        /** @var AdminInviteTokenService $admin_invite_token_service */
        $admin_invite_token_service = $this->createService('AdminInviteTokenService');
        $admin_invite_token_service->inviteAdmin($this->brand->id, $this->mail_address);
        $this->Data['saved'] = 1;
        return 'redirect: '.Util::rewriteUrl('admin-settings', 'administrator_settings_form', array(), array('mid'=>'send_invite'));
    }

}
