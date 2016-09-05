<?php

class BrandSalesforceServiceTest extends BaseTest {

    private $target;

    public function setUp() {
        $this->target = aafwServiceFactory::create("BrandSalesforceService");
    }

    public function testCreateEmptyBrandSalesforce() {
        $result = $this->target->createEmptyBrandSalesforce();

        $this->assertNull($result->id);
    }

    public function testSaveBrandSalesforce01_whenReturnBrandSalesforce() {
        $brand = $this->entity('Brands');
        $brandSalesforce = $this->entity('BrandSalesforces',
                                                array('brand_id' => $brand->id,
                                                      'url' => 'https://ap.salesforce.com/hoge',
                                                      'start_date' => '2016-02-05',
                                                      'end_date' => '2016-02-06')
        );

        $brandSalesforce->url           = 'https://ap.salesforce.com/huga';
        $brandSalesforce->start_date    = '2016-02-10';
        $brandSalesforce->end_date      = '2016-02-20';

        $result = $this->target->saveBrandSalesforce($brandSalesforce);

        $this->assertEquals(array('url' => 'https://ap.salesforce.com/huga', 'start_date' => '2016-02-10', 'end_date' => '2016-02-20'),
                            array('url' => $result->url, 'start_date' => $result->start_date, 'end_date' => $result->end_date));
    }

    public function testSaveBrandSalesforce02_whenReturnEmptyBrandSalesforce() {
        $result = $this->target->saveBrandSalesforce(null);

        $this->assertEquals(array('url' => null, 'start_date' => null, 'end_date' => null),
                            array('url' => $result->url, 'start_date' => $result->start_date, 'end_date' => $result->end_date));
    }

    public function testCreateBrandSalesforce01_whenReturnBrandSalesforce() {
        $brand = $this->entity('Brands');

        $result = $this->target->createBrandSalesforce($brand->id, 'https://ap.salesforce.com/hoge', '2016-02-20', '2016-03-20');

        $this->assertEquals(array('brand_id' => $brand->id, 'url' => 'https://ap.salesforce.com/hoge', 'start_date' => '2016-02-20', 'end_date' => '2016-03-20'),
                            array('brand_id' => $result->brand_id, 'url' => $result->url, 'start_date' => $result->start_date, 'end_date' => $result->end_date));
    }

    public function testCreateBrandSalesforce02_whenReturnEmptyBrandSalesforce() {
        $result = $this->target->createBrandSalesforce(null, null, null, null);

        $this->assertEquals(array('brand_id' => null, 'url' => null, 'start_date' => null, 'end_date' => null),
                            array('brand_id' => $result->brand_id, 'url' => $result->url, 'start_date' => $result->start_date, 'end_date' => $result->end_date));
    }

    public function testGetBrandSalesforcesByBrandId01_whenReturnArray() {
        $brand = $this->entity('Brands');
        $brandSalesforceNo1 = $this->entity('BrandSalesforces',
            array('brand_id' => $brand->id,
                'url' => 'https://ap.salesforce.com/hoge',
                'start_date' => '2016-02-05',
                'end_date' => '2016-02-06')
        );
        $brandSalesforceNo2 = $this->entity('BrandSalesforces',
            array('brand_id' => $brand->id,
                'url' => 'https://ap.salesforce.com/huga',
                'start_date' => '2016-03-05',
                'end_date' => '2016-03-06')
        );

        $results = $this->target->getBrandSalesforcesByBrandId($brand->id);

        foreach ($results as $result) {
            $this->assertEquals(array('brand_id' => $brand->id), array('brand_id' => $result->brand_id));
        }
    }

    public function testGetBrandSalesforcesByBrandId02_whenReturnEmptyArray() {
        $result = $this->target->getBrandSalesforcesByBrandId(null);

        $this->assertNull($result->id);
    }

    public function testCountSalesforceByBrandId() {
        $brand = $this->entity('Brands');
        $brandSalesforceNo1 = $this->entity('BrandSalesforces',
            array('brand_id' => $brand->id,
                'url' => 'https://ap.salesforce.com/hoge',
                'start_date' => '2016-02-05',
                'end_date' => '2016-02-06')
        );
        $brandSalesforceNo2 = $this->entity('BrandSalesforces',
            array('brand_id' => $brand->id,
                'url' => 'https://ap.salesforce.com/huga',
                'start_date' => '2016-03-05',
                'end_date' => '2016-03-06')
        );

        $result = $this->target->countSalesforceByBrandId($brand->id);

        $this->assertEquals(2, $result);
    }

    public function testGetOrCreateBrandSalesforceById01_whenReturnBrandSalesforce() {
        $brand = $this->entity('Brands');
        $brandSalesforce = $this->entity('BrandSalesforces',
            array('brand_id' => $brand->id,
                'url' => 'https://ap.salesforce.com/huga',
                'start_date' => '2016-03-05',
                'end_date' => '2016-03-06')
        );

        $result = $this->target->getOrCreateBrandSalesforceById($brandSalesforce->id);

        $this->assertEquals(array('id' => $brandSalesforce->id, 'brand_id' => $brand->id), array('id' => $result->id, 'brand_id' => $result->brand_id));
    }

    public function testGetOrCreateBrandSalesforceById02_whenReturnEmptyBrandSalesforce() {
        $result = $this->target->getOrCreateBrandSalesforceById(null);

        $this->assertNull($result->id);
    }
}