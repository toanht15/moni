<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class UpdateCpDateToPresent {

    public function executeProcess($arg) {
        if (!$arg[1]) {
            return;
        }
        $builder = aafwDataBuilder::newBuilder();
        $builder->executeUpdate("UPDATE cps SET public_date = NOW() - INTERVAL 2 MINUTE, start_date = NOW() - INTERVAL 2 MINUTE WHERE id = " . $arg[1]);
    }
}
