<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class SocialAppService extends aafwServiceBase {

	public function getSocialAppByProvider($provider, $p = 1, $find_one_flg = false) {

		$social_apps = $this->getModel('SocialApps');

		$conditions = array(
			'conditions' => array(
				'provider' => $provider,
				'del_flg' => 0,
			),
			'order' => array(
				'name' => 'id',
				'direction' => 'asc',
			),
			'pager' => array(
				'count' => 20,
				'page' => $p,
			),
		);

		if ($find_one_flg) {
			return $social_apps->findOne($conditions);
		}
		return $social_apps->find($conditions);
	}
}

