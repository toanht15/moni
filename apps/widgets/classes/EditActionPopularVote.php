<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionPopularVote extends aafwWidgetBase{

    public function doService( $params = array() ){

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        /** @var CpPopularVoteActionService $cp_popular_vote_action_service */
        $cp_popular_vote_action_service = $this->getService('CpPopularVoteActionService');

        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);

        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }

        $params['file_type'] = $this->ActionForm['file_type'] ? : '1';
        $params['current_file_type'] = $this->ActionForm['current_file_type'] ? : $params['file_type'];
        $params['is_cp_action_fixed'] = $cp_flow_service->isCpActionFixed($params['action']);

        $params['candidate_list'] = array();
        $candidates = $cp_popular_vote_action_service->getCpPopularVoteCandidateByCpPopularVoteActionId($this->ActionForm['id']);
        foreach ($candidates as $candidate) {
            $key = $candidate->order_no - 1;
            $params['candidate_list'][$key] = array();
            $params['candidate_list'][$key]['id'] = $candidate->id;
            $params['candidate_list'][$key]['title'] = $candidate->title;
            $params['candidate_list'][$key]['description'] = $candidate->description;
            $params['candidate_list'][$key]['thumbnail_url'] = $candidate->thumbnail_url;
            $params['candidate_list'][$key]['original_url'] = $candidate->original_url;
            if ($params['file_type'] == CpPopularVoteAction::FILE_TYPE_MOVIE) {
                $params['candidate_list'][$key]['original_url'] .= YoutubeStream::EMBED_URL_SUFFIX;
                $params['candidate_list'][$key]['movie'] = substr($candidate->original_url, strlen(YoutubeStream::EMBED_URL_PREFIX));
            }
        }

        if ($this->ActionForm['popular_vote_post_flg']) {
            $params['candidate_list'] = ($params['is_cp_action_fixed'] && $cp->status != Cp::STATUS_DRAFT) ? array() : $params['candidate_list'];
            foreach ($this->ActionForm['candidate_id'] as $key => $value) {
                if ($params['is_cp_action_fixed'] && $cp->status != Cp::STATUS_DRAFT) {
                    $params['candidate_list'][$key]['id'] = $this->ActionForm['candidate_id'][$key];
                }
                $params['candidate_list'][$key]['title'] = $this->ActionForm['candidate_title'][$key];
                $params['candidate_list'][$key]['description'] = $this->ActionForm['candidate_description'][$key];
                $params['candidate_list'][$key]['movie'] = $this->ActionForm['candidate_movie'][$key];

                if ($this->ActionError && (!$this->ActionError->isValid('candidate_image_'. $key) || !$this->ActionError->isValid('candidate_movie_'. $key))) {
                    $params['candidate_list'][$key]['thumbnail_url'] = '';
                    $params['candidate_list'][$key]['original_url'] = '';
                } else if ($params['is_cp_action_fixed'] && $cp->status != Cp::STATUS_DRAFT) {
                    $params['candidate_list'][$key]['thumbnail_url'] = $this->ActionForm['candidate_thumbnail_url'][$key];
                    $params['candidate_list'][$key]['original_url'] = $this->ActionForm['candidate_original_url'][$key];
                }
            }
        }

        $params['share_url_type'] = $this->ActionForm['share_url_type'] ? : '1';
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        return $params;
    }
}
