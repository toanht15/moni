<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.entities.Manager');

class manager_list extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId    = Manager::MENU_MANAGER_LIST;

	public function validate () {
		return true;
	}

    function doAction() {
        /** @var ManagerService $manager_service */
        $manager_service = $this->createService('ManagerService');
        if (!$this->auth || $this->auth) {
            $array_auth = Manager::$MANAGER_AUTHORITY_LIST;
            $array_default = array("-1" => "-");
            $this->Data['auth'] = $array_default + $array_auth;
        } else {
            $this->Data["auth"] = $this->auth;
        }
        if (!$this->limit || !$this->isNumeric($this->limit)) {
            $this->limit = 20;
        }
        if (!$this->p) {
            $this->p = 1;
        }
        $pager = array(
            'page' => $this->p,
            'count' => $this->limit,
        );
        $condition = array();
        if ($this->manager_id) {
            $condition['ID'] = '__ON__';
            $condition['manager_id'] = $this->manager_id;
        }
        if ($this->manager_name) {
            $condition['NAME'] = '__ON__';
            $condition['manager_name'] = '%' . $this->manager_name . '%';
        }
        if ($this->manager_mail) {
            $condition['EMAIL'] = '__ON__';
            $condition['manager_email'] = '%' . $this->manager_mail . '%';
        }
        if ($this->auth >= 0){
            $condition['AUTH'] = '__ON__';
            $condition['manager_auth'] = $this->auth;
        }
        $db = new aafwDataBuilder();
        $manager = $db->getManagerSearch($condition, null, $pager, true, 'Manager');
        $this->Data['allManagerCount'] = $manager['pager']['count'];
        /** @var LoginLogManagerDataService $login_log_manager_data_service */
        $login_log_manager_data_service = $this->createService('LoginLogManagerDataService');
        $this->Data['managerAccounts'] = array();
        foreach ($manager['list'] as $managerAccount) {
            $manager = $managerAccount->toArray();
            $manager['last_login_date'] = $managerAccount->login_date;
            $manager['login_count'] = $login_log_manager_data_service->getLoginCount($managerAccount->mail_address);
            $this->Data['managerAccounts'][] = $manager;
        }
        $total_page = ceil($this->Data['allManagerCount'] / $this->limit);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['limit'] = $this->limit;

        return 'manager/dashboard/manager_list.php';
    }

}