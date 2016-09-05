<?php
AAFW::import ('jp.aainc.classes.services.BrandGlobalMenuService');

class BrandGlobalMenuServiceTest extends BaseTest {

    /** @var  BrandGlobalMenuService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandGlobalMenuService");
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
    }

    /**
     * @test
     */
    public function getAllHiddenEntries() {
        $this->markTestSkipped('テストを記載するためには、本体のリファクタリングが必要です');
    }

    /**
     * @test
     */
    public function saveMenusByBrandIdAndMenuIdsAndPosts01_whenEmptyId() {
        $brand = $this->entity('Brands');
        aafwRedisManager::getRedisInstance()->set(
            'cache' . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, 'TEST'
        );

        $this->target->saveMenusByBrandIdAndMenuIdsAndPosts($brand->id, [], []);

        $this->assertEquals(false, aafwRedisManager::getRedisInstance()->get(
            'cache' . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id
        ));
    }

    /**
     * @test
     */
    public function getMenusByBrandIdAndMenuIds() {
        $this->markTestSkipped('テストを記載するためには、本体のリファクタリングが必要です(idとmenuIdsの部分)');
    }

    /**
     * @test
     */
    public function saveMenusByBrandIdAndMenuIdsAndPosts_新規登録() {
        list($brand, $user, $brand_users_relation) = $this->newBrandToBrandUsersRelation(); 

        $brand_id = $brand->id;
        $menu_ids = [1, 2, 3];
        $post = [];
        $this->target->saveMenusByBrandIdAndMenuIdsAndPosts($brand_id, $menu_ids, $post);

        $datas = $this->find('BrandGlobalMenus', [
            'brand_id' => $brand_id
        ]);
        $this->assertEquals(3, $datas->total());
        while ($data = $datas->current()) {
            $this->assertEquals($brand_id, $data->brand_id);
            $this->assertEquals(0, $data->hidden_flg);
            $this->assertEquals(0, $data->is_blank_flg);
            $datas->next();
        }
    }
    /**
     * @test
     */
    public function getMenusByBrandIdAndMenuIds_検索結果取得0件() {
        $brand_id = -1;
        $mene_id = 100;
        $target_menu = $this->target->getMenusByBrandIdAndMenuIds($brand_id, [$menu_id]);

        $this->assertNull($target_menu);
    }

    /**
     * @test
     */
    public function getGlobalMenuByCpLink_検索結果取得成功() {
        list($brand, $user, $brand_users_relation) = $this->newBrandToBrandUsersRelation();

        $cp_id = 10;
        $link = 'https://'.
                Util::getMappedServerName($brand->id) . '/' .
                Util::resolveDirectoryPath($brand->id, $brand->directory_name) .
                'campaigns/' . $cp_id;

        $menu = $this->entity('BrandGlobalMenus', [
            'brand_id' => $brand->id,
            'hidden_flg' => 1,
            'is_blank_flg' => 0,
            'link' => $link
        ]);

        $target_menu = $this->target->getGlobalMenuByCpLink($cp_id, $brand->id, $brand->directory_name)->current();

        $this->assertEquals($menu->id, $target_menu->id);
        $this->assertEquals($brand->id, $target_menu->brand_id);
        $this->assertEquals(1, $target_menu->hidden_flg);
        $this->assertEquals(0, $target_menu->is_blank_flg);
        $this->assertEquals($link, $target_menu->link);
    }
}
