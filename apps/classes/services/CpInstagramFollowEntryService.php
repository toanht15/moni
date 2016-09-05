<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpInstagramFollowEntryService extends aafwServiceBase {

    /** @var CpInstagramFollowEntryService $cp_ig_follow_entry */
    protected $cp_ig_follow_entry;

    public function __construct() {
        $this->cp_ig_follow_entry = $this->getModel('CpInstagramFollowActionEntries');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_instagram_follow_action_id
     * @param $brand_social_account_id
     * @param $instagram_entry_id
     */
    public function create($cp_instagram_follow_action_id, $brand_social_account_id, $instagram_entry_id){
        $entry = $this->cp_ig_follow_entry->createEmptyObject();
        $entry->cp_instagram_follow_action_id = $cp_instagram_follow_action_id;
        $entry->brand_social_account_id = $brand_social_account_id;
        $entry->instagram_entry_id = $instagram_entry_id;
        $this->cp_ig_follow_entry->save($entry);
    }

    /**
     * @param $cp_instagram_follow_action_id
     * @param $brand_social_account_id
     * @param $instagram_entry_id
     * @return mixed
     */
    public function update($cp_instagram_follow_action_id, $brand_social_account_id, $instagram_entry_id) {
        $entry = $this->getTargetAccount($cp_instagram_follow_action_id);
        $entry->brand_social_account_id = $brand_social_account_id;
        $entry->instagram_entry_id = $instagram_entry_id;
        return $this->cp_ig_follow_entry->save($entry);
    }

    /**
     * @param $cp_instagram_follow_action_id
     * @return mixed
     */
    public function getTargetAccount($cp_instagram_follow_action_id) {
        $filter = array(
            'cp_instagram_follow_action_id' => $cp_instagram_follow_action_id
        );
        return $this->cp_ig_follow_entry->findOne($filter);
    }

}
