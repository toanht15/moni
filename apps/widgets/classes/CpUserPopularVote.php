<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class CpUserPopularVote extends aafwWidgetBase {

    public function doService($params = array()) {
        /** @var PopularVoteUserService $cp_popular_vote_user_service */
        $params['popular_vote_user_service'] = $this->getService('PopularVoteUserService');
        /** @var CpPopularVoteActionService $cp_popular_vote_action_service */
        $cp_popular_vote_action_service = $this->getService('CpPopularVoteActionService');

        $params['cp_popular_vote_action'] = $cp_popular_vote_action_service->getCpPopularVoteActionByCpActionId($params['display_action_id']);

        $service_factory = new aafwServiceFactory();
        /** @var $brand_user_relation_service BrandsUsersRelationService */
        $params['brand_user_relation_service'] = $service_factory->create('BrandsUsersRelationService');

        return $params;
    }
}
