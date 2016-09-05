<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class Monipla2EnterpriseSSOTokenService extends aafwServiceBase {
	protected $enterprise_sso_token;

	public function __construct() {
		$this->enterprise_sso_token = $this->getModel("Monipla2EnterpriseSSOTokens");

		$this->logger = aafwLog4phpLogger::getDefaultLogger();
	}

	private function getEnterpriseSSOTokenById($id) {
		$filter = array(
			'enterprise_id' => $id,
		);
		return $this->enterprise_sso_token->findOne($filter);
	}

	private function deleteEnterpriseSSOToken($enterprise_sso_token) {
		return $this->enterprise_sso_token->deletePhysical($enterprise_sso_token);
	}

	public function createEnterpriseSSOToken($enterprise) {
		$enterprise_sso_token = $this->getEnterpriseSSOTokenById($enterprise->id);

		if($enterprise_sso_token) {
			$this->deleteEnterpriseSSOToken($enterprise_sso_token);
		}
		$enterprise_sso_token = $this->enterprise_sso_token->createEmptyObject();
		$enterprise_sso_token->enterprise_id = $enterprise->id;

		$enterprise_sso_token->token = $this->makeRandStr(32);
		return $this->enterprise_sso_token->save($enterprise_sso_token);
	}

	private function makeRandStr($length) {
		$str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z"'));
		$r_str = null;
		for ($i = 0; $i < $length; $i++) {
			$r_str .= $str[rand(0, count($str))];
		}

		return $r_str;
	}
}