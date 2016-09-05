<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.services.BrandPageSettingService');

class save_page_settings_manager extends BrandcoPOSTActionBase {
    protected $ContainerName = 'page_settings';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'page_settings_form',
    );
    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'top_page_url' => array(
            'type' => 'str',
            'length' => 511
        ),
    );

    public function validate () {
        // トップページURL形式チェック
        if ($this->top_page_url) $this->validateTopPageUrl();

        return !$this->Validator->getErrorCount();
    }

    function doAction() {
        // トップページ差し替えURL更新
        /** @var BrandPageSettingService $brand_page_settings_service */
        $brand_page_settings_service = $this->createService('BrandPageSettingService');
        $brand_page_settings_service->updateTopPageUrl($this->brand->getBrandPageSetting(), $this->top_page_url);

        $this->assign('saved',1);
        return 'redirect: '.Util::rewriteUrl('admin-settings', 'page_settings_form', array(), array('mid' => 'updated'));
    }

    /**
     * トップページURLの形式チェック
     */
    private function validateTopPageUrl() {
        $path = explode('/', $this->top_page_url);
        // 最初が/か、brand名一致 以外はNG
        if( $this->top_page_url[0] !== '/' || (Util::haveDirectoryName($this->brand) && $path[1] !== $this->brand->directory_name)) {
            $this->Validator->setError('top_page_url', 'NOT_MATCHES');
        }
    }

}
