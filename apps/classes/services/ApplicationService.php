<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class ApplicationService extends aafwServiceBase {
    const PLATFORM = 0;
    const BRANDCO = 1;
    const MONIPLA = 2;
    const DOMAIN_MAPPING_DM_TEST = 5;
    const DOMAIN_MAPPING_KOSE = 6;
    const DOMAIN_MAPPING_ISEHAN = 7;
    const DOMAIN_MAPPING_GDO = 9;
    const DOMAIN_MAPPING_OLYMPUS = 10;
    const DOMAIN_MAPPING_SUGAO = 11;
    const DOMAIN_MAPPING_WHITEBELG = 12;
    const DOMAIN_MAPPING_KENKEN = 13;
    const DOMAIN_MAPPING_UQ     = 14;
    const DOMAIN_MAPPING_KANEKISUISAN = 15;
    const DOMAIN_MAPPING_JRWEST = 16;

    const CLIENT_ID_PLATFORM = 'platform';
    const CLIENT_ID_BRANDCO = 'brandco';
    const CLIENT_ID_COMCAMPAIGN = 'com_campaign';//廃止予定

    public static $ApplicationMaster = array(
        self::PLATFORM => array('name' => 'モニプラ', 'client_id' => self::CLIENT_ID_PLATFORM),
        self::BRANDCO => array('name' => 'BRANDCo', 'client_id' => self::CLIENT_ID_BRANDCO),
        self::MONIPLA => array('name' => 'MONIPLA', 'client_id' => self::CLIENT_ID_COMCAMPAIGN),
        self::DOMAIN_MAPPING_DM_TEST => array('name' => 'dm_test', 'client_id' => 'dm_test'),
        self::DOMAIN_MAPPING_KOSE => array('name' => 'dm_kose', 'client_id' => 'dm_kose'),
        self::DOMAIN_MAPPING_ISEHAN => array('name' => 'dm_isehan', 'client_id' => 'dm_isehan'),
        self::DOMAIN_MAPPING_GDO => array('name' => 'dm_gdo', 'client_id' => 'dm_gdo'),
        self::DOMAIN_MAPPING_OLYMPUS => array('name' => 'dm_olympus', 'client_id' => 'dm_olympus'),
        self::DOMAIN_MAPPING_SUGAO => array('name' => 'dm_sugaotaiken', 'client_id' => 'dm_sugaotaiken'),
        self::DOMAIN_MAPPING_WHITEBELG => array('name' => 'dm_whitebelg', 'client_id' => 'dm_whitebelg'),
        self::DOMAIN_MAPPING_KENKEN => array('name' => 'dm_kenken', 'client_id' => 'dm_kenken'),
        self::DOMAIN_MAPPING_UQ => array('name' => 'dm_uq', 'client_id' => 'dm_uq'),
        self::DOMAIN_MAPPING_KANEKISUISAN => array('name' => 'dm_kanekisuisan', 'client_id' => 'dm_kanekisuisan'),
        self::DOMAIN_MAPPING_JRWEST => array('name' => 'dm_jrwest', 'client_id' => 'dm_jrwest')
    );

    public static function getClientId($brand) {
        if (is_null($brand)) {
            return self::$ApplicationMaster[self::BRANDCO]['client_id'];
        }

        return self::$ApplicationMaster[$brand->app_id]['client_id'];
    }
}
