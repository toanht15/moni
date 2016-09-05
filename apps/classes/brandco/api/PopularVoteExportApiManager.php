<?php
AAFW::import('jp.aainc.classes.brandco.api.base.ContentExportApiManagerBase');
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');

class PopularVoteExportApiManager extends ContentExportApiManagerBase {

    public function doSubProgress() {

        $db = new aafwDataBuilder();

        $param = array(
            'code' => $this->code,
            'cp_action_type' => CpAction::TYPE_POPULAR_VOTE
        );

        $result = $db->getPopularVoteSummaryByContentApiCodes($param, array(), array(), true);
        $popular_votes = $result['list'];

        if (!$popular_votes) {
            $json_data = $this->createResponseData('ng', array(), array('message' => '人気投票が存在しません'));
            return $json_data;
        }

        $response_data = $this->getApiExportData($popular_votes);
        $json_data = $this->createResponseData('ok', $response_data, array(), array());
        return $json_data;
    }

    /**
     * @param $export_data
     * @param null $brand
     * @return array
     */
    public function getApiExportData($export_data, $brand = null) {
        $data               = array();
        $popular_votes      = array();
        $popular_candidates = array();
        foreach ($export_data as $element) {
            if (empty($popular_votes)) {
                $popular_votes[] = array(
                    'id'        => $element['popular_vote_id'],
                    'title'     => $element['popular_vote_title'],
                    'image_url' => $element['popular_vote_image_url']
                );
            }
            $popular_candidates[] = array(
                'id'                => $element['popular_candidate_id'],
                'title'             => $element['popular_candidate_title'],
                'description'       => $element['popular_candidate_description'],
                'thumbnail_url'     => $element['popular_candidate_thumbnail_url'],
                'original_url'      => $element['popular_candidate_original_url'],
                'order_no'          => $element['popular_candidate_order_no'],
                'vote_num'          => $element['popular_vote_num']
            );
        }
        $data['popular_vote']       = $popular_votes;
        $data['popular_candidates'] = $popular_candidates;

        return $data;
    }
}