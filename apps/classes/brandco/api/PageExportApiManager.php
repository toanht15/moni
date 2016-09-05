<?php
AAFW::import('jp.aainc.classes.brandco.api.base.ContentExportApiManagerBase');

/**
 * 記事のカテゴリー情報を取得するAPI
 * Class PageExportApiManager
 */
class PageExportApiManager extends ContentExportApiManagerBase {

    private $static_html_entry;
    private $page_url;

    public function __construct($init_data) {
        parent::__construct($init_data);
        $this->page_url = $init_data['page_url'];
    }

    public function validate() {
        if ($this->callback && strlen($this->callback) > 512) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'Invalid callback: ' . $this->callback));
            return $json_data;
        }

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->service_factory->create('BrandGlobalSettingService');
        $can_use_page_api = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_USE_PAGE_API);

        if (!$can_use_page_api) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'Permission Denied!'));
            return $json_data;
        }

        if (Util::isNullOrEmpty($this->page_url)) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'Page URL Empty'));
            return $json_data;
        }

        /** @var StaticHtmlEntryService $static_html_entry_service */
        $static_html_entry_service = $this->service_factory->create('StaticHtmlEntryService');
        $this->static_html_entry = $static_html_entry_service->getEntryByBrandIdAndPageUrl($this->getBrand()->id, $this->page_url);

        if (!$this->static_html_entry || $this->static_html_entry->hidden_flg == 1) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'ページが存在しません!'));
            return $json_data;
        }

        return true;

    }

    public function doSubProgress() {
        /** @var PageCategoryRelationService $page_category_relation_service */
        $page_category_relation_service = $this->service_factory->create('PageCategoryRelationService');

        $page_data = array();

        $page_data['id'] = $this->static_html_entry->id;
        $page_data['title'] = $this->static_html_entry->title;
        $page_data['url'] = $this->static_html_entry->getUrl();
        $page_data['public_date'] = $this->static_html_entry->public_date;
        $page_data['categories'] = $page_category_relation_service->getCategoriesInfo($this->static_html_entry->id);

        $response_data = $this->getApiExportData($page_data);
        $json_data = $this->createResponseData('ok', $response_data, array());
        return $json_data;
    }

    public function getApiExportData($data, $brand = null) {
        if (count($data) == 1) {
            return $data[0];
        }

        return $data;
    }
}