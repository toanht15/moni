<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.util.AddressChecker');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class UpdateDuplicateAddressCountBrandUser extends BrandcoBatchBase{

    private $addressChecker;
    private $brandService;
    private $brandsUsersRelationService;
    private $db;

    const LIMIT_EXECUTE_RECORD = 100;

    public function __construct($argv = null) {
        parent::__construct($argv);
        $this->brandService = $this->service_factory->create('BrandService');
        $this->brandsUsersRelationService = $this->service_factory->create('BrandsUsersRelationService');
        $this->addressChecker = new AddressChecker();
        $this->db = aafwDataBuilder::newBuilder();
    }

    private function updateDuplicateAddressCount($brand) {
        try {

            $shippingAddresses = $this->getShippingAddressByBrandId($brand->id);

            $checkAddresses = array();
            $notHaveAddresses = array();

            foreach($shippingAddresses as $shippingAddress){
                if($this->isNotHaveAddress($shippingAddress)){
                    $notHaveAddresses[] = $shippingAddress['user_id'];
                }else{
                    $checkAddresses[] = $shippingAddress;
                }
            }

            unset($shippingAddresses);

            $position = 0;

            $sql = "";

            foreach($notHaveAddresses as $notHaveAddressId) {

                $position++;

                if($position == 1) {
                    $sql = "INSERT INTO brands_users_relations(id, duplicate_address_count, updated_at) VALUES ";
                }

                $sql .=  "({$notHaveAddressId}, ".BrandsUsersRelationService::NOT_HAVE_ADDRESS." , NOW()),";

                if($position >= self::LIMIT_EXECUTE_RECORD){

                    $position = 0;

                    $sql = substr($sql, 0, strlen($sql) - 1);
                    $sql .= " ON DUPLICATE KEY UPDATE duplicate_address_count = VALUES(duplicate_address_count), updated_at = NOW()";
                    $this->db->executeUpdate($sql);

                    $sql = "";
                }

            }

            unset($notHaveAddresses);

            $duplicateAddresses = $this->addressChecker->checkDuplicateWithDuplicateCountMoreThanZero($checkAddresses);

            unset($checkAddresses);

            foreach($duplicateAddresses as $duplicateAddressIds){

                $duplicateCount = count($duplicateAddressIds);

                foreach($duplicateAddressIds as $duplicateAddressId) {

                    $position++;

                    if($position == 1) {
                        $sql = "INSERT INTO brands_users_relations(id, duplicate_address_count, updated_at) VALUES ";
                    }

                    $sql .=  "({$duplicateAddressId}, ".$duplicateCount." , NOW()),";

                    if($position >= self::LIMIT_EXECUTE_RECORD){

                        $position = 0;

                        $sql = substr($sql, 0, strlen($sql) - 1);
                        $sql .= " ON DUPLICATE KEY UPDATE duplicate_address_count = VALUES(duplicate_address_count), updated_at = NOW()";
                        $this->db->executeUpdate($sql);

                        $sql = "";
                    }
                }
            }

            unset($duplicateAddresses);

            if($position){
                $sql = substr($sql, 0, strlen($sql) - 1);
                $sql .= " ON DUPLICATE KEY UPDATE duplicate_address_count = VALUES(duplicate_address_count), updated_at = NOW()";
                $this->db->executeUpdate($sql);
            }

        } catch(Exception $e){
            $this->logger->error('ブランドユーザ重複住所更新エラー： Brand_ID = '.$brand->id);
            $this->logger->error($e);
        }
    }

    function executeProcess() {
        $this->logger->info("start UpdateDuplicateAddressCountBrandUser");
        ini_set('memory_limit', '256M');
        $brands = $this->brandService->getAllBrands();
        foreach($brands as $brand){

            $this->logger->info('Executing brand： Brand_ID = '.$brand->id);

            $this->updateDuplicateAddressCount($brand);
            
        }
    }
    
    private function getShippingAddressByBrandId($brandId){
        $filter = array(
            'brand_id' => $brandId
        );
        $shippingAddresses = $this->db->getShippingAddressByBrand($filter);
        return $shippingAddresses;
    }

    private function isNotHaveAddress($shippingAddress){
        if($shippingAddress['address1'] == NULL || ($shippingAddress['address1'] != NULL && $shippingAddress['address1'] == ''
            && $shippingAddress['address2'] == '' && $shippingAddress['address3'] == '')){
            return true;
        }
        return false;
    }
}