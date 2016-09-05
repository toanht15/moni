<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.util.AddressChecker');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class UpdateDuplicateAddressCountCpUser extends BrandcoBatchBase{

    private $cpFlowService;
    private $cpUserService;
    protected $addressChecker;
    private $db;

    const LIMIT_EXECUTE_RECORD = 1000;

    public function __construct($argv = null) {

        parent::__construct($argv);

        $this->cpFlowService = $this->service_factory->create('CpFlowService');
        $this->cpUserService = $this->service_factory->create('CpUserService');

        $this->addressChecker = new AddressChecker();

        $this->db = aafwDataBuilder::newBuilder();

    }

    function executeProcess() {

        ini_set('memory_limit', '256M');

        $limitMode = ($this->argv['limit_mode']  == 'y' ) ? true : false;

        $targetCpIds = $this->cpFlowService->getCpIdsHaveMoreThan2Group($limitMode);

        foreach($targetCpIds as $cpId){

            if($this->cpFlowService->isNeedUpdateDuplicateAddressCountCpUser($cpId['id'])){

                $this->logger->info('Executing cp： CP_ID = '.$cpId['id']);

                $this->updateDuplicateAddressCount($cpId['id']);

            }
        }
    }

    private function updateDuplicateAddressCount($campaignId) {

        try {

            $shippingAddresses = $this->getShippingAddressByCpId($campaignId);
            if($shippingAddresses) {
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
                        $sql = "INSERT INTO cp_users (id, duplicate_address_count, updated_at) VALUES ";
                    }

                    $sql .=  "({$notHaveAddressId}, ".CpUser::NOT_HAVE_ADDRESS." , NOW()),";

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
                            $sql = "INSERT INTO cp_users (id, duplicate_address_count, updated_at) VALUES ";
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
            }
        } catch(Exception $e){
            $this->logger->error('キャンペーンユーザ重複住所更新エラー： CP_ID = '.$campaignId);
            $this->logger->error($e);
        }
    }

    private function getShippingAddressByCpId($cpId){
        $filter = array(
            'cp_id' => $cpId
        );
        $shippingAddresses = $this->db->getShippingAddressUserByCp($filter);
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