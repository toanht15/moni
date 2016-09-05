<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.entities.Manager');


class agent_list extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    public $ManagerPageId = Manager::MENU_MANAGER_LIST;

    private $limit = 20;    //デフォルトの表示件/1ページ
    private $p     = 1;     //デフォルトのページ

    public function validate() {
        return true;
    }

    public function doAction() {
        if ($this->GET['limit'] && $this->isNumeric($this->GET['limit'])) {
            $this->limit = $this->GET['limit'];
        }
        if ($this->GET['limit'] && $this->isNumeric($this->GET['p'])) {
            $this->p = $this->GET['p'];
        }
        $pager = array(
            'page' => $this->p,
            'count' => $this->limit,
        );

        $conditions = array(
            'AUTH'         => '__ON__',
            'manager_auth' => Manager::AGENT
        );

        $db = new aafwDataBuilder();
        $agent = $db->getManagerSearch($conditions, null, $pager, true, 'Manager');
        $this->Data['total_count'] = $agent['pager']['count'];
        $this->Data['agent_list'] = array();

        foreach ($agent['list'] as $managerAccount) {
            $manager = $managerAccount->toArray();
            $this->Data['agent_list'][] = $manager;
        }
        $total_page = ceil($this->Data['total_count'] / $this->limit);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['limit'] = $this->limit;

        return 'manager/dashboard/agent_list.php';
    }
}