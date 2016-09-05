<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.lib.db.aafwRedisManager');
AAFW::import('jp.aainc.classes.CacheManager');

class TopPageService extends aafwServiceBase {

    const PANEL_TYPE_TOP = 'Top';
    const PANEL_TYPE_NORMAL = 'Normal';

    const CACHE_TYPE_ALL = "all";
    const CACHE_TYPE_NORMAL = "normal";

    const MEDIA_TYPE_PC = "pc";
    const MEDIA_TYPE_SP = "sp";

    private $top_panel_service;
    private $normal_panel_service;
    private $cache_manager;
    private $service_factory;
    private $streams = [];
    private $brand_social_accounts = [];

    public function __construct() {
        $this->service_factory = new aafwServiceFactory();
        $this->cache_manager = new CacheManager();

        /** @var  TopPanelService $top_panel_service */
        $this->top_panel_service = $this->service_factory->create("TopPanelService");

        /** @var  NormalPanelService $normal_panel_service */
        $this->normal_panel_service = $this->service_factory->create("NormalPanelService");
    }

    /**
     * @param $brand
     * @param $media_type
     * @param $page
     * @param $top_panel_count
     * @param $normal_panel_count
     * @return array|mixed|null
     */
    public function getAllPanelList($brand, $media_type, $page, $top_panel_count, $normal_panel_count) {

        $panel_list = $this->cache_manager->getPanelCache($brand->id, self::CACHE_TYPE_ALL, $media_type, $page);

        if ($panel_list == null) {

            $top_entries = $this->top_panel_service->getEntriesByPage($brand, $page, $top_panel_count);
            if ($top_entries) {
                foreach ($top_entries as $redisPanelValue) {
                    $panel_list[] = $this->getPanel($redisPanelValue, self::PANEL_TYPE_TOP, $brand);
                }
            }

            $normal_entries = $this->normal_panel_service->getEntriesByPage($brand, $page, $normal_panel_count-count($top_entries));
            if ($normal_entries) {
                foreach ($normal_entries as $redisPanelValue) {
                    $panel_list[] = $this->getPanel($redisPanelValue, self::PANEL_TYPE_NORMAL, $brand);
                }
            }

            // キャッシュに追加
            $this->cache_manager->addPanelCache($brand->id, self::CACHE_TYPE_ALL, $media_type, $page, $panel_list);
        }

        return $panel_list;
    }

    /**
     * @param $brand
     * @param $media_type
     * @param $page
     * @param $offset
     * @param $limit
     * @return array|mixed|null
     */
    public function getNormalPanelList($brand, $media_type, $page, $offset, $limit) {

        $panel_list = $this->cache_manager->getPanelCache($brand->id, self::CACHE_TYPE_NORMAL, $media_type, $page);

        if ($panel_list == null) {

            $normal_entries = $this->normal_panel_service->getEntriesByOffsetAndLimit($brand, $offset, $limit);
            $panel_list = array();

            if ($normal_entries) {
                foreach ($normal_entries as $redisPanelValue) {
                    $panel_list[] = $this->getPanel($redisPanelValue, self::PANEL_TYPE_NORMAL, $brand);
                }
            }

            // キャッシュに追加
            $this->cache_manager->addPanelCache($brand->id, self::CACHE_TYPE_NORMAL, $media_type, $page, $panel_list);
        }

        return $panel_list;
    }

    /**
     * @param $panel
     * @return mixed
     */
    private function getStream($panel) {

        $method_name = "get" . $panel['streamName'];
        $stream_key = $panel['streamName'] . "_" . $panel['entry']->stream_id;

        $stream = $this->streams[$stream_key];
        if (!$stream) {
            $stream = $panel['entry']->{$method_name}();
            $this->streams[$stream_key] = $stream;
        }

        return $stream;
    }

    /**
     * @param $stream
     * @return mixed
     */
    private function getBrandSocialAccount($stream) {

        $brand_social_accounts_key = "brand" . "_" . $stream->brand_social_account_id;

        $brand_social_account = $this->brand_social_accounts[$brand_social_accounts_key];
        if (!$brand_social_account) {
            $brand_social_account = $stream->getBrandSocialAccount();
            $this->brand_social_accounts[$brand_social_accounts_key] = $brand_social_account;
        }

        return $brand_social_account;
    }

