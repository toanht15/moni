<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');
AAFW::import('jp.aainc.classes.services.YoutubeStreamService');

class EditActionMovie extends aafwWidgetBase{
    private $ActionForm;
    private $ActionError;

    public function doService( $params = array()) {
        $this->ActionForm = $params['ActionForm'];
        $this->ActionError = $params['ActionError'];

        $service_factory = new aafwServiceFactory();
        /** @var CpFlowService $cp_flow_service */
        $cp_flow_service = $service_factory->create('CpFlowService');
        $params['action'] = $cp_flow_service->getCpActionById($params['action_id']);

        $cp = $cp_flow_service->getCpById($params['cp_id']);
        if ($cp->start_date != '0000-00-00 00:00:00') {
            $start_date = date_create($cp->start_date);
        } else {
            $start_date = date_create();
        }
        $params['start_date'] = $start_date->format('Y/m/d H:i');

        $cp_movie_action_manager = new CpMovieActionManager();
        $cp_actions = $cp_movie_action_manager->getCpActions($params['action_id']);
        $params['action'] = $cp_actions[0];
        $params['ActionForm']['movie_object_id_url'] = $cp_actions[1]->movie_object_id;
        $params['ActionForm']['module_movie'] = $cp_actions[1]->movie_type;
        if(($cp_actions[1]->movie_type) == 3)
            $params['ActionForm']['movie_upload_url'] = $cp_actions[1]->movie_url;

        $brand_global_setting_service = $service_factory->create('BrandGlobalSettingService');
        $params['video_upload_enable'] = $brand_global_setting_service->getBrandGlobalSettingByName(BrandInfoContainer::getInstance()->getBrandGlobalSettings(),BrandGlobalSettingService::CAN_UPLOAD_ORIGINAL_VIDEO);
        /** @var YoutubeStreamService $stream_service */
        $stream_service = new YoutubeStreamService();
        $streams = $stream_service->getAvailableStreamsByBrandId($cp->brand_id);
        foreach($streams as $stream) {
            $entries = $stream_service->getAllEntriesByStreamId($stream->id);
            foreach($entries as $entry) {
                $params['entries'][$entry->object_id] = json_decode($entry->extra_data)->snippet->title;
            }
        }
        return $params;
    }
}