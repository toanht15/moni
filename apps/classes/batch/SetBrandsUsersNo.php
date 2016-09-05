<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

class SetBrandsUsersNo extends BrandcoBatchBase {

    function executeProcess() {
        /** @var BrandService $brand_service */
        $brand_service = $this->service_factory->create('BrandService');

        /** @var BrandsUsersRelationService $brands_users_service */
        $brands_users_relation_service = $this->service_factory->create('BrandsUsersRelationService');

        /** @var BrandsUsersRelationService $brands_users_service */
        $brand_max_relation_no_service = $this->service_factory->create('BrandMaxRelationNoService');

        /** @var BrandMaxRelationNos $brand_max_relation_entity */
        $brand_max_relation_entity = aafwEntityStoreFactory::create('BrandMaxRelationNos');

        $brands = $brand_service->getAllBrands();

        $execute_count = 0;
        foreach ($brands as $brand) {
            $this->setDataInfo('$brand_id = ' . $brand->id);
            $new_brands_users_relations = $brands_users_relation_service->getNewBrandsUsersRelations($brand->id);

            foreach($new_brands_users_relations as $brands_users_relation) {
                try {
                    $brand_max_relation_entity->begin();

                    // 現時点の会員番号の最大値を取得
                    $brand_max_relation_no = $brand_max_relation_no_service->getMaxNoByBrandIdForUpdate($brand->id);
                    if($brand_max_relation_no) {
                        $max_no = $brand_max_relation_no->max_no;
                    } else {
                        $max_no = $brands_users_relation_service->getSavedBrandsUsersRelationsNo($brand->id);
                        $brand_max_relation_no = aafwEntityStoreFactory::create('BrandMaxRelationNos');
                        $brand_max_relation_no->brand_id = $brand->id;
                    }

                    $brands_users_relation->no = $max_no + 1;
                    $brands_users_relation_service->createBrandsUsersRelation($brands_users_relation);

                    $brand_max_relation_no->max_no = $max_no + 1;
                    $brand_max_relation_no_service->setMaxNo($brand_max_relation_no);

                    $brand_max_relation_entity->commit();
                    $execute_count += 1;
                } catch (Exception $e) {
                    $brand_max_relation_entity->rollback();
                    throw $e;
                }
            }
        }
        $this->setExecuteCount($execute_count);
    }
}