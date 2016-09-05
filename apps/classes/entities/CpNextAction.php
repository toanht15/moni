<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpNextAction extends aafwEntityBase {

    protected $_Relations = array(

        'CpAction' => array(
            'cp_action_id' => 'id',
        ),

        'CpAction' => array(
            'next_cp_action_id' => 'id',
        ),

    );

    public function getCpNextActionInfo() {
        $cp_next_action_info = $this->getModel('CpNextActionInfos');
        return $cp_next_action_info->find(array('next_action_table_id' => $this->id));
    }

}