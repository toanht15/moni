<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.services.ThirdPartyMasterService');
AAFW::import('jp.aainc.classes.entities.ThirdPartyUserRelation');

class ThirdPartyUserRelationService extends aafwServiceBase {
    protected $third_party_user_relations;

    public function __construct() {
        $this->third_party_user_relations   = $this->getModel('ThirdPartyUserRelations');
    }

    /**
     * @param $user_id
     * @param $third_party_master_id
     * @return mixed
     */
    public function getThirdPartyUserRelation($user_id, $third_party_master_id) {
        $filter = array(
            'user_id'               => $user_id,
            'third_party_master_id' => $third_party_master_id
        );
        return $this->third_party_user_relations->findOne($filter);
    }

    /**
     * @param $key
     * @return null
     */
    public function getThirdPartyMasterIdByKey($key) {
        $third_party_master_service = new ThirdPartyMasterService();
        $third_party_master = $third_party_master_service->getThirdPartyMasterByKey($key);
        if (!$third_party_master) {
            $third_party_master = $third_party_master_service->createThirdPartyMaster($key);
        }
        return $third_party_master->id ? : null;
    }

    /**
     * @param $user_id
     * @param $third_party_master_id
     * @param $value
     * @return mixed
     */
    public function createThirdPartyUserRelation($user_id, $third_party_master_id, $value) {
        $third_party_user_relation                         = $this->createEmptyThirdPartyUserRelationData();
        $third_party_user_relation->user_id                = $user_id;
        $third_party_user_relation->third_party_master_id  = $third_party_master_id;
        $third_party_user_relation->value                  = $value;
        return $this->saveThirdPartyUserRelationData($third_party_user_relation);
    }

    /**
     * @param $user_id
     * @param $key_value
     * @return mixed|null
     */
    public function updateThirdPartyUserRelation($user_id, $key_value) {
        $third_party_master_id              = $this->getThirdPartyMasterIdByKey($key_value['key']);
        $third_party_user_relation          = $this->getThirdPartyUserRelation($user_id, $third_party_master_id);
        if (!$third_party_user_relation) return $this->createThirdPartyUserRelation($user_id, $third_party_master_id, $key_value['value']);

        $third_party_user_relation->value   = $key_value['value'];
        return $this->saveThirdPartyUserRelationData($third_party_user_relation);

    }

    /**
     * @return mixed
     */
    public function createEmptyThirdPartyUserRelationData() {
        return $this->third_party_user_relations->createEmptyObject();
    }

    /**
     * @param $third_party_user_relation_data
     * @return mixed
     */
    public function saveThirdPartyUserRelationData($third_party_user_relation_data) {
        return $this->third_party_user_relations->save($third_party_user_relation_data);
    }


    /**
     * @param $user_id
     * @param $key
     */
    public function deleteThirdPartyUserRelation($user_id, $key) {
        $third_party_master_id     = $this->getThirdPartyMasterIdByKey($key);
        $third_party_user_relation = $this->getThirdPartyUserRelation($user_id, $third_party_master_id);
        if ($third_party_user_relation) {
            $this->third_party_user_relations->delete($third_party_user_relation);
        }
    }

}