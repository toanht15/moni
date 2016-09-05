<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class MultiPostSnsQueueService extends aafwServiceBase {

    const MAX_READ_COUNT = 10000;

    /** @var MultiPostSnsQueues $multi_post_sns_queues */
    protected $multi_post_sns_queues;

    public function __construct() {
        $this->multi_post_sns_queues = $this->getModel('MultiPostSnsQueues');
    }

    /**
     * @param $callback_function_type
     * @param $callback_parameters
     * @return array
     * @throws Exception
     */
    public function getMultiPostSnsQueueByCallback($callback_function_type, $callback_parameters) {
        if($this->isEmpty($callback_parameters)) {
            return array();
        }

        $conditions = array(
            'callback_function_type' => $callback_function_type,
            'callback_parameters' => $callback_parameters
        );

        $db = aafwDataBuilder::newBuilder();
        return $db->getMultiPostSnsQueueByCallback($conditions);
    }

    public function getErrorMultiPostSnsQueues() {
        $filter = array(
            'error_flg' => 1
        );
        return $this->multi_post_sns_queues->find($filter);
    }

    public function getMultiPostSnsQueues() {
        $filter = array(
            'pager' => array(
                'count' => self::MAX_READ_COUNT,
            ),
            'error_flg' => 0
        );
        return $this->multi_post_sns_queues->find($filter);
    }

    public function getMultiPostSnsQueueByIdForUpdate($multi_post_sns_queue_id) {
        $filter = array(
            'id' => $multi_post_sns_queue_id,
            'for_update' => true
        );
        return $this->multi_post_sns_queues->find($filter);
    }

    public function createEmptyObject() {
        return $this->multi_post_sns_queues->createEmptyObject();
    }

    public function update($multi_post_sns_queue) {
        $this->multi_post_sns_queues->save($multi_post_sns_queue);
    }

    public function deletePhysical($multi_post_sns_queue) {
        $this->multi_post_sns_queues->deletePhysical($multi_post_sns_queue);
    }

    public function deleteLogical($multi_post_sns_queue) {
        $this->multi_post_sns_queues->deleteLogical($multi_post_sns_queue);
    }

    /**
     * @param $api_result
     * @param $social_media_type
     * @return string
     */
    public function getShareUrl($api_result, $social_media_type) {
        $share_url = '';

        $api_result = json_decode($api_result, true);
        if (Util::isNullOrEmpty($api_result)) {
            return $share_url;
        }

        if ($social_media_type == SocialAccount::SOCIAL_MEDIA_FACEBOOK) {
            if (Util::isNullOrEmpty($api_result['id'])) {
                return $share_url;
            }

            $share_url = SocialAccountService::getSnsUrl($social_media_type) . $api_result['id'];
        } elseif ($social_media_type == SocialAccount::SOCIAL_MEDIA_TWITTER) {
            if (Util::isNullOrEmpty($api_result['id_str']) || Util::isNullOrEmpty($api_result['user']['screen_name'])) {
                return $share_url;
            }

            $share_url = SocialAccountService::getSnsUrl($social_media_type) . $api_result['user']['screen_name'] . '/status/' . $api_result['id_str'];
        }

        return $share_url;
    }
}
