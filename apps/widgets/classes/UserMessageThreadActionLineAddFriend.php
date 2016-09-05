<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class UserMessageThreadActionLineAddFriend extends aafwWidgetBase{

    public function doService( $params = array() ){

        /** @var CpLineAddFriendActionLogService $cp_line_add_friend_log_service */
        $cp_line_add_friend_log_service = $this->getService('CpLineAddFriendActionLogService');

        $cp_line_add_friend_log = $cp_line_add_friend_log_service->findLogByCpActionIdAndCpUserId($params['message_info']["concrete_action"]->id, $params['cp_user']->id);

        //クリックログがある、またはAction参加済み場合、クリックActionを送信しない
        $params['clicked_line_add_friend_url'] = ($cp_line_add_friend_log || $params['message_info']['action_status']->status == CpUserActionStatus::JOIN)? 1 : 0;

        return $params;
    }
}
