<?php
AAFW::import('jp.aainc.aafw.web.aafwWidgetBase');

class UserMessageThreadActionMovie extends aafwWidgetBase {

    public function doService($params) {
        $cp_movie_action_manager = new CpMovieActionManager();
        $cp_actions = $cp_movie_action_manager->getCpActions($params['message_info']['cp_action']->id);
        $params['text'] = $cp_actions[1]->text;
        return $params;
    }
}
