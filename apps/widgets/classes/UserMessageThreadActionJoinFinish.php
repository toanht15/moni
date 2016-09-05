<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class UserMessageThreadActionJoinFinish extends aafwWidgetBase {

    public function doService($params) {

        if ($params['message_info']["concrete_action"]->cv_tag) {

            /** @var ReplaceTagService $replace_tag_service */
            $replace_tag_service = $this->getService('ReplaceTagService');

            $params['message_info']["concrete_action"]->cv_tag = $replace_tag_service->getTag(
                $params['message_info']["concrete_action"]->cv_tag,
                array(ReplaceTagService::TYPE_ALLIED_ID => $params['pageStatus']['userInfo']->id)
            );
        }

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        $params['last_action'] = $cp_flow_service->getLastActionInGroupByGroupId($params['message_info']['cp_action']->cp_action_group_id);

        return $params;
    }
}
