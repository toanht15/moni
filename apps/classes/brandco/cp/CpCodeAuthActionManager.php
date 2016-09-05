<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');

use Michelf\Markdown;

class CpCodeAuthActionManager extends aafwObject implements CpActionManager {

    use CpActionTrait;

    protected $cp_concrete_actions;
    protected $code_auth_users;

    public function __construct() {
        parent::__construct();
        $this->cp_actions = $this->getModel('CpActions');
        $this->cp_concrete_actions = $this->getModel('CpCodeAuthenticationActions');
        $this->code_auth_users = $this->getModel('CodeAuthenticationUsers');
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpActions($cp_action_id) {
        $cp_action = $this->getCpActionById($cp_action_id);
        $cp_concrete_action = $cp_action === null ? null : $this->getCpConcreteActionByCpAction($cp_action);

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
        $cp_concrete_action->title = '認証コード';
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
        $cp_concrete_action->image_url = $data["image_url"];
        $cp_concrete_action->text = $data["text"];
        $cp_concrete_action->html_content = Markdown::defaultTransform($data["text"]);
        $cp_concrete_action->title = $data["title"];
        $cp_concrete_action->code_auth_id = $data["code_auth_id"];

        $cp_concrete_action->min_code_flg = $data['min_code_flg'];
        if ($data['min_code_count']) {
            $cp_concrete_action->min_code_count = $data['min_code_count'];
        }

        $cp_concrete_action->max_code_flg = $data['max_code_flg'];
        if ($data['max_code_count']) {
            $cp_concrete_action->max_code_count = $data['max_code_count'];
        }

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
     * @param CpAction $old_cp_action
     * @param $new_cp_action_id
     * @return mixed
     */
    public function copyConcreteAction(CpAction $old_cp_action, $new_cp_action_id) {
        $cp_concrete_action = $this->getConcreteAction($old_cp_action);
        $new_concrete_action = $this->cp_concrete_actions->createEmptyObject();
        $new_concrete_action->cp_action_id = $new_cp_action_id;
        $new_concrete_action->title = $cp_concrete_action->title;
        $new_concrete_action->image_url = $cp_concrete_action->image_url;
        $new_concrete_action->text = !$cp_concrete_action->text ? '' : $cp_concrete_action->text;
        $new_concrete_action->html_content = $cp_concrete_action->html_content;
        $this->cp_concrete_actions->save($new_concrete_action);
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
     * @param $cp_action_id
     * @return entity
     */
    public function getCpConcreteActionByCpActionId($cp_action_id) {
        return $this->cp_concrete_actions->findOne(array('cp_action_id' => $cp_action_id));
    }

    /**
     * @param $code_auth_id
     * @return aafwEntityContainer|array
     */
    public function getCpConcreteActionByCodeAuthId($code_auth_id) {
        return $this->cp_concrete_actions->find(array('code_auth_id' => $code_auth_id));
    }

    /**
     * @param $user_id
     * @param $cp_action_id
     * @return aafwEntityContainer|array
     */
    public function getCodeAuthUsersByUserIdAndCpActionId($user_id, $cp_action_id) {
        $filter = array(
            'user_id' => $user_id,
            'cp_action_id' => $cp_action_id
        );

        return $this->code_auth_users->find($filter);
    }

    // Code Authentication Users

    /**
     * @return mixed
     * @throws aafwException
     */
    public function createEmptyCodeAuthUser() {
        return $this->code_auth_users->createEmptyObject();
    }

    /**
     * @param $code_auth_user
     */
    public function createCodeAuthUser($code_auth_user) {
        $this->code_auth_users->save($code_auth_user);
    }

    /**
     * @param $code_auth_user
     */
    public function updateCodeAuthUser($code_auth_user) {
        $this->code_auth_users->save($code_auth_user);
    }

    /**
     * @param $code_id
     * @param $user_id
     * @param $cp_action_id
     * @return aafwEntityContainer|array
     */
    public function getCodeAuthUser($code_id, $user_id, $cp_action_id) {
        $filter = array(
            'code_auth_code_id' => $code_id,
            'user_id' => $user_id,
            'cp_action_id' => $cp_action_id
        );

        return $this->code_auth_users->find($filter);
    }

    /**
     * @param $cp_action_id
     * @return aafwEntityContainer|array
     */
    public function getCodeAuthUsersByCpActionId($cp_action_id) {
        return $this->code_auth_users->find(array('cp_action_id' => $cp_action_id));
    }

    /**
     * @param $code_auth_code_id
     * @return aafwEntityContainer|array
     */
    public function countCodeAuthUsersByCodeAuthCodeId($code_auth_code_id) {
        return $this->code_auth_users->count(array('code_auth_code_id' => $code_auth_code_id));
    }

    /**
     * @param CpAction $cp_action
     * @param bool $with_concrete_actions
     * @throws Exception
     */
    public function deletePhysicalRelatedCpActionData(CpAction $cp_action, $with_concrete_actions = false) {
        if ($with_concrete_actions) {
            //TODO delete concrete action
        }

        if (!$cp_action || !$cp_action->id) {
            throw new Exception("CpCodeAuthActionManager#deletePhysicalRelatedCpActionData code_auth_code null cp_action_id=".$cp_action->id);
        }

        /** @var CodeAuthUserTrackingService $code_tracking_service */
        $code_tracking_service = $this->getService("CodeAuthUserTrackingService");
        $code_tracking_service->deletePhysicalTrackingLogByCpActionId($cp_action->id);

        $codes_auth_users = $this->code_auth_users->find(array("cp_action_id" => $cp_action->id));
        if (!$codes_auth_users) {
            return;
        }

        foreach ($codes_auth_users as $code_auth_user) {
            if (!$code_auth_user || !$code_auth_user->id) {
                throw new Exception("CpCodeAuthActionManager#deletePhysicalRelatedCpActionData code null cp_action_id=".$cp_action->id);
            }

            $this->deletePhysicalCodeAuthUser($code_auth_user);
        }
    }

    public function deletePhysicalRelatedCpActionDataByCpUser(CpAction $cp_action, CpUser $cp_user) {

        if (!$cp_action || !$cp_user) {
            throw new Exception("CpCodeAuthActionManager#deletePhysicalRelatedCpActionDataByCpUser cp_action_id=".$cp_action->id);
        }

        /** @var CodeAuthUserTrackingService $code_tracking_service */
        $code_tracking_service = $this->getService("CodeAuthUserTrackingService");
        $code_tracking_service->deletePhysicalTrackingLogByCpActionIdAndUserId($cp_action->id, $cp_user->user_id);

        $codes_auth_users = $this->code_auth_users->find(array("cp_action_id" => $cp_action->id, "user_id" => $cp_user->user_id));
        if (!$codes_auth_users) {
            return;
        }

        foreach ($codes_auth_users as $code_auth_user) {
            if (!$code_auth_user || !$code_auth_user->id) {
                throw new Exception("CpCodeAuthActionManager#deletePhysicalRelatedCpActionDataByCpUser code null cp_action_id=".$cp_action->id);
            }
            $this->deletePhysicalCodeAuthUser($code_auth_user);
        }
    }

    public function deletePhysicalCodeAuthUser(CodeAuthenticationUser $code_auth_user) {
        $code_auth_code_store = $this->getModel('CodeAuthenticationCodes');
        $code_auth_code = $code_auth_code_store->findOne($code_auth_user->code_auth_code_id);

        if (!$code_auth_code) {
            throw new Exception("CpCodeAuthActionManager#deletePhysicalCodeAuthUser code_auth_code null code_auth_user=".$code_auth_user->id);
        }

        $code_auth_code->reserved_num -= 1;
        $code_auth_code_store->save($code_auth_code);

        $this->code_auth_users->deletePhysical($code_auth_user);
    }
}
