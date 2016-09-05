<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class MsgCodeAuthCodeList extends aafwWidgetBase {

    public function doService($params = array()) {
        $service_factory = new aafwServiceFactory();
        $cp_action_manager = $service_factory->create('CpCodeAuthActionManager');
        $cp_user_service = $service_factory->create('CpUserService');

        $params['concrete_action'] = $cp_action_manager->getCpConcreteActionByCpActionId($params['cp_action_id']);
        $params['code_auth_users'] = $cp_action_manager->getCodeAuthUsersByUserIdAndCpActionId($params['user_id'], $params['cp_action_id']);

        $action_status = $cp_user_service->getCpUserActionStatus($params['cp_user_id'], $params['cp_action_id']);
        $params['is_not_join'] = $action_status->status == CpUserActionStatus::NOT_JOIN;
        $params['can_enter_code'] = ($params['concrete_action']->max_code_flg == CpCodeAuthenticationAction::CODE_FLG_OFF
            || ($params['code_auth_users'] && $params['code_auth_users']->total() < $params['concrete_action']->max_code_count)) ? true : false;
        $params['is_action_clear'] = ($params['concrete_action']->min_code_flg == CpCodeAuthenticationAction::CODE_FLG_OFF)
            || ($params['code_auth_users'] && $params['code_auth_users']->total() >= $params['concrete_action']->min_code_count) ? true : false;

        $tracking_service = $service_factory->create('CodeAuthUserTrackingService');
        $track_log = $tracking_service->getTrackingUserByUserAnCpActionId($params['user_id'], $params['cp_action_id']);
        $params['is_locking_user'] = $tracking_service->isLockingUser($track_log);

        $params['code_auth_user_count'] = $params['code_auth_users'] ? $params['code_auth_users']->total() : 0;
        $params['code_input_disabled'] =  ($params['concrete_action']->max_code_flg == CpCodeAuthenticationAction::CODE_FLG_ON
            &&  $params['code_auth_user_count'] >= $params['concrete_action']->max_code_count) || $params['is_locking_user'] ? 'disabled' : '';
        $params['remain_code_count'] = $params['concrete_action']->min_code_count > $params['code_auth_user_count'] ? $params['concrete_action']->min_code_count - $params['code_auth_user_count'] : 0;

        return $params;
    }
}