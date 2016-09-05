<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class page_settings extends BrandcoPOSTActionBase {
    protected $ContainerName = 'page_settings';
    protected $Form = array(
        'package' => 'admin-settings',
        'action' => 'page_settings_form',
    );
    protected $ValidatorDefinition = array(
        'dummy' => array()
    );

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    public function validate () {
        if (array_key_exists('public_settings', $this->POST) && $this->public_settings == 1) {

            // お問い合わせ通知先があるかどうか確認
            $inquiry_validator = new InquiryValidator();
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND, array('brand_id' => $this->brand->id))) {
                return false;
            }
            $inquiry_brand = $inquiry_validator->getEntityCache(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND);
            if (!$inquiry_validator->isExistedRecord(InquiryValidator::ENTITY_TYPE_INQUIRY_BRAND_RECEIVER, array('inquiry_brand_id' => $inquiry_brand->id))) {
                $this->Validator->setError('inquiry_brand_receiver', 'NOT_OWNER');
            }

            // ページタイトルがあるかどうか確認
            if (!$this->brand->name) {
                $this->Validator->setError('name', 'NOT_OWNER');
            }

            // プロフィール画像があるかどうか確認
            if (!$this->brand->profile_img_url) {
                $this->Validator->setError('profile_img_url', 'NOT_OWNER');
            }
        }

        return !$this->Validator->getErrorCount();
    }
    
    function doAction() {

        $page_setting_service = $this->createService('BrandPageSettingService');

        if(array_key_exists('tag_text', $this->POST)) {
            $page_setting_service->setTagPageSettings($this->brand->id, $this->tag_text);
        }

        if(array_key_exists('header_tag_text', $this->POST)) {
            $page_setting_service->setHeaderTagPageSettings($this->brand->id, $this->header_tag_text);
        }

        if(array_key_exists('public_settings', $this->POST)) {
            $page_setting_service->setPublicPageSettings($this->brand->id, $this->public_settings);
        }

        $this->assign('saved', 1);

        return 'redirect: '.Util::rewriteUrl('admin-settings', 'page_settings_form', array(), array('mid' => 'updated'));
    }
}
