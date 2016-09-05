<?php

AAFW::import('jp.aainc.aafw.base.aafwEntityBase');

class CpAnnounceAction extends aafwEntityBase {

    const DEFAULT_DESIGN_TYPE = 1;
    const NORMAL_DESIGN_TYPE = 2;

    public static $design_type = array(
        self::DEFAULT_DESIGN_TYPE => 'デフォルト',
        self::NORMAL_DESIGN_TYPE => 'ノーマル'
    );

    protected $_Relations = array(

        'CpAction' => array(
            'cp_action_id' => 'id',
        ),
    );

    public function getFixedText($user_name) {
        $param = ["<#USER_NAME>" => $user_name];
        return Util::applyParameter($this->text, $param);
    }
}