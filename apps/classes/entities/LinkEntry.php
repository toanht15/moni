<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.entities.base.IPanelEntry');

class LinkEntry extends aafwEntityBase implements IPanelEntry {

    const SERVICE_PREFIX = 'LinkEntry';

    const TARGET_TOP = 0;
    const TARGET_BLANK = 1;

    protected $_Relations = array(
        'Brands' => array(
            'brand_id' => 'id'
        )
    );

    public function getEntryPrefix() {
        return self::ENTRY_PREFIX_LINK;
    }

    public function getStoreName() {
        return "LinkEntries";
    }

    public function isSocialEntry() {
        return false;
    }

    public function getServicePrefix() {
        return 'LinkEntry';
    }

    public function asArray() {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "body" => $this->body,
            "link" => $this->link,
            "target" => $this->target,
            "image_url" => $this->image_url,
            "display_type" => $this->display_type,
            "is_social_entry" => $this->isSocialEntry(),
            "service_prefix" => $this->getServicePrefix(),
        ];
    }
}
