<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class RssEntry extends aafwEntityBase implements IPanelEntry {

    public function getEntryPrefix() {
        return self::ENTRY_PREFIX_RSS;
    }

    public function getType() {
        return StreamService::STREAM_TYPE_RSS;
    }

    protected $_Relations = array(
        'RssStreams' => array(
            'stream_id' => 'id',
        ),
        'Brands' => array(
            'brand_id' => 'id'
        )
    );

    public function getStoreName() {
        return "RssEntries";
    }

    public function getServicePrefix() {
        return 'RssStream';
    }

    public function isSocialEntry() {
        return false;
    }

    public function asArray() {
        return [
            "id" => $this->id,
            "text" => $this->text,
            "link" => $this->link,
            "image_url" => $this->image_url,
            "description" => $this->description,
            "display_type" => $this->display_type,
            "panel_text" => $this->panel_text,
            "is_social_entry" => $this->isSocialEntry(),
            "service_prefix" => $this->getServicePrefix(),
        ];
    }

    /**
     * フルテキスト取得
     * @param $entry
     * @return mixed
     */
    public function getFullText() {
        return $this->text;
    }
}
