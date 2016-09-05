<?php
AAFW::import('jp.aainc.classes.services.StreamService');
AAFW::import('jp.aainc.classes.services.FacebookStreamService');
AAFW::import('jp.aainc.classes.services.TwitterStreamService');
AAFW::import('jp.aainc.classes.services.YoutubeStreamService');
AAFW::import('jp.aainc.classes.services.InstagramStreamService');
AAFW::import('jp.aainc.classes.services.RssStreamService');
AAFW::import('jp.aainc.classes.services.LinkEntryService');
AAFW::import('jp.aainc.classes.services.PhotoStreamService');

class UserPanelClickService extends aafwServiceBase {

    private $user_panel_click;

    public function __construct() {
        $this->user_panel_click = $this->getModel('UserPanelClicks');
    }

    /**
     * @param $entries
     * @param $entries_id
     * @param $user_id
     * @param $panel_type
     * パネルのクリック履歴を記録
     */
    public function setPanelClick($entries, $entries_id, $user_id, $panel_type) {
        if (!$entries || !$entries_id) return;

        $panel_click = $this->createEmptyPanelClick();
        $panel_click->user_id = $user_id ? $user_id : 0;
        $panel_click->panel_type = $panel_type;
        $panel_click->entries = $entries;
        $panel_click->entries_id = $entries_id;
        $panel_click->user_agent = $_SERVER['HTTP_USER_AGENT'];

        $this->createPanelClick($panel_click);
    }

    public function createPanelClick($panel_click) {
        $this->user_panel_click->save($panel_click);
    }

    public function createEmptyPanelClick() {
        return $this->user_panel_click->createEmptyObject();
    }
}