<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
class logout extends BrandcoManagerGETActionBase {

	public function validate() {
		return true;
	}

	public function doAction() {
        if( !$this->isLoginManager() ) {
            return 'redirect: ' . Util::rewriteUrl('account', 'index', array(), array(), '', true);
        }
		//managerに関するセッションの削除
		foreach (preg_grep( '#^manager#', array_keys($this->SESSION) ) as $key) {
			$this->setSession($key,null);
		}
		$this->resetActionContainerByType();

		return 'redirect: ' . Util::rewriteUrl('account', 'index', array(), array(), '', true);
	}
}
