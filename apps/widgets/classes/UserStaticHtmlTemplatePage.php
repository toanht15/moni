<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class UserStaticHtmlTemplatePage extends aafwWidgetBase {

    public function doService( $params = array() ){
        $partsList = json_decode($params['staticHtmlEntry']['template_contents_json'], true);
        $params["publicContents"] = array();
        $params["limitedContents"] = array();
        $limitedFlg = false;

        /** @var StaticHtmlEntryService $static_html_entry_service */
        $static_html_entry_service = $this->getService('StaticHtmlEntryService');

        foreach($partsList as $parts) {
            if($parts['type'] == StaticHtmlTemplate::TEMPLATE_TYPE_LOGIN_LIMIT_BOUNDARY) {
                $limitedFlg = true;
                continue;
            }

            if($params['pageStatus']['can_use_fan_count_markdown']){
                $parts['template']['text'] = $static_html_entry_service->evalFanCountMarkdown($parts['template']['text'],$params['pageStatus']['brand_info']['users_num']);
            }
            if(isset($params['pageStatus']['joined_cp_count'])){
                $parts['template']['text'] = $static_html_entry_service->evalUserJoinedStampRallyCpsCount($parts['template']['text'],$params['pageStatus']['joined_cp_count']);
            }

            if($parts['type'] == StaticHtmlTemplate::TEMPLATE_TYPE_INSTAGRAM) {
                $parts['template']['layout_type'] = $params['staticHtmlEntry']['layout_type'];
            }

            if($parts['type'] == StaticHtmlTemplate::TEMPLATE_TYPE_STAMP_RALLY) {
                $parts['template']['is_login'] = $params['pageStatus']['isLogin'];
            }

            if($limitedFlg) {
                $params["limitedContents"][] = $parts;
            }else{
                $params["publicContents"][] = $parts;
            }
        }

        return $params;
    }
}