    /**
     * @param $redis_panel_value
     * @param $panel_type
     * @param $brand
     * @return array|bool
     * // TODO リファクタリング
     */
    public function getPanel($redis_panel_value, $panel_type, $brand) {

        $panel = array();
        $panel['panelType'] = $panel_type;

        $panel_service = $this->getPanelServiceByType($panel_type);

        // エントリーを取得

        $panel['entry'] = $panel_service->getEntryByEntryValue($redis_panel_value);

        if (!$panel['entry']) {
            return false;
        }

        // ストリーム名を取得
        $panel['streamName'] = $panel_service->getStreamNameByEntryValue($redis_panel_value);
        $panel['entry']->link = $this->rewriteInternalUrl($panel['entry']->link, $brand);

        // text and image for social entry
        if ($panel['entry']->isSocialEntry()) {

            $stream = $this->getStream($panel);
            $brand_social_account = $this->getBrandSocialAccount($stream);

            $panel['brandSocialAccountId'] = $brand_social_account->id;
            $panel['imageUrl'] = $brand_social_account->picture_url;

            if ($panel['entry']->getStoreName() == 'TwitterEntries') {
                $panel['pageName'] = $brand_social_account->name . '@' . $brand_social_account->screen_name;
                $panel['screenName'] = $brand_social_account->screen_name;
            } else {
                $panel['pageName'] = $brand_social_account->name;
            }

            if ($stream->getBrand()->isViewFullText()) {
                $panel['entry']->panel_text = $panel['entry']->getFullText();
            }
        } else {

            if ($panel['entry']->getStoreName() == 'RssEntries') {
                $stream = $this->getStream($panel);
                $panel['rssTitle'] = $stream->title;

                if ($stream->getBrand()->isViewFullText()) {
                    $panel['entry']->panel_text = $panel['entry']->getFullText();
                }
            } elseif ($panel['entry']->getStoreName() == 'PageEntries') {
                $panel['page_new_flg'] = $panel['entry']->isNewPagePanel();

                /** @var StaticHtmlCategoryService $static_html_category_service */
                $static_html_category_service = $this->getService('StaticHtmlCategoryService');
                $category = $static_html_category_service->getStaticHtmlCategoryByStaticHtmlEntryId($panel['entry']->static_html_entry_id);
                $panel['category'] = $category ? $category->toArray() : null;
            }
        }

        $panel['entry'] = $panel['entry']->asArray();
        $panel['entry']['size_class'] = $this->getPanelSizeClass($panel['entry']['display_type']);
        return $panel;
    }

    /**
     * @param $url
     * @param Brand $brand
     * @return string
     */
    public function rewriteInternalUrl($url, Brand $brand) {
        $parsed_url = parse_url($url);

        $mapped_brand_id = Util::getMappedBrandId($parsed_url['host']);

        if ($mapped_brand_id === Util::NOT_MAPPED_BRAND) {
            if (Util::isExternalDomain($parsed_url['host'])) {
                return $url;
            }

            $parsed_request_uri = Util::parseRequestUri($parsed_url['path']);

            if ($brand->directory_name != $parsed_request_uri['directory_name']) {
                return $url;
            }
        } else if ($brand->id != $mapped_brand_id) {
            return $url;
        }

        if ($parsed_url['query']) {
            return sprintf('%s://%s%s?%s', config('Protocol.Secure'), $parsed_url['host'], $parsed_url['path'], $parsed_url['query']);
        }

        return sprintf('%s://%s%s', config('Protocol.Secure'), $parsed_url['host'], $parsed_url['path']);
    }

    /**
     * @param $type
     * @return string
     */
    public function getPanelSizeClass($type) {
        switch ($type) {
            case PanelServiceBase::ENTRY_DISPLAY_TYPE_MIDDLE:
                $size = 'boxSizeMiddle';
                break;
            case PanelServiceBase::ENTRY_DISPLAY_TYPE_SMALL:
                $size = 'boxSizeSmall';
                break;
            case PanelServiceBase::ENTRY_DISPLAY_TYPE_LARGE:
                $size = 'boxSizeLarge';
                break;
            default:
                $size = 'boxSizeSmall';
                break;
        }
        return $size;
    }

    /**
     * @param $panel_type
     * @return null
     */
    private function getPanelServiceByType($panel_type) {

        if ($panel_type === self::PANEL_TYPE_TOP) {
            return $this->top_panel_service;
        } else {
            return $this->normal_panel_service;
        }
    }

    /**
     * @param $brand
     * @return int
     */
    public function getTotalCount($brand) {
        return $this->top_panel_service->count($brand) + $this->normal_panel_service->count($brand);
    }
}