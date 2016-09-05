<?php
require_once dirname(__FILE__) . '/../../config/define.php';
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoBatchBase');

class CopyTrackerDBToBC extends BrandcoBatchBase{

    public function executeProcess()
    {
        /** @var ConversionService $conversion_service */
        $conversion_service = $this->service_factory->create('ConversionService');
        /** @var UserService $user_service */
        $user_service = $this->service_factory->create('UserService');
        $id = null;
        try {
            $conversion_logs = $conversion_service->getConversionLogToCopyToBC();
            $conversion_service->brands_users_conversion->begin();

            foreach ($conversion_logs as $conversion_log) {
                $id = $conversion_log->id;

                if (!$conversion_log->brand_id) {
                    // 更新失敗状態としてスキップ
                    $conversion_service->saveConversionNotBcUser($conversion_log);
                    continue;
                }

                if (!$conversion_log->aa_user_id) {
                    //saved_bcフラッグを2に更新する
                    $conversion_service->saveConversionNotBcUser($conversion_log);
                    continue;
                }

                $user = $user_service->getUserByMoniplaUserId($conversion_log->aa_user_id);

                if(Util::isNullOrEmpty($user)) {
                    //ユーザがない場合、saved_bcフラッグを2に更新する
                    $conversion_service->saveConversionNotBcUser($conversion_log);
                    continue;
                }

                $new_brandco_user_conversion = $conversion_service->createEmptyBrandcoUserConversion();

                foreach ($conversion_log->toArray() as $key => $value) {
                    if ($key == 'id') {
                        continue;
                    }
                    $new_brandco_user_conversion->$key = $value;
                }

                $new_brandco_user_conversion->user_id = $user->id;
                $new_brandco_user_conversion->date_conversion = $conversion_log->date_created;
                $conversion_service->updateBrandcoUserConversion($new_brandco_user_conversion);

                //conversion_logsのsaved_bcフラッグ変更
                $conversion_service->savedConversionLog($conversion_log);
            }
            $conversion_service->brands_users_conversion->commit();

        } catch (Exception $e) {

            $this->hipchat_logger->error('CopyTrackerDBToBC conversion_log_id='.$id);
            $this->hipchat_logger->error($e);

            try {
                $conversion_service->brands_users_conversion->rollback();
            } catch (Exception $e) {
                $this->hipchat_logger->error($e);
                $this->hipchat_logger->error('brands_users_conversion->rollback失敗しました、メッセージ：'.$e->getMessage());
            }

            throw $e;
        }
    }
}