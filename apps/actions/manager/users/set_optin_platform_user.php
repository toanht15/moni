<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');
AAFW::import('jp.aainc.classes.services.BrandsUsersRelationService');
AAFW::import('jp.aainc.classes.brandco.auth.trait.BrandcoAuthTrait');
AAFW::import('jp.aainc.classes.services.monipla.OldMoniplaUserOptinService');

class set_optin_platform_user extends BrandcoManagerPOSTActionBase {
    use BrandcoAuthTrait;

    public $NeedManagerLogin = true;
    public $ManagerPageId = Manager::MENU_USER_SEARCH;
    protected $ContainerName = 'index';
    protected $Form = array(
        'package' => 'users',
        'action' => 'index',
    );

    public function validate() {
        if (!$this->POST['platform_user_id'] || Util::isNullOrEmpty($this->POST['new_optin_flg']) || !$this->POST['opt_in_type']) {
            return false;
        }
        return true;
    }

    public function doAction() {

        $new_optin_flg = intval($this->POST['new_optin_flg']);
        if ($this->POST['opt_in_type'] == "AAID") {
            $this->updateOptinAAID($this->POST['platform_user_id'], $new_optin_flg);
        } elseif ($this->POST['opt_in_type'] == "MPFB") {
            $this->updateOptinMpFb($this->POST['platform_user_id'], $new_optin_flg, OldMoniplaUserOptinService::FROM_ID_MGR, null);
        }
        return 'redirect: ' . urldecode($this->POST['return_url']);
    }
}