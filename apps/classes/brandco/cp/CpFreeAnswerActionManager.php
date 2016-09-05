<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

/**
 * Class CpFreeAnswerActionManager
 * TODO トランザクション
 */
class CpFreeAnswerActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    protected $cp_free_answer_actions;
    protected $cp_free_answer_action_answers;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_free_answer_actions = $this->getModel("CpFreeAnswerActions");
        $this->cp_free_answer_action_answers = $this->getModel("CpFreeAnswerActionAnswers");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param $cp_action_id
     * @return array|mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $free_answer_action = null;
        } else {
            $free_answer_action = $this->getCpFreeAnswerActionByCpAction($cp_action);
        }
        return array($cp_action, $free_answer_action);

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
        $free_answer_action = $this->createConcreteAction($cp_action);
        return array($cp_action, $free_answer_action);
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
        return $this->getCpFreeAnswerActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $free_answer_action = $this->cp_free_answer_actions->createEmptyObject();
        $free_answer_action->cp_action_id = $cp_action->id;
        $free_answer_action->title = "自由回答";
        $free_answer_action->question = "";
        $this->cp_free_answer_actions->save($free_answer_action);
        return $free_answer_action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $free_answer_action = $this->getCpFreeAnswerActionByCpAction($cp_action);
        $free_answer_action->image_url = $data["image_url"];
        $free_answer_action->question = $data["question"];
        $free_answer_action->html_content = Markdown::defaultTransform($data['question']);
        $free_answer_action->button_label = $data["button_label"];
        $free_answer_action->title = $data['title'];
        $free_answer_action->del_flg = 0;
        $this->cp_free_answer_actions->save($free_answer_action);
    }

    /**
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $free_answer_action = $this->getCpFreeAnswerActionByCpAction($cp_action);
        $free_answer_action->del_flg = 1;
        $this->cp_free_answer_actions->save($free_answer_action);
    }

    /**
     * @param $cp_action
     * @return mixed
     */
    private function getCpFreeAnswerActionByCpAction(CpAction $cp_action) {
        return $this->cp_free_answer_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * @param $post
     */
    public function saveUserAnswerWithPost($post) {
        $answer = $this->cp_free_answer_action_answers->createEmptyObject();
        $answer->cp_user_id = $post['cp_user_id'];
        $answer->cp_action_id = $post['cp_action_id'];
        $answer->free_answer = $post['free_answer'];
        return $this->cp_free_answer_action_answers->save($answer);
    }

    /**
     * @param $cp_user_id
     * @param $cp_action_id
     * @return mixed
     */
    public function getAnswerByUserAndQuestion($cp_user_id, $cp_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_id,
                'cp_action_id' => $cp_action_id
            )
        );
        return $this->cp_free_answer_action_answers->findOne($filter);
    }

    /**
     * @param $cp_user_ids
     * @param $cp_action_id
     * @return mixed
     */
    public function getAnswersByUserAndQuestion($cp_user_ids, $cp_action_id) {
        $filter = array(
            'conditions' => array(
                'cp_user_id' => $cp_user_ids,
                'cp_action_id' => $cp_action_id
            )
        );
        $answers = $this->cp_free_answer_action_answers->find($filter);

        $result = array();
        foreach ($answers as $answer) {
            $result[$answer->cp_user_id]['free_answer'] = $answer->free_answer;
        }
        return $result;
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_free_answer_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->image_url = $old_concrete_action->image_url;
        $new_concrete_action->button_label = $old_concrete_action->button_label;
        $new_concrete_action->question = $old_concrete_action->question;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $new_concrete_action->title = $old_concrete_action->title;
        return $this->cp_free_answer_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpFreeAnswerActionManager#deletePhysicalRelatedCpActionData cp_action_id=".$cp_action->id);
        }

        if ($with_concrete_actions) {
            //TODO delete concrete action
        }

        //delete answers
        $answers = $this->cp_free_answer_action_answers->find(array('cp_action_id' => $cp_action->id));
        if (!$answers) {
            return;
        }
        foreach ($answers as $answer) {
            $this->cp_free_answer_action_answers->deletePhysical($answer);
        }
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpFreeAnswerActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=".$cp_action->id);
        }
        //delete answers
        $answers = $this->cp_free_answer_action_answers->find(array('cp_action_id' => $cp_action->id, 'cp_user_id' => $cp_user->id));
        if (!$answers) {
            return;
        }
        foreach ($answers as $answer) {
            $this->cp_free_answer_action_answers->deletePhysical($answer);
        }
    }
}
