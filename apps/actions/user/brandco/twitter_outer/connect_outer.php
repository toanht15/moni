<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
require_once dirname(__FILE__) . '/../twitter/connect.php';

class connect_outer extends connect {

    public function validate() {
        // クライアント画面にログイン済みかチェック
        if (!parent::isLogin()) {
            return false;
        }
        // 認証チェック
        if ($this->getSession('login_outer') !== 1) {
            return false;
        }

        return parent::validate();
    }
}
