<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class InstagramEntry extends aafwEntityBase implements IPanelEntry {

    const INIT_CRAWL_COUNT = 100;

    protected $_Relations = array(
        'InstagramStreams' => array(
            'stream_id' => 'id',
        )
    );

    public function getEntryPrefix() {
        return self::ENTRY_PREFIX_INSTAGRAM;
    }

    public function getStoreName() {
        return 'InstagramEntries';
    }

    public function getType() {
        return StreamService::STREAM_TYPE_INSTAGRAM;
    }

    public function isSocialEntry() {
        return true;
    }

    public function getServicePrefix() {
        return 'InstagramStream';
    }

    public function getBrandSocialAccount() {
        $service_factory = new aafwServiceFactory ();

        $streamService = $service_factory->create('InstagramStreamService');
        $stream = $streamService->getStreamById($this->stream_id);
        $brandSocialAccountService = $service_factory->create('BrandSocialAccountService');

        return $brandSocialAccountService->getBrandSocialAccountById($stream->brand_social_account_id);
    }

    public function asArray() {
        return [
            "id" => $this->id,
            "object_id" => $this->object_id,
            "link" => $this->link,
            "image_url" => $this->image_url,
            "display_type" => $this->display_type,
            "panel_text" => json_decode($this->extra_data)->caption->text,
            "panel_comment" => $this->panel_comment,
            "is_social_entry" => $this->isSocialEntry(),
            "service_prefix" => $this->getServicePrefix(),
        ];
    }

    /**
     * フルテキスト取得
     * @return true
     */
    public function getFullText() {
        // そのまま
        return $this->panel_text;
    }
}
