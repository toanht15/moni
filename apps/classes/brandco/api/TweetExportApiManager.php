<?php
AAFW::import('jp.aainc.classes.brandco.api.base.ContentExportApiManagerBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class TweetExportApiManager extends ContentExportApiManagerBase {

    private $cur_action_ids;

    public function __construct($init_data) {
        parent::__construct($init_data);

        $action_ids = $init_data['action_ids'] ? urldecode($init_data['action_ids']) : null;
        $this->cur_action_ids = $action_ids != null ? explode(",", $action_ids) : null;
    }

    public function doSubProgress() {

        $db = new aafwDataBuilder();

        $param = array(
            'code' => $this->code,
            'max_id' => $this->max_id ? $this->max_id : null,
            'cp_action_type' => CpAction::TYPE_TWEET
        );

        if (count($this->cur_action_ids)) {
            $param['SEARCH_BY_ACTION_IDS'] = "__ON__";
            $param['cur_action_id'] = $this->cur_action_ids;
        }

        $pager = array(
            'page' => self::DEFAULT_PAGE,
            'count' => $this->limit + 1     // $tweet_message_count = $page_limit + $next_min_user
        );

        $order = array(
            'name' => 'id',
            'direction' => 'desc'
        );

        $result = $db->getTweetPostsByContentApiCodes($param, $order, $pager, true);
        $tweet_messages = $result['list'];

        if (!$tweet_messages) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'ツイート投稿が存在しません'));
            return $json_data;
        }

        $api_code_service = $this->service_factory->create('ContentApiCodeService');

        // API Pagination
        $pagination = array();
        if ($result['pager']['count'] >= $this->limit + 1) {
            // If next_min_user is available pop it from tweet_messages list
            $last_tweet_message = array_pop($tweet_messages);

            $pagination = array(
                'next_id' => $last_tweet_message['id'],
                'next_url'    => $api_code_service->getApiUrl($this->code, CpAction::TYPE_TWEET, $last_tweet_message['id'], $this->limit, $this->cur_action_ids)
            );
        }

        $response_data = $this->getApiExportData($tweet_messages);
        $json_data = $this->createResponseData('ok', $response_data, array(), $pagination);
        return $json_data;
    }

    /**
     * @param $export_data
     * @param null $brand
     * @return array
     */
    public function getApiExportData($export_data, $brand = null) {
        $data = array();
        $last_tweet_messages = null;

        $builder = aafwDataBuilder::newBuilder();
        $retweet_msg_service = $this->service_factory->create('CpRetweetMessageService');

        foreach ($export_data as $tweet_message) {
            $cur_media = array();

            if ($tweet_message['has_photo']) {
                $query = "/* get tweet photos by tweet_message_id */
                    SELECT tp.* FROM tweet_photos tp WHERE tp.tweet_message_id = ?tweet_message_id? AND tp.del_flg = 0";
                $results = $builder->getBySQL($query, array(array('tweet_message_id' => $tweet_message['id'])));

                foreach ($results as $result) {
                    $cur_media[] = array(
                        'id' => $result['id'],
                        'media_url' => $result['image_url']
                    );
                }
            }

            $data[] = array(
                'id' => $tweet_message['id'],
                'message' => $tweet_message['tweet_text'] . ($tweet_message['tweet_fixed_text'] ? "\r\n" . $tweet_message['tweet_fixed_text'] : ""),
                'tweet_id' => $retweet_msg_service->getTweetIdByTweetUrl($tweet_message['tweet_content_url']),
                'tweet_url' => $tweet_message['tweet_content_url'],
                'created_at' => $tweet_message['created_at'],
                'media' => $cur_media,
                'twitter_user' => array(
                    'twitter_account_id' => $tweet_message['twitter_account_id'],
                    'profile_page_url' => $tweet_message['profile_page_url'],
                    'screen_name' => $tweet_message['screen_name']
                )
            );
        }

        return $data;
    }
}