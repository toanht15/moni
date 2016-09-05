<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionMessage extends aafwWidgetBase{
    private $ActionForm;
    private $ActionError;

    public function doService( $params = array() ){
        $service_factory = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);
        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }

        $last_action = $cp_flow_service->getLastActionInGroupByGroupId($params['action']->cp_action_group_id);
        $params['is_last_action_in_group'] = $last_action->id === $params['action']->id;

        $params['start_date'] = $start_date->format('Y/m/d H:i');
        $params['is_entry_message_action'] = $this->isEntryMessageAction($cp, $params['action']->id);

        $params['is_show_send_text_mail_button'] = $this->isShowSendTextMailButton($params['pageStatus']['brand']);

        return $params;
    }

    /**
     * @param $cp
     * @param $action_id
     * @return bool
     */
    public function isEntryMessageAction($cp, $action_id) {
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $this->getService('CpFlowService');
        list($entry_action, $entry_concrete_action) = $cp_flow_service->getEntryActionInfoByCpId($cp->id);

        return $cp->type === Cp::TYPE_MESSAGE && $entry_action->id === $action_id;
    }

    private function isShowSendTextMailButton($brand) {

        /** @var BrandGlobalSettingService $brand_global_setting_service */
        $brand_global_setting_service = $this->getService('BrandGlobalSettingService');
        $can_set_crm_text_mail = $brand_global_setting_service->getBrandGlobalSetting($brand->id, BrandGlobalSettingService::CAN_SET_CRM_TEXT_MAIL);

        if(!Util::isNullOrEmpty($can_set_crm_text_mail)) {
            return true;
        }

        return false;
    }
}