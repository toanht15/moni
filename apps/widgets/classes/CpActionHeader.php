<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpActionHeader extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var $serviceFactory aafwServiceFactory */
        $serviceFactory = new aafwServiceFactory();

        $cp_list_service = $serviceFactory->create('CpListService');

        $params['cp_status'] = $params['cp']->getStatus();

        $params['post_actions'] = array();
        $params['announce_actions'] = array();
        $params['popular_vote_actions'] = array();
        $params['first_photo_action'] = null;
        $params['first_instagram_hashtag_action'] = null;
        $params['first_tweet_action'] = null;
        $params['first_questionnaire_action'] = null;
        
        $action_no = 0;
        $announce_group = array();
        if(!$params['group_array']) {
            $cp_ids[] = $params['cp']->id;
            $cps = $cp_list_service->getListPublicCp($cp_ids);
            $params['group_array'] = $cps[$params['cp']->id];
        }
        foreach($params['group_array'] as $group) {
            $first_action_id_in_group = 0;

            foreach($group as $action_id => $action) {
                if(!is_array($action)) {
                    continue;
                }
                $action_no++;
                if($action_no == 1) {
                    $params['first_action_id'] = $action_id;
                }

                if ($action['action_order_no'] == 1) {
                    $first_action_id_in_group = $action_id;
                }

                if($action['type'] == CpAction::TYPE_PHOTO) {
                    if($params['first_photo_action']) {
                        continue;
                    }
                    $params['post_actions'][$action_id]['type'] = $action['type'];
                    $params['first_photo_action'] = $action_id;
                }

                if($action['type'] == CpAction::TYPE_INSTAGRAM_HASHTAG) {
                    if($params['first_instagram_hashtag_action']) {
                        continue;
                    }
                    $params['post_actions'][$action_id]['type'] = $action['type'];
                    $params['first_instagram_hashtag_action'] = $action_id;
                }

                if ($action['type'] == CpAction::TYPE_QUESTIONNAIRE && $params['pageStatus']['manager']->id) {
                    if ($params['first_questionnaire_action']) {
                        continue;
                    }

                    $params['first_questionnaire_action'] = $action_id;
                }

                if($action['type'] == CpAction::TYPE_ANNOUNCE || $action['type'] == CpAction::TYPE_ANNOUNCE_DELIVERY) {
                    if(in_array($group['group_order_no'], $announce_group)) {
                        continue;
                    }
                    $params['announce_actions'][$action_id]['type'] = $action['type'];
                    $params['announce_actions'][$action_id]['order_no'] = $action_no;
                    $params['announce_actions'][$action_id]['first_action_id_in_group'] = $first_action_id_in_group;
                    $announce_group[] = $group['group_order_no'];
                }

                // ツイート管理がクライアントも利用できるハードコーディング（SUBWAY-14）
                if($action['type'] == CpAction::TYPE_TWEET) {
                    if($params['first_tweet_action'] || $params['subway_tweet_action']) {
                        continue;
                    }
                    if ($params['pageStatus']['manager']->id) {
                        $params['first_tweet_action'] = $action_id;
                    } elseif ($params['cp']->brand_id == 496) {
                        $params['subway_tweet_action'] = $action_id;
                    }
                }

                if ($action['type'] == CpAction::TYPE_POPULAR_VOTE && $params['pageStatus']['manager']->id) {
                    if ($params['first_popular_vote_action']) {
                        continue;
                    }
                    $params['first_popular_vote_action']        = $action_id;
                }

                // 候補一覧ページ、写真ダウンロード、Instagram投稿ダウンロードは参加者一覧のみ導線設置
                if(!$params['user_list_page']) {
                    continue;
                }
                if($action['type'] == CpAction::TYPE_POPULAR_VOTE) {
                    $params['popular_vote_actions'][$action_id]['type'] = $action['type'];
                    $params['popular_vote_actions'][$action_id]['order_no'] = $action_no;
                }
            }
        }

        if (!$params['cp']->isPermanent()) {
            if ($params['cp']->start_date == '0000-00-00 00:00:00' && $params['cp']->end_date == '0000-00-00 00:00:00') {
                $params['cp_entry_term'] .= '-月-日 〜 -月-日';
            } else {
                $params['cp_entry_term'] .= DateTime::createFromFormat('Y-m-d H:i:s', $params['cp']->start_date)->format(
                    Util::isPresentYear($params['cp']->start_date) ? 'm月d日 H:i' : 'Y年m月d日 H:i'
                );
                $params['cp_entry_term'] .= " 〜 ";
                $params['cp_entry_term'] .= DateTime::createFromFormat('Y-m-d H:i:s', $params['cp']->end_date)->format(
                    (date("Y", strtotime($params['cp']->start_date)) === date("Y", strtotime($params['cp']->end_date))) ? 'm月d日 H:i' : 'Y年m月d日 H:i'
                );
            }

            if ($params['cp']->announce_date == '0000-00-00 00:00:00') {
                $params['cp_announce_date'] = '-月-日';
            } else {
                $params['cp_announce_date'] = DateTime::createFromFormat('Y-m-d H:i:s', $params['cp']->announce_date)->format(
                    Util::isPresentYear($params['cp']->announce_date) ? 'm月d日' : 'Y年m月d日'
                );
            }

            $params['cp_winner_count'] = $params['cp']->winner_count ? $params['cp']->winner_count : '-';
            if ($params['user_list_page']) {
                $params['get_winner_info'] = $this->getWinnerInfo($params['cp'], $params['action_id']);
            }
        } else {
            if ($params['cp']->start_date == '0000-00-00 00:00:00') {
                $params['cp_entry_term'] .= '-月-日';
            } else {
                $params['cp_entry_term'] .= DateTime::createFromFormat('Y-m-d H:i:s', $params['cp']->start_date)->format(
                    Util::isPresentYear($params['cp']->start_date) ? 'm月d日 H:i' : 'Y年m月d日 H:i'
                );
            }

            $params['cp_entry_term'] .= ' 〜';
        }

        $params['should_announce'] = $this->shouldAnnounce($params['cp']);

        return $params;
    }

    /**
     * 当選発表すべきかどうか
     * @return bool
     */
    public function shouldAnnounce($cp) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');

        return !$cp->isNonIncentiveCp() && $cp->isFixed() && $cp->isOverAnnounceDate() && !$cp_flow_service->isAnnounced($cp);
    }
}
