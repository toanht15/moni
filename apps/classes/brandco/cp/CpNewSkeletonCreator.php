<?php

AAFW::import('jp.aainc.lib.base.aafwObject');
AAFW::import('jp.aainc.classes.brandco.cp.interface.CpCreator');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionGroupTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpActionTrait');
AAFW::import('jp.aainc.classes.brandco.cp.trait.CpNextActionTrait');
AAFW::import('jp.aainc.classes.brandco.cp.*');

class CpNewSkeletonCreator extends aafwObject implements CpCreator {

    use CpTrait;
    use CpActionTrait;
    use CpActionGroupTrait;
    use CpNextActionTrait;

    public static $shipping_address_type = array(
        self::SHIPPING_ADDRESS_ALL => '全員',
        self::SHIPPING_ADDRESS_ELECTED => '当選者',
        self::SHIPPING_ADDRESS_NONE=>'なし'
    );

    private $logger;
    private $cp;

    public static $announce_type = array(
        self::ANNOUNCE_SELECTION => 'キャンペーン終了後に当選者を選考',
        self::ANNOUNCE_FIRST => '参加後その場で先着順',
        self::ANNOUNCE_LOTTERY => '参加後その場で抽選',
        self::ANNOUNCE_DELIVERY => '賞品の発送をもって発表',
    );

    public function __construct() {
        $this->cps = $this->getModel("Cps");
        $this->cp_actions = $this->getModel("CpActions");
        $this->cp_action_groups = $this->getModel("CpActionGroups");
        $this->cp_next_actions = $this->getModel("CpNextActions");
        $this->logger = aafwLog4phpLogger::getDefaultLogger();
    }

    public function create($brand_id, $data = array(), $cps_type = cp::TYPE_CAMPAIGN, $join_limit_flg = cp::JOIN_LIMIT_OFF) {
        try {
            $this->cps->begin();

            if ($cps_type == cp::TYPE_CAMPAIGN) {
                $this->cp = $this->createCp($brand_id, $data['announce_type'], $join_limit_flg);
            } elseif ($cps_type == cp::TYPE_MESSAGE) {
                $this->cp = $this->createMsg($brand_id);
            }

            $prev_cp_action = null;
            //create cp data
            for ($i = 1; $i <= $data['groupCount']; $i++) {

                //create cp group
                $cp_group = $this->createCpActionGroup($this->cp->id, $i);

                $actions_type = explode(",", $data['group' . $i]);
                foreach ($actions_type as $order => $action_type) {
                    $action_manager = $this->getManagerClassByType($action_type);

                    if ($action_type === CpAction::TYPE_ANNOUNCE_DELIVERY) {
                        $action = $action_manager->createCpActions($cp_group->id, $action_type, CpAction::STATUS_FIX, $order+1);
                    } else {
                        $action = $action_manager->createCpActions($cp_group->id, $action_type, CpAction::STATUS_DRAFT, $order+1);
                    }

                    if ($prev_cp_action) {
                        $this->createCpNextAction($prev_cp_action->id, $action[0]->id);
                    }
                    $prev_cp_action = $action[0];
                }
                $prev_cp_action = null;
            }
            $this->cps->commit();

        } catch (Exception $e) {
            $this->logger->error("CpNewSkeletonCreator#create error" . $e);
            $this->cps->rollback();

        } finally {
            $this->logger->debug("CpNewSkeletonCreator#create success");
        }

        return $this->cp;
    }

