<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');

class api_change_approval_instagram_hashtag_action extends BrandcoPOSTActionBase {
    protected $ContainerName = 'api_change_approval_instagram_hashtag_action';
    protected $AllowContent = array('JSON');

    public $NeedOption = array();
    public $NeedAdminLogin = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        /** @var CpInstagramHashtagActionService $cp_instagram_hashtag_action_service */
        $cp_instagram_hashtag_action_service = $this->createService('CpInstagramHashtagActionService');

        $cp_instagram_hashtag_action = $cp_instagram_hashtag_action_service->getCpInstagramHashtagActionByCpActionId($this->POST['action_id']);
        if ($cp_instagram_hashtag_action->approval_flg != $this->POST['approval_flg']) {
            $cp_instagram_hashtag_action->approval_flg = $this->POST['approval_flg'];
            $cp_instagram_hashtag_action_service->saveCpInstagramHashtagAction($cp_instagram_hashtag_action);
        }

        $json_data = $this->createAjaxResponse('ok');
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }
}