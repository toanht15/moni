<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoPOSTActionBase');
AAFW::import('jp.aainc.classes.entities.Cp');
AAFW::import('jp.aainc.classes.entities.CpAction');
AAFW::import('jp.aainc.classes.brandco.cp.CpNewSkeletonCreator');

class save_setting_skeleton extends BrandcoPOSTActionBase {

    protected $ContainerName = 'save_setting_skeleton';
    protected $Form = array(
        'package' => 'admin-cp',
        'action' => 'edit_setting_skeleton?mid=failed',
    );
    /** @var $cp_flow_service CpFlowService */
    protected $cp_flow_service;

    public $NeedOption = array();
    public $NeedAdminLogin = true;
    public $CsrfProtect = true;

    protected $ValidatorDefinition = array(
        'shipping_address_present' => array(
            'type' => 'str',
        ),
        'shipping_address_questionnaire' => array(
            'type' => 'str',
        ),
    );

    public function doThisFirst() {
        $this->Data['skeleton_type'] = $this->POST['skeleton_type'];
        $this->Data['cps_type'] = $this->POST['cps_type'];
        $this->Data['id'] = $this->POST['cp_id'];
        $this->Data['join_limit_flg']   = $this->POST['join_limit_flg'];
        $this->Data['announce_type'] = $this->POST['announce_type'];

        if ($this->Data['skeleton_type'] == Cp::SKELETON_NEW) {
            $this->ValidatorDefinition['groupCount']['required'] = true;
        }
        $this->cp_flow_service = $this->createService('CpFlowService');
    }

