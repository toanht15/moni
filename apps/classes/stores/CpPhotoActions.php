<?php
AAFW::import ( 'jp.aainc.aafw.base.aafwEntityStoreBase' );
class CpPhotoActions extends aafwEntityStoreBase {

    protected $_TableName = "cp_photo_actions";
    protected $_EntityName = "CpPhotoAction";

    const APPROVE_HIDDEN_FLG_OFF = 0;
    const APPROVE_HIDDEN_FLG_ON = 1;

    public function __toString() {
        return "";
    }
}
