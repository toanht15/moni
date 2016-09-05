<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');
/**
 * ユーザのキャンペーン当選回数を更新
 *
 * <p>
 *  一日一回本バッチを実行し、ユーザのキャンペーン当選回数を更新する
 * </p>
 * <ul>
 *  <li>前日（引数がある場合は指定期間）の当選通知のメッセージから更新対象ユーザを取得し、Tempテーブルに格納</li>
 *  <li>100人ずつ更新対象ユーザの当選回数を更新</li>
 * </ul>
 *
 * Class UpdateCpAnnounceCount
 */
class UpdateCpAnnounceCount extends BrandcoBatchBase{

    function executeProcess() {
        ini_set('memory_limit', '256M');

        $updateCountBatchLogService = $this->service_factory->create('UpdateCountBatchLogService');
        $date = date('Y:m:d');

        if(!$updateCountBatchLogService->getBatchLog(get_class($this),$date,UpdateCountBatchLog::SUCCESS_STATUS)) {

            /** @var BrandsUsersSearchInfoService $brands_users_search_info_service */
            $brands_users_search_info_service = $this->service_factory->create('BrandsUsersSearchInfoService');

            list($from_date, $to_date) = $brands_users_search_info_service->getTargetDate($this->argv);

            if (!$from_date) {
                echo 'from_date error:日付(%Y-%m-%d)形式で入力してください。';
                return;
            }
            if (!$to_date) {
                echo 'to_date error:日付(%Y-%m-%d)形式で入力してください。';
                return;
            }

            $set_temp_result = $brands_users_search_info_service->setTempCpAnnounceActionUsers($from_date, $to_date);
            if (!$set_temp_result) {
                throw new aafwException("setTempCpAnnounceActionUsers FAILED!: " . $set_temp_result . ",from_date=" . $from_date . ", to_date=" . $to_date);
            }

            $max_id = (int)$brands_users_search_info_service->getTempCpAnnounceMaxId();
            if (!isset($max_id)) {
                throw new aafwException("getTempCpAnnounceMaxId FAILED!");
            }
            $this->setExecuteCount($max_id);

            for ($i = 1; $i <= $max_id; $i += 100) {
                try {
                    $maxRange = $i + 99;
                    $insert_result = $brands_users_search_info_service->insertCpAnnounceActionUsers($i, $maxRange);
                    if (!$insert_result) {
                        throw new aafwException("insertCpAnnounceActionUsers FAILED!: " . $insert_result . ", i=" . $i,
                            " maxRange=", $maxRange);
                    }
                } catch (Exception $e) {
                    throw $e;
                }
            }
            $updateCountBatchLogService->saveBatchLog(get_class($this),UpdateCountBatchLog::SUCCESS_STATUS);
        }
    }
}