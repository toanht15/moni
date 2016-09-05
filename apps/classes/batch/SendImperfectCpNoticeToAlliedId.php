<?php

AAFW::import('jp.aainc.aafw.web.aafwController');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.CacheManager');
AAFW::import('jp.aainc.vendor.cores.MoniplaCore');
AAFW::import ('jp.aainc.classes.services.ApplicationService');
AAFW::import ('jp.aainc.classes.services.CpFlowService');
AAFW::import ('jp.aainc.classes.services.CpUserActionStatusService');

require_once dirname(__FILE__) . '/../../config/define.php';

class SendImperfectCpNoticeToAlliedId {

    private $logger;

    private $moniplaCore = null;

    /** @var $service_factory aafwServiceFactory */
    private $service_factory;

    /** @var CpFlowService $cp_flow_service */
    private $cp_flow_service;

    /** @var CpUserActionStatusService $cp_user_action_status_service */
    private $cp_user_action_status_service;

    /** @var CpTransactionService $transaction_service */
    private $transaction_service;

    private $empty_arg = array();

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->cp_flow_service = $this->service_factory->create('CpFlowService');
        $this->cp_user_action_status_service = $this->service_factory->create('CpUserActionStatusService');
        $this->transaction_service = $this->service_factory->create("CpTransactionService");
        $this->db = aafwDataBuilder::newBuilder();
        $this->cache_manager = new CacheManager();
    }

    public function doProcess() {
        //キャンペーン未送信かつ未完了を抽出
        $unsentNotices = $this->selectImperfectUserCps();
        $sentNotices = array();

        //お知らせを送信
        foreach ($unsentNotices as $unsentNotice) {
            try {
                if (!$this->moniplaCore) {
                    /** @var \Monipla\Core\MoniplaCore moniplaCore */
                    $this->moniplaCore = \Monipla\Core\MoniplaCore::getInstance();
                }

                $cp = $this->cp_flow_service->getCpById($unsentNotice['cp_id']);
                $unsentNotice['title'] = $cp->getTitle();
                $unsentNotice['clientId'] = ApplicationService::$ApplicationMaster[$unsentNotice['app_id']]['client_id'];

                $this->moniplaCore->sendNotification( array (
                    'class'  => 'Thrift_AddNotification',
                    'fields' => array (
                        'socialAccount' => array(
                            'class' => 'Thrift_SocialAccount',
                            'fields' => array(
                                'socialMediaType' => 'Platform',
                                'socialMediaAccountID' => $unsentNotice['monipla_user_id'],
                                'name' => $unsentNotice['brand_name'],
                            ),
                        ),
                        'clientId' => $unsentNotice['clientId'],
                        'title' => $this->getTitle($unsentNotice['title']),
                        'message' => $this->getDescription($unsentNotice['title'], $unsentNotice['end_date']),
                        'url' => $this->getLink($unsentNotice['brand_dir'], $unsentNotice['cp_id'], $unsentNotice['brand_id']),
                        'remindFlg' => 1
                    )));
                $sentNotices[] = $unsentNotice;
            } catch (Exception $e) {
                $this->logger->error('sendNotification Error.' . $e);
            }

        }
        $this->updateSentNoticeLogs($sentNotices);
    }

    private function selectImperfectUserCps() {
        $query = "
            SELECT DISTINCT cu.id 'cp_user_id', cp.id 'cp_id', cp.end_date 'end_date', br.app_id 'app_id', br.name 'brand_name', br.directory_name 'brand_dir', us.monipla_user_id 'monipla_user_id', br.id 'brand_id'
            FROM cps cp
            INNER JOIN cp_users cu ON cu.cp_id = cp.id
            INNER JOIN brands br ON br.id = cp.brand_id
            INNER JOIN users us ON us.id = cu.user_id
            WHERE
              cp.type = 1 AND
              cp.start_date < now() AND
              cp.end_date > now() AND
              cp.show_monipla_com_flg = 1 AND
              NOT cp.selection_method = 2 AND
              br.test_page = 0 AND
              NOT EXISTS (SELECT im.id FROM imperfect_cp_notice_logs im WHERE im.cp_user_id = cu.id) AND
              cp.del_flg = 0 AND cu.del_flg = 0 AND br.del_flg = 0 AND us.del_flg = 0
            ";
        $rs = $this->db->getBySQL($query, array('__NOFETCH__'));

        //送信対象候補から未完了のユーザーに絞る
        $unsentNotices = array();
        while ($target = $this->db->fetch($rs)) {
            try {
                $entry_action = $this->cp_flow_service->getEntryActionByCpId($target['cp_id']);
                $join_finish_action = $this->cp_flow_service->getJoinFinishActionByCpId($target['cp_id']);
                if ($entry_action && $join_finish_action) {
                    $entry_status = $this->cp_user_action_status_service->getCpUserActionStatusByCpUserIdAndCpActionId($target['cp_user_id'], $entry_action->id);
                    $join_finish_status = $this->cp_user_action_status_service->getCpUserActionStatusByCpUserIdAndCpActionId($target['cp_user_id'], $join_finish_action->id);
                    if ($entry_status == CpUserActionStatus::JOIN && $join_finish_status == CpUserActionStatus::NOT_JOIN) {
                        $params = array();
                        $params['cp_user_id'] = $target['cp_user_id'];
                        $params['cp_id'] = $target['cp_id'];
                        $params['end_date'] = $target['end_date'];
                        $params['app_id'] = $target['app_id'];
                        $params['brand_name'] = $target['brand_name'];
                        $params['brand_dir'] = $target['brand_dir'];
                        $params['monipla_user_id'] = $target['monipla_user_id'];
                        $unsentNotices[] = $params;
                    }
                }
            } catch (Exception $e) {
                $this->logger->error("selectImperfectUserCps setUnsentNotice Error. cp_user_id = {$target->cp_user_id} {$e}");
            }
        }
        return $unsentNotices;
    }

    private function updateSentNoticeLogs($sentNotices) {
        $query = "INSERT INTO imperfect_cp_notice_logs (cp_user_id, created_at, updated_at) VALUES";
        $value = "";
        foreach ($sentNotices as $sentNotice) {
            if ($value) {
                $value .= ",";
            }
            $value .= " ({$sentNotice['cp_user_id']}, '" . date("Y-m-d H:i:s") . "', '" . date("Y-m-d H:i:s") ."')";
        }
        $query .= $value;
        $this->db->executeUpdate($query);
    }

    private function getTitle($title) {
        return "【参加が未完了です】{$title}";
    }

    private function getDescription($title, $end_date) {
        return "「{$title}」への参加手続きがまだ完了していません。参加期限が迫っていますので、必要条件をマイページでご確認下さい。マイページの「{$title}」欄から参加条件をご確認の上、{$end_date}までにご投稿をお願いします。";
    }

    private function getLink($brand_dir, $cp_id, $brand_id) {
        return Util::getMappedServerName($brand_id) . "/{$brand_dir}/campaigns/{$cp_id}";
    }
}
