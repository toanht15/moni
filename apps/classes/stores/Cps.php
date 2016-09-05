<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityStoreBase');

class Cps extends aafwEntityStoreBase {

    protected $_TableName = 'cps';
    protected $_EntityName = "Cp";

    const ADEBIS_NEW_USER = "NewUser";
    const ADEBIS_CP_JOIN_FINISH = "CpJoinFinish";

}