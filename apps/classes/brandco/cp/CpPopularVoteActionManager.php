<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

/**
* Class CpPopularVoteActionManager
*/
class CpPopularVoteActionManager extends aafwObject implements CpActionManager {
    use CpActionTrait;

    /** @var CpPopularVoteActionManager cp_concrete_actions */
    protected $cp_concrete_actions;


    function __construct() {
        $this->cp_actions = $this->getModel('CpActions');
        $this->cp_concrete_actions = $this->getModel('CpPopularVoteActions');
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $cp_concrete_action = null;
        } else {
            $cp_concrete_action = $this->getConcreteAction($cp_action);
        }

        return array($cp_action, $cp_concrete_action);
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
        $cp_concrete_action = $this->createConcreteAction($cp_action);
        return array($cp_action, $cp_concrete_action);
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
        return $this->cp_concrete_actions->findOne(array('cp_action_id' => $cp_action->id));
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $cp_concrete_action->cp_action_id = $cp_action->id;
        $cp_concrete_action->title = '人気投票';
        $this->cp_concrete_actions->save($cp_concrete_action);

        return $cp_concrete_action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_concrete_action = $this->getConcreteAction($cp_action);
        $cp_concrete_action->title = $data['title'];
        $cp_concrete_action->image_url = $data['image_url'];
        $cp_concrete_action->button_label_text = $data['button_label_text'];
        $cp_concrete_action->file_type = $data['file_type'];
        $cp_concrete_action->share_placeholder = $data['share_placeholder'];
        $cp_concrete_action->share_url_type = $data['share_url_type'];
        $cp_concrete_action->fb_share_required = $data['fb_share_required'];
        $cp_concrete_action->tw_share_required = $data['tw_share_required'];
        $cp_concrete_action->text = $data['text'];
        $cp_concrete_action->html_content = $data['html_content'];
        $cp_concrete_action->random_flg = $data['random_flg'];
        $cp_concrete_action->show_ranking_flg = $data['show_ranking_flg'];
        $cp_concrete_action->del_flg = 0;
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->getConcreteAction($cp_action);
        $cp_concrete_action->del_flg = 1;
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $old_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->title = $old_concrete_action->title;
        $new_concrete_action->image_url = $old_concrete_action->image_url;
        $new_concrete_action->button_label_text = $old_concrete_action->button_label_text;
        $new_concrete_action->text = $old_concrete_action->text;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $new_concrete_action->file_type = $old_concrete_action->file_type;
        $new_concrete_action->share_placeholder = $old_concrete_action->share_placeholder;
        $new_concrete_action->share_url_type = $old_concrete_action->share_url_type;
        $new_concrete_action->fb_share_required = $old_concrete_action->fb_share_required;
        $new_concrete_action->tw_share_required = $old_concrete_action->tw_share_required;
        $new_concrete_action->random_flg = $old_concrete_action->random_flg;
        $new_concrete_action->show_ranking_flg = $old_concrete_action->show_ranking_flg;
        $this->cp_concrete_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @param bool|false $with_concrete_actions
     * @throws Exception
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("deletePhysicalRelatedCpActionData#deletePhysicalRelatedCpActionData cp_action_id=" . $cp_action->id);
        }

        /** @var PopularVoteUserService $popular_vote_user_service */
        $popular_vote_user_service = $this->getService('PopularVoteUserService');
        $popular_vote_user_service->deletePhysicalPopularVoteUserByCpActionId($cp_action->id);
    }

    /**
     * @param CpAction $cp_action
     * @param CpUser $cp_user
     * @throws Exception
     */
    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("deletePhysicalRelatedCpActionDataByCpUser#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        /** @var PopularVoteUserService $popular_vote_user_service */
        $popular_vote_user_service = $this->getService('PopularVoteUserService');
        $popular_vote_user_service->deletePhysicalPopularVoteUserByCpActionIdAndCpUserId($cp_action->id, $cp_user->id);
    }
}
