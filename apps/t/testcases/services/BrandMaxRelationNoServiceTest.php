<?php
AAFW::import ('jp.aainc.classes.services.BrandMaxRelationNoService');

class BrandMaxRelationNoServiceTest extends BaseTest {

    /** @var BrandMaxRelationNoService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create('BrandMaxRelationNoService');
    }

    /**
     * @test
     */
    public function getMaxNoByBrandIdForUpdate_引数がNullの場合() {
        $brand_id = null;
        $brand_max_relation_no = $this->target->getMaxNoByBrandIdForUpdate($brand_id);

        $this->assertNull($brand_max_relation_no);
    }

    /**
     * @test
     */
    public function getMaxNoByBrandIdForUpdate_該当データの取得成功() {
        list($brand, $user, $brand_users_relation) = $this->newBrandToBrandUsersRelation();
        $store = 'BrandMaxRelationNos';
        $max_no = 10;
        $properties = [
            'brand_id' => $brand->id,
            'max_no' => $max_no
        ];
        $this->entity($store, $properties);
        $brand_max_relation_no = $this->target->getMaxNoByBrandIdForUpdate($brand->id);

        $this->assertEquals($brand->id, $brand_max_relation_no->brand_id);
        $this->assertEquals($max_no, $brand_max_relation_no->max_no);
    }

    /**
     * @test
     */
    public function getMaxNoByBrandIdForUpdate_該当データの取得失敗() {
        $brand_id = -1;
        $brand_max_relation_no = $this->target->getMaxNoByBrandIdForUpdate($brand_id);

        $this->assertNull($brand_max_relation_no);
    }

    /**
     * @test
     */
    public function setMaxNo_MaxNoの更新あり() {
        list($brand, $user, $brand_users_relation) = $this->newBrandToBrandUsersRelation();
        $store = 'BrandMaxRelationNos';
        $max_no = 1;
        $properties = [
            'brand_id' => $brand->id,
            'max_no' => $max_no
        ];
        $brand_relation_no = $this->entity($store, $properties);

        $this->assertEquals($max_no, $brand_relation_no->max_no);

        $max_no = 20;
        $brand_relation_no->max_no = $max_no;
        $this->target->setMaxNo($brand_relation_no);

        $data = $this->findOne($store, ['id' => $brand_relation_no->id]);
        $this->assertEquals($brand_relation_no->id, $data->id);
        $this->assertEquals($max_no, $data->max_no);
    }

    /**
     * @test
     */
    public function setMaxNo_MaxNoの更新なし() {
        list($brand, $user, $brand_users_relation) = $this->newBrandToBrandUsersRelation();
        $store = 'BrandMaxRelationNos';
        $max_no = 50;
        $properties = [
            'brand_id' => $brand->id,
            'max_no' => $max_no
        ];
        $brand_relation_no = $this->entity($store, $properties);

        $this->assertEquals($max_no, $brand_relation_no->max_no);

        $brand_relation_no->max_no = $max_no;
        $this->target->setMaxNo($brand_relation_no);

        $data = $this->findOne($store, ['id' => $brand_relation_no->id]);
        $this->assertEquals($brand_relation_no->id, $data->id);
        $this->assertEquals($max_no, $data->max_no);
    }
}
