<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_check_joined_user extends BrandcoGETActionBase {
    public $NeedOption = array();
    protected $AllowContent = array('JSON');

    public function validate() {
        $brand = $this->getBrand();
        $validatorService = new CpValidator($brand->id);
        if (!$validatorService->isOwner($this->GET['cp_id'])) {
            $json_data = $this->createAjaxResponse("ng");
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }
        return true;
    }

    function doAction() {
        /** @var $service_factory aafwServiceFactory */
        $service_factory = new aafwServiceFactory();
        /** @var $cp_flow_service CpFlowService */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $cp = $cp_flow_service->getCpById($this->GET['cp_id']);

        $exist_not_editable_groups = $cp_flow_service->getNotEditableGroups($cp->id);
        $data = array();

        // 編集できない条件
        // 1.キャンペーンが公開予約中（メッセージは予約中でも可）
        // 2.既に参加してしまったか、送信予約中
        // 3.キャンペーン公開後
        if(($cp->status == Cp::STATUS_SCHEDULE && $cp->type == Cp::TYPE_CAMPAIGN) || $exist_not_editable_groups || $cp->status >= Cp::STATUS_FIX) {
            $data['alert_message'] = 'デモ公開中、公開予約中、公開後は参加完了までのフローの編集ができません。</br>また、配信済や配信予約中のメッセージもフローの編集ができません。';
            $json_data = $this->createAjaxResponse("ok", $data);
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        }

        $json_data = $this->createAjaxResponse("ok", $data);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
