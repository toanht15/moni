<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class OperationLogAdminDataService extends aafwServiceBase {

	private $operation_log_admin_data;

	const PC_DEVICE = 1;
	const SMARTPHONE_DEVICE = 2;

    const MANAGER_LOGIN = 1;
    const NOT_MANAGER_LOGIN = 0;

	public function __construct() {
		$this->operation_log_admin_data = $this->getModel('OperationLogAdminDatas');
	}

	/**
	 * @param $brand_id
	 * @param $user_id
     * @param $from_manager
	 * オペレーションログの保存
	 */
	public function setOperationLog($user_id, $brand_id, $from_manager) {

		$date = date("Y-m-d H:i:s", time());

		$operationLogData = $this->createEmptyOperationLog();
		$operationLogData->user_id = $user_id;
		$operationLogData->brand_id = $brand_id;
		$operationLogData->enter_url = $_SERVER["REQUEST_URI"];
		$operationLogData->enter_date = $date;
		$operationLogData->cookie = json_encode($_COOKIE);
		$operationLogData->user_agent = $_SERVER['HTTP_USER_AGENT'];
		$operationLogData->device = $this->getDevice();
		$operationLogData->ip_address = Util::getIpAddress();
		$operationLogData->referer_url = $this->getOperationReferer();
        $operationLogData->from_manager = $from_manager;

		$this->createOperationLog($operationLogData);
	}

	public function createOperationLog($operationLogData) {
		$this->operation_log_admin_data->save($operationLogData);
	}

	public function createEmptyOperationLog() {
		return $this->operation_log_admin_data->createEmptyObject();
	}

	public function setOperationReferer($operationReferer) {
		$_SESSION['adminOpeReferer'] = $operationReferer;
	}

	public function getOperationReferer() {
		$operationReferer = $_SESSION['adminOpeReferer'];
		unset($_SESSION['adminOpeReferer']);

		return $operationReferer;
	}

	public function getDevice() {
		$device = aafwMobileDispatcher::isMobile($_SERVER);
		if($device['is_mobile'] || $device['is_smart']) {
			return self::SMARTPHONE_DEVICE;
		} else {
			return self::PC_DEVICE;
		}
	}

}