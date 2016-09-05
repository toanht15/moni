<?php
AAFW::import('jp.aainc.lib.base.aafwServiceBase');
AAFW::import('jp.aainc.classes.entities.CpGiftAction');

class CpGiftActionService extends aafwServiceBase {

    protected $cp_gift_actions;

    public function __construct() {
        $this->cp_gift_actions = $this->getModel('CpGiftActions');
    }

    /**
     * ギフトアクションを取得する
     * @param $cp_action_id
     * @return mixed
     */
    public function getCpGiftAction($cp_action_id){
        $filter = array(
            'conditions' => array(
                'cp_action_id' => $cp_action_id
            ),
        );
        return $this->cp_gift_actions->findOne($filter);
    }

    /**
     * @param $cp_gift_action_id
     * @return mixed
     */
    public function getCpGiftActionById($cp_gift_action_id){
        $filter = array(
            'id' => $cp_gift_action_id
        );
        return $this->cp_gift_actions->findOne($filter);
    }
}