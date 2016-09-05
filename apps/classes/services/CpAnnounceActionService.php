<?php
AAFW::import('jp.aainc.aafw.db.aafwDataBuilder');
AAFW::import('jp.aainc.classes.services.CpCreateSqlService');

class CpAnnounceActionService extends aafwServiceBase {

    /**
     * CPACTIONIDから当選者の一覧(Collection)を取得
     * @param $cp_action_id
     * @return $mixed
     */
    public function getAnnouncedUserByCpActionId($cp_action_id) {
        $service_factory = new aafwServiceFactory ();
        /** @var CpUserListService $cp_user_list_service */
        $cp_user_list_service = $service_factory->create('CpUserListService');

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $cp_action = $cp_flow_service->getCpActionById($cp_action_id);
        if($cp_action->type != CpAction::TYPE_ANNOUNCE && $cp_action->type != CpAction::TYPE_ANNOUNCE_DELIVERY) {
            return false;
        }
        $cp = $cp_flow_service->getCpByCpAction($cp_action);

        //当選発表アクションの当選済み状態を作る
        $electedCondition = array(
            CpCreateSqlService::SEARCH_PARTICIPATE_CONDITION.'/'.$cp_action->id =>
                array(
                    'search_participate_condition/'.$cp_action->id.'/'.CpCreateSqlService::PARTICIPATE_COMPLETE    => 1,
                    'search_participate_condition/'.$cp_action->id.'/'.CpCreateSqlService::PARTICIPATE_READ        => 1,
                    'search_participate_condition/'.$cp_action->id.'/'.CpCreateSqlService::PARTICIPATE_NOT_READ    => 1
                )
        );

        $page_info = array(
            'cp_id'     => $cp->id,
            'brand_id'  => $cp->brand_id
        );
        $fan_list_users = $cp_user_list_service->getAllFanList($page_info, $electedCondition);
        return $fan_list_users;
    }
}