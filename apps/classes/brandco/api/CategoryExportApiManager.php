<?php
AAFW::import('jp.aainc.classes.brandco.api.base.ContentExportApiManagerBase');

class CategoryExportApiManager extends ContentExportApiManagerBase {

    protected $cur_category_ids;
    protected $limit;
    protected $db;

    private $end_point_url = "categories";
    private $static_html_category_service;
    private $paging = false;

    public function __construct($init_data) {
        parent::__construct($init_data);

        $category_ids = $init_data['ids'] ? urldecode($init_data['ids']) : null;
        $this->cur_category_ids = explode(",", $category_ids);

        $this->limit            = $init_data['limit'] && is_numeric($init_data['limit']) ? $init_data['limit'] : null;
        $this->static_html_category_service = $this->service_factory->create("StaticHtmlCategoryService");
        $this->db = aafwDataBuilder::newBuilder();
    }

    protected function validate() {
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->service_factory->create('BrandGlobalSettingService');
        $can_use_categories_api = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_USE_CATEGORIES_API);

        if (!$can_use_categories_api) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'Permission Denied!'));
            return $json_data;
        }

        if ($this->callback && strlen($this->callback) > 512) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'Invalid callback: ' . $this->callback));
            return $json_data;
        }

        if (count($this->cur_category_ids) == 0 || (count($this->cur_category_ids) == 1 && Util::isNullOrEmpty($this->cur_category_ids[0]))) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'Category IDs empty!'));
            return $json_data;
        }

        foreach ($this->cur_category_ids as $category_id) {
            if (Util::isNullOrEmpty($category_id) || !is_numeric($category_id)) {
                $json_data = $this->createResponseData('ng', array(), array('message' => 'Invalid Category ID: ' . $category_id));
                return $json_data;
            }

            $category = $this->static_html_category_service->getCategoryById($category_id);

            if (!$category) {
                $json_data = $this->createResponseData('ng', array(), array('message' => "category_id={$category_id}: カテゴリデータが存在しません!"));
                return $json_data;
            }

            if ($category->brand_id != $this->getBrand()->id) {
                $json_data = $this->createResponseData('ng', array(), array('message' => 'Permission Denied!'));
                return $json_data;
            }
        }

        return true;
    }

    public function doSubProgress() {
        if (count($this->cur_category_ids) == 1 && $this->limit) {
            $this->paging = true;
        }

        $categories_data = array();

        foreach ($this->cur_category_ids as $category_id) {
            $category_data = $this->getCategoryInfo($category_id);

            $child_categories = $this->getChildCategories($category_id);
            $category_data['child_categories'] = array_values($child_categories);

            list($pages, $pagination) = $this->getStaticHtmlEntriesByCategoryId($category_id, $this->paging);
            $category_data['pages'] = array_values($pages);

            $categories_data[] = $category_data;
        }

        $response_data = $this->getApiExportData($categories_data);
        $json_data = $this->createResponseData('ok', $response_data, array(), $pagination);
        return $json_data;
    }

    private function getCategoryInfo($category_id) {
        $category_info = array();

        $category = $this->static_html_category_service->getCategoryById($category_id);

        $category_info['id'] = $category->id;
        $category_info['name'] = $category->name;
        $category_info['description'] = $category->description;
        $category_info['keyword'] = $category->keyword;
        $category_info['created_at'] = $category->created_at;
        $category_info['url'] = $this->static_html_category_service->getUrlByCategory($category);
        $category_info['total_pages'] = $this->getTotalPages($category_info['id']);

        return $category_info;
    }

    /**
     * @param $category_id
     * @param bool $paging
     * @return mixed
     */
    private function getStaticHtmlEntriesByCategoryId($category_id, $paging = false) {
        if ($paging) {
            $pager = array(
                'page' => self::DEFAULT_PAGE,
                'count' => $this->limit ? $this->limit + 1 : null
            );
            $max_id = $this->max_id ? $this->max_id : null;
        } else {
            $max_id = null;
        }

        $params = array(
            'category_id' => $category_id,
            'max_id' => $max_id
        );

        $order = array(
            'name' => 'id',
            'direction' => 'desc'
        );

        $result = $this->db->getStaticHtmlEntriesByCategoryId($params, $order, $pager, true, 'StaticHtmlEntry');
        $list_entries = $result['list'];

        /** @var BrandOptionsService $brand_option_service */
        $brand_option_service = $this->service_factory->create('BrandOptionsService');

        /** @var CommentPluginService $comment_plugin_service */
        $comment_plugin_service = $this->service_factory->create('CommentPluginService');

        //コメント数やカテゴリ数を追加、エントリ情報順番を変更
        $static_html_entries = array();
        foreach ($list_entries as $entry) {
            $new_entry = array(
                'id'                => $entry->id,
                'title'             => $entry->title,
                'url'               => $entry->getUrl(),    //ページのURL
                'og_image_url'      => $entry->og_image_url
            );

            //ブランドオプションコメントがあるかどうかチェックする
            $has_option_comment = $brand_option_service->getBrandOptionByBrandIdAndOptionId($this->getBrand()->id, BrandOptions::OPTION_COMMENT);

            if ($has_option_comment) {
                $new_entry['comment_count'] = $comment_plugin_service->countAvailableCommentByStaticHtmlEntryId($this->getBrand()->id, $entry->id);
            }
            $new_entry['category_count'] = $this->countCategoriesByStaticHtmlEntryId($entry->id);
            $new_entry['created_at']     = $entry->created_at;

            $static_html_entries[] = $new_entry;
        }

        //Pagination
        $pagination = array();
        if ($paging) {
            if ($result['pager']['count'] >= $this->limit + 1) {
                $last_page = array_pop($static_html_entries);
                $pagination = array(
                    "next_id" => $last_page['id'],
                    "next_url" => $this->getApiUrl($category_id, $last_page['id'], $this->limit)
                );
            }

        }

        return array($static_html_entries, $pagination);
    }

    /**
     * @param $category_id
     * @return array
     */
    private function getChildCategories($category_id) {
        $result = array();

        $condition = array(
            'parent_id' => $category_id
        );

        $order = array(
            'name'      => 'order_no',
            'direction' => 'asc'
        );

        $child_categories_data = $this->db->getChildCategoriesInfoByParentId($condition, $order, null, false, "StaticHtmlCategory");

        //Add Url, pages to child categories data
        foreach ($child_categories_data as $data) {
            $category = $data->toArray();
            $category['url'] = $this->static_html_category_service->getUrlByCategory($data);
            $category['total_pages'] = $this->getTotalPages($category['id']);

            //Get lower child if have
            $hasChildren = $this->static_html_category_service->hasChildren($category['id']);
            if ($hasChildren) {
                $child_category = $this->getChildCategories($category['id']);
                $category['child_categories'] = array_values($child_category);
            }

            list($pages, $pagination) = $this->getStaticHtmlEntriesByCategoryId($category['id']);
            $category['pages'] = array_values($pages);

            $result[] = $category;
        }

        return $result;
    }

    /**
     * 記事のカテゴリー数のカウント
     * @param $entry_id
     * @return 件数
     */
    private function countCategoriesByStaticHtmlEntryId($entry_id) {
        /** @var StaticHtmlEntryCategories $static_html_entry_category_store */
        $static_html_entry_category_store = aafwEntityStoreFactory::create('StaticHtmlEntryCategories');

        return $static_html_entry_category_store->count(array('static_html_entry_id' => $entry_id));
    }

    private function getTotalPages($category_id) {
        $params = array(
            'category_id' => $category_id,
        );

        $result = $this->db->getCountStaticHtmlEntriesByCategoryId($params, null, null, false);
        $total_pages = $result[0]["COUNT(*)"];

        return $total_pages;
    }

    public function getApiExportData($data, $brand = null) {
        return array_values($data);
    }

    /**
     * @param $category_id
     * @param $max_id
     * @param int $limit
     * @return string
     */
    private function getApiUrl($category_id, $max_id, $limit) {
        $query_params = array(
            'ids' => $category_id
        );

        if (!Util::isNullOrEmpty($max_id)) {
            $query_params['next_id'] = $max_id;
        }

        if (!Util::isNullOrEmpty($limit)) {
            $query_params['limit'] = $limit;
        }

        $end_point_url = $this->end_point_url . '.json';

        return Util::rewriteUrl('api', $end_point_url, array(), $query_params);
    }

}