<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
class ProfileQuestionnaireService extends aafwServiceBase{

    private $profile_questionnaire;
    private $profile_questionnaire_answer;
    private $logger;

    const QUESTIONNAIRE_TYPE_RADIO = 1;
    const QUESTIONNAIRE_TYPE_CHECKBOX = 2;
    const QUESTIONNAIRE_TYPE_FREE = 3;

    const QUESTIONNAIRE_REQUIRED = 2;
    const QUESTIONNAIRE_NOT_REQUIRED = 1;

    public function __construct() {
        $this->profile_questionnaire = $this->getModel('ProfileQuestionnaires');
        $this->profile_questionnaire_answer = $this->getModel('ProfileQuestionnaireAnswers');
        $this->old_new_question_relations = $this->getModel('OldNewQuestionRelations');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $brand_id
     * @throws Exception
     */
    public function deleteAllProfileQuestionOfBrand($brand_id) {
        try{
            $this->profile_questionnaire->begin();
            $questions = $this->getAllProfileQuestionByBrandId($brand_id);
            foreach ($questions as $question) {
                $this->profile_questionnaire->deletePhysical($question);
            }
            $this->profile_questionnaire->commit();
        } catch (Exception $e) {
            $this->profile_questionnaire->rollback();
            $this->logger->error('BrandPageSettingService #deleteAllProfileQuestionOfBrand Error.' . $e);
            throw $e;
        }
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getAllProfileQuestionByBrandId($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
            ),
        );
        return $this->profile_questionnaire->find($filter);
    }

    /**
     * @param $post
     * @param $brand_id
     */
    public function createAndUpdateProfileQuestionnairesByPost($post, $brand_id) {
        try {
            $this->profile_questionnaire->begin();
            for ($i=1; $i<=$post['question_num']; $i++) {

                if ($post['question_id_'.$i] != -1) {
                    // update questionnaire
                    $question = $this->getQuestionnaireById($post['question_id_'.$i]);
                    if (!$post['item_'.$i]){ // not update when checkbox isn't checked
                        $question->public_flg = 0;
                        $this->profile_questionnaire->save($question);
                        continue;
                    }
                    if (!$question) continue;
                    $question->brand_id = $brand_id;
                    $question->question = $post['question_'.$i];
                    $question->requirement_flg = $post['requirement_'.$i];
                    $question->type = $post['questionnaire_type_'.$i];
                    $question->public_flg = 1;
                    $question->choices = str_replace("\r\n", "<br />", $post['answer_'.$i]);
                    if (!$question->choices) {
                        $question->choices = '';
                    }
                    $this->profile_questionnaire->save($question);

                } else {
                    // create new questionnaire
                    $question = $this->profile_questionnaire->createEmptyObject();
                    $question->brand_id = $brand_id;
                    $question->question = $post['question_'.$i];
                    $question->requirement_flg = $post['requirement_'.$i];
                    $question->type = $post['questionnaire_type_'.$i];
                    $question->choices = str_replace("\r\n", "<br />", $post['answer_'.$i]);
                    if (!$question->choices) {
                        $question->choices = '';
                    }
                    $question->public_flg = ($post['item_'.$i])?1:0;
                    $this->profile_questionnaire->save($question);
                }
            }

            $this->profile_questionnaire->commit();
        } catch (Exception $e) {
            $this->profile_questionnaire->rollback();
            $this->logger->error('ProfileQuestionnaireService @createAndUpdateProfileQuestionnairesByPost error '.$e->getMessage());
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getQuestionnaireById($id) {
        return $this->profile_questionnaire->findOne($id);
    }

    /**
     * @param $relation_id
     * @param $question_id
     * @param $answer
     */
    public function createProfileQuestionAnswer($relation_id, $question_id, $answer) {
        $profile_questionnaire_answer = $this->profile_questionnaire_answer->createEmptyObject();
        $profile_questionnaire_answer->relation_id = $relation_id;
        $profile_questionnaire_answer->question_id = $question_id;
        $profile_questionnaire_answer->answer = $answer;
        return $this->profile_questionnaire_answer->save($profile_questionnaire_answer);
    }

    /**
     * @param $relation_id
     * @param $question_id
     * @return $profile_questionnaire_answer
     */
    public function getProfileQuestionAnswer($relation_id, $question_id) {
        $filter = array(
            'conditions' => array(
                'relation_id' => $relation_id,
                'question_id' => $question_id,
            ),
        );

        return $this->profile_questionnaire_answer->findOne($filter);
    }

    /**
     * @param $brand_id
     * @return mixed
     */
    public function getAllProfileQuestionByBrandIdOrderById($brand_id) {
        $filter = array(
            'conditions' => array(
                'brand_id' => $brand_id,
            ),
            'order' => array(
                'name' => 'id',
                'direction' => 'asc'
            )
        );
        return $this->profile_questionnaire->find($filter);
    }

    /**
     * @param $relate_id
     * @return mixed
     */
    public function getProfileQuestionnaireAnswersByRelateId($relation_id) {
        $filter = array(
            'conditions' => array(
                'relation_id' => $relation_id,
            ),
            'order' => array(
                'name' => 'question_id',
                'direction' => 'asc'
            )
        );
        return $this->profile_questionnaire_answer->find($filter);
    }

    /**
     * @param $old_question_id
     * @param $new_question_id
     */
    public function createOldNewQuestionRelations($old_question_id, $new_question_id) {
        if(!$this->getNewProfileQuestionByOldQuestion($old_question_id)) {
            $old_new_question_relation = $this->old_new_question_relations->createEmptyObject();
            $old_new_question_relation->old_question_id = $old_question_id;
            $old_new_question_relation->new_question_id = $new_question_id;
            return $this->old_new_question_relations->save($old_new_question_relation);
        }
    }

    /**
     * @param $relate_id
     * @return mixed
     */
    public function getNewProfileQuestionByOldQuestion($old_question_id) {
        $filter = array(
            'conditions' => array(
                'old_question_id' => $old_question_id,
            ),
        );
        return $this->old_new_question_relations->findOne($filter);
    }

    /**
     * @param $new_question_id
     * @return mixed
     */
    public function getOldNewProfileQuestionByNewQuestionId($new_question_id) {
        $filter = array(
            'conditions' => array(
                'new_question_id' => $new_question_id,
            ),
        );
        return $this->old_new_question_relations->findOne($filter);
    }

    public function deleteProfileQuestionnaireAnswerByBrandRelationId($relation_id) {
        $answers = $this->getProfileQuestionnaireAnswerByBrandRelationId($relation_id);
        foreach ($answers as $answer) {
            $this->profile_questionnaire_answer->deletePhysical($answer);
        }
    }
}