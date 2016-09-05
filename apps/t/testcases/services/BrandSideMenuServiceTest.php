<?php
AAFW::import ('jp.aainc.classes.services.BrandSideMenuService');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class BrandSideMenuServiceTest extends BaseTest {

    /** @var  BrandSideMenuService $target */
    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandSideMenuService");
        aafwRedisManager::getRedisInstance()->flushAll(); // 抹殺!
    }

    public function testSaveMenusByBrandIdAndMenuIdsAndPosts01_whenAbsent() {
        $brand = $this->entity('Brands');
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->saveMenusByBrandIdAndMenuIdsAndPosts($brand->id, array(), "");

        $this->assertFalse(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id));
    }

    public function testSaveMenusByBrandIdAndMenuIdsAndPosts02_whenExist() {
        $brand = $this->entity('Brands');
        $this->entity("BrandSideMenus", array("brand_id" => $brand->id));
        aafwRedisManager::getRedisInstance()->set("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id, "TEST");

        $this->target->saveMenusByBrandIdAndMenuIdsAndPosts(
            $brand->id,
            array(1, 2),
            array("link_1" => "http://hogehoge.org", "title_1" => "TEST", "hidden_flg_1" => "1", "is_blank_flg_1" => "on"));

        $menus = $this->find("BrandSideMenus", array("brand_id" => $brand->id))->toArray();
        $menu1 = $menus[0];
        $menu2 = $menus[1];

        $this->assertEquals(
            array(false,
                2,
                "http://hogehoge.org", "TEST", 1, 1,
                "", "", 0, 0),
            array(aafwRedisManager::getRedisInstance()->get("cache" . CacheManager::KEY_BRAND_INFO_CONTAINER . $brand->id),
                count($menus),
                $menu1->link, $menu1->name, $menu1->hidden_flg, $menu1->is_blank_flg,
                $menu2->link, $menu2->name, $menu2->hidden_flg, $menu2->is_blank_flg)
        );
    }
}