    public function updateAction($cp_id, $data = array()) {
        if(!$cp_id) {
            return;
        }
        try {
            $this->cps->begin();

            $all_old_groups = $this->getCpActionGroupsByCpId($cp_id);
            if(!$all_old_groups) {
                return;
            }

            // 変更不可のグループについては変更をしないため、再確認
            $data_builder = aafwDataBuilder::newBuilder();
            $condition = array(
                'cp_id' => $cp_id
            );
            $not_editable_groups = $data_builder->getNotEditableGroups($condition, array());
            $not_editable_group_array = array();
            foreach($not_editable_groups as $not_editable_group) {
                $not_editable_group_array[] = $not_editable_group->id;
            }

            $old_groups = array();
            foreach($all_old_groups as $old_group) {
                $old_groups[$old_group->id] = $old_group;
                // 編集不可
                if(in_array($old_group->id, $not_editable_group_array)) {
                    continue;
                }
                // 更新作業以前に登録済みのアクション
                $old_cp_actions[] = $this->getCpActionsByCpActionGroupId($old_group->id);
            }

            // cp_next_actionsを1グループずつ一旦全て削除
            foreach($old_cp_actions as $old_actions) {
                $this->deleteCpNextActionsInGroup($old_actions);
            }

            $first_action = null;
            $prev_cp_action = null;
            $action_id_list = array();
            $group_id_list = array();
            $post_group_updates = explode(",", $data['groupUpdate']);
            for($i = 1; $i <= $data['groupCount']; $i++) {
                $update_group_id = $post_group_updates[$i - 1];
                $group_id_list[] = $update_group_id;

                if($update_group_id != -1) {
                    $cp_group = $old_groups[$update_group_id];

                    // 前のグループが削除されたりなどでグループ番号が変わった場合
                    if($i != $cp_group->order_no) {
                        $cp_group->order_no = $i;
                        $this->updateCpActionGroup($cp_group);
                    }

                    if(in_array($update_group_id, $not_editable_group_array)) {
                        continue;
                    }
                } else {
                    $cp_group = $this->createCpActionGroup($cp_id, $i);
                }
                $actions_type = explode(",", $data['group' . $i]);
                $update_action_ids = explode(",", $data['actionUpdate' . $i]);
                foreach($actions_type as $order => $action_type) {
                    $action_manager = $this->getManagerClassByType($action_type);
                    $action_id = $update_action_ids[$order];

                    if($action_id != '-1' && !in_array($action_id, $action_id_list)) {
                        $action = $this->getCpActionById($action_id);
                        $action->order_no = $order + 1;
                        $action->cp_action_group_id = $cp_group->id;
                        $this->updateCpAction($action);
                        $action_id_list[] = $action->id;
                        if($prev_cp_action) {
                            $this->createCpNextAction($prev_cp_action->id, $action->id);
                        }
                        $prev_cp_action = $action;
                        if(!$first_action) $first_action = $action;
                    } else {
                        if ($action_type === CpAction::TYPE_ANNOUNCE_DELIVERY) {
                            $action = $action_manager->createCpActions($cp_group->id, $action_type, CpAction::STATUS_FIX, $order + 1);
                        } else {
                            $action = $action_manager->createCpActions($cp_group->id, $action_type, CpAction::STATUS_DRAFT, $order + 1);
                        }
                        if($prev_cp_action) {
                            $this->createCpNextAction($prev_cp_action->id, $action[0]->id);
                        }
                        $prev_cp_action = $action[0];
                        if(!$first_action) $first_action = $action[0];
                    }
                }
                $prev_cp_action = null;
            }

            // 削除されたグループのdel_flg更新
            foreach($old_groups as $old_group) {
                if(!in_array($old_group->id, $group_id_list)) {
                    $this->deleteCpActionGroup($old_group);
                }
            }

            // 削除されたアクションのdel_flg更新
            foreach($old_cp_actions as $value) {
                foreach($value as $old_action) {
                    if(!in_array($old_action->id, $action_id_list)) {
                        $this->deleteCpAction($old_action);
                        $action_manager = $this->getManagerClassByType($old_action->type);
                        $action_manager->deleteConcreteAction($old_action);
                    }
                }
            }
            $this->cps->commit();

        } catch (Exception $e) {
            $this->logger->error("CpNewSkeletonCreator#create error" . $e);
            $this->cps->rollback();

        } finally {
            $this->logger->debug("CpNewSkeletonCreator#create success");
        }

        return $first_action;
    }


    public function getManagerClassByType($action_type) {
        switch ($action_type) {
            case CpAction::TYPE_ANNOUNCE   : return new CpAnnounceActionManager();
            case CpAction::TYPE_BUTTONS    : return new CpButtonsActionManager();
            case CpAction::TYPE_ENTRY      : return new CpEntryActionManager();
            case CpAction::TYPE_ENGAGEMENT    : return new CpEngagementActionManager();
            case CpAction::TYPE_FREE_ANSWER: return new CpFreeAnswerActionManager();
            case CpAction::TYPE_MESSAGE    : return new CpMessageActionManager();
            case CpAction::TYPE_QUESTIONNAIRE : return new CpQuestionnaireActionManager();
            case CpAction::TYPE_SHIPPING_ADDRESS : return new CpShippingAddressActionManager();
            case CpAction::TYPE_JOIN_FINISH : return new CpJoinFinishActionManager();
            case CpAction::TYPE_INSTANT_WIN : return new CpInstantWinActionManager();
            case CpAction::TYPE_PHOTO : return new CpPhotoActionManager();
            case CpAction::TYPE_COUPON : return new CpCouponActionManager();
            case CpAction::TYPE_MOVIE : return new CpMovieActionManager();
            case CpAction::TYPE_TWITTER_FOLLOW : return new CpTwitterFollowActionManager();
            case CpAction::TYPE_SHARE : return new CpShareActionManager();
            case CpAction::TYPE_FACEBOOK_LIKE : return new CpFacebookLikeActionManager();
            case CpAction::TYPE_GIFT : return new CpGiftActionManager();
            case CpAction::TYPE_INSTAGRAM_FOLLOW : return new CpInstagramFollowActionManager();
            case CpAction::TYPE_TWEET : return new CpTweetActionManager();
            case CpAction::TYPE_CODE_AUTHENTICATION : return new CpCodeAuthActionManager();
            case CpAction::TYPE_INSTAGRAM_HASHTAG : return new CpInstagramHashtagActionManager();
            case CpAction::TYPE_RETWEET : return new CpRetweetActionManager();
            case CpAction::TYPE_YOUTUBE_CHANNEL : return new CpYoutubeChannelActionManager();
            case CpAction::TYPE_POPULAR_VOTE : return new CpPopularVoteActionManager();
            case CpAction::TYPE_ANNOUNCE_DELIVERY: return new CpAnnounceDeliveryActionManager();
            case CpAction::TYPE_CONVERSION_TAG: return new CpConversionTagActionManager();
            case CpAction::TYPE_LINE_ADD_FRIEND: return new CpLineAddFriendActionManager();
            case CpAction::TYPE_PAYMENT: return new CpPaymentActionManager();
            default : return null;
        }
    }
}
