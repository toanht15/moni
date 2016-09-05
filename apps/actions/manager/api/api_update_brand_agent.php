<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoManagerPOSTActionBase');

/**
 * 代理店とブランド連携更新API
 * Class api_update_brand_agent
 */
class api_update_brand_agent extends BrandcoManagerPOSTActionBase {
    const UPDATE_TYPE_ADD = 1;
    const UPDATE_TYPE_DELETE = 2;

    protected $AllowContent = array('JSON');
    protected $brand_service;
    protected $ContainerName = 'api_update_brand_agent';

    private $brand_id;
    private $manager_id;
    private $update_type;
    /** @var  BrandsAgentService $brands_agent_service */
    private $brands_agent_service;

    public function beforeValidate() {
        $this->brand_id = $this->POST['brand_id'];
        $this->manager_id = $this->POST['manager_id'];
        $this->update_type = $this->POST['update_type'];
    }

    public function validate() {
        if (!$this->brand_id || !$this->manager_id || !$this->update_type) {
            return false;
        }

        $this->brands_agent_service = $this->getService('BrandsAgentService');
        $brand_agent = $this->brands_agent_service->getBrandAgentByBrandIdAndManagerId($this->brand_id, $this->manager_id);

        //追加する場合は、brand_agentが存在すれば、falseを戻す
        //削除する場合は、brand_agentが存在しなければ、falseを戻す
        if (($this->update_type == self::UPDATE_TYPE_ADD && $brand_agent) ||
            ($this->update_type == self::UPDATE_TYPE_DELETE && !$brand_agent)) {
            return false;
        }

        return true;
    }

    public function doAction() {
        if ($this->update_type == self::UPDATE_TYPE_ADD) {
            $this->brands_agent_service->updateBrandAgent($this->brand_id, $this->manager_id);
        } elseif ($this->update_type == self::UPDATE_TYPE_DELETE){
            $brand_agent = $this->brands_agent_service->getBrandAgentByBrandIdAndManagerId($this->brand_id, $this->manager_id);
            $this->brands_agent_service->deletePhysicalBrandAgent($brand_agent);
        }

        $json_data = $this->createAjaxResponse("ok");
        $this->assign('json_data', $json_data);

        return "dummy.php";
    }
}