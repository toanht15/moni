<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerGETActionBase');

class edit_agent_form extends BrandcoManagerGETActionBase {

    public $NeedManagerLogin = true;
    protected $ContainerName = 'edit_agent_form';

    public function validate() {
        if (!$this->GET['exts'][0]) {
            return '404';
        }

        /** @var ManagerService $manager_service */
        $manager_service = $this->getService('ManagerService');
        $manager = $manager_service->getManagerById($this->GET['exts'][0]);
        if (!$manager || $manager->authority != Manager::AGENT) {
            return '404';
        }

        return true;
    }

    public function doAction() {
        /** @var BrandsAgentService $brands_agent_service */
        $brands_agent_service = $this->getService('BrandsAgentService');
        /** @var BrandService $brand_service */
        $brand_service = $this->getService('BrandService');

        $this->Data['manager_id'] = $this->GET['exts'][0];
        $brands_agents = $brands_agent_service->getBrandsAgentByManagerId($this->Data['manager_id']);

        $this->Data['current_agent_brands'] = array();
        $current_agent_brand_ids = array(); //代理店の設定済みブランド

        foreach ($brands_agents as $brands_agent) {
            $brand = $brand_service->getBrandById($brands_agent->brand_id);
            $this->Data['current_agent_brands'][] = $brand->toArray();

            $current_agent_brand_ids[] = $brands_agent->brand_id;
        }

        //全部ブランドを取得する
        $brands = $brand_service->getAllBrands();
        //連携可能ブランド
        $this->Data['available_brands'] = array();
        $this->Data['available_brands'][0] = "";

        foreach ($brands as $brand) {
            if (in_array($brand->id, $current_agent_brand_ids)) {
                continue;
            }

            $this->Data['available_brands'][$brand->id] = $brand->id . ' | ' . $brand->name;
        }

        return 'manager/dashboard/edit_agent_form.php';
    }
}