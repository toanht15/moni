<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class ContentApiCodeService extends aafwServiceBase {

    const DEFAULT_LIMIT = 20;

    private $end_point_urls = array(
        CpAction::TYPE_PHOTO                => 'photos',
        CpAction::TYPE_INSTAGRAM_HASHTAG    => 'cp_instagram_hashtags',
        CpAction::TYPE_TWEET                => 'tweet_posts',
        CpAction::TYPE_QUESTIONNAIRE        => 'questionnaire_answers',
        CpAction::TYPE_POPULAR_VOTE         => 'popular_votes'
    );

    private $api_codes;

    public function __construct() {
        $this->api_codes = $this->getModel('ContentApiCodes');
    }

    /**
     * @return mixed
     */
    public function createEmptyApiCode() {
        return $this->api_codes->createEmptyObject();
    }

    /**
     * @param $api_code
     */
    public function createApiCode($api_code) {
        $this->api_codes->save($api_code);
    }

    /**
     * @param $api_code
     */
    public function updateApiCode($api_code) {
        $this->api_codes->save($api_code);
    }

    /**
     * @param $cp_id
     * @param $cp_action_type
     * @return mixed
     */
    public function getApiCodeByCpIdAndCpActionType($cp_id, $cp_action_type) {
        $filter = array(
            'cp_id' => $cp_id,
            'cp_action_type' => $cp_action_type
        );

        return $this->api_codes->findOne($filter);
    }

    /**
     * @param $cp_action_id
     * @return mixed
     */
    public function getApiCodeByCpActionId($cp_action_id) {
        $filter = array(
            'cp_action_id' => $cp_action_id
        );

        return $this->api_codes->findOne($filter);
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getApiCodeByCode($code) {
        $filter = array(
            'code' => $code
        );

        return $this->api_codes->findOne($filter);
    }

    /**
     * @param $prefix
     * @return string
     */
    public function generateCode($prefix) {
        return md5(uniqid($prefix, true));
    }

    /**
     * @param $code
     * @param $api_type
     * @param int $max_id
     * @param int $limit
     * @param array $action_ids
     * @return string
     */
    public function getApiUrl($code, $api_type, $max_id = 0, $limit = self::DEFAULT_LIMIT, $action_ids = array()) {
        $query_params = $this->getUrlQueryParams($code, $max_id, $limit, $action_ids);
        $end_point_url = $this->end_point_urls[$api_type] . '.json';

        return Util::rewriteUrl('api', $end_point_url, array(), $query_params);
    }

    /**
     * @param $code
     * @param int $max_id
     * @param int $limit
     * @param array $action_ids
     * @return array
     */
    public function getUrlQueryParams($code, $max_id = 0, $limit = self::DEFAULT_LIMIT, $action_ids = array()) {
        $query_params = array(
            'code' => $code
        );

        if ($max_id != 0) {
            $query_params['next_id'] = $max_id;
        }

        if ($limit != self::DEFAULT_LIMIT) {
            $query_params['limit'] = $limit;
        }

        if (count($action_ids)) {
            $query_params['action_ids'] = urlencode(implode(",", $action_ids));
        }

        return $query_params;
    }
}