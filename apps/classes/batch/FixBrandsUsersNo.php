<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

class FixBrandsUsersNo extends BrandcoBatchBase {

    function executeProcess() {
        /** @var BrandService $brand_service */
        $brand_service = $this->service_factory->create('BrandService');

        /** @var BrandsUsersRelationService $brands_users_service */
        $brands_users_relation_service = $this->service_factory->create('BrandsUsersRelationService');

        $brands = $brand_service->getAllBrands();

        $execute_count = 0;
        foreach ($brands as $brand) {
            $this->setDataInfo('$brand_id = ' . $brand->id);
            $all_brands_users_relations = $brands_users_relation_service->getBrandsUsersRelationsByBrandIdOrderById($brand->id);

            $brands_users_relation_no = 1;
            foreach($all_brands_users_relations as $brands_users_relation) {
                $brands_users_relation->no = $brands_users_relation_no;
                $brands_users_relation_service->createBrandsUsersRelation($brands_users_relation);

                $brands_users_relation_no += 1;
                $execute_count += 1;
            }
        }
        $this->setExecuteCount($execute_count);
    }
}