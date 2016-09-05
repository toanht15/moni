<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

//リダイレクトアクション、一ヶ月後に消す
class inbox extends BrandcoGETActionBase {

    public $NeedOption = array();

    public function validate() {
        return true;
    }

    function doAction() {
        return 'redirect:' . Util::rewriteUrl('mypage', 'inbox');
    }
}
