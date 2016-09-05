<?php
AAFW::import('jp.aainc.t.helpers.CampaignHelper');
AAFW::import('jp.aainc.t.helpers.adapters.AbstractCpHelper');

class InstagramHashtagHelper extends AbstractCpHelper {

    public function createCp() {
        $cp_data = array('cps_type' => 1, 'skeleton_type' => 4, 'announce_type' => 0, 'groupCount' => 2, 'group1' => '0,20,9', 'group2' => '3');
        return $this->campaign_helper->createCampaign(CampaignHelper::BRAND_ID, $cp_data);
    }

    public function publishCp($cp_id) {
        $cps = aafwEntityStoreFactory::create('Cps');
        $cp = $cps->findOne(array('id' => $cp_id));

        $cp->public_date = '2015-01-01 00:00:00';
        $cp->start_date = '2015-01-01 00:00:00';
        $cp->end_date = '2016-01-01 00:00:00';
        $cp->announce_date = '2016-01-01 00:00:00';
        $cp->status = 3;

        $cps->save($cp);
    }

    public function deleteCpUsers($cp_id) {
        $this->campaign_helper->cleanupCampaignUsersByCpId($cp_id);
    }

    public function deleteCp($cp_id) {
        $this->campaign_helper->cleanupCampaignByCpId($cp_id);
    }

    public function getCpActionManager() {
        if (!self::$cp_action_manager) {
            return new CpPhotoActionManager();
        }
        return self::$cp_action_manager;
    }
}
