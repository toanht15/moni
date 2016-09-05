<?php

class BrandBusinessCategoryServiceTest extends BaseTest {

    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandBusinessCategoryService");
    }

    public function testGetCategoryList() {
        $categoryList = array(
            1 => 'メーカー(食品・飲料・健康食品)',
            2 => 'メーカー（化粧品・美容・コスメ）',
            3 => 'メーカー（化学・医薬品）',
            4 => 'メーカー（家電・パソコン）',
            5 => 'メーカー（電気・精密機器）',
            6 => 'メーカー(自動車)',
            7 => 'メーカー（アパレル・スポーツ用品）',
            8 => 'メーカー（インテリア・雑貨）',
            9 => 'メーカー（その他）',
            10 => '小売（百貨店）',
            11 => '小売（家電）',
            12 => '小売（アパレル）',
            13 => '小売（CVS）',
            14 => '小売（その他）',
            15 => 'インターネットサービス・ネットメディア',
            16 => '金融・証券・保険',
            17 => '出版・印刷・放送',
            18 => 'Web制作・SEO',
            19 => '広告・PR・マーケティング',
            20 => '通信',
            21 => 'レジャー',
            22 => '人材紹介・ヘッドハンティング・リクルーティング',
            23 => '教育・学習・趣味',
            24 => '医療機関・福祉',
            25 => '飲食店（外食）',
            26 => '商社',
            27 => '交通・輸送',
            28 => '建設・不動産',
            29 => '水・ガス・電気・石油',
            30 => '農林・水産',
            31 => '工業',
            32=> '非営利法人・官公庁',
            33 => 'その他サービス',
        );

        $result = $this->target->getCategoryList();

        $this->assertEquals($categoryList, $result);
    }

    public function testGetSizeList() {
        $sizeList = array(
            1 => '超大',
            2 => '大',
            3 => '中',
            4 => '小',
        );

        $result = $this->target->getSizeList();

        $this->assertEquals($sizeList, $result);
    }

    public function testCreateEmptyBrandBusinessCategory() {
        $brandBusinessCategory = $this->target->createEmptyBrandBusinessCategory();

        $this->assertNull($brandBusinessCategory->id);
    }

    public function testCreateBrandBusinessCategory01_whenReturnBrandBusinessCategory() {
        $brand = $this->entity('Brands');
        $brandBusinessCategory = $this->target->createBrandBusinessCategory($brand->id, 1, 2);

        $this->assertEquals(array('brand_id' => $brand->id, 'category' => 1, 'size' => 2),
                            array('brand_id' => $brandBusinessCategory->brand_id, 'category' => $brandBusinessCategory->category, 'size' => $brandBusinessCategory->size));
    }

    public function testCreateBrandBusinessCategory02_whenReturnEmptyBrandBusinessCategory() {
        $brandBusinessCategory = $this->target->createBrandBusinessCategory(null, null, null);

        $this->assertEquals(array('brand_id' => null, 'category' => null, 'size' => null),
            array('brand_id' => $brandBusinessCategory->brand_id, 'category' => $brandBusinessCategory->category, 'size' => $brandBusinessCategory->size));
    }

    public function testGetOrCreateBrandBusinessCategoryByBrandId01_whenReturnBrandBusinessCategory() {
        $brand = $this->entity('Brands');
        $this->entity('BrandBusinessCategories', array('brand_id' => $brand->id, 'category' => 1, 'size' => 2));

        $result = $this->target->getOrCreateBrandBusinessCategoryByBrandId($brand->id);

        $this->assertEquals(array('brand_id' => $brand->id, 'category' => 1, 'size' => 2),
                            array('brand_id' => $result->brand_id, 'category' => $result->category, 'size' => $result->size));
    }

    public function testGetOrCreateBrandBusinessCategoryByBrandId02_whenReturnEmptyBrandBusinessCategory() {
        $result = $this->target->getOrCreateBrandBusinessCategoryByBrandId(null);

        $this->assertEquals(array('brand_id' => null, 'category' => null, 'size' => null),
                            array('brand_id' => $result->brand_id, 'category' => $result->category, 'size' => $result->size));
    }

    public function testSaveBrandBusinessCategory01_whenReturnBrandBusinessCategory() {
        $brand = $this->entity('Brands');
        $brandBusinessCategory = $this->entity('BrandBusinessCategories', array('brand_id' => $brand->id, 'category' => 1, 'size' => 2));
        $brandBusinessCategory->category    = 3;
        $brandBusinessCategory->size       = 4;

        $result = $this->target->saveBrandBusinessCategory($brandBusinessCategory);

        $this->assertEquals(array('brand_id' => $brand->id, 'category' => 3, 'size' => 4),
                            array('brand_id' => $result->brand_id, 'category' => $result->category, 'size' => $result->size));
    }

    public function testSaveBrandBusinessCategory02_whenReturnEmptyBrandBusinessCategory() {
        $result = $this->target->saveBrandBusinessCategory(null);

        $this->assertEquals(array('brand_id' => null, 'category' => null, 'size' => null),
                            array('brand_id' => $result->brand_id, 'category' => $result->category, 'size' => $result->size));
    }
}