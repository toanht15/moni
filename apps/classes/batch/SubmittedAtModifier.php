<?php

AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
require_once dirname(__FILE__) . '/../../config/define.php';

class SubmittedAtModifier {

    public function doProcess() {

        /** @var aafwDataBuilder $databuilder */
        $databuilder = aafwDataBuilder::newBuilder();

        /** @var aafwEntityStoreBase store */
        $store =  aafwEntityStoreFactory::create('TmpProfileChoiceAnswerHistories');

        aafwLog4phpLogger::getDefaultLogger()->warn('ugoita');

        $sql = "SELECT MAX(brands_users_relation_id) AS id FROM __tmp_profile_choice_answer_histories;";
        $value = $databuilder->getBySQL($sql, []);

        $perPage = 10000;

        $maxId = (int) $value[0]['id'];
        $maxPage = ceil($maxId / $perPage);

        for ($page = 1; $page <= $maxPage; $page++) {

            $fromId = ($page - 1) * $perPage;
            $toId = $page * $perPage;

            $filter =[
                'order' => 'brands_users_relation_id ASC, submitted_at ASC',
                'where' => "brands_users_relation_id > $fromId AND brands_users_relation_id <= $toId"
            ];

            $entities = $store->find($filter);

            // 日付変換
            $lastBurId = 0;
            $lastSubmittedAt = '0000-00-00 00:00:00';

            foreach ($entities as $entity) {

                $burId = $entity->brands_users_relation_id;
                $submittedAt = $entity->submitted_at;

                if ($burId === $lastBurId) {

                    if (strtotime($submittedAt) != strtotime($lastSubmittedAt) &&
                        strtotime($submittedAt) < strtotime($lastSubmittedAt . "+5 second")) {
                        $entity->submitted_at = $lastSubmittedAt;
                        $store->save($entity);
                    } else {
                        $lastSubmittedAt = $submittedAt;
                    }
                } else {
                    $lastSubmittedAt = $submittedAt;
                    $lastBurId = $burId;
                }
            }

            aafwLog4phpLogger::getDefaultLogger()->warn('SubmittedAtModifier brands_users_relation_id = ' . $toId . ' was finished.');
        }

        $store =  aafwEntityStoreFactory::create('TmpProfileFreeAnswerHistories');

        $filter = [
            'order' => 'brands_users_relation_id ASC, submitted_at ASC'
        ];

        $entities = $store->find($filter);

        // 日付変換
        $lastBurId = 0;
        $lastSubmittedAt = '0000-00-00 00:00:00';

        foreach ($entities as $entity) {

            $burId = $entity->brands_users_relation_id;
            $submittedAt = $entity->submitted_at;

            if ($burId === $lastBurId) {

                if (strtotime($submittedAt) != strtotime($lastSubmittedAt) &&
                    strtotime($submittedAt) < strtotime($lastSubmittedAt . "+5 second")) {
                    $entity->submitted_at = $lastSubmittedAt;
                    $store->save($entity);
                } else {
                    $lastSubmittedAt = $submittedAt;
                }
            } else {
                $lastSubmittedAt = $submittedAt;
                $lastBurId = $burId;
            }
        }

        aafwLog4phpLogger::getDefaultLogger()->warn('owata');
    }
}