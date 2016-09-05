<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class page extends BrandcoGETActionBase {

    public $NeedOption = array(BrandOptions::OPTION_CMS);
    public $NeedRedirect = true;
    private $static_html_entry_service;
    private $staticHtmlEntry;
    private $content;

    public function doThisFirst(){

        $brand = $this->getBrand();
        if (!$this->preview || $this->preview == StaticHtmlEntries::DEFAULT_PREVIEW_MODE) {

            $this->Data['pageUrl'] = $this->GET['exts'][0];
            $this->static_html_entry_service = $this->createService('StaticHtmlEntryService');

            $this->staticHtmlEntry = $this->static_html_entry_service->getEntryByBrandIdAndPageUrl($brand->id, $this->Data['pageUrl']);

            $this->staticHtmlEntry->body = base64_decode($this->staticHtmlEntry->encode_body);
            $this->staticHtmlEntry->extra_body = base64_decode($this->staticHtmlEntry->encode_extra_body);
        } else {
            $cache_manager = new CacheManager(CacheManager::PREVIEW_PREFIX);
            $this->content = $cache_manager->getCache(CacheManager::PAGE_PREVIEW_KEY, array($brand->id));
        }
    }

	public function validate () {
        if(!$this->preview && !$this->static_html_entry_service->isActivePage($this->staticHtmlEntry)) {
            return '404';
        }

        if($this->staticHtmlEntry->embed_flg){
            return '404';
        }

		return true;
	}

	function doAction() {

        // プレーンモード
        if ($this->staticHtmlEntry->layout_type == StaticHtmlEntries::LAYOUT_PLAIN
            || $this->content['layout_type'] == StaticHtmlEntries::LAYOUT_PLAIN) {

            if ($this->preview && $this->preview != StaticHtmlEntries::DEFAULT_PREVIEW_MODE) {
                $this->staticHtmlEntry->body = $this->content['body'];
            }

            // 共通フッターのタグ置換
            if (strpos($this->staticHtmlEntry->body, '<#moniplaFooter>')) {
                $parser = new PHPParser();
                $html = $parser->parseTemplate('PageTagMoniplaFooter.php', ['brand' => $this->getBrand()]);
                $this->staticHtmlEntry->body = str_replace('<#moniplaFooter>', $html, $this->staticHtmlEntry->body);
            }

            $this->Data['staticHtmlEntry'] = $this->staticHtmlEntry;
            return 'user/brandco/plain_page.php';
        }

        if ($this->canSaveBrandUserRelationNo()) {
            if ($this->isLogin()) {
                setcookie("bur_no", $this->getBrandsUsersRelation()->no);
            } else {
                setcookie("bur_no", null);
            }
        }

        $cur_og_url = $this->getCurOgUrl();
        $this->Data['pageStatus']['brand_info'] = $this->getFanCountInfo();
        $this->Data['pageStatus']['can_use_fan_count_markdown'] = $this->canUseFanCountMarkdown();
        $user_id = $this->getSession('pl_monipla_userId');

        /** @var StaticHtmlEntryService $static_html_entry_service */
        $static_html_entry_service = $this->createService('StaticHtmlEntryService');
        $this->Data['staticHtmlEntry'] = $this->staticHtmlEntry;

        // Comment Plugin
        $this->Data['has_comment_option'] = $this->hasOption(BrandOptions::OPTION_COMMENT, false);

        if (!$this->preview || $this->preview == StaticHtmlEntries::DEFAULT_PREVIEW_MODE) {

            $this->Data['pageStatus']['keyword'] = $this->Data['staticHtmlEntry']->meta_keyword;

            $this->Data['pageStatus']['og']['title'] = $this->Data['staticHtmlEntry']->title . ' / ' . $this->brand->name;

            if ($this->Data['staticHtmlEntry']->meta_title) {
                $this->Data['pageStatus']['og']['title'] = $this->Data['staticHtmlEntry']->meta_title;
            }
            if ($this->Data['staticHtmlEntry']->meta_description) {
                $this->Data['pageStatus']['og']['description'] = $this->Data['staticHtmlEntry']->meta_description;
            }
            if ($this->Data['staticHtmlEntry']->og_image_url) {
                $this->Data['pageStatus']['og']['image'] = $this->Data['staticHtmlEntry']->og_image_url;
            }
            $this->Data['pageStatus']['og']['url'] = $cur_og_url;
            $this->Data['pageStatus']['og']['site_name'] = $this->brand->name;

            $this->Data['staticHtmlEntry'] = $this->Data['staticHtmlEntry']->toArray();

            if($this->canUseFanCountMarkdown() && $this->Data['staticHtmlEntry']['write_type'] == StaticHtmlEntries::WRITE_TYPE_BLOG){

                $this->Data['staticHtmlEntry']['body'] = $static_html_entry_service->evalFanCountMarkdown($this->Data['staticHtmlEntry']['body'],$this->Data['pageStatus']['brand_info']['users_num']);

                $this->Data['staticHtmlEntry']['extra_body'] = $static_html_entry_service->evalFanCountMarkdown($this->Data['staticHtmlEntry']['extra_body'],$this->Data['pageStatus']['brand_info']['users_num']);

            }elseif($this->Data['staticHtmlEntry']['write_type'] == StaticHtmlEntries::WRITE_TYPE_TEMPLATE) {

                $this->static_html_entry_template_service = $this->createService('StaticHtmlEntryTemplateService');
                $this->Data['staticHtmlEntry']['template_contents_json'] = $this->static_html_entry_template_service->getTemplateJsonByEntryId($this->Data['staticHtmlEntry']['id']);
                if($this->canUseStampRallyTemplate()){
                    $this->Data['pageStatus']['joined_cp_count'] = $static_html_entry_service->getUserJoinedStampRallyCpCount($this->Data['staticHtmlEntry']['template_contents_json'],$user_id );
                }
            }

            $sns_plugins = $static_html_entry_service->getStaticHtmlSnsPluginsByEntryId($this->Data['staticHtmlEntry']['id']);
            $this->Data['sns_plugin_ids'] = array();
            foreach ($sns_plugins as $sns_plugin) {
                $this->Data['sns_plugin_ids'][] = $sns_plugin->sns_plugin_id;
            }

            /** @var StaticHtmlCategoryService $static_category_service */
            $static_category_service = $this->createService('StaticHtmlCategoryService');
            $category = $static_category_service->getCategoryByEntryId($this->Data['staticHtmlEntry']['id']);
            if ($category) {
                $this->setNavigationData($category->current()->category_id);
            }

            if ($this->Data['has_comment_option']) {
                /** @var CommentPluginService $comment_plugin_service */
                $comment_plugin_service = $this->getService('CommentPluginService');
                $this->Data['comment_plugin'] = $comment_plugin_service->getActiveCommentPlugin($this->getBrand()->id, $this->Data['staticHtmlEntry']['id']);
            }
        } else {
            try {
                $content = $this->content;

                $this->Data['staticHtmlEntry']['title'] = $content['title'];
                $this->Data['staticHtmlEntry']['keyword'] = $content['keyword'];
                $this->Data['staticHtmlEntry']['sns_plugin_tag_text'] = $content['custom_plugin'];
                $this->Data['staticHtmlEntry']['title_hidden_flg'] = $content['title_hidden_flg'];
                $this->Data['staticHtmlEntry']['layout_type'] = $content['layout_type'];
                $this->Data['staticHtmlEntry']['write_type'] = $content['write_type'];

                if($this->Data['staticHtmlEntry']['write_type'] == StaticHtmlEntries::WRITE_TYPE_BLOG) {

                    if($this->canUseFanCountMarkdown()){

                        $this->Data['staticHtmlEntry']['body'] = $static_html_entry_service->evalFanCountMarkdown($content['body'], $this->Data['pageStatus']['brand_info']['users_num']);

                        $this->Data['staticHtmlEntry']['extra_body'] = $static_html_entry_service->evalFanCountMarkdown($content['extra_body'], $this->Data['pageStatus']['brand_info']['users_num']);

                    } else {

                        $this->Data['staticHtmlEntry']['body'] = $content['body'];

                        $this->Data['staticHtmlEntry']['extra_body'] = $content['extra_body'];
                    }

                }else if($this->Data['staticHtmlEntry']['write_type'] == StaticHtmlEntries::WRITE_TYPE_TEMPLATE) {
                    $this->Data['staticHtmlEntry']['template_contents_json'] = $content['template_contents_json'];

                    if($this->canUseStampRallyTemplate()) {
                        $this->Data['pageStatus']['joined_cp_count'] = $static_html_entry_service->getUserJoinedStampRallyCpCount($this->Data['staticHtmlEntry']['template_contents_json'], $user_id);
                    }
                }

                $this->Data['pageStatus']['og']['title'] = $this->Data['staticHtmlEntry']->meta_title ? $this->Data['staticHtmlEntry']->meta_title : $this->Data['staticHtmlEntry']->title . ' / ' . $this->brand->name;
                $this->Data['pageStatus']['og']['description'] = $this->Data['staticHtmlEntry']->meta_description;
                $this->Data['pageStatus']['og']['image'] = $this->Data['staticHtmlEntry']->og_image_url;
                $this->Data['pageStatus']['og']['url'] = $cur_og_url;
                $this->Data['sns_plugin_ids'] = $content['sns_plugin'];

                if ($content['category_id']) {
                    $this->setNavigationData($content['category_id']);
                }

                if ($this->Data['has_comment_option']) {
                    /** @var CommentPluginService $comment_plugin_service */
                    $comment_plugin_service = $this->getService('CommentPluginService');
                    $this->Data['comment_plugin'] = $comment_plugin_service->createEmptyCommentPlugin();
                    $this->Data['comment_plugin']->status = $content['cp_status'];

                    $php_parser = new PHPParser();
                    /** @var UserService $user_service */
                    $user_service = $this->getService('UserService');
                    $cur_user = $user_service->getUserPublicInfoByMoniplaUserId($this->Data['pageStatus']['userInfo']->id);

                    $this->Data['comment_plugin']->from = array(
                        'name' => $cur_user->name,
                        'profile_img_url' => $cur_user->profile_image_url ?: $php_parser->setVersion('/img/base/imgUser1.jpg'),
                        'share_sns_list' => $content['cp_sns_list']
                    );
                }
            } catch (Exception $e) {
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error($e);
            }
        }

        $this->Data['pageStatus']['script'] = array('PageService');
        return 'user/brandco/page.php';
    }

    private function setNavigationData($category_id) {
        /** @var StaticHtmlCategoryService $static_category_service */
        $static_category_service = $this->createService('StaticHtmlCategoryService');

        /** @var StaticHtmlEntryService $static_html_entry_service */
        $static_html_entry_service = $this->createService('StaticHtmlEntryService');

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');

        if ($category_id) {
            // 現在のカテゴリー情報取得
            $current_category = $static_category_service->getCategoryById($category_id);
            $this->Data['current_category_name'] = $current_category->name;
            $this->Data['current_category_url'] = $static_category_service->getUrlByCategory($current_category);
            $this->Data['father_category'] = $static_category_service->getParentOfCategory($current_category->id);

            $current_category_post_ids = $static_category_service->getPostsByCategoryId($current_category->id);
            $relation_post_ids = array_diff($current_category_post_ids, array($this->Data['staticHtmlEntry']['id']));
            if (count($relation_post_ids) > 0) {
                $this->Data['current_category_posts'] = $static_html_entry_service->getPublicEntryByIds($relation_post_ids, 1, 4);
                //日付の表示ON/OFF
                $this->Data['hidden_date_flg'] = $brand_global_setting_service->getBrandGlobalSetting($this->Data['brand']->id, BrandGlobalSettingService::CMS_CATEGORY_LIST_DATETIME_HIDDEN);
            }

            if ($this->Data['father_category']) {
                $this->Data['grandfather_category'] = $static_category_service->getParentOfCategory($this->Data['father_category']->id);
            }

        }
    }

    private function canUseFanCountMarkdown(){

        /** BrandGlobalSettingService $brand_global_settings_service */
        $brand_global_settings_service = $this->createService('BrandGlobalSettingService');

        if($brand_global_settings_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_USE_FAN_COUNT_MARKDOWN)){
            return true;
        }

        return false;
    }

    private function canUseStampRallyTemplate(){
        /** BrandGlobalSettingService $brand_global_settings_service */
        $brand_global_settings_service = $this->createService('BrandGlobalSettingService');
        $stamp_rally_template_setting = $brand_global_settings_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_USE_STAMP_RALLY_TEMPLATE);

        if(Util::isNullOrEmpty($stamp_rally_template_setting)){
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function canSaveBrandUserRelationNo() {

        $brand_global_settings_service = $this->getService('BrandGlobalSettingService');

        if ($brand_global_settings_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_SAVE_BRAND_USER_RELATION_NO)) {
            return true;
        }

        return false;
    }

    private function getCurOgUrl() {
        if (!Util::isBaseUrl()) return Util::getCurrentUrl();

        //トップページ差替えのOG:URL対応
        $brand_page_settings = BrandInfoContainer::getInstance()->getBrandPageSetting();

        if ($brand_page_settings->top_page_url) {
            $top_page_og_url = Util::getUrlFromPath($brand_page_settings->top_page_url);

            if ($top_page_og_url) return $top_page_og_url;
        }

        return Util::getCurrentUrl();
    }
}