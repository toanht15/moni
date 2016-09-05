<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class api_count_cp_user_action_messages extends BrandcoGETActionBase{

    protected $AllowContent = array('JSON');
    public $NeedOption = array();
    public $NeedUserLogin = true;

    function validate() {
        return true;
    }

    function doAction() {
        $cp_service = $this->createService('CpFlowService');
        $cp = $cp_service->getCpById($this->GET['cp_id']);
        $cp_actions = $cp_service->getCpActionsByCpId($cp->id);
        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->createService('CpUserService');
        $result = array();
        foreach($cp_actions as $cp_action){
            if($cp_action->order_no == 1){
                $result[$cp_action->id]['sendCount'] = $cp_user_service->getSendMessageCount($cp_action->id);
            }
            $result[$cp_action->id]['readCount'] = $cp_user_service->getReadPageCount($cp_action->id);
            $result[$cp_action->id]['finishCount'] = $cp_user_service->getFinishActionCount($cp_action->id);
        }
        $json_data = $this->createApiResponse("ok",$result);
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}
