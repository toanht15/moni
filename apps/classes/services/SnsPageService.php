<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.base.PanelServiceBase');

class SnsPageService extends aafwServiceBase {
    private $brand_social_account_id;

    public $stream_service;

    public function __construct($brand_social_account_id) {
        $this->brand_social_account_id = $brand_social_account_id;

        $service_factory = new aafwServiceFactory ();
        $this->brand_social_account_service = $service_factory->create('BrandSocialAccountService');

        $this->brand_social_account = $this->brand_social_account_service->getBrandSocialAccountById($this->brand_social_account_id);
        $this->stream = $this->brand_social_account_service->getStreamByBrandSocialAccountId($this->brand_social_account->id);

        $this->stream_service = $service_factory->create(get_class($this->stream) . 'Service');
    }

    /**
     * @return bool
     */
    public function checkLegalSnsPanel() {
        return in_array($this->brand_social_account->social_app_id, SocialApps::$social_pages);
    }

    /**
     * @param $page
     * @return array
     */
    public function getSnsPanelList($page) {
        $panel_list = array();

        $order = array(
            'name' => "pub_date",
            'direction' => "desc",
        );

        if ($this->checkLegalSnsPanel()) {
            $sns_entries = $this->stream_service->getAvailableEntriesByStreamId($this->stream->id, $page, $this->getPageLimit(), $order);
            if ($sns_entries) {
                foreach ($sns_entries as $sns_entry) {
                    $panel_list[] = $this->getPanel($sns_entry);
                }
            }
        }

        return $panel_list;
    }

    /**
     * @param $sns_entry
     * @return array|bool
     */
    private function getPanel($sns_entry) {
        if (!$sns_entry) return false;

        $panel = array();

        // ストリーム名を取得
        $panel['streamName'] = $sns_entry->getServicePrefix();

        $panel['streamId'] = $this->stream->id;
        $panel['brandSocialAccountId'] = $this->brand_social_account->id;
        $panel['imageUrl'] = $this->brand_social_account->picture_url;

        if ($sns_entry->getStoreName() == 'TwitterEntries') {
            $panel['pageName'] = $this->brand_social_account->name . '@' . $this->brand_social_account->screen_name;
            $panel['screenName'] = $this->brand_social_account->screen_name;
        } else {
            $panel['pageName'] = $this->brand_social_account->name;
        }

        $stream = $this->stream_service->getStreamById($panel['streamId']);
        if ($stream->getBrand()->isViewFullText()) {
            $sns_entry->panel_text = $sns_entry->getFullText();
        }

        $panel['entry'] = $sns_entry->asArray();
        $panel['entry']['page_link'] = Util::rewriteUrl('sns', 'detail', array($this->brand_social_account->id, $sns_entry->id));
        $panel['entry']['pub_date'] = $sns_entry->pub_date;

        if ($this->brand_social_account->social_app_id == SocialApps::PROVIDER_GOOGLE) {
            $thumbnails = json_decode($sns_entry->extra_data)->snippet->thumbnails;
            if ($thumbnails->high->url) {
                $panel['entry']['image_url'] = $thumbnails->high->url;
            }
        }

        return $panel;
    }

    /**
     * @return mixed
     */
    public function getTotalCount() {
        return $this->stream_service->getAvailableEntriesCountByStreamId($this->stream->id);
    }

    public function getPageLimit() {
        if (Util::isSmartPhone()) return PanelServiceBase::DEFAULT_PAGE_COUNT_SP;

        if ($this->brand_social_account->social_app_id == SocialApps::PROVIDER_INSTAGRAM) return PanelServiceBase::INSTAGRAM_PAGE_LIMIT_PC;

        return PanelServiceBase::DEFAULT_PAGE_COUNT_PC;
    }
}
