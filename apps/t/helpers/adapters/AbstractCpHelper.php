<?php
AAFW::import('jp.aainc.t.helpers.CampaignHelper');

abstract class AbstractCpHelper {

    /** @var CampaignHelper $campaign_helper */
    protected $campaign_helper;

    protected static $cp_action_manager;

    public function __construct() {
        $this->campaign_helper = new CampaignHelper();
    }

    public abstract function createCp();

    public abstract function publishCp($cp_id);

    public abstract function deleteCpUsers($cp_id);

    public abstract function deleteCp($cp_id);

    public abstract function getCpActionManager();
}