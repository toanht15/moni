<?php
/**
 * Created by PhpStorm.
 * User: katoriyusuke
 * Date: 15/07/22
 * Time: 18:58
 */

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpInfoService extends aafwServiceBase {

    private $cpInfos;

    public function __construct() {
        $this->cpInfos = $this->getModel("CpInfos");
    }

    public function getCpInfoByCpId($cpId) {
        $filter = array(
            'conditions' => array('cp_id' => $cpId)
        );

        if ($this->cpInfos->findOne($filter)) {
            return $this->cpInfos->findOne($filter);
        } else {
            return $this->getEmptyCpInfoObject();
        }
    }

    public function getEmptyCpInfoObject() {
        return $this->cpInfos->createEmptyObject();
    }

    public function saveCpInfo(CpInfo $cpInfo) {
        $this->cpInfos->save($cpInfo);
    }

}