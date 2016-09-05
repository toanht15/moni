<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpFacebookLikeLogService extends aafwServiceBase {

    protected $engagement_logs;
    private $logger;

    public function __construct() {
        $this->engagement_logs = $this->getModel('CpFacebookLikeLogs');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $fb_like_info
     */
    public function create($fb_like_info){
        $cp_user_id = $fb_like_info['cp_user_id'];
        $cp_action_id = $fb_like_info['cp_action_id'];
        $brand_social_account_id = $fb_like_info['brand_social_account_id'];
        $status = $fb_like_info['status'];

        $fb_like_log = $this->getLogByCpUserIdAndActionId($cp_user_id, $cp_action_id);
        if (is_null($fb_like_log)) {
            $fb_like_log = $this->engagement_logs->createEmptyObject();
        }
        $fb_like_log->cp_user_id = $cp_user_id;
        $fb_like_log->cp_action_id = $cp_action_id;
        $fb_like_log->brand_social_account_id = $brand_social_account_id;
        $fb_like_log->status = $status;

        return $this->engagement_logs->save($fb_like_log);
    }

    public function getLogByCpUserIdAndActionId($cp_user_id, $cp_action_id) {
        $log = $this->engagement_logs->findOne(array(
            'cp_user_id' => $cp_user_id,
            'cp_action_id' => $cp_action_id
        ));

        return $log;
    }

    public function getLogByCpUserIdsAndActionId($cp_user_ids, $cp_action_id) {
        $filter = array(
            'cp_user_id' => $cp_user_ids,
            'cp_action_id' => $cp_action_id
        );

        return $this->engagement_logs->find($filter);
    }

    public function getCpFbLikeLogStatuses($cp_user_ids, $cp_action_id) {
        $like_statuses = array();
        $cp_fb_like_logs = $this->getLogByCpUserIdsAndActionId($cp_user_ids, $cp_action_id);

        foreach ($cp_fb_like_logs as $cp_fb_like_log) {
            $like_statuses[$cp_fb_like_log->cp_user_id] = CpFacebookLikeLog::$fb_like_statuses[$cp_fb_like_log->status];
        }

        return $like_statuses;
    }

    public function deletePhysicalFbLikeLogsByCpActionId ($cp_action_id) {

        if (!$cp_action_id) {
            return;
        }

        $logs = $this->engagement_logs->find(array('cp_action_id' => $cp_action_id));
        if (!$logs) {
            return ;
        }

        foreach ($logs as $log) {
            $this->engagement_logs->deletePhysical($log);
        }
    }

    public function deletePhysicalFbLikeLogsByCpActionIdAndCpUserId ($cp_action_id, $cp_user_id) {

        if (!$cp_action_id || !$cp_user_id) {
            return;
        }

        $logs = $this->engagement_logs->find(array('cp_action_id' => $cp_action_id, 'cp_user_id' => $cp_user_id));
        if (!$logs) {
            return ;
        }

        foreach ($logs as $log) {
            $this->engagement_logs->deletePhysical($log);
        }
    }
}
