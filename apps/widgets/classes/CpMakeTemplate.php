<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
class CpMakeTemplate extends aafwWidgetBase {

    public function doService( $params = array() ){
        if ( is_file( $fn = AAFW_DIR . '/config/campaign_templates.yml' ) ) {
            $yml = new YAMLParser();
            $campaign_templates = $yml->in( $fn );
            $params['templates'] = $campaign_templates;

            $cp_action = new CpAction();
            $params['CpActionDetail'] = $cp_action->getAvailableCampaignActions();
        }
        return $params;
    }
}