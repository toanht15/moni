<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');

class message extends BrandcoGETActionBase {
    public $NeedOption = array(BrandOptions::OPTION_CP);
    public $checkCpClosed = true;
    protected $ContainerName = 'logging';
    private $user_id;

    public function validate() {
        $cp_id = $this->GET['exts'][0];

        // キャンペーン情報のバリデート
        $this->Data['cp'] = CpInfoContainer::getInstance()->getCpById($cp_id);
        if (!$this->Data['cp'] || $this->Data['cp']->type !== Cp::TYPE_MESSAGE || $this->Data['cp']->brand_id !== $this->getBrand()->id) {
            return "404";
        }

        // 値を取得
        $params = json_decode(base64_decode($this->msg_token), true);
        if (!$params['user_id'] || !$params['cp_action_id']) {
            return "404";
        }
        $this->user_id = $params['user_id'];

        // ユーザ情報のバリデート
        /** @var CpUserService $cp_user_service */
        $cp_user_service = $this->getService('CpUserService');
        $this->Data['cp_user'] = $cp_user_service->getCpUserByCpIdAndUserId($cp_id, $params['user_id']);
        if (!$this->Data['cp_user']) {
            return "404";
        }
        
        $service_factory = new aafwServiceFactory();
        $cp_user_action_status_service = $service_factory->create('CpUserActionStatusService');
        $status = $cp_user_action_status_service->getCpUserActionStatus(
            $this->Data['cp_user']->id, $params['cp_action_id'])->status;

        $this->Data['status'] = $status;

        return true;
    }

    function doAction() {
        if ($this->isLogin()) {
            /** @var UserService $user_service */
            $user_service = $this->getService('UserService');

            $login_info = $this->getLoginInfo();
            $user = $user_service->getUserByMoniplaUserId($login_info['userInfo']->id);

            if ($user->id === $this->Data['cp_user']->user_id) {
                return 'redirect: ' . Util::rewriteUrl('messages', 'thread', array($this->Data['cp']->id));
            } else {
                // ログアウト
                $this->setSharedCriticalResourceSessionThatMustBeUsedVeryCarefully('logoutRedirectUrl',
                    Util::rewriteUrl('', 'message', array($this->Data['cp']->id), array('msg_token' => $this->msg_token)));
                return 'redirect: ' . Util::rewriteUrl('my', 'logout');
            }
        }
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $cp_actions = $cp_flow_service->getCpActionsInFirstGroupByCpId($this->Data['cp']->id);
        /** @var  $cp_user_service CPUserService */
        $cp_user_service = $this->createService('CpUserService');

        // アクション数が1つの場合、最後のアクションである
        $this->Data['is_last_action_in_group'] = count($cp_actions) === 1;

        $this->Data['cp_action'] = $cp_actions[0];
        $this->Data['concrete_action'] = $cp_actions[0]->getActionManagerClass()->getConcreteAction($cp_actions[0]);
        $this->Data['msg_token'] = $this->msg_token;

        $message = $cp_user_service->findCpUserActionMessageByCpUserId($this->Data['cp_user']->id);

        $this->Data['message_info'] = array(
            "message" => $message,
            "cp_action" => $this->Data['cp_action'],
        );
        
        $this->Data['can_read_message'] = $this->Data['message_info']['cp_action']->isActive($this->Data['cp']);
        // CpUserActionStatus・CpUserActionMessageのステータス更新
        if (!$this->updateCpUserActionStatusAndMessage($cp_actions, $this->Data['can_read_message'])) {
            return 500;
        }

        return 'user/brandco/message.php';
    }

    /**
     * @param $cp_actions
     * @param $can_read_message
     * @return bool
     */
    private function updateCpUserActionStatusAndMessage($cp_actions, $can_read_message) {
        /** @var CpUserActionStatusService $cp_user_action_status_service */
        $cp_user_action_status_service = $this->getService('CpUserActionStatusService');
        $cp_user_action_status = $cp_user_action_status_service->getCpUserActionStatusByCpUserIdAndCpActionId($this->Data['cp_user']->id, $cp_actions[0]->id);

        if ($cp_user_action_status->status == CpUserActionStatus::NOT_JOIN) {
            $cp_users = aafwEntityStoreFactory::create('CpUsers');

            try {
                $cp_users->begin();

                /** @var CpUserService $cp_user_service */
                $cp_user_service = $this->getService('CpUserService');
                if ($can_read_message) {
                    $cp_user_service->joinAction($this->Data['cp_user']->id, $cp_actions[0]->id, $this->SERVER['HTTP_USER_AGENT'], Util::isSmartPhone());
                }
                $cp_user_action_message = $cp_user_action_status_service->getCpUserActionMessagesByCpUserIdAndCpActionId($this->Data['cp_user']->id, $cp_actions[0]->id);
                $cp_user_service->readCpUserActionMessage($cp_user_action_message);

                // アクション数が1つ以上の場合、次のアクションのステータスを作成する
                if (count($cp_actions) !== 1) {
                    $manager = $cp_actions[1]->getActionManagerClass();
                    $concrete_action = $manager->getConcreteAction($cp_actions[1]);
                    $cp_user_service->sendActionMessage($this->Data['cp_user']->id, $cp_actions[1], $concrete_action, false);
                }

                $cp_users->commit();
            } catch (Exception $e) {
                $cp_users->rollback();
                aafwLog4phpLogger::getDefaultLogger()->error($e);
                aafwLog4phpLogger::getDefaultLogger()->error('message/updateCpUserActionStatusAndMessage');

                return false;
            }
        }

        return true;
    }
}
