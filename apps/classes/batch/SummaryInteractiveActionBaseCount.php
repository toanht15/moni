<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

abstract class SummaryInteractiveActionBaseCount extends BrandcoBatchBase {

    const TYPE_INTERNAL = 1;
    const TYPE_EXTERNAL = 2;

    protected $service_factory;
    protected $logger;
    protected $db;
    protected $execute_class;

    public function __construct() {
        $this->service_factory = new aafwServiceFactory();
        $this->execute_class = get_class($this);
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->db = aafwDataBuilder::newBuilder();
    }

    public function updateSnsActionCountLogs($sns_action_count_data) {
        $total = count($sns_action_count_data);
        $sql = "";
        $count = 0;
        foreach ($sns_action_count_data as $value) {
            $count++;
            if ($count % 100 == 1) {
                $sql = "INSERT INTO sns_action_count_logs(social_app_id, user_id, social_media_account_id, log_type, action_count, created_at, updated_at) VALUES";
            }
            $sql .= " ({$value['social_app_id']},{$value['uid']},{$value['social_media_account_id']},{$value['log_type']},{$value['action_count']},NOW(),NOW()),";
            if ($count == $total || ($count % 100 == 0)) {
                $sql  = substr($sql, 0, strlen($sql) - 1);
                $sql .= " ON DUPLICATE KEY UPDATE action_count = VALUES(action_count), updated_at = NOW()";
                $this->db->executeUpdate($sql);
            }
        }
    }

    abstract function getDataSnsActionCountLogs();

    public function executeProcess() {
        $data = $this->getDataSnsActionCountLogs();
        $this->updateSnsActionCountLogs($data);
    }
}