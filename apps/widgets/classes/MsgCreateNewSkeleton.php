<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.entities.CpAction');

class MsgCreateNewSkeleton extends aafwWidgetBase {
    public function doService( $params = array() ){

        $cp_action = new CpAction();
        $params['CpActionDetail'] = $cp_action->getAvailableMessageActions();
        return $params;
    }
}