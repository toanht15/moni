<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class EditActionRetweet extends aafwWidgetBase{

    private $ActionForm;
    private $ActionError;
    private $cp;

    public function doService( $params = array() ){

        $this->ActionForm   = $params['ActionForm'];
        $this->ActionError  = $params['ActionError'];
        $service_factory            = new aafwServiceFactory();

        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service            = $service_factory->create('CpFlowService');

        /** @var CpRetweetActionService $cp_retweet_action_service */
        $cp_retweet_action_service  = $service_factory->create('CpRetweetActionService');

        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);

        $this->cp = $cp_flow_service->getCpById($params['cp_id']);

        if ($this->cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($this->cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        $retweet_action_manager = new CpRetweetActionManager();
        $params['concrete_action']      = $retweet_action_manager->getConcreteAction($params['action']);
        if ($params['concrete_action']->tweet_has_photo) {
            $params['tweet_photos'] = $cp_retweet_action_service->getRetweetPhotos($params['concrete_action']->id);
        }

        return $params;
    }
}
