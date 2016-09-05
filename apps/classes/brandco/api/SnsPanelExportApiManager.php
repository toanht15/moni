<?php
AAFW::import('jp.aainc.classes.validator.api.ExportAPIValidator');
AAFW::import('jp.aainc.classes.brandco.api.base.ContentExportApiManagerBase');

class SnsPanelExportApiManager extends ContentExportApiManagerBase {

    const NOT_SHOW_PANEL = 0;       //パネルを表示しないフラグ

    private $p;
    private $total_count;
    private $top_page_service;
    private $api_params;

    public function __construct($init_data) {
        parent::__construct($init_data);
        $this->p = $init_data['p'] ? intval($init_data['p']) : 1;
        $this->top_page_service = $this->service_factory->create('TopPageService');
    }

    public function validate() {
        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->service_factory->create('BrandGlobalSettingService');
        $can_use_brand_global_setting = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(), BrandGlobalSettingService::CAN_USE_SNS_PANELS_API);

        if (!$can_use_brand_global_setting) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'Permission Denied!'));
            return $json_data;
        }

        if ($this->callback && strlen($this->callback) > 512) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'Invalid callback: ' . $this->callback));
            return $json_data;
        }

        $export_api_validator = new ExportAPIValidator($this->getBrand()->id, $this->code, ExportAPIValidator::TYPE_SNS_PANEL_API);

        if (!$export_api_validator->validate()) {
            $json_data = $this->createResponseData('ng', array(), $export_api_validator->getErrors());
            return $json_data;
        }

        $this->api_code = $export_api_validator->getApiCode();

        //APIのパラメーターを取得する
        $this->api_params = json_decode($this->api_code->extra_data);

        //APIのパラメーターのバリデータ
        $brand_social_account_ids = $this->api_params->brand_social_account_ids;
        $rss_stream_ids = $this->api_params->rss_ids;

        $is_validate_brand_social_account_ids = $this->validateBrandSocialAccountIds($brand_social_account_ids);
        $is_validate_rss_stream_ids = $this->validateRssStreamIds($rss_stream_ids);

        if(!$is_validate_brand_social_account_ids || !$is_validate_rss_stream_ids) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'Invalid Parameter!'));
            return $json_data;
        }

        return true;
    }

    public function doSubProgress() {
        $panels = $this->getCurrentPanels();

        $response_data = $this->getApiExportData($panels);

        /** @var SnsPanelApiCodeService $api_code_service */
        $api_code_service = $this->service_factory->create('SnsPanelApiCodeService');

        $check_next_url = $this->total_count - ($this->limit) * ($this->p);
        $next_url = $check_next_url > 0 ? $api_code_service->getUrl($this->code, $this->limit, $this->p + 1) : null;

        $pagination = array();
        if ($next_url) {
            $pagination['next_url'] = $next_url;
        }

        $json_data = $this->createResponseData('ok', $response_data, array(), $pagination);
        return $json_data;
    }

    /**
     * @return array
     */
    private function getCurrentPanels() {
        $brand_id = $this->getBrand()->id;
        $cache_manager = new CacheManager();

        if ($this->p == 1) {
            //キャッシュクリア
            $cache_manager->deleteSnsPanelCache($brand_id);

            $panel_list = $this->getAllPanelsByBrandId($brand_id);

            //キャッシュ追加
            $cache_manager->addSnsPanelCache($brand_id, $panel_list);
        } else {
            //キャッシュ取得
            $panel_list = $cache_manager->getSnsPanelCache($brand_id);

            if ($panel_list == null) {
                $panel_list = $this->getAllPanelsByBrandId($brand_id);
                $cache_manager->addSnsPanelCache($brand_id, $panel_list);
            }
        }

        $this->total_count = count($panel_list);

        //ページによってパネルを取得する
        $offset = ($this->p - 1) * $this->limit;
        $current_panels = array_slice($panel_list, $offset, $this->limit);

        return $current_panels;
    }

    /**
     * @param $brand_id
     * @return array
     */
    private function getAllPanelsByBrandId($brand_id) {
        $sns_ids = $this->api_params->brand_social_account_ids ?: array();
        $rss_stream_ids = $this->api_params->rss_ids ?: array();

        $sns_panels = $this->getSnsPanels($brand_id, $sns_ids);
        $rss_panels = $this->getRssPanels($brand_id, $rss_stream_ids);
        $page_panels = $this->api_params->page ? $this->getPagePanels($brand_id) : array();
        $link_panels = $this->api_params->link ? $this->getLinkPanels($brand_id) : array();
        $photo_panels = $this->api_params->photo ? $this->getPhotoPanels($brand_id) : array();

        $panel_list = array_merge($sns_panels, $rss_panels, $page_panels, $link_panels, $photo_panels);
        $panel_list = $this->sortPanels($panel_list);

        return $panel_list;
    }

    /**
     * @param $brand_id
     * @param $sns_ids
     * @return array
     */
    private function getSnsPanels($brand_id, $sns_ids) {
        /** @var BrandSocialAccountService $brand_social_account_service */
        $brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');
        $sns_panels = array();

        if (count($sns_ids) == 0) {
            $brand_social_accounts = $brand_social_account_service->getBrandSocialAccountByBrandId($brand_id);
            $brand_social_account_ids = array();

            foreach ($brand_social_accounts as $brand_social_account) {
                $brand_social_account_ids[] = $brand_social_account->id;
            }

        } else {
            //$sns_ids={0}場合は、SNSパネルを表示しない
            if(count($sns_ids) == 1 && $sns_ids[0] == self::NOT_SHOW_PANEL) {
                return $sns_panels;
            }

            $brand_social_account_ids = $sns_ids;
        }

        foreach ($brand_social_account_ids as $brand_social_account_id) {
            $stream = $brand_social_account_service->getStreamByBrandSocialAccountId($brand_social_account_id);
            $brand_social_account = $brand_social_account_service->getBrandSocialAccountById($brand_social_account_id);
            $stream_service = $this->service_factory->create(get_class($stream) . 'Service');

            $entries = $stream_service->getAllEntriesByStreamIdAndHiddenFlg($stream->id, 0);

            foreach ($entries as $entry) {
                $panel = array();
                $panel['display_priority'] = $entry->priority_flg;
                $panel['entry'] = $entry->asArray();
                $panel['entry']['page_link'] = Util::rewriteUrl('sns', 'detail', array($brand_social_account_id, $entry->id));
                $panel['entry']['pub_date'] = $entry->pub_date;
                $panel['stream_name'] = get_class($entry);
                $panel['brand_social_account_id'] = $brand_social_account_id;
                $panel['image_url'] = $brand_social_account->picture_url;

                if (get_class($stream) == "TwitterStream") {
                    $panel['page_name'] = $brand_social_account->name . '@' . $brand_social_account->screen_name;
                    $panel['screen_name'] = $brand_social_account->screen_name;
                } else {
                    $panel['page_name'] = $brand_social_account->name;
                }

                $panel['updated_at'] = $entry->updated_at;

                if ($this->getBrand()->isViewFullText()) {
                    $panel['entry']['panel_text'] = $entry->getFullText();
                }

                $sns_panels[] = $panel;
            }
        }

        return $sns_panels;
    }

    /**
     * @param $brand_id
     * @param array $stream_ids
     * @return array
     */
    private function getRssPanels($brand_id, $stream_ids = array()) {
        /** @var RssStreamService $rss_stream_service */
        $rss_stream_service = $this->service_factory->create('RssStreamService');
        $rss_panels = array();

        if (count($stream_ids) == 0) {
            $rss_streams = $rss_stream_service->getStreamByBrandId($brand_id);

            foreach ($rss_streams as $stream) {
                $stream_ids[] = $stream->id;
            }
        } else if(count($stream_ids) == 1 && $stream_ids[0] == self::NOT_SHOW_PANEL) { //$stream_ids={0}場合は、Rssパネルを表示しない
            return $rss_panels;
        }

        foreach ($stream_ids as $stream_id) {
            $rss_entries = $rss_stream_service->getAllEntriesByStreamIdAndHiddenFlg($stream_id, 0);
            $stream = $rss_stream_service->getStreamById($stream_id);

            foreach ($rss_entries as $entry) {
                $panel = array();
                $panel['display_priority'] = $entry->priority_flg;
                $panel['entry'] = $entry->asArray();
                $panel['entry']['pub_date'] = $entry->pub_date;
                $panel['stream_name'] = "RssEntry";
                $panel['rss_title'] = $stream->title;
                $panel['updated_at'] = $entry->updated_at;

                if ($this->getBrand()->isViewFullText()) {
                    $panel['entry']['panel_text'] = $entry->getFullText();
                }

                $rss_panels[] = $panel;
            }
        }

        return $rss_panels;
    }

    /**
     * @param $brand_id
     * @return array
     */
    private function getPagePanels($brand_id) {
        /** @var PageStreamService $page_stream_service */
        $page_stream_service = $this->service_factory->create('PageStreamService');
        /** @var StaticHtmlCategoryService $static_html_category_service */
        $static_html_category_service = $this->service_factory->create('StaticHtmlCategoryService');

        $page_stream = $page_stream_service->getStreamByBrandId($brand_id);
        $page_panels = array();

        $page_entries = $page_stream_service->getAllEntriesByStreamIdAndHiddenFlg($page_stream->id, 0);

        foreach ($page_entries as $entry) {
            $panel = array();
            $panel['display_priority'] = $entry->priority_flg;
            $panel['entry'] = $entry->asArray();
            $panel['entry']['pub_date'] = $entry->pub_date;
            $panel['stream_name'] = "PageEntry";
            $panel['new_page'] = $entry->isNewPagePanel();
            $panel['updated_at'] = $entry->updated_at;

            $category = $static_html_category_service->getStaticHtmlCategoryByStaticHtmlEntryId($entry->static_html_entry_id);
            $panel['category'] = $category ? $category->toArray() : null;

            $page_panels[] = $panel;
        }

        return $page_panels;
    }

    /**
     * @param $brand_id
     * @return array
     */
    private function getLinkPanels($brand_id) {
        /** @var LinkEntryService $link_stream_service */
        $link_entry_service = $this->service_factory->create('LinkEntryService');
        $panels = array();

        $link_entries = $link_entry_service->getAvailableEntriesByBrandId($brand_id);
        foreach ($link_entries as $entry) {
            $panel = array();
            $panel['display_priority'] = $entry->priority_flg;
            $panel['entry'] = $entry->asArray();
            $panel['entry']['pub_date'] = $entry->pub_date;
            $panel['stream_name'] = "LinkEntry";
            $panel['updated_at'] = $entry->updated_at;
            $panels[] = $panel;
        }

        return $panels;
    }

    /**
     * @param $brand_id
     * @return array
     */
    private function getPhotoPanels($brand_id) {
        /** @var PhotoStreamService $photo_stream_service */
        $photo_stream_service = $this->service_factory->create('PhotoStreamService');
        $photo_streams = $photo_stream_service->getStreamByBrandId($brand_id);
        $panels = array();

        foreach ($photo_streams as $photo_stream) {
            $photo_entries = $photo_stream_service->getAllEntriesByStreamIdAndHiddenFlg($photo_stream->id, 0);

            foreach ($photo_entries as $entry) {
                $panel = array();
                $panel['display_priority'] = $entry->priority_flg;
                $panel['entry'] = $entry->asArray();
                $panel['entry']['pub_date'] = $entry->pub_date;
                $panel['streamName'] = "PhotoEntry";
                $panel['updated_at'] = $entry->updated_at;
                $panels[] = $panel;
            }
        }

        return $panels;
    }

    /**
     * @param $panels
     * @return array
     */
    private function sortPanels($panels) {
        $top_panels = array();

        foreach ($panels as $key => $panel) {
            if ($panel['display_priority'] == '1') {
                $top_panels[] = $panel;
                unset($panels[$key]);
            }
        }

        usort($top_panels, function ($panel1, $panel2) {
            return strtotime($panel1['updated_at']) < strtotime($panel2['updated_at']);
        });
        usort($panels, function ($panel1, $panel2) {
            return strtotime($panel1['entry']['pub_date']) < strtotime($panel2['entry']['pub_date']);
        });

        return array_merge($top_panels, $panels);
    }

    /**
     * @param $data
     * @param null $brand
     * @return array
     */
    public function getApiExportData($data, $brand = null) {
        return array_values($data);
    }

    /**
     * APIの$brand_social_account_idsパラメーターのバリデータ
     * @param $brand_social_account_ids
     * @return bool
     */
    private function validateBrandSocialAccountIds ($brand_social_account_ids) {
        //brand_social_account_idのバリデータ
        if($brand_social_account_ids && !is_array($brand_social_account_ids)){
            return false;
        }

        //$brand_social_account_ids = {0}場合は、SNSパネルを表示しないので、バリデータする必要がない
        if(count($brand_social_account_ids) == 1 && $brand_social_account_ids[0] == self::NOT_SHOW_PANEL){
            return true;
        }

        foreach ($brand_social_account_ids as $brand_social_account_id) {
            /** @var BrandSocialAccountService $brand_social_account_service */
            $brand_social_account_service = $this->service_factory->create('BrandSocialAccountService');
            $brand_social_account = $brand_social_account_service->getBrandSocialAccountById($brand_social_account_id);

            if (!$brand_social_account || $brand_social_account->brand_id != $this->getBrand()->id) {
                return false;
            }
        }

        return true;
    }

    /**
     * APIのrss_stream_idsパラメーターのバリデータ
     * @param $rss_stream_ids
     * @return bool
     */
    private function validateRssStreamIds ($rss_stream_ids) {
        //rss_stream_idのバリデータ
        if($rss_stream_ids && !is_array($rss_stream_ids)){
            return false;
        }

        //$rss_stream_ids = {0}場合は、Rssパネルを表示しないので、バリデータする必要がない
        if(count($rss_stream_ids) == 1 && $rss_stream_ids[0] == self::NOT_SHOW_PANEL){
            return true;
        }

        foreach ($rss_stream_ids as $rss_stream_id) {
            /** @var RssStreamService $rss_stream_service */
            $rss_stream_service = $this->service_factory->create('RssStreamService');
            $rss_stream = $rss_stream_service->getStreamById($rss_stream_id);

            if (!$rss_stream || $rss_stream->brand_id != $this->getBrand()->id) {
                return false;
            }
        }

        return true;
    }
}