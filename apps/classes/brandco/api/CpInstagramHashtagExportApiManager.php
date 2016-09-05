<?php
AAFW::import('jp.aainc.classes.brandco.api.base.ContentExportApiManagerBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class CpInstagramHashtagExportApiManager extends ContentExportApiManagerBase {

    public function doSubProgress() {

        $db = new aafwDataBuilder();

        $param = array(
            'codes' => array($this->code),
            'max_id' => $this->max_id ? $this->max_id : null,
            'cp_action_type' => CpAction::TYPE_INSTAGRAM_HASHTAG,
            'BY_MAX_ID' => '__ON__',
        );

        $pager = array(
            'page' => self::DEFAULT_PAGE,
            'count' => $this->limit + 1     // $instagram_hashtag_user_posts_count = $page_limit + $next_min_user
        );

        $order = array(
            'name' => 'id',
            'direction' => 'desc'
        );

        $result = $db->getCpInstagramHashtagUserPostsByContentApiCodes($param, $order, $pager, true, 'InstagramHashtagUserPost');
        $instagram_hashtag_user_posts = $result['list'];

        if (!$instagram_hashtag_user_posts) {
            $json_data = $this->createResponseData('ng', array(), array('message' => 'InstagramHashtag投稿が存在しません'));
            return $json_data;
        }

        /** @var ContentApiCodeService $api_code_service */
        $api_code_service = $this->service_factory->create('ContentApiCodeService');

        // API Pagination
        $pagination = array();
        if ($result['pager']['count'] >= $this->limit + 1) {
            // If next_min_user is available pop it from photo_users list
            $last_instagram_hashtag_user_post = array_pop($instagram_hashtag_user_posts);

            $pagination = array(
                'next_id' => $last_instagram_hashtag_user_post->id,
                'next_url'    => $api_code_service->getApiUrl($this->code, CpAction::TYPE_INSTAGRAM_HASHTAG, $last_instagram_hashtag_user_post->id, $this->limit)
            );
        }

        $response_data = $this->getApiExportData($instagram_hashtag_user_posts);
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
        $last_photo_user = null;

        foreach ($export_data as $instagram_hashtag_user_post) {
            $cur_data = json_decode($instagram_hashtag_user_post->detail_data);
            $cur_data->post_id = $instagram_hashtag_user_post->id;
            $data[] = $cur_data;
        }

        return $data;
    }
}