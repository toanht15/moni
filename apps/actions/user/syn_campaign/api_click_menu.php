<?php
AAFW::import('jp.aainc.aafw.base.aafwPOSTActionBase');
AAFW::import('jp.aainc.classes.services.instant_win.SynInstantWinService');

class api_click_menu extends aafwPOSTActionBase {

    protected $ContainerName = 'api_click_menu';
    protected $AllowContent = array('JSON');
    public $CsrfProtect = true;

    public function validate() {
        return true;
    }

    public function doAction() {
        /** @var SynInstantWinService $synInstantWinService */
        $synInstantWinService = $this->getService('SynInstantWinService');
        $userInfo = (object)$this->getSession('pl_monipla_userInfo');
        $user_service = $this->getService('UserService');
        $user = $user_service->getUserByMoniplaUserId($userInfo->id);
        $CpStore = $this->getModel('Cps');
        $cp = $CpStore->findOne($this->cp_id);
        if( $cp ) {
            $synCp = $cp->getSynCp();
            if( $synCp ) {
                $synInstantWinService->saveClickMenu($user->id, $synCp->id);
            }
        }
        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);
    }
}
