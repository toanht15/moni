<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
class categories extends BrandcoGETActionBase {

    public $NeedOption = array();
    private $category_directory;

    public function beforeValidate () {
        $this->category_directory = end($this->GET['exts']);
        $this->Data['brand'] = $this->getBrand();

        /** @var StaticHtmlCategoryService $category_service */
        $category_service = $this->createService('StaticHtmlCategoryService');
        $this->Data['current_category'] = $category_service->getCategoryByDirectoryAndBrandId($this->category_directory, $this->brand->id);

        if (!$this->preview || $this->preview == StaticHtmlEntries::DEFAULT_PREVIEW_MODE) {
            if (!$this->category_directory) return '404';
            if (!$this->Data['current_category']) return '404';
        }
    }

    public function validate () {
        return true;
    }

    function doAction() {

        /** @var StaticHtmlCategoryService $category_service */
        $category_service = $this->createService('StaticHtmlCategoryService');

        /** @var StaticHtmlEntryService $static_entry_service */
        $static_entry_service = $this->createService('StaticHtmlEntryService');

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->createService('BrandGlobalSettingService');

        if ($this->preview == StaticHtmlEntries::SESSION_PREVIEW_MODE) {
            try {
                $cache_manager = new CacheManager(CacheManager::PREVIEW_PREFIX);
                $this->Data['current_category'] = $cache_manager->getCache(CacheManager::CATEGORIES_PREVIEW_KEY, array($this->brand->id));
                $this->Data['father_category'] = $category_service->getCategoryById($this->Data['current_category']['parent_id']);

                if ($this->Data['current_category']['is_use_customize']) {
                    $this->Data['current_category']['customize_code'] = str_replace('%title%', $this->Data['current_category']['name'], $this->Data['current_category']['customize_code']);

                    $body_split = explode('%loop_start%', $this->Data['current_category']['customize_code']);
                    $this->Data['loop_before'] = $body_split[0];
                    $body_split = explode('%loop_end%', $body_split[1]);
                    $this->Data['loop'] = $body_split[0];
                    $this->Data['loop_after'] = $body_split[1];
                }

            } catch (Exception $e) {
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error($e);
            }

        } else {
            $sns_plugins = $category_service->getStaticHtmlCategorySnsPlugins($this->Data['current_category']->id);
            $this->Data['sns_plugin_ids'] = array();
            foreach ($sns_plugins as $sns_plugin) {
                $this->Data['sns_plugin_ids'][] = $sns_plugin->sns_plugin_id;
            }

            $this->Data['father_category'] = $category_service->getParentOfCategory($this->Data['current_category']->id);

            if ($this->Data['current_category']->description) {
                $this->Data['pageStatus']['og']['description'] = $this->Data['current_category']->description;
            }
            if ($this->Data['current_category']->og_image_url) {
                $this->Data['pageStatus']['og']['image'] = $this->Data['current_category']->og_image_url;
            }
            if ($this->Data['current_category']->keyword) {
                $this->Data['pageStatus']['keyword'] = $this->Data['current_category']->keyword;
            }
            if ($this->Data['current_category']->title) {
                $this->Data['pageStatus']['og']['title'] = $this->Data['current_category']->title;
            } else if ($this->Data['current_category']->name) {
                $this->Data['pageStatus']['og']['title'] = $this->Data['current_category']->name;
            }

            $this->Data['pageStatus']['og']['url'] = Util::getCurrentUrl();

            $this->Data['current_category'] = $this->Data['current_category']->toArray();
        }

        if ($this->Data['current_category']) {
            $static_entries = $category_service->getAllPostByCategoryId($this->Data['current_category']['id']);
        } else {
            $static_entries = array();
        }

        if (count($static_entries) > 0) {
            if ($this->preview == StaticHtmlEntries::SESSION_PREVIEW_MODE && ($this->Data['current_category']['id'] == '259' || ($this->Data['father_category'] && $this->Data['father_category']->id == '259'))) {
                $this->Data['static_entry_count'] = $static_entry_service->countAllEntryByIdsWithPager($static_entries, $this->p, StaticHtmlCategory::CATEGORY_PAGE_LIMIT);
                $this->Data['static_entries'] = $static_entry_service->getAllEntryByIds($static_entries, $this->p, StaticHtmlCategory::CATEGORY_PAGE_LIMIT);
            } else {
                $this->Data['static_entry_count'] = $static_entry_service->countPublicEntryByIdsWithPager($static_entries, $this->p, StaticHtmlCategory::CATEGORY_PAGE_LIMIT);
                $this->Data['static_entries'] = $static_entry_service->getPublicEntryByIds($static_entries, $this->p, StaticHtmlCategory::CATEGORY_PAGE_LIMIT);
            }
            //日付の表示ON/OFF
            //TODO オリンパスブランドのハードコーディング、カテゴリ毎の日付表示ON/OFFができたら削除予定
            if($this->Data['current_category']['id'] == '259' || ($this->Data['father_category'] && $this->Data['father_category']->id == '259')){
                $this->Data['hidden_date_flg'] = FALSE;
            }else{
                $this->Data['hidden_date_flg'] = $brand_global_setting_service->getBrandGlobalSetting($this->Data['brand']->id, BrandGlobalSettingService::CMS_CATEGORY_LIST_DATETIME_HIDDEN);
            }

            if (in_array($this->Data['current_category']['id'], array('259', '231'))) {
                $this->Data['pageStatus']['olympus_tag'] = TRUE;
            }
        }

        //ページング
        $this->Data['totalEntriesCount'] = $this->Data['static_entry_count'] ? $this->Data['static_entry_count'] : 0;
        $this->Data['total_page'] = floor ( $this->Data['totalEntriesCount'] / StaticHtmlCategory::CATEGORY_PAGE_LIMIT ) + ( $this->Data['totalEntriesCount'] % StaticHtmlCategory::CATEGORY_PAGE_LIMIT > 0 );
        $this->p = Util::getCorrectPaging($this->p, $this->Data['total_page']);

        $this->Data['pageLimited'] = StaticHtmlCategory::CATEGORY_PAGE_LIMIT;

        if ($this->Data['father_category']) {
            $this->Data['grandfather_category'] = $category_service->getParentOfCategory($this->Data['father_category']->id);
        }

        $this->Data['pageStatus']['script'] = array('PageService');
        $this->Data['pageStatus']['brand_info'] = $this->getFanCountInfo();

        // UQ用カテゴリ一覧のハードコーディング
        if ($this->Data['pageStatus']['is_uq_account'] == 1) {
            $this->setUQViewParams();

            if ($this->preview) {
                return 'user/brandco/categories_preview.php';
            } else {
                return 'user/brandco/categories_uq.php';
            }
        }

        if ($this->preview) {
            return 'user/brandco/categories_preview.php';
        } else {
            return 'user/brandco/categories.php';
        }
    }

    private function setUQViewParams() {
        /** @var PageCategoryRelationService $page_category_relation_service */
        $page_category_relation_service = $this->getService('PageCategoryRelationService');
        /** @var CommentPluginService $comment_plugin_service */
        $comment_plugin_service = $this->getService('CommentPluginService');

        foreach ($this->Data['static_entries'] as $static_entry) {
            $this->Data['static_entries_categories'][$static_entry->id] = $page_category_relation_service->getCategoriesInfo($static_entry->id);
            $this->Data['static_entries_comment_count'][$static_entry->id] = $comment_plugin_service->countAvailableCommentByStaticHtmlEntryId($this->getBrand()->id, $static_entry->id);
        }
    }
}
