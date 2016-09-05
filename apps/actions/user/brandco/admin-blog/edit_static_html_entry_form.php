<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.entities.BrandUploadFile');
AAFW::import('jp.aainc.classes.services.UserService');

class edit_static_html_entry_form extends BrandcoGETActionBase {
    protected $ContainerName = 'edit_static_html_entry';

    public $NeedOption = array(BrandOptions::OPTION_CMS);
    public $NeedAdminLogin = true;
    /** @var StaticHtmlEntryService $static_html_entry_service */
    protected $static_html_entry_service;
    protected $static_html_entry_template_service;
    protected $staticHtmlEntry;

    public function beforeValidate() {
        $this->deleteErrorSession();
        $this->Data['entry_id'] = $this->GET['exts'][0];

        // entry_idチェック
        if (!$this->Data['entry_id']) return 'redirect:' . Util::rewriteUrl('admin-blog', 'static_html_entries');

        $this->static_html_entry_service = $this->createService('StaticHtmlEntryService');

        // entry取得
        $this->Data['brand'] = $this->getBrand();
        $staticHtmlEntry = $this->static_html_entry_service->getEntryByBrandIdAndEntryId( $this->Data['brand']->id, $this->Data['entry_id']);
        $staticHtmlEntry->body = base64_decode($staticHtmlEntry->encode_body); // encodeして出力
        $staticHtmlEntry->extra_body = base64_decode($staticHtmlEntry->encode_extra_body); // encodeして出力
        $this->staticHtmlEntry = $staticHtmlEntry;
    }

    public function validate() {

        // entryチェック
        $idValidator = new StaticEntryValidator(BrandcoValidatorBase::SERVICE_NAME_STATIC_HTML, $this->Data['brand']->id);
        if (!$idValidator->isCorrectEntryId($this->Data['entry_id'])){
            return false;
        }

        if($this->staticHtmlEntry->isEmbedPage()){
            return false;
        }

		return true;
	}

	function doAction() {

        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_USE_STAMP_RALLY_TEMPLATE);
        $this->Data['can_use_stamp_rally'] = !Util::isNullOrEmpty($brand_global_setting);

        $this->Data['can_use_embed_page'] = $this->canAddEmbedPage();

		$this->static_html_entry_service = $this->createService('StaticHtmlEntryService');

        $this->setAuthorUsers();

        // 公開日を設定
        $this->setPublicDate();

        if ($this->staticHtmlEntry->layout_type == StaticHtmlEntries::LAYOUT_PLAIN) {
            if (!$this->Data['pageStatus']['isLoginManager']) return 'redirect:' . Util::rewriteUrl('admin-blog', 'static_html_entries');

            $this->Data['entry'] = $this->staticHtmlEntry;
            $this->assign('ActionForm', $this->staticHtmlEntry->toArray());

            return 'user/brandco/admin-blog/edit_plain_static_html_entry_form.php';
        } else {
            // sns_plugin取得
            $sns_plugins = array();
            foreach ($this->staticHtmlEntry->getStaticHtmlSnsPlugins() as $sns_plugin) {
                $sns_plugins[] = $sns_plugin->sns_plugin_id;
            }
            $this->staticHtmlEntry->sns_plugins = $sns_plugins;

            // template取得
            $this->static_html_entry_template_service = $this->createService('StaticHtmlEntryTemplateService');
            $this->staticHtmlEntry->template_contents_json = $this->static_html_entry_template_service->getTemplateJsonByEntryId($this->staticHtmlEntry->id);

            /** @var StaticHtmlCategoryService $static_html_tag_service */
            $static_html_tag_service = $this->createService('StaticHtmlCategoryService');
            $this->Data['categories_tree'] = $static_html_tag_service->getCategoriesTree($this->brand->id);

            $categories = $static_html_tag_service->getCategoryByEntryId($this->Data['entry_id']);

            $this->Data['categories_id'] = array();
            foreach ($categories as $category) {
                $this->Data['categories_id'][] = $category->category_id;
            }

            // entryを変数へ格納
            $this->Data['entry'] = $this->staticHtmlEntry;

            // Comment Plugin
            $this->Data['has_comment_option'] = $this->hasOption(BrandOptions::OPTION_COMMENT,false);
            if ($this->Data['has_comment_option']) {
                /** @var CommentPluginService $comment_plugin_service */
                $comment_plugin_service = $this->getService('CommentPluginService');
                $comment_plugin = $comment_plugin_service->getCommentPlugin($this->getBrand()->id, $this->Data['entry_id']);

                if ($comment_plugin) {
                    $this->staticHtmlEntry->cp_status = $comment_plugin->status;
                    $this->staticHtmlEntry->cp_sns_list = $comment_plugin_service->getCommentPluginShareSnsList($comment_plugin->id);
                } else {
                    $this->staticHtmlEntry->cp_status = CommentPlugin::COMMENT_PLUGIN_STATUS_PRIVATE;
                }
            }

            // ActionFormに格納
            $this->assign('ActionForm', $this->staticHtmlEntry->toArray());

            return 'user/brandco/admin-blog/edit_static_html_entry_form.php';
        }
	}

    private function setAuthorUsers(){
        $this->Data['author'] = $this->static_html_entry_service->getAuthorStaticHtmlEntry($this->staticHtmlEntry);
    }

    private function setPublicDate() {
        if ($this->staticHtmlEntry->public_date == '0000-00-00 00:00:00') {
            $this->staticHtmlEntry->public_date = $this->getToday();
        }else{
            $public_date = $this->staticHtmlEntry->public_date;
            $this->staticHtmlEntry->public_date = $this->formatDate($public_date, 'YYYY/MM/DD');
            $this->staticHtmlEntry->public_time_hh = date('H', strtotime($public_date));
            $this->staticHtmlEntry->public_time_mm = date('i', strtotime($public_date));
        }
    }
}
