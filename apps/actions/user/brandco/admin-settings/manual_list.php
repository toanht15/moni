<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class manual_list extends BrandcoGETActionBase {
    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {

        return true;
    }

    function doAction() {
        $manual_service = $this->createService('ManualService');
        //マニュアル一覧取得
        $this->Data['manuals'] = $manual_service->getAllManuals(array('order' => 'order_num ASC'));

        //ファンサイト構築マニュアル取得
        foreach($this->Data['manuals'] as $manual) {
            if($manual->type == Manuals::CMS) {
                $this->Data['manuals_cms'][] = $manual;
            }
        }

        //キャンペーン作成マニュアル取得
        foreach($this->Data['manuals'] as $manual) {
            if($manual->type == Manuals::CAMPAIGN) {
                $this->Data['manuals_campaign'][] = $manual;
            }
        }

        return 'user/brandco/admin-settings/manual_list.php';
    }
}
