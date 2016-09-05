<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.brandco.cp.CpFacebookLikeActionManager');

class EngagementLogService extends aafwServiceBase {

    /** @var EngagementLogs $engagement_logs */
    protected $engagement_logs;

    public function __construct() {
        $this->engagement_logs = $this->getModel('EngagementLogs');
    }

    public function create($engagement_info){
        $engagement_log = $this->engagement_logs->createEmptyObject();
        $engagement_log->cp_user_id = $engagement_info['cp_user_id'];
        $engagement_log->cp_action_id = $engagement_info['cp_action_id'];
        $engagement_log->brand_social_account_id = $engagement_info['brand_social_account_id'];
        // status: 1 - このCPでいいねした, 2 - 既にいいね済み
        $engagement_log->status = $engagement_info['status'];
        return $this->engagement_logs->save($engagement_log);
    }

    public function getEngagementLogByIds($cp_user_id, $cp_action_id, $brand_social_account_id, $on_master = false){
        $filter = array(
            'on_master' => $on_master,
            'cp_user_id' => $cp_user_id,
            'cp_action_id' => $cp_action_id,
            'brand_social_account_id' => $brand_social_account_id
        );
        return $this->engagement_logs->findOne($filter);
    }

    public function getEngagementLogCountByCpActionId($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id
        );
        return $this->engagement_logs->count($filter);
    }

    public function getEngagementLogCountByCpActionIdAndStatus($cp_action_id, $status) {
        $filter = array(
            'cp_action_id' => $cp_action_id,
            'status' => $status
        );

        return $this->engagement_logs->count($filter);
    }
}
