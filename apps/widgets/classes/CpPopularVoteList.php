<?php
AAFW::import('jp.aainc.widgets.base.AdminCpListBase');

class CpPopularVoteList extends AdminCpListBase {

    public function doSubService($params = array()) {

        $cp_flow_service = $this->getService('CpFlowService');
        $params['popular_vote_actions'] = $cp_flow_service->getCpActionsByCpIdAndActionType($params['cp_id'], $this->getCurCpActionType());

        $api_code_service = $this->getService('ContentApiCodeService');
        $api_code = $api_code_service->getApiCodeByCpActionId($params['action_id']);
        $params['api_url'] = $api_code ? $api_code_service->getApiUrl($api_code->code, $this->getCurCpActionType()) : '';

        return $params;
    }

    public function getSearchParams($params) {
    }

    public function getCurCpActionType() {
        return CpAction::TYPE_POPULAR_VOTE;
    }
}