<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class YoutubeEntry extends aafwEntityBase implements IPanelEntry {

    public function getEntryPrefix() {
        return self::ENTRY_PREFIX_YOUTUBE;
    }

    public function getType() {
        return StreamService::STREAM_TYPE_YOUTUBE;
    }

    protected $_Relations = array(

        'YoutubeStreams' => array(
            'stream_id' => 'id',
        )
    );

    public function getStoreName() {
        return "YoutubeEntries";
    }

    public function getServicePrefix() {
        return 'YoutubeStream';
    }

    public function isSocialEntry() {
        return true;
    }

    public function getBrandSocialAccount() {
        $service_factory = new aafwServiceFactory ();

        $streamService = $service_factory->create('YoutubeStreamService');
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
            "panel_text" => $this->panel_text,
            "is_social_entry" => $this->isSocialEntry(),
            "service_prefix" => $this->getServicePrefix()
        ];
    }

    /**
     * フルテキスト取得
     * @param $entry
     * @return mixed
     */
    public function getFullText() {
        return json_decode($this->extra_data)->snippet->description;
    }
}

