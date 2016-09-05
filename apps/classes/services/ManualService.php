<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class ManualService extends aafwServiceBase {

    private $manuals;

    public function __construct() {
        $this->manuals = $this->getModel('Manuals');
    }

    public function getAllManuals($filter) {
        return $this->manuals->find($filter);
    }
}
