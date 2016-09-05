<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.BrandUploadFile');

class create_static_html_entry_form extends BrandcoGETActionBase {
    protected $ContainerName = 'create_static_html_entry';

    public $NeedOption = array(BrandOptions::OPTION_CMS);
    public $NeedAdminLogin = true;

    public function beforeValidate() {
        $this->deleteErrorSession();
    }

    public function validate() {
        return true;
    }

    function doAction() {

        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_USE_STAMP_RALLY_TEMPLATE);
        $this->Data['can_use_stamp_rally'] = !Util::isNullOrEmpty($brand_global_setting);

        $this->Data['can_use_embed_page'] = $this->canAddEmbedPage();

        /** @var StaticHtmlCategoryService $static_html_tag_service */
        $static_html_tag_service = $this->createService('StaticHtmlCategoryService');
        $this->Data['categories_tree'] = $static_html_tag_service->getCategoriesTree($this->brand->id);

        $action_form['public_date'] = $this->getToday();
        $action_form['public_time_hh'] = date('H', time());
        $action_form['public_time_mm'] = date('i', time());
        $action_form['layout_type'] = StaticHtmlEntries::LAYOUT_NORMAL;
        $action_form['write_type'] = StaticHtmlEntries::WRITE_TYPE_BLOG;

        // Comment Plugins
        $this->Data['has_comment_option'] = $this->hasOption(BrandOptions::OPTION_COMMENT,false);
        if ($this->Data['has_comment_option']) {
            $action_form['cp_status'] = CommentPlugin::COMMENT_PLUGIN_STATUS_PRIVATE;
        }

        $this->assign('ActionForm', $action_form);
        return 'user/brandco/admin-blog/create_static_html_entry_form.php';
    }
}
