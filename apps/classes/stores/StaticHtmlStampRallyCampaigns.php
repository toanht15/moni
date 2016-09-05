<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');
class StaticHtmlStampRallyCampaigns extends aafwEntityStoreBase {

    protected $_TableName = 'static_html_stamp_rally_campaigns';
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;

    public function getRecordsByStampRallyId($stampRallyId) {
        $records = array();
        $stampRallyCps = $this->find(array("conditions" => array("static_html_stamp_rally_id" => $stampRallyId)));

        foreach($stampRallyCps as $stampRallyCp) {
            $records[] = intval($stampRallyCp->campaign_id);
        }
        
        return $records;
    }
}