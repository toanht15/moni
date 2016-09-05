<?php
/**
 * User: t-yokoyama
 * Date: 15/03/24
 * Time: 13:32
 */

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

class CpFacebookLikeActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    const LIKE_FORM_TITLE =
        "「%s」のFacebookページを応援しよう！";
    const SOCIAL_TYPE_STRING = 'Facebook';

    /** @var CpFacebookLikeActions $cp_concrete_actions */
    protected $cp_concrete_actions;
    protected $logger;

    function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_concrete_actions = $this->getModel("CpFacebookLikeActions");
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
        $cp_concrete_action->title = '';
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
        $cp_concrete_action->del_flg = 0;
        $cp_concrete_action->title = $data['title'];
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
    public function getCpConcreteActionByCpAction(CpAction $cp_action) {
        return $this->cp_concrete_actions->findOne(array("cp_action_id" => $cp_action->id));
    }

    /**
     * cp_concrete_action取得
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpConcreteActionByCpActionId($cp_action_id) {
        return $this->cp_concrete_actions->findOne(array("cp_action_id" => $cp_action_id));
    }

    /**
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $cp_fb_like_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->title = $cp_fb_like_action->title;
        $this->cp_concrete_actions->save($new_concrete_action);

        //copy cp_facebook_like_accounts
        /** @var CpFacebookLikeAccountService $cp_fb_like_account_service */
        $cp_fb_like_account_service = $this->_ServiceFactory->create('CpFacebookLikeAccountService');
        $engagement_tw_social_account =
            $cp_fb_like_account_service->getLikeTargetSocialAccount($cp_fb_like_action->id);
        $cp_fb_like_account_service->create(
            $new_concrete_action->id,
            $engagement_tw_social_account->brand_social_account_id
        );
    }

    /**
     * Likeフォームタイトルを生成
     *
     * @param $brand_social_account_name
     * @return string
     */
    public function makeLikeActionTitle($brand_social_account_name) {
        $title = sprintf(
            self::LIKE_FORM_TITLE, $brand_social_account_name
        );

        return $title;
    }

    /**
     * @param CpAction $cp_action
     * @return mixed|void
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpFacebookLikeActionManager#deletePhysicalRelatedCpActionData cp_action_id=".$cp_action->id);
        }

        if ($with_concrete_actions) {
            //TODO delete concrete action
        }
        /** @var CpFacebookLikeLogService $fb_like_log_service */
        $fb_like_log_service = $this->getService("CpFacebookLikeLogService");
        $fb_like_log_service->deletePhysicalFbLikeLogsByCpActionId($cp_action->id);
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {

        if (!$cp_action || !$cp_user) {
            throw new Exception("CpFacebookLikeActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=".$cp_action->id);
        }

        /** @var CpFacebookLikeLogService $fb_like_log_service */
        $fb_like_log_service = $this->getService("CpFacebookLikeLogService");
        $fb_like_log_service->deletePhysicalFbLikeLogsByCpActionIdAndCpUserId($cp_action->id, $cp_user->id);
    }
}
