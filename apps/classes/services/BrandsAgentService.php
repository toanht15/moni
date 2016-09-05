<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandsAgentService extends aafwServiceBase {

    protected $brands_agent_store;

    public function __construct() {
        $this->brands_agent_store = $this->getModel('BrandsAgents');
    }

    /**
     * @param $brand_id
     * @param $manager_id
     */
    public function updateBrandAgent($brand_id, $manager_id) {
        $brand_agent = $this->getBrandAgentByBrandIdAndManagerId($brand_id, $manager_id);
        if (!$brand_agent) {
            $brand_agent = $this->brands_agent_store->createEmptyObject();
        }
        $brand_agent->brand_id = $brand_id;
        $brand_agent->manager_id = $manager_id;

        $this->brands_agent_store->save($brand_agent);
    }

    /**
     * @param $brand_id
     * @param $manager_id
     * @return mixed
     */
    public function getBrandAgentByBrandIdAndManagerId($brand_id, $manager_id) {
        $filter = array(
            'brand_id' => $brand_id,
            'manager_id' => $manager_id
        );

        return $this->brands_agent_store->findOne($filter);
    }

    /**
     * @param $manager_id
     * @return mixed
     */
    public function getBrandsAgentByManagerId($manager_id) {
        $filter = array(
            'manager_id' => $manager_id
        );

        return $this->brands_agent_store->find($filter);
    }

    /**
     * @param $brand_agent
     * @return mixed
     */
    public function deletePhysicalBrandAgent($brand_agent) {
        return $this->brands_agent_store->deletePhysical($brand_agent);
    }
}