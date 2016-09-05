<?php
AAFW::import('jp.aainc.classes.manager_kpi.IManagerKPI');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class ProfileQuestionKPI implements IManagerKPI {

    function doExecute() {
        list($date) = func_get_args();

        $filter = array(
            'conditions' => array(
                'created_at:<=' => date("Y-m-d 23:59:59", strtotime($date)),
                'public' => 1
            ),
        );
        $questionnaires_questions_relation = aafwEntityStoreFactory::create('ProfileQuestionnairesQuestionsRelations');

        return $questionnaires_questions_relation->count($filter);
    }
}