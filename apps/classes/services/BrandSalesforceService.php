<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class BrandSalesforceService extends aafwServiceBase {

    private $brandSalesforces;

    public function __construct() {
        $this->brandSalesforces = $this->getModel('BrandSalesforces');
    }

    public function createEmptyBrandSalesforce() {
        return $this->brandSalesforces->createEmptyObject();
    }

    public function saveBrandSalesforce($brandSalesforce) {
        return $this->brandSalesforces->save($brandSalesforce);
    }

    public function createBrandSalesforce($brandId, $salesforceUrl, $startDate, $endDate) {
        $brandSalesforce = $this->createEmptyBrandSalesforce();

        $brandSalesforce->brand_id       = $brandId;
        $brandSalesforce->url            = $salesforceUrl;
        $brandSalesforce->start_date     = $startDate;
        $brandSalesforce->end_date       = $endDate;

        return $this->saveBrandSalesforce($brandSalesforce);
    }

    public function getBrandSalesforcesByBrandId($brandId) {
        return $this->brandSalesforces->find( array('brand_id' => $brandId) );
    }

    public function countSalesforceByBrandId($brandId) {
        return $this->brandSalesforces->count( array('brand_id' => $brandId) );
    }

    public function getOrCreateBrandSalesforceById($id) {
        $filter = array(
            'conditions' => array(
                'id' => $id
            )
        );

        $salesforce = $this->brandSalesforces->findOne($filter);

        if ($salesforce) {
            return $salesforce;
        } else {
            return $this->createEmptyBrandSalesforce();
        }
    }

}