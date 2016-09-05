<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class PageEntry extends aafwEntityBase implements IPanelEntry {

    const TARGET_TOP        = 0;
    const TARGET_BLANK      = 1;

    const NEW_PAGE_LIMIT    = 3;

    protected $_Relations = array(
        'StaticHtmlEntries' => array(
            'static_html_entry_id' => 'id'
        )
    );

    public function getEntryPrefix() {
        return self::ENTRY_PREFIX_PAGE;
    }

    public function getStoreName() {
        return "PageEntries";
    }

    public function getServicePrefix(){
        return 'PageStream';
    }

    public function isSocialEntry() {
        return false;
    }

    public function isNewPagePanel() {
        if (strtotime(date('Y/m/d 00:00', strtotime('-' . self::NEW_PAGE_LIMIT . ' day'))) < strtotime($this->pub_date)) {
            return true;
        }

        return false;
    }

    public function isPrePublicPage() {
        if (strtotime(date('Y/m/d H:i')) < strtotime($this->pub_date)) {
            return true;
        }

        return false;
    }

    public function asArray() {
        $static_html_entry = $this->getStaticHtmlEntry();

        return [
            "id" => $this->id,
            "title" => $static_html_entry->title,
            "panel_text" => $this->panel_text,
            'link' => $static_html_entry->getUrl(),
            "image_url" => $this->image_url,
            "display_type" => $this->display_type,
            "is_social_entry" => $this->isSocialEntry(),
            "service_prefix" => $this->getServicePrefix(),
        ];
    }
}