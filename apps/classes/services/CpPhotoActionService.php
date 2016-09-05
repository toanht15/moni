<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpPhotoActionService extends aafwServiceBase {

    /** @var CpShareActionss $cp_photo_actions */
    protected $cp_photo_actions;

    public function __construct() {
        $this->cp_photo_actions = $this->getModel('CpPhotoActions');
    }

    public function updateCpPhotoAction($cp_photo_action) {
        $this->cp_photo_actions->save($cp_photo_action);
    }

    public function getCpPhotoAction($cp_action_id){
        $filter = array(
            'cp_action_id' => $cp_action_id
        );

        return $this->cp_photo_actions->findOne($filter);
    }
}