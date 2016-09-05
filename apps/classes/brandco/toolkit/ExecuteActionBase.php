<?php
AAFW::import('jp.aainc.aafw.parsers.PHPParser');
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpActionManager');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.RequestUserInfoContainer');
AAFW::import('jp.aainc.classes.CpInfoContainer');

abstract class ExecuteActionBase extends BrandcoPOSTActionBase {

    public $NeedUserLogin = true;
    public $CsrfProtect = true;
    protected $AllowContent = array('JSON');
    protected $snsPermitStatuses = array(
        CpAction::TYPE_ENGAGEMENT,
        CpAction::TYPE_SHARE,
        CpAction::TYPE_TWITTER_FOLLOW,
        CpAction::TYPE_FACEBOOK_LIKE,
    );

    abstract function saveData();

    // 今のところアンケートモジュールしか使わない
    function saveExtraData($params = null) {}

    function doAction() {

        $cp_users = aafwEntityStoreFactory::create('CpUsers');
        
        try {

            $cp_users->begin();

            /** @var CpUserService $cp_user_service */
            /** @var CpFlowService $cp_flow_service */
            $cp_user_service = $this->createService('CpUserService');
            $cp_flow_service = $this->createService('CpFlowService');

            // アクションのステータスをアップデート
            $cp_user_service->joinAction($this->cp_user_id, $this->cp_action_id, $this->SERVER['HTTP_USER_AGENT'], Util::isSmartPhone());

            $this->saveData();

            // 次のアクションが存在するかチェック
            $next_action = $cp_flow_service->getCpNextActionByCpActionId($this->cp_action_id);

            // 次のアクションが無い場合
            if (!$next_action->id) {
                $data = array('next_action' => false, 'sns_action' => false);
                $json_data = $this->createAjaxResponse("ok", $data);
                $this->assign('json_data', $json_data);
            } else {
                // 次のアクションを取得
                $cp_action = CpInfoContainer::getInstance()->getCpActionById($next_action->cp_next_action_id);

                // 具体的なアクションを取得
                $manager = $cp_action->getActionManagerClass();
                $concrete_action = $manager->getConcreteAction($cp_action);
                // メッセージ送信
                list($message, $action_status) = $cp_user_service->sendActionMessage($this->cp_user_id, $cp_action, $concrete_action, true);

                $this->saveExtraData($action_status);

                $message_info = array(
                    "cp_action" => $cp_action,
                    "concrete_action" => $concrete_action,
                    "message" => $message,
                    "action_status" => $action_status
                );
                $cp_user = $cp_user_service->getCpUserById($this->cp_user_id);
                $cp = CpInfoContainer::getInstance()->getCpById($cp_user->cp_id);
                $cp_status = RequestuserInfoContainer::getInstance()->getStatusByCp($cp);
                $cp_info = $cp_flow_service->getCampaignInfo($cp,$this->brand, null, $cp_status);

                $shown_monipla_media_link = false;
                $can_display_syn_next = false;
                $is_last_cp_action_in_first_group = $cp_flow_service->isLastCpActionInFirstGroup($next_action->cp_next_action_id);
                if ($is_last_cp_action_in_first_group) {/** @var MoniplaPRService $monipla_pr_service */
                $monipla_pr_service = $this->getService('MoniplaPRService');
                $can_display_monipla_link = $monipla_pr_service->canDisplayMoniplaLink($this->getBrand(), $cp->id, $cp_user->from_id);
                $shown_monipla_media_link = $can_display_monipla_link && $cp->isCpTypeCampaign();
                $can_display_syn_next = Util::isSmartPhone() && $cp->isSynCpAndFromSynMenu($cp_user->from_id);
                }

                $this->Data['pageStatus']['cp'] = $cp;
                $this->Data['pageStatus']['from_id'] = $cp_user->from_id;
                // HTMLを作成
                $parser = new PHPParser();
                $html = $parser->parseTemplate(
                    'CpMessageAction.php',
                    array(
                        'cp_user' => $cp_user,
                        'message_info' => $message_info,
                        'pageStatus' => $this->Data['pageStatus'],
                        'cp_info' => $cp_info,
                        'is_last_cp_action_in_first_group' => $cp_flow_service->isLastCpActionInFirstGroup($next_action->cp_next_action_id),
                        'shown_monipla_media_link' => $shown_monipla_media_link,
                        'can_display_syn_next' => $can_display_syn_next
                    )
                );
                $data = array(
                    'next_action' => true,
                    'sns_action'  => in_array($cp_action->type, $this->snsPermitStatuses),
                    'message_id'  => $message->id
                );
                $json_data = $this->createAjaxResponse("ok", $data, array(), $html);
                $this->assign('json_data', $json_data);
            }
            $cp_users->commit();

            return 'dummy.php';
        } catch (Exception $e) {
            $cp_users->rollback();
            $logger = aafwLog4phpLogger::getDefaultLogger();
            $logger->error($e);
            $json_data = $this->createAjaxResponse("ng", array(), array(), '');
            $this->assign('json_data', $json_data);
            return 'dummy.php';
        } finally {
            $this->Data['destroyContainer'] = 1;
        }
    }

    protected function isCanPostSNS() {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService("CpFlowService");

        if ($cp_flow_service->isDemoCpByCpActionId($this->cp_action_id)) {
            return false;
        }

        return true;
    }

    protected function isCanSendCpInfoForMonipla() {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->createService("CpFlowService");

        if (!$this->cp_action_id) {
            return false;
        }

        $action = CpInfoContainer::getInstance()->getCpActionById($this->cp_action_id);
        if (!$action) {
            return false;
        }

        $cp = $cp_flow_service->getCpByCpAction($action);
        if (!$cp) {
            return false;
        }

        if ($cp->isDemo()) {
            return false;
        }

        if ($cp->isNonIncentiveCp()) {
            return false;
        }

        if ($this->getBrand()->test_page == Brand::BRAND_TEST_PAGE) {
            return false;
        }

        return true;
    }
}