    public function validate() {
        if ($this->Data['announce_type'] != CpCreator::ANNOUNCE_NON_INCENTIVE && !in_array($this->Data['announce_type'], array_keys(CpNewSkeletonCreator::$announce_type))) {
            return '404';
        }

        if (!$this->Data['skeleton_type'] || ($this->Data['skeleton_type'] != Cp::SKELETON_NEW && !$this->Data['id'])) {
            return '404';
        }

        if(!in_array($this->Data['cps_type'], array_keys(cp::$cp_type_array))){
            return '404';
        }

        if($this->Data['skeleton_type'] == Cp::SKELETON_NEW && $this->Data['cps_type'] == Cp::TYPE_MESSAGE) {
            $this->Form = array(
                'package' => 'admin-cp',
                'action' => 'edit_customize_skeleton?type=2&mid=failed',
            );
        }

        if ($this->Data['skeleton_type'] == Cp::SKELETON_COPY || $this->Data['skeleton_type'] == Cp::SKELETON_ADD) {
            $cp_validator = new CpValidator($this->getBrand()->id);
            if(!$cp_validator->isOwner($this->Data['id'])) {
                return '404';
            }

            if($this->Data['skeleton_type'] == Cp::SKELETON_ADD) {
                $cp = $this->cp_flow_service->getCpById($this->Data['id']);
                $this->Data['announce_type'] = $cp->selection_method;
                if($cp->status == Cp::STATUS_DRAFT) {
                    $this->Form = array(
                        'package' => 'admin-cp',
                        'action' => 'edit_setting_basic/'.$cp->id.'?mid=failed',
                    );
                } else {
                    $first_action = $this->cp_flow_service->getFirstActionOfCp($this->Data['id']);
                    $this->Form = array(
                        'package' => 'admin-cp',
                        'action' => 'edit_action/'.$cp->id.'/'.$first_action->id.'?mid=failed',
                    );
                }
            }
        }

        // 編集不可なグループのorder_noリスト
        $uneditable_group_order_numbers = array();

        $group_updates = explode(",", $this->POST['groupUpdate']);
        if($this->Data['skeleton_type'] == Cp::SKELETON_ADD) {
            // 変更不可のグループについて、本当に変更がないか確認
            $not_editable_groups = $this->cp_flow_service->getNotEditableGroups($this->Data['id']);
            foreach($not_editable_groups as $value) {
                // 編集してはいけないグループが消されていないか確認
                if(!in_array($value['group_id'], $group_updates)) {
                    return false;
                }

                $cp_action_group = $this->cp_flow_service->getCpActionGroupById($value['group_id']);
                $cp_actions = $this->cp_flow_service->getCpActionsByCpActionGroupId($value['group_id']);

                // action_idの配列を格納
                $action_updates = explode(",", $this->POST['actionUpdate'.$cp_action_group->order_no]);

                // POSTされたactionsのidと保存されているactionsのidを比較
                foreach($cp_actions as $cp_action) {
                    if($action_updates[$cp_action->order_no - 1] != $cp_action->id) {
                        return false;
                    }
                }

                $uneditable_group_order_numbers[] = $cp_action_group->order_no;
            }
        }

        if($this->Data['skeleton_type'] == Cp::SKELETON_NEW || $this->Data['skeleton_type'] == Cp::SKELETON_ADD) {
            $available_action = new CpAction();
            if($this->Data['cps_type'] == Cp::TYPE_MESSAGE) {
                $available_action_details = $available_action->getAvailableMessageActions();
            } else {
                $available_action_details = $available_action->getAvailableCampaignActions();
            }

            $action_list = array();
            for ($i = 1; $i <= $this->POST['groupCount']; $i++) {
                $actions_type = explode(",", $this->POST['group' . $i]);
                foreach ($actions_type as $order => $action_type) {
                    if (!$this->isNumeric($order) || $this->isEmpty($order)) {
                        return false;
                    }

                    if(!array_key_exists($action_type, $available_action_details)) {
                        return false;
                    }

                    if($this->Data['cps_type'] == Cp::TYPE_CAMPAIGN) {
                        // クーポンは各グループの先頭にあってはいけない
                        if($action_type == CpAction::TYPE_COUPON && $order == 0) {
                            return false;
                        }
                        // 同一キャンペーン内に複数あってはいけないアクションについてチェック
                        if(in_array($action_type, array(CpAction::TYPE_ENTRY,
                                CpAction::TYPE_JOIN_FINISH,
                                CpAction::TYPE_INSTANT_WIN,
                                CpAction::TYPE_MOVIE,
                                CpAction::TYPE_SHARE,
                                CpAction::TYPE_GIFT,
                            )) && in_array($action_type, $action_list)) {
                            return false;
                        } else {
                            $action_list[] = $action_type;
                        }
                    }
                }
                if($this->Data['skeleton_type'] == Cp::SKELETON_ADD) {
                    $action_updates = explode(",", $this->POST['actionUpdate'.$i]);
                    if(count($action_updates) != count($actions_type)) {
                        return false;
                    }
                }
                if($this->Data['cps_type'] == Cp::TYPE_MESSAGE) {
                    if($i == 1) {
                        // 最初のステップはメッセージ
                        if($actions_type[0] != CpAction::TYPE_MESSAGE) {
                            return false;
                        }
                    }

                    continue;
                }
                // 第1グループ
                if($i == 1) {
                    // 最初のステップはエントリーかアンケート
                    if($this->Data['announce_type'] != CpCreator::ANNOUNCE_NON_INCENTIVE && $actions_type[0] != CpAction::TYPE_ENTRY) {
                        return false;
                    }
                    if($this->Data['announce_type'] == CpCreator::ANNOUNCE_NON_INCENTIVE && !($actions_type[0] == CpAction::TYPE_QUESTIONNAIRE || $actions_type[0] == CpAction::TYPE_PAYMENT)) {
                        return false;
                    }
                    // 「参加後その場で抽選」の場合は当選通知、スピードくじ必須
                    if($this->Data['announce_type'] == CpCreator::ANNOUNCE_LOTTERY && (!in_array(CpAction::TYPE_ANNOUNCE, $actions_type) || !in_array(CpAction::TYPE_INSTANT_WIN, $actions_type))) {
                        return false;
                    }
                // 第2グループ
                } elseif($i == 2) {
                    // 「キャンペーン終了後に当選者を選考」の場合は当選通知必須
                    if($this->Data['announce_type'] == CpCreator::ANNOUNCE_SELECTION && !in_array(CpAction::TYPE_ANNOUNCE, $actions_type)) {
                        return false;
                    }

                    // 発送をもって発表の場合、第2グループに発送をもって発表アクションが存在しているか確認
                    if($this->Data['announce_type'] == CpCreator::ANNOUNCE_DELIVERY && !in_array(CpAction::TYPE_ANNOUNCE_DELIVERY, $actions_type)) {
                        return false;
                    }
                    // 発送をもって発表のがある場合でも、同一グループに複数のアクションがあるか確認
                    if(in_array(CpAction::TYPE_ANNOUNCE_DELIVERY, $actions_type) && count($actions_type) > 1) {
                        return false;
                    }
                }

                // グループの最後のアクションを確認
                if (!in_array($i, $uneditable_group_order_numbers) && !$this->isValidTypeForLastCpAction(end($actions_type))) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param $type
     * @return bool
     */
    public function isValidTypeForLastCpAction($type) {
        $enabled_types = array(
            CpAction::TYPE_JOIN_FINISH,
            CpAction::TYPE_ANNOUNCE,
            CpAction::TYPE_MESSAGE,
            CpAction::TYPE_COUPON,
            CpAction::TYPE_ANNOUNCE_DELIVERY
        );

        return in_array($type, $enabled_types, true);
    }

    function doAction() {
        if ($this->Data['skeleton_type'] == Cp::SKELETON_COPY) {
            //過去のキャンペーンのコピーを使う
            $creator = new CpCopySkeletonCreator();
            $cp = $creator->create($this->brand->id, $this->Data);
        } elseif ($this->Data['skeleton_type'] == Cp::SKELETON_NEW) {
            $creator = new CpNewSkeletonCreator();
            $cp = $creator->create($this->brand->id, $this->POST, $this->Data['cps_type'], $this->Data['join_limit_flg']);
        } elseif ($this->Data['skeleton_type'] == Cp::SKELETON_ADD) {
            $adder = new CpNewSkeletonCreator();
            $first_action = $adder->updateAction($this->Data['id'], $this->POST);
            $cp = $this->cp_flow_service->getCpById($this->Data['id']);
        }

        $this->Data['saved'] = 1;

        if($this->Data['skeleton_type'] == Cp::SKELETON_ADD){
            if($cp->status == Cp::STATUS_DRAFT) {
                $return = 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_setting_basic', array($cp->id), array('mid' => 'updated'));
            } else {
                $return = 'redirect:' . Util::rewriteUrl('admin-cp', 'edit_action', array("cp_id" => $this->Data['id'], "action_id" => $first_action->id), array('mid' => 'updated'));
            }
        }else{
            // NEW or COPY
            if($this->Data['cps_type'] == cp::TYPE_CAMPAIGN) {
                $return = 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_setting_basic', array($cp->id));
            }elseif($this->Data['cps_type'] == cp::TYPE_MESSAGE) {
                $cp_action_groups = $this->cp_flow_service->getCpActionGroupsByCpId($cp->id);
                $cp_action = $this->cp_flow_service->getCpActionByGroupIdAndOrderNo($cp_action_groups->current()->id, 1);
                $return = 'redirect: ' . Util::rewriteUrl('admin-cp', 'edit_action', array("cp_id" => $cp->id, "action_id" => $cp_action->id));
            }
        }
        return $return;
    }
}
