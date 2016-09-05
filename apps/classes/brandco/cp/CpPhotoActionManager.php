<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

class CpPhotoActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    /** @var CpPhotoActions $cp_concrete_actions */
    protected $cp_concrete_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_concrete_actions = $this->getModel("CpPhotoActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * cp_actionとcp_concrete_action取得
     * @param $cp_action_id
     * @return array|mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        if ($cp_action === null) {
            $cp_concrete_action = null;
        } else {
            $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        }
        return array($cp_action, $cp_concrete_action);
    }

    /**
     * cp_actionとcp_concrete_action生成
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
     * cp_actionとcp_concrete_action削除
     * @param CpAction $cp_action
     * @return mixed
     */
    public function deleteCpActions(CpAction $cp_action) {
        $this->deleteConcreteAction($cp_action);
        $this->deleteCpAction($cp_action);
    }

    /**
     * cp_actionとcp_concrete_action更新
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateCpActions(CpAction $cp_action, $data) {
        $this->updateCpAction($cp_action);
        $this->updateConcreteAction($cp_action, $data);
    }


    /**
     * cp_concrete_action取得
     * @param CpAction $cp_action
     * @return mixed
     */
    public function getConcreteAction(CpAction $cp_action) {
        return $this->getCpConcreteActionByCpAction($cp_action);
    }

    /**
     * cp_concrete_action生成
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $cp_concrete_action->cp_action_id = $cp_action->id;
        $cp_concrete_action->title = "写真投稿";
        $this->cp_concrete_actions->save($cp_concrete_action);
        return $cp_concrete_action;
    }

    /**
     * cp_concrete_action更新
     * @param CpAction $cp_action
     * @param $data
     * @return mixed|void
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $cp_concrete_action->del_flg = 0;
        $cp_concrete_action->title = $data['title'];
        $cp_concrete_action->image_url = $data['image_url'];
        $cp_concrete_action->text = $data['text'];
        $cp_concrete_action->html_content = Markdown::defaultTransform($data['text']);
        $cp_concrete_action->title_required = $data['title_required'];
        $cp_concrete_action->comment_required = $data['comment_required'];
        $cp_concrete_action->fb_share_required = $data['fb_share_required'];
        $cp_concrete_action->tw_share_required = $data['tw_share_required'];
        $cp_concrete_action->panel_hidden_flg = $data['panel_hidden_flg'];
        $cp_concrete_action->button_label_text = $data['button_label_text'];
        $cp_concrete_action->share_placeholder = $data['share_placeholder'];
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * cp_concrete_action削除
     * @param $cp_action
     * @return mixed|void
     */
    public function deleteConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $cp_concrete_action->del_flg = 1;
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * cp_concrete_action取得
     * @param $cp_action
     * @return mixed
     */
    private function getCpConcreteActionByCpAction(CpAction $cp_action) {
        return $this->cp_concrete_actions->findOne(array("cp_action_id" => $cp_action->id));
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
        $new_concrete_action->text = $old_concrete_action->text;
        $new_concrete_action->image_url = $old_concrete_action->image_url;
        $new_concrete_action->title_required = $old_concrete_action->title_required;
        $new_concrete_action->comment_required = $old_concrete_action->comment_required;
        $new_concrete_action->panel_hidden_flg = $old_concrete_action->panel_hidden_flg;
        $new_concrete_action->fb_share_required = $old_concrete_action->fb_share_required;
        $new_concrete_action->tw_share_required = $old_concrete_action->tw_share_required;
        $new_concrete_action->button_label_text = $old_concrete_action->button_label_text;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $this->cp_concrete_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @return mixed|void
     * @throws Exception
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpPhotoActionManager#deletePhysicalRelatedCpActionData cp_action_id=" . $cp_action->id);
        }

        /** @var PhotoUserService $photo_user_service */
        $photo_user_service = $this->getService('PhotoUserService');
        $photo_users = $photo_user_service->getPhotoUsersByActionIds($cp_action->id);

        /** @var PhotoEntries $photo_entries */
        $photo_entries = $this->getModel('PhotoEntries');
        /** @var PhotoUsers $photo_user_model */
        $photo_user_model = $this->getModel('PhotoUsers');

        foreach ($photo_users as $photo_user) {
            if ($photo_user->isExistsPhotoEntries()){
                $photo_entry = $photo_user->getPhotoEntry();

                $this->deletePhysicalPhotoUserShare($photo_user);

                $photo_entries->deletePhysical($photo_entry);
            }
            $photo_user_model->deletePhysical($photo_user);
        }

        if ($with_concrete_actions) {
            $cp_concrete_action = $this->getConcreteAction($cp_action);
            $this->cp_concrete_actions->deletePhysical($cp_concrete_action);
        }
    }

    private function deletePhysicalPhotoUserShare($photo_user) {
//        $multi_post_sns_queues = $this->getModel('MultiPostSnsQueues');
//        foreach( $multi_post_sns_queues->find() as $multi_post_sns_queue) {
//            $multi_post_sns_queues->deletePhysical($multi_post_sns_queue);
//        }

        $photo_user_shares = $this->getModel('PhotoUserShares');

        if ($photo_user->isExistsPhotoUserShares()){
            foreach($photo_user->getPhotoUserShares() as $photo_user_share) {
                $photo_user_shares->deletePhysical($photo_user_share);
            }
        }

    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpPhotoActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        /** @var PhotoUserService $photo_user_service */
        $photo_user_service = $this->getService('PhotoUserService');
        $photo_users = $photo_user_service->getPhotoUserByActionIdAndCpUserId($cp_action->id, $cp_user->id);
        if (!$photo_users) {
            return;
        }
        /** @var PhotoEntries $photo_entries */
        $photo_entries = $this->getModel('PhotoEntries');
        /** @var PhotoUsers $photo_user_model */
        $photo_user_model = $this->getModel('PhotoUsers');

        foreach ($photo_users as $photo_user) {
            if (!$photo_user) {
                throw new Exception("CpPhotoActionManager#deletePhysicalRelatedCpActionDataByCpUser photo_user null cp_action_id=" . $cp_action->id);
            }
            if ($photo_user->isExistsPhotoEntries()){
                $photo_entry = $photo_user->getPhotoEntry();
                if (!$photo_entry) {
                    throw new Exception("CpPhotoActionManager#deletePhysicalRelatedCpActionDataByCpUser photo_entry null cp_action_id=" . $cp_action->id);
                }
                $this->deletePhysicalPhotoUserShare($photo_user);
                $photo_entries->deletePhysical($photo_entry);
            }
            $photo_user_model->deletePhysical($photo_user);
        }
    }
}
