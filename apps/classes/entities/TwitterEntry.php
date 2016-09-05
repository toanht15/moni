<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class TwitterEntry extends aafwEntityBase implements IPanelEntry {

    public function getEntryPrefix() {
        return self::ENTRY_PREFIX_TWITTER;
    }

    public function getType() {
        return StreamService::STREAM_TYPE_TWITTER;
    }

    protected $_Relations = array(

        'TwitterStreams' => array(
            'stream_id' => 'id',
        )
    );

    public function getStoreName() {
        return "TwitterEntries";
    }

    public function getServicePrefix() {
        return 'TwitterStream';
    }

    public function isSocialEntry() {
        return true;
    }

    public function getBrandSocialAccount() {
        $service_factory = new aafwServiceFactory ();

        $streamService = $service_factory->create('TwitterStreamService');
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
            "panel_text" => json_decode($this->extra_data)->text,
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
        // そのまま
        return $this->panel_text;
    }
}
