<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpCreator');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionGroupTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpNextActionTrait');
AAFW::import('jp.aainc.classes.brandco.cp.*');

class CpCopyCreator extends aafwObject {

    use CpTrait;
    use CpActionTrait;
    use CpActionGroupTrait;
    use CpNextActionTrait;

    protected $cp;
    protected $cp_info;
    protected $logger;
    protected $cp_info_service;

    protected $blocked_action_list = array(
        CpAction::TYPE_COUPON,
        CpAction::TYPE_POPULAR_VOTE,
        CpAction::TYPE_INSTAGRAM_FOLLOW,
        CpAction::TYPE_INSTAGRAM_HASHTAG,
        CpAction::TYPE_YOUTUBE_CHANNEL,
        CpAction::TYPE_CODE_AUTHENTICATION
    );

    public function __construct() {
        parent::__construct();
        $this->cps = $this->getModel("Cps");
        $this->cp_info = $this->getModel("CpInfos");
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_action_groups = $this->getModel("CpActionGroups");
        $this->cp_next_actions = $this->getModel("CpNextActions");
        $this->cp_info_service = $this->getService('CpInfoService');
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create($data = array()) {
        try {
            $this->cps->begin();

            $data['fix_basic_flg'] = Cp::SETTING_FIX;
            $data['fix_attract_flg'] = Cp::SETTING_FIX;
            $data['status'] = Cp::STATUS_DEMO;
            $this->cp = $this->copyCpWithNewData($data['cp'], $data);

            if ($data['cp']->restricted_address_flg == Cp::CP_RESTRICTED_ADDRESS_FLG_ON) {
                $this->cp->updateCpRestrictedAddress($data['restricted_addresses']);
            }

            $old_cp_info = $this->cp_info_service->getCpInfoByCpId($data['cp']->id);
            $new_cp_info = $this->cp_info->createEmptyObject();
            $new_cp_info->cp_id = $this->cp->id;
            $new_cp_info->salesforce_id = $old_cp_info->salesforce_id;
            $this->cp_info_service->saveCpInfo($new_cp_info);

            //参加時SNS限定
            if ($this->cp->join_limit_sns_flg == cp::FLAG_SHOW_VALUE) {
                $this->cp->refreshJoinLimitSns($data['cp']->getJoinLimitSns());
            }

            $action_groups = $this->getCpActionGroupsByCpId($data['cp']->id);
            $cp_actions_alias = array();
            foreach ($action_groups as $action_group) {
                $new_group = $this->copyCpActionGroup($action_group, $this->cp->id);

                $cp_actions = $this->getCpActionsByCpActionGroupId($action_group->id);

                foreach ($cp_actions as $cp_action) {
                    if (in_array($cp_action->type, $this->blocked_action_list)) {
                        throw new aafwException('Cannot copy this action! $action_id: ' . $cp_action->id);
                    }

                    //copy cp_action
                    $new_cp_action = $this->copyCpActionAndConfirm($cp_action, $new_group->id);
                    $cp_actions_alias[$cp_action->id] = $new_cp_action->id;

                    //copy concrete action
                    $action_manager = $cp_action->getActionManagerClass();
                    $action_manager->copyConcreteAction($cp_action, $new_cp_action->id);

                    //update new title for new campaign
                    if ($action_group->order_no == 1 && $cp_action->order_no == 1) {
                        $concrete_action = $action_manager->getConcreteAction($cp_action);
                        $concrete_action->title = $this->createNewCpTitle($cp_action, $data['start_date']);

                        $action_manager->updateConcreteAction($new_cp_action, $concrete_action->getValues());
                    }
                }
            }

            //copy next action
            foreach ($cp_actions_alias as $old_action_id => $new_action_id) {
                $old_cp_action = $this->getCpActionById($old_action_id);
                $old_cp_next_actions = $old_cp_action->getCpNextActions();

                foreach ($old_cp_next_actions as $old_cp_next_action) {
                    $new_next_action = $this->createCpNextAction($new_action_id, $cp_actions_alias[$old_cp_next_action->cp_next_action_id]);

                    if ($old_cp_action->type == CpAction::TYPE_BUTTONS) {
                        $button_action_manager = $old_cp_action->getActionManagerClass();
                        $data = array();
                        $next_action_info = $button_action_manager->getNextActionInfoByCpNextAction($old_cp_next_action);
                        $data['cp_next_action_table_id'] = $new_next_action->id;
                        $data['label'] = $next_action_info->label;
                        $data['order'] = $next_action_info->order_no;
                        $button_action_manager->createNextActionInfo($data);
                    }
                }
            }

            $this->cps->commit();

            return $this->cp;
        } catch (Exception $e) {
            $this->logger->error("CpCopyCreator#create error! cp_id = " . $data['cp']->id . " Exception " . $e);
            $this->cps->rollback();
            return false;
        }
    }

    /**
     * @param $first_action
     * @param $start_date
     * @return string
     */
    public function createNewCpTitle($first_action, $start_date) {
        $old_cp_title = $first_action->getCpActionData()->title;
        $date = date('m月d日', strtotime($start_date));
        $new_cp_title = $old_cp_title . "【{$date}】";

        return $new_cp_title;
    }
}