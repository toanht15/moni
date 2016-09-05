<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.entities.Brand');
AAFW::import('jp.aainc.classes.entities.Cp');
AAFW::import('jp.aainc.classes.services.ApplicationService');

class index extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    protected $ContainerName = 'index';
    protected $Form = array(
        'package' => 'brands',
        'action' => 'index',
    );

    public static $delete_status_list = array(
        -1 => '指定なし',
        BrandContracts::MODE_OPEN => '公開前もしくは公開中',
        BrandContracts::MODE_CLOSED => 'クローズ表示切替期間中',
        BrandContracts::MODE_SITE_CLOSED => 'クローズ済',
        BrandContracts::MODE_DATA_DELETED => 'クローズ済かつデータ削除済',
    );

    public function validate() {
        return true;
    }

    function doAction() {
        if (!$this->limit || !$this->isNumeric($this->limit)) {
            $this->limit = 100;
        }
        if (!$this->p) {
            $this->p = 1;
        }
        $pager = array(
            'page' => $this->p,
            'count' => $this->limit,
            'offset' => ($this->p - 1) * $this->limit,
        );

        $condition = $this->setBrandListCondition();

        $db = new aafwDataBuilder();
        $brand_list = $db->getBrandList($condition, null, $pager, true, 'Brand');
        $this->Data['list'] = $brand_list['list'];

        $this->Data['allBrandCount'] = $brand_list['pager']['count'];
        $total_page = floor($this->Data['allBrandCount'] / $this->limit) + ($this->Data['allBrandCount'] % $this->limit > 0);
        $this->p = Util::getCorrectPaging($this->p, $total_page);
        $this->Data['limit'] = $this->limit;
        if ($this->mode == ManagerService::ADD_FINISH){
            /** @var BrandService $brand_service */
            $brand_service = $this->getService('BrandService');
            $this->Data['brand_name'] = $brand_service->getBrandById($this->brand_id)->name;
        }

        /** @var ManagerService $manager_service */
        $manager_service = $this->getService('ManagerService');
        $managers = $manager_service->getManagers('mail_address');
        $manager_list = array(0 => '指定なし');
        foreach ($managers as $manager) {
            $manager_list[$manager->id] = $manager->name;
        }
        $this->Data['select_list_manager'] = $manager_list;

        // クローズ関連の絞り込み選択肢をviewに返す
        $this->Data['delete_status_list'] = self::$delete_status_list;

        return 'manager/brands/index.php';
    }

    private function setBrandListCondition() {
        $condition = array();

        //累計ファン数
        /** @var ManagerBrandKpiService $manager_brand_kpi_service */
        $manager_brand_kpi_service = $this->getService('ManagerBrandKpiService');
        $manager_brand_kpi_column = $manager_brand_kpi_service->getManagerBrandKpiColumnByImport('jp.aainc.classes.manager_brand_kpi.BrandsUsersNum');
        $condition['column_id'] = $manager_brand_kpi_column->id;
        $condition['today_date'] = date('Y-m-d', strtotime('-2 days'));
        $condition['yesterday_date'] = date('Y-m-d', strtotime('-3 days'));

        if ($this->search_brand_name) {
            $condition['SEARCH_BRAND_NAME'] = '__ON__';
            $condition['search_brand_name'] = '%' . $this->search_brand_name . '%';
        }
        if ($this->access > 0) {
            $condition['ACCESS'] = '__ON__';
            $condition['public_flg'] = $this->access;
        }
        if ($this->account == Brand::BRAND_ENTERPRISE_PAGE || $this->account == Brand::BRAND_TEST_PAGE) {
            $condition['ACCOUNT'] = '__ON__';
            $condition['test_page'] = (int)$this->account;
        }
        if ($this->sales) {
            $condition['SALES'] = '__ON__';
            $condition['sales_manager_id'] = $this->sales;
        }
        if ($this->consultant) {
            $condition['CONSULTANT'] = '__ON__';
            $condition['consultants_manager_id'] = $this->consultant;
        }
        if ($this->delete_status >= 0) {
            $condition['DELETE_STATUS'] = '__ON__';
            $condition['delete_status'] = $this->delete_status;
        }
        if ($this->Data['managerAccount']->authority == Manager::AGENT) {
            $condition['IS_AGENT'] = '__ON__';
            $condition['manager_id'] = $this->Data['managerAccount']->id;
        }

        return $condition;
    }
}
