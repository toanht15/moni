<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class StaticHtmlStampRallies extends aafwEntityStoreBase {

    protected $_TableName = 'static_html_stamp_rallies';
    protected $_DeleteType = aafwEntityStoreBase::DELETE_TYPE_PHYSICAL;

    public function delete($staticHtmlStampRally) {

        if(!$staticHtmlStampRally->id) return false;

        $staticHtmlStampRallyCampaigns = $this->getModel('StaticHtmlStampRallyCampaigns');
        $stampRallyCps = $staticHtmlStampRallyCampaigns->find(array('static_html_stamp_rally_id' => $staticHtmlStampRally->id));
        foreach( $stampRallyCps as $stampRallyCp) {
            $staticHtmlStampRallyCampaigns->delete($stampRallyCp);
        }

        parent::delete($staticHtmlStampRally);
    }

    public function insert($template_id, $template) {
        $stampRallyCampaigns = $this->getModel('StaticHtmlStampRallyCampaigns');

        $record = $this->createEmptyObject();
        $record->template_id = $template_id;
        $record->campaign_count = $template->campaign_count;
        $record->stamp_status_joined_image = $template->stamp_status_joined_image;
        $record->stamp_status_finished_image = $template->stamp_status_finished_image;
        $record->stamp_status_coming_soon_image = $template->stamp_status_coming_soon_image;
        $record = $this->save($record);

        $staticHtmlStampRallyCampaignId = $record->id;

        $cpFlowService = $this->getService('CpFlowService');

        foreach($template->cp_ids as $cp_id) {
            $cp = $cpFlowService->getCpById($cp_id);
            if($cp && $cp->archive_flg == Cp::ARCHIVE_OFF ){
                $stampRallyCampaign = $stampRallyCampaigns->createEmptyObject();
                $stampRallyCampaign->static_html_stamp_rally_id = $staticHtmlStampRallyCampaignId;
                $stampRallyCampaign->campaign_id = $cp_id;
                $stampRallyCampaigns->save($stampRallyCampaign);
            }
        }
    }

    public function getRecordByTemplateId($template_id) {
        $stampRallyCampaigns = $this->getModel('StaticHtmlStampRallyCampaigns');
        $recordObj = $this->findOne(array('template_id' => $template_id));
        return array(
            'campaign_count' => $recordObj->campaign_count,
            'stamp_status_joined_image' => $recordObj->stamp_status_joined_image,
            'stamp_status_finished_image' => $recordObj->stamp_status_finished_image,
            'stamp_status_coming_soon_image' => $recordObj->stamp_status_coming_soon_image,
            'cp_ids' => $stampRallyCampaigns->getRecordsByStampRallyId($recordObj->id)
        );
    }
}