<?php
AAFW::import('jp.aainc.aafw.net.aafwCurlRequestBase');
AAFW::import('jp.aainc.classes.services.CpUserService');
AAFW::import('jp.aainc.classes.services.UserService');
AAFW::import('jp.aainc.classes.services.CpFlowService');
AAFW::import('jp.aainc.classes.entities.ResendCpUserStatusLog');

abstract class UpdateCpInfoBase extends aafwCurlRequestBase {

    protected $service_factory;
    /** @var CpUserService $cp_user_service */
    protected $cp_user_service;
    /** @var UserService $user_service */
    protected $user_service;
    /** @var CpFlowService $cp_flow_service */
    protected $cp_flow_service;

    public function __construct() {
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
        $this->service_factory = new aafwServiceFactory();
        $this->cp_user_service = $this->service_factory->create('CpUserService');
        $this->user_service = $this->service_factory->create('UserService');
        $this->cp_flow_service = $this->service_factory->create('CpFlowService');
    }

    /**
     * @param $monipla_user_id
     * @param $app_id
     * @param $cp_id
     * @param $module_type
     * @param Exception $e
     * @return mixed
     */
    protected function saveResendLog($monipla_user_id, $app_id, $cp_id, $module_type, Exception $e) {
        $resend_cp_user_status_logs = aafwEntityStoreFactory::create('ResendCpUserStatusLogs');

        $resend_cp_user_status_log = $resend_cp_user_status_logs->createEmptyObject();
        $resend_cp_user_status_log->monipla_user_id = $monipla_user_id;
        $resend_cp_user_status_log->app_id = $app_id;
        $resend_cp_user_status_log->cp_id = $cp_id;
        $resend_cp_user_status_log->module_type = $module_type;

        if ($e != null) {
            $resend_cp_user_status_log->error_code = $e->getCode();
            $resend_cp_user_status_log->error_message = $e->getMessage();
        }

        return $resend_cp_user_status_logs->save($resend_cp_user_status_log);
    }

    abstract public function sendCpUserStatus($cp_user_id, $cp_action_id, $app_id, $module_type, $is_elected = 0);

    abstract protected function isLegalCpAction($module_type);

    abstract protected function getCommonParams($cp_action_id, $module_type, $app_id);

    abstract protected function getApiUrl($module_type);
}