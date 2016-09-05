<?php

AAFW::import('jp.aainc.lib.base.aafwServiceBase');

class CpShareActionService extends aafwServiceBase {

    const TOP_PAGE_SHARE = 1;
    const EXTERNAL_SHARE = 2;

    /** @var CpShareActionss $cp_share_actions */
    protected $cp_share_actions;

    public function __construct() {
        $this->cp_share_actions = $this->getModel('CpShareActions');
    }

    public function getCpShareActionById($cp_action_id){
        $filter = array(
            'cp_action_id' => $cp_action_id
        );
        return $this->cp_share_actions->findOne($filter);
    }

    public function getShareTwitterPlaceholder($cp_action_id,$url){
        $cp_share_action = $this->getCpShareActionById($cp_action_id);
        return $cp_share_action->placeholder."-".$url;
    }
}