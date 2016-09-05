<?php
AAFW::import('jp.aainc.classes.CacheManager');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

class SetBrandFansCount extends BrandcoBatchBase {

    function executeProcess() {
        /** @var BrandService $brand_service */
        $brand_service = $this->service_factory->create('BrandService');

        /** @var BrandsUsersRelationService $brands_users_service */
        $brand_users_service = $this->service_factory->create('BrandsUsersRelationService');

        $brands = $brand_service->getAllBrands();

        $execute_count = 0;
        $result = array();
        foreach ($brands as $brand) {
            $brands_users_count = 0;
            $this->setDataInfo('$brand_id = ' . $brand->id);
            $brands_users_count = $brand_users_service->countBrandsUsersRelationsByBrandId($brand->id);
            if ($brands_users_count) {
                $execute_count += $brands_users_count;
                $result[$brand->id] = $brands_users_count;
            }
        }

        /** @var CacheManager $cache_manager */
        $cache_manager = new CacheManager();

        foreach( $result as $brand_id => $count) {

            $cache_manager->addCache("fc", $count, array($brand_id));

        }
        $this->setExecuteCount($execute_count);
    }
}