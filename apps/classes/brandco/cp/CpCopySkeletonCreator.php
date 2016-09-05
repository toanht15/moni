<?php
AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpCreator');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionGroupTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpNextActionTrait');
AAFW::import('jp.aainc.classes.brandco.cp.*');

class CpCopySkeletonCreator extends aafwObject implements CpCreator {

    use CpTrait;
    use CpActionTrait;
    use CpActionGroupTrait;
    use CpNextActionTrait;

    private $logger;
    private $cp;

    public function __construct() {
        $this->cps = $this->getModel("Cps");
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_action_groups = $this->getModel("CpActionGroups");
        $this->cp_next_actions = $this->getModel("CpNextActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function create($brand_id, $data = array()) {
        try {
            $this->cps->begin();

            $this->cp = $this->copyCp($data['id']);


            //参加時SNS限定
            if($this->cp->join_limit_sns_flg == cp::FLAG_SHOW_VALUE) {
                $cp = $this->getCpById($data['id']);
                $this->cp->refreshJoinLimitSns($cp->getJoinLimitSns());
            }

            $action_groups = $this->getCpActionGroupsByCpId($data['id']);
            $cp_actions_alias = array();
            foreach ($action_groups as $action_group) {
                $new_group = $this->copyCpActionGroup($action_group, $this->cp->id);

                $cp_actions = $this->getCpActionsByCpActionGroupId($action_group->id);

                foreach ($cp_actions as $cp_action) {

                    //copy cp_action
                    $new_cp_action = $this->copyCpAction($cp_action, $new_group->id);
                    $cp_actions_alias[$cp_action->id] = $new_cp_action->id;

                    //copy concrete action
                    $action_manager = $cp_action->getActionManagerClass();
                    $action_manager->copyConcreteAction($cp_action, $new_cp_action->id);

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
            $this->logger->error("CpCopySkeletonCreator#create error" . $e);
            $this->cps->rollback();
        }

    }

}