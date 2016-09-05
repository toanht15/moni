<?php
AAFW::import('jp.aainc.classes.brandco.toolkit.BrandcoGETActionBase');
AAFW::import('jp.aainc.classes.CpLPInfoContainer');
AAFW::import('jp.aainc.classes.BrandInfoContainer');

class ranking extends BrandcoGETActionBase {
    protected $ContainerName = 'candidate';
    public $NeedOption = array();

    private $ids;
    private $cp;
    private $cp_action;
    private $cp_concrete_action;

    public function doThisFirst() {
        $this->ids = array(
            'cp_action_id'     => ($this->GET['exts'][0]) ? : -1,
            'cp_popular_vote_candidate_id'  => ($this->GET['exts'][1]) ? : -1,
        );
    }

    public function validate() {
        $popular_vote_cp_validator = new PopularVoteCpValidator($this->getBrand()->id, $this->ids['cp_action_id']);
        if (!$popular_vote_cp_validator->validate()) {
            return '404';
        } else if (
            $this->ids['cp_popular_vote_candidate_id'] != -1 &&
            !$popular_vote_cp_validator->isValidCandidate($this->ids['cp_popular_vote_candidate_id'])) {
            return '404';
        }

        $this->cp                   = $popular_vote_cp_validator->getCp();
        $this->cp_action            = $popular_vote_cp_validator->getCpAction();
        $this->cp_concrete_action   = $popular_vote_cp_validator->getCpConcreteAction();

        if ($this->ids['cp_popular_vote_candidate_id'] == -1) {
            if ($this->isLoginAdmin()) {
                $this->cp_concrete_action->show_ranking_flg = CpPopularVoteAction::RANKING_FLG;
            }
        }

        return true;
    }

    public function doAction() {
        $this->Data['cp_popular_vote_action'] = $this->cp_concrete_action;
        $this->Data['cp_popular_vote_candidates'] = $this->setCandidates($this->Data['cp_popular_vote_action']->id);
        $this->Data['cp_image_url'] = $this->getCpImageUrl();

        $ref_url = $this->cp->getReferenceUrl(false, $this->getBrand());
        $sep = parse_url($ref_url, PHP_URL_QUERY) != '' ? '&' : '?';
        $this->Data['cp_url'] = $ref_url . $sep . 'fid=vtr';

        $this->setRankingClass();

        // OGPの設定
        if ($candidate = $this->isContainedCandidate($this->ids['cp_popular_vote_candidate_id'])) {
            $this->Data['pageStatus']['og'] = array(
                'title'         => '「' . $candidate['title'] . '」に投票しました！',
                'description'   => $this->cp->getTitle() . ' / ' . $this->getBrand()->name,
                'image'         => $candidate['thumbnail_url'],
                'url'           => Util::rewriteUrl('popular_vote', 'ranking', $this->ids)
            );

            $this->Data['candidate'] = $candidate;
        } else {
            $this->Data['pageStatus']['og']['url'] = $this->Data['cp_url'];
        }

        // ピンチを許可
//        $this->Data['pageStatus']['canPinch'] = true;

        return 'user/brandco/popular_vote/ranking.php';
    }

    public function getCpImageUrl() {
        $container = new CpLPInfoContainer();
        $lp_info = $container->getCpLPInfo($this->cp, BrandInfoContainer::getInstance()->getBrand());
        $action_info = $lp_info[CpLPInfoContainer::KEY_ACTION_INFO];

        return $action_info['concrete_action']['image_url'];
    }

    public function setCandidates($cp_popular_vote_action_id) {
        /** @var CpPopularVoteActionService $cp_popular_vote_action_service */
        $cp_popular_vote_action_service = $this->getService('CpPopularVoteActionService');
        /** @var PopularVoteUserService $popular_vote_user_service */
        $popular_vote_user_service      = $this->getService('PopularVoteUserService');

        $n_votes_array = array();
        $cp_popular_vote_candidates = $cp_popular_vote_action_service->getCpPopularVoteCandidateByCpPopularVoteActionId($cp_popular_vote_action_id);
        foreach ($cp_popular_vote_candidates as $cp_popular_vote_candidate) {
            $candidate = array(
                'id'            => $cp_popular_vote_candidate->id,
                'title'         => $cp_popular_vote_candidate->title,
                'description'   => $cp_popular_vote_candidate->description,
                'thumbnail_url' => $cp_popular_vote_candidate->thumbnail_url,
                'original_url'  => $cp_popular_vote_candidate->original_url,
                'n_votes'       => $popular_vote_user_service->countPopularVoteUserByCpPopularVoteCandidateId($cp_popular_vote_candidate->id),
                'class_name'    => ''
            );

            $this->Data['candidates'][] = $candidate;
            $n_votes_array[] = $candidate['n_votes'];
        }

        if ($this->Data['cp_popular_vote_action']->show_ranking_flg == CpPopularVoteAction::RANKING_FLG) {
            array_multisort($n_votes_array, SORT_DESC, $this->Data['candidates']);
        } else if ($this->Data['cp_popular_vote_action']->random_flg == CpPopularVoteAction::RANDOM_FLG) {
            shuffle($this->Data['candidates']);
        }
    }

    public function setRankingClass() {
        $n_votes = 0;
        $class_list = array('rank1st', 'rank2nd', 'rank3rd');

        if ($this->Data['cp_popular_vote_action']->show_ranking_flg == CpPopularVoteAction::RANKING_FLG) {
            foreach ($this->Data['candidates'] as $key => $candidate) {
                if ($candidate['n_votes'] != 0) {
                    if ($candidate['n_votes'] < $n_votes || $n_votes === 0) {
                        $this->Data['candidates'][$key]['class_name'] = (count($class_list)) ? array_shift($class_list) : '';
                    } else if ($candidate['n_votes'] == $n_votes) {
                        $this->Data['candidates'][$key]['class_name'] = $this->Data['candidates'][$key - 1]['class_name'];
                    }
                } else {
                    $this->Data['candidates'][$key]['class_name'] = '';
                }

                $n_votes = $candidate['n_votes'];
            }
        }
    }

    public function isContainedCandidate($cp_popular_vote_candidate_id) {
        foreach ($this->Data['candidates'] as $candidate)  {
            if ($candidate['id'] == $cp_popular_vote_candidate_id) {
                return $candidate;
            }
        }

        return false;
    }
}
