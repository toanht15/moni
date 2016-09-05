<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class Monipla2EnterpriseService extends aafwServiceBase {
	protected $enterprise;

	public function __construct() {
		$this->enterprise = $this->getModel("Monipla2Enterprises");

		$this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

	public function getEnterpriseById($id) {
		$filter = array(
			'id' => $id,
		);
		return $this->enterprise->findOne($filter);
	}

	public function getEnterpriseByLoginId($loginId) {
		$filter = array(
			'login_id' => $loginId,
		);
		return $this->enterprise->findOne($filter);
	}

	public function getEnterpriseByLoginAccount($loginId, $password) {
		$filter = array( 'conditions' => array(
			'login_id'         => $loginId,
			'password'         => md5( $password ),
			'active_flg'       => '1',
			'cancellation_flg' => '0',
		));
		return $this->enterprise->findOne($filter);
	}

	public function createEnterprise($enterprise) {
		$this->enterprise->save($enterprise);
	}
}