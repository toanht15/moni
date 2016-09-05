<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
class index extends BrandcoManagerGETActionBase {

	protected $ContainerName = 'login';

	public function doThisFirst () {
		// ログインエラー時はエラーフラグをつける
		$this->Data['login_err'] = $this->login_err ? $this->login_err : '';
	}

	public function beforeValidate () {

		// ログイン中であればそのままダッシュボードへ
		if ($this->isLoginManager()) {
			return 'redirect: ' . Util::rewriteUrl ('dashboard', 'index', array(), array(), '', true);
		}

		$this->resetValidateError();
	}

	public function validate () {
		return true;
	}

	function doAction() {
		return 'manager/account/index.php';
	}
}
