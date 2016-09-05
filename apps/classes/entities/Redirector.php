<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityBase' );
class Redirector extends aafwEntityBase {

    public function countLogs() {
        return $this->getModel("RedirectorLogs")->count(array('redirector_id' => $this->id));
    }
}
