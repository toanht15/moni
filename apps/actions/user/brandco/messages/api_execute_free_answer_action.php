<?php

AAFW::import('jp.aainc.classes.brandco.toolkit.ExecuteActionBase');
AAFW::import('jp.aainc.classes.validator.user.UserEntryActionValidator');
AAFW::import('jp.aainc.classes.brandco.cp.CpFreeAnswerActionManager');

class api_execute_free_answer_action extends ExecuteActionBase {

    public $NeedOption = array();
    protected $ContainerName = 'api_execute_free_answer_action';

    public function validate() {

        $validatorDefinition = array(
            'free_answer' => array(
                'required' => true,
                'type' => 'str',
                'length' => 2048
            )
        );

        $validator = new UserEntryActionValidator($this->cp_user_id, $this->cp_action_id);
        $validator->validate();

        if (!$validator->isValid()) {
            $errors = $validator->getErrors();
            $json_data = $this->createAjaxResponse("ng", array(), $errors);
            $this->assign('json_data', $json_data);

            return false;
        }
        $validator = new aafwValidator($validatorDefinition);
        $validator->validate($this->POST);
        if($validator->getErrorCount()) {
            $errorMessages['free_answer'] = $validator->getMessage('free_answer');
            $json_data = $this->createAjaxResponse("ng", array(), $errorMessages);
            $this->assign('json_data', $json_data);
            return false;
        }

        return true;
    }

    function saveData() {
        //回答を保存する
        $free_answer_manager = new CpFreeAnswerActionManager();
        try {
            $free_answer_manager->saveUserAnswerWithPost($this->POST);
        } catch (Exception $e) {
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error('api_execute_free_answer_action: saveData '.$e->getMessage());
        }
    }
}
