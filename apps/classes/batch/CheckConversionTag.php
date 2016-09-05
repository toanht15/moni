<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
AAFW::import('jp.aainc.classes.services.ManagerKpiService');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

/**
 * Class CheckConversionTag
 *
 * 1時間ごとにコンバージョンタグの件数をチェックして、0件だったらHipchatに通知します。
 *
 */
class CheckConversionTag extends BrandcoBatchBase{

    public function executeProcess() {

        try {

            $sql = 'SELECT created_at FROM brands_users_conversions ORDER BY id DESC LIMIT 1';
            $data_builder = aafwDataBuilder::newBuilder();

            $result = $data_builder->getBySQL($sql, array());

            $last_insert_time = strtotime($result[0]['created_at']);
            $one_hour_before = strtotime('-1 hour');

            if($last_insert_time < $one_hour_before ) {
                $this->hipchat_logger->error('■'.date('Y-m-d H:i:s', $one_hour_before).'からコンバージョンが発生していません。確認してください！');
            }

        } catch (Exception $e) {
            $this->logger->error($e);
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }
}