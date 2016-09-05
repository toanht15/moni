<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');
AAFW::import('jp.aainc.classes.services.CpQuestionnaireService');
AAFW::import('jp.aainc.classes.services.QuestionnaireUserAnswerService');

use Michelf\Markdown;
/**
 * Class CpQuestionnaireActionManager
 * TODO トランザクション
 */
class CpQuestionnaireActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    protected $cp_questionnaire_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_questionnaire_actions = $this->getModel("CpQuestionnaireActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @return array|mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $cp_qustionnaire_action = null;
        } else {
            $cp_qustionnaire_action = $this->getCpQuestionnaireActionByCpAction($cp_action);
        }
        return array($cp_action, $cp_qustionnaire_action);
    }

    /**
     * @param $cp_action_group_id
     * @param $type
     * @param $status
     * @param $order_no
     * @return mixed
     */
    public function createCpActions($cp_action_group_id, $type, $status, $order_no) {
        $cp_action = $this->createCpAction($cp_action_group_id, $type, $status, $order_no);
        $cp_qustionnaire_action = $this->createConcreteAction($cp_action);
        return array($cp_action, $cp_qustionnaire_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteCpActions(CpAction $cp_action) {
        $this->deleteConcreteAction($cp_action);
        $this->deleteCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateCpActions(CpAction $cp_action, $data) {
        $this->updateCpAction($cp_action);
        $this->updateConcreteAction($cp_action, $data);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function getConcreteAction(CpAction $cp_action) {
        return $this->getCpQuestionnaireActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $cp_qustionnaire_action = $this->cp_questionnaire_actions->createEmptyObject();
        $cp_qustionnaire_action->cp_action_id = $cp_action->id;
        $cp_qustionnaire_action->title = "アンケート";
        $cp_qustionnaire_action->text = "";
        $cp_qustionnaire_action->button_label_text = "回答する";
        $this->cp_questionnaire_actions->save($cp_qustionnaire_action);
        return $cp_qustionnaire_action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_qustionnaire_action = $this->getCpQuestionnaireActionByCpAction($cp_action);
        $cp_qustionnaire_action->image_url = $data["image_url"];
        $cp_qustionnaire_action->text = $data["text"];
        $cp_qustionnaire_action->title = $data['title'];
        $cp_qustionnaire_action->html_content = Util::isNullOrEmpty($data['text']) ? null : Markdown::defaultTransform($data['text']);
        $cp_qustionnaire_action->button_label_text = $data["button_label_text"];
        $this->cp_questionnaire_actions->save($cp_qustionnaire_action);
    }

    /**
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $cp_qustionnaire_action = $this->getCpQuestionnaireActionByCpAction($cp_action);
        $this->cp_questionnaire_actions->delete($cp_qustionnaire_action);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    private function getCpQuestionnaireActionByCpAction(CpAction $cp_action) {
        return $this->cp_questionnaire_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $cp_old_concrete_action = $this->getConcreteAction($old_cp_action);
        // copy cp_questionnaire_actions
        $cp_questionnaire_action = $this->cp_questionnaire_actions->createEmptyObject();
        $cp_questionnaire_action->cp_action_id = $new_cp_action_id;
        $cp_questionnaire_action->image_url = $cp_old_concrete_action->image_url;
        $cp_questionnaire_action->text = $cp_old_concrete_action->text;
        $cp_questionnaire_action->html_content = $cp_old_concrete_action->html_content;
        $cp_questionnaire_action->title = $cp_old_concrete_action->title;
        $cp_questionnaire_action->button_label_text = $cp_old_concrete_action->button_label_text;
        $this->cp_questionnaire_actions->save($cp_questionnaire_action);

        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = new CpQuestionnaireService();

        //copy questionnaire_questions
        $questionnaire_questions = $cp_questionnaire_service->getQuestionsByQuestionnaireActionId($cp_old_concrete_action->id);

        if (!$questionnaire_questions) return;

        foreach ($questionnaire_questions as $questionnaire_question) {
            //copy questionnaires_questions
            $new_question = $cp_questionnaire_service->setQuestion($questionnaire_question->type_id, $questionnaire_question->question, -1);

            $questionnaires_questions_relation = $cp_questionnaire_service->getRelationByQuestionnaireActionIdAndQuestionId($cp_old_concrete_action->id, $questionnaire_question->id);

            //copy questionnaires_questions_relations
            $cp_questionnaire_service->setQuestionnairesQuestionsRelation($cp_questionnaire_action->id, $new_question->id, $questionnaires_questions_relation->requirement_flg, $questionnaires_questions_relation->number);

            if (QuestionTypeService::isChoiceQuestion($new_question->type_id)) {
                //copy question_choice_requirements
                $question_choice_requirement = $cp_questionnaire_service->getRequirementByQuestionId($questionnaire_question->id);

                if (!$question_choice_requirement) return;
                $cp_questionnaire_service->setRequirement($new_question->id, $question_choice_requirement->use_other_choice_flg, $question_choice_requirement->random_order_flg, $question_choice_requirement->multi_answer_flg);

                //copy all choice
                $question_choices = $cp_questionnaire_service->getChoicesByQuestionId($questionnaire_question->id);
                foreach ($question_choices as $question_choice) {
                    $cp_questionnaire_service->setChoices($new_question->id, $question_choice->choice, -1, $question_choice->choice_num, $question_choice->image_url, $question_choice->other_choice_flg);
                }
            }
        }
    }

    /**
     * @param CpAction $cp_action
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpQuestionnaireActionManager#deletePhysicalRelatedCpActionData cp_action_id=" . $cp_action->id);
        }
        if ($with_concrete_actions) {
            //TODO delete concrete action
        }

        //delete user's answers
        /** @var CpQuestionnaireService $questionnaire_service */
        $questionnaire_service = $this->getService("CpQuestionnaireService");
        /** @var QuestionnaireUserAnswerService $questionnaire_user_answer_service */
        $questionnaire_user_answer_service = $this->getService('QuestionnaireUserAnswerService');
        $question_action = $this->getConcreteAction($cp_action);
        if ($question_action) {
            $questionnaire_service->deletePhysicalUserAnswerByQuestionnaireActionId($question_action->id);
            $questionnaire_user_answer_service->deletePhysicalUserAnswersByCpActionId($cp_action->id);
        }
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpQuestionnaireActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        /** @var CpQuestionnaireService $questionnaire_service */
        $questionnaire_service = $this->getService("CpQuestionnaireService");
        /** @var QuestionnaireUserAnswerService $questionnaire_user_answer_service */
        $questionnaire_user_answer_service = $this->getService('QuestionnaireUserAnswerService');

        $question_action = $this->getConcreteAction($cp_action);
        if (!$question_action) {
            throw new Exception("CpQuestionnaireActionManager#deletePhysicalRelatedCpActionDataByCpUser question_action null cp_action_id=" . $cp_action->id);
        }

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService("CpFlowService");
        $cp = $cp_flow_service->getCpByCpAction($cp_action);
        if (!$cp) {
            throw new Exception("CpQuestionnaireActionManager#deletePhysicalRelatedCpActionDataByCpUser cp null cp_action_id=" . $cp_action->id);
        }
        $brand_id = $cp->getBrand()->id;
        if (!$brand_id) {
            throw new Exception("CpQuestionnaireActionManager#deletePhysicalRelatedCpActionDataByCpUser brand_id null cp_action_id=" . $cp_action->id);
        }

        /** @var BrandsUsersRelationService $brand_relation_service */
        $brand_relation_service = $this->getService("BrandsUsersRelationService");
        $brand_user_relation = $brand_relation_service->getBrandsUsersRelationsByBrandIdAndUserId($brand_id, $cp_user->user_id);

        if (!$brand_user_relation) {
            throw new Exception("CpQuestionnaireActionManager#deletePhysicalRelatedCpActionDataByCpUser brand_user_relation null cp_action_id=" . $cp_action->id);
        }

        $questionnaire_service->deletePhysicalUserAnswerByQuestionnaireActionIdAndBrandUserRelation($question_action->id, $brand_user_relation->id);
        $questionnaire_user_answer_service->deletePhysicalUserAnswerByBurIdAndCpActionId($brand_user_relation->id, $cp_action->id);
    }

    public function getActionData($action_id) {
        /** @var CpQuestionnaireService $cp_questionnaire_service */
        $cp_questionnaire_service = $this->getService('CpQuestionnaireService');
        // アンケートの設問を並び順通りに取得
        $cp_concrete_action = $cp_questionnaire_service->getCpQuestionnaireAction($action_id);
        $questions_relations = $cp_questionnaire_service->getRelationsByQuestionnaireActionId($cp_concrete_action->id);
        $questions = array();
        foreach ($questions_relations as $relation) {
            $questions[$relation->id] = $cp_questionnaire_service->getQuestionById($relation->question_id);
        }

        return array(
            'questions_relations'=>$questions_relations,
            'questions'=>$questions
        );
    }
}
