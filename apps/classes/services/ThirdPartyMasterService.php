<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.ThirdPartyMaster');

class ThirdPartyMasterService extends aafwServiceBase {
    protected $third_party_masters;

    public function __construct() {
        $this->third_party_masters   = $this->getModel('ThirdPartyMasters');
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getThirdPartyMasterByKey($key) {
        $filter = array(
            'tpk' => $key,
        );
        return $this->third_party_masters->findOne($filter);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function createThirdPartyMaster($key) {
        $third_party_master                = $this->createEmptyThirdPartyMasterData();
        $third_party_master->tpk           = $key;
        return $this->saveThirdPartyMasterData($third_party_master);
    }

    /**
     * @return mixed
     */
    public function createEmptyThirdPartyMasterData() {
        return $this->third_party_masters->createEmptyObject();
    }

    /**
     * @param $third_party_master_data
     * @return mixed
     */
    public function saveThirdPartyMasterData($third_party_master_data) {
        return $this->third_party_masters->save($third_party_master_data);
    }

    /**
     * @param $third_party_master_key
     */
    public function deleteThirdPartyMaster($third_party_master_key) {
        $third_party_master = $this->getThirdPartyMasterByKey($third_party_master_key);
        if ($third_party_master) {
            $this->third_party_masters->delete($third_party_master);
        }
    }

}