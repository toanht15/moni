<?php
AAFW::import('jp.aainc.classes.services.StreamService');
class OperationLogManagerDataService extends aafwServiceBase {

	private $operation_log_manager_data;

	const PC_DEVICE = 1;
	const SMARTPHONE_DEVICE = 2;

	public function __construct() {
		$this->operation_log_manager_data = $this->getModel('OperationLogManagerDatas');
	}

	/**
	 * @param $brand_id
	 * @param $user_id
	 * ログイン情報の保存
	 */
	public function setOperationLog($mailAddressHash, $managerAccount) {

		$date = date("Y-m-d H:i:s", time());

		$operationManagerLogData = $this->createEmptyOperationManagerLog();
		$operationManagerLogData->mail_address = $managerAccount->mail_address;
		$operationManagerLogData->enter_url = $_SERVER["REQUEST_URI"];
		$operationManagerLogData->enter_date = $date;
		$operationManagerLogData->cookie = json_encode($_COOKIE);
		$operationManagerLogData->user_agent = $_SERVER['HTTP_USER_AGENT'];
		$operationManagerLogData->device = $this->getDevice();
		$operationManagerLogData->ip_address = Util::getIpAddress();
		$operationManagerLogData->referer_url = $this->getOperationReferer();

		$this->createOperationManaagerLog($operationManagerLogData);
	}

	public function createOperationManaagerLog($operationManagerLogData) {
		$this->operation_log_manager_data->save($operationManagerLogData);
	}

	public function createEmptyOperationManagerLog() {
		return $this->operation_log_manager_data->createEmptyObject();
	}

	public function setOperationReferer($operationReferer) {
		$_SESSION['managerOpeReferer'] = $operationReferer;
	}

	public function getOperationReferer() {
		$operationReferer = $_SESSION['managerOpeReferer'];
		unset($_SESSION['managerOpeReferer']);

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