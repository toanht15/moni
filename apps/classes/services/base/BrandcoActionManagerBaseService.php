<?php
AAFW::import('jp.aainc.aafw.base.aafwActionBase');
AAFW::import('jp.aainc.aafw.web.aafwWidgets');
AAFW::import('jp.aainc.classes.services.base.BrandcoActionManagerBaseInterface');
AAFW::import('jp.aainc.classes.services.ManagerService');
AAFW::import('jp.aainc.classes.entities.BrandOption');
trait BrandcoActionManagerBaseService {

    protected $set_log_flg = false;
    public function doService() {
        $manager_user = $this->getSession('managerUserId');
        $getManagerAccount = $this->createService('ManagerService');
        $managerAccount = $getManagerAccount->getManagerFromHash($manager_user);
        $this->Data['managerAccount'] = $managerAccount;
        if($this->isValidManagerPageAuthority() === false){
            return 403;
        }
        return parent::doService();
    }

	public function isLoginManager() {

		$manager_user = $this->getSession('managerUserId');
        if(!$manager_user) {
            return false;
        }
        $getManagerAccount = $this->createService('ManagerService');
        $managerAccount = $getManagerAccount->getManagerFromHash($manager_user);

		if(!$managerAccount) {
			return false;
		} else {
            if(!$this->set_log_flg) {
                $this->setOperationManagerLog($manager_user, $managerAccount);
                $this->set_log_flg = true;
            }
			return true;
		}
	}
    private function isValidManagerPageAuthority() {
        if($this->managerAccount == null){
            $managerService = $this->createService('ManagerService');
            $this->managerAccount = $managerService->getManagerFromHash($this->SESSION['managerUserId']);
        }
        if($_SERVER['REQUEST_METHOD'] != 'GET'){
            return true;
        }
        if(count($this->ManagerPageId) == 0){
            return true;
        }else{
            return $this->managerAccount->isAllowedPage($this->ManagerPageId);
        }
    }


	public function setOperationManagerLog($manager_user, $managerAccount) {

		// オペレーションログを記録する
		$operationManagerLog = $this->createService('OperationLogManagerDataService');
		$operationManagerLog->setOperationReferer($_SERVER['HTTP_REFERER']);
		$operationManagerLog->setOperationLog($manager_user, $managerAccount);
	}

    /**
     * Override
     */
    public function getContainerType() {
        return 'manager';
    }
}
