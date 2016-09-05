<?php

AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
require_once dirname(__FILE__) . '/../../config/define.php';

class MigrateProfileHistories {

    public function doProcess() {

        aafwLog4phpLogger::getDefaultLogger()->info('migratehajimata');

        /** @var aafwDataBuilder $dataBuilder */
        $dataBuilder = aafwDataBuilder::newBuilder();

        /** @var aafwEntityStoreBase $store */
        $store =  aafwEntityStoreFactory::create('TmpProfileChoiceAnswerHistories');

        $logger = aafwLog4phpLogger::getDefaultLogger();

        $sql = "SELECT MAX(id) as id FROM __tmp_profile_choice_answer_histories";
        $value = $dataBuilder->getBySQL($sql);

        $perPage = 1000;

        $maxId = (int) $value[0]['id'];
        $maxPage = ceil($maxId / $perPage);

        for ($page = 1; $page <= $maxPage; $page++) {

            $fromId = ($page - 1) * $perPage;
            $toId = $page * $perPage;

            $sql = "INSERT INTO `profile_choice_answer_histories` (`choice_id`, `questionnaires_questions_relation_id`, `brands_users_relation_id`, `answer_text`, `submitted_at`, `del_flg`)
                SELECT `choice_id`, `questionnaires_questions_relation_id`, `brands_users_relation_id`, `answer_text`, `submitted_at`, `del_flg` FROM __tmp_profile_choice_answer_histories
                WHERE id > $fromId AND id <= $toId ;";

            try {
                $store->begin();

                $result = $dataBuilder->executeUpdate($sql);
                if (!$result) {
                    throw new Exception("Command execution failed! : " . $sql);
                }

                $logger->info('MigrateProfileHistories is in progress. ID : ' . $toId . ' was finished.');

                $store->commit();
            } catch (Exception $ex) {
                // ロールバックに失敗する状況でエラー情報を記録できるとは思えないのでそのまま。
                $store->rollback();
                $logger->error('MigrateProfileHistories failed!! ID : ' . $toId . " ex = " . $ex);
                exit;
            }
        }

        aafwLog4phpLogger::getDefaultLogger()->info('migrateowata');
    }
}