<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

/**
* Class CpInstagramHashtagActionManager
*/
class CpInstagramHashtagActionManager extends aafwObject implements CpActionManager {
    use CpActionTrait;

    /** @var CpInstagramHashtagActions $cp_concrete_actions */
    protected $cp_concrete_actions;

    /** @var CpInstagramHashtags $cp_instagram_hashtags */
    protected $cp_instagram_hashtags;

    /** @var InstagramHashtagUsers $instagram_hashtag_users */
    protected $instagram_hashtag_users;

    /** @var InstagramHashtagUserPosts $instagram_hashtag_user_posts */
    protected $instagram_hashtag_user_posts;

    /** @var InstagramHashtagUserService $instagram_hashtag_user_service */
    protected $instagram_hashtag_user_service;

    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_concrete_actions = $this->getModel("CpInstagramHashtagActions");
        $this->cp_instagram_hashtags = $this->getModel("CpInstagramHashtags");
        $this->instagram_hashtag_users = $this->getModel("InstagramHashtagUsers");
        $this->instagram_hashtag_user_posts = $this->getModel("InstagramHashtagUserPosts");
        $this->instagram_hashtag_user_service = $this->getService('InstagramHashtagUserService');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
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
            $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
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
        return $this->getCpConcreteActionByCpAction($cp_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
     */
    public function createConcreteAction(CpAction $cp_action) {
        $cp_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $cp_concrete_action->cp_action_id = $cp_action->id;
        $cp_concrete_action->title = 'Instagram 投稿';
        $cp_concrete_action->button_label_text = '次へ';
        $this->cp_concrete_actions->save($cp_concrete_action);
        return $cp_concrete_action;
    }

    /**
     * @param CpAction $cp_action
     * @param $data
     * @return mixed
     */
    public function updateConcreteAction(CpAction $cp_action, $data) {
        $cp_concrete_action = $this->getCpConcreteActionByCpAction($cp_action);
        $cp_concrete_action->title = $data['title'];
        $cp_concrete_action->image_url = $data['image_url'];
        $cp_concrete_action->text = $data['text'];
        $cp_concrete_action->html_content = Markdown::defaultTransform($data['text']);
        $cp_concrete_action->button_label_text = $data['button_label_text'];
        $cp_concrete_action->skip_flg = $data['skip_flg'];
        $cp_concrete_action->autoload_flg = $data['autoload_flg'];
        $cp_concrete_action->approval_flg = $data['approval_flg'];
        $cp_concrete_action->del_flg = 0;
        $this->cp_concrete_actions->save($cp_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @return mixed
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
        $new_concrete_action->image_url = $old_concrete_action->image_url;
        $new_concrete_action->text = $old_concrete_action->text;
        $new_concrete_action->html_content = $old_concrete_action->html_content;
        $new_concrete_action->button_label_text = $old_concrete_action->button_label_text;
        $new_concrete_action->skip_flg = $old_concrete_action->skip_flg;
        $new_concrete_action->autoload_flg = $old_concrete_action->autoload_flg;
        $new_concrete_action->approval_flg = $old_concrete_action->approval_flg;
        $this->cp_concrete_actions->save($new_concrete_action);
    }

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @return mixed
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        $cp_concrete_action = $this->getConcreteAction($cp_action);

        $instagram_hashtag_users = $this->instagram_hashtag_user_service->getInstagramHashtagUsersByCpActionId($cp_action->id);
        $this->instagram_hashtag_user_service->deletePhysicalByInstagramHashtagUsers($instagram_hashtag_users, true);

        if ($cp_concrete_action && $cp_concrete_action->isExistsCpInstagramHashtags()) {
            if ($with_concrete_actions) {
                foreach ($cp_concrete_action->getCpInstagramHashtags() as $cp_instagram_hashtag) {
                    $this->cp_instagram_hashtags->deletePhysical($cp_instagram_hashtag);
                }
                $this->cp_concrete_actions->deletePhysical($cp_concrete_action);
            }else{
                foreach ($cp_concrete_action->getCpInstagramHashtags() as $cp_instagram_hashtag) {
                    $cp_instagram_hashtag->pagination = '';
                    $cp_instagram_hashtag->total_media_count_start = 0;
                    $cp_instagram_hashtag->total_media_count_ent = 0;
                    $cp_instagram_hashtag->cp_media_count_summary = 0;
                    $this->cp_instagram_hashtags->save($cp_instagram_hashtag);
                }
            }
        }
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {
        if (!$cp_action || !$cp_user) {
            throw new Exception("CpInstagramHashtagActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=" . $cp_action->id);
        }

        $instagram_hashtag_user = $this->instagram_hashtag_user_service->getInstagramHashtagUserByCpActionIdAndCpUserId($cp_action->id, $cp_user->id);
        if (!$instagram_hashtag_user) {
            return;
        }

        $this->instagram_hashtag_user_service->deletePhysicalByInstagramHashtagUser($instagram_hashtag_user, true);
    }
}
