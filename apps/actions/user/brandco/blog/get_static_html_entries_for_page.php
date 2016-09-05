<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class get_static_html_entries_for_page extends BrandcoGETActionBase {

    public $NeedOption = array();

    public function validate() {
        if ($this->isEmpty($this->p) || $this->p < 1 || !$this->isNumeric($this->p)) return '403';
        return true;
    }

    function doAction() {

        /** @var StaticHtmlCategoryService $category_service */
        $category_service = $this->createService('StaticHtmlCategoryService');

        /** @var StaticHtmlEntryService $static_entry_service */
        $static_entry_service = $this->createService('StaticHtmlEntryService');

        $this->Data['current_category'] = $category_service->getCategoryByDirectoryAndBrandId($this->category_directory, $this->brand->id);

        if ($this->preview == StaticHtmlEntries::SESSION_PREVIEW_MODE) {
            try {
                $redis = aafwRedisManager::getRedisInstance();
                $key = StaticHtmlEntries::PREVIEW_PREFIX.":". $this->brand->id . ":".StaticHtmlEntries::CATEGORIES_PREVIEW_KEY;
                $this->Data['current_category'] = json_decode($redis->get($key), true);

            } catch (Exception $e) {
                $logger = aafwLog4phpLogger::getDefaultLogger();
                $logger->error($e);
            } finally {
                if ($redis) {
                    $redis->close();
                }
            }
        } else {
            $this->Data['current_category'] = $this->Data['current_category']->toArray();
        }

        if ($this->Data['current_category']) {
            $static_entries = $category_service->getAllPostByCategoryId($this->Data['current_category']['id']);
        } else {
            $static_entries = array();
        }

        if (count($static_entries) > 0) {
            $this->Data['static_entries'] = $static_entry_service->getPublicEntryByIds($static_entries, $this->p, StaticHtmlCategory::CATEGORY_PAGE_LIMIT);
        }

        $this->Data['pageLimited'] = StaticHtmlCategory::CATEGORY_PAGE_LIMIT;

        return 'user/brandco/blog/get_static_html_entries_for_page.php';
    }
}