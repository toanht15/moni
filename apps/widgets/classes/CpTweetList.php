<?php
AAFW::import('jp.aainc.widgets.base.AdminCpListBase');

class CpTweetList extends AdminCpListBase {
    const PAGE_LIMITED = 20;

    public function doSubService($params = array()) {
        $params['page_limited'] = self::PAGE_LIMITED;

        $order = $this->getUserDataOrder($params);
        $search_params = $this->getSearchParams($params);

        $cp_flow_service = $this->getService('CpFlowService');
        $params['tweet_actions'] = $cp_flow_service->getCpActionsByCpIdAndActionType($params['cp_id'], $this->getCurCpActionType());

        $cp_tweet_action_service = $this->getService('CpTweetActionService');
        $params['cur_tweet_action'] = $cp_tweet_action_service->getCpTweetAction($params['action_id']);

        $tweet_service = $this->getService('TweetMessageService');
        $params['total_tweet_count'] = $tweet_service->countTweetPostsByActionIds($params['cur_tweet_action']->id, $search_params);

        $total_page = floor($params['total_tweet_count'] / self::PAGE_LIMITED) + ($params['total_tweet_count'] % self::PAGE_LIMITED > 0);
        $params['page'] = Util::getCorrectPaging($params['page'], $total_page);

        $params['tweet_messages'] = $tweet_service->getTweetMessages($params['cur_tweet_action']->id, $params['page'], self::PAGE_LIMITED, $order, $search_params);
        $params['approved_tweet_count'] = $tweet_service->countTweetPostsByActionIds($params['cur_tweet_action']->id, array('approval_status' => TweetMessage::APPROVAL_STATUS_APPROVE));

        return $params;
    }

    public function getSearchParams($params) {
        $search_params = array();

        /**
         * Saving data as tweet status instead of account status because showing data is the tweet not user account
         * Checkbox value {'1' => 'アカウント公開', '2' => 'アカウント非公開', '3' => 'ツイート削除済'}
         * Tweet status value {'0' => 'ツイート公開', '1' => 'ツイート非公開', '2' => 'ツイート削除済'}
         */
        if ($params['tweet_status']) {
            foreach ($params['tweet_status'] as $tweet_status) {
                $search_params['tweet_status'][] = $tweet_status - 1;
            }
        }

        /**
         * Execute after tweet_status
         * Checkbox value {'1' => '出力', '2' => '非出力'}
         * Approval value {'1' => '非出力', '2' => '出力'}
         */
        if ($params['approval_status'] && (is_array($search_params['tweet_status']) && in_array(TweetMessage::TWEET_STATUS_PUBLIC, $search_params['tweet_status']))) {
            $search_params['approval_status'] = $params['approval_status'];
        }

        return $search_params;
    }

    public function getCurCpActionType() {
        return CpAction::TYPE_TWEET;
    }
}