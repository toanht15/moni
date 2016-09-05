<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
AAFW::import('jp.aainc.classes.services.StreamService');
class UserPanelClick extends aafwEntityBase {

    const TOP_PANEL = 1;
    const CATEGORY_PANEL = 2;

    public static $panel_type = array(
        self::TOP_PANEL => 'Top Panel',
        self::CATEGORY_PANEL => 'Category Panel',
    );

    protected $_Relations = array(
        'Users' => array(
            'user_id' => 'id'
        ),
        'FacebookEntries' => array(
            'entries_id' => 'id'
        ),
        'TwitterEntries' => array(
            'entries_id' => 'id'
        ),
        'RssEntries' => array(
            'entries_id' => 'id'
        ),
        'LinkEntries' => array(
            'entries_id' => 'id'
        ),
        'InstagramEntries' => array(
            'entries_id' => 'id'
        ),
        'YoutubeEntries' => array(
            'entries_id' => 'id'
        ),
        'PhotoEntries' => array(
            'entries_id' => 'id'
        ),
        'PageEntries' => array(
            'entries_id' => 'id'
        )
    );

    public function getBrand() {
        $entry = $this->getEntry();
        if ( $this->entries == 'link') {
            return $entry->getBrand();
        }else{
            $stream_service = $this->getService('StreamService', array($this->convertCamel($this->entries)));
            $stream = $stream_service->getStreamById($entry->stream_id);
            return $stream->getBrand();
        }
    }

    public function getEntry() {
        return $this->findOneByRelatedObject($this->convertCamel($this->entries) . 'Entries', array());
    }

    public function getEntryUrl( $directory_name ) {
        $entry = $this->getEntry();
        if ( $this->entries == 'link' || $this->entries == 'rss' ) {
            return $entry->link;
        } elseif ($this->entries == 'photo') {
            return Util::constructBaseURL($this->getBrand()->id, $directory_name) . 'photo/detail/' . $entry->id;
        } elseif ($this->entries == 'page') {
            /** @var StaticHtmlEntryService $static_html_entry_service */
            $static_html_entry_service = $this->getService('StaticHtmlEntryService');
            $static_html_entry = $static_html_entry_service->getEntryById($entry->id);
            return Util::constructBaseURL($this->getBrand()->id, $directory_name) . 'page/' . $static_html_entry->page_url;
        } else {
            $stream = $entry->findOneByRelatedObject($this->convertCamel($this->entries) . 'Streams', array());
            return Util::constructBaseURL($this->getBrand()->id, $directory_name). 'sns/detail/' . $stream->brand_social_account_id . '/' . $entry->id;
        }
    }
}
