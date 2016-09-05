<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.CpInfoContainer');

class api_pre_execute_questionnaire_action extends ExecuteActionBase {

    public $NeedOption = array();
    public $NeedUserLogin = false;

    protected $ContainerName = 'api_execute_entry_action';
    protected $Form = array(
        'package' => 'messages',
        'action' => 'thread/{cp_action_id}',
    );

    private $user_answers;
    /** @var CpQuestionnaireService $cp_questionnaire_service */
    protected $cp_questionnaire_service;

    public function doThisFirst() {
        $service_factory = new aafwServiceFactory();
        $this->cp_questionnaire_service = $service_factory->create('CpQuestionnaireService');

        foreach($this->POST as $key=>$value) {
            if(preg_match('/^single_answer|^multi_answer|^free_answer/', $key)) {
                $this->user_answers[$key] = $value;
            }
        }
    }

    public function validate() {
        $errors = array();

        if (isset($this->POST['mail_type'])) {
            $this->setSession('cp_id', null);
            $validatorDefinition = array();

            if ($this->POST['mail_type'] == 'login') {
                $validatorDefinition = array(
                    'mail_address' => array('required' => 1, 'type' => 'str', 'length' => 255, 'validator' => array('MailAddress')),
                    'password' => array('required' => 1, 'type' => 'str')
                );
            } elseif ($this->POST['mail_type'] == 'signup') {
                $validatorDefinition = array(
                    'mail_address' => array('required' => 1, 'type' => 'str', 'length' => 255, 'validator' => array('MailAddress')),
                    'password' => array('required' => 1, 'type' => 'str', 'length' => array('min' => 8), 'validator' => array('Alnum'))
                );
            }

            $validator = new aafwValidator($validatorDefinition);
            $validator->validate($this->POST);

            if ($validator->isValid()) {
                $brandco_auth_service = $this->getService('BrandcoAuthService', array($this->getMoniplaCore()));

                if ($this->POST['mail_type'] == 'login') {
                    $result = $brandco_auth_service->checkAccount($this->mail_address, $this->password);

                    if ($result->result->status != Thrift_APIStatus::SUCCESS) {
                        $validator->setError('password', 'INVALID_PASSWORD');
                    }
                } elseif ($this->POST['mail_type'] == 'signup') {
                    $result = $brandco_auth_service->getUsersByMailAddress($this->mail_address);

                    if ($result->user) {
                        $validator->setError('duplicated_mail', 'DUPLICATED_MAIL');
                    }
                }
            }

            if (!$validator->isValid()) {
                foreach ($validator->getError() as $key => $value) {
                    $errors[$key] = $validator->getMessage($key);
                }
            }
        }

        $cp_questionnaire_action = $this->cp_questionnaire_service->getCpQuestionnaireAction($this->cp_action_id);
        $questionnaires_questions_relations = $this->cp_questionnaire_service->getRelationsByQuestionnaireActionId($cp_questionnaire_action->id);

        // 必須設問で回答があるかチェック
        $required_question_ids = array();
        $choice_question_ids = array();
        foreach($questionnaires_questions_relations as $relation) {
            if ($relation->requirement_flg) {
                $required_question_ids[] = $relation->question_id;
            }
        }
        if (!empty($required_question_ids)) {
            // 必須free_answer質問で回答があるかチェック
            $required_questions = $this->cp_questionnaire_service->getQuestionByIds($required_question_ids);

            foreach ($required_questions as $required_question) {
                if(QuestionTypeService::isChoiceQuestion($required_question->type_id)) {
                    $choice_question_ids[] = $required_question->id;
                } else {
                    if($this->user_answers['free_answer/' . $required_question->id] === '') {
                        $errors['question/' . $required_question->id] = '必ず回答してください。';
                        continue;
                    }
                }
            }
        }
        if (!empty($choice_question_ids)) {
            // 必須選択質問で回答があるかチェック
            $choice_requirements = $this->cp_questionnaire_service->getRequirementByQuestionIds($choice_question_ids);

            foreach ($choice_requirements as $choice_requirement) {
                if ($choice_requirement->multi_answer_flg == CpQuestionnaireService::SINGLE_ANSWER) {
                    if (!$this->user_answers['single_answer/' . $choice_requirement->question_id]) {
                        $errors['question/' . $choice_requirement->question_id] = '必ず回答してください。';
                        continue;
                    }
                } else {
                    if (!preg_grep('/^multi_answer\/' . $choice_requirement->question_id . '\//', array_keys($this->user_answers))) {
                        $errors['question/' . $choice_requirement->question_id] = '必ず回答してください。';
                        continue;
                    }
                }
            }
        }

        $other_choice_question_ids = array();
        $other_choice_question_values = array();
        foreach($this->user_answers as $key=>$value) {
            $question_data = explode('/', $key);
            $question_type = $question_data[0];
            $question_id = $question_data[1];

            if($question_type == 'single_answer_othertext' || $question_type == 'multi_answer_othertext') {
                //文字数のカウント
                if(mb_strlen($value, 'UTF-8') > 255) {
                    $errors['question/' . $question_id] = '回答は255字以内で入力してください。';
                    continue;
                }
            }
            if($question_type == 'free_answer') {
                //文字数のカウント
                if(mb_strlen($value, 'UTF-8') > 2048) {
                    $errors['question/' . $question_id] = '回答は2048字以内で入力してください。';
                    continue;
                }
            }
            if($question_type == 'single_answer_othertext' || $question_type == 'multi_answer_othertext' || $question_type == 'free_answer') {
                //文字列チェック
                if(!is_string($value)) {
                    $errors['question/' . $question_id] = '回答は文字列で入力してください。';
                    continue;
                }
            }
            // その他を選択しているが、内容の記載がない
            if ($question_type == 'single_answer_othertext') {
                $other_choice_question_ids[$question_id] = 'single_answer';
                $other_choice_question_values[$question_id] = $value;
            } elseif ($question_type == 'multi_answer_othertext') {
                $other_choice_question_ids[$question_id] = 'multi_answer';
                $other_choice_question_values[$question_id] = $value;
            }
        }
        if (!empty($other_choice_question_ids)) {
            $other_choice_questions = $this->cp_questionnaire_service->getOtherChoiceByQuestionIds(array_keys($other_choice_question_ids));

            foreach ($other_choice_questions as $other_choice_question) {
                if ($other_choice_question_ids[$other_choice_question->question_id] == 'single_answer') {
                    if($this->user_answers['single_answer/' . $other_choice_question->question_id] == $other_choice_question->id
                        && $other_choice_question_values[$other_choice_question->question_id] === '') {
                        $errors['question/' . $other_choice_question->question_id] = 'その他を選択した場合、内容を入力してください。';
                        continue;
                    }
                } elseif ($other_choice_question_ids[$other_choice_question->question_id] == 'multi_answer') {
                    if($this->user_answers['multi_answer/' . $other_choice_question->question_id . '/' . $other_choice_question->id]
                        && $other_choice_question_values[$other_choice_question->question_id] === '') {
                        $errors['question/' . $other_choice_question->question_id] = 'その他を選択した場合、内容を入力してください。';
                        continue;
                    }
                }
            }
        }

        if(!empty($errors)) {
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    public function doAction() {
        $service_factory = new aafwServiceFactory();
        $cp_flow_service = $service_factory->create('CpFlowService');

        $cp_action = CpInfoContainer::getInstance()->getCpActionById($this->cp_action_id);
        $cp_action_group = $cp_action->getCpActionGroup();

        $qa[$cp_action_group->cp_id] = $this->user_answers;
        $this->setBrandSession('qa', $qa);

        if (isset($this->POST['mail_type'])) {
            $this->setSession('cp_id', $cp_action_group->cp_id);
        }

        $json_data = $this->createAjaxResponse('ok');
        $this->assign('json_data', $json_data);
        return 'dummy.php';
    }

    function saveData() {}
}