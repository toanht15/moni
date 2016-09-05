<?php
AAFW::import('jp.aainc.aafw.base.aafwEntityBase');
class SynCpRedirectDuration extends aafwEntityBase {
    protected $_Relations = array(
        'SynCps' => array(
            'syn_cp_id' => 'id'
        ),
    );
}